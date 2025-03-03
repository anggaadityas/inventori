<?php
session_start();
error_reporting(0);
include "db.php";


$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$status_dokumen = $_POST['status_dokumen'];
$kategori_retur = $_POST['kategori_retur'];
$store=$_SESSION["nama"];
$area_div=$_SESSION["area_div"];
$role_id=$_SESSION["role_id"];
$divisi=$_SESSION["id_divisi"];
$toko = $_POST['toko'];


// if($status_dokumen=='Selesai'){
//  $status='in (3)';
// }else if($status_dokumen=='Belum Selesai'){
// $status='not in (3)';
// }else{
//  $status='';
// }

if($status_dokumen=='Selesai'){

    $status="where statusfixck ='Selesai'";
}
else if($status_dokumen=='Belum Selesai'){

  $status="where statusfixck not in ('Selesai','Reject')";

}else if($status_dokumen=='Reject'){

  $status="where statusfixck in ('Reject')";

}else{
  $status='';
}


if($kategori_retur !=='999'){

    $kategori_retur="and jenisform='$kategori_retur'";
}else{
  $kategori_retur='';
}

if($divisi =='9'){
  // $user = $store;
 $user="and reqrtn_user='$store'";
}else if($divisi =='12' ||  $divisi =='11' ){
   if($toko == 999){
      $user="";
  }else{
       $user="and reqrtn_user='$toko'";
  }
}else{
 $user="and reqrtn_destination='$area_div'";
 }

$startdate=str_replace("-","",$start_date);
$enddate=str_replace("-","",$end_date);

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=report_rtn".$voucher_type."_".$startdate."_".$enddate.".xls");
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

<?php

if($area_div=='ENG JAKARTA' OR $area_div =='ENG SURABAYA'){

?>

<table>
    <tr>
    <th scope="col">No Dokumen</th>
    <th scope="col">No Dokumen SJT</th>
      <th scope="col">Tanggal Permintaan</th>
      <th scope="col">Tanggal Pengambilan</th>
      <th scope="col">Tanggal Kedatangan</th>
      <th scope="col">Tanggal Transaksi Portal</th>
      <th scope="col">Jenis Permintaan</th>
      <th scope="col">Toko Asal</th>
      <th scope="col">Divisi Tujuan</th>
      <th scope="col">CK Area</th>
      <th scope="col">Status Dokumen</th>
      <th scope="col">Kode Barang</th>
      <th scope="col">Nama Barang</th>
      <th scope="col">Satuan</th>
      <th scope="col">Jenis Barang</th>
      <th scope="col">Alasan Permintaan</th>
      <th scope="col">Kedatangan Barang Di Store</th>
      <th scope="col" class="text-center" style="text-align: center;">Toko Kirim</th>
      <th scope="col" class="text-center" style="text-align: center;">Verifikasi</th>
      <th scope="col" class="text-center" style="text-align: center;">Balance</th>
      <th scope="col">Remarks Verifikasi</th>
    </tr>



<?php

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$sql ="SELECT * FROM ( SELECT *, CASE
WHEN ck = 1 THEN statusck
ELSE statusotherck
END as statusfixck FROM (SELECT
  a.reqrtn_code,
  a.reqrtn_nodoc_sap,
  CONVERT ( CHAR ( 10 ), a.reqrtn_nodoc_sap_posting_date, 126 ) date_posting_sap,
  CONVERT ( CHAR ( 10 ), a.reqrtn_nodoc_sap_date, 126 ) date_transaksi_sap,
  CONVERT ( CHAR ( 10 ), a.reqrtn_date, 126 ) date_delivery,
  CONVERT ( CHAR ( 10 ), a.created_date, 126 ) date_req,
  CONVERT ( CHAR ( 10 ), a.reqrtn_destination_arrival_goods_date, 126 ) date_arrival,
  b.req_type_name,
  c.req_type_name_item,
  a.reqrtn_user,
  a.reqrtn_destination,
  a.reqrtn_ck,
  a.reqrtn_reason,
  a.reqrtn_nopica,
  rtnitem_code,
  rtnitem_name,
  rtnitem_uom,
  rtnitem_cat,
  rtnitem_reason,
  rtnitem_remarks,
  rtnitem_qty_good AS qty_good,
  rtnitem_qty_not_good AS qty_notgood,
  CONVERT ( CHAR ( 10 ), rtnitem_expired, 126 ) AS expired,
  CONVERT ( CHAR ( 10 ), rtnitem_arrival, 126 ) AS arrival,
  ( rtnitem_qty_good + rtnitem_qty_not_good ) AS qty_kirim,
  ( rtnitem_qty_verifikasi_good + rtnitem_qty_verifikasi_not_good ) AS qty_verifikasi,
   ( rtnitem_qty_good + rtnitem_qty_not_good ) - ( rtnitem_qty_verifikasi_good + rtnitem_qty_verifikasi_not_good ) as balance,
   rtnitem_qty_verifikasi_good,
  rtnitem_qty_verifikasi_not_good,
  rtnitem_remarks_verifikasi,
   CASE
WHEN reqrtn_destination like 'CK%' THEN 1
ELSE 0
END as ck,
CASE
        WHEN status_progress = 1 THEN
        'Menunggu Disetujui ' + a.reqrtn_ck
        WHEN status_progress = 2 THEN
        'Menunggu Pengecekan ' + a.reqrtn_destination 
        WHEN status_progress = 3 THEN
        'Selesai'
        WHEN status_progress = 4 THEN
        'Reject'  ELSE '' 
      END AS statusck,
    CASE      
        WHEN status_progress = 1 THEN
        'Menunggu Disetujui ' + a.reqrtn_ck 
        WHEN status_progress = 2 THEN
        'Menunggu Pengecekan ' + a.reqrtn_destination 
        WHEN status_progress = 3 THEN
        'Selesai' 
        WHEN status_progress = 4 THEN
        'Reject' ELSE '' 
      END AS statusotherck 
FROM
  header_returnck a
  INNER JOIN mst_req_type b ON a.reqrtn_type= b.id_mst_type
  LEFT JOIN mst_req_type_item c ON a.reqrtn_item_type= c.id_mst_type_item
  INNER JOIN detail_returnck d ON a.id_rtn= d.header_idrtn 

  ) a
  WHERE date_delivery BETWEEN '$start_date' and '$end_date' 
  and (reqrtn_user is not null or reqrtn_user='') 
   $user
  ) b
  $status
