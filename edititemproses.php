<?php
include "db.php";

$id = $_POST['id'];
$created_by=$_SESSION["nama"];
$jenis = $_POST['jenis'];
$divisi = $_POST['divisi'];
$tipe = $_POST['tipe'];
$kode_barang = $_POST['kode_barang'];
$nama_barang = $_POST['nama_barang'];
$satuan_barang = $_POST['satuan_barang'];
$jenis_barang = $_POST['jenis_barang'];
$kondisi_barang = $_POST['kondisi_barang'];
$status = $_POST['status'];

if($kode_barang ==''){
  $fixkodebarang = $_POST['kode_barang_edit'];
}else{
  $fixkodebarang=$kode_barang;
}

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
 
 /* Initiate transaction. */  
 /* Exit script if transaction cannot be initiated. */  
 if ( sqlsrv_begin_transaction( $conn ) === false )  
 {  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
 }  
 
 $sqlheader = "UPDATE mst_item SET
              item_type ='".htmlspecialchars(addslashes(trim(strip_tags($jenis))))."', 
              item_cat='".htmlspecialchars(addslashes(trim(strip_tags($jenis_barang))))."', 
              div_name='".htmlspecialchars(addslashes(trim(strip_tags($div))))."', 
              item_code='".htmlspecialchars(addslashes(trim(strip_tags($fixkodebarang))))."', 
              item_name='".htmlspecialchars(addslashes(trim(strip_tags($nama_barang))))."',
              item_uom='".htmlspecialchars(addslashes(trim(strip_tags($satuan_barang))))."', 
              sap_flag='".htmlspecialchars(addslashes(trim(strip_tags($tipe))))."',
              exp_flag='".htmlspecialchars(addslashes(trim(strip_tags($expired_flag))))."', 
              kondisi_flag= '".htmlspecialchars(addslashes(trim(strip_tags($kondisi_barang))))."',
              Active='".htmlspecialchars(addslashes(trim(strip_tags($status))))."',
              updated_date= getdate() ,
              updated_by= '$created_by'
              WHERE id_mst_item='$id'";
 
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
          $_SESSION['pesan'] = '<b>Kode Barang '.$fixkodebarang.' Berhasil Di Proses!</b>';
          header('Location: masteritem.php');
 
     }else{
          echo "Errrorrrrr";

          echo  $sqlheader;
     }
 
 
  
?>