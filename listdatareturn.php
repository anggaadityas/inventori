<?php
include "db.php";
$store1 = $_SESSION['nama'];
$area_div = $_SESSION['area_div'];
$requestData = $_REQUEST;

if ($requestData['searchByJenisPrioritas'] == '') {
	$jenisprioritas = " ";
} else {
	$jenisprioritas = "AND reqrtn_type_prioritas = '" . $requestData['searchByJenisPrioritas'] . "'";
}

if ($requestData['searchByJenisSistem'] == '') {
	$jenissistem = " ";
} else {
	$jenissistem = "AND reqrtn_type_req = '" . $requestData['searchByJenisSistem'] . "'";
}

if ($requestData['searchByStore'] == '') {
	$store = " ";
} else {
	$store = "AND reqrtn_user = '" . $requestData['searchByStore'] . "'";
}

if ($requestData['searchByStartdate'] == '' or $requestData['searchByEnddate'] == '') {
	$tanggalpengiriman = " ";
} else {
	$tanggalpengiriman = "AND convert(char(10),a.reqrtn_date,126) between '" . $requestData['searchByStartdate'] . "' and '" . $requestData['searchByEnddate'] . "' ";
}



if ($requestData['searchByStatusDokumen'] == '') {
	$statusdokumen = " ";
} else {

	if ($requestData['searchByStatusDokumen'] == 2) {
		$statusdokumen = "AND reqrtn_destination_approve = 'Verifikasi'";
	} else if ($requestData['searchByStatusDokumen'] == 3) {
		$statusdokumen = "AND reqrtn_ck_approve = 'Reject'";
	} else {
		$statusdokumen = "AND reqrtn_destination_approve != 'Verifikasi'";
	}

}

// reqrtn_ck_approve='Approved' and

if ($area_div == 'CK JAKARTA' or $area_div == 'CK SURABAYA') {
	$where = "WHERE (reqrtn_user is not null or reqrtn_user='') and reqrtn_ck_approve='Approved' and reqrtn_destination='" . $area_div . "'";
	$filter = "WHERE (reqrtn_user is not null or reqrtn_user='') and reqrtn_destination='" . $area_div . "' AND (reqrtn_code LIKE '" . $requestData['search']['value'] . "%' OR reqrtn_type_prioritas LIKE '%" . $requestData['search']['value'] . "%') ";
	$filter1 = "WHERE (reqrtn_user is not null or reqrtn_user='') and reqrtn_destination='" . $area_div . "' " . $jenisprioritas . " " . $jenissistem . " " . $store . " " . $tanggalpengiriman . " " . $statusdokumen . " ";
	$orderby = "ORDER BY CONVERT ( CHAR ( 10 ), a.reqrtn_date, 126 ) ASC";
} else if ($area_div == 'IT JAKARTA' or $area_div == 'IT SURABAYA' or $area_div == 'GA JAKARTA' or $area_div == 'GA SURABAYA' or $area_div == 'ENG JAKARTA' or $area_div == 'ENG SURABAYA') {
	$where = "WHERE (reqrtn_user is not null or reqrtn_user='') and reqrtn_ck_approve='Approved' and reqrtn_destination='" . $area_div . "'";
	$filter = "WHERE  (reqrtn_user is not null or reqrtn_user='') and reqrtn_ck_approve='Approved' and  reqrtn_destination='" . $area_div . "' AND (reqrtn_code LIKE '" . $requestData['search']['value'] . "%' OR reqrtn_type_prioritas LIKE '%" . $requestData['search']['value'] . "%') ";
	$filter1 = "WHERE  (reqrtn_user is not null or reqrtn_user='') and  reqrtn_destination='" . $area_div . "' " . $jenisprioritas . " " . $jenissistem . " " . $store . " " . $tanggalpengiriman . " " . $statusdokumen . " ";
	$orderby = "ORDER BY id_rtn desc";
} else {
	$where = "WHERE reqrtn_user='" . $store1 . "'";
	$filter = "WHERE reqrtn_user='" . $store1 . "' AND (reqrtn_code LIKE '" . $requestData['search']['value'] . "%' OR reqrtn_type_prioritas LIKE '%" . $requestData['search']['value'] . "%') ";
	$filter1 = "WHERE  reqrtn_user='" . $store1 . "' " . $jenisprioritas . " " . $jenissistem . " " . $store . " " . $tanggalpengiriman . " " . $statusdokumen . " ";
	$orderby = "ORDER BY id_rtn desc";
}

