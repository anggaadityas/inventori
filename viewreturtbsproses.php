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
$tanggal_pengembalian = $_POST['tanggal_pengembalian'];
// $date_approve = $_POST['date_approve'];
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
// $pluscal = $_POST['itempluscal']; 
// $minuscal = $_POST['itemminuscal'];   
// $plus = $_POST['itemplus'];  
// $minus =  str_replace("-","",$_POST['itemminus']);
$selisi = $_POST['selisi'];
$status_destination_retur_verifikasi =  $_POST['status_destination_retur_verifikasi'];


$sqlflagpengembalian="SELECT count(COALESCE(pengembalianke,0))+1 as fixpengembalianke  from (
  SELECT flag pengembalianke from detail_returntb 
  WHERE header_idrtrtb='$id_tb'
  group by header_idrtrtb,flag
  ) a";
$hasilcekpengembalian= sqlsrv_query( $conn, $sqlflagpengembalian );
$rowflag = sqlsrv_fetch_array( $hasilcekpengembalian, SQLSRV_FETCH_ASSOC);
$flagpengembalian = $rowflag['fixpengembalianke'];




if($selisi < 0){

        /* Initiate transaction. */  
        /* Exit script if transaction cannot be initiated. */  
        if ( sqlsrv_begin_transaction( $conn ) === false )  
        {  
             echo "Could not begin transaction.\n";  
             die( print_r( sqlsrv_errors(), true ));  
        }  

        $sqlheader = "UPDATE header_tb SET
                  reqtb_user_retur='".$status_request."',
                     reqtb_destination_retur_verifikasi=(NULL),
                     reqtb_destination_retur_verifikasi_date=(NULL),
                     reqtb_destination_retur_verifikasi_note=(NULL),
                      reqtb_user_retur_date=getdate(),
                      reqtb_user_retur_note='".htmlspecialchars(addslashes(trim(strip_tags($note_request_approve))))."',
                  status_progress=4
                  WHERE id_tb='$id_tb'";

              $stmt = sqlsrv_query( $conn, $sqlheader);
                  $sqldetail = "INSERT INTO detail_returntb  (
                    header_idrtrtb,
                    header_detailid,
                    rtrtbitem_id,
                    rtrtbitem_qty_retur,
                    rtrtbitem_expired_retur,
                    rtrtbitem_remarks_retur,
                    rtrtbflag_tp,
                    flag,
                    created_date,
                    created_by) values ";
             
                  foreach($_POST['id_barang'] as $option => $opt){
                    
                        if($_POST['expired_date'][$option] == ''){
                          $fixexpired = 'NULL';
                        }else{
                          $fixexpired ="'".$_POST['expired_date'][$option]."'";
                        }

                        if($_POST['input_tp'][$option] == ''){
                         $doktp = 'NULL';
                       }else{
                         $doktp ="'".$_POST['input_tp'][$option]."'";
                       }

                               $sqldetail .= "(
                               '".htmlspecialchars(addslashes(trim(strip_tags($id_tb))))."',
                               '".htmlspecialchars(addslashes(trim(strip_tags($_POST['detail_idtb'][$option]))))."',
                               '".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."',
                               '".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtyretur'][$option]))))."',
                               ".$fixexpired.",
                               '".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barangretur'][$option]))))."',
                               ".$doktp.",
                               ".$flagpengembalian.",
                               getdate(),
                               '$created_by')";
                               $sqldetail .= ",";

                  }
                  
                  // '".htmlspecialchars(addslashes(trim(strip_tags($_POST['kondisi'][$option]))))."',
                  // '".htmlspecialchars(addslashes(trim(strip_tags($_POST['expired_date'][$option]))))."',
             
                  $sqlfixdetail = rtrim($sqldetail,",");
                  $stmt1 = sqlsrv_query($conn,$sqlfixdetail);

             $sqllog = "INSERT  INTO log_tb (
              log_idtb,
              log_flagretur,
              note_tb,
              created_date,
              created_by,
              log_returdate
              ) VALUES (
               '$id_tb', 
               '$flagpengembalian',
               'RETUR REQUEST-".$note_request_approve."',
               getdate(), 
               '$created_by',
               '$tanggal_pengembalian'
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

                  $name  ='Info Pengembalian Permintaan Barang';
                  $email = 'info.voucherrequest@multirasa.co.id';
                  $subject =''.$status_request.' Permintaan Pengembalian Barang Dengan No #'.$code.'';
                  $body ='Dear Store '.$store_destination.' <br><br> 
                                Permintaan Barang <br>dengan No <b>'.$code.' </b> telah dilakukan pengembalian
                               Oleh Store '.$store_request.' 
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

        /* Free statement and connection resources. */  
        sqlsrv_free_stmt( $stmt);  
        sqlsrv_free_stmt( $stmt1);  
        sqlsrv_free_stmt( $stmt2);  
        sqlsrv_close( $conn);  

  
// }else if($selisi > 0){


// /* Initiate transaction. */  
// /* Exit script if transaction cannot be initiated. */  
// if ( sqlsrv_begin_transaction( $conn ) === false )  
// {  
//      echo "Could not begin transaction.\n";  
//      die( print_r( sqlsrv_errors(), true ));  
// }  

// $sqlheader = "UPDATE header_tb SET
//           reqtb_user_retur='".$status_request."',
//              reqtb_destination_retur_verifikasi='Pending',
//               reqtb_user_retur_date=getdate(),
//               reqtb_item_plus='".htmlspecialchars(addslashes(trim(strip_tags($pluscal))))."',
//               reqtb_item_minus='".htmlspecialchars(addslashes(trim(strip_tags($minus))))."',
//                reqtb_destination_retur_revisi_date=getdate(),
//                     reqtb_destination_retur_revisi_note='".htmlspecialchars(addslashes(trim(strip_tags($note_request_approve))))."',
//           status_progress=4
//           WHERE id_tb='$id_tb'";

              

//       $stmt = sqlsrv_query( $conn, $sqlheader);


//       $sqldetail = "";
//       foreach($_POST['id_barang'] as $option => $opt){

//         if($_POST['expired_date_retur_revisi'][$option] == ''){
//             $fixexpired = '(NULL)';
//           }else{
//             $fixexpired ="'".$_POST['expired_date_retur_revisi'][$option]."'";
//           }

//                           $sqldetail .= "UPDATE detail_tb SET 
//                            tbitem_qty_retur_revisi_plus='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtyrevisireturplus'][$option]))))."',
//                             tbitem_qty_retur_revisi_minus='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtyrevisireturminus'][$option]))))."',
//                             tbitem_expired_retur_revisi=$fixexpired,
//                             tbitem_remarks_retur_revisi='".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barangverifikasi'][$option]))))."',
//                           update_date=getdate()
//                           WHERE
//                           header_idtb='$id_tb'
//                            and tbitem_id='".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."'";
//                           $sqldetail .= ";";

//       }

//       $sqldetailfix = rtrim($sqldetail,";");
//       $stmt1 = sqlsrv_query($conn,$sqldetailfix);

//      $sqllog = "INSERT  INTO log_tb (
//       log_idtb,
//       note_tb,
//       created_date,
//       created_by
//       ) VALUES (
//        '$id_tb', 
//        'REVISI RETUR REQUEST-".htmlspecialchars(addslashes(trim(strip_tags($note_request_approve))))."',
//        getdate(), 
//        '$created_by'
//         )";

//       // $paramslog = array(
//       //       $lastinsertid,
//       //       $created_by
//       // );
//       // $stmt2 = sqlsrv_query( $conn, $sqllog, $paramslog);

//       $stmt2 = sqlsrv_query( $conn, $sqllog);

//      if( $stmt && $stmt1 && $stmt2 )  
//      {  
//           sqlsrv_commit($conn);  

//           $cc="angga.aditya@multirasa.co.id";

//           $name  ='Info Pengembalian Permintaan Barang';
//           $email = 'info.voucherrequest@multirasa.co.id';
//           $subject =''.$status_request.' Request Revisi Pengembalian Barang Dengan No #'.$code.'';
//           $body ='Dear Store '.$store_destination.' <br><br> 
//                        Request Revisi Pengembalian Barang <br>dengan No Request  <b>'.$code.' </b> telah dilakukan revisi, silahkan cek sistem untuk melihat revisi pengembalian barang Oleh Store '.$store_request.' 
//                        <br><br>   
//                        Note : <br> 
//                        '.$note_request_approve.'
//                        <br><br><br> 
//                        Terimakasih.';

//           $mail = new PHPMailer();

//           //SMtb Settings
//           $mail->isSMTP();
//           $mail->Host = "mail.multirasa.co.id";
//           $mail->SMTPAuth = true;
//           $mail->Username = "info.voucherrequest@multirasa.co.id"; //enter you email address
//           $mail->Password = 'yoshimulti'; //enter you email password
//           $mail->Port = 465;
//           $mail->SMTPSecure = "ssl";
  
//           //Email Settings
//           $mail->isHTML(true);
//           $mail->setFrom($email, $name);
//           $mail->addAddress($emailstoredestination); 
//           // if($status_request =='Approved'){
//           //   $emailcc =  $mail->addCC($cc);
//           // }else{
//           //   $emailcc ='';
//           // }//enter you email address
//           $mail->Subject = ("$subject");
//           $mail->Body = $body;

//           if ($mail->send()) {
//               $status = "success";
//               $response = "Email is sent!";
//           } else {
//               $status = "failed";
//               $response = "Something is wrong: <br><br>" . $mail->ErrorInfo;
//           }
//           $_SESSION['pesan'] = '<b>Permintaan Berhasil Di Proses!</b>';
//           // header('Location: listrequesttbs.php');
//           echo $sqlheader."\n";  
//           echo $sqldetail."\n";  
//           echo $sqllog."\n";  
//           echo "Transaction was committed.\n";  
//      }  
//      else  
//      {  
//           sqlsrv_rollback($conn);  
//           $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
//           // header('Location: listrequesttbs.php');
//           echo "Transaction was rolled back.\n"; 
//           echo $sqlheader."\n";  
//           echo $sqldetail."\n";  
//           echo $sqllog."\n";  
//      } 

// /* Free statement and connection resources. */  
// sqlsrv_free_stmt( $stmt);  
// sqlsrv_free_stmt( $stmt1);  
// sqlsrv_free_stmt( $stmt2);  
// sqlsrv_close( $conn); 

// 
}else{

}

?>