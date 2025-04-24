<?php
header('Content-Type: application/json');
include 'db.php';

$response = [];

try {
    $jenis_asset = isset($_GET['jenis_asset']) ? $_GET['jenis_asset'] : '';
    $query = "SELECT Id, AssetReason,AssetKondisi FROM MasterAssetReasons WHERE DocTrans = ?";
    $params = [$jenis_asset];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        throw new Exception(print_r(sqlsrv_errors(), true));
    }

    $items = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $items[] = [
            "id" => $row['AssetReason'],
            "text" => $row['AssetReason'],
            'kondisi' => isset($row['AssetKondisi']) ? trim($row['AssetKondisi']) : ''
        ];
    }

    $response = ["results" => $items];
} catch (Exception $e) {
    $response = ["error" => $e->getMessage()];
}

echo json_encode($response);
?>