$columns = array(
	0 => 'id_rtn',
	1 => 'reqrtn_code',
	2 => 'req_date',
	3 => 'due_date',
	4 => 'req_type_name',
	5 => 'req_type_name_item',
	6 => 'reqrtn_user',
	7 => 'reqrtn_ck',
	8 => 'reqrtn_destination',
	9 => 'reqrtn_reason',
	10 => 'reqrtn_note',
	11 => 'reqrtn_destination_approve',
	12 => 'reqrtn_nodoc_sap',
	13 => 'action'
);

$sql = "SELECT * 
FROM 
( 
      SELECT 
				a.id_rtn,
				a.reqrtn_code,
				convert(char(10),a.reqrtn_date,126) req_date,
				convert(char(10),a.reqrtn_nodoc_sap_date,126) due_date,
				b.req_type_name,			
				CASE
    WHEN a.reqrtn_type_req=1 THEN 'Sistem'
    WHEN a.reqrtn_type_req=2 THEN 'Non Sistem'
    WHEN a.reqrtn_type_req=3 THEN 'Wadah'
    WHEN a.reqrtn_type_req=4 THEN 'NCR'
	WHEN a.reqrtn_type_req=5 THEN 'DAMAGE'
    ELSE ''
END as reqrtn_type_req,
CASE
    WHEN a.reqrtn_type_prioritas=1 THEN 'Normal'
    WHEN a.reqrtn_type_prioritas=2 THEN 'Darurat'
     WHEN a.reqrtn_type_prioritas=3 THEN 'Hari H'
    ELSE ''
END as reqrtn_type_prioritas,
				c.req_type_name_item,
			 a.reqrtn_user,
				a.reqrtn_ck,
			 a.reqrtn_destination,
				a.reqrtn_reason,
				a.reqrtn_note,
				a.reqrtn_ck_approve,
				a.reqrtn_destination_approve,
				a.reqrtn_nodoc_sap,
        ROW_NUMBER() OVER (" . $orderby . ") as rowNum 
      FROM header_returnck a inner join mst_req_type b on a.reqrtn_type=b.id_mst_type
			 left join mst_req_type_item c on a.reqrtn_item_type=c.id_mst_type_item
			" . $where . "
) sub " . $where . "";
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
				a.id_rtn,
				a.reqrtn_code,
				convert(char(10),a.reqrtn_date,126) req_date,
				convert(char(10),a.reqrtn_nodoc_sap_date,126) due_date,
				b.req_type_name,
				CASE
    WHEN a.reqrtn_type_req=1 THEN 'Sistem'
    WHEN a.reqrtn_type_req=2 THEN 'Non Sistem'
    WHEN a.reqrtn_type_req=3 THEN 'Wadah'
    WHEN a.reqrtn_type_req=4 THEN 'NCR'
	WHEN a.reqrtn_type_req=5 THEN 'DAMAGE'
    ELSE ''
END as reqrtn_type_req,
CASE
    WHEN a.reqrtn_type_prioritas=1 THEN 'Normal'
    WHEN a.reqrtn_type_prioritas=2 THEN 'Darurat'
     WHEN a.reqrtn_type_prioritas=3 THEN 'Hari H'
    ELSE ''
