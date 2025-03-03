<?php
// Koneksi ke SQL Server
define('SQLSRV_SERVER', '192.168.1.5');
define('SQLSRV_DATABASE', 'HEADQ');
define('SQLSRV_USERNAME', 'sa');
define('SQLSRV_PASSWORD', 'Mrn.14');

$connectionInfo = [
    "Database" => SQLSRV_DATABASE,
    "UID" => SQLSRV_USERNAME,
    "PWD" => SQLSRV_PASSWORD,
    "CharacterSet" => "UTF-8"
];
$sqlsrv_conn = sqlsrv_connect(SQLSRV_SERVER, $connectionInfo);
if ($sqlsrv_conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Koneksi ke MySQL
define('MYSQL_SERVER', '192.168.1.231');
define('MYSQL_DATABASE', 'gas');
define('MYSQL_USERNAME', 'root');
define('MYSQL_PASSWORD', 'And142857');

$mysqli_conn = new mysqli(MYSQL_SERVER, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
if ($mysqli_conn->connect_error) {
    die("Connection failed: " . $mysqli_conn->connect_error);
}


$today = date('Y-m-d', strtotime('+1 day'));
$currentDay = date('d', strtotime($today)); 

if ($currentDay <= 5) {
    $startDate = date('Y-m-01', strtotime('first day of last month')); 
    $endDate = $today;
} else {
    $startDate = date('Y-m-01');
    $endDate = $today;
}

$sql = "SELECT * FROM OPENQUERY(SAPHANA,'
                        SELECT 
                        PR.\"DocDate\",
                        WEBPR.\"U_ReqDate\",
                        WEBPR.\"U_Remarks\",
                        PR.\"DocNum\" AS \"DocPR\",
                        CASE PR.\"DocStatus\"
                            WHEN ''C''
                            THEN (
                            CASE PR.\"CANCELED\"
                                WHEN ''Y''
                                    THEN ''Canceled''
                                ELSE ''Closed''
                                END
                                )
                            WHEN ''O''
                                THEN ''Open''
                            ELSE PR.\"DocStatus\"
                        END AS \"StatusPR\",
                        PO.\"CardName\",
                        PR1.\"WhsCode\",
                        PR1.\"ItemCode\",
                        PR1.\"Dscription\",
                        SUM(PR1.\"Quantity\") AS \"QtyPR\",
                        SUM(PR1.\"OpenQty\") AS \"QtyPROpen\",
                        PO.\"DocNum\" AS \"DocPO\",
                        PO.\"DocDate\" AS \"DocDatePO\",
                        SUM(PO1.\"Quantity\") AS \"QtyPO\",
                        SUM(PO1.\"OpenQty\") AS \"QtyPOOpen\",
                        SUM(PO1.\"LineTotal\") AS \"TotalPO\"
                        FROM MRN_LIVE.OPRQ PR
                        INNER JOIN  MRN_LIVE.PRQ1 PR1 ON PR.\"DocEntry\"=PR1.\"DocEntry\"
                        LEFT  JOIN  MRN_LIVE.POR1 PO1 ON PR.\"DocEntry\"=PO1.\"BaseEntry\" AND PR1.\"LineNum\"=PO1.\"BaseLine\"
                        LEFT  JOIN  MRN_LIVE.OPOR PO  ON PO1.\"DocEntry\"=PO.\"DocEntry\"
                        LEFT  JOIN MRN_LIVE.\"@ST_PR_DOCWEBH\" WEBPR ON PR.\"DocNum\"=WEBPR.\"U_DocNumSAP\"
                        WHERE 
                        PR.\"DocType\" =''I''
                        AND WEBPR.\"U_ReqDate\" BETWEEN ''$startDate'' AND ''$endDate''
                        AND PR.\"CANCELED\"=''N''
                        --AND PR.\"DocStatus\"=''C''
                        AND PR1.\"ItemCode\" IN (''SGAS00001'',''CHEM00042'')
                        AND PR1.\"WhsCode\" NOT LIKE ''%CKWH%'' 
                        AND PR1.\"WhsCode\" NOT LIKE ''%WHEG%''
                        GROUP BY 
                        PR1.\"WhsCode\",
                        PR.\"DocDate\",
                        WEBPR.\"U_ReqDate\",
                        WEBPR.\"U_Remarks\",
                        PR.\"DocStatus\",
                        PR.\"CANCELED\",    
                        PO.\"CardName\",
                        PR.\"DocNum\",
                        PO.\"DocNum\",
                        PO.\"DocDate\",
                        PR1.\"ItemCode\",
                        PR1.\"Dscription\"
                        ORDER BY 
                        PR.\"DocDate\",
                        PR1.\"WhsCode\"
                        DESC
                ')";
    $stmt = sqlsrv_query($sqlsrv_conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $jenis_gas ='LPG';
    $comdiv_user='ADMIN.COMDIV';
    $progress_permintaan=2;
   

    $insertData = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

        if($row['StatusPR']=='Canceled'){
             $comdiv_user_status='Ditolak';
             $status='';
        }else{
             $comdiv_user_status='Disetujui';
             $status='Menunggu Kedatangan Barang';
        }

        $DocDate = ($row['DocDate'] instanceof DateTime) ? $row['DocDate']->format('Y-m-d') : null;
        $uReqDate = ($row['U_ReqDate'] instanceof DateTime) ? $row['U_ReqDate']->format('Y-m-d') : null;

        $insertData[] = sprintf(
            "(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            empty($row['DocPR']) ? "NULL" : "'" . $mysqli_conn->real_escape_string($row['DocPR']) . "'",
            empty($row['WhsCode']) ? "NULL" : "'" . $mysqli_conn->real_escape_string($row['WhsCode']) . "'",
            empty($DocDate) ? "NULL" : "'" . $mysqli_conn->real_escape_string($DocDate) . "'",
            "'LPG'", 
            empty($row['Dscription']) ? "NULL" : "'" . $mysqli_conn->real_escape_string($row['Dscription']) . "'",
            empty($row['QtyPR']) ? "NULL" : $mysqli_conn->real_escape_string($row['QtyPR']),
            empty($uReqDate) ? "NULL" : "'" . $mysqli_conn->real_escape_string($uReqDate) . "'",
            empty($row['DocPR']) ? "NULL" : "'" . $mysqli_conn->real_escape_string($row['DocPR']) . "'", 
            empty($row['DocPO']) ? "NULL" : "'" . $mysqli_conn->real_escape_string($row['DocPO']) . "'", 
            empty($row['CardName']) ? "NULL" : "'" . $mysqli_conn->real_escape_string($row['CardName']) . "'", 
            empty($row['TotalPO']) ? "NULL" : "'" . $mysqli_conn->real_escape_string($row['TotalPO']) . "'", 
            empty($comdiv_user) ? "NULL" : "'" . $mysqli_conn->real_escape_string($comdiv_user) . "'",
            empty($comdiv_user_status) ? "NULL" : "'" . $mysqli_conn->real_escape_string($comdiv_user_status) . "'",
            empty($progress_permintaan) ? "NULL" : "'" . $mysqli_conn->real_escape_string($progress_permintaan) . "'",
            empty($row['U_Remarks']) ? "NULL" : "'" . $mysqli_conn->real_escape_string($row['U_Remarks']) . "'",
            empty($status) ? "NULL" : "'" . $mysqli_conn->real_escape_string($status) . "'",
            empty($row['WhsCode']) ? "NULL" : "'" . $mysqli_conn->real_escape_string($row['WhsCode']) . "'"
        );

        
    }

    if (!empty($insertData)) {
        $sql = "
            INSERT INTO header_gas (
                no_permintaan,
                store,
                tanggal_permintaan,
                jenis_gas,
                tipe_tabung,
                jumlah_permintaan,
                tanggal_kedatangan,
                no_pr,
                no_po,
                nama_vendor,
                harga_tabung_gas,
                comdiv_user,
                comdiv_user_status,
                progress_permintaan,
                keterangan_permintaan,
                status,
                created_by
            ) VALUES " . implode(", ", $insertData) . "
            ON DUPLICATE KEY UPDATE 
                no_permintaan = VALUES(no_permintaan),
                store = VALUES(store),
                tanggal_permintaan = VALUES(tanggal_permintaan),
                jenis_gas = VALUES(jenis_gas),
                tipe_tabung = VALUES(tipe_tabung),
                jumlah_permintaan = VALUES(jumlah_permintaan),
                tanggal_kedatangan = VALUES(tanggal_kedatangan),
                status = VALUES(status),
                no_pr = VALUES(no_pr),
                no_po = VALUES(no_po),
                nama_vendor = VALUES(nama_vendor),
                harga_tabung_gas = VALUES(harga_tabung_gas),
                comdiv_user = VALUES(comdiv_user),
                comdiv_user_status = VALUES(comdiv_user_status),
                progress_permintaan = VALUES(progress_permintaan),
                keterangan_permintaan = VALUES(keterangan_permintaan),
                status = VALUES(status),
                created_by = VALUES(created_by)";
        
        if (!$mysqli_conn->query($sql)) {
            echo "Error executing bulk insert/update: " . $mysqli_conn->error;
        }
    }

// echo $sql;
// Tutup koneksi
$mysqli_conn->close();
sqlsrv_free_stmt($stmt);
sqlsrv_close($sqlsrv_conn);
?>

