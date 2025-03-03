<?php
include "db.php";
// error_reporting(0);
$item =$_GET['q'];

if($_GET['div'] =='CK JAKARTA' OR $_GET['div']=='CK SURABAYA'){
     $div='CK';
}else if($_GET['div'] =='IT JAKARTA' OR $_GET['div']=='IT SURABAYA'){
     $div='IT';
}else if($_GET['div'] =='ENG JAKARTA' OR $_GET['div']=='ENG SURABAYA'){
     $div='ENG';
}else if($_GET['div'] =='GA JAKARTA' OR $_GET['div']=='GA SURABAYA'){
     $div='GA';
}else{
     $div = $_GET['div'];
}

if($_GET['tipe'] !== ''){
     if($_GET['tipe'] == 4){
      $tipe ="and sap_flag in (1,2)";
     }else{
     $tipe ="and sap_flag='".$_GET['tipe']."'";
     }
}else{
     $tipe='';
}

if($_GET['jenis_permintaan'] == 3){
     $fixjenispermintaan = 1;
}else{
     $fixjenispermintaan = $_GET['jenis_permintaan'];
}

// $sql = "SELECT  upper(itemcode) itemcode,itemname,upper(InvntryUom) InvntryUom from oitm where itemname LIKE '%".$item."%'
// group by itemcode,itemname, InvntryUom ";
$sql = "SELECT  id_mst_item,upper(item_code) item_code,item_name,upper(item_uom) item_uom,item_cat,exp_flag,kondisi_flag from mst_item where Active=0 and item_type='".$fixjenispermintaan."' and div_name='".$div."' ".$tipe." and  (item_name LIKE '%".$item."%' OR item_code LIKE '%".$item."%')
group by id_mst_item,item_code,item_name, item_uom,item_cat,exp_flag,kondisi_flag";
$stmt = sqlsrv_query( $conn, $sql );
// if( $stmt === false) {
//     die( print_r( sqlsrv_errors(), true) );
// }

$json = [];
while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
     $json[] = [
          'id'=>$row['item_name'],
           'text'=>$row['item_code'].' - ' .html_entity_decode($row['item_name']),
          'uom'=>$row['item_uom'],
          'item_cat'=>$row['item_cat'],
          'exp_flag'=>$row['exp_flag'],
          'kondisi_flag'=>$row['kondisi_flag'],
          'id_mst_item'=>$row['id_mst_item'],
          'item_code'=>$row['item_code']
     ];
}

echo json_encode($json);

// echo $sql;




?>