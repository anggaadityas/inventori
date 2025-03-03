<?php
include "db.php";
$store = $_SESSION['nama'];
$div_area = $_SESSION['area_div'];
if ($div_area == 'CK JAKARTA' or $div_area == 'CK SURABAYA') {
  $div = "(a.div_name ='CK' OR a.div_name ='STORE')";
} else if ($store == 'IT JAKARTA' or $div_area == 'IT SURABAYA') {
  $div = "a.div_name ='IT'";
} else if ($div_area == 'ENG JAKARTA' or $div_area == 'ENG SURABAYA') {
  $div = "a.div_name ='ENG'";
} else if ($div_area == 'GA JAKARTA' or $div_area == 'GA SURABAYA') {
  $div = "a.div_name ='GA'";
} else {
  $div = $div_area;
}
$requestData = $_REQUEST;

$columns = array(
  0 => 'id_mst_item',
  1 => 'item_type',
  2 => 'div_name',
  3 => 'item_cat',
  4 => 'item_code',
  5 => 'item_name',
  6 => 'item_uom',
  7 => 'sap_flag',
  8 => 'exp_flag',
  9 => 'kondisi_flag',
  10 => 'status',
  11 => 'action'
);

$sql  = "SELECT * 
FROM 
( 
      SELECT 
				a.id_mst_item,
				b.req_type_name,
        CASE
        WHEN a.div_name='STORE' THEN 'CK'
        ELSE a.div_name END as div_name,
				CASE
				WHEN sap_flag=1 THEN 'System'
				WHEN sap_flag=2 THEN 'Non System'
        WHEN sap_flag=3 THEN 'Wadah'
        WHEN sap_flag=5 THEN 'Damage'
				ELSE '' END as sap_flag,
				a.item_cat,
				a.item_code,
				a.item_name,
				a.item_uom,
				CASE
				WHEN kondisi_flag=0 THEN 'Good & Non Good'
				WHEN kondisi_flag=1 THEN 'Good'
				WHEN kondisi_flag=2 THEN 'Non Good'
				ELSE '' END as kondisi_flag,
					CASE
				WHEN exp_flag=0 THEN 'Tidak Ada Kadarluarsa'
				WHEN exp_flag=1 THEN 'Kadarluarsa'
				ELSE '' END as exp_flag,
    a.active,
        ROW_NUMBER() OVER (ORDER BY id_mst_item desc) as rowNum 
      FROM mst_item a inner join mst_req_type b on a.item_type=b.id_mst_type
      where $div
) sub ";
$params = array();
$options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$query = sqlsrv_query($conn, $sql, $params, $options) or die("data.php: get InventoryItems" . $sql . "");
$totalData = sqlsrv_num_rows($query);
$totalFiltered = $totalData;

if (!empty($requestData['search']['value'])) {
  $sql  = "SELECT * 
 FROM 
 ( 
       SELECT 
     a.id_mst_item,
     b.req_type_name,
     CASE
        WHEN a.div_name='STORE' THEN 'CK'
        ELSE a.div_name END as div_name,
     CASE
     WHEN sap_flag=1 THEN 'System'
     WHEN sap_flag=2 THEN 'Non System'
       WHEN sap_flag=3 THEN 'Wadah'
       WHEN sap_flag=5 THEN 'Damage'
     ELSE '' END as sap_flag,
     a.item_cat,
     a.item_code,
     a.item_name,
     a.item_uom,
     CASE
     WHEN kondisi_flag=0 THEN 'Good & Non Good'
     WHEN kondisi_flag=1 THEN 'Good'
     WHEN kondisi_flag=2 THEN 'Non Good'
     ELSE '' END as kondisi_flag,
      CASE
     WHEN exp_flag=0 THEN 'Tidak Ada Kadarluarsa'
     WHEN exp_flag=1 THEN 'Kadarluarsa'
     ELSE '' END as exp_flag,
     a.active,
         ROW_NUMBER() OVER (ORDER BY id_mst_item desc) as rowNum 
       FROM mst_item a inner join mst_req_type b on a.item_type=b.id_mst_type ";
  $sql .= "WHERE $div and (item_code LIKE '" . $requestData['search']['value'] . "%' ";
  $sql .= " OR div_name LIKE '" . $requestData['search']['value'] . "%' ";
  $sql .= " OR item_name LIKE '" . $requestData['search']['value'] . "%' ";
  $sql .= " OR sap_flag LIKE '" . $requestData['search']['value'] . "%' )";
  $sql .= ") sub ";
  $params = array();
  $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
  $query = sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO1" . $sql . "");
  $totalFiltered = sqlsrv_num_rows($query);
  $sql .= " WHERE rowNum > " . $requestData['start'] . " AND rowNum <= " . $requestData['start'] . " + " . $requestData['length'] . "";
  $params = array();
  $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
  $query = sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO2" . $sql . "");
} else {

  $sql  = "SELECT * 
 FROM 
 ( 
       SELECT 
     a.id_mst_item,
     b.req_type_name,
      CASE
        WHEN a.div_name='STORE' THEN 'CK'
        ELSE a.div_name END as div_name,
     CASE
     WHEN sap_flag=1 THEN 'System'
     WHEN sap_flag=2 THEN 'Non System'
     WHEN sap_flag=3 THEN 'Wadah'
     WHEN sap_flag=5 THEN 'Damage'
     ELSE '' END as sap_flag,
     a.item_cat,
     a.item_code,
     a.item_name,
     a.item_uom,
     CASE
     WHEN kondisi_flag=0 THEN 'Good & Non Good'
     WHEN kondisi_flag=1 THEN 'Good'
     WHEN kondisi_flag=2 THEN 'Non Good'
     ELSE '' END as kondisi_flag,
      CASE
     WHEN exp_flag=0 THEN 'Tidak Ada Kadarluarsa'
     WHEN exp_flag=1 THEN 'Kadarluarsa'
     ELSE '' END as exp_flag,
     a.active,
         ROW_NUMBER() OVER (ORDER BY id_mst_item desc) as rowNum 
       FROM mst_item a inner join mst_req_type b on a.item_type=b.id_mst_type
       where $div
 ) sub ";
  $sql .= "where rowNum > " . $requestData['start'] . " AND rowNum <= " . $requestData['start'] . " + " . $requestData['length'] . "";
  $params = array();
  $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
  $query = sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO3'" . $sql . "'");
}

$data = array();
while ($row = sqlsrv_fetch_array($query)) {
  $nestedData = array();

  if ($row['active'] == 0) {
    $status = 'Aktif';
  } elseif ($row['active'] == 1) {
    $status = 'Tidak Aktif';
  } else {
    $status = '';
  }



  $nestedData[] = $row["req_type_name"];
  $nestedData[] = $row["div_name"];
  $nestedData[] = $row["sap_flag"];
  $nestedData[] = $row["item_code"];
  $nestedData[] = $row["item_name"];
  $nestedData[] = $row["item_uom"];
  $nestedData[] = $row["item_cat"];
  $nestedData[] = $row["kondisi_flag"];
  $nestedData[] = $row["exp_flag"];
  $nestedData[] = $status;
  $nestedData[] = '<a title="Edit Item" class="badge badge-danger" href="edititem.php?id=' . $row["id_mst_item"] . '"><b>Edit Barang</b></a>';
  $data[] = $nestedData;
}

$json_data = array(
  "draw"                   => intval($requestData['draw']),
  "recordsTotal"      => intval($totalData),
  "recordsFiltered"  => intval($totalFiltered),
  "data"                    => $data
);

echo json_encode($json_data);
