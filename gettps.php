<?php
include "db.php";

$tps =$_GET['q'];

$sql = "SELECT reqtp_code FROM header_tp where reqtp_code='".$tps."'";
$stmt = sqlsrv_query( $conn, $sql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}

$json = [];
while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
     $json[] = [
          'id'=>$row['reqtp_code'],
           'text'=>$row['reqtp_code']
     ];
}

echo json_encode($json);






?>