END as reqrtn_type_prioritas,
				c.req_type_name_item,
			 a.reqrtn_user,
				a.reqrtn_ck,
			 a.reqrtn_destination,
				a.reqrtn_reason,
				a.reqrtn_note,
				a.reqrtn_ck_approve,
				a.reqrtn_destination_approve,
				a.reqrtn_nodoc_sap,
        ROW_NUMBER() OVER (" . $orderby . ") as rowNum 
      FROM header_returnck a inner join mst_req_type b on a.reqrtn_type=b.id_mst_type
			 left join mst_req_type_item c on a.reqrtn_item_type=c.id_mst_type_item ";
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
				a.id_rtn,
				a.reqrtn_code,
				convert(char(10),a.reqrtn_date,126) req_date,
				convert(char(10),a.reqrtn_nodoc_sap_date,126) due_date,
				b.req_type_name,
				CASE
    WHEN a.reqrtn_type_req=1 THEN 'Sistem'
    WHEN a.reqrtn_type_req=2 THEN 'Non Sistem'
    WHEN a.reqrtn_type_req=3 THEN 'Wadah'
    WHEN a.reqrtn_type_req=4 THEN 'NCR'
	WHEN a.reqrtn_type_req=5 THEN 'DAMAGE'
    ELSE ''
END as reqrtn_type_req,
CASE
    WHEN a.reqrtn_type_prioritas=1 THEN 'Normal'
    WHEN a.reqrtn_type_prioritas=2 THEN 'Darurat'
      WHEN a.reqrtn_type_prioritas=3 THEN 'Hari H'
    ELSE ''
END as reqrtn_type_prioritas,
				c.req_type_name_item,
			 a.reqrtn_user,
				a.reqrtn_ck,
			 a.reqrtn_destination,
				a.reqrtn_reason,
				a.reqrtn_note,
				a.reqrtn_ck_approve,
				a.reqrtn_destination_approve,
				a.reqrtn_nodoc_sap,
        ROW_NUMBER() OVER (" . $orderby . ") as rowNum 
      FROM header_returnck a inner join mst_req_type b on a.reqrtn_type=b.id_mst_type
			 left join mst_req_type_item c on a.reqrtn_item_type=c.id_mst_type_item ";
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
				a.id_rtn,
				a.reqrtn_code,
				convert(char(10),a.reqrtn_date,126) req_date,
				convert(char(10),a.reqrtn_nodoc_sap_date,126) due_date,
				b.req_type_name,
				CASE
    WHEN a.reqrtn_type_req=1 THEN 'Sistem'
    WHEN a.reqrtn_type_req=2 THEN 'Non Sistem'
    WHEN a.reqrtn_type_req=3 THEN 'Wadah'
    WHEN a.reqrtn_type_req=4 THEN 'NCR'
	WHEN a.reqrtn_type_req=5 THEN 'DAMAGE'
    ELSE ''
END as reqrtn_type_req,
CASE
    WHEN a.reqrtn_type_prioritas=1 THEN 'Normal'
    WHEN a.reqrtn_type_prioritas=2 THEN 'Darurat'
    ELSE ''
