<?php
session_start();
error_reporting(0);
include "db.php";


$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$status_dokumen = $_POST['status_dokumen'];
$jenis_report = $_POST['jenis_report'];
$store=$_SESSION["nama"]; 
$area_div=$_SESSION["area_div"];
$divisi=$_SESSION["id_divisi"];
$toko = $_POST['toko'];

if($status_dokumen=='Selesai'){
 $status='and status_progress in (4)';
}
else if($status_dokumen=='Belum Selesai'){
$status='and status_progress not in (4,5)';
}else{
 $status='';
}



if($divisi =='9'){
  if($jenis_report == ''){
     $user="and (reqtp_user='$store' OR reqtp_destination='$store')";
  }else if($jenis_report == '1'){
      $user="and reqtp_user='$store'";
  }else if($jenis_report == '2'){
      $user="and reqtp_destination='$store'";
  }else{
     $user="and reqtp_user=''";
  }
}else if($divisi  =='19'){
 $user="and reqtp_ck_destination='$area_div'";
}else if($divisi  =='12'){
  if($toko == 999){
      $user="";
  }else{
       $user="and (reqtp_user='$toko' OR reqtp_destination='$toko')";
  }
}else{
  $user='';
 }

$startdate=str_replace("-","",$start_date);
$enddate=str_replace("-","",$end_date);

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=report_tps".$voucher_type."_".$startdate."_".$enddate.".xls");
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

<table>
    <tr>
    <th scope="col">No Dokumen</th>
      <th scope="col">No Dokumen SAP</th>
      <th scope="col">Tanggal Pengiriman</th>
      <th scope="col">Tanggal Posting SAP</th>
      <th scope="col">Tanggal Transaksi Portal</th>
      <th scope="col">Tanggal Verifikasi Store Penerima</th>
      <th scope="col">Jenis Permintaan</th>
       <th scope="col">Toko Penerima</th>
      <th scope="col">Toko Pengirim</th>
      <th scope="col">CK Area</th>
      <th scope="col">Status Dokumen</th>
      <th scope="col">Keterangan Permintaan</th>
      <th scope="col">Kode Barang</th>
      <th scope="col">Nama Barang</th>
      <th scope="col">Satuan</th>
      <th scope="col">Jenis Barang</th>
      <th scope="col">Alasan Permintaan</th>
      <th scope="col">Kadarluarsa Barang</th>
      <th scope="col">Jumlah Permintaan</th>
      <th scope="col">Jumlah Kirim</th>
      <th scope="col">Remarks Kirim</th>
      <th scope="col">Jumlah Verifikasi</th>
      <th scope="col">Remarks Verifikasi</th>
    </tr>

<?php

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$sql ="SELECT 
a.reqtp_code,
a.reqtp_nodoc_sap,
convert(char(10),a.reqtp_nodoc_sap_posting_date,126) date_posting_sap,
convert(char(10),a.reqtp_nodoc_sap_date,126) date_transaksi_sap,
convert(char(10),a.reqtp_date,126) req_date,
CONVERT(char(16), a.reqtp_user_verifikasi_date, 126) date_user_verifikasi,
b.req_type_name,
c.req_type_name_item,
 a.reqtp_user,
 a.reqtp_destination,
 a.reqtp_ck_destination,
a.reqtp_reason,
a.reqtp_note,
tpitem_code,
tpitem_name,
tpitem_uom,
tpitem_cat,
tpitem_reason,
tpitem_qty,
tpitem_qty_approve as qty_kirim,
tpitem_remarks_approve,
convert(char(10),tpitem_expired,126) as expired,
(tpitem_qty_verifikasi_good + tpitem_qty_verifikasi_not_good) as qty_verifikasi,
tpitem_qty_verifikasi_good,
tpitem_qty_verifikasi_not_good,
 tpitem_remarks_verifikasi,
 a.reqtp_destination_approve,
 a.reqtp_user_verifikasi,
CASE
WHEN status_progress=1 THEN 'Menunggu Disetujui '+ a.reqtp_destination
WHEN status_progress=2 THEN 'Menunggu Pengecekan '+ a.reqtp_user
WHEN status_progress=3 THEN 'Menunggu Transfer SAP '+ a.reqtp_ck_destination
WHEN status_progress=4 THEN 'Selesai'
WHEN status_progress=5 THEN 'Reject'

ELSE ''
END as status
FROM header_tp a inner join mst_req_type b on a.reqtp_type=b.id_mst_type
left join mst_req_type_item c on a.reqtp_item_type=c.id_mst_type_item
inner join detail_tp d on a.id_tp=d.header_idtp
where a.reqtp_user !='' AND a.reqtp_destination !='' and a.reqtp_date between '$start_date' and '$end_date' $status $user
";
$stmtdetail = sqlsrv_query( $conn, $sql );
if( $stmtdetail === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$no=0;
while($row = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){


    echo " <tr>
    <td>".$row['reqtp_code']."</td>
    <td>".$row['reqtp_nodoc_sap']."</td>
    <td>".$row['req_date']."</td>
    <td>".$row['date_posting_sap']."</td> 
    <td>".$row['date_transaksi_sap']."</td>
    <td>".$row['date_user_verifikasi']."</td>
    <td>".$row['req_type_name']."</td>
    <td>".$row['reqtp_user']."</td>
    <td>".$row['reqtp_destination']."</td>
    <td>".$row['reqtp_ck_destination']."</td>
    <td>".$row['status']."</td>
    <td>".$row['reqtp_note']."</td>
    <td>".$row['tpitem_code']."</td>
    <td>".$row['tpitem_name']."</td>
    <td>".$row['tpitem_uom']."</td>
    <td>".$row['tpitem_cat']."</td>
    <td>".$row['tpitem_reason']."</td>
    <td>".$row['expired']."</td>
    <td>".$row['tpitem_qty']."</td>
    <td>".number_format($row['qty_kirim'],2)."</td>
    <td>".$row['tpitem_remarks_approve']."</td>
    <td>".number_format($row['qty_verifikasi'],2)."</td>
    <td>".$row['tpitem_remarks_verifikasi']."</td>
  </tr>";


  

       }


?>

 <!-- <?php echo  $sql; ?>  -->

</table>


</body>
</html>

