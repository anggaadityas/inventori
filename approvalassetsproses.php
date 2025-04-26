<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

$serverName = "192.168.2.135"; // atau IP/hostname server SQL
$connectionOptions = array(
    "Database" => "DB_SCK", // ganti dengan nama database Anda
    "Uid" => "sa",                // ganti dengan username SQL Server
    "PWD" => "And142857",          // ganti dengan password SQL Server
    "CharacterSet" => "UTF-8"
);

// Membuat koneksi
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Cek koneksi
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $TransID = $_POST['TransID'];
    $docNum = $_POST['DocNum'];
    $TransName = $_POST['TransName'];
    $approvalProgress = $_POST['ApprovalProgress'];
    $ApprovalStatus = $_POST['ApprovalStatus'];
    $ApprovalRemarks = $_POST['ApprovalRemarks'];
    $DocDate = $_POST['DocDate'];
    $rev_question = $_POST['rev_question'] ?? null;
    $revDocDate = $_POST['rev_date_req'] ?? null;
    $createdBy = $_SESSION['nama'];
    $StoreCode = $_POST['WarehouseFrom'] ?? null;
    $WarehouseTo = $_POST['WarehouseTo'] ?? null;

    if ($rev_question == 1) {
        $fixDocDate = $revDocDate;
        $fixDocPastDate = $DocDate;
        $fixnotiftanggalkirim = 'Ada Perubahan Tanggal Pengiriman Retur Barang :<br>
         &nbsp;&nbsp;&nbsp;Sebelumnya : ' . $DocDate . '<br> &nbsp;&nbsp; Menjadi : ' . $revDocDate . '';
    } else {
        $fixDocDate = $DocDate;
        $fixDocPastDate = NULL;
        $fixnotiftanggalkirim = $DocDate;
    }

    // Checkboxes yang diceklis
    $statusItems = $_POST['status_item'] ?? [];
    $detailIds = $_POST['ID'] ?? [];
    $RemarksItemApproval = $_POST['RemarksItemApproval'] ?? [];
    $QuantityVer = $_POST['QuantityVer'] ?? [];

    $response = [
        'status' => 'error',
        'message' => 'Gagal memproses permintaan.'
    ];

    try {

        foreach ($detailIds as $itemCode => $value) {
            $status = $statusItems[$itemCode] ?? 0;
            $id = $detailIds[$itemCode] ?? null;
            $RemarksItem = $RemarksItemApproval[$itemCode] ?? null;
            $QuantityVerItem = $QuantityVer[$itemCode] ?? null;

            if ($id) {
                if ($approvalProgress == 1) {
                    $sqlUpdate = "UPDATE InventoriAssetDetail 
                              SET StatusApprovalAM = ?, RemarksApprovalAM = ?, DateApprovalAM = GETDATE(), QuantityVer = ?
                              WHERE ID = ?";
                } else if ($approvalProgress == 2) {
                    $sqlUpdate = "UPDATE InventoriAssetDetail 
                    SET StatusApprovalDistribusi = ?, RemarksApprovalDistribusi = ?, DateApprovalDistribusi = GETDATE(), QuantityVer = ?
                    WHERE ID = ?";
                } else if ($approvalProgress >= 3) {
                    $sqlUpdate = "UPDATE InventoriAssetDetail 
                              SET StatusApprovalWarehouse = ?, RemarksApprovalWarehouse = ?, DateApprovalWarehouse = GETDATE(), QuantityVer = ?
                              WHERE ID = ?";
                }
                $params = [$status, $RemarksItem, $QuantityVerItem, $id];
                $stmt = sqlsrv_query($conn, $sqlUpdate, $params);

                if (!$stmt) {
                    throw new Exception("Gagal mengupdate item dengan ID: $id");
                }
            }
        }

        if ($ApprovalStatus == 'Menunggu Approval Distribusi') {

            // Query untuk mencari warehouse yang item-nya totalnya sama dengan total yang di-reject
            $sqlCheckReject = "SELECT WarehouseTo, 
                                    COUNT(*) AS TotalItems, 
                                    SUM(CASE WHEN StatusApprovalDistribusi = 0 THEN 1 ELSE 0 END) AS TotalRejected
                             FROM InventoriAssetDetail
                             WHERE TransID = ? AND StatusApprovalAM = 1
                             GROUP BY WarehouseTo
                             HAVING COUNT(*) = SUM(CASE WHEN StatusApprovalDistribusi = 0 THEN 1 ELSE 0 END)";

            // Execute the query
            $stmtUpdateApproval = sqlsrv_query($conn, $sqlCheckReject, [$TransID]);

            // Loop through the results
            while ($rowApproval = sqlsrv_fetch_array($stmtUpdateApproval, SQLSRV_FETCH_ASSOC)) {
                // Check if there's data to update
                if ($rowApproval) {
                    // Prepare the update query
                    $sqlUpdate = "UPDATE InventoriApprovalAsset 
                                  SET ApprovalStatus = 'Reject', 
                                      ApprovalRemarks = 'Direject Distribusi', 
                                      ApprovalDate = GETDATE()
                                  WHERE TransID = ? 
                                    AND UserNameApproval = ?";

                    // Bind the parameters
                    $params = [$TransID, $rowApproval['WarehouseTo']];

                    // Execute the update
                    $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, $params);

                }
            }
        }


        // Cek apakah ada item dengan StatusApproval = NULL
        if ($approvalProgress == 1) {
            $sqlCheckNull = "SELECT COUNT(*) AS unapproved FROM InventoriAssetDetail WHERE TransID = ? AND StatusApprovalAM=1 AND  StatusApprovalAM IS NULL";
        } else if ($approvalProgress == 2) {
            $sqlCheckNull = "SELECT COUNT(*) AS unapproved FROM InventoriAssetDetail WHERE TransID = ? AND StatusApprovalAM=1 AND StatusApprovalDistribusi IS NULL";
        } else if ($approvalProgress >= 3) {
            $sqlCheckNull = "SELECT COUNT(*) AS unapproved FROM InventoriAssetDetail WHERE TransID = ? AND StatusApprovalAM=1 AND StatusApprovalWarehouse IS NULL";
        }
        $stmtNull = sqlsrv_query($conn, $sqlCheckNull, [$TransID]);

        if ($stmtNull === false) {
            die(print_r(sqlsrv_errors(), true)); // Tampilkan detail error SQL Server
        }

        $rowNull = sqlsrv_fetch_array($stmtNull, SQLSRV_FETCH_ASSOC);

        if ($rowNull['unapproved'] >= 0) {
            // Tidak ada StatusApproval yang NULL, lanjut proses
            if ($approvalProgress == 1) {
                $sqlCheckAll = "SELECT 
                        COUNT(*) AS total, 
                        SUM(CASE WHEN StatusApprovalAM = 1 THEN 1 ELSE 0 END) AS approved,
                        SUM(CASE WHEN StatusApprovalAM = 0 THEN 1 ELSE 0 END) AS rejected
                    FROM InventoriAssetDetail 
                    WHERE TransID = ?";
            } else if ($approvalProgress == 2) {
                $sqlCheckAll = "SELECT 
                        COUNT(*) AS total, 
                        SUM(CASE WHEN StatusApprovalDistribusi = 1 THEN 1 ELSE 0 END) AS approved,
                        SUM(CASE WHEN StatusApprovalDistribusi = 0 THEN 1 ELSE 0 END) AS rejected
                    FROM InventoriAssetDetail 
                    WHERE TransID = ? AND StatusApprovalAM=1";
            } else if ($approvalProgress >= 3) {
                $sqlCheckAll = "SELECT 
                        COUNT(*) AS total, 
                        SUM(CASE WHEN StatusApprovalWarehouse = 1 THEN 1 ELSE 0 END) AS approved,
                        SUM(CASE WHEN StatusApprovalWarehouse = 0 THEN 1 ELSE 0 END) AS rejected
                    FROM InventoriAssetDetail 
                    WHERE TransID = ? AND StatusApprovalAM=1 AND StatusApprovalDistribusi=1";
            }
            $stmtCheck = sqlsrv_query($conn, $sqlCheckAll, [$TransID]);
            $rowCheck = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC);

            if ($rowCheck) {
                $total = (int) $rowCheck['total'];
                $approved = (int) $rowCheck['approved'];
                $rejected = (int) $rowCheck['rejected'];



                if ($ApprovalStatus == 'Menunggu Approval Distribusi') {
                    $approvalProgressnextstep = $_POST['ApprovalProgress'];
                    $sql = "SELECT * FROM InventoriApprovalAsset 
                                         WHERE TransID=? AND ApprovalStep > ?
                                        AND(ApprovalStatus IS NULL OR ApprovalStatus != 'Reject')";
                    $stmt = sqlsrv_query($conn, $sql, [$TransID, $approvalProgressnextstep]);

                    $rownextapproval = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                    $nextprogress = $rownextapproval['ApprovalStep'] ?? null;
                    $nextstatusprogress = $rownextapproval['StatusApproval'] ?? null;
                    $nextidapproval = $rownextapproval['UserIDApproval'] ?? null;
                    $nextnameapproval = $rownextapproval['UserNameApproval'] ?? null;
                } else {

                    $approvalProgressnextstep = $_POST['ApprovalProgress'] + 1;
                    $sql = "SELECT * FROM InventoriApprovalAsset 
                             WHERE TransID=? AND ApprovalStep=?;";
                    $stmt = sqlsrv_query($conn, $sql, [$TransID, $approvalProgressnextstep]);
                    // }
                    $rownextapproval = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                    $nextprogress = $rownextapproval['ApprovalStep'] ?? null;
                    $nextstatusprogress = $rownextapproval['StatusApproval'] ?? null;
                    $nextidapproval = $rownextapproval['UserIDApproval'] ?? null;
                    $nextnameapproval = $rownextapproval['UserNameApproval'] ?? null;

                }

                if ($approved > 0) {
                    // Ada yang disetujui → anggap dokumen disetujui
                    $sqlUpdateHeader = "UPDATE InventoriAssetHeader SET DocDate = ?,DocPastDate = ?,ApprovalProgress = ?,ApprovalUser=?,ApprovalUserName=?,ApprovalStatus=? WHERE ID = ?";
                    $params = [$fixDocDate, $fixDocPastDate, $nextprogress, $nextidapproval, $nextnameapproval, $nextstatusprogress, $TransID];

                    // Eksekusi query
                    sqlsrv_query($conn, $sqlUpdateHeader, $params);

                    // Print query yang dieksekusi (simulasi)
                    $printQuery = $sqlUpdateHeader;
                    foreach ($params as $param) {
                        $safeParam = is_null($param) ? "NULL" : "'" . addslashes($param) . "'";
                        $printQuery = preg_replace('/\?/', $safeParam, $printQuery, 1);
                    }

                    $sqlUpdate = "UPDATE InventoriApprovalAsset 
                          SET ApprovalStatus = 'Approved', ApprovalRemarks = ?, ApprovalDate = GETDATE()
                          WHERE TransID = ? AND ApprovalStep= ? AND UserNameApproval = ?";
                    $params = [$ApprovalRemarks, $TransID, $approvalProgress, $createdBy];
                    sqlsrv_query($conn, $sqlUpdate, $params);

                    // Insert ke InventoriAssetLog
                    $sqlLog = "INSERT INTO InventoriAssetLog (TransID, DocProgress, Remarks, CreatedBy) 
                    VALUES (?, 'Approval', ?, :createdBy)";
                    $paramslog = [$TransID, $ApprovalRemarks, $createdBy];
                    sqlsrv_query($conn, $sqlLog, $paramslog);

                    $sqlDetail = "SELECT ItemCode, ItemName, Quantity,StatusApprovalAM,StatusApprovalDistribusi,StatusApprovalWarehouse
                    FROM InventoriAssetDetail 
                    WHERE TransID = ?";
                    $paramsdetail = [$TransID];
                    $stmtDetail = sqlsrv_query($conn, $sqlDetail, $paramsdetail);

                    // 1. Cek jumlah total approval step untuk TransID ini
                    $sqlTotalStep = "SELECT COUNT(*) AS Total FROM InventoriApprovalAsset WHERE TransID = ?";
                    $stmtTotal = sqlsrv_query($conn, $sqlTotalStep, [$TransID]);
                    $rowTotal = sqlsrv_fetch_array($stmtTotal, SQLSRV_FETCH_ASSOC);
                    $totalSteps = $rowTotal['Total'];

                    // 2. Cek jumlah approval yang sudah selesai (Approved atau Reject)
                    $sqlDoneStep = "SELECT COUNT(*) AS Done FROM InventoriApprovalAsset
                                    WHERE TransID = ? AND ApprovalStatus IN ('Approved', 'Reject')";
                    $stmtDone = sqlsrv_query($conn, $sqlDoneStep, [$TransID]);
                    $rowDone = sqlsrv_fetch_array($stmtDone, SQLSRV_FETCH_ASSOC);
                    $doneSteps = $rowDone['Done'];

                    // 3. Jika semua step sudah selesai, update header status

                    try {
                        if ($totalSteps == $doneSteps) {
                            // Update the header status
                            $sqlUpdateHeader = "UPDATE InventoriAssetHeader SET ApprovalUser=NULL, ApprovalUserName=NULL, ApprovalStatus = 'Close', ApprovalProgress=4, StatusDoc='Close' WHERE ID = ?";
                            $updateHeaderResult = sqlsrv_query($conn, $sqlUpdateHeader, [$TransID]);

                            if ($updateHeaderResult === false) {
                                throw new Exception("Error updating InventoriAssetHeader: " . print_r(sqlsrv_errors(), true));
                            }

                            // Fetch items from the transaction detail
                            $sqlItems = "SELECT A.ItemCode, A.ItemName, A.ItemUom, A.Quantity, A.WarehouseTo,B.TermsRetur,B.TermsStoreClosing
                            ,B.AssetConditionOK,B.AssetConditionNonOK FROM InventoriAssetDetail A
                            LEFT JOIN MasterAssets B ON A.ItemCode = B.ItemCode
                             WHERE TransID = ?  AND B.Warehouse= ? AND StatusApprovalAM = 1 AND StatusApprovalDistribusi = 1";
                            $stmtItems = sqlsrv_query($conn, $sqlItems, [$TransID, $StoreCode]);

                            if ($stmtItems === false) {
                                throw new Exception("Error fetching items from InventoriAssetDetail: " . print_r(sqlsrv_errors(), true));
                            }

                            while ($row = sqlsrv_fetch_array($stmtItems, SQLSRV_FETCH_ASSOC)) {
                                $ItemCode = $row['ItemCode'];
                                $ItemName = $row['ItemName'];
                                $ItemUom = $row['ItemUom'];
                                $Qty = $row['Quantity'];
                                $WarehouseFrom = $StoreCode;
                                $WarehouseTo = $WarehouseTo;
                                $TermsRetur = $row['TermsRetur'];
                                $TermsStoreClosing = $row['TermsStoreClosing'];
                                $AssetConditionOK = $row['AssetConditionOK'];
                                $AssetConditionNonOK = $row['AssetConditionNonOK'];
                                $CreatedBy = 'AutoSystem'; // Bisa ganti dengan session login
                                $Now = date('Y-m-d H:i:s');

                                // Insert into MasterAssetLog
                                $sqlInsertLog = "INSERT INTO MasterAssetLogs (ItemType, ItemCode, ItemName, ItemUom, AssetQuantity, CreatedDate, CreatedBy, IsActive, WarehouseFrom, WarehouseTo, DocTrans, TypeTrans, RemarksTrans, DateTrans) 
                                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                $paramsLog = [3, $ItemCode, $ItemName, $ItemUom, $Qty, $Now, $CreatedBy, 1, $WarehouseFrom, $WarehouseTo, $TransID, $TransName, 'Auto insert from approval proses', $Now];
                                $insertLogResult = sqlsrv_query($conn, $sqlInsertLog, $paramsLog);

                                if ($insertLogResult === false) {
                                    throw new Exception("Error inserting into MasterAssetLog: " . print_r(sqlsrv_errors(), true));
                                }

                                // Check if the asset exists in MasterAsset for WarehouseTo
                                $sqlCheckAsset = "SELECT ID, AssetQuantity FROM MasterAssets WHERE ItemCode = ? AND Warehouse = ?";
                                $stmtCheck = sqlsrv_query($conn, $sqlCheckAsset, [$ItemCode, $WarehouseTo]);

                                if ($stmtCheck === false) {
                                    throw new Exception("Error checking asset in MasterAssets: " . print_r(sqlsrv_errors(), true));
                                }

                                if ($existing = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC)) {
                                    // Update the quantity for existing asset in WarehouseTo
                                    $newQty = $existing['AssetQuantity'] + $Qty;
                                    $sqlUpdateAsset = "UPDATE MasterAssets SET AssetQuantity = ?, UpdatedDate = ?, UpdatedBy = ? WHERE ID = ?";
                                    $updateAssetResult = sqlsrv_query($conn, $sqlUpdateAsset, [$newQty, $Now, $CreatedBy, $existing['ID']]);

                                    if ($updateAssetResult === false) {
                                        throw new Exception("Error updating asset in MasterAsset for WarehouseTo: " . print_r(sqlsrv_errors(), true));
                                    }
                                } else {
                                    // Insert new asset for WarehouseTo
                                    $sqlInsertAsset = "INSERT INTO MasterAssets (ItemCode, ItemName, ItemUom, AssetQuantity, CreatedDate, CreatedBy, IsActive, Warehouse, TermsRetur, TermsStoreClosing, AssetConditionOK, AssetConditionNonOK) 
                                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                                    $paramsAsset = [$ItemCode, $ItemName, $ItemUom, $Qty, $Now, $CreatedBy, 1, $WarehouseTo, $TermsRetur, $TermsStoreClosing, $AssetConditionOK, $AssetConditionNonOK];
                                    $insertAssetResult = sqlsrv_query($conn, $sqlInsertAsset, $paramsAsset);

                                    if ($insertAssetResult === false) {
                                        throw new Exception("Error inserting into MasterAssets for WarehouseTo: " . print_r(sqlsrv_errors(), true));
                                    }
                                }

                                // Check and update asset quantity in WarehouseFrom
                                $stmtCheckFrom = sqlsrv_query($conn, $sqlCheckAsset, [$ItemCode, $WarehouseFrom]);

                                if ($stmtCheckFrom === false) {
                                    throw new Exception("Error checking asset in MasterAsset for WarehouseFrom: " . print_r(sqlsrv_errors(), true));
                                }

                                if ($existing = sqlsrv_fetch_array($stmtCheckFrom, SQLSRV_FETCH_ASSOC)) {
                                    $newQty = $existing['AssetQuantity'] - $Qty;
                                    $sqlUpdateAsset = "UPDATE MasterAssets SET AssetQuantity = ?, UpdatedDate = ?, UpdatedBy = ? WHERE ID = ?";
                                    $updateAssetResult = sqlsrv_query($conn, $sqlUpdateAsset, [$newQty, $Now, $CreatedBy, $existing['ID']]);

                                    if ($updateAssetResult === false) {
                                        throw new Exception("Error updating asset in MasterAssets for WarehouseFrom: " . print_r(sqlsrv_errors(), true));
                                    }
                                }
                            }

                            // Commit the transaction if everything is successful
                            sqlsrv_query($conn, "COMMIT TRANSACTION");
                        } else {
                            // echo "Approval still in progress. ($doneSteps / $totalSteps completed)\n";
                        }
                    } catch (Exception $e) {
                        // Rollback the transaction if any error occurs
                        sqlsrv_query($conn, "ROLLBACK TRANSACTION");

                        // Optionally log the error or handle it as needed
                        echo "Error: " . $e->getMessage();
                    }



                    $itemList = "";
                    while ($row = sqlsrv_fetch_array($stmtDetail, SQLSRV_FETCH_ASSOC)) {
                        if ($approvalProgress == 1) {
                            $Approval = 'AM';
                            $StatusApproval = $row['StatusApprovalAM'];
                        } else if ($approvalProgress == 2) {
                            $Approval = 'Distribusi';
                            $StatusApproval = $row['StatusApprovalDistribusi'];
                        } else if ($approvalProgress >= 3) {
                            $Approval = 'Warehouse';
                            $StatusApproval = $row['StatusApprovalWarehouse'];
                        }

                        $itemList .= "<tr>
                            <td>{$row['ItemCode']}</td>
                            <td>{$row['ItemName']}</td>
                            <td>{$row['Quantity']}</td>
                            <td>{$StatusApproval}</td>
                        </tr>";
                    }

                    $bodyEmail = '
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <meta charset="UTF-8">
                            <style>
                                body {
                                    font-family: "Segoe UI", Arial, sans-serif;
                                    font-size: 14px;
                                    color: #333;
                                    margin: 0;
                                    padding: 20px;
                                }
                                .info-table {
                                    width: 100%;
                                    border-collapse: collapse;
                                    margin-bottom: 10px;
                                }
                                .info-table td {
                                    padding: 2px 6px;
                                    vertical-align: top;
                                }
                                .info-table td.label {
                                    font-weight: bold;
                                    width: 160px;
                                }
                                .item-table {
                                    border-collapse: collapse;
                                    width: 100%;
                                    margin-top: 15px;
                                    margin-bottom: 10px;
                                }
                                .item-table th, .item-table td {
                                    border: 1px solid #ddd;
                                    padding: 8px 10px;
                                    text-align: left;
                                }
                                .item-table th {
                                    background-color: #f5f5f5;
                                }
                                .footer {
                                    margin-top: 5px;
                                    line-height: 1.4;
                                }
                            </style>
                        </head>
                        <body>

                            <table class="info-table">
                                <tr><td class="label">No Dokumen:</td><td>' . $docNum . '</td></tr>
                                <tr><td class="label">Transaksi:</td><td>' . $TransName . '</td></tr>
                                <tr><td class="label">Dibuat Oleh:</td><td>' . $StoreCode . '</td></tr>
                                <tr><td class="label">Approval By:</td><td>' . $Approval . '</td></tr>
                                <tr><td class="label">Tanggal Pengiriman:</td><td>' . $fixnotiftanggalkirim . '</td></tr>
                                <tr><td class="label">Catatan Approval:</td><td>' . $ApprovalRemarks . '</td></tr>
                            </table>
                            <br>
                            <table class="item-table">
                                <thead>
                                    <tr>
                                        <th>Kode Item</th>
                                        <th>Nama Item</th>
                                        <th>Qty</th>
                                        <th>Status Approval</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ' . $itemList . '
                                </tbody>
                            </table>
                            <br>
                            <div class="footer">
                                Terima kasih atas perhatian Anda.<br>
                                Hormat kami,<br>
                                Sistem Inventaris Assets
                            </div>

                        </body>
                        </html>';




                    // Kirim email
                    $mail = new PHPMailer();
                    try {
                        // Konfigurasi SMTP
                        $mail->isSMTP();
                        $mail->Host = "mail.multirasa.co.id";
                        $mail->SMTPAuth = true;
                        $mail->Username = "info.voucherrequest@multirasa.co.id"; //enter you email address
                        $mail->Password = 'yoshimulti'; //enter you email password
                        $mail->Port = 465;
                        $mail->SMTPSecure = "ssl";

                        $wh = 'angga.aditya@multirasa.co.id';
                        // Penerima
                        $mail->setFrom('info.voucherrequest@multirasa.co.id', 'Sistem Approval');
                        $mail->addAddress($wh); // bisa juga berdasarkan $nextidapproval lookup email-nya

                        $mail->isHTML(true);
                        $mail->Subject = "Notifikasi Approval Inventaris Assets - {$docNum}";
                        $mail->Body = $bodyEmail;

                        if ($mail->send()) {
                            $status = "success";
                            $response = "Email is sent!";
                        } else {
                            $status = "failed";
                            echo "Something is wrong: <br><br>" . $mail->ErrorInfo;
                        }
                    } catch (Exception $e) {
                        $response['message'] .= " Email gagal dikirim. Error: {$mail->ErrorInfo}";
                    }


                } elseif ($total === $rejected) {
                    // Semua ditolak → anggap dokumen ditolak

                    // Update header InventoriAssetHeader
                    $sqlUpdateHeader = "UPDATE InventoriAssetHeader 
                                        SET ApprovalProgress = 998, ApprovalStatus = 'Reject', StatusDoc = 'Cancel' 
                                        WHERE ID = ?";
                    $stmtUpdateHeader = sqlsrv_query($conn, $sqlUpdateHeader, [$TransID]);
                    if ($stmtUpdateHeader === false) {
                        die(print_r(sqlsrv_errors(), true)); // Menampilkan error jika query gagal
                    }

                    // Update status approval di InventoriApprovalAsset
                    $sqlUpdate = "UPDATE InventoriApprovalAsset 
                                  SET ApprovalStatus = 'Reject', ApprovalRemarks = ?, ApprovalDate = GETDATE()
                                  WHERE TransID = ? AND ApprovalStep = ? AND UserNameApproval = ?";
                    $params = [$ApprovalRemarks, $TransID, $approvalProgress, $createdBy];
                    $stmtUpdate = sqlsrv_query($conn, $sqlUpdate, $params);
                    if ($stmtUpdate === false) {
                        die(print_r(sqlsrv_errors(), true)); // Menampilkan error jika query gagal
                    }

                    // Insert log ke InventoriAssetLog
                    $sqlLog = "INSERT INTO InventoriAssetLog (TransID, DocProgress, Remarks, CreatedBy) 
                               VALUES (?, 'Reject', ?, ?)";
                    $paramsLog = [$TransID, $ApprovalRemarks, $createdBy];
                    $stmtLog = sqlsrv_query($conn, $sqlLog, $paramsLog);
                    if ($stmtLog === false) {
                        die(print_r(sqlsrv_errors(), true)); // Menampilkan error jika insert log gagal
                    }

                    // Jika semua query berhasil, beri tahu bahwa proses telah selesai
                    echo "Semua item telah ditolak dan status dokumen diperbarui.";


                } else {
                    echo "Tidak ada execute";
                }
            }
        }

        $response = [
            'status' => 'success',
            'message' => 'Data berhasil diproses!'
        ];
    } catch (Exception $e) {
        $response = [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }

    // Return response as JSON
    echo json_encode($response);
}
?>