<?php
include "db.php";
error_reporting(0);
$id=$_GET['id'];
$flag=$_GET['flag'];
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
    flagretur,
    created_date,
    created_by
    ) VALUES (
     '$id', 
     '$flag', 
     getdate(), 
     '$created_by'
      )";
$stmt = sqlsrv_query( $conn, $sqllog);

if( $stmt)  
{  
 sqlsrv_commit($conn);  

  
$sqlprint = "SELECT count(request_id) as totalprint from log_print where request_id='$id' and flagretur='$flag' and created_by='$created_by'";
$stmtprint = sqlsrv_query( $conn, $sqlprint );
if( $stmtprint === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowprint = sqlsrv_fetch_array( $stmtprint, SQLSRV_FETCH_ASSOC);


$sqlheader = "SELECT *,convert(char(10),reqtb_user_retur_date,103) req_date_retur,convert(char(10),reqtb_destination_retur_verifikasi_date,103) date_retur_verifikasi FROM header_tb a
 left join mst_req_type b on a.reqtb_type=b.id_mst_type
 left join mst_req_type_item  c on a.reqtb_item_type=c.id_mst_type_item
 where id_tb='$id'";
$stmtheader = sqlsrv_query( $conn, $sqlheader );
if( $stmtheader === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowheader = sqlsrv_fetch_array( $stmtheader, SQLSRV_FETCH_ASSOC);

$sqldateretur = "SELECT convert(char(10),log_returdate,103) dateretur,note_tb FROM log_tb where log_idtb='$id' and log_flagretur='$flag' and note_tb like 'RETUR REQUEST%'";
$stmtdateretur= sqlsrv_query( $conn, $sqldateretur );
if( $stmtdateretur === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowdateretur = sqlsrv_fetch_array( $stmtdateretur, SQLSRV_FETCH_ASSOC);

$noteretur = explode("-",$rowdateretur['note_tb']);
$fixnoteretur = $noteretur[1];


$sqldatereturver = "SELECT convert(char(10),created_date,103) datereturver FROM log_tb where log_idtb='$id' and log_flagretur='$flag' and note_tb like 'VERIFIKASI RETUR REQUEST%'";
$stmtdatereturver= sqlsrv_query( $conn, $sqldatereturver );
if( $stmtdatereturver === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowdatereturver = sqlsrv_fetch_array( $stmtdatereturver, SQLSRV_FETCH_ASSOC);

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

$x = 1;
while($x <= 2) {

  $html .='<table width="100%">
    <tr>
    <td width="16%"  align="top"></td>
    <td width="100%"  align="left" style="margin-left:40px;">
    <div style="margin-top:-30px;margin-left:170px;">
    <p style="font-size:12px;"><b>FORM TRANSFER BALIK STORE</b></p>
   <p style="margin-top:-15px;font-size:12px;"><b>'.$rowheader['reqtb_code'].' </b></p>
   </div>
    </td>
    </tr>
  </table>

  <table width="30%" align="left" style="margin-top:10px;">
        <tr><td width="40%" style="margin-bottom:10px;">Tanggal Pengembalian</td><td>: '.$rowdateretur['dateretur'].'</td></tr>
        <tr><td width="40%" style="margin-bottom:10px;">Tanggal Verifikasi</td><td>: '.$rowdatereturver['datereturver'].'</td></tr>
       <tr><td width="40%">Print Ke </td><td>: '.$rowprint['totalprint'].'</td></tr>    
 </table>

 <table width="70%" align="right">
 <tr><td width="50px;">Dari Store</td><td>: '.$rowheader['reqtb_user'].'</td></tr>  
 <tr><td width="20%">Kepada Store</td><td>: '.$rowheader['reqtb_destination'].'</td></tr>  
        <tr><td width="20%">Keterangan</td><td>: '.$fixnoteretur.'</td></tr> 
  </table>

  <table width="40%" align="right">
  <tr><td width="60px;">Pengembalian Ke</td><td>: '.$flag.'</td></tr>  
   </table>

<br><br>';

  $html .='<table width="100%" class="item"  style="margin-top:20px;">
    <thead style="background-color: lightgray;">
      <tr>
        <th style="width:3%">No</th>
                <th style="width:5%">Kode</th>
                <th align="left" style="width:32%">Nama</th>
                <th align="left" style="width:5%">Satuan</th>
                <th align="left" style="width:10%">Jenis Barang</th>
                <th align="left" style="width:10%">Alasan</th>
                <th align="left" style="width:1%">Qty Pengembalian</th>
                <th align="left" style="width:5%">Kadaluarsa</th>
                <th align="left" style="width:1%">Qty Verifikasi Pengembalian</th>
                <th align="left" style="width:30%">Remarks</th>
      </tr>
    </thead>
    <tbody>';


    $sqldetail = "SELECT
    *, CONVERT ( CHAR ( 10 ), a.rtrtbitem_expired_retur, 126 ) expiredpeminjaman 
FROM
    detail_returntb a inner join detail_tb b on a.header_detailid=b.id_detailtb
WHERE
    header_idrtrtb = '$id'  and flag='$flag' and (rtrtbflag_tp is null OR rtrtbflag_tp ='')
ORDER BY
    flag ASC";
    $stmtdetail = sqlsrv_query( $conn, $sqldetail );
    if( $stmtdetail === false) {
        die( print_r( sqlsrv_errors(), true) );
    }
    $no=0;
    while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){
      $fixqtyver =0;
      $fixqtyver = $rowdetail['rtrtbitem_qty_retur_verifikasi'];
      if($fixqtyver == 0){
          $fixqtyverf ="";
      }else{
        $fixqtyverf =$fixqtyver;
      }
    $no++;
    $html .=' <tr>
              <td scope="row">'.$no.'</td>
              <td class="text-muted">'.$rowdetail['tbitem_code'].'</td>
              <td align="left">'.htmlspecialchars_decode($rowdetail['tbitem_name']).'</td>
              <td align="left">'.$rowdetail['tbitem_uom'].'</td>
              <td align="left">'.$rowdetail['tbitem_cat'].'</td>
              <td align="left">'.$rowdetail['tbitem_reason'].'</td>
              <td align="left">'.number_format($rowdetail['rtrtbitem_qty_retur'],2,'.',',').'</td>    
              <td align="left">'.$rowdetail['expiredpeminjaman'].'</td>        
              <td align="left">'.number_format($fixqtyverf,2,'.',',').'</td>   
              <td align="left">'.$rowdetail['rtrtbitem_remarks_retur_verifikasi'].'</td>
              </td>
            </tr>';

    }

   $html .=' </tbody>

  </table>



 <table width="100%" style="margin-top:5px;">
    <tr>
    <td align="right">
    <div align="center">
      <br/>
    <p>Diberikan Oleh,</p>
    <br>    <br/>    <br/>
    <p class="text-muted">......................................................</p>
    <p>'.$rowheader['reqtb_user'].'</p>
  </div>
  </td>
  <td align="right">
  <div align="center">
    <br/>
  <p>Diterima Oleh,</p>
  <br>    <br/>    <br/>
  <p class="text-muted">......................................................</p>
  <p>'.$rowheader['reqtb_destination'].'</p>
</div>
</td>
     
    </tr>
  </table><br><br>';

  $x++;
} 


$html .='</body>
</html>';

}else{
 echo $sqlheader;
}

// Diberikan <p>'.$rowheader['reqtb_destination'].'</p>
//Diterima <p>'.$rowheader['reqtb_user'].'</p>
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
//       <p style="margin-top:-12px;">'.$rowheader['reqtb_code'].'</p>
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

