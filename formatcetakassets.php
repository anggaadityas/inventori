<?php
include "db.php";
// error_reporting(0);
$id = $_GET['id'];
$warehouse = $_GET['warehouse'];
$created_by = $_SESSION['nama'];

/* Initiate transaction. */
/* Exit script if transaction cannot be initiated. */
if (sqlsrv_begin_transaction($conn) === false) {
    echo "Could not begin transaction.\n";
    die(print_r(sqlsrv_errors(), true));
}

$sqllog = "INSERT  INTO log_print (
    request_id,
    note,
    created_date,
    created_by
    ) VALUES (
     '$id', 
     '$warehouse',
     getdate(), 
     '$created_by'
      )";
$stmt = sqlsrv_query($conn, $sqllog);

if ($stmt) {
    sqlsrv_commit($conn);

    $sqlprint = "SELECT count(request_id) as totalprint from log_print where request_id='$id' and note like '$warehouse%' and created_by='$created_by'";
    $stmtprint = sqlsrv_query($conn, $sqlprint);
    if ($stmtprint === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $rowprint = sqlsrv_fetch_array($stmtprint, SQLSRV_FETCH_ASSOC);

    $sqlheader = "SELECT a.ID,
        a.DocNum,
		convert(char(10),a.DocDate,126) DocDate,
        a.WarehouseFrom,
        a.WarehouseTo,
        b.TransName,
        a.TermsAsset,
         CASE
    WHEN a.DocPriority=1 THEN 'Normal'
    WHEN a.DocPriority=2 THEN 'Darurat'
    ELSE ''
	END as DocPriority,
        a.Remarks,
        a.ApprovalUser,
        a.ApprovalProgress,
		a.ApprovalStatus,
		a.StatusDoc,
        convert(char(20),a.CreatedDate,120) date_submit 
        FROM InventoriAssetHeader a
        inner join MasterDocTrans b on a.DocTrans=b.ID
        where a.ID='$id'";
    $stmtheader = sqlsrv_query($conn, $sqlheader);
    if ($stmtheader === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $rowheader = sqlsrv_fetch_array($stmtheader, SQLSRV_FETCH_ASSOC);

    $html = '<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Form Permintaan Barang</title>

<style type="text/css">
      * {
        font-family: Verdana, Arial, sans-serif;
        font-weight: bold;
    }
    table{
      font-size:7px;
         border-collapse: collapse;
    }
    .item tr td{
        font-weight: bold;
        font-size:7px;
         border: 1px solid #cecfd5;
    }
    thead tr th{
        font-weight: bold;
        font-size:7px;
         border: 1px solid #cecfd5;
    }
    tfoot tr td{
        font-weight: bold;
        font-size:7px;
        border: 1px solid #cecfd5;
    }
    .gray {
        background-color: lightgray
    }
    ul {list-style-type:none;}
li {list-style-type:none;}
</style>

</head>
<body>';

    $x = 1;
    while ($x <= 2) {

        $html .= '<table width="100%">
                <tr>
                <td width="16%"  align="top"></td>
                <td width="100%"  align="left" style="margin-left:40px;">
                <div style="margin-top:-30px;margin-left:180px;">
                <p style="font-size:12px;"><b>FORM ASSETS</b></p>
                <p style="margin-top:-15px;font-size:12px;"><b>' . $rowheader['DocNum'] . ' </b></p>
                </div>
                </td>
                </tr>
                </table>

                <table width="30%" align="left">
                <tr><td width="40%" style="margin-bottom:10px;">Tanggal Permintaan</td><td>: ' . $rowheader['DocDate'] . '</td></tr>
                </table>

                <table width="50%" align="left" style="margin-left:200px;">
                <tr><td width="50%">Dari</td><td>: ' . $rowheader['WarehouseFrom'] . '</td></tr> 
                <tr><td width="50%">Ke</td><td>: ' . $warehouse . '</td></tr> 
                </table>

                <table width="40%" align="right">
                <tr><td width="40%">Print Ke </td><td>: ' . $rowprint['totalprint'] . '</td></tr> 
                <tr><td width="20%">Keterangan</td><td>: ' . $rowheader['Remarks'] . '</td></tr>
                </table>

                <br><br>
                <table width="100%" class="item">
                    <thead style="background-color: lightgray;">
                    <tr>
                    <th style="width:1%">No</th>
                            <th style="width:5%">Kode</th>
                            <th align="left" style="width:30%">Nama</th>
                            <th align="left" style="width:5%">Satuan</th>
                            <th align="left" style="width:10%">Kondisi</th>
                             <th align="left" style="width:10%">Alasan</th>
                            <th  align="left" style="width:1%">Warehouse Tujuan</th>
                            <th align="left" style="width:1%">Quantity</th>
                            <th align="left" style="width:1%">Quantity Verifikasi Warehouse</th>
                            <th align="left" style="width:30%">Remarks</th>
                </tr>
                    </thead>
    <tbody>';

        $sqldetail = "SELECT * FROM InventoriAssetDetail where TransID='$id' and StatusApprovalAM=1 and WarehouseTo = '$warehouse'";
        $stmtdetail = sqlsrv_query($conn, $sqldetail);
        if ($stmtdetail === false) {
            die(print_r(sqlsrv_errors(), true));
        }
        $no = 0;
        while ($rowdetail = sqlsrv_fetch_array($stmtdetail, SQLSRV_FETCH_ASSOC)) {

            $no++;
            $html .= ' <tr>
              <td scope="row">' . $no . '</td>
              <td class="text-muted">' . $rowdetail['ItemCode'] . '</td>
              <td align="left">' . htmlspecialchars_decode($rowdetail['ItemName']) . '</td>
              <td align="left">' . $rowdetail['ItemUom'] . '</td>
              <td align="left">' . $rowdetail['ConditionAsset'] . '</td>
              <td align="left">' . $rowdetail['Reason'] . '</td>
              <td align="left">' . $rowdetail['WarehouseTo'] . '</td>
              <td align="left">' . $rowdetail['Quantity'] . '</td>
              <td align="left">' . $rowdetail['QuantityVer'] . '</td>  
              <td align="left">' . $rowdetail['Remarks'] . '</td>
              </td>
            </tr>';
        }
        $html .= ' </tbody>

  </table>

  <br>
      <table width="100%" style="margin-top:-20px;">
    <tr>
    <td align="right">
    <div align="center">
      <br/><br/>
    <p>Diberikan Oleh,</p>
    <br><br><br><br>
    <p class="text-muted">......................................................</p>
    <p>' . $rowheader['WarehouseFrom'] . '</p>
  </div>
  </td>
  <td align="right">
  <div align="center">
    <br/><br/>
  <p>Diterima Oleh,</p>
  <br><br><br><br>
  <p class="text-muted">......................................................</p>
  <p>Helper / Driver WH CK</p>
</div>
</td>
<td align="right">
<div align="center">
  <br/><br/>
<p>Diverifikasi Oleh,</p>
<br><br<><br><br>
<p class="text-muted">.......................................................</p>
<p> '.$warehouse.' </p>
</div>
</td>
     
    </tr>
  </table><br><br>';
        $x++;
    }


    $html .= '</body>
</html>';


} else {
    echo $sqlheader;
}

?>