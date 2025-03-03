<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

$serverNameStore = "192.168.1.5"; 
$connectionOptionsStore = array(
    "Database" => "role",
    "Uid" => "sa",
    "PWD" => "Mrn.14"
);

$connStore = sqlsrv_connect($serverNameStore, $connectionOptionsStore);
if ($connStore === false) {
    die(print_r(sqlsrv_errors(), true));
}

$serverNameLogSales = "192.168.1.5"; 
$connectionOptionsLogSales = array(
    "Database" => "HEADQ",
    "Uid" => "sa",
    "PWD" => "Mrn.14"
);

$connLogSales = sqlsrv_connect($serverNameLogSales, $connectionOptionsLogSales);
if ($connLogSales === false) {
    die(print_r(sqlsrv_errors(), true));
}

$sql = "SELECT storeCode, storeHost FROM storesett
  WHERE active=1
  storeCode in ('PKM','SCY','MGI','JSI','ATS')
 ORDER BY storeCode ASC";
$stmt = sqlsrv_query($connStore, $sql);

if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$results = array();
$errorMessages = array();
$selisihResults = array();

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $storeCode = $row['storeCode'];
    $storeHost = $row['storeHost'];
    $result = cekSalesPerStore($connLogSales, $storeHost, $storeCode, $errorMessages);
    $results[] = $result;

    if ($result['selisih'] > 0) {
        $selisihResults[] = $result;
    }
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($connStore);

// echo "<html><head><title>Sales Report</title></head><body>";
// echo "<h1>Sales Report</h1>";

// echo "<table border='1'>
// <tr>
//     <th>Store Code</th>
//     <th>Tanggal</th>
//     <th>Selisih</th>
//     <th>Sales HO</th>
//     <th>Sales Store</th>
//     <th>Error Message</th>
// </tr>";

// // Display results
// foreach ($results as $result) {
//     echo "<tr>
//         <td>{$result['storeCode']}</td>
//         <td>{$result['tanggal']}</td>
//         <td>{$result['selisih']}</td>
//         <td>{$result['salesho']}</td>
//         <td>{$result['salesstore']}</td>
//         <td>{$result['errorMessage']}</td>
//     </tr>";
// }

// echo "</table>";

// if (!empty($errorMessages)) {
//     echo "<h2>Error Messages</h2>";
//     echo "<ul>";
//     foreach ($errorMessages as $errorMessage) {
//         echo "<li>$errorMessage</li>";
//     }
//     echo "</ul>";
// }

// echo "</body></html>";

if (!empty($selisihResults)) {

        $mail = new PHPMailer(true);

    try {

        $mail->isSMTP();
        $mail->Host = 'mail.multirasa.co.id'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'info.voucherrequest@multirasa.co.id'; 
        $mail->Password = 'yoshimulti'; 
        $mail->SMTPSecure = 'ssl'; 
        $mail->Port = 465; 

        $mail->setFrom('info.voucherrequest@multirasa.co.id', 'Cek Selisih Sales Store');
        $mail->addAddress('angga.aditya@multirasa.co.id','handri.yulianto@multirasa.co.id','jansen.anrico@multirasa.co.id'); 
        // 'handri.yulianto@multirasa.co.id'
        $mail->isHTML(true);
        $mail->Subject = 'Store Sales Report';
        $tableHtml = '<table border="1">
                        <tr>
                            <th>Store Code</th>
                            <th>Tanggal Sales</th>
                            <th>Sales HO</th>
                            <th>Sales Store</th>
                            <th>Selisih</th>
                        </tr>';
                        foreach ($selisihResults as $result) {
                            $salesho = number_format($result['salesho']);
                            $salesstore = number_format($result['salesstore']);
                            $selisih = number_format($result['selisih']);
                            $tableHtml .= "<tr>
                                <td>{$result['storeCode']}</td>
                                <td>{$result['tanggal']}</td>
                                <td>{$salesho}</td>
                                <td>{$salesstore}</td>
                                <td>{$selisih}</td>
                            </tr>";
                        }
        $tableHtml .= '</table>';
        $mail->Body = "<html><body>";
        $mail->Body .= "<h3>Store Sales Report</h3>";
        $mail->Body .= $tableHtml;
        $mail->Body .= "</body></html>";

        $mail->send();
        echo 'Email has been sent';

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function cekSalesPerStore($connLogSales, $storeHost, $storeCode, &$errorMessages) {
    $tanggal = date('Y-m-d');

    try {
        $sql = "EXEC sales_store @date1 = ?, @date2 = ?, @str=?, @ip=?";
        $params = array(
            array($tanggal, SQLSRV_PARAM_IN),
            array($tanggal, SQLSRV_PARAM_IN),
            array($storeCode, SQLSRV_PARAM_IN),
            array($storeHost, SQLSRV_PARAM_IN)
        );

        $stmt = sqlsrv_query($connLogSales, $sql, $params);

        if ($stmt === false) {
            throw new Exception(print_r(sqlsrv_errors(), true));
        }

        $selisih = 'No sales data';
        $salesho = 'No sales data';
        $salesstore = 'No sales data';

        if (sqlsrv_has_rows($stmt)) {
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $selisih = isset($row['SELISIH']) ? $row['SELISIH'] : 'N/A';
            $salesho = isset($row['SALES HO']) ? $row['SALES HO'] : 'BELUM MASUK HO';
            $salesstore = isset($row['SALES STORE']) ? $row['SALES STORE'] : 'SALES STORE TIDAK ADA';
        }

        sqlsrv_free_stmt($stmt);
        return array(
            'storeCode' => $storeCode,
            'tanggal' => $tanggal,
            'selisih' => $selisih,
            'salesho' => $salesho,
            'salesstore' => $salesstore,
            'errorMessage' => ''
        );

    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        $errorMessages[] = "Error for Store: $storeCode - $errorMessage";
        
        return array(
            'storeCode' => $storeCode,
            'tanggal' => $tanggal,
            'selisih' => 'No sales data',
            'salesho' => 'No sales data',
            'salesstore' => 'No sales data',
            'errorMessage' => $errorMessage
        );
    }
}

