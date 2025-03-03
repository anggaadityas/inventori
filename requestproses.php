<?php
error_reporting(0);
include "db.php";
if(!isset($_SESSION['uid']) || !isset($_SESSION['nama_divisi'])){   
     header('Location: index.php');
     exit;
     }

use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

function tgl_indo($tanggal){
  $bulan = array (
    1 =>   'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
  );
  $pecahkan = explode('-', $tanggal); 
  return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}

$tanggal_permintaan = $_POST['tanggal_permintaan'];
$jenis_permintaan = $_POST['jenis_permintaan'];
$jenis_barang = $_POST['jenis_barang'];
$store_request =   $_SESSION['nama']; 
$tipe = $_POST['tipe'];
$pickuptype= $_POST['pickup_type'];
$jenis_prioritas = $_POST['jenis_prioritas'];
$flag_wadah =  $_SESSION["wadah_flag"];
$nosjt =  $_POST["nosjt"];
$no_ireap =  $_POST["no_ireap"];
$nopica =  $_POST["nopica"];
$alasan = $_POST['reason_retur_eng'];

if($nosjt == ''){
  $fixnosjt ='NULL';
}else{
  $fixnosjt = "'".$_POST['nosjt']."'";
}

if($nopica == ''){
  $fixnopica ='NULL';
}else{
  $fixnopica = "'".$_POST['nopica']."'";
}

if($alasan == ''){
  $fixalasan ='NULL';
}else{
  $fixalasan = "'".$_POST['reason_retur_eng']."'";
}

if($jenis_permintaan == 2){

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
     END as emailck from storesett where storeCode='$store_request'";
     $stmtck = sqlsrv_query( $connHO, $sqlck );
     if( $stmtck === false) {
         die( print_r( sqlsrv_errors(), true) );
     }
     $rowck = sqlsrv_fetch_array( $stmtck, SQLSRV_FETCH_ASSOC);
     $CK= $rowck['CK'];
     $emailck= $rowck['emailck'];
     $ck_destination = $CK;
     $store_destination = $_POST['divisi'];

}else{  
$store_destination = $_POST['store'];
}

$keterangan = $_POST['keterangan'];
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



if($jenis_permintaan == 1){
          $bulan = date('m');
          $tahun = date ('Y');
          // $query = "SELECT MAX(uniqueorder) AS maxKode FROM order_voucher WHERE YEAR(date_order)='$tahun' and MONTH(date_order) = '$bulan'";
          $sqlcode="SELECT MAX(reqtp_code) AS maxKode FROM header_tp
                    WHERE YEAR(created_date)='$tahun' and MONTH(created_date) = '$bulan' and  reqtp_user= '".$created_by."' and reqtp_type=1";
          $hasilcode = sqlsrv_query( $conn, $sqlcode );
          $rowcode = sqlsrv_fetch_array( $hasilcode, SQLSRV_FETCH_ASSOC);

          $noUrut= $rowcode["maxKode"] + 1;
          $kode =  sprintf("%03s", $noUrut);
          $nomor = "/".$created_by."/TPS/".$bulan."/".$tahun;
          $code = $kode.$nomor;
}else if ($jenis_permintaan == 2){
          $bulan = date('m');
          $tahun = date ('Y');
          // $query = "SELECT MAX(uniqueorder) AS maxKode FROM order_voucher WHERE YEAR(date_order)='$tahun' and MONTH(date_order) = '$bulan'";
          $sqlcode="SELECT MAX(reqrtn_code) AS maxKode FROM header_returnck
                    WHERE YEAR(created_date)='$tahun' and MONTH(created_date) = '$bulan' and  reqrtn_user= '".$created_by."'";
          $hasilcode = sqlsrv_query( $conn, $sqlcode );
          $rowcode = sqlsrv_fetch_array( $hasilcode, SQLSRV_FETCH_ASSOC);
          
          $noUrut= $rowcode["maxKode"] + 1;
          $kode =  sprintf("%03s", $noUrut);
          $nomor = "/".$created_by."/RTR/".$bulan."/".$tahun;
          $code = $kode.$nomor;
}else if($jenis_permintaan == 3){
     $bulan = date('m');
     $tahun = date ('Y');
     // $query = "SELECT MAX(uniqueorder) AS maxKode FROM order_voucher WHERE YEAR(date_order)='$tahun' and MONTH(date_order) = '$bulan'";
     $sqlcode="SELECT MAX(reqtb_code) AS maxKode FROM header_tb
               WHERE YEAR(created_date)='$tahun' and MONTH(created_date) = '$bulan' and  reqtb_user= '".$created_by."'";
     $hasilcode = sqlsrv_query( $conn, $sqlcode );
     $rowcode = sqlsrv_fetch_array( $hasilcode, SQLSRV_FETCH_ASSOC);
     
     $noUrut= $rowcode["maxKode"] + 1;
     $kode =  sprintf("%03s", $noUrut);
     $nomor = "/".$created_by."/TBS/".$bulan."/".$tahun;
     $code = $kode.$nomor;
}else{
     $code ="'";
}

