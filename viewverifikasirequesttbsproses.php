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
$note_request_verifkasi = $_POST['note_request_verifkasi'];
$status_request = $_POST['status_request'];
// $alasan =$_POST['alasan'];
$created_by =  $_SESSION['nama'];

$hostName = "192.168.2.136";
$username = "root";
$password = "aas260993";
$dbname = "voucher_trial";
$mysqli = new mysqli($hostName, $username, $password, $dbname);

$querystore="SELECT email from mst_user where nama='$store_destination'";
$hasilstore = $mysqli->query($querystore);
$rowstore = $hasilstore->fetch_assoc();
$emailstoredestination = $rowstore['email'];

// $serverNameHO = "192.168.1.5";
// $serverNameHO = "portal.mutirasa.co.id";
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

if($status_request == 'Reject'){

  /* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  

$sqlheader = "UPDATE header_tb SET
          reqtb_user_verifikasi='".$status_request."',
              reqtb_user_verifikasi_date=getdate(),
          reqtb_user_verifikasi_note='".htmlspecialchars(addslashes(trim(strip_tags($note_request_verifkasi))))."',
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


          $sqldetail = "SELECT *,convert(char(10),tbitem_expired,126) expired FROM detail_tb where header_idtb='$id_tb'";
          $stmtdetail = sqlsrv_query( $conn, $sqldetail );
          if( $stmtdetail === false) {
              die( print_r( sqlsrv_errors(), true) );
          }
          $no=0;

          $laporan="<h4><b>Data Transfer Balik Store</b></h4>";
          $laporan .="<br/>";
          $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
          $laporan .="<tr style=\"bgcolor: blue;\">";
          $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Kirim</td><td>Kadaluarsa</td><td>Qty Terima</td>";
          $laporan .="</tr>";
        
         while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
          {
            $fixqtyver =0;
            $fixqtyver = $rowdetail['tbitem_qty_verifikasi'];
              $laporan .="<tr>";
              $laporan .="<td>".$rowdetail['tbitem_code']."</td><td>".$rowdetail['tbitem_name']."</td><td>".$rowdetail['tbitem_uom']."</td><td>".$rowdetail['tbitem_cat']."</td><td>".$rowdetail['tbitem_reason']."</td><td>".number_format($rowdetail['tbitem_qty_approve'],2,'.',',')."</td><td>".$rowdetail['expired']."</td><td>".number_format($fixqtyver,2,'.',',')."</td>";
              $laporan .="</tr>";
          }
          $laporan .="</table>";
          

          $name  ='Info Verifikasi Permintaan Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          $subject =''.$status_request.' Permintaan Barang Dengan No #'.$code.'';
          $body ='Dear Store '.$store_destination.' <br><br> 
                        Permintaan Barang <br>dengan No <b>'.$code.' </b> telah ditolak
                       Oleh Store '.$store_request.' 
                       <br><br>   
                       '.$laporan.'
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
          $mail->addAddress($emailstoredestination); 
          // if($rowck['CK'] =='CK JAKARTA'){  
          //   $cc ='wh.inv.jkt@multirasa.co.id'; 
          //   $cc1 ='gabriella.tardini@multirasa.co.id';
          //   $cc2 ='alexius.sugeng@multirasa.co.id';
          //   $mail->addCC($cc);
          //   $mail->addCC($cc1);
          //   $mail->addCC($cc2);
          // }else if($rowck['CK'] =='CK SURABAYA'){ 
          //   $cc ='ck.admin.sby2@multirasa.co.id';
          //   $cc1 ='henri.hakim@multirasa.co.id';
          //   $cc2 ='januar.kusriwahjudi@multirasa.co.id';
          //   $mail->addCC($cc);
          //   $mail->addCC($cc1);
          //   $mail->addCC($cc2);
          // }else{
          // }
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
          // header('Location: listrequesttbs.php');    
          echo "Transaction was committed.\n";  
     }  
     else  
     {  
          sqlsrv_rollback($conn);  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          // header('Location: listrequesttbs.php');   
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
          reqtb_user_verifikasi='".$status_request."',
              reqtb_user_verifikasi_date=getdate(),
          reqtb_user_verifikasi_note='".htmlspecialchars(addslashes(trim(strip_tags($note_request_verifkasi))))."',
          status_progress=3
          WHERE id_tb='$id_tb'";

      $stmt = sqlsrv_query( $conn, $sqlheader);


      $sqldetail = "";
      foreach($_POST['id_barang'] as $option => $opt){

                          $sqldetail .= "UPDATE detail_tb SET 
                          tbitem_qty_verifikasi='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtyverifikasi_good'][$option]))))."',
                          tbitem_remarks_verifikasi='".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barangverifikasi'][$option]))))."',
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


          $sqldetail = "SELECT *,convert(char(10),tbitem_expired,126) expired FROM detail_tb where header_idtb='$id_tb'";
          $stmtdetail = sqlsrv_query( $conn, $sqldetail );
          if( $stmtdetail === false) {
              die( print_r( sqlsrv_errors(), true) );
          }
          $no=0;

          $laporan="<h4><b>Data Transfer Balik Store</b></h4>";
          $laporan .="<br/>";
          $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
          $laporan .="<tr style=\"bgcolor: blue;\">";
          $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Kirim</td><td>Kadaluarsa</td><td>Qty Terima</td>";
          $laporan .="</tr>";
        
         while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
          {
            $fixqtyver =0;
            $fixqtyver = $rowdetail['tbitem_qty_verifikasi'];
              $laporan .="<tr>";
              $laporan .="<td>".$rowdetail['tbitem_code']."</td><td>".$rowdetail['tbitem_name']."</td><td>".$rowdetail['tbitem_uom']."</td><td>".$rowdetail['tbitem_cat']."</td><td>".$rowdetail['tbitem_reason']."</td><td>".number_format($rowdetail['tbitem_qty_approve'],2,'.',',')."</td><td>".$rowdetail['expired']."</td><td>".number_format($fixqtyver,2,'.',',')."</td>";
              $laporan .="</tr>";
          }
          $laporan .="</table>";
          

          $name  ='Info Verifikasi Permintaan Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          $subject =''.$status_request.' Permintaan Barang Dengan No #'.$code.'';
          $body ='Dear Store '.$store_destination.' <br><br> 
                        Permintaan Barang <br>dengan No <b>'.$code.' </b> telah dilakukan verifikasi
                       Oleh Store '.$store_request.' 
                       <br><br>   
                       '.$laporan.'
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
          $mail->addAddress($emailstoredestination); 
          // if($rowck['CK'] =='CK JAKARTA'){  
          //   $cc ='wh.inv.jkt@multirasa.co.id'; 
          //   $cc1 ='gabriella.tardini@multirasa.co.id';
          //   $cc2 ='alexius.sugeng@multirasa.co.id';
          //   $mail->addCC($cc);
          //   $mail->addCC($cc1);
          //   $mail->addCC($cc2);
          // }else if($rowck['CK'] =='CK SURABAYA'){ 
          //   $cc ='ck.admin.sby2@multirasa.co.id';
          //   $cc1 ='henri.hakim@multirasa.co.id';
          //   $cc2 ='januar.kusriwahjudi@multirasa.co.id';
          //   $mail->addCC($cc);
          //   $mail->addCC($cc1);
          //   $mail->addCC($cc2);
          // }else{
          // }
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
          // header('Location: listrequesttbs.php');    
          echo "Transaction was committed.\n";  
     }  
     else  
     {  
          sqlsrv_rollback($conn);  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          // header('Location: listrequesttbs.php');   
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