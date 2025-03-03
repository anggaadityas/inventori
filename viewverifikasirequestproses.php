<?php
include "db.php";

use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

$id_tp = $_POST['id_tp'];
$code = $_POST['reqtp_code'];
$store_request =  $_POST['reqtp_user'];
$store_destination = $_POST['reqtp_destination'];
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

$serverNameHO = "192.168.1.5";
// $serverNameHO = "portal.multirasa.co.id";
$connectionInfoHO = array( "Database"=>"role", "UID"=>"sa", "PWD"=>"Mrn.14");
$connHO = sqlsrv_connect( $serverNameHO, $connectionInfoHO );
if( $connHO === false ) {
    die( print_r( sqlsrv_errors(), true));
}
$sqlck = "SELECT 
CASE
WHEN area=1 THEN 'CK JAKARTA'
WHEN area=2 THEN 'CK SURABAYA'
ELSE 'CK JAKARTA'
END as CK,
CASE
WHEN area=1 THEN 'angga.aditya@multirasa.co.id'
WHEN area=2 THEN 'angga.aditya@multirasa.co.id'
ELSE ''
END as emailck from storesett where storeCode='$store_request'";
$stmtck = sqlsrv_query( $connHO, $sqlck );
if( $stmtck === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowck = sqlsrv_fetch_array( $stmtck, SQLSRV_FETCH_ASSOC);

$emailck= $rowck['emailck'];

if($created_by =='OPR.HO'){
  $CK ='CK JAKARTA';
}else{
  $CK= $rowck['CK'];
}

if($status_request == 'Reject'){

  /* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  

$sqlheader = "UPDATE header_tp SET
          reqtp_user_verifikasi='".$status_request."',
              reqtp_user_verifikasi_date=getdate(),
          reqtp_user_verifikasi_note='".htmlspecialchars(addslashes(trim(strip_tags($note_request_verifkasi))))."',
          reqtp_ck_destination='".$CK."',
          status_progress=5
          WHERE id_tp='$id_tp'";

      $stmt = sqlsrv_query( $conn, $sqlheader);



     $sqllog = "INSERT  INTO log_tp (
      log_idtp,
      note_tp,
      created_date,
      created_by
      ) VALUES (
       '$id_tp', 
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


          $sqldetail = "SELECT *,convert(char(10),tpitem_expired,126) expired FROM detail_tp where header_idtp='$id_tp'";
          $stmtdetail = sqlsrv_query( $conn, $sqldetail );
          if( $stmtdetail === false) {
              die( print_r( sqlsrv_errors(), true) );
          }
          $no=0;

          $laporan="<h4><b>Data Transfer Putus Store</b></h4>";
          $laporan .="<br/>";
          $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
          $laporan .="<tr style=\"bgcolor: blue;\">";
          $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Kirim</td><td>Kadaluarsa</td><td>Qty Terima</td>";
          $laporan .="</tr>";
        
         while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
          {
            $fixqtyver =0;
            $fixqtyver = $rowdetail['tpitem_qty_verifikasi_good'] + $rowdetail['tpitem_qty_verifikasi_not_good'];
              $laporan .="<tr>";
              $laporan .="<td>".$rowdetail['tpitem_code']."</td><td>".$rowdetail['tpitem_name']."</td><td>".$rowdetail['tpitem_uom']."</td><td>".$rowdetail['tpitem_cat']."</td><td>".$rowdetail['tpitem_reason']."</td><td>".number_format($rowdetail['tpitem_qty_approve'],2,'.',',')."</td><td>".$rowdetail['expired']."</td><td>".number_format($fixqtyver,2,'.',',')."</td>";
              $laporan .="</tr>";
          }
          $laporan .="</table>";
          

          $name  ='Info Verifikasi Permintaan Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          $subject =''.$status_request.' Permintaan Barang Dengan No #'.$code.'';
          $body ='Dear Store '.$store_destination.' <br><br> 
                        Permintaan Barang <br>dengan No <b>'.$code.' </b> telah dilakukan reject
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
          if($rowck['CK'] =='CK JAKARTA'){  
            // $cc ='angga.aditya@multirasa.co.id'; 
            // $cc1 ='gabriella.tardini@multirasa.co.id';
            // $cc2 ='alexius.sugeng@multirasa.co.id';
            $cc ='yoshinoya.transfertoko.jkt@multirasa.co.id';
            $mail->addCC($cc);
            // $mail->addCC($cc1);
            // $mail->addCC($cc2);
          }else if($rowck['CK'] =='CK SURABAYA'){ 
            // $cc ='ck.admin.sby2@multirasa.co.id';
            // $cc1 ='henri.hakim@multirasa.co.id';
            // $cc2 ='januar.kusriwahjudi@multirasa.co.id';
            $cc ='yoshinoya.transfertoko.sby@multirasa.co.id';
            $mail->addCC($cc);
            // $mail->addCC($cc1);
            // $mail->addCC($cc2);
          }else{
          }
          // $mail->addCC('angga.aditya@multirasa.co.id');
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
          // header('Location: listrequest.php');    
          echo "Transaction was committed.\n";  
     }  
     else  
     {  
          sqlsrv_rollback($conn);  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          // header('Location: listrequest.php');   
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

$sqlheader = "UPDATE header_tp SET
          reqtp_user_verifikasi='".$status_request."',
              reqtp_user_verifikasi_date=getdate(),
          reqtp_user_verifikasi_note='".htmlspecialchars(addslashes(trim(strip_tags($note_request_verifkasi))))."',
          reqtp_ck_destination='".$CK."',
          status_progress=3
          WHERE id_tp='$id_tp'";

      $stmt = sqlsrv_query( $conn, $sqlheader);


      $sqldetail = "";
      foreach($_POST['id_barang'] as $option => $opt){

                          $sqldetail .= "UPDATE detail_tp SET 
                          tpitem_qty_verifikasi_good='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtyverifikasi_good'][$option]))))."',
                           tpitem_qty_verifikasi_not_good='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtyverifikasi_not_good'][$option]))))."',
                          tpitem_remarks_verifikasi='".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barangverifikasi'][$option]))))."',
                          update_date=getdate()
                          WHERE
                          header_idtp='$id_tp'
                           and tpitem_id='".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."'";
                          $sqldetail .= ";";

      }

      $sqldetailfix = rtrim($sqldetail,";");
      $stmt1 = sqlsrv_query($conn,$sqldetailfix);

     $sqllog = "INSERT  INTO log_tp (
      log_idtp,
      note_tp,
      created_date,
      created_by
      ) VALUES (
       '$id_tp', 
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


          $sqldetail = "SELECT *,convert(char(10),tpitem_expired,126) expired FROM detail_tp where header_idtp='$id_tp'";
          $stmtdetail = sqlsrv_query( $conn, $sqldetail );
          if( $stmtdetail === false) {
              die( print_r( sqlsrv_errors(), true) );
          }
          $no=0;

          $laporan="<h4><b>Data Transfer Putus Store</b></h4>";
          $laporan .="<br/>";
          $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
          $laporan .="<tr style=\"bgcolor: blue;\">";
          $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Kirim</td><td>Kadaluarsa</td><td>Qty Terima</td>";
          $laporan .="</tr>";
        
         while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
          {
            $fixqtyver =0;
            $fixqtyver = $rowdetail['tpitem_qty_verifikasi_good'] + $rowdetail['tpitem_qty_verifikasi_not_good'];
              $laporan .="<tr>";
              $laporan .="<td>".$rowdetail['tpitem_code']."</td><td>".$rowdetail['tpitem_name']."</td><td>".$rowdetail['tpitem_uom']."</td><td>".$rowdetail['tpitem_cat']."</td><td>".$rowdetail['tpitem_reason']."</td><td>".number_format($rowdetail['tpitem_qty_approve'],2,'.',',')."</td><td>".$rowdetail['expired']."</td><td>".number_format($fixqtyver,2,'.',',')."</td>";
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
          if($rowck['CK'] =='CK JAKARTA'){  
            // $cc ='angga.aditya@multirasa.co.id'; 
            // $cc1 ='gabriella.tardini@multirasa.co.id';
            // $cc2 ='alexius.sugeng@multirasa.co.id';
            $cc ='yoshinoya.transfertoko.jkt@multirasa.co.id';
            $mail->addCC($cc);
            // $mail->addCC($cc1);
            // $mail->addCC($cc2);
          }else if($rowck['CK'] =='CK SURABAYA'){ 
            // $cc ='ck.admin.sby2@multirasa.co.id';
            // $cc1 ='henri.hakim@multirasa.co.id';
            // $cc2 ='januar.kusriwahjudi@multirasa.co.id';
            $cc ='yoshinoya.transfertoko.sby@multirasa.co.id';
            $mail->addCC($cc);
            // $mail->addCC($cc1);
            // $mail->addCC($cc2);
          }else{
          }
          // $mail->addCC('angga.aditya@multirasa.co.id');
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
          // header('Location: listrequest.php');    
          echo "Transaction was committed.\n";  
     }  
     else  
     {  
          sqlsrv_rollback($conn);  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          // header('Location: listrequest.php');   
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