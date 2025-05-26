<?php
header('Content-Type: application/json');
include 'db.php'; 

$response = [];
$user = $_SESSION['nama'];
try {
    $query = "SELECT Warehouse,ItemCode, ItemName, ItemUom,AssetQuantity,AssetConditionOk,AssetConditionNonOk,TransFlag FROM MasterAssets WHERE Warehouse='$user'
    AND AssetQuantity > 0";
    $stmt = sqlsrv_query($conn, $query);

    if ($stmt === false) {
        throw new Exception(print_r(sqlsrv_errors(), true));
    }

    $items = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $items[] = $row;
    }

    $response = ["data" => $items];
} catch (Exception $e) {
    $response = ["error" => $e->getMessage()];
}

echo json_encode($response);
?>
