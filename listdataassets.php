<?php
include "db.php";
$store1 = $_SESSION['nama'];
$area_div = $_SESSION['area_div'];
$requestData = $_REQUEST;

if ($requestData['searchByJenisPrioritas'] == '') {
	$jenisprioritas = " ";
} else {
	$jenisprioritas = "AND DocPriority = '" . $requestData['searchByJenisPrioritas'] . "'";
}

if ($requestData['searchByJenisSistem'] == '') {
	$jenissistem = " ";
} else {
	$jenissistem = "AND TermsAsset = '" . $requestData['searchByJenisSistem'] . "'";
}

if ($requestData['searchByStore'] == '') {
	$store = " ";
} else {
	$store = "AND WarehouseFrom = '" . $requestData['searchByStore'] . "'";
}

if ($requestData['searchByStartdate'] == '' or $requestData['searchByEnddate'] == '') {
	$tanggalpengiriman = " ";
} else {
	$tanggalpengiriman = "AND convert(char(10),a.DocDate,126) between '" . $requestData['searchByStartdate'] . "' and '" . $requestData['searchByEnddate'] . "' ";
}

if ($requestData['searchByStatusDokumen'] == '') {
	$statusdokumen = " ";
} else {
	$statusdokumen = "AND DocStatus = '" . $requestData['searchByStatusDokumen'] . "'";
}


if ($area_div == 'AM' or $area_div == 'PROJECT' or $area_div == 'CK JAKARTA' or $area_div == 'CK SURABAYA' or $area_div == 'IT JAKARTA' or $area_div == 'IT SURABAYA' or $area_div == 'GA JAKARTA' or $area_div == 'GA SURABAYA' or $area_div == 'ENG JAKARTA' or $area_div == 'ENG SURABAYA' or $area_div == 'IAC') {
	$where = "WHERE (WarehouseFrom is not null or WarehouseFrom='')";
	$filter = "WHERE  (WarehouseFrom is not null or WarehouseFrom='') AND (DocNum LIKE '" . $requestData['search']['value'] . "%' OR DocPriority LIKE '%" . $requestData['search']['value'] . "%') ";
	$filter1 = "WHERE  (WarehouseFrom is not null or WarehouseFrom='') AND " . $jenisprioritas . " " . $jenissistem . " " . $store . " " . $tanggalpengiriman . " " . $statusdokumen . " ";
	$orderby = "ORDER BY a.ID desc";
} else {
	$where = "WHERE (WarehouseFrom='" . $store1 . "' OR WarehouseTo='" . $store1 . "')";
	$filter = "WHERE (WarehouseFrom='" . $store1 . "' OR WarehouseTo='" . $store1 . "') AND (DocNum LIKE '" . $requestData['search']['value'] . "%' OR DocPriority LIKE '%" . $requestData['search']['value'] . "%') ";
	$filter1 = "WHERE  (WarehouseFrom='" . $store1 . "'OR WarehouseTo='" . $store1 . "') " . $jenisprioritas . " " . $jenissistem . " " . $store . " " . $tanggalpengiriman . " " . $statusdokumen . " ";
	$orderby = "ORDER BY a.ID desc";
}

$columns = array(
	0 => 'ID',
	1 => 'DocNum',
	2 => 'DocDate',
	3 => 'WarehouseFrom',
	4 => 'WarehouseTo',
	5 => 'TransName',
	6 => 'TermsAsset',
	7 => 'DocPriority',
	8 => 'Remarks',
	9 => 'ApprovalStatus',
	10 => 'DocStatus',
	11 => 'Action'
);

