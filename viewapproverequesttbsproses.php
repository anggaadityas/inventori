<?php
include "db.php";

use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

$id_tb = $_POST['id_tb'];
$code = $_POST['reqtb_code'];
$store_request =  $_POST['reqtb_user'];
$store_destination = $_POST['reqtb_destination'];
$note_request_approve = $_POST['note_request_approve'];
$status_request = $_POST['status_request'];
// $date_approve = $_POST['date_approve'];
// $alasan =$_POST['alasan'];
$created_by =  $_SESSION['nama'];

$hostName = "192.168.2.136";
$username = "root";
$password = "aas260993";
$dbname = "voucher_trial";
$mysqli = new mysqli($hostName, $username, $password, $dbname);

$querystore="SELECT email from mst_user where nama='$store_request'";
$hasilstore = $mysqli->query($querystore);
$rowstore = $hasilstore->fetch_assoc();
$emailstoredestination = $rowstore['email'];


if($status_request == 'Reject'){

  /* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  

$sqlheader = "UPDATE header_tb SET
          reqtb_destination_approve='".$status_request."',
              reqtb_destination_approve_date=getdate(),
          reqtb_destination_approve_note='".htmlspecialchars(addslashes(trim(strip_tags($note_request_approve))))."',
          status_progress=6
          WHERE id_tb='$id_tb'";

      $stmt = sqlsrv_query( $conn, $sqlheader);


     $sqllog = "INSERT  INTO log_tb (
      log_idtb,
      note_tb,
      created_date,
      created_by
      ) VALUES (
       '$id_tb', 
       'REJECT REQUEST',
       getdate(), 
       '$created_by'
        )";

      // $paramslog = array(
      //       $lastinsertid,
      //       $created_by
      // );
      // $stmt2 = sqlsrv_query( $conn, $sqllog, $paramslog);

      $stmt2 = sqlsrv_query( $conn, $sqllog);

     if( $stmt && $stmt2 )  
     {  
          sqlsrv_commit($conn);  

          $cc="angga.aditya@multirasa.co.id";

          $name  ='Info Persetujuan Permintaan Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          $subject =''.$status_request.' Permintaan Barang Dengan No #'.$code.'';
          $body ='Dear Store '.$store_request.' <br><br> 
                       Permintaan Barang <br>dengan No <b>'.$code.' </b> telah ditolak
                       Oleh Store '.$store_destination.'
                       <br><br>   
                       Note : <br> 
                       '.$note_request_approve.'
                       <br><br><br> 
                       Terimakasih.';

          $mail = new PHPMailer();

          //SMtb Settings
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
          $mail->addAddress($emailstoredestination); 
          // if($status_request =='Approved'){
          //   $emailcc =  $mail->addCC($cc);
          // }else{
          //   $emailcc ='';
          // }//enter you email address
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
          header('Location: listapproverequesttbs.php');
          echo "Transaction was committed.\n";  
     }  
     else  
     {  
          sqlsrv_rollback($conn);  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          header('Location: listapproverequesttbs.php');
          echo "Transaction was rolled back.\n"; 
          echo $sqlheader."\n";  
          echo $sqldetail."\n";  
          echo $sqllog."\n";  
     } 

}else{


  /* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  

$sqlheader = "UPDATE header_tb SET
          reqtb_destination_approve='".$status_request."',
              reqtb_destination_approve_date=getdate(),
          reqtb_destination_approve_note='".htmlspecialchars(addslashes(trim(strip_tags($note_request_approve))))."',
          status_progress=2
          WHERE id_tb='$id_tb'";

      $stmt = sqlsrv_query( $conn, $sqlheader);


      $sqldetail = "";
      foreach($_POST['id_barang'] as $option => $opt){

        if($_POST['expired_date'][$option] == ''){
            $fixexpired = '(NULL)';
          }else{
            $fixexpired ="'".$_POST['expired_date'][$option]."'";
          }

                          $sqldetail .= "UPDATE detail_tb SET 
                          tbitem_qty_approve='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtyverifikasi'][$option]))))."',
                          tbitem_expired=$fixexpired,
                          tbitem_remarks_approve='".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barangverifikasi'][$option]))))."',
                          update_date=getdate()
                          WHERE
                          header_idtb='$id_tb'
                           and tbitem_id='".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."'";
                          $sqldetail .= ";";

      }

      $sqldetailfix = rtrim($sqldetail,";");
      $stmt1 = sqlsrv_query($conn,$sqldetailfix);

     $sqllog = "INSERT  INTO log_tb (
      log_idtb,
      note_tb,
      created_date,
      created_by
      ) VALUES (
       '$id_tb', 
       'APPROVE REQUEST',
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

          $cc="angga.aditya@multirasa.co.id";

          $name  ='Info Persetujuan Permintaan Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          $subject =''.$status_request.' Permintaan Barang Dengan No #'.$code.'';
          $body ='Dear Store '.$store_request.' <br><br> 
                       Permintaan Barang <br>dengan No <b>'.$code.' </b> telah disetujui
                       Oleh Store '.$store_destination.'
                       <br><br>   
                       Note : <br> 
                       '.$note_request_approve.'
                       <br><br><br> 
                       Terimakasih.';

          $mail = new PHPMailer();

          //SMtb Settings
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
          $mail->addAddress($emailstoredestination); 
          // if($status_request =='Approved'){
          //   $emailcc =  $mail->addCC($cc);
          // }else{
          //   $emailcc ='';
          // }//enter you email address
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
          header('Location: listapproverequesttbs.php');
          echo "Transaction was committed.\n";  
     }  
     else  
     {  
          sqlsrv_rollback($conn);  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          header('Location: listapproverequesttbs.php');
          echo "Transaction was rolled back.\n"; 
          echo $sqlheader."\n";  
          echo $sqldetail."\n";  
          echo $sqllog."\n";  
     } 


}




/* Free statement and connection resources. */  
sqlsrv_free_stmt( $stmt);  
sqlsrv_free_stmt( $stmt1);  
sqlsrv_free_stmt( $stmt2);  
sqlsrv_close( $conn);  

?>