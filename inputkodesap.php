<?php
include "db.php";
use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

$id = $_POST['id'];
$kodesap = $_POST['kodesap'];
$date_posting = $_POST['date_posting'];
$tokoasal = $_POST['tokoasal'];
$tokodestination = $_POST['tokodestination'];
$code = $_POST['code'];
$created_by =  $_SESSION['nama'];


$hostName = "192.168.2.136";
$username = "root";
$password = "aas260993";
$dbname = "voucher_trial";
$mysqli = new mysqli($hostName, $username, $password, $dbname);

$querystore="SELECT email from mst_user where nama='$tokoasal'";
$hasilstore = $mysqli->query($querystore);
$rowstore = $hasilstore->fetch_assoc();
$emailtokoasal = $rowstore['email'];

$querystore1="SELECT email from mst_user where nama='$tokodestination'";
$hasilstore1 = $mysqli->query($querystore1);
$rowstore1 = $hasilstore1->fetch_assoc();
$emailtokodestination = $rowstore1['email'];

/* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  

$sqlheader = "UPDATE header_tp SET
          reqtp_nodoc_sap='".htmlspecialchars(addslashes(trim(strip_tags($kodesap))))."',
              reqtp_nodoc_sap_posting_date='".htmlspecialchars(addslashes(trim(strip_tags($date_posting))))."',
              reqtp_nodoc_sap_date=getdate(),
              status_progress=4
          WHERE id_tp='$id'";

$stmt = sqlsrv_query( $conn, $sqlheader);

$sqllog = "INSERT  INTO log_tp (
    log_idtp,
    note_tp,
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

 $name  ='Info Pemostingan SAP Permintaan Barang Transfer Putus';
          $email = 'info.voucherrequest@multirasa.co.id';
          $subject ='Info Pemostingan SAP Permintaan Barang Dengan No #'.$code.'';
          $body ='Dear All  Store <br><br> 
                       Permintaan Barang <br>dengan No <b>'.$code.' </b> telah dilakukan pemostingan di SAP
                       Oleh CK
                       <br><br> 
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
          $mail->addAddress($emailtokoasal); 
          $mail->addAddress($emailtokodestination); 
          $mail->Subject = ("$subject");
          $mail->Body = $body;

          if ($mail->send()) {
              $status = "success";
              $response = "Email is sent!";
          } else {
              $status = "failed";
              $response = "Something is wrong: <br><br>" . $mail->ErrorInfo;
          }

 echo "No Dokumen SAP Berhasil Di Proses";

}else{
 echo $sqlheader;
 echo "No Dokumen SAP Gagal Di Proses";
}

?>