$sql = "SELECT * 
FROM 
( 
      SELECT 
		a.ID,
        a.DocNum,
		convert(char(10),a.DocDate,126) DocDate,
        a.WarehouseFrom,
		a.WarehouseTo,
        b.TransName,
        a.TermsAsset,
        CASE
    WHEN a.DocPriority=1 THEN 'Normal'
    WHEN a.DocPriority=2 THEN 'Darurat'
    ELSE ''
	END as DocPriority,
        a.Remarks,
        a.ApprovalUser,
		a.ApprovalUserName,
        a.ApprovalProgress,
		a.ApprovalStatus,
		a.StatusDoc,
		a.RemarksIAC,
        ROW_NUMBER() OVER (ORDER BY a.ID DESC) as rowNum 
      FROM InventoriAssetHeader a inner join MasterDocTrans b on a.DocTrans=b.ID
			" . $where . "
) sub " . $where . " ";
$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$query = sqlsrv_query($conn, $sql, $params, $options) or die("data.php: get InventoryItems" . $sql . "");
$totalData = sqlsrv_num_rows($query);
$totalFiltered = $totalData;

if (!empty($requestData['search']['value'])) {
	$sql = "SELECT * 
FROM 
( 
      SELECT 
		a.ID,
        a.DocNum,
		convert(char(10),a.DocDate,126) DocDate,
        a.WarehouseFrom,
		a.WarehouseTo,
        b.TransName,
        a.TermsAsset,
          CASE
    WHEN a.DocPriority=1 THEN 'Normal'
    WHEN a.DocPriority=2 THEN 'Darurat'
    ELSE ''
	END as DocPriority,
        a.Remarks,
        a.ApprovalUser,
		a.ApprovalUserName,
        a.ApprovalProgress,
		a.ApprovalStatus,
		a.StatusDoc,
		a.RemarksIAC,
        ROW_NUMBER() OVER (ORDER BY a.ID DESC) as rowNum 
      FROM InventoriAssetHeader a inner join MasterDocTrans b on a.DocTrans=b.ID";
	$sql .= " " . $filter . "";
	$sql .= ") sub ";
	$params = array();
	$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
	$query = sqlsrv_query($conn, $sql, $params, $options) or die("data.php: get PO1" . $sql . "");
	$totalFiltered = sqlsrv_num_rows($query);
	$sql .= " WHERE rowNum > " . $requestData['start'] . " AND rowNum <= " . $requestData['start'] . " + " . $requestData['length'] . "";
	$params = array();
	$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
	$query = sqlsrv_query($conn, $sql, $params, $options) or die("data.php: get PO2" . $sql . "");

} else if (!empty($_POST['searchByJenisPrioritas']) or !empty($_POST['searchByJenisSistem']) or !empty($_POST['searchByStore']) or !empty($_POST['searchByStartdate']) or !empty($_POST['searchByEnddate']) or !empty($_POST['searchByStatusDokumen'])) {

	$sql = "SELECT * 
	FROM 
	( 
		SELECT 
		a.ID,
        a.DocNum,
		convert(char(10),a.DocDate,126) DocDate,
        a.WarehouseFrom,
		a.WarehouseTo,
        b.TransName,
        a.TermsAsset,
         CASE
    WHEN a.DocPriority=1 THEN 'Normal'
    WHEN a.DocPriority=2 THEN 'Darurat'
    ELSE ''
	END as DocPriority,
        a.Remarks,
        a.ApprovalUser,
		a.ApprovalUserName,
        a.ApprovalProgress,
		a.ApprovalStatus,
		a.StatusDoc,
		a.RemarksIAC,
        ROW_NUMBER() OVER (ORDER BY a.ID DESC) as rowNum 
      FROM InventoriAssetHeader a inner join MasterDocTrans b on a.DocTrans=b.ID";
	$sql .= " " . $filter1 . "";
	$sql .= ") sub ";
	$params = array();
	$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
	$query = sqlsrv_query($conn, $sql, $params, $options) or die("data.php: get PO1" . $sql . "");
	$totalFiltered = sqlsrv_num_rows($query);
	$sql .= " WHERE rowNum > " . $requestData['start'] . " AND rowNum <= " . $requestData['start'] . " + " . $requestData['length'] . " ";
	$params = array();
	$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
	$query = sqlsrv_query($conn, $sql, $params, $options) or die("data.php: get PO2" . $sql . "");

} else {

	$sql = "SELECT * 
	FROM 
	( 
		SELECT
		a.ID,
        a.DocNum,
		convert(char(10),a.DocDate,126) DocDate,
        a.WarehouseFrom,
		a.WarehouseTo,
        b.TransName,
        a.TermsAsset,
         CASE
    WHEN a.DocPriority=1 THEN 'Normal'
    WHEN a.DocPriority=2 THEN 'Darurat'
    ELSE ''
	END as DocPriority,
        a.Remarks,
        a.ApprovalUser,
		a.ApprovalUserName,
        a.ApprovalProgress,
		a.ApprovalStatus,
		a.StatusDoc,
		a.RemarksIAC,
        ROW_NUMBER() OVER (ORDER BY a.ID DESC) as rowNum 
      FROM InventoriAssetHeader a inner join MasterDocTrans b on a.DocTrans=b.ID
			 " . $where . "
	) sub ";
	$sql .= " " . $where . " AND  rowNum > " . $requestData['start'] . " AND rowNum <= " . $requestData['start'] . " + " . $requestData['length'] . "";
	$params = array();
	$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
	$query = sqlsrv_query($conn, $sql, $params, $options) or die("data.php: get PO3'" . $sql . "'");

}

$data = array();
while ($row = sqlsrv_fetch_array($query)) {
	$nestedData = array();

	if ($row["ApprovalUser"] == $_SESSION["uid"]) {
		if ($row["StatusDoc"] == 'Open') {
			$status = $row["ApprovalStatus"] . ' (' . $row["ApprovalUserName"] . ') ' . ' - ' . $row["StatusDoc"];
			$approval = '<a title="Verfikasi Dokumen" class="badge badge-danger" href="viewapprovalassets.php?id=' . $row["ID"] . '"><b>Approval</b></a>
								<br>';
		} else {
			$status = '';
			$approval = '';
		}
		$action = ' ' . $approval . '
						<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewassets.php?id=' . $row["ID"] . '"><b>Lihat Permintaan</b></a>';
	} else {
		if ($row["StatusDoc"] == 'Selesai') {
			$status = 'Menuggu Proses Approval IAC';
			if ($store1 == 'IAC') {
				if ($row['RemarksIAC'] == '') {
					$status = 'Menuggu Proses Approval IAC';
				} else {
					$status = 'Selesai';
				}
				$action = '<a title="Verifikasi Dokumen" class="badge badge-danger open-modal" data-id="' . $row["ID"] . '" data-code="' . $row["DocNum"] . '" data-remarks="' . $row["RemarksIAC"] . '"><b>Input Remaks</b></a>
								<br>
								<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewassets.php?id=' . $row["ID"] . '"><b>Lihat Permintaan</b></a>';
			} else {
				if ($row['RemarksIAC'] == '') {
					$status = 'Menuggu Proses Approval IAC';
				} else {
					$status = 'Selesai';
				}
				$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewassets.php?id=' . $row["ID"] . '"><b>Lihat Permintaan</b></a>';
			}
		} else {
			$status = $row["ApprovalStatus"] . ' (' . $row["ApprovalUserName"] . ') ' . ' - ' . $row["StatusDoc"];
			$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewassets.php?id=' . $row["ID"] . '"><b>Lihat Permintaan</b></a>';
		}
	}

	$nestedData[] = '<a href="#" class="code"><b>' . $row["DocNum"] . '</b></a>';
	$nestedData[] = $row["DocDate"];
	$nestedData[] = $row["WarehouseFrom"];
	$nestedData[] = $row["WarehouseTo"];
	$nestedData[] = $row["TransName"];
	$nestedData[] = $row["TermsAsset"];
	$nestedData[] = $row["DocPriority"];
	$nestedData[] = $row["Remarks"];
	$nestedData[] = $row["RemarksIAC"];
	$nestedData[] = $status;
	$nestedData[] = $action;
	$data[] = $nestedData;

}

$json_data = array(
	"draw" => intval($requestData['draw']),
	"recordsTotal" => intval($totalData),
	"recordsFiltered" => intval($totalFiltered),
	"data" => $data
);

echo json_encode($json_data);


?>