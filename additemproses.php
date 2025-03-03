<?php
include "db.php";

$created_by=htmlspecialchars(addslashes(trim(strip_tags($_SESSION["nama"]))));
$jenis = htmlspecialchars(addslashes(trim(strip_tags($_POST['jenis']))));
$divisi = htmlspecialchars(addslashes(trim(strip_tags($_POST['divisi']))));
$tipe = htmlspecialchars(addslashes(trim(strip_tags($_POST['tipe']))));
$kode_barang = htmlspecialchars(addslashes(trim(strip_tags($_POST['kode_barang']))));
$nama_barang = htmlspecialchars(addslashes(trim(strip_tags($_POST['nama_barang']))));
$satuan_barang = htmlspecialchars(addslashes(trim(strip_tags($_POST['satuan_barang']))));
$jenis_barang = htmlspecialchars(addslashes(trim(strip_tags($_POST['jenis_barang']))));
$kondisi_barang = htmlspecialchars(addslashes(trim(strip_tags($_POST['kondisi_barang']))));

if($jenis == 1){

  $div = 'STORE';

}else{

   $div = $divisi;

}

if($jenis_barang =="FOOD"){
  $expired_flag ='1';
}else{
  $expired_flag ='0';
}


$sqldetail = "SELECT count(item_code)  as cek from mst_item where item_type='$jenis' and item_code='$kode_barang' and div_name='$div'";
$stmtdetail = sqlsrv_query( $conn, $sqldetail );
if( $stmtdetail === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$resultcek = sqlsrv_fetch_array($stmtdetail);

if($resultcek['cek'] > 0){

 $_SESSION['pesan'] = '<b>Kode Barang '.$kode_barang.' Sudah Ada Di Database!</b>';
 
 header('Location: masteritem.php');
 
 }else{
 
 /* Initiate transaction. */  
 /* Exit script if transaction cannot be initiated. */  
 if ( sqlsrv_begin_transaction( $conn ) === false )  
 {  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
 }  
 
 $sqlheader = "INSERT  INTO mst_item (
              item_type,
              item_cat,
              div_name,
              item_code,
              item_name,
              item_uom,
              sap_flag,
              exp_flag,
              kondisi_flag,
              Active,
              created_date,
              created_by
              ) VALUES (
              '".htmlspecialchars(addslashes(trim(strip_tags($jenis))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($jenis_barang))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($div))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($kode_barang))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($nama_barang))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($satuan_barang))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($tipe))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($expired_flag))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($kondisi_barang))))."', 
              '0',
               getdate() ,
               '$created_by'
                ); SELECT @@IDENTITY as id;";
 
      // $params = array(
      //             $tanggal_permintaan,
      //             $jenis_permintaan,
      //             $jenis_barang,
      //             $store_request,
      //             $store_destination,
      //             $alasan,
      //             $keterangan,
      //             $created_by
      //       );
      // $stmt = sqlsrv_query( $conn, $sqlheader, $params);
 
      $stmt = sqlsrv_query( $conn, $sqlheader);
     
 
     if( $stmt  )  
     {  
          sqlsrv_commit($conn);  
          $_SESSION['pesan'] = '<b>Kode Barang '.$kode_barang.' Berhasil Di Proses!</b>';
          header('Location: masteritem.php');
 
     }else{
          echo "Errrorrrrr";
     }
 
 }
  
?>