if($jenis_permintaan == 1){

/* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  

$sqlheader = "INSERT  INTO header_tp (
              reqtp_code,
              reqtp_date,
              reqtp_type,
              reqtp_user,
              reqtp_destination,
              reqtp_note,
              reqtp_destination_approve,
              status_progress,
              created_date,
              created_by
              ) VALUES (
              '".htmlspecialchars(addslashes(trim(strip_tags($code))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($tanggal_permintaan))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($jenis_permintaan))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($store_request))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($store_destination))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($keterangan))))."', 
              'On Progress',
              1,
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
      $next_result = sqlsrv_next_result($stmt); 
      $row = sqlsrv_fetch_array($stmt); 

      $lastinsertid = $row['id'];

$sqldetail = "INSERT INTO detail_tp  (
       header_idtp,
       tpitem_id,
       tpitem_code,
       tpitem_name,
       tpitem_uom,
       tpitem_cat,
       tpitem_reason,
       tpitem_qty,
       tpitem_remarks,
       created_date,
       created_by) values ";

     foreach($_POST['id_barang'] as $option => $opt){
                  $sqldetail .= "(
                  '$lastinsertid',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['kode_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['nama_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['uom'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['jenisbarang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['alasan'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['qty'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barang'][$option]))))."',
                  getdate(),
                  '$created_by')";
                  $sqldetail .= ",";
     }
     
     // '".htmlspecialchars(addslashes(trim(strip_tags($_POST['kondisi'][$option]))))."',
     // '".htmlspecialchars(addslashes(trim(strip_tags($_POST['expired_date'][$option]))))."',

     $sqlfixdetail = rtrim($sqldetail,",");
     $stmt1 = sqlsrv_query($conn,$sqlfixdetail);

     $sqllog = "INSERT  INTO log_tp (
      log_idtp,
      note_tp,
      created_date,
      created_by
      ) VALUES (
       '$lastinsertid', 
       'CREATE REQUEST',
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

          $name  ='Info Permintaan Barang Transfer Putus Store';
          $email = 'info.voucherrequest@multirasa.co.id';
          $subject ='Permintaan Barang Dengan No #'.$code.'';
          $body ='Dear Store '.$store_destination.' <br><br> 
                       Mohon persetujuannya untuk Permintaan Barang Dari Store '.$store_request.', dengan No  <b>'.$code.' </b>
                       <br><br>   
                       Note : <br> 
                       '.$keterangan.'
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
          $mail->addAddress($emailstoredestination); //enter you email address
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
          header('Location: tps.php');
     }  
     else  
     {  
          sqlsrv_rollback($conn);  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          header('Location: tps.php');
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

}else if($jenis_permintaan == 2){

     if($pickuptype == 1){


if($tipe == 3){

if($jenis_prioritas == 3){

/* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  

$sqlheader = "INSERT  INTO header_returnck (
              reqrtn_code,
              reqrtn_date,
              reqrtn_pickup_type,
              reqrtn_type,
              reqrtn_type_req,
              reqrtn_type_prioritas,
              reqrtn_user,
              reqrtn_ck,
              reqrtn_destination,
              reqrtn_reason,
              reqrtn_note,
              reqrtn_ck_approve,
              reqrtn_ck_approve_date,
              reqrtn_ck_approve_note,
              reqrtn_destination_approve,
              status_progress,
              created_date,
              created_by
              ) VALUES (
              '".htmlspecialchars(addslashes(trim(strip_tags($code))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($tanggal_permintaan))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($pickuptype))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($jenis_permintaan))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($tipe))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($jenis_prioritas))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($store_request))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($ck_destination))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($store_destination))))."', 
              ".$fixalasan.", 
              '".htmlspecialchars(addslashes(trim(strip_tags($keterangan))))."', 
              'Approved',
               getdate(),
              'Approved by Sistem',
              'On Progress',
              2,
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
      $next_result = sqlsrv_next_result($stmt); 
      $row = sqlsrv_fetch_array($stmt); 

      $lastinsertid = $row['id'];

$sqldetail = "INSERT INTO detail_returnck  (
       header_idrtn,
       rtnitem_id,
       rtnitem_code,
       rtnitem_name,
       rtnitem_uom,
       rtnitem_cat,
       rtnitem_reason,
       rtnitem_qty_good,
       rtnitem_qty_not_good,
       rtnitem_expired,
       rtnitem_arrival,
       rtnitem_remarks,
       rtnitem_status_approve,
       rtnitem_remarks_approve,
       created_date,
       created_by) values ";

     foreach($_POST['id_barang'] as $option => $opt){

          if($_POST['expired_date'][$option] ==''){
               $fixexpired='NULL';
          }else{
               $fixexpired= "'".$_POST['expired_date'][$option]."'";
          }

          if($_POST['qty_good'][$option] ==''){
               $fixqty_good='NULL';
          }else{
               $fixqty_good= "'".$_POST['qty_good'][$option]."'";
          }

          if($_POST['qty_notgood'][$option] ==''){
               $fixqty_notgood='NULL';
          }else{
               $fixqty_notgood= "'".$_POST['qty_notgood'][$option]."'";
          }

          if($_POST['arrival_date'][$option] ==''){
               $fixarrival='NULL';
          }else{
               $fixarrival= "'".$_POST['arrival_date'][$option]."'";
          }

                  $sqldetail .= "(
                  '$lastinsertid',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['kode_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['nama_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['uom'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['jenis_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['alasan'][$option]))))."',
                 ".$fixqty_good.",
                 ".$fixqty_notgood.",
                  ".$fixexpired.",
                  ".$fixarrival.",
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barang'][$option]))))."',
                  '0',
                  'Auto Approve',
                  getdate(),
                  '$created_by')";
                  $sqldetail .= ",";
     }

     $sqlfixdetail = rtrim($sqldetail,",");
     $stmt1 = sqlsrv_query($conn,$sqlfixdetail);

     $sqllog = "INSERT  INTO log_return (
      log_idrtn,
      note_rtn,
      created_date,
      created_by
      ) VALUES (
       '$lastinsertid', 
       'CREATE REQUEST',
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

          $servername = "192.168.2.136";
          $username = "root";
          $password = "aas260993";
          $dbname = "voucher_trial";
          $conn1 = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());

           $tsql = "SELECT * FROM mst_user where nama= '$store_destination'";   
           $stmt = mysqli_query($conn1,$tsql);
           $user =mysqli_fetch_array($stmt);
           // $emaildivisi = 'angga.aditya@multirasa.co.id';

          //  $tsql1 = "SELECT * FROM mst_user where nama= '$store_request'";   
          //  $stmt1 = mysqli_query($conn1,$tsql1);
          //  $user1 =mysqli_fetch_array($stmt1);
          //  $ckarea = $user1['area_ck'];
          //  if($ckarea == 1){
          //       $emailck ='angga.aditya@multirasa.co.id';
          //  }else if($ckarea == 2){
          //      $emailck ='ck.sby@multirasa.co.id';
          //  }else{
          //      $emailck ='';
          //  }

          $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,126) expired,convert(char(10),rtnitem_arrival,126) arrival FROM detail_returnck where header_idrtn='$lastinsertid'";
          $stmtdetail = sqlsrv_query( $conn, $sqldetail );
          if( $stmtdetail === false) {
              die( print_r( sqlsrv_errors(), true) );
          }
          $no=0;

          $laporan="<h4><b>Data Retur Barang Store</b></h4>";
          $laporan .="<br/>";
          $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
          $laporan .="<tr style=\"bgcolor: blue;\">";
          $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Barang Bagus</td><td>Qty Barang Tidak Bagus</td><td>Kadaluarsa</td><td>Kedatangan Barang</td>";
          $laporan .="</tr>";
        
         while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
          {
               if($rowdetail['sap_flag'] == 1){
                    $color ="style='background-color: coral;'";
               }else{
                    $color ='';
               }    
            $fixqtyver =0;
              $laporan .="<tr>";
              $laporan .="<td ".$color.">".$rowdetail['rtnitem_code']."</td><td>".$rowdetail['rtnitem_name']."</td><td>".$rowdetail['rtnitem_uom']."</td><td>".$rowdetail['rtnitem_cat']."</td><td>".$rowdetail['rtnitem_reason']."</td><td>".number_format($rowdetail['rtnitem_qty_good'],2,'.',',')."</td><td>".number_format($rowdetail['rtnitem_qty_not_good'],2,'.',',')."</td><td>".$rowdetail['expired']."</td><td>".$rowdetail['arrival']."</td>";
              $laporan .="</tr>";
          }
          $laporan .="</table>";

          $name  ='Info Rencana Retur Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          // $subject ='Request Retur Barang Dengan No #'.$code.'';
          $subject =''.$store_request.' - Rencana Retur Barang Dengan No #'.$code.' - '.$tanggal_permintaan.'';
          $body ='Dear '.$CK.' <br><br> 
                       Ada Retur Barang Dari Store '.$store_request.', <br>dengan No Request  <b>'.$code.' </b>
                       <br><br> 
                       Tanggal Pengiriman Retur Barang : '.$tanggal_permintaan.'
                       <br><br>   
                       '.$laporan.'
                       <br><br>   
                       Note : <br> 
                       '.$keterangan.'
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
          // $to ='angga.aditya@multirasa.co.id';
          $to ='wh.distribusi.jkt@multirasa.co.id';
          $to1 ='ck.admin.sby2@multirasa.co.id';
          if($rowck['CK'] =='CK JAKARTA'){
               // $cc='wh.inv.jkt@multirasa.co.id';
               // $cc1 ='gabriella.tardini@multirasa.co.id';
               // $cc2 ='alexius.sugeng@multirasa.co.id';
               $cc='yoshinoya.retur.jkt@multirasa.co.id';
               $mail->addAddress($to);
               $mail->addCC($cc);
             }else if($rowck['CK'] =='CK SURABAYA'){
               // $cc1 ='henri.hakim@multirasa.co.id';
               // $cc2 ='januar.kusriwahjudi@multirasa.co.id';
               $cc='yoshinoya.retur.sby@multirasa.co.id';
               $mail->addAddress($to1);
               $mail->addCC($cc);
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
          $_SESSION['pesan'] = '<b>Permintaan Berhasil Di Proses!</b>';
          header('Location: retur.php');
     }  
     else  
     {  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          header('Location: retur.php');
          sqlsrv_rollback($conn);  
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

$sqlheader = "INSERT  INTO header_returnck (
              reqrtn_code,
              reqrtn_date,
              reqrtn_pickup_type,
              reqrtn_type,
              reqrtn_type_req,
              reqrtn_type_prioritas,
              reqrtn_user,
              reqrtn_ck,
              reqrtn_destination,
              reqrtn_reason,
              reqrtn_note,
              reqrtn_ck_approve,
              reqrtn_nodoc_sap,
              reqrtn_nopica,
              status_progress,
              created_date,
              created_by
              ) VALUES (
              '".htmlspecialchars(addslashes(trim(strip_tags($code))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($tanggal_permintaan))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($pickuptype))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($jenis_permintaan))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($tipe))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($jenis_prioritas))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($store_request))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($ck_destination))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($store_destination))))."', 
              ".$fixalasan.", 
              '".htmlspecialchars(addslashes(trim(strip_tags($keterangan))))."', 
              'On Progress',
              ".$fixnosjt.", 
              ".$fixnopica.", 
              1,
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
      $next_result = sqlsrv_next_result($stmt); 
      $row = sqlsrv_fetch_array($stmt); 

      $lastinsertid = $row['id'];

$sqldetail = "INSERT INTO detail_returnck  (
       header_idrtn,
       rtnitem_id,
       rtnitem_code,
       rtnitem_name,
       rtnitem_uom,
       rtnitem_cat,
       rtnitem_reason,
       rtnitem_qty_good,
       rtnitem_qty_not_good,
       rtnitem_expired,
       rtnitem_arrival,
       rtnitem_remarks,
       created_date,
       created_by) values ";

     foreach($_POST['id_barang'] as $option => $opt){

          if($_POST['expired_date'][$option] ==''){
               $fixexpired='NULL';
          }else{
               $fixexpired= "'".$_POST['expired_date'][$option]."'";
          }

          if($_POST['qty_good'][$option] ==''){
               $fixqty_good='NULL';
          }else{
               $fixqty_good= "'".$_POST['qty_good'][$option]."'";
          }

          if($_POST['qty_notgood'][$option] ==''){
               $fixqty_notgood='NULL';
          }else{
               $fixqty_notgood= "'".$_POST['qty_notgood'][$option]."'";
          }

          if($_POST['arrival_date'][$option] ==''){
               $fixarrival='NULL';
          }else{
               $fixarrival= "'".$_POST['arrival_date'][$option]."'";
          }

                  $sqldetail .= "(
                  '$lastinsertid',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['kode_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['nama_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['uom'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['jenis_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['alasan'][$option]))))."',
                 ".$fixqty_good.",
                 ".$fixqty_notgood.",
                  ".$fixexpired.",
                  ".$fixarrival.",
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barang'][$option]))))."',
                  getdate(),
                  '$created_by')";
                  $sqldetail .= ",";
     }

     $sqlfixdetail = rtrim($sqldetail,",");
     $stmt1 = sqlsrv_query($conn,$sqlfixdetail);

     $sqllog = "INSERT  INTO log_return (
      log_idrtn,
      note_rtn,
      created_date,
      created_by
      ) VALUES (
       '$lastinsertid', 
       'CREATE REQUEST',
       getdate(), 
       '$created_by'
        )";

      // $paramslog = array(
      //       $lastinsertid,
      //       $created_by
      // );
      // $stmt2 = sqlsrv_query( $conn, $sqllog, $paramslog);

      $stmt2 = sqlsrv_query( $conn, $sqllog);

                  // File upload configuration 
    $targetDir = "dokumen/"; 
    $allowTypes = array('jpg','png','jpeg','gif','JPG','pdf'); 
     
    $statusMsg = $errorMsg = $insertValuesSQL = $errorUpload = $errorUploadType = ''; 
    $fileNames = array_filter($_FILES['dokumensjt']['name']); 
    if(!empty($fileNames)){ 
        foreach($_FILES['dokumensjt']['name'] as $key=>$val){ 
            // File upload path 
            $fileName = basename($_FILES['dokumensjt']['name'][$key]); 
            $targetFilePath = $targetDir . $fileName; 
             
            // Check whether file type is valid 
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
            if(in_array($fileType, $allowTypes)){ 
                // Upload file to server 
                if(move_uploaded_file($_FILES["dokumensjt"]["tmp_name"][$key], $targetFilePath)){ 
                    // Image db insert sql 
                    $insertValuesSQL .= "('$lastinsertid','".$fileName."', getdate(),'$created_by'),"; 
                }else{ 
                    $errorUpload .= $_FILES['dokumensjt']['name'][$key].' | '; 
                } 
            }else{ 
                $errorUploadType .= $_FILES['dokumensjt']['name'][$key].' | '; 
            } 
        } 
         
        // Error message 
        $errorUpload = !empty($errorUpload)?'Upload Error: '.trim($errorUpload, ' | '):''; 
        $errorUploadType = !empty($errorUploadType)?'File Type Error: '.trim($errorUploadType, ' | '):''; 
        $errorMsg = !empty($errorUpload)?'<br/>'.$errorUpload.'<br/>'.$errorUploadType:'<br/>'.$errorUploadType; 
         
        if(!empty($insertValuesSQL)){ 
            $insertValuesSQL = trim($insertValuesSQL, ','); 
            // Insert image file name into database 
            $sqldokumen = "INSERT INTO retur_dokumen (reqrtn_id, nama_dokumen,created_date,created_by) VALUES $insertValuesSQL"; 
             $stmt3 = sqlsrv_query( $conn, $sqldokumen);
        }else{ 
            $statusMsg = "Upload failed! ".$errorMsg; 
        } 
    }else{ 
        $statusMsg = 'Please select a file to upload.'; 
    } 

    if($store_destination =='ENG JAKARTA' || $store_destination =='ENG SURABAYA' || $store_destination =='GA JAKARTA'){

     if( $stmt && $stmt1 && $stmt2 && $stmt3 )  
     {  
          sqlsrv_commit($conn);  

          $servername = "192.168.2.136";
          $username = "root";
          $password = "aas260993";
          $dbname = "voucher_trial";
          $conn1 = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());

           $tsql = "SELECT * FROM mst_user where nama= '$store_destination'";   
           $stmt = mysqli_query($conn1,$tsql);
           $user =mysqli_fetch_array($stmt);
           $emaildivisi = 'angga.aditya@multirasa.co.id';

          //  $tsql1 = "SELECT * FROM mst_user where nama= '$store_request'";   
          //  $stmt1 = mysqli_query($conn1,$tsql1);
          //  $user1 =mysqli_fetch_array($stmt1);
          //  $ckarea = $user1['area_ck'];
          //  if($ckarea == 1){
          //       $emailck ='angga.aditya@multirasa.co.id';
          //  }else if($ckarea == 2){
          //      $emailck ='ck.sby@multirasa.co.id';
          //  }else{
          //      $emailck ='';
          //  }

          $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,126) expired,convert(char(10),rtnitem_arrival,126) arrival FROM detail_returnck where header_idrtn='$lastinsertid'";
          $stmtdetail = sqlsrv_query( $conn, $sqldetail );
          if( $stmtdetail === false) {
              die( print_r( sqlsrv_errors(), true) );
          }
          $no=0;

          $laporan="<h4><b>Data Retur Barang Store</b></h4>";
          $laporan .="<br/>";
          $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
          $laporan .="<tr style=\"bgcolor: blue;\">";
          $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Kirim</td><td>Kadaluarsa</td><td>Kedatangan Barang</td>";
          $laporan .="</tr>";
        
         while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
          {
               if($rowdetail['sap_flag'] == 1){
                    $color ="style='background-color: coral;'";
               }else{
                    $color ='';
               }    
            $fixqtyver =0;
              $laporan .="<tr>";
              $laporan .="<td ".$color.">".$rowdetail['rtnitem_code']."</td><td>".$rowdetail['rtnitem_name']."</td><td>".$rowdetail['rtnitem_uom']."</td><td>".$rowdetail['rtnitem_cat']."</td><td>".$rowdetail['rtnitem_reason']."</td><td>".number_format($rowdetail['rtnitem_qty_not_good'],2,'.',',')."</td><td>".$rowdetail['expired']."</td><td>".$rowdetail['arrival']."</td>";
              $laporan .="</tr>";
          }
          $laporan .="</table>";

          $name  ='Info Rencana Retur Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          // $subject ='Request Retur Barang Dengan No #'.$code.'';
          $subject =''.$store_request.' - Rencana Retur Barang Dengan No #'.$code.' - '.$tanggal_permintaan.'';
          $body ='Dear '.$CK.' <br><br> 
                       Ada Retur Barang Dari Store '.$store_request.', <br>dengan No Request  <b>'.$code.' </b>
                       <br><br> 
                       Tanggal Pengiriman Retur Barang : '.$tanggal_permintaan.'
                       <br><br>   
                       '.$laporan.'
                       <br><br>   
                       Note : <br> 
                       '.$keterangan.'
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
          // $to ='angga.aditya@multirasa.co.id';
          $to ='wh.distribusi.jkt@multirasa.co.id';
          $to1 ='ck.admin.sby2@multirasa.co.id';
              if($rowck['CK'] =='CK JAKARTA'){
               // $cc='wh.inv.jkt@multirasa.co.id';
               // $cc1 ='gabriella.tardini@multirasa.co.id';
               // $cc2 ='alexius.sugeng@multirasa.co.id';
               $cc='yoshinoya.retur.jkt@multirasa.co.id';
               $mail->addAddress($to);
               $mail->addCC($cc);
             }else if($rowck['CK'] =='CK SURABAYA'){
               // $cc1 ='henri.hakim@multirasa.co.id';
               // $cc2 ='januar.kusriwahjudi@multirasa.co.id';
               $cc='yoshinoya.retur.sby@multirasa.co.id';
               $mail->addAddress($to1);
               $mail->addCC($cc);
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
          $_SESSION['pesan'] = '<b>Permintaan Berhasil Di Proses!</b>';
          header('Location: retur.php');
     }  
     else  
     {  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          header('Location: retur.php');
          sqlsrv_rollback($conn);  
          echo "Transaction was rolled back.\n"; 
          echo $sqlheader."\n";  
          echo $sqldetail."\n";  
          echo $sqllog."\n";  
     }

   }else{

         if( $stmt && $stmt1 && $stmt2)  
     {  
          sqlsrv_commit($conn);  

          $servername = "192.168.2.136";
          $username = "root";
          $password = "aas260993";
          $dbname = "voucher_trial";
          $conn1 = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());

           $tsql = "SELECT * FROM mst_user where nama= '$store_destination'";   
           $stmt = mysqli_query($conn1,$tsql);
           $user =mysqli_fetch_array($stmt);
           $emaildivisi = 'angga.aditya@multirasa.co.id';

          //  $tsql1 = "SELECT * FROM mst_user where nama= '$store_request'";   
          //  $stmt1 = mysqli_query($conn1,$tsql1);
          //  $user1 =mysqli_fetch_array($stmt1);
          //  $ckarea = $user1['area_ck'];
          //  if($ckarea == 1){
          //       $emailck ='angga.aditya@multirasa.co.id';
          //  }else if($ckarea == 2){
          //      $emailck ='ck.sby@multirasa.co.id';
          //  }else{
          //      $emailck ='';
          //  }

          $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,126) expired,convert(char(10),rtnitem_arrival,126) arrival FROM detail_returnck where header_idrtn='$lastinsertid'";
          $stmtdetail = sqlsrv_query( $conn, $sqldetail );
          if( $stmtdetail === false) {
              die( print_r( sqlsrv_errors(), true) );
          }
          $no=0;

          $laporan="<h4><b>Data Retur Barang Store</b></h4>";
          $laporan .="<br/>";
          $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
          $laporan .="<tr style=\"bgcolor: blue;\">";
          $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Barang Bagus</td><td>Qty Barang Tidak Bagus</td><td>Kadaluarsa</td><td>Kedatangan Barang</td>";
          $laporan .="</tr>";
        
         while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
          {
               if($rowdetail['sap_flag'] == 1){
                    $color ="style='background-color: coral;'";
               }else{
                    $color ='';
               }    
            $fixqtyver =0;
              $laporan .="<tr>";
              $laporan .="<td ".$color.">".$rowdetail['rtnitem_code']."</td><td>".$rowdetail['rtnitem_name']."</td><td>".$rowdetail['rtnitem_uom']."</td><td>".$rowdetail['rtnitem_cat']."</td><td>".$rowdetail['rtnitem_reason']."</td><td>".number_format($rowdetail['rtnitem_qty_good'],2,'.',',')."</td><td>".number_format($rowdetail['rtnitem_qty_not_good'],2,'.',',')."</td><td>".$rowdetail['expired']."</td><td>".$rowdetail['arrival']."</td>";
              $laporan .="</tr>";
          }
          $laporan .="</table>";

          $name  ='Info Rencana Retur Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          // $subject ='Request Retur Barang Dengan No #'.$code.'';
          $subject =''.$store_request.' - Rencana Retur Barang Dengan No #'.$code.' - '.$tanggal_permintaan.'';
          $body ='Dear '.$CK.' <br><br> 
                       Ada Retur Barang Dari Store '.$store_request.', <br>dengan No Request  <b>'.$code.' </b>
                       <br><br> 
                       Tanggal Pengiriman Retur Barang : '.$tanggal_permintaan.'
                       <br><br>   
                       '.$laporan.'
                       <br><br>   
                       Note : <br> 
                       '.$keterangan.'
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
          // $to ='angga.aditya@multirasa.co.id';
          $to ='wh.distribusi.jkt@multirasa.co.id';
          $to1 ='ck.admin.sby2@multirasa.co.id';
              if($rowck['CK'] =='CK JAKARTA'){
               // $cc='wh.inv.jkt@multirasa.co.id';
               // $cc1 ='gabriella.tardini@multirasa.co.id';
               // $cc2 ='alexius.sugeng@multirasa.co.id';
               $cc='yoshinoya.retur.jkt@multirasa.co.id';
               $mail->addAddress($to);
               $mail->addCC($cc);
             }else if($rowck['CK'] =='CK SURABAYA'){
               // $cc1 ='henri.hakim@multirasa.co.id';
               // $cc2 ='januar.kusriwahjudi@multirasa.co.id';
               $cc='yoshinoya.retur.sby@multirasa.co.id';
               $mail->addAddress($to1);
               $mail->addCC($cc);
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
          $_SESSION['pesan'] = '<b>Permintaan Berhasil Di Proses!</b>';
          header('Location: retur.php');
     }  
     else  
     {  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          header('Location: retur.php');
          sqlsrv_rollback($conn);  
          echo "Transaction was rolled back.\n"; 
          echo $sqlheader."\n";  
          echo $sqldetail."\n";  
          echo $sqllog."\n";  
     }



   }



}


}else if($tipe == 1 OR $tipe==2 OR $tipe==4 OR $tipe==5){

     /* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  

$sqlheader = "INSERT  INTO header_returnck (
              reqrtn_code,
              reqrtn_date,
              reqrtn_pickup_type,
              reqrtn_type,
              reqrtn_type_req,
              reqrtn_type_prioritas,
              reqrtn_user,
              reqrtn_ck,
              reqrtn_destination,
              reqrtn_reason,
              reqrtn_note,
              reqrtn_ck_approve,
              reqrtn_nodoc_sap,
              reqrtn_nopica,
              status_progress,
              docnum_ireap,
              created_date,
              created_by
              ) VALUES (
              '".htmlspecialchars(addslashes(trim(strip_tags($code))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($tanggal_permintaan))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($pickuptype))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($jenis_permintaan))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($tipe))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($jenis_prioritas))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($store_request))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($ck_destination))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($store_destination))))."', 
                ".$fixalasan.", 
              '".htmlspecialchars(addslashes(trim(strip_tags($keterangan))))."', 
              'On Progress',
               ".$fixnosjt.", 
               ".$fixnopica.", 
              1,
               '".htmlspecialchars(addslashes(trim(strip_tags($no_ireap))))."', 
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
      $next_result = sqlsrv_next_result($stmt); 
      $row = sqlsrv_fetch_array($stmt); 

      $lastinsertid = $row['id'];

$sqldetail = "INSERT INTO detail_returnck  (
       header_idrtn,
       rtnitem_id,
       rtnitem_code,
       rtnitem_name,
       rtnitem_uom,
       rtnitem_cat,
       rtnitem_reason,
       rtnitem_qty_good,
       rtnitem_qty_not_good,
       rtnitem_expired,
       rtnitem_arrival,
       rtnitem_remarks,
       created_date,
       created_by) values ";

     foreach($_POST['id_barang'] as $option => $opt){

          if($_POST['expired_date'][$option] ==''){
               $fixexpired='NULL';
          }else{
               $fixexpired= "'".$_POST['expired_date'][$option]."'";
          }

          if($_POST['qty_good'][$option] ==''){
               $fixqty_good='NULL';
          }else{
               $fixqty_good= "'".$_POST['qty_good'][$option]."'";
          }

          if($_POST['qty_notgood'][$option] ==''){
               $fixqty_notgood='NULL';
          }else{
               $fixqty_notgood= "'".$_POST['qty_notgood'][$option]."'";
          }

          if($_POST['arrival_date'][$option] ==''){
               $fixarrival='NULL';
          }else{
               $fixarrival= "'".$_POST['arrival_date'][$option]."'";
          }

                  $sqldetail .= "(
                  '$lastinsertid',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['kode_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['nama_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['uom'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['jenis_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['alasan'][$option]))))."',
                   ".$fixqty_good.",
                    ".$fixqty_notgood.",
                  ".$fixexpired.",
                  ".$fixarrival.",
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barang'][$option]))))."',
                  getdate(),
                  '$created_by')";
                  $sqldetail .= ",";
     }

     $sqlfixdetail = rtrim($sqldetail,",");
     $stmt1 = sqlsrv_query($conn,$sqlfixdetail);

     $sqllog = "INSERT  INTO log_return (
      log_idrtn,
      note_rtn,
      created_date,
      created_by
      ) VALUES (
       '$lastinsertid', 
       'CREATE REQUEST',
       getdate(), 
       '$created_by'
        )";


      // $paramslog = array(
      //       $lastinsertid,
      //       $created_by
      // );
      // $stmt2 = sqlsrv_query( $conn, $sqllog, $paramslog);

      $stmt2 = sqlsrv_query( $conn, $sqllog);

            // File upload configuration 
    $targetDir = "dokumen/"; 
     $allowTypes = array('jpg','png','jpeg','gif','JPG','pdf'); 
     
    $statusMsg = $errorMsg = $insertValuesSQL = $errorUpload = $errorUploadType = ''; 
    $fileNames = array_filter($_FILES['dokumensjt']['name']); 
    if(!empty($fileNames)){ 
        foreach($_FILES['dokumensjt']['name'] as $key=>$val){ 
            // File upload path 
            $fileName = basename($_FILES['dokumensjt']['name'][$key]); 
            $targetFilePath = $targetDir . $fileName; 
             
            // Check whether file type is valid 
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
            if(in_array($fileType, $allowTypes)){ 
                // Upload file to server 
                if(move_uploaded_file($_FILES["dokumensjt"]["tmp_name"][$key], $targetFilePath)){ 
                    // Image db insert sql 
                    $insertValuesSQL .= "('$lastinsertid','".$fileName."', getdate(),'$created_by'),"; 
                }else{ 
                    $errorUpload .= $_FILES['dokumensjt']['name'][$key].' | '; 
                } 
            }else{ 
                $errorUploadType .= $_FILES['dokumensjt']['name'][$key].' | '; 
            } 
        } 
         
        // Error message 
        $errorUpload = !empty($errorUpload)?'Upload Error: '.trim($errorUpload, ' | '):''; 
        $errorUploadType = !empty($errorUploadType)?'File Type Error: '.trim($errorUploadType, ' | '):''; 
        $errorMsg = !empty($errorUpload)?'<br/>'.$errorUpload.'<br/>'.$errorUploadType:'<br/>'.$errorUploadType; 
         
        if(!empty($insertValuesSQL)){ 
            $insertValuesSQL = trim($insertValuesSQL, ','); 
            // Insert image file name into database 
            $sqldokumen = "INSERT INTO retur_dokumen (reqrtn_id, nama_dokumen,created_date,created_by) VALUES $insertValuesSQL"; 
             $stmt3 = sqlsrv_query( $conn, $sqldokumen);
        }else{ 
            $statusMsg = "Upload failed! ".$errorMsg; 
        } 
    }else{ 
        $statusMsg = 'Please select a file to upload.'; 
    } 


      if($store_destination =='ENG JAKARTA' || $store_destination =='ENG SURABAYA' || $store_destination =='GA JAKARTA'){

     if( $stmt && $stmt1 && $stmt2 && $stmt3 )  
     {  
          sqlsrv_commit($conn);  

          $servername = "192.168.2.136";
          $username = "root";
          $password = "aas260993";
          $dbname = "voucher_trial";
          $conn1 = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());

           $tsql = "SELECT * FROM mst_user a inner join mst_divisi b on a.div_id=b.id_divisi where nama= '$store_destination'";   
           $stmt = mysqli_query($conn1,$tsql);
           $user =mysqli_fetch_array($stmt);
           $divisi = $user['inisial_divisi'];
           // $emaildivisi = 'angga.aditya@multirasa.co.id';

          //  $tsql1 = "SELECT * FROM mst_user where nama= '$store_request'";   
          //  $stmt1 = mysqli_query($conn1,$tsql1);
          //  $user1 =mysqli_fetch_array($stmt1);
          //  $ckarea = $user1['area_ck'];
          //  if($ckarea == 1){
          //       $emailck ='angga.aditya@multirasa.co.id';
          //  }else if($ckarea == 2){
          //      $emailck ='ck.sby@multirasa.co.id';
          //  }else{
          //      $emailck ='';
          //  }



          $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,126) expired,convert(char(10),rtnitem_arrival,126) arrival FROM detail_returnck where header_idrtn='$lastinsertid'";
          $stmtdetail = sqlsrv_query( $conn, $sqldetail );
          if( $stmtdetail === false) {
              die( print_r( sqlsrv_errors(), true) );
          }
          $no=0;

          $laporan="<h4><b>Data Retur Barang Store</b></h4>";
          $laporan .="<br/>";
          $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
          $laporan .="<tr style=\"bgcolor: blue;\">";
          $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Kirim</td><td>Kadaluarsa</td><td>Kedatangan Barang</td>";
          $laporan .="</tr>";
        
         while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
          {
               if($rowdetail['sap_flag'] == 1){
                    $color ="style='background-color: coral;'";
               }else{
                    $color ='';
               }    
            $fixqtyver =0;
              $laporan .="<tr>";
              $laporan .="<td ".$color.">".$rowdetail['rtnitem_code']."</td><td>".$rowdetail['rtnitem_name']."</td><td>".$rowdetail['rtnitem_uom']."</td><td>".$rowdetail['rtnitem_cat']."</td><td>".$rowdetail['rtnitem_reason']."</td><td>".number_format($rowdetail['rtnitem_qty_not_good'],2,'.',',')."</td><td>".$rowdetail['expired']."</td><td>".$rowdetail['arrival']."</td>";
              $laporan .="</tr>";
          }
          $laporan .="</table>";

          $name  ='Info Rencana Retur Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          // $subject ='Request Retur Barang Dengan No #'.$code.'';
          $subject =''.$store_request.' - Rencana Retur Barang Dengan No #'.$code.' - '.$tanggal_permintaan.'';
          $body ='Dear '.$CK.' <br><br> 
                       Mohon approvalnya untuk Retur Barang Dari Store '.$store_request.', <br>dengan No Request  <b>'.$code.' </b>
                       <br><br> 
                       Tanggal Pengiriman Retur Barang : '.$tanggal_permintaan.'
                       <br><br>   
                       '.$laporan.'
                       <br><br>   
                       Note : <br> 
                       '.$keterangan.'
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
          // $to ='angga.aditya@multirasa.co.id';
          $to ='wh.distribusi.jkt@multirasa.co.id';
          $to1 ='ck.admin.sby2@multirasa.co.id';
              if($rowck['CK'] =='CK JAKARTA'){
               // $cc='wh.inv.jkt@multirasa.co.id';
               // $cc1 ='gabriella.tardini@multirasa.co.id';
               // $cc2 ='alexius.sugeng@multirasa.co.id';
               $cc='yoshinoya.retur.jkt@multirasa.co.id';
               $mail->addAddress($to);
               $mail->addCC($cc);
             }else if($rowck['CK'] =='CK SURABAYA'){
               // $cc1 ='henri.hakim@multirasa.co.id';
               // $cc2 ='januar.kusriwahjudi@multirasa.co.id';
               $cc='yoshinoya.retur.sby@multirasa.co.id';
               $mail->addAddress($to1);
               $mail->addCC($cc);
             }else{
                if($divisi == 'IT'){
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
                }else if($divisi == 'ENG'){
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
                }else if($divisi == 'GA'){
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
          header('Location: retur.php');
     }  
     else  
     {  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          // header('Location: retur.php');
          sqlsrv_rollback($conn);  
          echo "Transaction was rolled back.\n"; 
          echo $sqlheader."\n";  
          echo $sqldetail."\n";  
          echo $sqllog."\n";  
          echo $sqldokumen."\n"; 
     } 

   }else{

         if( $stmt && $stmt1 && $stmt2)  
     {  
          sqlsrv_commit($conn);  

          $servername = "192.168.2.136";
          $username = "root";
          $password = "aas260993";
          $dbname = "voucher_trial";
          $conn1 = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());

           $tsql = "SELECT * FROM mst_user a inner join mst_divisi b on a.div_id=b.id_divisi where nama= '$store_destination'";   
           $stmt = mysqli_query($conn1,$tsql);
           $user =mysqli_fetch_array($stmt);
           $divisi = $user['inisial_divisi'];
           $emaildivisi = 'angga.aditya@multirasa.co.id';

          //  $tsql1 = "SELECT * FROM mst_user where nama= '$store_request'";   
          //  $stmt1 = mysqli_query($conn1,$tsql1);
          //  $user1 =mysqli_fetch_array($stmt1);
          //  $ckarea = $user1['area_ck'];
          //  if($ckarea == 1){
          //       $emailck ='angga.aditya@multirasa.co.id';
          //  }else if($ckarea == 2){
          //      $emailck ='ck.sby@multirasa.co.id';
          //  }else{
          //      $emailck ='';
          //  }

          $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,126) expired,convert(char(10),rtnitem_arrival,126) arrival FROM detail_returnck where header_idrtn='$lastinsertid'";
          $stmtdetail = sqlsrv_query( $conn, $sqldetail );
          if( $stmtdetail === false) {
              die( print_r( sqlsrv_errors(), true) );
          }
          $no=0;

          $laporan="<h4><b>Data Retur Barang Store</b></h4>";
          $laporan .="<br/>";
          $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
          $laporan .="<tr style=\"bgcolor: blue;\">";
          $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Barang Bagus</td><td>Qty Barang Tidak Bagus</td><td>Kadaluarsa</td><td>Kedatangan Barang</td>";
          $laporan .="</tr>";
        
         while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
          {
               if($rowdetail['sap_flag'] == 1){
                    $color ="style='background-color: coral;'";
               }else{
                    $color ='';
               }    
            $fixqtyver =0;
              $laporan .="<tr>";
              $laporan .="<td ".$color.">".$rowdetail['rtnitem_code']."</td><td>".$rowdetail['rtnitem_name']."</td><td>".$rowdetail['rtnitem_uom']."</td><td>".$rowdetail['rtnitem_cat']."</td><td>".$rowdetail['rtnitem_reason']."</td><td>".number_format($rowdetail['rtnitem_qty_good'],2,'.',',')."</td><td>".number_format($rowdetail['rtnitem_qty_not_good'],2,'.',',')."</td><td>".$rowdetail['expired']."</td><td>".$rowdetail['arrival']."</td>";
              $laporan .="</tr>";
          }
          $laporan .="</table>";

          $name  ='Info Rencana Retur Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          // $subject ='Request Retur Barang Dengan No #'.$code.'';
          $subject =''.$store_request.' - Rencana Retur Barang Dengan No #'.$code.' - '.$tanggal_permintaan.'';
          $body ='Dear '.$CK.' <br><br> 
                       Mohon approvalnya untuk Retur Barang Dari Store '.$store_request.', <br>dengan No Request  <b>'.$code.' </b>
                       <br><br> 
                       Tanggal Pengiriman Retur Barang : '.$tanggal_permintaan.'
                       <br><br>   
                       '.$laporan.'
                       <br><br>   
                       Note : <br> 
                       '.$keterangan.'
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
          // $to ='angga.aditya@multirasa.co.id';
          $to ='wh.distribusi.jkt@multirasa.co.id';
          $to1 ='ck.admin.sby2@multirasa.co.id';
             if($rowck['CK'] =='CK JAKARTA'){
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
             }else{
                 if($divisi == 'IT'){
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
                }else if($divisi == 'ENG'){
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
                }else if($divisi == 'GA'){
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
          header('Location: retur.php');
     }  
     else  
     {  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          // header('Location: retur.php');
          sqlsrv_rollback($conn);  
          echo "Transaction was rolled back.\n"; 
          echo $sqlheader."\n";  
          echo $sqldetail."\n";  
          echo $sqllog."\n";  
          echo $sqldokumen."\n"; 
     } 



   }

    /* Free statement and connection resources. */  
    sqlsrv_free_stmt( $stmt);  
    sqlsrv_free_stmt( $stmt1);  
    sqlsrv_free_stmt( $stmt2);  
    sqlsrv_free_stmt( $stmt3);  
    sqlsrv_close( $conn);  

}else{
     echo "tipe sap tidak ditemukan!";
}

}else if($pickuptype == 2){

 /* Initiate transaction. */  
/* Exit script if transaction cannot be initiated. */  
if ( sqlsrv_begin_transaction( $conn ) === false )  
{  
     echo "Could not begin transaction.\n";  
     die( print_r( sqlsrv_errors(), true ));  
}  

$sqlheader = "INSERT  INTO header_returnck (
              reqrtn_code,
              reqrtn_date,
              reqrtn_pickup_type,
              reqrtn_type,
              reqrtn_type_req,
              reqrtn_type_prioritas,
              reqrtn_user,
              reqrtn_ck,
              reqrtn_destination,
              reqrtn_reason,
              reqrtn_note,
              reqrtn_ck_approve,
              reqrtn_ck_approve_date,
              reqrtn_ck_approve_note,
              reqrtn_destination_approve,
              reqrtn_nodoc_sap,
              reqrtn_nopica,
              status_progress,
              created_date,
              created_by
              ) VALUES (
              '".htmlspecialchars(addslashes(trim(strip_tags($code))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($tanggal_permintaan))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($pickuptype))))."',
              '".htmlspecialchars(addslashes(trim(strip_tags($jenis_permintaan))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($tipe))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($jenis_prioritas))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($store_request))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($ck_destination))))."', 
              '".htmlspecialchars(addslashes(trim(strip_tags($store_destination))))."', 
              ".$fixalasan.", 
              '".htmlspecialchars(addslashes(trim(strip_tags($keterangan))))."', 
              'Approved',
               getdate(),
              'Approved by Sistem (Pengambilan Lewat Divisi Tujuan)',
              'On Progress',
              ".$fixnosjt.",
              ".$fixnopica.",
              2,
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
      $next_result = sqlsrv_next_result($stmt); 
      $row = sqlsrv_fetch_array($stmt); 

      $lastinsertid = $row['id'];

$sqldetail = "INSERT INTO detail_returnck  (
       header_idrtn,
       rtnitem_id,
       rtnitem_code,
       rtnitem_name,
       rtnitem_uom,
       rtnitem_cat,
       rtnitem_reason,
       rtnitem_qty_good,
       rtnitem_qty_not_good,
       rtnitem_expired,
       rtnitem_arrival,
       rtnitem_remarks,
       rtnitem_status_approve,
       rtnitem_remarks_approve,
       created_date,
       created_by) values ";

     foreach($_POST['id_barang'] as $option => $opt){

          if($_POST['expired_date'][$option] ==''){
               $fixexpired='NULL';
          }else{
               $fixexpired= "'".$_POST['expired_date'][$option]."'";
          }

          if($_POST['qty_good'][$option] ==''){
               $fixqty_good='NULL';
          }else{
               $fixqty_good= "'".$_POST['qty_good'][$option]."'";
          }

          if($_POST['qty_notgood'][$option] ==''){
               $fixqty_notgood='NULL';
          }else{
               $fixqty_notgood= "'".$_POST['qty_notgood'][$option]."'";
          }


          if($_POST['arrival_date'][$option] ==''){
               $fixarrival='NULL';
          }else{
               $fixarrival= "'".$_POST['arrival_date'][$option]."'";
          }

                  $sqldetail .= "(
                  '$lastinsertid',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['kode_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['nama_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['uom'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['jenis_barang'][$option]))))."',
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['alasan'][$option]))))."',
                 ".$fixqty_good.",
                  ".$fixqty_notgood.",
                  ".$fixexpired.",
                  ".$fixarrival.",
                  '".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barang'][$option]))))."',
                  '0',
                  'Auto Approve (Langsung diambil divisi tujuan)',
                  getdate(),
                  '$created_by')";
                  $sqldetail .= ",";
     }

     $sqlfixdetail = rtrim($sqldetail,",");
     $stmt1 = sqlsrv_query($conn,$sqlfixdetail);

     $sqllog = "INSERT  INTO log_return (
      log_idrtn,
      note_rtn,
      created_date,
      created_by
      ) VALUES (
       '$lastinsertid', 
       'CREATE REQUEST',
       getdate(), 
       '$created_by'
        )";

      // $paramslog = array(
      //       $lastinsertid,
      //       $created_by
      // );
      // $stmt2 = sqlsrv_query( $conn, $sqllog, $paramslog);

      $stmt2 = sqlsrv_query( $conn, $sqllog);

              // File upload configuration 
    $targetDir = "dokumen/"; 
     $allowTypes = array('jpg','png','jpeg','gif','JPG','pdf'); 
     
    $statusMsg = $errorMsg = $insertValuesSQL = $errorUpload = $errorUploadType = ''; 
    $fileNames = array_filter($_FILES['dokumensjt']['name']); 
    if(!empty($fileNames)){ 
        foreach($_FILES['dokumensjt']['name'] as $key=>$val){ 
            // File upload path 
            $fileName = basename($_FILES['dokumensjt']['name'][$key]); 
            $targetFilePath = $targetDir . $fileName; 
             
            // Check whether file type is valid 
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
            if(in_array($fileType, $allowTypes)){ 
                // Upload file to server 
                if(move_uploaded_file($_FILES["dokumensjt"]["tmp_name"][$key], $targetFilePath)){ 
                    // Image db insert sql 
                    $insertValuesSQL .= "('$lastinsertid','".$fileName."', getdate(),'$created_by'),"; 
                }else{ 
                    $errorUpload .= $_FILES['dokumensjt']['name'][$key].' | '; 
                } 
            }else{ 
                $errorUploadType .= $_FILES['dokumensjt']['name'][$key].' | '; 
            } 
        } 
         
        // Error message 
        $errorUpload = !empty($errorUpload)?'Upload Error: '.trim($errorUpload, ' | '):''; 
        $errorUploadType = !empty($errorUploadType)?'File Type Error: '.trim($errorUploadType, ' | '):''; 
        $errorMsg = !empty($errorUpload)?'<br/>'.$errorUpload.'<br/>'.$errorUploadType:'<br/>'.$errorUploadType; 
         
        if(!empty($insertValuesSQL)){ 
            $insertValuesSQL = trim($insertValuesSQL, ','); 
            // Insert image file name into database 
            $sqldokumen = "INSERT INTO retur_dokumen (reqrtn_id, nama_dokumen,created_date,created_by) VALUES $insertValuesSQL"; 
             $stmt3 = sqlsrv_query( $conn, $sqldokumen);
        }else{ 
            $statusMsg = "Upload failed! ".$errorMsg; 
        } 
    }else{ 
        $statusMsg = 'Please select a file to upload.'; 
    } 


     if($store_destination =='ENG JAKARTA' || $store_destination =='ENG SURABAYA' || $store_destination =='GA JAKARTA' || $store_destination =='GA SURABAYA'){

     if( $stmt && $stmt1 && $stmt2 && $stmt3 )  
     {  
          sqlsrv_commit($conn);  

          $servername = "192.168.2.136";
          $username = "root";
          $password = "aas260993";
          $dbname = "voucher_trial";
          $conn1 = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());

             $tsql = "SELECT * FROM mst_user a inner join mst_divisi b on a.div_id=b.id_divisi where nama= '$store_destination'";   
           $stmt = mysqli_query($conn1,$tsql);
           $user =mysqli_fetch_array($stmt);
           $divisi = $user['inisial_divisi'];
           // $emaildivisi = 'angga.aditya@multirasa.co.id';

          //  $tsql1 = "SELECT * FROM mst_user where nama= '$store_request'";   
          //  $stmt1 = mysqli_query($conn1,$tsql1);
          //  $user1 =mysqli_fetch_array($stmt1);
          //  $ckarea = $user1['area_ck'];
          //  if($ckarea == 1){
          //       $emailck ='angga.aditya@multirasa.co.id';
          //  }else if($ckarea == 2){
          //      $emailck ='ck.sby@multirasa.co.id';
          //  }else{
          //      $emailck ='';
          //  }

          $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,126) expired,convert(char(10),rtnitem_arrival,126) arrival FROM detail_returnck where header_idrtn='$lastinsertid'";
          $stmtdetail = sqlsrv_query( $conn, $sqldetail );
          if( $stmtdetail === false) {
              die( print_r( sqlsrv_errors(), true) );
          }
          $no=0;

          $laporan="<h4><b>Data Retur Barang Store</b></h4>";
          $laporan .="<br/>";
          $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
          $laporan .="<tr style=\"bgcolor: blue;\">";
          $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Kirim</td><td>Kadaluarsa</td><td>Kedatangan Barang</td>";
          $laporan .="</tr>";
        
         while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
          {
               if($rowdetail['sap_flag'] == 1){
                    $color ="style='background-color: coral;'";
               }else{
                    $color ='';
               }    
            $fixqtyver =0;
              $laporan .="<tr>";
              $laporan .="<td ".$color.">".$rowdetail['rtnitem_code']."</td><td>".$rowdetail['rtnitem_name']."</td><td>".$rowdetail['rtnitem_uom']."</td><td>".$rowdetail['rtnitem_cat']."</td><td>".$rowdetail['rtnitem_reason']."</td><td>".number_format($rowdetail['rtnitem_qty_not_good'],2,'.',',')."</td><td>".$rowdetail['expired']."</td><td>".$rowdetail['arrival']."</td>";
              $laporan .="</tr>";
          }
          $laporan .="</table>";

          $name  ='Info Rencana Retur Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          // $subject ='Request Retur Barang Dengan No #'.$code.'';
          $subject =''.$store_request.' - Rencana Retur Barang Dengan No #'.$code.' - '.$tanggal_permintaan.'';
          $body ='Dear '.$divisi.' <br><br> 
                       Ada Retur Barang Dari Store '.$store_request.' dan langsung dilakukan pengiriman ke divisi yang bersangkutan, <br>dengan No Request  <b>'.$code.' </b>
                       <br><br> 
                       Tanggal Pengiriman Retur Barang : '.$tanggal_permintaan.'
                       <br><br>   
                       '.$laporan.'
                       <br><br>   
                       Note : <br> 
                       '.$keterangan.'
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
          // $to ='angga.aditya@multirasa.co.id';
          // $to = $emailstoredestination;
          // $to1 ='ck.admin.sby2@multirasa.co.id';
          $mail->addAddress($emailstoredestination);
          // if($rowck['CK'] =='CK JAKARTA'){
          //      $cc='wh.inv.jkt@multirasa.co.id';
          //      $cc1 ='gabriella.tardini@multirasa.co.id';
          //      $cc2 ='alexius.sugeng@multirasa.co.id';
          //      $mail->addAddress($to);
          //      // $mail->addCC($cc);
          //      // $mail->addCC($cc1);
          //      // $mail->addCC($cc2);
          //    }else if($rowck['CK'] =='CK SURABAYA'){
          //      $cc1 ='henri.hakim@multirasa.co.id';
          //      $cc2 ='januar.kusriwahjudi@multirasa.co.id';
          //      $mail->addAddress($to1);
          //      $mail->addCC($cc1);
          //      $mail->addCC($cc2);
          //    }else{
                if($divisi == 'IT'){
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
                }else if($divisi == 'ENG'){
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
                }else if($divisi == 'GA'){
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
          header('Location: retur.php');
     }  
     else  
     {  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          // header('Location: retur.php');
          sqlsrv_rollback($conn);  
          echo "Transaction was rolled back.\n"; 
          echo $sqlheader."\n";  
          echo $sqldetail."\n";  
          echo $sqllog."\n";  
          echo $sqldokumen."\n";  
     }

   }else{

         if( $stmt && $stmt1 && $stmt2)  
     {  
          sqlsrv_commit($conn);  

          $servername = "192.168.2.136";
          $username = "root";
          $password = "aas260993";
          $dbname = "voucher_trial";
          $conn1 = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());

             $tsql = "SELECT * FROM mst_user a inner join mst_divisi b on a.div_id=b.id_divisi where nama= '$store_destination'";   
           $stmt = mysqli_query($conn1,$tsql);
           $user =mysqli_fetch_array($stmt);
           $divisi = $user['inisial_divisi'];
           $emaildivisi = 'angga.aditya@multirasa.co.id';

          //  $tsql1 = "SELECT * FROM mst_user where nama= '$store_request'";   
          //  $stmt1 = mysqli_query($conn1,$tsql1);
          //  $user1 =mysqli_fetch_array($stmt1);
          //  $ckarea = $user1['area_ck'];
          //  if($ckarea == 1){
          //       $emailck ='angga.aditya@multirasa.co.id';
          //  }else if($ckarea == 2){
          //      $emailck ='ck.sby@multirasa.co.id';
          //  }else{
          //      $emailck ='';
          //  }

          $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,126) expired,convert(char(10),rtnitem_arrival,126) arrival FROM detail_returnck where header_idrtn='$lastinsertid'";
          $stmtdetail = sqlsrv_query( $conn, $sqldetail );
          if( $stmtdetail === false) {
              die( print_r( sqlsrv_errors(), true) );
          }
          $no=0;

          $laporan="<h4><b>Data Retur Barang Store</b></h4>";
          $laporan .="<br/>";
          $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
          $laporan .="<tr style=\"bgcolor: blue;\">";
          $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Barang Bagus</td><td>Qty Barang Tidak Bagus</td><td>Kadaluarsa</td><td>Kedatangan Barang</td>";
          $laporan .="</tr>";
        
         while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
          {
               if($rowdetail['sap_flag'] == 1){
                    $color ="style='background-color: coral;'";
               }else{
                    $color ='';
               }    
            $fixqtyver =0;
              $laporan .="<tr>";
              $laporan .="<td ".$color.">".$rowdetail['rtnitem_code']."</td><td>".$rowdetail['rtnitem_name']."</td><td>".$rowdetail['rtnitem_uom']."</td><td>".$rowdetail['rtnitem_cat']."</td><td>".$rowdetail['rtnitem_reason']."</td><td>".$rowdetail['rtnitem_qty_good']."</td><td>".number_format($rowdetail['rtnitem_qty_not_good'],2,'.',',')."</td><td>".$rowdetail['expired']."</td><td>".$rowdetail['arrival']."</td>";
              $laporan .="</tr>";
          }
          $laporan .="</table>";

          $name  ='Info Rencana Retur Barang';
          $email = 'info.voucherrequest@multirasa.co.id';
          // $subject ='Request Retur Barang Dengan No #'.$code.'';
          $subject =''.$store_request.' - Rencana Retur Barang Dengan No #'.$code.' - '.$tanggal_permintaan.'';
          $body ='Dear '.$divisi.' <br><br> 
                       Ada Retur Barang Dari Store '.$store_request.' dan langsung dilakukan pengiriman ke divisi yang bersangkutan, <br>dengan No Request  <b>'.$code.' </b>
                       <br><br> 
                       Tanggal Pengiriman Retur Barang : '.$tanggal_permintaan.'
                       <br><br>   
                       '.$laporan.'
                       <br><br>   
                       Note : <br> 
                       '.$keterangan.'
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
          // $to ='angga.aditya@multirasa.co.id';
          // $to = $emailstoredestination;
          // $to1 ='ck.admin.sby2@multirasa.co.id';
          $mail->addAddress($emailstoredestination);
          // if($rowck['CK'] =='CK JAKARTA'){
          //      $cc='wh.inv.jkt@multirasa.co.id';
          //      $cc1 ='gabriella.tardini@multirasa.co.id';
          //      $cc2 ='alexius.sugeng@multirasa.co.id';
          //      $mail->addAddress($to);
          //      // $mail->addCC($cc);
          //      // $mail->addCC($cc1);
          //      // $mail->addCC($cc2);
          //    }else if($rowck['CK'] =='CK SURABAYA'){
          //      $cc1 ='henri.hakim@multirasa.co.id';
          //      $cc2 ='januar.kusriwahjudi@multirasa.co.id';
          //      $mail->addAddress($to1);
          //      $mail->addCC($cc1);
          //      $mail->addCC($cc2);
          //    }else{
                 if($divisi == 'IT'){
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
                }else if($divisi == 'ENG'){
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
                }else if($divisi == 'GA'){
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
          header('Location: retur.php');
     }  
     else  
     {  
          $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          // header('Location: retur.php');
          sqlsrv_rollback($conn);  
          echo "Transaction was rolled back.\n"; 
          echo $sqlheader."\n";  
          echo $sqldetail."\n";  
          echo $sqllog."\n";  
     }



   }



     }else{
          echo "pickup type tidak ditemukan";
     }

}else if($jenis_permintaan == 3){

     /* Initiate transaction. */  
     /* Exit script if transaction cannot be initiated. */  
     if ( sqlsrv_begin_transaction( $conn ) === false )  
     {  
          echo "Could not begin transaction.\n";  
          die( print_r( sqlsrv_errors(), true ));  
     }  
     
     $sqlheader = "INSERT  INTO header_tb (
                   reqtb_code,
                   reqtb_date,
                   reqtb_type,
                   reqtb_user,
                   reqtb_destination,
                   reqtb_note,
                   reqtb_destination_approve,
                   status_progress,
                   created_date,
                   created_by
                   ) VALUES (
                   '".htmlspecialchars(addslashes(trim(strip_tags($code))))."', 
                   '".htmlspecialchars(addslashes(trim(strip_tags($tanggal_permintaan))))."', 
                   '".htmlspecialchars(addslashes(trim(strip_tags($jenis_permintaan))))."', 
                   '".htmlspecialchars(addslashes(trim(strip_tags( $store_request))))."', 
                   '".htmlspecialchars(addslashes(trim(strip_tags($store_destination))))."', 
                   '".htmlspecialchars(addslashes(trim(strip_tags($keterangan))))."', 
                   'On Progress',
                   1,
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
           $next_result = sqlsrv_next_result($stmt); 
           $row = sqlsrv_fetch_array($stmt); 
     
           $lastinsertid = $row['id'];
     
     $sqldetail = "INSERT INTO detail_tb  (
            header_idtb,
            tbitem_id,
            tbitem_code,
            tbitem_name,
            tbitem_uom,
            tbitem_cat,
            tbitem_reason,
            tbitem_qty,
            tbitem_remarks,
            created_date,
            created_by) values ";
     
          foreach($_POST['id_barang'] as $option => $opt){
                       $sqldetail .= "(
                       '$lastinsertid',
                       '".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."',
                       '".htmlspecialchars(addslashes(trim(strip_tags($_POST['kode_barang'][$option]))))."',
                       '".htmlspecialchars(addslashes(trim(strip_tags($_POST['nama_barang'][$option]))))."',
                       '".htmlspecialchars(addslashes(trim(strip_tags($_POST['uom'][$option]))))."',
                       '".htmlspecialchars(addslashes(trim(strip_tags($_POST['jenisbarang'][$option]))))."',
                       '".htmlspecialchars(addslashes(trim(strip_tags($_POST['alasan'][$option]))))."',
                       '".htmlspecialchars(addslashes(trim(strip_tags($_POST['qty'][$option]))))."',
                       '".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barang'][$option]))))."',
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
           note_tb,
           created_date,
           created_by
           ) VALUES (
            '$lastinsertid', 
            'CREATE REQUEST',
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
     
               $name  ='Info Permintaan Barang Transfer Balik Store';
               $email = 'info.voucherrequest@multirasa.co.id';
               $subject ='Permintaan Barang Dengan No #'.$code.'';
               $body ='Dear Store '.$store_destination.' <br><br> 
                            Mohon persetujuannya untuk Permintaan Barang Dari Store '.$store_request.', dengan No   <b>'.$code.' </b>
                            <br><br>   
                            Note : <br> 
                            '.$keterangan.'
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
               $mail->addAddress($emailstoredestination); //enter you email address
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
               header('Location: tbs.php');
          }  
          else  
          {  
               sqlsrv_rollback($conn);  
               $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
               header('Location: tbs.php');
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
     
     }else{
     $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
          header('Location: request_new.php');
}

?>