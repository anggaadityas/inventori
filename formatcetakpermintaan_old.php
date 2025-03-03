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


$sqlheader = "SELECT *,convert(char(10),reqtp_date,103) req_date FROM header_tp a
 left join mst_req_type b on a.reqtp_type=b.id_mst_type
 left join mst_req_type_item  c on a.reqtp_item_type=c.id_mst_type_item
 where id_tp='$id'";
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
    <p style="font-size:24px;"><b>FORM TRANSFER PUTUS STORE</b></p>
   <p style="margin-top:-20px;font-size:16px;"><b>'.$rowheader['reqtp_code'].' #'.$rowprint['totalprint'].' </b></p>
   </div>
    </td>
    </tr>
  </table>

  <table width="50%" align="left"  style="margin-top:20px;">
        <tr><td width="40%" style="margin-bottom:10px;">Tanggal Permintaan</td><td>: '.$rowheader['req_date'].'</td></tr>
        <tr><td width="40%">Jenis Permintaan</td><td>: '.$rowheader['req_type_name'].'</td></tr>
        <tr><td width="40%">Kepada Store</td><td>: '.$rowheader['reqtp_destination'].'</td></tr>    
        <tr><td width="40%">Dari Store</td><td>: '.$rowheader['reqtp_user'].'</td></tr>
 </table>

 <table width="50%" align="right"  style="margin-top:20px;">
        <tr><td width="40%">Keterangan</td><td>: '.$rowheader['reqtp_note'].'</td></tr> 
  </table>

<br>


  <table width="100%" class="item"  style="margin-top:90px;">
    <thead style="background-color: lightgray;">
      <tr>
        <th style="width:3%">Nomor</th>
                <th style="width:13%">Kode</th>
                <th align="left" style="width:20%">Nama</th>
                <th align="left" style="width:10%">Satuan</th>
                <th align="left" style="width:10%">Jenis Barang</th>
                <th align="left" style="width:10%">Alasan</th>
                <th align="left" style="width:5%">Qty Kirim</th>
                <th align="left" style="width:5%">Kadaluarsa</th>
                <th align="left" style="width:5%">Qty Terima</th>
                <th align="left">Remarks</th>
      </tr>
    </thead>
    <tbody>';


    $sqldetail = "SELECT *,convert(char(10),tpitem_expired,103) expired FROM detail_tp where header_idtp='$id'";
    $stmtdetail = sqlsrv_query( $conn, $sqldetail );
    if( $stmtdetail === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    $no=0;
    while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){
      $fixqtyver =0;
      $fixqtyver = $rowdetail['tpitem_qty_verifikasi_good'] + $rowdetail['tpitem_qty_verifikasi_not_good'];
    $no++;
    $html .=' <tr>
              <td scope="row">'.$no.'</td>
              <td class="text-muted">'.$rowdetail['tpitem_code'].'</td>
              <td align="left">'.$rowdetail['tpitem_name'].'</td>
              <td align="left">'.$rowdetail['tpitem_uom'].'</td>
              <td align="left">'.$rowdetail['tpitem_cat'].'</td>
              <td align="left">'.$rowdetail['tpitem_reason'].'</td>
              <td align="left">'.$rowdetail['tpitem_qty_approve'].'</td>    
              <td align="left">'.$rowdetail['expired'].'</td>        
              <td align="left">'.$fixqtyver.'</td>   
              <td align="left">'.$rowdetail['tpitem_remarks_verifikasi'].'</td>
              </td>
            </tr>';

    }

   $html .=' </tbody>

  </table>



 <table width="100%" style="margin-top:5px;">
    <tr>
    <td align="right">
    <div align="center">
      <br/><br/><br/>
    <p>Diberikan Oleh,</p>
    <br><br><br><br>
    <p class="text-muted">......................................................</p>
    <p>'.$rowheader['reqtp_destination'].'</p>
  </div>
  </td>
  <td align="right">
  <div align="center">
    <br/><br/><br/>
  <p>Diterima Oleh,</p>
  <br><br><br><br>
  <p class="text-muted">......................................................</p>
  <p>'.$rowheader['reqtp_user'].'</p>
</div>
</td>
     
    </tr>
  </table>


</body>
</html>';

}else{
 echo $sqlheader;
}

// Diberikan <p>'.$rowheader['reqtp_destination'].'</p>
//Diterima <p>'.$rowheader['reqtp_user'].'</p>
// <td align="right">
// <div align="center">
//   <br/><br/><br/>
// <p>Diverifikasi Oleh,</p>
// <br><br<><br><br><br>
// <p class="text-muted">________________________</p>
// </div>
// </td>

// <table width="100%">
// <tr>
//     <td align="right">
//       <h2>FORM TRANSFER PUTUS STORE</h2>
//       <p style="margin-top:-12px;">'.$rowheader['reqtp_code'].'</p>
// <ul>
// <strong>
// <li style="font-size:15px;"> PT. Multirasa Nusantara</li>
// <li style="font-size:12px;margin-bottom:10px;"> Grha Bank Mas Lantai 3A</li>
// </strong>
// <li>Jalan Setia Budi Selatan Kav.7-8, Setiabudi, Karet Kuningan, Jakarta Selatan</li>
// <li> Daerah Khusus Ibukota Jakarta 12920</li>
// </ul>
// </td>
// </tr>

// </table> 

?>

