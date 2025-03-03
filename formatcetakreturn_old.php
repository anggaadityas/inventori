<?php
include "db.php";
$id=$_GET['id'];
$created_by =  $_SESSION['nama'];

/* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  

$sqllog = "INSERT  INTO log_print (
    request_id,
    created_date,
    created_by
    ) VALUES (
     '$id', 
     getdate(), 
     '$created_by'
      )";
$stmt = sqlsrv_query( $conn, $sqllog);

if( $stmt)  
{  
 sqlsrv_commit($conn);  

 $sqlprint = "SELECT count(request_id) as totalprint from log_print where request_id='$id' and created_by='$created_by'";
$stmtprint = sqlsrv_query( $conn, $sqlprint );
if( $stmtprint === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowprint = sqlsrv_fetch_array( $stmtprint, SQLSRV_FETCH_ASSOC);

$sqlheader = "SELECT *,convert(char(10),reqrtn_date,103) req_date FROM header_returnck a
 inner join mst_req_type b on a.reqrtn_type=b.id_mst_type
 inner join mst_req_type_item  c on a.reqrtn_type_item=c.id_mst_type_item
 where id_rtn='$id'";
$stmtheader = sqlsrv_query( $conn, $sqlheader );
if( $stmtheader === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowheader = sqlsrv_fetch_array( $stmtheader, SQLSRV_FETCH_ASSOC);

$html='<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Form Retur Barang</title>

<style type="text/css">
      * {
        font-family: Verdana, Arial, sans-serif;
    }
    table{
        font-size: x-small;
         border-collapse: collapse;
    }
    .item tr td{
        font-weight: bold;
        font-size: x-small;
         border: 1px solid #cecfd5;
    }
    thead tr th{
        font-weight: bold;
        font-size: x-small;
         border: 1px solid #cecfd5;
    }
    tfoot tr td{
        font-weight: bold;
        font-size: x-small;
        border: 1px solid #cecfd5;
    }
    .gray {
        background-color: lightgray
    }
    ul {list-style-type:none;}
li {list-style-type:none;}
</style>

</head>
<body>


<table width="100%">
<tr>
<td width="16%"  align="top"><img style="margin-top:-5px;" src="img/Yoshinoya-template.png" alt="" width="200" height="50"/></td>
<td width="100%"  align="left" style="margin-left:40px;">
<div style="margin-top:-5px;margin-left:40px;">
<p style="font-size:24px;"><b>FORM RETUR BARANG</b></p>
<p style="margin-top:-20px;font-size:16px;"><b>'.$rowheader['reqrtn_code'].' #'.$rowprint['totalprint'].' </b></p>
</div>
</td>
</tr>
</table>

  <table width="50%" style="margin-top:20px;" align="left">
        <tr><td width="40%" style="margin-bottom:10px;">Tanggal Permintaan</td><td>: '.$rowheader['req_date'].'</td></tr>
        <tr><td width="40%">Jenis Permintaan</td><td>: '.$rowheader['req_type_name'].'</td></tr>
        <tr><td width="40%">Jenis Barang</td><td>: '.$rowheader['req_type_name_item'].'</td></tr>
  </table>

  <table width="50%" style="margin-top:20px;" align="right">
  <tr><td width="40%">Dari Store</td><td>: '.$rowheader['reqrtn_user'].'</td></tr>
  <tr><td width="40%">Kepada Dept</td><td>: '.$rowheader['reqrtn_destination'].'</td></tr>
  <tr><td width="40%">Alasan</td><td>: '.$rowheader['reqrtn_reason'].'</td></tr>
  <tr><td width="40%">Keterangan</td><td>: '.$rowheader['reqrtn_note'].'</td></tr>
</table>

<br>
  <table width="100%" class="item"  style="margin-top:90px;">
    <thead style="background-color: lightgray;">
      <tr>
      <th style="width:2%">No</th>
      <th style="width:13%">Kode</th>
      <th align="left" style="width:15%">Nama</th>
      <th align="left" style="width:10%">Satuan</th>
      <th align="left" style="width:5%">Qty Toko</th>
     <th align="left"  style="width:8%">Kondisi Toko</th>
     <th align="left" style="width:5%">Kadaluarsa</th>
      <th align="left" style="width:5%">Qty VerCK</th>   
      <th align="left" style="width:8%">Kondisi  VerCK</th>
      <th align="left">Remarks</th>
      </tr>
    </thead>
    <tbody>';


    $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,103) expired_date FROM detail_returnck where header_idrtn='$id'";
    $stmtdetail = sqlsrv_query( $conn, $sqldetail );
    if( $stmtdetail === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    $no=0;
    while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){

    $no++;
    $html .=' <tr>
              <td scope="row">'.$no.'</td>
              <td class="text-muted">'.$rowdetail['rtnitem_code'].'</td>
              <td align="left">'.$rowdetail['rtnitem_name'].'</td>
              <td align="left">'.$rowdetail['rtnitem_uom'].'</td>
              <td align="left">'.$rowdetail['rtnitem_qty'].'</td>    
              <td align="left">'.$rowdetail['rtnitem_item_condition'].'</td>
              <td align="left">'.$rowdetail['expired_date'].'</td>
              <td align="left">'.$rowdetail['rtnitem_qty_verifikasi'].'</td> 
              <td align="left">'.$rowdetail['rtnitem_item_condition_verifikasi'].'</td>
              <td align="left">'.$rowdetail['rtnitem_remarks_verifikasi'].'</td>
              </td>
            </tr>';

    }

   $html .=' </tbody>

  </table>



      <table width="100%">
    <tr>
    <td align="right">
    <div align="center">
      <br/><br/>
    <p>Diberikan Oleh,</p>
    <br><br><br><br>
    <p class="text-muted">......................................................</p>
    <p>'.$rowheader['reqrtn_user'].'</p>
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
<p> Crew Retur / Leader WH CK</p>
</div>
</td>
     
    </tr>
  </table>


</body>
</html>';

}else{
  echo $sqlheader;
 }

// <table width="100%">

//     <td align="right">
//       <h2>TANDA TERIMA</h2>
//       <p style="margin-top:-12px;">'.$rowheader['reqrtn_code'].'</p>
// </td>
// </tr>

// </table>

// <td width="50%"  align="left">
// <ul>
// <strong>
// <li style="font-size:15px;"> PT. Multirasa Nusantara</li>
// <li style="font-size:12px;margin-bottom:10px;"> Grha Bank Mas Lantai 3A</li>
// </strong>
// <li>Jalan Setia Budi Selatan Kav.7-8, Setiabudi, Karet Kuningan, Jakarta Selatan</li>
// <li> Daerah Khusus Ibukota Jakarta 12920</li>
// </ul>
// </td>

?>