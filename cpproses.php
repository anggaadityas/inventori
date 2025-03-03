<?php
session_start();
date_default_timezone_set("Asia/Bangkok");
$hostName = "192.168.2.136";
$username = "root";
$password = "aas260993";
$dbname = "voucher_trial";
$mysqli = new mysqli($hostName, $username, $password, $dbname);

$uid =  $_SESSION["uid"];
$password = md5($_POST['password']);

$sql = "UPDATE mst_user SET 
password = '".$password."',
updated_at=now()
WHERE id_user='$uid'";
$result = $mysqli->query($sql);
if($result){
$_SESSION['pesan'] = '<b>Kata Sandi Berhasil Di Proses, Terimakasih</b>';
header('Location: changepassword.php');
}else{
$_SESSION['pesan'] = '<b>Kata Sandi Gagal Di Proses, Terimakasih</b>';
header('Location: changepassword.php');
}

?>