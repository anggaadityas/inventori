<?php
error_reporting(0);
session_start();
// Koneksi ke SQL Server
$serverName = "192.168.2.135"; // Ganti dengan nama server
$database = "DB_SCK"; // Ganti dengan nama database
$username = "sa"; // Ganti dengan username SQL Server
$password = "And142857"; // Ganti dengan password SQL Server

try {
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Ambil data header
        $jenisPermintaan = $_POST['jenis_permintaan'];
        $jenisPrioritas = $_POST['jenis_prioritas'];
        $terms = $_POST['terms'];
        $tanggalPermintaan = $_POST['tanggal_permintaan'];
        $keterangan = $_POST['keterangan'];
        $createdBy = $_SESSION['nama']; 

        // Buat nomor dokumen (DocNum)
        $docNum = "ASSET-" . date("YmdHis");

        $serverNameHO = "192.168.1.5";
        // $serverNameHO = "portal.multirasa.co.id";
        $connectionInfoHO = array("Database" => "role", "UID" => "sa", "PWD" => "Mrn.14");
        $connHO = sqlsrv_connect($serverNameHO, $connectionInfoHO);
        if ($connHO === false) {
            die(print_r(sqlsrv_errors(), true));
        }
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
        if ($stmtck === false) {
            die(print_r(sqlsrv_errors(), true));
        }
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

        $amId = 386;
        $amName = 'AM';
        $approvalprogress = 1; // Status awal
        $approvalstatus = "Menunggu Approval AM";
        $statusDoc = 'Open';
        $approvalusername = 'AM';

        // Simpan ke InventoriAssetHeader
        $sqlHeader = "INSERT INTO InventoriAssetHeader 
            (DocNum, DocDate,StoreCode, DocTrans,TermsAsset, DocPriority, ApprovalUser, ApprovalUserName,ApprovalProgress, ApprovalStatus, Remarks, CreatedBy,StatusDoc) 
              VALUES (:docNum, :docDate,:StoreCode, :docTrans,:TermsAsset, :DocPriority, :ApprovalUser, :ApprovalUserName, :ApprovalProgress, :ApprovalStatus, :remarks, :createdBy,:StatusDoc)";

        $stmt = $conn->prepare($sqlHeader);
        $stmt->execute([
            ':docNum' => $docNum,
            ':docDate' => $tanggalPermintaan,
            ':StoreCode' => $createdBy,
            ':docTrans' => $jenisPermintaan,
            ':TermsAsset' => $terms,
            ':DocPriority' => $jenisPrioritas,
            ':ApprovalUser' => $amId,
            ':ApprovalUserName' => $approvalusername,
            ':ApprovalProgress' => $approvalprogress,
            ':ApprovalStatus' => $approvalstatus,
            ':remarks' => $keterangan,
            ':createdBy' => $createdBy,
            ':StatusDoc' => $statusDoc
        ]);

        // Ambil TransID terakhir
        $transID = $conn->lastInsertId();

        // Loop data detail (array)
        foreach ($_POST['itemName'] as $key => $itemName) {
            $itemCode = $_POST['itemCode'][$key];
            $itemUom = $_POST['itemUom'][$key];
            $quantity = $_POST['quantity'][$key];
            $kondisiAsset = $_POST['kondisiAsset'][$key] ?? null;
            $alasan = $_POST['alasan'][$key] ?? null;
            $remarks = $_POST['keteranganAsset'][$key] ?? null;
            $WarehouseFrom = $_SESSION['nama'];

            if ($kondisiAsset == '1') {
                $WarehouseTo = $_POST['AssetConditionOk'][$key] ?? null;
            } else {
                $WarehouseTo = $_POST['AssetConditionNonOk'][$key] ?? null;
            }

            // Insert ke InventoriAssetDetail
            $sqlDetail = "INSERT INTO InventoriAssetDetail 
                (TransID, ItemCode, ItemName, ItemUom, Quantity, ConditionAsset, Reason,Remarks, WarehouseFrom,WarehouseTo,CreatedBy) 
                VALUES (:transID, :itemCode, :itemName, :itemUom, :quantity, :conditionAsset, :Reason,:Remarks, :warehousefrom, :warehouseto, :createdBy)";

            if ($WarehouseTo == 'Project') {
                $fixarea = '';
            } else {
                $fixarea = $area;
            }

            $stmtDetail = $conn->prepare($sqlDetail);
            $stmtDetail->execute([
                ':transID' => $transID,
                ':itemCode' => $itemCode,
                ':itemName' => $itemName,
                ':itemUom' => $itemUom,
                ':quantity' => $quantity,
                ':conditionAsset' => $kondisiAsset,
                ':Reason' => $alasan,
                ':Remarks' => $remarks,
                ':warehousefrom' => $WarehouseFrom,
                ':warehouseto' => $WarehouseTo . " " . $fixarea,
                ':createdBy' => $createdBy
            ]);
        }

        // Insert ke InventoriAssetLog
        $sqlLog = "INSERT INTO InventoriAssetLog (TransID, DocProgress, Remarks, CreatedBy) 
                   VALUES (:transID, 'Created', 'Dokumen dibuat', :createdBy)";
        $stmtLog = $conn->prepare($sqlLog);
        $stmtLog->execute([
            ':transID' => $transID,
            ':createdBy' => $createdBy
        ]);

        // Ambil semua warehouse tujuan unik dari transaksi
        $warehouseList = [];
        foreach ($_POST['itemName'] as $key => $itemName) {
            $WarehouseTo = ($_POST['kondisiAsset'][$key] == '1') ? $_POST['AssetConditionOk'][$key] : $_POST['AssetConditionNonOk'][$key];
            if (!in_array($WarehouseTo, $warehouseList) && !empty($WarehouseTo)) {
                $warehouseList[] = $WarehouseTo;
            }
        }

        // Insert Approval AM (hanya sekali)
        $sqlApprovalAM = "INSERT INTO InventoriApprovalAsset (TransID, DocTrans, UserIDApproval,UserNameApproval, StatusApproval, CreatedBy, ApprovalStep)
                SELECT 
                    :transID, 
                    :docTrans1, 
                    $amId, 
                    '$amName', 
                    'Menunggu Approval AM',
                    :createdBy, 
                    1
                FROM MasterApprovalDetail 
                WHERE ApprovalID = (
                    SELECT TOP 1 ID FROM MasterApprovalHeader 
                    WHERE DocTrans = :docTrans2 AND IsActive = 1
                ) 
                AND ApprovalStep = 1
                ";

        // $sqlDebug = $sqlApprovalAM;
        // $sqlDebug = str_replace(":transID", "'".$transID."'", $sqlDebug);
        // $sqlDebug = str_replace(":docTrans", "'".$jenisPermintaan."'", $sqlDebug);
        // $sqlDebug = str_replace(":createdBy", "'".$createdBy."'", $sqlDebug);
        // echo "<pre>$sqlDebug</pre>";

        $stmtApprovalAM = $conn->prepare($sqlApprovalAM);
        $stmtApprovalAM->execute([
            ':transID' => $transID,
            ':docTrans1' => $jenisPermintaan, // Parameter pertama
            ':docTrans2' => $jenisPermintaan, // Parameter kedua (beda alias)
            ':createdBy' => $createdBy
        ]);

        // Insert Approval Distribusi (hanya sekali)
        $sqlApprovalDistribusi = "INSERT INTO InventoriApprovalAsset (TransID, DocTrans, UserIDApproval,UserNameApproval, StatusApproval, CreatedBy, ApprovalStep)
                SELECT 
                    :transID, 
                    :docTrans1, 
                    $userid_distribusick, 
                    '$distribusi_area', 
                    'Menunggu Approval Distribusi',
                    :createdBy, 
                    2
                FROM MasterApprovalDetail 
                WHERE ApprovalID = (
                    SELECT TOP 1 ID FROM MasterApprovalHeader 
                    WHERE DocTrans = :docTrans2 AND IsActive = 1
                ) 
                AND ApprovalStep = 2
                ";

        // $sqlDebug = $sqlApprovalDistribusi;
        // $sqlDebug = str_replace(":transID", "'".$transID."'", $sqlDebug);
        // $sqlDebug = str_replace(":docTrans", "'".$jenisPermintaan."'", $sqlDebug);
        // $sqlDebug = str_replace(":createdBy", "'".$createdBy."'", $sqlDebug);
        // echo "<pre>$sqlDebug</pre>";

        $stmtApprovalDistribusi = $conn->prepare($sqlApprovalDistribusi);
        $stmtApprovalDistribusi->execute([
            ':transID' => $transID,
            ':docTrans1' => $jenisPermintaan, // Parameter pertama
            ':docTrans2' => $jenisPermintaan, // Parameter kedua (beda alias)
            ':createdBy' => $createdBy
        ]);

        // Insert Approval Warehouse berdasarkan WarehouseTo
        $nourutwh = 2;
        foreach ($warehouseList as $warehouse) {
            $nourutwh++;
            if ($warehouse == 'CK') {
                $userIDApproval = $userid_ck;
                $userNameApproval = $ck_area;
            } elseif ($warehouse == 'IT') {
                $userIDApproval = $userid_it;
                $userNameApproval = $it_area;
            } elseif ($warehouse == 'ENG') {
                $userIDApproval = $userid_eng;
                $userNameApproval = $eng_area;
            } elseif ($warehouse == 'Project') {
                $userIDApproval = $userid_proj;
                $userNameApproval = $proj_area;
            } else {
                $userIDApproval = '';
                $userNameApproval = '';
            }
            $sqlApprovalWH = "INSERT INTO InventoriApprovalAsset (TransID, DocTrans, UserIDApproval, UserNameApproval,StatusApproval, CreatedBy, ApprovalStep)
                      SELECT :transID, :docTrans1, '$userIDApproval','$userNameApproval', 'Menunggu Verifikasi Warehouse', :createdBy, $nourutwh
                      FROM MasterApprovalDetail
                      WHERE ApprovalID = (SELECT ID FROM MasterApprovalHeader WHERE DocTrans = :docTrans2 AND IsActive = 1) AND ApprovalStep = 3";

            $stmtApprovalWH = $conn->prepare($sqlApprovalWH);
            $stmtApprovalWH->execute([
                ':transID' => $transID,
                ':docTrans1' => $jenisPermintaan, // Parameter pertama
                ':docTrans2' => $jenisPermintaan, // Parameter kedua (beda alias)
                ':createdBy' => $createdBy
            ]);

        }

        echo json_encode(["status" => "success", "message" => "Data berhasil disimpan", "docNum" => $docNum]);
    }
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Gagal menyimpan: " . $e->getMessage()]);
}
?>