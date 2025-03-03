<?php
include "db.php";
error_reporting(0);
use PHPMailer\PHPMailer\PHPMailer;
require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";
require_once "PHPMailer/Exception.php";

$id_tb = $_POST['id_tb'];
$code = $_POST['reqtb_code'];
$store_request =  $_POST['reqtb_user'];
$store_destination = $_POST['reqtb_destination'];
$note_request_verifikasi = $_POST['note_request_verifikasi'];
// $status_header = $_POST['status_header'];
$status_request = $_POST['status_request'];
$created_by =  $_SESSION['nama'];
$reqtb_req = $_POST['reqtb_req']; 
$today = date('Y-m-d');
// $totalpengembalian = $_POST['totalselisi']; 
// $totalpeminjaman = $_POST['subTotalpeminjaman']; 
// $selisi =$totalpeminjaman + $totalpengembalian; 



if($_POST['totalselisi'] ==''){
  $selisi= $_POST['subTotalpeminjaman'];
}else{
  $selisi=$_POST['totalselisi']; 
}
$kelebihan =$_POST['totalkelebihan'];

// echo $selisi;

if($kelebihan ==''){
  $flagkelebihan='(NULL)';
  $statuskelebihan='(NULL)';
}else{
  $flagkelebihan= "'1'";
  $statuskelebihan="'On Progress'";
}

$hostName = "192.168.2.136";
$username = "root";
$password = "aas260993";
$dbname = "voucher_trial";
$mysqli = new mysqli($hostName, $username, $password, $dbname);

$querystore="SELECT email from mst_user where nama='$store_request'";
$hasilstore = $mysqli->query($querystore);
$rowstore = $hasilstore->fetch_assoc();
$emailstore= $rowstore['email'];

$querystoredestination="SELECT email from mst_user where nama='$store_destination'";
$hasilstoredestination = $mysqli->query($querystoredestination);
$rowstoredestination = $hasilstoredestination->fetch_assoc();
$emailstoredestination= $rowstoredestination['email'];

$sqlflagpengembalian="SELECT max(flag) as flagpengembalian from detail_returntb 
WHERE header_idrtrtb='$id_tb'";
$hasilcekpengembalian= sqlsrv_query( $conn, $sqlflagpengembalian );
$rowflag = sqlsrv_fetch_array( $hasilcekpengembalian, SQLSRV_FETCH_ASSOC);
$flagpengembalian = $rowflag['flagpengembalian'];

foreach($_POST['tp'] as $option => $opt){
  $b[]=$_POST['tp'][$option];
  }

  if (in_array("1", array_merge($b)))
  {
     $status ="TPS";
  }else if(in_array("2", array_merge($b))){
     $status ="NO TPS";
  }else{
     $status ="";
  }

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


