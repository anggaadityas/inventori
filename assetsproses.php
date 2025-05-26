<?php
session_start();
header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'data' => []];

use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

$serverName = "192.168.2.135";
$connectionOptions = [
    "Database" => "DB_SCK",
    "Uid" => "sa",
    "PWD" => "And142857"
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false)
    throw new Exception(print_r(sqlsrv_errors(), true));
sqlsrv_begin_transaction($conn);

$servername = "192.168.2.136";
$username = "root";
$password = "aas260993";
$dbname = "voucher_trial";

$am_id=$_SESSION["am_id"];
$connmysl = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
$tsql = "SELECT * FROM mst_user a INNER JOIN mst_divisi b
              on a.div_id=b.id_divisi WHERE a.id_user= '$am_id'";   


$stmt = mysqli_query($connmysl,$tsql);
$useram =mysqli_fetch_array($stmt);

try {
    $jenisPermintaan = $_POST['jenis_permintaan'];
    $jenisPrioritas = $_POST['jenis_prioritas'];
    $terms = $_POST['terms'];
    $tanggalPermintaan = $_POST['tanggal_permintaan'];
    $keterangan = $_POST['keterangan'];
    $createdBy = $_SESSION['nama'];
    $WarehouseTo = $_POST['WarehouseTo'];

    $itemNames = $_POST['itemName'];
    $itemCodes = $_POST['itemCode'];
    $itemUoms = $_POST['itemUom'];
    $quantities = $_POST['quantity'];
    $assetQuantities = $_POST['AssetQuantity'];
    $kondisiAssets = $_POST['kondisiAsset'];
    $alasan = $_POST['alasan'];
    $keteranganAsset = $_POST['keteranganAsset'];
    $assetConditionOk = $_POST['AssetConditionOk'];
    $assetConditionNonOk = $_POST['AssetConditionNonOk'];

    $serverNameHO = "192.168.1.5";
    // $serverNameHO = "portal.multirasa.co.id";
    $connectionInfoHO = array("Database" => "role", "UID" => "sa", "PWD" => "Mrn.14");
    $connHO = sqlsrv_connect($serverNameHO, $connectionInfoHO);
    if ($connHO === false)
        throw new Exception(print_r(sqlsrv_errors(), true));

    $sqlck = "SELECT 
                    CASE
                    WHEN area=1 THEN 'JAKARTA'
                    WHEN area=2 THEN 'SURABAYA'
                    ELSE ''
                    END as area,
                    CASE
                    WHEN area=1 THEN 'WH DISTRIBUSI JKT'
                    WHEN area=2 THEN 'WH DISTRIBUSI SBY'
                    ELSE ''
                    END as DISTRIBUSICK,
                    CASE
                    WHEN area=1 THEN 'wh.distribusi.jkt@multirasa.co.id'
                    WHEN area=2 THEN 'wh.sby@multirasa.co.id'
                    ELSE ''
                    END as emaildistribusick,
                    CASE
                    WHEN area=1 THEN 295
                    WHEN area=2 THEN 297
                    ELSE ''
                    END as iduserdistribusick,
                    CASE
                    WHEN area=1 THEN 'CK JAKARTA'
                    WHEN area=2 THEN 'CK SURABAYA'
                    ELSE ''
                    END as CK,
                    CASE
                    WHEN area=1 THEN 'wh.inv.jkt@multirasa.co.id'
                    WHEN area=2 THEN 'ck.admin.sby2@multirasa.co.id'
                    ELSE ''
                    END as emailck,
                    CASE
                    WHEN area=1 THEN 287
                    WHEN area=2 THEN 288
                    ELSE ''
                    END as iduserck,
                    CASE
                    WHEN area=1 THEN 'IT JAKARTA'
                    WHEN area=2 THEN 'IT SURABAYA'
                    ELSE ''
                    END as IT,
                    CASE
                    WHEN area=1 THEN 'ridwan.anas@multirasa.co.id'
                    WHEN area=2 THEN 'wrida.wardana@multirasa.co.id'
                    ELSE ''
                    END as emailit,
                    CASE
                    WHEN area=1 THEN 289
                    WHEN area=2 THEN 290
                    ELSE ''
                    END as iduserit,
                    CASE
                    WHEN area=1 THEN 'ENG JAKARTA'
                    WHEN area=2 THEN 'ENG SURABAYA'
                    ELSE ''
                    END as ENG,
                    CASE
                    WHEN area=1 THEN 'stockkeeper.engjkt@multirasa.co.id'
                    WHEN area=2 THEN 'stockkeeper.engsby@multirasa.co.id'
                    ELSE ''
                    END as emaileng,
                    CASE
                    WHEN area=1 THEN 291
                    WHEN area=2 THEN 299
                    ELSE ''
                    END as idusereng,
                    CASE
                    WHEN area=1 THEN 'PROJECT'
                    WHEN area=2 THEN 'PROJECT'
                    ELSE ''
                    END as PROJ,
                    CASE
                    WHEN area=1 THEN 'angga.aditya1@multirasa.co.id'
                    WHEN area=2 THEN 'angga.aditya1@multirasa.co.id'
                    ELSE ''
                    END as emailproj,
                    CASE
                    WHEN area=1 THEN 387
                    WHEN area=2 THEN 387
                    ELSE ''
                    END as iduserproj from storesett where storeCode='$createdBy'";
    $stmtck = sqlsrv_query($connHO, $sqlck);
    if ($stmtck === false)
        throw new Exception(print_r(sqlsrv_errors(), true));
    $rowck = sqlsrv_fetch_array($stmtck, SQLSRV_FETCH_ASSOC);
    $area = $rowck['area'];
    $emailck = $rowck['emailck'];
    $ck_area = $rowck['CK'];
    $userid_ck = $rowck['iduserck'];
    $emaildistribusick = $rowck['emailck'];
    $distribusi_area = $rowck['DISTRIBUSICK'];
    $userid_distribusick = $rowck['iduserdistribusick'];
    $emailit = $rowck['emailit'];
    $it_area = $rowck['IT'];
    $userid_it = $rowck['iduserit'];
    $emaileng = $rowck['emaileng'];
    $eng_area = $rowck['ENG'];
    $userid_eng = $rowck['idusereng'];
    $proj_area = $rowck['PROJ'];
    $userid_proj = $rowck['iduserproj'];

    $amId = $useram['id_user'];
    $amName = $useram['nama'];
    $approvalprogress = 1; // Status awal
    $approvalstatus = "Menunggu Approval AM";
    $statusDoc = 'Open';
    $approvalusername = 'AM';

    $rmId = 367;
    $rmName = 'RM';

    // Grouping data berdasarkan kondisiAsset dan warehouse
    $grouped = [];

    foreach ($itemCodes as $i => $code) {
        $kondisi = $kondisiAssets[$i];
        if ($jenisPermintaan == 3) {
            $warehouse = $kondisi == 1 ? $assetConditionOk[$i] : $assetConditionNonOk[$i];
        } else {
            $warehouse = $WarehouseTo;
        }
        $groupKey = $kondisi . '_' . $warehouse;

        if (!isset($grouped[$groupKey])) {
            $grouped[$groupKey] = [
                'kondisi' => $kondisi,
                'warehouse' => $warehouse,
                'items' => []
            ];
        }

        $grouped[$groupKey]['items'][] = [
            'itemCode' => $itemCodes[$i],
            'itemName' => $itemNames[$i],
            'itemUom' => $itemUoms[$i],
            'quantity' => $quantities[$i],
            'assetQuantity' => $assetQuantities[$i],
            'alasan' => $alasan[$i],
            'keteranganAsset' => $keteranganAsset[$i]
        ];

    }

    // Lakukan insert per grup
    foreach ($grouped as $group) {
        $docNum = "ASSET-" . date('YmdHis');

        // Insert ke header
        $sqlHeader = "INSERT INTO InventoriAssetHeader 
         (DocNum, DocDate,WarehouseFrom,WarehouseTo, DocTrans,TermsAsset, DocPriority, ApprovalUser, ApprovalUserName,ApprovalProgress, ApprovalStatus, Remarks, CreatedBy,StatusDoc) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?);SELECT SCOPE_IDENTITY() AS last_id;";

        $paramsHeader = [
            $docNum,
            $tanggalPermintaan,
            $createdBy,
            $group['warehouse'],
            $jenisPermintaan,
            $terms,
            $jenisPrioritas,
            $amId,
            $approvalusername,
            $approvalprogress,
            $approvalstatus,
            $keterangan,
            $createdBy,
            $statusDoc
        ];

        // Eksekusi insert header dan ambil last inserted ID
        $stmt = sqlsrv_query($conn, $sqlHeader, $paramsHeader);
        if ($stmt === false) {
            sqlsrv_rollback($conn);
            throw new Exception(print_r(sqlsrv_errors(), true));
        }

        sqlsrv_next_result($stmt);
        sqlsrv_fetch($stmt);
        $transID = sqlsrv_get_field($stmt, 0);

        // Insert ke detail
        foreach ($group['items'] as $item) {
            $sqlDetail = "INSERT INTO InventoriAssetDetail 
           (TransID, ItemCode, ItemName, ItemUom, Quantity, ConditionAsset, Reason,Remarks, WarehouseFrom,WarehouseTo,CreatedBy) 
            VALUES (?,?,?,?,?,?,?,?,?,?,?);";

            if ($jenisPermintaan == 3) {
                if ($group['warehouse'] == 'Project') {
                    $fixarea = '';
                } else {
                    $fixarea = $area;
                }
            } else {
                $fixarea = '';
            }

            $paramsDetail = [
                $transID,
                $item['itemCode'],
                $item['itemName'],
                $item['itemUom'],
                $item['quantity'],
                $group['kondisi'],
                $item['alasan'],
                $item['keteranganAsset'],
                $createdBy,
                $group['warehouse'] . " " . $fixarea,
                $createdBy,
            ];
            $stmtDetail = sqlsrv_query($conn, $sqlDetail, $paramsDetail);
            if ($stmtDetail === false) {
                sqlsrv_rollback($conn);
                throw new Exception(print_r(sqlsrv_errors(), true));
            }

            $sqlUpdateMaster = "UPDATE MasterAssets SET TransFlag = 1 WHERE ItemCode = ? AND Warehouse =?";
            $paramsUpdate = [$item['itemCode'],$group['warehouse']];
            $stmtUpdate = sqlsrv_query($conn, $sqlUpdateMaster, $paramsUpdate);
            if ($stmtUpdate === false) {
                sqlsrv_rollback($conn);
                throw new Exception("Gagal update MasterAssets: " . print_r(sqlsrv_errors(), true));
            }

        }
        $generatedDocs[] = [
            'docNum' => $docNum,
            'warehouseTo' => $group['warehouse'] == 'Project' ? 'Project' : $group['warehouse'] . " " . $area
        ];

        // === Insert Approval AM ===
        $sqlApprovalAM = "
        INSERT INTO InventoriApprovalAsset 
            (TransID, DocTrans, UserIDApproval, UserNameApproval, StatusApproval, CreatedBy, ApprovalStep)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $paramsAM = [
            $transID,
            $jenisPermintaan,
            $amId,
            $amName,
            'Menunggu Approval AM',
            $createdBy,
            1
        ];

        $stmtApprovalAM = sqlsrv_query($conn, $sqlApprovalAM, $paramsAM);
        if ($stmtApprovalAM === false) {
            sqlsrv_rollback($conn);
            $errors = sqlsrv_errors();
            echo json_encode([
                "status" => "error",
                "message" => "Gagal menyimpan approval AM.",
                "errors" => $errors
            ]);
            exit;
        }

        // === Insert Approval RM ===
        $sqlApprovalRM = "
        INSERT INTO InventoriApprovalAsset 
            (TransID, DocTrans, UserIDApproval, UserNameApproval, StatusApproval, CreatedBy, ApprovalStep)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $paramsRM = [
            $transID,
            $jenisPermintaan,
            $rmId,
            $rmName,
            'Menunggu Approval RM',
            $createdBy,
            2
        ];

        $stmtApprovalRM = sqlsrv_query($conn, $sqlApprovalRM, $paramsRM);
        if ($stmtApprovalRM === false) {
            sqlsrv_rollback($conn);
            $errors = sqlsrv_errors();
            echo json_encode([
                "status" => "error",
                "message" => "Gagal menyimpan approval AM.",
                "errors" => $errors
            ]);
            exit;
        }

        // === Insert Approval Distribusi ===
        // $sqlApprovalDistribusi = "
        // INSERT INTO InventoriApprovalAsset 
        //     (TransID, DocTrans, UserIDApproval, UserNameApproval, StatusApproval, CreatedBy, ApprovalStep)
        // VALUES (?, ?, ?, ?, ?, ?, ?)";

        // $paramsDistribusi = [
        //     $transID,
        //     $jenisPermintaan,
        //     $userid_distribusick,
        //     $distribusi_area,
        //     'Menunggu Approval Distribusi',
        //     $createdBy,
        //     3
        // ];

        // $stmtApprovalDistribusi = sqlsrv_query($conn, $sqlApprovalDistribusi, $paramsDistribusi);
        // if ($stmtApprovalDistribusi === false) {
        //     sqlsrv_rollback($conn);
        //     $errors = sqlsrv_errors();
        //     echo json_encode([
        //         "status" => "error",
        //         "message" => "Gagal menyimpan approval Distribusi.",
        //         "errors" => $errors
        //     ]);
        //     exit;
        // }

        // === Tentukan user approval WH ===
        if ($jenisPermintaan == 3) {
            if ($group['warehouse'] == 'CK') {
                $userIDApproval = $userid_ck;
                $userNameApproval = $ck_area;
            } elseif ($group['warehouse'] == 'IT') {
                $userIDApproval = $userid_it;
                $userNameApproval = $it_area;
            } elseif ($group['warehouse'] == 'ENG') {
                $userIDApproval = $userid_eng;
                $userNameApproval = $eng_area;
            } elseif ($group['warehouse'] == 'Project') {
                $userIDApproval = $userid_proj;
                $userNameApproval = $proj_area;
            } else {
                $userIDApproval = '';
                $userNameApproval = '';
            }
        } else {
            $store = $group['warehouse'];
            $servername = "192.168.2.136";
            $username = "root";
            $password = "aas260993";
            $dbname = "voucher_trial";
            $connmysqli = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
            $tsql = "SELECT id_user FROM mst_user WHERE nama= '$store'";
            $stmt = mysqli_query($connmysqli, $tsql);
            $user = mysqli_fetch_array($stmt);
            $userIDApproval = $user['id_user'];
            $userNameApproval = $group['warehouse'];
        }

        // === Insert Approval WH ===
        $sqlApprovalWH = "
        INSERT INTO InventoriApprovalAsset 
            (TransID, DocTrans, UserIDApproval, UserNameApproval, StatusApproval, CreatedBy, ApprovalStep)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $paramsApprovalWH = [
            $transID,
            $jenisPermintaan,
            $userIDApproval,
            $userNameApproval,
            'Menunggu Verifikasi Warehouse',
            $createdBy,
            3
        ];

        $stmtApprovalWH = sqlsrv_query($conn, $sqlApprovalWH, $paramsApprovalWH);
        if ($stmtApprovalWH === false) {
            sqlsrv_rollback($conn);
            $errors = sqlsrv_errors();
            echo json_encode([
                "status" => "error",
                "message" => "Gagal menyimpan approval Warehouse.",
                "errors" => $errors
            ]);
            exit;
        }

        // === Insert Log ke InventoriAssetLog ===
        $sqlLog = "
        INSERT INTO InventoriAssetLog 
            (TransID, DocProgress, Remarks, CreatedBy) 
        VALUES (?, ?, ?, ?)";

        $paramsLog = [
            $transID,
            'Created',
            'Dokumen dibuat',
            $createdBy
        ];

        $stmtLog = sqlsrv_query($conn, $sqlLog, $paramsLog);
        if ($stmtLog === false) {
            sqlsrv_rollback($conn);
            $errors = sqlsrv_errors();
            echo json_encode([
                "status" => "error",
                "message" => "Gagal menyimpan log Inventori Asset.",
                "errors" => $errors
            ]);
            exit;
        }

    }

    // Kirim email ke AM
    $mail = new PHPMailer();
    try {
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host = "mail.multirasa.co.id";
        $mail->SMTPAuth = true;
        $mail->Username = "info.voucherrequest@multirasa.co.id";
        $mail->Password = 'yoshimulti';
        $mail->Port = 465;
        $mail->SMTPSecure = "ssl";

        $am = 'angga.aditya@multirasa.co.id';
        // Penerima
        $mail->setFrom('info.voucherrequest@multirasa.co.id', 'Sistem Approval');
        $mail->addAddress($am);

        $mail->isHTML(true);
        $mail->Subject = "Notifikasi Pengajuan Inventaris Assets";
        $mail->Body = 'Dear Pa ' . $am . ',<br><br>' .
            'Store ' . $createdBy . ' telah mengajukan permohonan inventaris assets dengan detail sebagai berikut :<br>' .
            '<table border="1" cellpadding="5" cellspacing="0">' .
            '<tr><th>DocNum</th><th>WarehouseTo</th></tr>';
        foreach ($generatedDocs as $doc) {
            $mail->Body .= '<tr><td>' . $doc['docNum'] . '</td><td>' . $doc['warehouseTo'] . '</td></tr>';
        }
        $mail->Body .= '</table><br>' .
            'Silakan cek di sistem untuk detail lebih lanjut.<br><br>' .
            'Terima kasih atas perhatian Anda,<br>' .
            'Hormat Kami,<br>' .
            'Sistem Inventaris Assets';

        if ($mail->send()) {
            sqlsrv_commit($conn);
            $response['success'] = true;
            $response['message'] = "Data berhasil disimpan";
            $response['data'] = $generatedDocs;
        } else {
            sqlsrv_rollback($conn);
            $response['success'] = false;
            $response['message'] = "Terjadi kesalahan: " . $mail->ErrorInfo . ", " . $e->getMessage();
        }
    } catch (Exception $e) {
        $response['message'] .= " Email gagal dikirim. Error: {$mail->ErrorInfo}";
    }

} catch (Exception $e) {
    sqlsrv_rollback($conn);
    $response['success'] = false;
    $response['message'] = "Terjadi kesalahan: " . $e->getMessage();
}
echo json_encode($response);