END as reqrtn_type_prioritas,
				c.req_type_name_item,
			 a.reqrtn_user,
				a.reqrtn_ck,
			 a.reqrtn_destination,
				a.reqrtn_reason,
				a.reqrtn_note,
				a.reqrtn_ck_approve,
				a.reqrtn_destination_approve,
				a.reqrtn_nodoc_sap,
        ROW_NUMBER() OVER (" . $orderby . ") as rowNum 
      FROM header_returnck a inner join mst_req_type b on a.reqrtn_type=b.id_mst_type
			 left join mst_req_type_item c on a.reqrtn_item_type=c.id_mst_type_item
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

	if ($area_div == 'CK JAKARTA' or $area_div == 'CK SURABAYA') {
		if ($row['reqrtn_ck_approve'] == 'On Progress') {
			$reqrtn_destination_approve = '<span class="badge badge-warning">Menunggu Persetujuan Distribusi - ' . $row["reqrtn_ck"] . '</span>';
		} else if ($row['reqrtn_ck_approve'] == 'Approved') {
			if ($row['reqrtn_destination_approve'] == 'Verifikasi') {
				if ($row["reqrtn_destination"] == 'CK JAKARTA' or 'CK SURABAYA') {
					if ($row['reqrtn_nodoc_sap'] == '') {
						// $reqrtn_destination_approve='<span class="badge badge-warning">Menunggu Transfer SAP - '.$row["reqrtn_destination"].'</span>';
						$reqrtn_destination_approve = '<span class="badge badge-success">Selesai</span>';
					} else {
						$reqrtn_destination_approve = '<span class="badge badge-success">Selesai</span>';
					}
				} else {
					$reqrtn_destination_approve = '<span class="badge badge-success">Selesai</span>';
				}
			} else {
				$reqrtn_destination_approve = '<span class="badge badge-warning">Menunggu Verifikasi Admin - ' . $row["reqrtn_destination"] . '</span>';
			}
		} else if ($row['reqrtn_ck_approve'] == 'Reject') {
			$reqrtn_destination_approve = '<span class="badge badge-danger">' . $row["reqrtn_ck_approve"] . '</span><br><a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a>';
		} else {
			$reqrtn_destination_approve = '';
		}
	} else {
		if ($row['reqrtn_ck_approve'] == 'On Progress') {
			$reqrtn_destination_approve = '<span class="badge badge-warning">Menunggu Persetujuan Distribusi - ' . $row["reqrtn_ck"] . '</span>';
		} else if ($row['reqrtn_ck_approve'] == 'Approved') {
			if ($row['reqrtn_destination_approve'] == 'Verifikasi') {
				if ($row["reqrtn_destination"] == 'CK JAKARTA' or $row["reqrtn_destination"] == 'CK SURABAYA') {
					if ($row['reqrtn_nodoc_sap'] == '') {
						// $reqrtn_destination_approve='<span class="badge badge-warning">Menunggu Transfer SAP - '.$row["reqrtn_destination"].'</span>';
						$reqrtn_destination_approve = '<span class="badge badge-success">Selesai</span>';
					} else {
						$reqrtn_destination_approve = '<span class="badge badge-success">Selesai</span>';
					}
				} else {
					$reqrtn_destination_approve = '<span class="badge badge-success">Selesai</span>';
				}
			} else {
				$reqrtn_destination_approve = '<span class="badge badge-warning">Menunggu Verifikasi Admin - ' . $row["reqrtn_destination"] . '</span>';
			}
		} else if ($row['reqrtn_ck_approve'] == 'Reject') {
			$reqrtn_destination_approve = '<span class="badge badge-danger">' . $row["reqrtn_ck_approve"] . ' - ' . $row["reqrtn_ck"] . '</span><br><a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a>';
		} else {
			$reqrtn_destination_approve = '';
		}
	}

	if (
		$area_div == 'CK JAKARTA' or $area_div == 'CK SURABAYA' or $area_div == 'IT JAKARTA' or $area_div == 'IT SURABAYA' or $area_div == 'ENG JAKARTA'
		or $area_div == 'ENG SURABAYA' or $area_div == 'GA JAKARTA' or $area_div == 'GA SURABAYA'
	) {

		if ($row['reqrtn_ck_approve'] == 'Approved') {
			if ($row['reqrtn_destination_approve'] == 'Verifikasi') {
				$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a><br><a title="Verfikasi Dokumen" class="badge badge-danger" href="viewapprovereturn.php?id=' . $row["id_rtn"] . '"><b>Revisi Verifikasi</b></a>';
			} else if ($row['reqrtn_destination_approve'] == 'On Progress') {
				$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a><br><a title="Verfikasi Dokumen" class="badge badge-danger" href="viewapprovereturn.php?id=' . $row["id_rtn"] . '"><b>Belum Verifikasi</b></a>';
			} else {
				$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a><br>';
			}
		} else if ($row['reqrtn_ck_approve'] == 'Reject') {
			$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a><br><a data-toggle="modal" data-id="' . $row['id_rtn'] . '" data-code="' . $row['reqrtn_code'] . '" data-sap="' . $row['reqrtn_nodoc_sap'] . '"';
		} else {
			$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a><br><a data-toggle="modal" data-id="' . $row['id_rtn'] . '" data-code="' . $row['reqrtn_code'] . '" data-sap="' . $row['reqrtn_nodoc_sap'] . '" title="Add this item" class="open-AddBookDialog badge badge-danger" href="#addBookDialog">Belum Penyetujui</a>';
		}

	} else {
		if ($row['reqrtn_ck_approve'] == 'Approved') {
			if ($row["reqrtn_type_req"] == 'Wadah' or $row["reqrtn_type_req"] == 'Non Sistem' or $row["reqrtn_type_req"] == 'Sistem') {

				$date1 = new DateTime($row['req_date']);
				$date2 = new DateTime(date('Y-m-d'));
				$cekselisih = $date2->diff($date1)->format("%r%a"); //-3

				if ($cekselisih > 0) {

					if (date("h:i") <= '16:00') {

						$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a></br><a data-toggle="tooltip" title="Edit Dokumen" class="badge badge-danger" href="editretur.php?id=' . $row["id_rtn"] . '"><b>Edit Permintaan</b></a>';

					} else {

						$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a></br>';

					}

				} else {

					$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a></br>';


				}

				// 		</br>
				// <a data-toggle="tooltip" title="Edit Dokumen" class="badge badge-danger" href="editretur.php?id='.$row["id_rtn"].'"><b>Edit Permintaan</b></a>




			} else {
				$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a>';
			}
		} else {
			// $action ='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id='.$row["id_rtn"].'"><b>Lihat Permintaan</b></a><br>';
			if ($row["reqrtn_type_req"] == 'Wadah' or $row["reqrtn_type_req"] == 'Non Sistem' or $row["reqrtn_type_req"] == 'Sistem') {

				$date1 = new DateTime($row['req_date']);
				$date2 = new DateTime(date('Y-m-d'));
				$cekselisih = $date2->diff($date1)->format("%r%a"); //-3

				if ($cekselisih > 0) {

					if (date("h:i") <= '16:00') {

						$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a></br><a data-toggle="tooltip" title="Edit Dokumen" class="badge badge-danger" href="editretur.php?id=' . $row["id_rtn"] . '"><b>Edit Permintaan</b></a>';

					} else {

						$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a></br>';

					}

				} else {

					$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a></br>';


				}

				// 		</br>
				// <a data-toggle="tooltip" title="Edit Dokumen" class="badge badge-danger" href="editretur.php?id='.$row["id_rtn"].'"><b>Edit Permintaan</b></a>




			} else {
				$action = '<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id=' . $row["id_rtn"] . '"><b>Lihat Permintaan</b></a>';
			}
		}
	}

	if ($area_div == 'CK JAKARTA' or $area_div == 'CK SURABAYA') {
		if ($row["reqrtn_nodoc_sap"] == "") {
			if ($row['reqrtn_destination_approve'] == 'On Progress') {
				$kode_sap = $row["reqrtn_nodoc_sap"];
			} else {
				// $kode_sap='<a data-toggle="modal" data-id="'.$row['id_rtn'].'" data-code="'.$row['reqrtn_code'].'" data-sap="'.$row['reqrtn_nodoc_sap'].'" title="Add this item" class="open-AddBookDialog badge badge-danger" href="#addBookDialog">Masukan Kode SAP</a>';
				$kode_sap = $row["reqrtn_nodoc_sap"];
			}

		} else {
			$kode_sap = $row["reqrtn_nodoc_sap"];
		}
	} else {
		$kode_sap = $row["reqrtn_nodoc_sap"];
	}


	$nestedData[] = '<a href="#" class="code"><b>' . $row["reqrtn_code"] . '</b></a>';
	$nestedData[] = $row["req_date"];
	$nestedData[] = $row["due_date"];
	$nestedData[] = $row["req_type_name"];
	$nestedData[] = $row["reqrtn_type_req"];
	$nestedData[] = $row["reqrtn_type_prioritas"];
	$nestedData[] = $row["reqrtn_user"];
	$nestedData[] = $row["reqrtn_destination"];
	$nestedData[] = $row["reqrtn_note"];
	$nestedData[] = $kode_sap;
	$nestedData[] = $reqrtn_destination_approve;
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