<?php
include "db.php";
error_reporting(0);
$id=$_GET['id'];
$cat=$_GET['cat'];
$size=$_GET['size'];
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
    note,
    created_date,
    created_by
    ) VALUES (
     '$id', 
     '$cat',
     getdate(), 
     '$created_by'
      )";
$stmt = sqlsrv_query( $conn, $sqllog);


if( $stmt)  
{  
 sqlsrv_commit($conn);  

 $sqlprint = "SELECT count(request_id) as totalprint from log_print where request_id='$id' and note like '$cat%' and created_by='$created_by'";
$stmtprint = sqlsrv_query( $conn, $sqlprint );
if( $stmtprint === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowprint = sqlsrv_fetch_array( $stmtprint, SQLSRV_FETCH_ASSOC);

$sqlheader = "SELECT *,convert(char(10),reqrtn_date,103) req_date,convert(char(10),reqrtn_destination_approve_date,103) date_verifikasi FROM header_returnck a
 left join mst_req_type b on a.reqrtn_type=b.id_mst_type
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

if($rowheader['reqrtn_destination'] =='ENG JAKARTA' || $rowheader['reqrtn_destination'] == 'ENG SURABAYA'){

$x = 1;
while($x <= 2) {


$html .='<table width="100%">
<tr>
<td width="16%"  align="top"></td>
<td width="100%"  align="left" style="margin-left:40px;">
<div style="margin-top:-30px;margin-left:180px;">
<p style="font-size:12px;"><b>FORM RETUR BARANG</b></p>
<p style="margin-top:-15px;font-size:12px;"><b>'.$rowheader['reqrtn_code'].' </b></p>
</div>
</td>
</tr>
</table>

<table width="30%" align="left">
<tr><td width="40%" style="margin-bottom:10px;">Tanggal Permintaan</td><td>: '.$rowheader['req_date'].'</td></tr>
<tr><td width="40%" style="margin-bottom:10px;">Tanggal Verifikasi</td><td>: '.$rowheader['date_verifikasi'].'</td></tr>
  </table>

  <table width="50%" align="left" style="margin-left:200px;">
  <tr><td width="50%">Dari Store</td><td>: '.$rowheader['reqrtn_user'].'</td></tr> 
  <tr><td width="50%;">Kepada Dept</td><td>: '.$rowheader['reqrtn_destination'].'</td></tr>

</table>

<table width="40%" align="right">
<tr><td width="40%">Print Ke </td><td>: '.$rowprint['totalprint'].'</td></tr> 
<tr><td width="20%">Keterangan</td><td>: '.$rowheader['reqrtn_note'].'</td></tr>
</table>

<br><br>
  <table width="100%" class="item">
    <thead style="background-color: lightgray;">
    <tr>
    <th style="width:1%">No</th>
            <th style="width:5%">Kode</th>
            <th align="left" style="width:30%">Nama</th>
            <th align="left" style="width:5%">Satuan</th>
            <th align="left" style="width:10%">Jenis Barang</th>
            <th align="left" style="width:10%">Alasan</th>
            <th align="left" style="width:1%">Qty Toko</th>
            <th align="left" style="width:5%">Kadaluarsa</th>
            <th align="left" style="width:1%">Qty Ver</th>
            <th align="left" style="width:30%">Remarks</th>
  </tr>
    </thead>
    <tbody>';


    $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,103) expired_date FROM detail_returnck where header_idrtn='$id' and rtnitem_cat like '$cat%'";
    $stmtdetail = sqlsrv_query( $conn, $sqldetail );
    if( $stmtdetail === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    $no=0;
    while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){
        $totalqtytoko = $rowdetail['rtnitem_qty_good'] + $rowdetail['rtnitem_qty_not_good'];
        $totalqtyver = $rowdetail['rtnitem_qty_verifikasi_good'] + $rowdetail['rtnitem_qty_verifikasi_not_good'];
    $no++;
    $html .=' <tr>
              <td scope="row">'.$no.'</td>
              <td class="text-muted">'.$rowdetail['rtnitem_code'].'</td>
              <td align="left">'.$rowdetail['rtnitem_name'].'</td>
              <td align="left">'.$rowdetail['rtnitem_uom'].'</td>
              <td align="left">'.$rowdetail['rtnitem_cat'].'</td>
              <td align="left">'.$rowdetail['rtnitem_reason'].'</td>
              <td align="left">'.number_format($totalqtytoko,2,'.',',').'</td>    
              <td align="left">'.$rowdetail['expired_date'].'</td>
              <td align="left">'.number_format($totalqtyver,2,'.',',').'</td>
              <td align="left">'.$rowdetail['rtnitem_remarks_verifikasi'].'</td>
              </td>
            </tr>';

    }
   $html .=' </tbody>

  </table>



      <table width="100%" style="margin-top:-20px;">
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
  </table><br><br>';
  $x++;
}

}else{

$x = 1;
while($x <= 2) {


$html .='<table width="100%">
<tr>
<td width="16%"  align="top"></td>
<td width="100%"  align="left" style="margin-left:40px;">
<div style="margin-top:-30px;margin-left:180px;">
<p style="font-size:12px;"><b>FORM RETUR BARANG</b></p>
<p style="margin-top:-15px;font-size:12px;"><b>'.$rowheader['reqrtn_code'].' </b></p>
</div>
</td>
</tr>
</table>

<table width="30%" align="left">
<tr><td width="40%" style="margin-bottom:10px;">Tanggal Permintaan</td><td>: '.$rowheader['req_date'].'</td></tr>
<tr><td width="40%" style="margin-bottom:10px;">Tanggal Verifikasi</td><td>: '.$rowheader['date_verifikasi'].'</td></tr>

<tr><td width="40%" style="margin-bottom:10px;">Doc Num (Ireap)</td><td>: '.$rowheader['docnum_ireap'].'</td></tr>
  </table>

  <table width="50%" align="left" style="margin-left:200px;">
  <tr><td width="50%">Dari Store</td><td>: '.$rowheader['reqrtn_user'].'</td></tr> 
  <tr><td width="50%;">Kepada Dept</td><td>: '.$rowheader['reqrtn_destination'].'</td></tr>

</table>

<table width="40%" align="right">
<tr><td width="40%">Print Ke </td><td>: '.$rowprint['totalprint'].'</td></tr> 
<tr><td width="20%">Keterangan</td><td>: '.$rowheader['reqrtn_note'].'</td></tr>
</table>

<br><br>
  <table width="100%" class="item">
    <thead style="background-color: lightgray;">
    <tr>
    <th style="width:1%">No</th>
            <th style="width:5%">Kode</th>
            <th align="left" style="width:30%">Nama</th>
            <th align="left" style="width:5%">Satuan</th>
            <th align="left" style="width:10%">Jenis Barang</th>
            <th align="left" style="width:10%">Alasan</th>
            <th align="left" style="width:1%">Qty Kirim Bagus Toko</th>
            <th align="left" style="width:1%">Qty Kirim Tidak Bagus Toko</th>
            <th align="left" style="width:5%">Kadaluarsa</th>
            <th align="left" style="width:5%">Tanggal Terima Barang Dari CK</th>
            <th align="left" style="width:1%">Qty VerCK Bagus</th>
            <th align="left" style="width:1%">Qty VerCK Tidak Bagus</th>
            <th align="left" style="width:30%">Remarks</th>
  </tr>
    </thead>
    <tbody>';


    $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,103) expired_date,convert(char(10),rtnitem_arrival,103) arrival_date FROM detail_returnck where header_idrtn='$id' and rtnitem_cat like '$cat%'";
    $stmtdetail = sqlsrv_query( $conn, $sqldetail );
    if( $stmtdetail === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    $no=0;
    while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){
        $totalqtytoko = $rowdetail['rtnitem_qty_good'] + $rowdetail['rtnitem_qty_not_good'];
        if($rowdetail['rtnitem_qty_verifikasi_good'] ==''){
          $fixverfikasigood='';
        }else{
          $fixverfikasigood=number_format($rowdetail['rtnitem_qty_verifikasi_good'],2,'.',',');
        }
         if($rowdetail['rtnitem_qty_verifikasi_not_good'] ==''){
          $fixverfikasinotgood='';
        }else{
          $fixverfikasinotgood=number_format($rowdetail['rtnitem_qty_verifikasi_not_good'],2,'.',',');
        }
    $no++;
    $html .=' <tr>
              <td scope="row">'.$no.'</td>
              <td class="text-muted">'.$rowdetail['rtnitem_code'].'</td>
              <td align="left">'.htmlspecialchars_decode($rowdetail['rtnitem_name']).'</td>
              <td align="left">'.$rowdetail['rtnitem_uom'].'</td>
              <td align="left">'.$rowdetail['rtnitem_cat'].'</td>
              <td align="left">'.$rowdetail['rtnitem_reason'].'</td>
              <td align="left">'.number_format($rowdetail['rtnitem_qty_good'],2,'.',',').'</td>
              <td align="left">'.number_format($rowdetail['rtnitem_qty_not_good'],2,'.',',').'</td>  
              <td align="left">'.$rowdetail['expired_date'].'</td>
              <td align="left">'.$rowdetail['arrival_date'].'</td>
              <td align="left">'.$fixverfikasigood.'</td> 
              <td align="left">'.$fixverfikasinotgood.'</td>
              <td align="left">'.$rowdetail['rtnitem_remarks_verifikasi'].'</td>
              </td>
            </tr>';

    }
   $html .=' </tbody>

  </table>



      <table width="100%" style="margin-top:-20px;">
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
  </table><br><br>';
  $x++;
}


} 


$html .='</body>
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