";
$stmtdetail = sqlsrv_query( $conn, $sql );
if( $stmtdetail === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$no=0;
while($row = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){


    echo " <tr>
    <td>".$row['reqrtn_code']."</td>
    <td>".$row['rtnitem_remarks']."</td>
    <td>".$row['date_req']."</td>
    <td>".$row['date_delivery']."</td>
    <td>".$row['date_arrival']."</td>
    <td>".$row['date_transaksi_sap']."</td>
    <td>".$row['req_type_name']."</td> 
    <td>".$row['reqrtn_user']."</td>
    <td>".$row['reqrtn_destination']."</td>
    <td>".$row['reqrtn_ck']."</td>
    <td>".$row['statusfixck']."</td>
    <td>".$row['rtnitem_code']."</td>
    <td>".$row['rtnitem_name']."</td>
    <td>".$row['rtnitem_uom']."</td>
    <td>".$row['rtnitem_cat']."</td>
    <td>".$row['rtnitem_reason']."</td>
    <td>".$row['arrival']."</td>
    <td>".$row['qty_kirim']."</td>
    <td>".$row['qty_verifikasi']."</td> 
    <td>".$row['balance']."</td>
    <td>".$row['rtnitem_remarks_verifikasi']."</td>
  </tr>";


       }


?>

 <!-- <?php echo  $sql; ?>   -->

</table>


<?php
}else{

?>

<table>
    <tr>
      <th scope="col" rowspan="2">Jenis Form</th>
      <?php if($_POST['kategori_retur']=='WADAH'){
        ?>
        <th scope="col" rowspan="2">Jenis Wadah</th>
        <?php
        }
        ?>
    <th scope="col" rowspan="2">No Dokumen</th>
    <th scope="col" rowspan="2">No Dokumen SAP</th>
      <th scope="col" rowspan="2">Tanggal Permintaan</th>
      <th scope="col" rowspan="2">Tanggal Pengambilan</th>
      <th scope="col" rowspan="2">Tanggal Kedatangan</th>
      <th scope="col" rowspan="2">Tanggal Posting SAP</th>  
      <th scope="col" rowspan="2">Tanggal Transaksi Portal</th>
      <th scope="col" rowspan="2">Jenis Permintaan</th>
       <th scope="col" rowspan="2">Toko Asal</th>
      <th scope="col" rowspan="2">Divisi Tujuan</th>
      <th scope="col" rowspan="2">CK Area</th>
      <th scope="col" rowspan="2">Status Dokumen</th>
      <th scope="col" rowspan="2">Kode Barang</th>
      <th scope="col" rowspan="2">Nama Barang</th>
      <th scope="col" rowspan="2">Satuan</th>
      <th scope="col" rowspan="2">Jenis Barang</th>
      <th scope="col" rowspan="2">Alasan Permintaan</th>
      <th scope="col" rowspan="2">Kadaluarsa Barang</th>
      <th scope="col" rowspan="2">Tanggal Terima Barang Dari CK</th>
      <th colspan="3" class="text-center" style="text-align: center;">Toko Kirim</th>
      <th colspan="3" class="text-center" style="text-align: center;">Verifikasi</th>
      <th scope="col" rowspan="2">Remarks Verifikasi</th>
      <th scope="col" rowspan="2">Balance</th>
    </tr>
    <tr>
    <th scope="col" >Jumlah Kirim</th>
      <th scope="col">Jumlah Kirim Bagus</th>
      <th scope="col">Jumlah kirim Tidak Bagus</th>
      <th scope="col">Jumlah Verifikasi</th>
      <th scope="col">Kondisi Barang Verifikasi Bagus</th>
      <th scope="col">Kondisi Barang Verifikasi Tidak Bagus</th>
</tr>



<?php

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$sql ="SELECT * FROM ( SELECT *, CASE
WHEN ck = 1 THEN statusck
ELSE statusotherck
END as statusfixck FROM (
SELECT
	a.reqrtn_code,
	a.reqrtn_nodoc_sap,
  CASE
WHEN a.reqrtn_type_req = 1 THEN 'SISTEM'
WHEN a.reqrtn_type_req = 2 THEN 'NON SISTEM'
WHEN a.reqrtn_type_req = 3 THEN 'WADAH'
WHEN a.reqrtn_type_req = 4 THEN 'NCR'
WHEN a.reqrtn_type_req = 5 THEN 'DAMAGE'
ELSE ''
END as jenisform,
	CONVERT ( CHAR ( 10 ), a.reqrtn_nodoc_sap_posting_date, 126 ) date_posting_sap,
  CONVERT ( CHAR ( 10 ), a.reqrtn_nodoc_sap_date, 126 ) date_transaksi_sap,
	CONVERT ( CHAR ( 10 ), a.reqrtn_date, 126 ) date_delivery,
  CONVERT ( CHAR ( 10 ), a.created_date, 126 ) date_req,
  CONVERT ( CHAR ( 10 ), a.reqrtn_destination_arrival_goods_date, 126 ) date_arrival,
	b.req_type_name,
	c.req_type_name_item,
	a.reqrtn_user,
	a.reqrtn_destination,
  a.reqrtn_ck,
	a.reqrtn_reason,
  a.reqrtn_nopica,
	rtnitem_code,
	rtnitem_name,
	rtnitem_uom,
	rtnitem_cat,
	rtnitem_reason,
	rtnitem_qty_good AS qty_good,
	rtnitem_qty_not_good AS qty_notgood,
	CONVERT ( CHAR ( 10 ), rtnitem_expired, 126 ) AS expired,
  CONVERT ( CHAR ( 10 ), rtnitem_arrival, 126 ) AS arrival,
	( rtnitem_qty_good + rtnitem_qty_not_good ) AS qty_kirim,
	( rtnitem_qty_verifikasi_good + rtnitem_qty_verifikasi_not_good ) AS qty_verifikasi,
  ( rtnitem_qty_good + rtnitem_qty_not_good ) - ( rtnitem_qty_verifikasi_good + rtnitem_qty_verifikasi_not_good ) as balance,
	rtnitem_qty_verifikasi_good,
	rtnitem_qty_verifikasi_not_good,
	rtnitem_remarks_verifikasi,
	 CASE
WHEN reqrtn_destination like 'CK%' THEN 1
ELSE 0
END as ck,
CASE
				WHEN status_progress = 1 THEN
				'Menunggu Disetujui ' + a.reqrtn_ck
				WHEN status_progress = 2 THEN
				'Menunggu Pengecekan ' + a.reqrtn_destination 
        WHEN status_progress = 3 THEN
				'Selesai'
        WHEN status_progress = 4 THEN
        'Reject'  ELSE '' 
			END AS statusck,
		CASE			
				WHEN status_progress = 1 THEN
				'Menunggu Disetujui ' + a.reqrtn_ck 
				WHEN status_progress = 2 THEN
				'Menunggu Pengecekan ' + a.reqrtn_destination 
				WHEN status_progress = 3 THEN
				'Selesai'
        WHEN status_progress = 4 THEN
        'Reject'  ELSE '' 
			END AS statusotherck,
      CASE
WHEN rtnitem_code in ('ITEM00041',
'ITEM00039',
'ITEM00038',
'ITEM00037',
'ITEM00036',
'ITEM00035',
'ITEM00034',
'ITEM00033',
'ITEM00032',
'ITEM00031',
'ITEM00030',
'ITEM00029',
'ITEM00028',
'ITEM00027',
'ITEM00026',
'ITEM00025',
'ITEM00023',
'ITEM00022',
'ITEM00021',
'ITEM00019',
'ITEM00018',
'ITEM00017',
'ITEM00016',
'ITEM00015',
'ITEM00001',
'UTES00291',
'SUBS01350') THEN 'WADAH EKONOMIS'
WHEN rtnitem_code in (
'ITEM00024',
'ITEM00020',
'UTES00491',
'UTES03750',
'UTES00460',
'UTES02491',
'UTES04104',
'UTES04101',
'UTES04102',
'UTES04103',
'UTES02490'
) THEN 'WADAH OPERASIONAL'
ELSE ''
END as jeniswadah
FROM
	header_returnck a
	INNER JOIN mst_req_type b ON a.reqrtn_type= b.id_mst_type
	LEFT JOIN mst_req_type_item c ON a.reqrtn_item_type= c.id_mst_type_item
	INNER JOIN detail_returnck d ON a.id_rtn= d.header_idrtn 
	) a
	WHERE	date_delivery BETWEEN '$start_date' and '$end_date' 
  and (reqrtn_user is not null or reqrtn_user='') 
	 $user $kategori_retur
	) b
	$status
