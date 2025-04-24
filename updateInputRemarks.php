<?php
include 'db.php'; 

$id = $_POST['id'];
$remarks = $_POST['remarks'];

$sql = "UPDATE InventoriAssetHeader SET RemarksIAC = ? WHERE ID = ?";
$params = array($remarks, $id);
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt) {
    echo "success";
} else {
    http_response_code(500);
    echo "error";
}
?>