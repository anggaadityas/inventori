<?php
session_start();
date_default_timezone_set("Asia/Bangkok");

$serverName = "SEVER-SAPB1"; 
$connectionInfo = array( "Database"=>"MRN_PRODUCTION", "UID"=>"sa", "PWD"=>"B1Admin");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

// if( $conn ) {
//      echo "Connection established.<br />";
// }else{
//      echo "Connection could not be established.<br />";
//      die( print_r( sqlsrv_errors(), true));
// }


$item =$_GET['q'];


$sql = "SELECT  upper(itemcode) itemcode,itemname,upper(InvntryUom) InvntryUom from oitm where (itemname LIKE '%".$item."%' OR itemcode LIKE '%".$item."%')
group by itemcode,itemname, InvntryUom ";
// $sql = "SELECT  id_mst_item,upper(item_code) item_code,item_name,upper(item_uom) item_uom,item_cat,exp_flag,kondisi_flag from mst_item where Active=0 and item_type='".$fixjenispermintaan."' and div_name='".$div."' ".$tipe." and  (item_name LIKE '%".$item."%' OR item_code LIKE '%".$item."%')
// group by id_mst_item,item_code,item_name, item_uom,item_cat,exp_flag,kondisi_flag";
$stmt = sqlsrv_query( $conn, $sql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}

// echo $sql;

$json = [];
while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
     $json[] = [
          'id'=>$row['itemcode'],
          'text'=>$row['itemcode'].' - ' .$row['itemname'],
          'uom'=>$row['InvntryUom'],
          'itemname'=>$row['itemname']
     ];
}

echo json_encode($json);

// echo $sql;




?>