if($selisi < 0){

      /* Initiate transaction. */  
      /* Exit script if transaction cannot be initiated. */  
      if ( sqlsrv_begin_transaction( $conn ) === false )  
      {  
           echo "Could not begin transaction.\n";  
           die( print_r( sqlsrv_errors(), true ));  
      }  

      $sqlheader = "UPDATE header_tb SET
                reqtb_user_retur='Pending',
                reqtb_destination_retur_verifikasi='Pending',
                    reqtb_destination_retur_verifikasi_date=getdate(),
                    reqtb_destination_retur_verifikasi_note='".htmlspecialchars(addslashes(trim(strip_tags($note_request_verifikasi))))."',
                 reqtb_flag_plus=$flagkelebihan,
                 reqtb_destination_retur_plus=$statuskelebihan,
              status_progress=3
                WHERE id_tb='$id_tb'";

            $stmt = sqlsrv_query( $conn, $sqlheader);

            if($status =='TPS'){

              $bulan = date('m');
              $tahun = date ('Y');
              // $query = "SELECT MAX(uniqueorder) AS maxKode FROM order_voucher WHERE YEAR(date_order)='$tahun' and MONTH(date_order) = '$bulan'";
              $sqlcode="SELECT MAX(reqtp_code) AS maxKode FROM header_tp
                        WHERE YEAR(created_date)='$tahun' and MONTH(created_date) = '$bulan' and  reqtp_user= '".$store_request."' and reqtp_type=1";
              $hasilcode = sqlsrv_query( $conn, $sqlcode );
              $rowcode = sqlsrv_fetch_array( $hasilcode, SQLSRV_FETCH_ASSOC);
    
              $noUrut= $rowcode["maxKode"] + 1;
              $kode =  sprintf("%03s", $noUrut);
              $nomor = "/".$store_request."/TPS/".$bulan."/".$tahun;
              $codeTP = $kode.$nomor;

            $sqlheader = "INSERT  INTO header_tp (
                          reqtp_code,
                          reqtp_date,
                          reqtp_type,
                          reqtp_user,
                          reqtp_destination,
                          reqtp_note,
                          reqtp_destination_approve,
                          reqtp_destination_approve_date,
                          reqtp_user_verifikasi,
                          reqtp_user_verifikasi_date,
                          reqtp_ck_destination,
                          status_progress,
                          created_date,
                          created_by
                          ) VALUES (
                          '".htmlspecialchars(addslashes(trim(strip_tags($codeTP))))."', 
                          '".htmlspecialchars(addslashes(trim(strip_tags($today))))."', 
                          '".htmlspecialchars(addslashes(trim(strip_tags(1))))."', 
                          '".htmlspecialchars(addslashes(trim(strip_tags( $store_request))))."', 
                          '".htmlspecialchars(addslashes(trim(strip_tags($store_destination))))."', 
                          'Auto Ganerate TPS, ACT date : $reqtb_req', 
                          'Approved',
                          getdate(),
                          'Verifikasi',
                          getdate(),
                          '$CK',
                          3,
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
                  tpitem_qty_approve,
                  tpitem_qty_verifikasi_good,
                  tpitem_qty_verifikasi_not_good,
                  created_date,
                  created_by) values ";

                foreach($_POST['id_detail_returtb'] as $option => $opt){

                  if($_POST['tp'][$option] == 1){
                              $sqldetail .= "(
                              '$lastinsertid',
                              '".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."',
                              '".htmlspecialchars(addslashes(trim(strip_tags($_POST['item_code'][$option]))))."',
                              '".htmlspecialchars(addslashes(trim(strip_tags($_POST['item_name'][$option]))))."',
                              '".htmlspecialchars(addslashes(trim(strip_tags($_POST['item_uom'][$option]))))."',
                              '".htmlspecialchars(addslashes(trim(strip_tags($_POST['item_cat'][$option]))))."',
                              '".htmlspecialchars(addslashes(trim(strip_tags($_POST['tbitem_reason'][$option]))))."',
                              '".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option]))))."',
                              '".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option]))))."',
                              '".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option]))))."',
                              '".htmlspecialchars(addslashes(trim(strip_tags(0))))."',
                              getdate(),
                              '$created_by')";
                              $sqldetail .= ",";

                  }
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
                  'AUTO GANERATE TPS',
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

                      
                      $sqldetail = "SELECT *,convert(char(10),tpitem_expired,126) expired FROM detail_tp where header_idtp='$lastinsertid'";
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
                      $subject =''.$status_request.' Permintaan Barang Dengan No #'.$codeTP.'';
                      $body ='Dear Store '.$store_destination.' <br><br> 
                                  Permintaan Barang <br>dengan No  <b>'.$codeTP.' </b> telah dilakukan verifikasi
                                  Oleh Store '.$store_request.' 
                                  <br><br>  
                                  '.$laporan.'
                                  <br><br> 
                                  Note : 
                                  <br><br><br> 
                                  Terimakasih.';

                      $mail1 = new PHPMailer();

                      //SMTP Settings
                      $mail1->isSMTP();
                      $mail1->Host = "mail.multirasa.co.id";
                      $mail1->SMTPAuth = true;
                      $mail1->Username = "info.voucherrequest@multirasa.co.id"; //enter you email address
                      $mail1->Password = 'yoshimulti'; //enter you email password
                      $mail1->Port = 465;
                      $mail1->SMTPSecure = "ssl";
              
                      //Email Settings
                      $mail1->isHTML(true);
                      $mail1->setFrom($email, $name); 
                      $mail1->addAddress($emailstore); 
                      $mail1->addAddress($emailstoredestination); 
                      if($rowck['CK'] =='CK JAKARTA'){  
                        // $cc ='angga.aditya@multirasa.co.id'; 
                        // $cc1 ='gabriella.tardini@multirasa.co.id';
                        // $cc2 ='alexius.sugeng@multirasa.co.id';
                        $cc ='yoshinoya.transfertoko.jkt@multirasa.co.id';
                        $mail1->addCC($cc);
                        // $mail->addCC($cc1);
                        // $mail->addCC($cc2);
                      }else if($rowck['CK'] =='CK SURABAYA'){ 
                        // $cc ='ck.admin.sby2@multirasa.co.id';
                        // $cc1 ='henri.hakim@multirasa.co.id';
                        // $cc2 ='januar.kusriwahjudi@multirasa.co.id';
                        $cc ='yoshinoya.transfertoko.sby@multirasa.co.id';
                        $mail1->addCC($cc);
                        // $mail->addCC($cc1);
                        // $mail->addCC($cc2);
                      }else{
                      }
                      // $mail1->addCC('angga.aditya@multirasa.co.id');
                      $mail1->Subject = ("$subject");
                      $mail1->Body = $body;

                      if ($mail1->send()) {
                          $status = "success";
                          $response = "Email is sent!";
                      } else {
                          $status = "failed";
                          $response = "Something is wrong: <br><br>" . $mail1->ErrorInfo;
                      } 

                      echo "Transaction was committed.\n<br>";  
                }  
                else  
                {  
                      sqlsrv_rollback($conn);  
                      echo "Transaction was rolled back.\n"; 
                      echo $sqlheader."\n";  
                      echo $sqldetail."\n";  
                      echo $sqllog."\n";  
                } 


            }


            $sqldetail = "";
            foreach($_POST['id_detail_returtb'] as $option => $opt){

              if($_POST['tp'][$option] == 1){
                $nodoktp =$codeTP; 
              }else{
                $nodoktp =''; 
              }

              if($_POST['tp'][$option] == 2){

                $sqldetail.="DELETE FROM  detail_returntb where 
                 id_detail_returtb='".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_detail_returtb'][$option]))))."'";

              }else{

              $sqldetail .= "UPDATE detail_returntb SET 
              rtrtbitem_qty_retur='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option]))))."',
              rtrtbitem_qty_retur_verifikasi='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option]))))."',
              rtrtbitem_remarks_retur_verifikasi='".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barangverifikasi'][$option]))))."',
             rtrtb_doktp='$nodoktp',
              updated_date=getdate()
              WHERE
              header_idrtrtb='$id_tb' 
               and rtrtbitem_id='".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."'
               and id_detail_returtb='".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_detail_returtb'][$option]))))."'";

              }

               $sqldetail .= ";";

            }

            $sqldetailfix = rtrim($sqldetail,";");
            $stmt1 = sqlsrv_query($conn,$sqldetailfix);

           $sqllog = "INSERT  INTO log_tb (
            log_idtb,
            log_flagretur,
            note_tb,
            created_date,
            created_by
            ) VALUES (
             '$id_tb', 
             '$flagpengembalian', 
             'VERIFIKASI RETUR REQUEST-".htmlspecialchars(addslashes(trim(strip_tags($note_request_verifikasi))))."',
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


                $sqldetail = "SELECT
                *,CASE WHEN rtrtbflag_tp=1 THEN 'Dilakukan Transfer Putus' ELSE '' END as remarks,
                CONVERT ( CHAR ( 10 ), rtrtbitem_expired_retur, 126 ) expiredpengembalian 
              FROM
                detail_tb a inner join detail_returntb b on a.id_detailtb=b.header_detailid 
              WHERE
                header_idtb = '$id_tb' and flag ='$flagpengembalian'";
                $stmtdetail = sqlsrv_query( $conn, $sqldetail );
                if( $stmtdetail === false) {
                    die( print_r( sqlsrv_errors(), true) );
                }
                $no=0;

                if($status =="TPS"){
                  $statustps ='Untuk permintaan Transfer Putus Store Telah di setujui oleh store '.$store_destination.' ';
                }else if($status =="NO TPS"){
                  $statustps ='Untuk permintaan Transfer Putus Store Telah di tolak oleh store '.$store_destination.' ';
                }else{
                  $statustps ='';
                }
                $laporan="<h4><b>Data Pengembalian Transfer Balik Store</b></h4>";
                $laporan .="<br/>";
                $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
                $laporan .="<tr style=\"bgcolor: blue;\">";
                $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Pengembalian</td><td>Kadaluarsa</td><td>Qty Verifikasi Pengembalian</td><td>Remarks TP</td>";
                $laporan .="</tr>";
              
               while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
                {
                  $fixqtyver =0;
                  $fixqtyver = $rowdetail['rtrtbitem_qty_retur_verifikasi'];
                    $laporan .="<tr>";
                    $laporan .="<td>".$rowdetail['tbitem_code']."</td><td>".$rowdetail['tbitem_name']."</td><td>".$rowdetail['tbitem_uom']."</td><td>".$rowdetail['tbitem_cat']."</td><td>".$rowdetail['tbitem_reason']."</td><td>".number_format($rowdetail['rtrtbitem_qty_retur'],2,'.',',')."</td><td>".$rowdetail['expiredpengembalian']."</td><td>".number_format($fixqtyver,2,'.',',')."</td><td>".$rowdetail['remarks']."</td>";
                    $laporan .="</tr>";
                }
                $laporan .="</table>";
                

                $name  ='Info Verifikasi Pengembalian Barang';
                $email = 'info.voucherrequest@multirasa.co.id';
                $subject =''.$status_request.' Permintaan Verifikasi Pengembalian Barang Dengan No #'.$code.'';
                $body ='Dear Store '.$store_request.' <br><br> 
                             Permintaan Barang <br>dengan No <b>'.$code.' </b> telah dilakukan verifikasi
                             Oleh Store '.$store_destination.' 
                             <br><br>   
                             '.$statustps.'
                             <br><br>   
                             '.$laporan.'
                             <br><br> 
                             Note : <br> 
                             '.$note_request_verifikasi.'
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

                if($codeTP ==''){
                  $fixcode='';
                }else{
                  $fixcode='Berikut untuk kode transfer putus storenya : '.$codeTP;
                }
                $_SESSION['pesan'] = '<b>Permintaan Berhasil Di Proses! '.$fixcode.'</b>';
                // header('Location: listrequesttbs.php');    
                echo "Transaction was committed.\n"; 
                   echo "Transaction was rolled back.\n"; 
                echo $sqlheader."\n";  
                echo $sqldetail."\n";  
                echo $sqllog."\n";

           }  
           else  
           {  
                sqlsrv_rollback($conn);  
                $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b> ';
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

}else if($selisi == 0){
  
  echo "balance";

  // if($kelebihan == 0){

              /* Initiate transaction. */  
        /* Exit script if transaction cannot be initiated. */  
        if ( sqlsrv_begin_transaction( $conn ) === false )  
        {  
             echo "Could not begin transaction.\n";  
             die( print_r( sqlsrv_errors(), true ));  
        }  

        $sqlheader = "UPDATE header_tb SET
                  reqtb_user_retur='".$status_request."',
                      reqtb_user_retur_date=getdate(),
                      reqtb_destination_retur_verifikasi='Verifikasi',
                    reqtb_destination_retur_verifikasi_date=getdate(),
                    reqtb_destination_retur_verifikasi_note='".$note_request_verifikasi."',
                    reqtb_flag_plus=$flagkelebihan,
                    reqtb_destination_retur_plus=$statuskelebihan,
                    status_progress=5
                  WHERE id_tb='$id_tb'";

              $stmt = sqlsrv_query( $conn, $sqlheader);

              if($status =='TPS'){

                $bulan = date('m');
                $tahun = date ('Y');
                // $query = "SELECT MAX(uniqueorder) AS maxKode FROM order_voucher WHERE YEAR(date_order)='$tahun' and MONTH(date_order) = '$bulan'";
                $sqlcode="SELECT MAX(reqtp_code) AS maxKode FROM header_tp
                          WHERE YEAR(created_date)='$tahun' and MONTH(created_date) = '$bulan' and  reqtp_user= '".$store_request."' and reqtp_type=1";
                $hasilcode = sqlsrv_query( $conn, $sqlcode );
                $rowcode = sqlsrv_fetch_array( $hasilcode, SQLSRV_FETCH_ASSOC);
      
                $noUrut= $rowcode["maxKode"] + 1;
                $kode =  sprintf("%03s", $noUrut);
                $nomor = "/".$store_request."/TPS/".$bulan."/".$tahun;
                $codeTP = $kode.$nomor;
  
              $sqlheader = "INSERT  INTO header_tp (
                            reqtp_code,
                            reqtp_date,
                            reqtp_type,
                            reqtp_user,
                            reqtp_destination,
                            reqtp_note,
                            reqtp_destination_approve,
                            reqtp_destination_approve_date,
                            reqtp_user_verifikasi,
                            reqtp_user_verifikasi_date,
                            reqtp_ck_destination,
                            status_progress,
                            created_date,
                            created_by
                            ) VALUES (
                            '".htmlspecialchars(addslashes(trim(strip_tags($codeTP))))."', 
                            '".htmlspecialchars(addslashes(trim(strip_tags($today))))."', 
                            '".htmlspecialchars(addslashes(trim(strip_tags(1))))."', 
                            '".htmlspecialchars(addslashes(trim(strip_tags( $store_request))))."', 
                            '".htmlspecialchars(addslashes(trim(strip_tags($store_destination))))."', 
                            'Auto Ganerate TPS ACT date : $reqtb_req', 
                            'Approved',
                            getdate(),
                            'Verifikasi',
                            getdate(),
                            '$CK',
                            3,
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
                    tpitem_qty_approve,
                    tpitem_qty_verifikasi_good,
                    tpitem_qty_verifikasi_not_good,
                    created_date,
                    created_by) values ";
  
                  foreach($_POST['id_detail_returtb'] as $option => $opt){
  
                    if($_POST['tp'][$option] == 1){
                                $sqldetail .= "(
                                '$lastinsertid',
                                '".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."',
                                '".htmlspecialchars(addslashes(trim(strip_tags($_POST['item_code'][$option]))))."',
                                '".htmlspecialchars(addslashes(trim(strip_tags($_POST['item_name'][$option]))))."',
                                '".htmlspecialchars(addslashes(trim(strip_tags($_POST['item_uom'][$option]))))."',
                                '".htmlspecialchars(addslashes(trim(strip_tags($_POST['item_cat'][$option]))))."',
                                '".htmlspecialchars(addslashes(trim(strip_tags($_POST['tbitem_reason'][$option]))))."',
                                '".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option]))))."',
                                '".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option]))))."',
                                '".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option]))))."',
                                '".htmlspecialchars(addslashes(trim(strip_tags(0))))."',
                                getdate(),
                                '$created_by')";
                                $sqldetail .= ",";
  
                    }
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
                    'AUTO GANERATE TPS',
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

                        
                      $sqldetail = "SELECT *,convert(char(10),tpitem_expired,126) expired FROM detail_tp where header_idtp='$lastinsertid'";
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
                      $subject =''.$status_request.' Permintaan Barang Dengan No #'.$codeTP.'';
                      $body ='Dear Store '.$store_destination.' <br><br> 
                                   Permintaan Barang <br>dengan No <b>'.$codeTP.' </b> telah dilakukan verifikasi
                                  Oleh Store '.$store_request.' 
                                  <br><br>   
                                  '.$laporan.'
                                  <br><br> 
                                  Note : 
                                  <br><br><br> 
                                  Terimakasih.';

                      $mail1 = new PHPMailer();

                      //SMTP Settings
                      $mail1->isSMTP();
                      $mail1->Host = "mail.multirasa.co.id";
                      $mail1->SMTPAuth = true;
                      $mail1->Username = "info.voucherrequest@multirasa.co.id"; //enter you email address
                      $mail1->Password = 'yoshimulti'; //enter you email password
                      $mail1->Port = 465;
                      $mail1->SMTPSecure = "ssl";
              
                      //Email Settings
                      $mail1->isHTML(true);
                      $mail1->setFrom($email, $name);
                      $mail1->addAddress($emailstore); 
                      $mail1->addAddress($emailstoredestination); 
                 if($rowck['CK'] =='CK JAKARTA'){  
                    // $cc ='angga.aditya@multirasa.co.id'; 
                    // $cc1 ='gabriella.tardini@multirasa.co.id';
                    // $cc2 ='alexius.sugeng@multirasa.co.id';
                    $cc ='yoshinoya.transfertoko.jkt@multirasa.co.id';
                    $mail1->addCC($cc);
                    // $mail->addCC($cc1);
                    // $mail->addCC($cc2);
                  }else if($rowck['CK'] =='CK SURABAYA'){ 
                    // $cc ='ck.admin.sby2@multirasa.co.id';
                    // $cc1 ='henri.hakim@multirasa.co.id';
                    // $cc2 ='januar.kusriwahjudi@multirasa.co.id';
                    $cc ='yoshinoya.transfertoko.sby@multirasa.co.id';
                    $mail1->addCC($cc);
                    // $mail->addCC($cc1);
                    // $mail->addCC($cc2);
                  }else{
                  }
                      // $mail1->addCC('angga.aditya@multirasa.co.id');
                      $mail1->Subject = ("$subject");
                      $mail1->Body = $body;

                      if ($mail1->send()) {
                          $status = "success";
                          $response = "Email is sent!";
                      } else {
                          $status = "failed";
                          $response = "Something is wrong: <br><br>" . $mail1->ErrorInfo;
                      } 
  
                        echo "Transaction was committed.\n<br>";  
                  }  
                  else  
                  {  
                        sqlsrv_rollback($conn);  
                        echo "Transaction was rolled back.\n"; 
                        echo $sqlheader."\n";  
                        echo $sqldetail."\n";  
                        echo $sqllog."\n";  
                  } 
  
  
              }
  

            $sqldetail = "";
            foreach($_POST['id_detail_returtb'] as $option => $opt){

              if($_POST['tp'][$option] == 1){
                $nodoktp =$codeTP; 
              }else{
                $nodoktp =''; 
              }

              if($_POST['tp'][$option] == 2){

                $sqldetail.="DELETE FROM  detail_returntb where 
                 id_detail_returtb='".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_detail_returtb'][$option]))))."'";

              }else{

              $sqldetail .= "UPDATE detail_returntb SET 
              rtrtbitem_qty_retur='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option]))))."',
              rtrtbitem_qty_retur_verifikasi='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option]))))."',
              rtrtbitem_remarks_retur_verifikasi='".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barangverifikasi'][$option]))))."',
             rtrtb_doktp='$nodoktp',
              updated_date=getdate()
              WHERE
              header_idrtrtb='$id_tb' 
               and rtrtbitem_id='".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."'
               and id_detail_returtb='".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_detail_returtb'][$option]))))."'";
               
              }
             
               $sqldetail .= ";";

            }

              $sqldetailfix = rtrim($sqldetail,";");
              $stmt1 = sqlsrv_query($conn,$sqldetailfix);

             $sqllog = "INSERT  INTO log_tb (
              log_idtb,
              log_flagretur,
              note_tb,
              created_date,
              created_by
              ) VALUES (
               '$id_tb', 
               '$flagpengembalian', 
               'VERIFIKASI RETUR BALANCE REQUEST-".$note_request_verifikasi."',
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

                  $sqldetail = "SELECT
                  *,CASE WHEN rtrtbflag_tp=1 THEN 'Dilakukan Transfer Putus' ELSE '' END as remarks,
                  CONVERT ( CHAR ( 10 ), rtrtbitem_expired_retur, 126 ) expiredpengembalian 
                FROM
                  detail_tb a inner join detail_returntb b on a.id_detailtb=b.header_detailid 
                WHERE
                  header_idtb = '$id_tb' and flag ='$flagpengembalian'";
                  $stmtdetail = sqlsrv_query( $conn, $sqldetail );
                  if( $stmtdetail === false) {
                      die( print_r( sqlsrv_errors(), true) );
                  }
                  $no=0;

                  if($status =="TPS"){
                    $statustps ='Untuk permintaan Transfer Putus Store Telah di setujui oleh store '.$store_destination.' ';
                  }else if($status =="NO TPS"){
                  $statustps ='Untuk permintaan Transfer Putus Store Telah di tolak oleh store '.$store_destination.' ';
                }else{
                    $statustps ='';
                  }
  
                  $laporan="<h4><b>Data  Pengembalian Transfer Balik Store</b></h4>";
                  $laporan .="<br/>";
                  $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
                  $laporan .="<tr style=\"bgcolor: blue;\">";
                  $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Pengembalian</td><td>Kadaluarsa</td><td>Qty Verifikasi Pengembalian</td><td>Remarks TP</td>";
                  $laporan .="</tr>";
                
                 while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
                  {
                    $fixqtyver =0;
                    $fixqtyver = $rowdetail['rtrtbitem_qty_retur_verifikasi'];
                      $laporan .="<tr>";
                      $laporan .="<td>".$rowdetail['tbitem_code']."</td><td>".$rowdetail['tbitem_name']."</td><td>".$rowdetail['tbitem_uom']."</td><td>".$rowdetail['tbitem_cat']."</td><td>".$rowdetail['tbitem_reason']."</td><td>".number_format($rowdetail['rtrtbitem_qty_retur'],2,'.',',')."</td><td>".$rowdetail['expiredpengembalian']."</td><td>".number_format($fixqtyver,2,'.',',')."</td><td>".$rowdetail['remarks']."</td>";
                      $laporan .="</tr>";
                  }
                  $laporan .="</table>";

                  $name  ='Info Verifikasi Barang';
                  $email = 'info.voucherrequest@multirasa.co.id';
                  $subject =''.$status_request.' Pengembalian Barang Dengan No #'.$code.'';
                  $body ='Dear Store '.$store_request.' <br><br> 
                               Pengembalian Barang <br>dengan No Request  <b>'.$code.' </b> telah dilakukan verifikasi
                               Oleh Store '.$store_destination.' 
                               <br><br>   
                               '.$statustps.'
                               <br><br>   
                               '.$laporan.'
                               <br><br> 
                               Note : <br> 
                               '.$note_request_verifikasi.'
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
                  $mail->addAddress($emailstore); 
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
                  if($codeTP ==''){
                    $fixcode='';
                  }else{
                    $fixcode='Berikut untuk kode transfer putus storenya : '.$codeTP;
                  }
                  $_SESSION['pesan'] = '<b>Permintaan Berhasil Di Proses! '.$fixcode.'</b>';
                  echo "Transaction was committed.\n";  
                echo $sqlheader."\n";  
                echo $sqldetail."\n";  
                echo $sqllog."\n";
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

      // }else if($kelebihan > 0){

      //                 /* Initiate transaction. */  
      //   /* Exit script if transaction cannot be initiated. */  
      //   if ( sqlsrv_begin_transaction( $conn ) === false )  
      //   {  
      //        echo "Could not begin transaction.\n";  
      //        die( print_r( sqlsrv_errors(), true ));  
      //   }  

      //   $sqlheader = "UPDATE header_tb SET
      //             reqtb_user_retur='".$status_request."',
      //                 reqtb_user_retur_date=getdate(),
      //                 reqtb_destination_retur_verifikasi='Pending',
      //               reqtb_destination_retur_verifikasi_date=getdate(),
      //               reqtb_destination_retur_verifikasi_note='".$note_request_verifikasi."',
      //               reqtb_flag_plus=$flagkelebihan,
      //               reqtb_destination_retur_plus=$statuskelebihan,
      //               status_progress=4
      //             WHERE id_tb='$id_tb'";

      //         $stmt = sqlsrv_query( $conn, $sqlheader);

      //       $sqldetail = "";
      //       foreach($_POST['id_detail_returtb'] as $option => $opt){

      //         $sqldetail .= "UPDATE detail_returntb SET 
      //         -- rtrtbitem_qty_retur='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option]))))."',
      //         rtrtbitem_qty_retur_plus='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtyverifikasiplus'][$option]))))."',
      //         rtrtbitem_qty_retur_verifikasi='".htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option]))))."',
      //         rtrtbitem_remarks_retur_verifikasi='".htmlspecialchars(addslashes(trim(strip_tags($_POST['keterangan_barangverifikasi'][$option]))))."',
      //         updated_date=getdate()
      //         WHERE
      //         header_idrtrtb='$id_tb' 
      //          and rtrtbitem_id='".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_barang'][$option]))))."'
      //          and id_detail_returtb='".htmlspecialchars(addslashes(trim(strip_tags($_POST['id_detail_returtb'][$option]))))."'";
      //         $sqldetail .= ";";

      //       }

      //         $sqldetailfix = rtrim($sqldetail,";");
      //         $stmt1 = sqlsrv_query($conn,$sqldetailfix);

      //        $sqllog = "INSERT  INTO log_tb (
      //         log_idtb,
      //         log_flagretur,
      //         note_tb,
      //         created_date,
      //         created_by
      //         ) VALUES (
      //          '$id_tb', 
      //          '$flagpengembalian', 
      //          'VERIFIKASI RETUR BALANCE REQUEST-".$note_request_verifikasi."',
      //          getdate(), 
      //          '$created_by'
      //           )";

      //         // $paramslog = array(
      //         //       $lastinsertid,
      //         //       $created_by
      //         // );
      //         // $stmt2 = sqlsrv_query( $conn, $sqllog, $paramslog);

      //         $stmt2 = sqlsrv_query( $conn, $sqllog);

      //        if( $stmt && $stmt1 && $stmt2 )  
      //        {  
      //             sqlsrv_commit($conn);  

      //             $cc="angga.aditya@multirasa.co.id";

      //             $sqldetail = "SELECT
      //             *,
      //             CONVERT ( CHAR ( 10 ), rtrtbitem_expired_retur, 126 ) expiredpengembalian 
      //           FROM
      //             detail_tb a inner join detail_returntb b on a.id_detailtb=b.header_detailid 
      //           WHERE
      //             header_idtb = '$id_tb' and flag ='$flagpengembalian'";
      //             $stmtdetail = sqlsrv_query( $conn, $sqldetail );
      //             if( $stmtdetail === false) {
      //                 die( print_r( sqlsrv_errors(), true) );
      //             }
      //             $no=0;
  
      //             $laporan="<h4><b>Data  Pengembalian Transfer Balik Store</b></h4>";
      //             $laporan .="<br/>";
      //             $laporan .="<table width=\"100%\" border=\"1\" align=\"center\" cellpadding=\"3\" cellspacing=\"0\">";
      //             $laporan .="<tr style=\"bgcolor: blue;\">";
      //             $laporan .="<td>Kode</td><td>Nama</td><td>Satuan</td><td>Jenis Barang</td><td>Alasan</td><td>Qty Pengembalian</td><td>Kadaluarsa</td><td>Qty Verifikasi Pengembalian</td>";
      //             $laporan .="</tr>";
                
      //            while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC))
      //             {
      //               $fixqtyver =0;
      //               $fixqtyver = $rowdetail['rtrtbitem_qty_retur_verifikasi'];
      //                 $laporan .="<tr>";
      //                 $laporan .="<td>".$rowdetail['tbitem_code']."</td><td>".$rowdetail['tbitem_name']."</td><td>".$rowdetail['tbitem_uom']."</td><td>".$rowdetail['tbitem_cat']."</td><td>".$rowdetail['tbitem_reason']."</td><td>".$rowdetail['rtrtbitem_qty_retur']."</td><td>".$rowdetail['expiredpengembalian']."</td><td>".$fixqtyver."</td>";
      //                 $laporan .="</tr>";
      //             }
      //             $laporan .="</table>";

      //             $name  ='Info Verifikasi Barang';
      //             $email = 'info.voucherrequest@multirasa.co.id';
      //             $subject =''.$status_request.' Pengembalian Barang Dengan No #'.$code.'';
      //             $body ='Dear Store '.$store_request.' <br><br> 
      //                          Pengembalian Barang <br>dengan No Request  <b>'.$code.' </b> telah dilakukan verifikasi
      //                          Oleh Store '.$store_destination.' 
      //                          <br><br>   
      //                          '.$laporan.'
      //                          <br><br> 
      //                          Note : <br> 
      //                          '.$note_request_verifikasi.'
      //                          <br><br><br> 
      //                          Terimakasih.';

      //             $mail = new PHPMailer();

      //             //SMtb Settings
      //             $mail->isSMTP();
      //             $mail->Host = "mail.multirasa.co.id";
      //             $mail->SMTPAuth = true;
      //             $mail->Username = "info.voucherrequest@multirasa.co.id"; //enter you email address
      //             $mail->Password = 'yoshimulti'; //enter you email password
      //             $mail->Port = 465;
      //             $mail->SMTPSecure = "ssl";
          
      //             //Email Settings
      //             $mail->isHTML(true);
      //             $mail->setFrom($email, $name);
      //             $mail->addAddress($emailstore); 
      //             // if($status_request =='Approved'){
      //             //   $emailcc =  $mail->addCC($cc);
      //             // }else{
      //             //   $emailcc ='';
      //             // }//enter you email address
      //             $mail->Subject = ("$subject");
      //             $mail->Body = $body;

      //             if ($mail->send()) {
      //                 $status = "success";
      //                 $response = "Email is sent!";
      //             } else {
      //                 $status = "failed";
      //                 $response = "Something is wrong: <br><br>" . $mail->ErrorInfo;
      //             }
      //             $_SESSION['pesan'] = '<b>Permintaan Berhasil Di Proses!</b>';
      //             echo "Transaction was committed.\n";  
      //        }  
      //        else  
      //        {  
      //             sqlsrv_rollback($conn);  
      //             $_SESSION['pesan'] = '<b>Permintaan Gagal Di Proses!</b>';
      //             echo "Transaction was rolled back.\n"; 
      //             echo $sqlheader."\n";  
      //             echo $sqldetail."\n";  
      //             echo $sqllog."\n";  
      //        } 

      //   /* Free statement and connection resources. */  
      //   sqlsrv_free_stmt( $stmt);  
      //   sqlsrv_free_stmt( $stmt1);  
      //   sqlsrv_free_stmt( $stmt2);  
      //   sqlsrv_close( $conn);

      // }

}else if($selisi > 0){
  echo"kelebihan pengembalian!";
}else{
  echo"tidak ada proses!";
}

 
?>