";
$stmtdetail = sqlsrv_query( $conn, $sql );
if( $stmtdetail === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$no=0;

// echo $sql;
while($row = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){
 
if($_POST['kategori_retur'] =='WADAH'){
    $jeniswadah ="<td>".$row['jeniswadah']."</td>";
  }else{
    $jeniswadah='';
  }


    echo " <tr>
    <td>".$row['jenisform']."</td>
    ".$jeniswadah."
    <td>".$row['reqrtn_code']."</td>
    <td>".$row['reqrtn_nodoc_sap']."</td>
    <td>".$row['date_req']."</td>
    <td>".$row['date_delivery']."</td>
     <td>".$row['date_arrival']."</td> 
    <td>".$row['date_posting_sap']."</td>
    <td>".$row['date_transaksi_sap']."</td>
    <td>".$row['req_type_name']."</td>
    <td>".$row['reqrtn_user']."</td>
    <td>".$row['reqrtn_destination']."</td>
    <td>".$row['reqrtn_ck']."</td>
    <td>".$row['statusfixck']."</td>
    <td>".$row['rtnitem_code']."</td>
    <td>".$row['rtnitem_name']."</td>
    <td>".$row['rtnitem_uom']."</td>
    <td>".$row['rtnitem_cat']."</td>
    <td>".$row['rtnitem_reason']."</td>
    <td>".$row['expired']."</td>
    <td>".$row['arrival']."</td>
    <td>".$row['qty_kirim']."</td>
    <td>".$row['qty_good']."</td>
    <td>".$row['qty_notgood']."</td>
    <td>".$row['qty_verifikasi']."</td>
    <td>".$row['rtnitem_qty_verifikasi_good']."</td>
    <td>".$row['rtnitem_qty_verifikasi_not_good']."</td>
    <td>".$row['rtnitem_remarks_verifikasi']."</td>
    <td>".$row['balance']."</td>
  </tr>";


       }


?>


</table>

<?php
}
?>


</body>
</html>

