<?php
session_start();
error_reporting(0);

$dsn = "hanamrnprod";
$user = "DBADMIN";
$password = "Passw0rd";

$conn = odbc_connect($dsn, $user, $password);

// if (!$conn) {
//     exit("Connection failed: " . odbc_errormsg());
// } else {
//     echo "Connected successfully!";
// }

$startdate =$_POST['start_date'];
$enddate =$_POST['end_date'];
if($_SESSION["id_divisi"] == 12 || $_SESSION["id_divisi"] == 11 || $_SESSION["id_divisi"] == 5){
    $store =$_POST['toko'];
}else{
    $store =$_SESSION['nama'];
}

$queryh = 'CALL "MRN_LIVE".MRN_WASTE_PORTAL(
    \''.$store.'\', 
    \''.$startdate.'\',        
    \''.$enddate.'\'          
);';

$resulth = odbc_exec($conn, $queryh);

$start_date = str_replace("-", "", $startdate);
$end_date = str_replace("-", "", $enddate);

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=report_waste_" . $start_date . "_" . $end_date . ".xls");
header('Cache-Control: max-age=0');

?>
<!DOCTYPE html>
<html>
<head>
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 5px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
</head>
<body>
            <table class="table table-bordered table-striped"  style="width: 100%;">
            <thead>
            <tr>
                <th rowspan='2'>No</th>
                <th rowspan='2'>Store</th>
                <th rowspan='2'>DocDate</th>
                <th rowspan='2'>DocNum</th>
                <th rowspan='2'>Item Code</th>
                <th rowspan='2'>Item Description</th>
                <th colspan='5' class='text-center'>Waste Book</th>
                <th colspan='2' class='text-center'>Inventori</th>
                <tr>
                <th>UoM</th>
                <th>Overholding Time</th>
                <th>Human Error</th>
                <th>Sisa Closing</th>
                <th>Total Waste</th>
                <th>Qty</th>
                <th>UoM</th>
                 </tr>    
            </tr>
            </thead>
            <tbody>
<?php
$no=1;
$totalOverholding = 0;
$totalHumanError = 0;
$totalSisaClosing = 0;
$totalWaste = 0;
$totalSummaryInvQty = 0;
while ($row = odbc_fetch_array($resulth)) {
    echo "<tr>";
    echo "<td>" . $no . "</td>";
    echo "<td>" . $row['U_StoreCode'] . "</td>";
    echo "<td>" . $row['U_DocDate'] . "</td>";
    echo "<td>" . $row['DocNum'] . "</td>";
    echo "<td>" . $row['U_ItemCode'] . "</td>";
    echo "<td>" . $row['U_ItemDesc'] . "</td>";
    echo "<td>" . $row['U_UomCode'] . "</td>";
    echo "<td>" . number_format($row['Overholding_Qty']) . "</td>";
    echo "<td>" . number_format($row['Human_Error_Qty']) . "</td>";
    echo "<td>" . number_format($row['Sisa_Closing_Qty']) . "</td>";
    echo "<td>" . number_format($row['TotalWaste']) . "</td>";
    echo "<td>" . number_format($row['SummaryInvQty'], 4, '.', '') . "</td>";
    echo "<td>" . $row['U_InvUom'] . "</td>";
    echo "</tr>";
    $no++;
    $totalOverholding += $row['Overholding_Qty'];
    $totalHumanError += $row['Human_Error_Qty'];
    $totalSisaClosing += $row['Sisa_Closing_Qty'];
    $totalWaste += $row['TotalWaste'];
    $totalSummaryInvQty += $row['SummaryInvQty'];
}
// Output the grand total row
echo "<tr style='font-weight: bold;'>";
echo "<td colspan='7' class='text-center'>Grand Total</td>";
echo "<td>" . number_format($totalOverholding) . "</td>";
echo "<td>" . number_format($totalHumanError) . "</td>";
echo "<td>" . number_format($totalSisaClosing) . "</td>";
echo "<td>" . number_format($totalWaste) . "</td>";
echo "<td>" . number_format($totalSummaryInvQty, 4, '.', '') . "</td>";
echo "<td></td>"; 
echo "</tr>";
echo " </tbody></table></div>";
odbc_close($conn);
?>
</body>
</html> 