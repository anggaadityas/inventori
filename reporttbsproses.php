<?php
session_start();
error_reporting(0);
include "db.php";


$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$status_dokumen = $_POST['status_dokumen'];
$store=$_SESSION["nama"];
$area_div=$_SESSION["area_div"];
$divisi=$_SESSION["id_divisi"];
$toko = $_POST['toko'];

if($status_dokumen=='Selesai'){
 $status='and status_progress in (5)';
}
else if($status_dokumen=='Belum Selesai'){
$status='and status_progress not in (5,6)';
}else{
 $status='';
}

if($divisi =='9'){
 $user="and (reqtb_user='$store' OR reqtb_destination='$store')";
}else if($divisi  =='12'){
  if($toko == 999){
      $user="";
  }else{
       $user="and (reqtb_user='$toko' OR reqtb_destination='$toko')";
  }
}else{
  $user='';
}

$startdate=str_replace("-","",$start_date);
$enddate=str_replace("-","",$end_date);

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=report_tbs_".$startdate."_".$enddate.".xls");
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
      <th scope="col">Tanggal Pengiriman</th>
      <th scope="col">Jenis Permintaan</th>
       <th scope="col">Toko Penerima</th>
      <th scope="col">Toko Pengirim</th>
      <th scope="col">Status Dokumen</th>
      <th scope="col">Kode Barang</th>
      <th scope="col">Nama Barang</th>
      <th scope="col">Satuan</th>
      <th scope="col">Jenis Barang</th>
      <th scope="col">Alasan Permintaan</th>
      <th scope="col">Jumlah Permintaan</th>
      <th scope="col">Jumlah Kirim</th>
      <th scope="col">Jumlah Verifikasi Kirim</th>
      <th scope="col">Jumlah Pengembalian</th>
      <th scope="col">Jumlah Verifikasi Pengembalian</th>
      <th scope="col">Jumlah Transfer Putus</th>
      <th scope="col">Selisi</th>
    </tr>

<?php

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$sql ="SELECT
	a.reqtb_code,
	CONVERT ( CHAR ( 10 ), a.reqtb_date, 126 ) req_date,
	b.req_type_name,
	c.req_type_name_item,
	a.reqtb_user,
	a.reqtb_destination,
	a.reqtb_reason,
	tbitem_code,
	tbitem_name,
	tbitem_uom,
	tbitem_cat,
	tbitem_reason,
	ISNULL( tbitem_qty, 0 ) as qty_permintaan,
	ISNULL( tbitem_qty_approve, 0 ) AS qty_kirim,
	ISNULL( tbitem_qty_verifikasi, 0 ) AS qty_verifikasi,
  CASE
    WHEN 	ISNULL( sumretur, 0 ) = 0 THEN ISNULL( sumretur1, 0 )
    ELSE ISNULL( sumretur, 0 )
END as qty_retur,
	ISNULL( sumreturverifikasi, 0 ) as qty_returverifikasi, 
  ISNULL( sumtp, 0 ) as sumtp, 
	(ISNULL( sumreturverifikasi, 0 ) +  ISNULL( sumtp, 0 ))  - ISNULL( tbitem_qty_verifikasi, 0 ) as selisi,
CASE
		
		WHEN status_progress = 1 THEN
		'Menunggu Disetujui ' + a.reqtb_destination 
		WHEN status_progress = 2 THEN
		'Menunggu Pengecekan ' + a.reqtb_user 
		WHEN status_progress = 3 THEN
		'Menunggu Pengembalian / Menunggu Perbaikan Pengembalian ' + a.reqtb_user 
		WHEN status_progress = 4 THEN
		'Menunggu Verifikasi Pengembalian ' + a.reqtb_destination 
		WHEN status_progress = 5 THEN
		'Selesai' 
    WHEN status_progress = 6 THEN
    'Reject'ELSE '' 
	END AS status 
FROM
	header_tb a
	INNER JOIN mst_req_type b ON a.reqtb_type= b.id_mst_type
	LEFT JOIN mst_req_type_item c ON a.reqtb_item_type= c.id_mst_type_item
	INNER JOIN detail_tb d ON a.id_tb= d.header_idtb 
	LEFT JOIN (
       SELECT header_idrtrtb,header_detailid,rtrtbitem_id,sum(rtrtbitem_qty_retur) sumretur,sum(rtrtbitem_qty_retur_verifikasi) as sumreturverifikasi from detail_returntb where (rtrtbflag_tp='' OR rtrtbflag_tp IS NULL) GROUP BY header_idrtrtb,header_detailid,rtrtbitem_id
   ) as e
  ON d.header_idtb=e.header_idrtrtb and d.id_detailtb=e.header_detailid and d.tbitem_id=e.rtrtbitem_id
  LEFT JOIN (
       SELECT header_idrtrtb,header_detailid,rtrtbitem_id,sum(rtrtbitem_qty_retur) sumretur1,sum(rtrtbitem_qty_retur_verifikasi) as sumtp from detail_returntb where rtrtbflag_tp=1 GROUP BY header_idrtrtb,header_detailid,rtrtbitem_id
   ) as f
  ON d.header_idtb=f.header_idrtrtb and d.id_detailtb=f.header_detailid and d.tbitem_id=f.rtrtbitem_id
WHERE  a.reqtb_date between '$start_date' and '$end_date' $status $user
";
$stmtdetail = sqlsrv_query( $conn, $sql );
if( $stmtdetail === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$no=0;
while($row = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){


    echo " <tr>
    <td>".$row['reqtb_code']."</td>
    <td>".$row['req_date']."</td>
    <td>".$row['req_type_name']."</td>
    <td>".$row['reqtb_user']."</td>
    <td>".$row['reqtb_destination']."</td>
    <td>".$row['status']."</td>
    <td>".$row['tbitem_code']."</td>
    <td>".$row['tbitem_name']."</td>
    <td>".$row['tbitem_uom']."</td>
    <td>".$row['tbitem_cat']."</td>
    <td>".$row['tbitem_reason']."</td>
    <td>".$row['qty_permintaan']."</td>
    <td>".$row['qty_kirim']."</td>
    <td>".$row['qty_verifikasi']."</td>
    <td>".$row['qty_retur']."</td>
    <td>".$row['qty_returverifikasi']."</td>
    <td>".$row['sumtp']."</td>
     <td>".$row['selisi']."</td> 
  </tr>";


  

       }


?>

<!-- <?php echo  $sql; ?>  -->

</table>


</body>
</html>

