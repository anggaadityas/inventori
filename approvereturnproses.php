<?php
include "db.php";
error_reporting(0);

use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

$id = $_POST['id_rtn'];
$status = $_POST['status'];
$code = $_POST['reqrtn_code'];
$store = $_POST['reqrtn_user'];
$divisi = $_POST['reqrtn_destination'];
$delivery = $_POST['reqrtn_code_date'];
$rev_date_req = $_POST['rev_date_req'];
$rev_question = $_POST['rev_question'];
$note = $_POST['note_request_verifkasi'];
$created_by =  $_SESSION['nama'];
$tipe = $_POST['reqrtn_type_req'];

   if($rev_question == 1){
      $fixreqrtn_date ="'".$rev_date_req."'";
      $fixreqrtn_dateemail =$rev_date_req;
       $fixreqrtn_past_date ="'".$delivery."'";
      $fixnotiftanggalkirim ='Ada Perubahan Tanggal Pengiriman Retur Barang :<br>
       &nbsp;&nbsp;&nbsp;Sebelumnya : '.$delivery.'<br> &nbsp;&nbsp; Menjadi : '.$rev_date_req.'';
   }else{
      $fixreqrtn_date ="'".$delivery."'";
      $fixreqrtn_dateemail =$delivery;
       $fixreqrtn_past_date ='(NULL)';
      $fixnotiftanggalkirim ='  Tanggal Pengiriman Retur Barang : '.$delivery.'';
   }

   foreach($_POST['status'] as $option => $opt){
    $b[]=$_POST['status'][$option];
    }

   if (in_array("0", array_merge($b)))
     {
        $status ="Approved";
        $fixstatus ="Disetujui";
     }
   else
     {
        $status ="Reject";
        $fixstatus ="Tidak Disetujui";
     }

    foreach($_POST['cat'] as $option1 => $opt1){
        if($_POST['status'][$option1] == 0){
            $c[]=$_POST['cat'][$option1];
        }
    }

   if (in_array("FOOD", array_merge($c)))
     {
       $emailqc ="";  
     }
   else
     {
        $emailqc ="";
     }



/* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  

if($status =="Approved"){


 $sqlheader = "UPDATE header_returnck SET
          reqrtn_ck_approve='".htmlspecialchars(addslashes(trim(strip_tags($status))))."',
              reqrtn_ck_approve_date=getdate(),
              reqrtn_ck_approve_note ='".htmlspecialchars(addslashes(trim(strip_tags($note))))."',
              reqrtn_date=$fixreqrtn_date,
              reqrtn_req_past_date=$fixreqrtn_past_date,
              reqrtn_destination_approve='On Progress',
              reqrtn_destination_approve_date =(NULL),
              reqrtn_destination_approve_note=(NULL),
              reqrtn_nodoc_sap=(NULL),
              reqrtn_nodoc_sap_posting_date=(NULL),
              reqrtn_nodoc_sap_date=(NULL),
              status_progress=2
          WHERE id_rtn='$id'";
          $stmt = sqlsrv_query( $conn, $sqlheader);


}else{

 $sqlheader = "UPDATE header_returnck SET
 reqrtn_ck_approve='".htmlspecialchars(addslashes(trim(strip_tags($status))))."',
     reqrtn_ck_approve_date=getdate(),
     reqrtn_ck_approve_note ='".htmlspecialchars(addslashes(trim(strip_tags($note))))."',
     reqrtn_destination_approve=(NULL),
     reqrtn_destination_approve_date =(NULL),
     reqrtn_destination_approve_note=(NULL),
     reqrtn_nodoc_sap=(NULL),
     reqrtn_nodoc_sap_posting_date=(NULL),
     reqrtn_nodoc_sap_date=(NULL),
     status_progress=4
 WHERE id_rtn='$id'";

$stmt = sqlsrv_query( $conn, $sqlheader);

}

$sqldetail = "";
foreach($_POST['cat'] as $option => $opt){

                    $sqldetail .= "UPDATE detail_returnck SET 
                    rtnitem_status_approve='".htmlspecialchars(addslashes(trim(strip_tags($_POST['status'][$option]))))."',
                     rtnitem_remarks_approve='".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan'][$option]))))."',
                     update_date=getdate()
                    WHERE
                    header_idrtn='$id'
                     and rtnitem_cat='".htmlspecialchars(addslashes(trim(strip_tags($_POST['cat'][$option]))))."'";
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
     '$id', 
     '".strtoupper($status)." REQUEST-".$note."',
     getdate(), 
     '$created_by'
      )";

    // $paramslog = array(
    //       $lastinsertid,
    //       $created_by
    // );
    // $stmt2 = sqlsrv_query( $conn, $sqllog, $paramslog);

    $stmt2 = sqlsrv_query( $conn, $sqllog);

if( $stmt &&  $stmt1 &&  $stmt2  )  
{  
 sqlsrv_commit($conn);  
 $servername = "localhost";
 $username = "root";
 $password = "aas260993";
 $dbname = "voucher_trial";
 $conn1 = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());


 $tsql =  "SELECT * FROM mst_user a inner join mst_divisi b on a.div_id=b.id_divisi where nama= '$divisi'";  
 $stmt = mysqli_query($conn1,$tsql);
 $divisi1 =mysqli_fetch_array($stmt);
 $rowdivisi = $divisi1['inisial_divisi'];
 $rowemaildivisi = $divisi1['email'];

 $tsql1 = "SELECT * FROM mst_user where nama= '$store'";   
 $stmt1 = mysqli_query($conn1,$tsql1);
 $resultstore =mysqli_fetch_array($stmt1);
 $emailstore = $resultstore['email'];
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
 ELSE ''
 END as CK,
 CASE
 WHEN area=1 THEN 'angga.aditya@multirasa.co.id'
 WHEN area=2 THEN 'angga.aditya@multirasa.co.id'
 ELSE ''
 END as emailck from storesett where storeCode='$store'";
 $stmtck = sqlsrv_query( $connHO, $sqlck );
 if( $stmtck === false) {
     die( print_r( sqlsrv_errors(), true) );
 }
 $rowck = sqlsrv_fetch_array( $stmtck, SQLSRV_FETCH_ASSOC);
 $CK= $rowck['CK'];
 $emailck= $rowck['emailck'];

  if($status =='Approved'){

     
  $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,126) expired,convert(char(10),rtnitem_arrival,126) arrival FROM detail_returnck where header_idrtn='$id' order by rtnitem_status_approve asc";
  $stmtdetail = sqlsrv_query( $conn, $sqldetail );
  if( $stmtdetail === false) {
      die( print_r( sqlsrv_errors(), true) );
  }
  $no=0;
  $laporan="<h4><b>Data Retur Barang Store</b></h4>";
  $laporan .="<br/>";
  $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
  $laporan .="<tr style=\"bgcolor: blue;\">";
  $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Barang Bagus</td><td>Qty Barang Tidak Bagus</td><td>Kadaluarsa</td><td>Kedatangan Barang</td><td>Status Approve</td><td>Remarks Approve</td>";
  $laporan .="</tr>";

 while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
  {
    if($rowdetail['rtnitem_status_approve'] == 0){
        $statusitem ='Approve';
    }else{
      $statusitem ='Not Approve';
    }
    
    $fixqtyver =0;
      $laporan .="<tr>";
      $laporan .="<td>".$rowdetail['rtnitem_code']."</td><td>".$rowdetail['rtnitem_name']."</td><td>".$rowdetail['rtnitem_uom']."</td><td>".$rowdetail['rtnitem_cat']."</td><td>".$rowdetail['rtnitem_reason']."</td><td>".number_format($rowdetail['rtnitem_qty_good'],2,'.',',')."</td><td>".number_format($rowdetail['rtnitem_qty_not_good'],2,'.',',')."</td><td>".$rowdetail['expired']."</td><td>".$rowdetail['arrival']."</td><td>".$statusitem."</td><td>".$rowdetail['rtnitem_remarks_approve']."</td>";
      $laporan .="</tr>";
  }
  $laporan .="</table>";

   $name  ='Info Persetujuan Permintaan Retur Barang';
   $email = 'info.voucherrequest@multirasa.co.id';
   // $subject ='Request Retur Barang Dengan No #'.$code.'';
   $subject =''.$store.' - Persetujuan Retur Barang Dengan No #'.$code.' - '.$fixreqrtn_dateemail.'';
   $body ='Dear '.$store.' <br><br> 
                 Permintaan Retur Barang, <br>dengan No Request  <b>'.$code.' </b>, telah disetujui oleh team distribusi
                 <br><br> 
                '.$fixnotiftanggalkirim .'
                 <br><br>   
                 '.$laporan.'
                 <br><br>   
                 Note : <br>
                '.$note.'
                <br><br><br> 
                Terimakasih.';

  }else{

   $name  ='Info Ditolak Permintaan Retur Barang';
   $email = 'info.voucherrequest@multirasa.co.id';
   // $subject ='Request Retur Barang Dengan No #'.$code.'';
   $subject =''.$store.' - Ditolak Retur Barang Dengan No #'.$code.' - '.$delivery.'';
   $body ='Dear '.$store.' <br><br> 
                 Permintaan Retur Barang, <br>dengan No Request  <b>'.$code.' </b>, telah ditolak oleh team distribusi
                 <br><br>    
                Note : <br> 
                '.$note.'
                <br><br><br> 
                Terimakasih.';

  }



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
 $mail->addAddress($emailstore); //enter you email address\
                 if($rowdivisi == 'IT'){
                   if($rowck['CK'] =='CK JAKARTA'){
                   $to='ridwan.anas@multirasa.co.id';
                   $cc ='yoshinoya.it@multirasa.co.id';
                   $mail->addAddress($to);
                   $mail->addCC($cc);
                 }else if($rowck['CK'] =='CK SURABAYA'){
                    $to ='wrida.wardana@multirasa.co.id';
                    $cc ='yoshinoya.it@multirasa.co.id';
                   $mail->addAddress($to);
                   $mail->addCC($cc);
                 }
                }else if($rowdivisi == 'CK'){
                   if($rowck['CK'] =='CK JAKARTA'){
                    $to ='wh.distribusi.jkt@multirasa.co.id';
                     // $cc='wh.inv.jkt@multirasa.co.id';
                     // $cc1 ='gabriella.tardini@multirasa.co.id';
                     // $cc2 ='alexius.sugeng@multirasa.co.id';
                     $cc='yoshinoya.retur.jkt@multirasa.co.id';
                     $ccqa='staff.qa@multirasa.co.id';
                     $mail->addAddress($to);
                     $mail->addCC($cc);
                     if($tipe == 4 || $tipe == 1){
                      $mail->addCC($ccqa);
                     }else{

                     }
                   }else if($rowck['CK'] =='CK SURABAYA'){
                    $to1 ='ck.admin.sby2@multirasa.co.id';
                     // $cc1 ='henri.hakim@multirasa.co.id';
                     // $cc2 ='januar.kusriwahjudi@multirasa.co.id';
                     $cc='yoshinoya.retur.sby@multirasa.co.id';
                     $ccqa='ncr.store.ckrk@multirasa.co.id';
                     $mail->addAddress($to1);
                     $mail->addCC($cc);
                     if($tipe == 4 ||$tipe == 1 ){
                      $mail->addCC($ccqa);
                     }else{

                     }
                    }
                }else if($rowdivisi == 'ENG'){
                   if($rowck['CK'] =='CK JAKARTA'){
                   $to='stockkeeper.engjkt@multirasa.co.id';
                   $cc ='stockkeeper.engpu@multirasa.co.id';
                   $mail->addAddress($to);
                   $mail->addCC($cc);
                 }else if($rowck['CK'] =='CK SURABAYA'){
                    $to ='stockkeeper.engsby@multirasa.co.id';
                   $mail->addAddress($to);
                   $mail->addCC($cc);
                 }
                }else if($rowdivisi == 'GA'){
                   if($rowck['CK'] =='CK JAKARTA'){
                   $to='siti.nurlaela@multirasa.co.id';
                   $cc='nia.fitriana@multirasa.co.id';
                   $mail->addAddress($to);
                   $mail->addCC($cc);
                 }else if($rowck['CK'] =='CK SURABAYA'){
                    $to ='siti.nurlaela@multirasa.co.id';
                    $cc='nia.fitriana@multirasa.co.id';
                    $cc1='aditya.riezky@multirasa.co.id';
                   $mail->addAddress($to);
                   $mail->addCC($cc);
                   $mail->addCC($cc1);
                 }
                }else{

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
 echo "No Dokumen Berhasil Di Proses";
}else{
 echo "No Dokumen Gagal Di Proses";
}

?>