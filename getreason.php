<?php

include "db.php";

$idreqtype =$_POST['jenis_permintaan'];

$sql = "SELECT * FROM mst_req_reason where reqtype_id='$idreqtype' and status=0";
$stmt = sqlsrv_query( $conn, $sql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}
  
 echo"<option value='' selected>-- Pilih Alasan --</option>";

 while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){

 echo "<option value='".$row['reason_name']."'>".$row['reason_name']."</option>";

}

?>