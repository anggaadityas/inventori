<?php
include "db.php";
error_reporting(0);

use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

$id_rtn = $_POST['id_rtn'];
$code = $_POST['reqrtn_code'];
$store_request =  $_POST['reqrtn_user'];
$store_destination = $_POST['reqrtn_destination'];
$note_request_verifkasi = $_POST['note_request_verifkasi'];
$status_request = $_POST['status_request'];
// $alasan =$_POST['alasan'];
$date_verifikasi = $_POST['date_verifikasi'];
$created_by =  $_SESSION['nama'];
$reqrtn_type_req = $_POST['reqrtn_type_req'];



if($_POST['kode_sap'] ==''){
    $kode_sap = 'NULL';
    $progress =3;
}else{
    $kode_sap =  "'".$_POST['kode_sap']."'";
    $progress =4;
}

if($_POST['date_posting'] ==''){
    $date_posting = 'NULL';
    $progress =3;
}else{
    $date_posting =  "'".$_POST['date_posting']."'";
    $progress =4;
}

$hostName = "192.168.2.136";
$username = "root";
$password = "aas260993";
$dbname = "voucher_trial";
$mysqli = new mysqli($hostName, $username, $password, $dbname);

$querystore="SELECT email from mst_user where nama='$store_request'";
$hasilstore = $mysqli->query($querystore);
$rowstore = $hasilstore->fetch_assoc();
$emailstore = $rowstore['email'];

$querydivisi="SELECT email from mst_user where nama='$store_destination'";
$hasildivisi = $mysqli->query($querydivisi);
$rowdivisi = $hasildivisi->fetch_assoc();
$emaildivisi = $rowdivisi['email'];

// $serverNameHO = "portal.multirasa.co.id";
// $connectionInfoHO = array( "Database"=>"role", "UID"=>"sa", "PWD"=>"Mrn.14");
// $connHO = sqlsrv_connect( $serverNameHO, $connectionInfoHO );
// if( $connHO === false ) {
//     die( print_r( sqlsrv_errors(), true));
// }
// $sqlck = "SELECT 
// CASE
// WHEN area=1 THEN 'CK JAKARTA'
// WHEN area=2 THEN 'CK SURABAYA'
// ELSE ''
// END as CK,
// CASE
// WHEN area=1 THEN 'angga.aditya@multirasa.co.id'
// WHEN area=2 THEN 'angga.aditya@multirasa.co.id'
// ELSE ''
// END as emailck from storesett where storeCode='$store_request'";
// $stmtck = sqlsrv_query( $connHO, $sqlck );
// if( $stmtck === false) {
//     die( print_r( sqlsrv_errors(), true) );
// }
// $rowck = sqlsrv_fetch_array( $stmtck, SQLSRV_FETCH_ASSOC);
// $CK= $rowck['CK'];
// $emailck= $rowck['emailck'];


/* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  


if($created_by =="CK JAKARTA" OR $created_by="CK SURABAYA" OR $created_by =="ENG JAKARTA" OR $created_by="ENG SURABAYA"){

    $sqlheader = "UPDATE header_returnck SET
         reqrtn_type_req='".$reqrtn_type_req."',
        reqrtn_destination_approve='".$status_request."',
        reqrtn_destination_approve_date=getdate(),
        reqrtn_destination_arrival_goods_date='".$date_verifikasi."',
        reqrtn_destination_approve_note='".htmlspecialchars(addslashes(trim(strip_tags($note_request_verifkasi))))."',
        reqrtn_nodoc_sap=".$kode_sap.",
        reqrtn_nodoc_sap_posting_date=  ".$date_posting.",
        reqrtn_nodoc_sap_date= getdate(),
        status_progress=3
        WHERE id_rtn='$id_rtn'";
        $stmt = sqlsrv_query( $conn, $sqlheader);

}else{
    
$sqlheader = "UPDATE header_returnck SET
    reqrtn_destination_approve='".$status_request."',
    reqrtn_destination_approve_date=getdate(),
    reqrtn_destination_arrival_goods_date='".$date_verifikasi."',
    reqrtn_destination_approve_note='".htmlspecialchars(addslashes(trim(strip_tags($note_request_verifkasi))))."',
    reqrtn_nodoc_sap=(NULL),
    reqrtn_nodoc_sap_posting_date=  (NULL),
    reqrtn_nodoc_sap_date= (NULL),
    status_progress=".$progress."
    WHERE id_rtn='$id_rtn'";
    $stmt = sqlsrv_query( $conn, $sqlheader);
    
}

      $sqldetail = "";
      foreach($_POST['id_barang'] as $option => $opt){

                          $sqldetail .= "UPDATE detail_returnck SET 
                          rtnitem_qty_verifikasi_good='".str_replace(',','',htmlspecialchars(addslashes(trim(strip_tags($_POST['qty_good'][$option])))))."',
                           rtnitem_qty_verifikasi_not_good='".str_replace(',','',htmlspecialchars(addslashes(trim(strip_tags($_POST['qty_notgood'][$option])))))."',
                          rtnitem_remarks_verifikasi='".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barangverifikasi'][$option]))))."',
                          update_date=getdate()
                          WHERE
                          header_idrtn='$id_rtn'
                        and rtnitem_id='".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."' and id_detailrtn='".htmlspecialchars(addslashes(trim(strip_tags($_POST['iddetailretur'][$option]))))."'  ";
                          $sqldetail .= ";"; 

      }

      $sqldetailfix = rtrim($sqldetail,";");
      $stmt1 = sqlsrv_query($conn,$sqldetailfix);

     $sqllog = "INSERT  INTO log_return (
      log_idrtn,
      note_rtn,
      created_date,
      created_by
      ) VALUES (
       '$id_rtn', 
       'VERIFIKASI REQUEST',
       getdate(), 
       '$created_by'
        )";

      // $paramslog = array(
      //       $lastinsertid,
      //       $created_by
      // );
      // $stmt2 = sqlsrv_query( $conn, $sqllog, $paramslog);

      $stmt2 = sqlsrv_query( $conn, $sqllog);

     if( $stmt && $stmt1 && $stmt2 )  
     {  
          sqlsrv_commit($conn);  


          $name  ='Info Verifikasi Permintaan Retur Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          // $subject =''.$status_request.' Request Retur Barang Dengan No #'.$code.'';
          $subject =''.$store_request.' - Verifikasi Retur Barang Dengan No #'.$code.'';
          $body ='Dear Store '.$store_request.' <br><br> 
                       Permintaan Retur Barang <br>dengan No <b>'.$code.' </b> telah dilakukan verifikasi
                       Oleh '.$store_destination.'
                       <br><br>   
                       Note : <br> 
                       '.$note_request_verifkasi.'
                       <br><br><br> 
                       Terimakasih.';

          $mail = new PHPMailer();

          //SMTP Settings
          $mail->isSMTP();
          $mail->Host = "mail.multirasa.co.id";
          $mail->SMTPAuth = true;
          $mail->Username = "info.voucherrequest@multirasa.co.id"; //enter you email address
          $mail->Password = 'yoshimulti'; //enter you email password
          $mail->Port = 465;
          $mail->SMTPSecure = "ssl";
  
          //Email Settings
          $mail->isHTML(true);
          $mail->setFrom($email, $name);
          $mail->addAddress($emailstore);
          if($store_destination =='CK JAKARTA'){
            $cc ='wh.inv.jkt@multirasa.co.id'; 
            $cc1 ='gabriella.tardini@multirasa.co.id';
            $cc2 ='alexius.sugeng@multirasa.co.id';
            // $mail->addCC($cc);
            // $mail->addCC($cc1);
            // $mail->addCC($cc2);
          }else if($store_destination =='CK SURABAYA'){
            $cc ='ck.admin.sby2@multirasa.co.id';
            $cc1 ='henri.hakim@multirasa.co.id';
            $cc2 ='januar.kusriwahjudi@multirasa.co.id';
            $mail->addCC($cc);
            $mail->addCC($cc1);
            $mail->addCC($cc2);
          }else{
            $mail->addCC($emaildivisi);
          }
          $mail->Subject = ("$subject");
          $mail->Body = $body;

          if ($mail->send()) {
              $status = "success";
              $response = "Email is sent!";
          } else {
              $status = "failed";
              $response = "Something is wrong: <br><br>" . $mail->ErrorInfo;
          }
          $_SESSION['pesan'] = '<b>Permintaan Berhasil Di Proses!</b>';    
          echo "Transaction was committed.\n";  
     }  
     else  
     {  
          sqlsrv_rollback($conn);  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';    
          echo "Transaction was rolled back.\n"; 
          echo $sqlheader."\n";  
          echo $sqldetail."\n";  
          echo $sqllog."\n";  
     } 

/* Free statement and connection resources. */  
sqlsrv_free_stmt( $stmt);  
sqlsrv_free_stmt( $stmt1);  
sqlsrv_free_stmt( $stmt2);  
sqlsrv_close( $conn);  

?>