<?php
include "db.php";
$id = $_POST['id'];
$kodesap = $_POST['kodesap'];
$date_posting = $_POST['date_posting'];
$created_by =  $_SESSION['nama'];

/* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  

$sqlheader = "UPDATE header_returnck SET
          reqrtn_nodoc_sap='".htmlspecialchars(addslashes(trim(strip_tags($kodesap))))."',
              reqrtn_nodoc_sap_date='".htmlspecialchars(addslashes(trim(strip_tags($date_posting))))."',
              status_progress=3
          WHERE id_rtn='$id'";

$stmt = sqlsrv_query( $conn, $sqlheader);

$sqllog = "INSERT  INTO log_return (
    log_idrtn,
    note_rtn,
    created_date,
    created_by
    ) VALUES (
     '$id', 
     'INPUT KODE SAP REQUEST',
     getdate(), 
     '$created_by'
      )";

    // $paramslog = array(
    //       $lastinsertid,
    //       $created_by
    // );
    // $stmt2 = sqlsrv_query( $conn, $sqllog, $paramslog);

    $stmt1 = sqlsrv_query( $conn, $sqllog);

if( $stmt &&  $stmt1 )  
{  
 sqlsrv_commit($conn);  
 echo "Kode SAP Berhasil Di Proses";
}else{
 echo $sqlheader;
 echo "Kode SAP Gagal Di Proses";
}

?>