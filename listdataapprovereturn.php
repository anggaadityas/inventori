<?php
include "db.php";
$store = $_SESSION['nama'];
$area_div = $_SESSION['area_div'];
$requestData= $_REQUEST;

if($requestData['searchByJenisPrioritas'] == ''){
	$jenisprioritas = " ";
}else{
	$jenisprioritas = "AND reqrtn_type_prioritas = '".$requestData['searchByJenisPrioritas']."'";
}

if($requestData['searchByJenisSistem'] == ''){
	$jenissistem = " ";
}else{
	$jenissistem = "AND reqrtn_type_req = '".$requestData['searchByJenisSistem']."'";
}

if($requestData['searchByStore'] == ''){
	$store = " ";
}else{
	$store = "AND reqrtn_user = '".$requestData['searchByStore']."'";
}

if($requestData['searchByStartdate'] == '' OR $requestData['searchByEnddate'] == '' ){
	$tanggalpengiriman = " ";
}else{
	$tanggalpengiriman = "AND convert(char(10),a.reqrtn_date,126) between '".$requestData['searchByStartdate']."' and '".$requestData['searchByEnddate']."' ";
}  

if($requestData['searchByStatusDokumen'] == ''){
	$store = " "; 
}else{

	if($requestData['searchByStatusDokumen'] == 1){
	    $store = "AND reqrtn_ck_approve = 'On Progress'";
	}else{
		$store = "AND reqrtn_ck_approve != 'On Progress'";
	}
}

if($area_div == 'CK JAKARTA' OR $area_div == 'CK SURABAYA' ){
	$where ="WHERE reqrtn_ck_approve ='On Progress' and reqrtn_ck='".$area_div."'";
	$where1 ="WHERE reqrtn_ck_approve ='On Progress' and reqrtn_ck='".$area_div."' AND ";
	$filter="WHERE  reqrtn_ck='".$area_div."' AND ";
	$filter1="WHERE reqrtn_ck='".$area_div."' ".$jenisprioritas." ".$jenissistem." ".$store." ".$tanggalpengiriman." ";
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

$sql  = "SELECT * 
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
        ROW_NUMBER() OVER (ORDER BY id_rtn desc) as rowNum 
      FROM header_returnck a inner join mst_req_type b on a.reqrtn_type=b.id_mst_type
			 left join mst_req_type_item c on a.reqrtn_item_type=c.id_mst_type_item
			  ".$where."
) sub ".$where."";
$params = array();
$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
$query=sqlsrv_query($conn, $sql, $params, $options) or die("data.php: get InventoryItems".$sql."");
$totalData = sqlsrv_num_rows($query);
$totalFiltered = $totalData;  

if( !empty($requestData['search']['value']) ) {
 $sql  = "SELECT * 
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
        ROW_NUMBER() OVER (ORDER BY id_rtn desc) as rowNum 
      FROM header_returnck a inner join mst_req_type b on a.reqrtn_type=b.id_mst_type
			 left join mst_req_type_item c on a.reqrtn_item_type=c.id_mst_type_item ";		
	$sql.=" ".$filter." (convert(char(10),a.reqrtn_date,126)='".$requestData['search']['value']."' OR reqrtn_code = '".$requestData['search']['value']."') "; 
 $sql.=") sub ";
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO1".$sql."");
	$totalFiltered = sqlsrv_num_rows($query);
	$sql.=" WHERE rowNum > ".$requestData['start']." AND rowNum <= ".$requestData['start']." + ".$requestData['length'].""; 
 $params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO2".$sql."");
	
}else if( !empty($_POST['searchByJenisPrioritas']) OR !empty($_POST['searchByJenisSistem']) OR !empty($_POST['searchByStore']) OR !empty($_POST['searchByStartdate']) OR !empty($_POST['searchByEnddate']) OR !empty($_POST['searchByStatusDokumen'])){

$sql  = "SELECT * 
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
        ROW_NUMBER() OVER (ORDER BY id_rtn desc) as rowNum 
      FROM header_returnck a inner join mst_req_type b on a.reqrtn_type=b.id_mst_type
			 left join mst_req_type_item c on a.reqrtn_item_type=c.id_mst_type_item ";		
	$sql.=" ".$filter1." "; 
 $sql.=") sub ";
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO1".$sql."");
	$totalFiltered = sqlsrv_num_rows($query);
	$sql.=" WHERE rowNum > ".$requestData['start']." AND rowNum <= ".$requestData['start']." + ".$requestData['length'].""; 
 $params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO2".$sql."");

}else {	

	$sql  = "SELECT * 
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
        ROW_NUMBER() OVER (ORDER BY id_rtn desc) as rowNum 
      FROM header_returnck a inner join mst_req_type b on a.reqrtn_type=b.id_mst_type
			 left join mst_req_type_item c on a.reqrtn_item_type=c.id_mst_type_item

			 ".$where."
	) sub ";
	$sql.=" ".$where1." rowNum > ".$requestData['start']." AND rowNum <= ".$requestData['start']." + ".$requestData['length'].""; 
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
 $query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO3'".$sql."'");

}

$data = array();
while( $row=sqlsrv_fetch_array($query) ) {  
 $nestedData=array(); 

	if($area_div == 'CK JAKARTA' OR $area_div == 'CK SURABAYA'){
	if($row['reqrtn_ck_approve'] =='On Progress'){
			$reqrtn_destination_approve='<span class="badge badge-warning">Menunggu Persetujuan Distribusi - '.$row["reqrtn_ck"].'</span>';
	}else if($row['reqrtn_ck_approve'] =='Approved'){
		if($row['reqrtn_destination_approve'] =='Verifikasi'){
			if($row["reqrtn_destination"] == 'CK JAKARTA' OR $row["reqrtn_destination"] == 'CK SURABAYA'){
				if($row['reqrtn_nodoc_sap']==''){
					// $reqrtn_destination_approve='<span class="badge badge-warning">Menunggu Transfer SAP - '.$row["reqrtn_destination"].'</span>';
					$reqrtn_destination_approve='<span class="badge badge-success">Selesai</span>';
					}else{
						$reqrtn_destination_approve='<span class="badge badge-success">Selesai</span>';
					}
				}else{
					$reqrtn_destination_approve='<span class="badge badge-success">Selesai</span>';
				}
			}else{
				$reqrtn_destination_approve='<span class="badge badge-warning">Menunggu Verifikasi Admin - '.$row["reqrtn_destination"].'</span>';
			}
	}else if($row['reqrtn_ck_approve'] =='Reject'){
		$reqrtn_destination_approve='<span class="badge badge-danger">Reject - '.$row["reqrtn_ck"].'</span><br><a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id='.$row["id_rtn"].'"><b>Lihat Permintaan</b></a>';
	}else{
		$reqrtn_destination_approve='';
	}
}else{
	if($row['reqrtn_ck_approve'] =='On Progress'){
		$reqrtn_destination_approve='<span class="badge badge-warning">Menunggu Persetujuan Distribusi - '.$row["reqrtn_ck"].'</span>';
}else if($row['reqrtn_ck_approve'] =='Approved'){
if($row['reqrtn_destination_approve'] =='Verifikasi'){
					$reqrtn_destination_approve='<span class="badge badge-success">Selesai</span>';
		}else{
			$reqtp_destination_approve='<span class="badge badge-warning">Menunggu Verifikasi Admin - '.$row["reqrtn_destination"].'</span>';
		}
}else if($row['reqrtn_destination_approve'] =='Reject'){
	$reqrtn_destination_approve='<span class="badge badge-danger">'.$row["reqrtn_destination_approve"].'</span>';
}else{
	$reqrtn_destination_approve='';
}
}

	if($area_div == 'CK JAKARTA' OR $area_div == 'CK SURABAYA' OR $area_div == 'IT JAKARTA' OR $area_div == 'IT SURABAYA' OR $area_div == 'ENG JAKARTA'
	 OR $area_div == 'ENG SURABAYA' OR $area_div == 'GA JAKARTA' OR  $area_div == 'GA SURABAYA'){
		
if($row['reqrtn_ck_approve'] =='Approved'){
			if($row['reqrtn_destination_approve'] =='Verifikasi'){
				$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id='.$row["id_rtn"].'"><b>Lihat Permintaan</b></a>'; 
		}else	if($row['reqrtn_destination_approve'] =='On Progress'){
			$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-danger" href="approvereturn.php?id='.$row["id_rtn"].'"><b>Revisi Pengiriman</b></a>';  
		}else{
			$action=''; 
		}
	}else if($row['reqrtn_ck_approve'] =='On Progress'){
		// $action='<a data-toggle="modal" data-id="'.$row['id_rtn'].'" data-code="'.$row['reqrtn_code'].'" data-delivery="'.$row['req_date'].'" data-store="'.$row['reqrtn_user'].'" data-divisi="'.$row['reqrtn_destination'].'" title="Add this item" class="open-AddBookDialog badge badge-danger" href="#addBookDialog">Belum Penyetujui</a>';
		$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-danger" href="approvereturn.php?id='.$row["id_rtn"].'"><b>Belum Menyetujui</b></a>';  
	}else{
		$action='';
	}
		

			}else{
		$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewreturn.php?id='.$row["id_rtn"].'"><b>Lihat Permintaan</b></a> &nbsp;&nbsp;</br>
		<a  data-toggle="tooltip" title="Cetak Dokumen" class="badge badge-primary" href="cetakreturn.php?id='.$row["id_rtn"].'"><b>Cetak Permintaan</b></a>'; 
	}

	if($area_div == 'CK JAKARTA' OR $area_div == 'CK SURABAYA'){
		if($row["reqrtn_nodoc_sap"] == ""){
			if($row['reqrtn_destination_approve'] =='On Progress'){
				$kode_sap=$row["reqrtn_nodoc_sap"];
			}else{
				// $kode_sap='<a data-toggle="modal" data-id="'.$row['id_rtn'].'" data-code="'.$row['reqrtn_code'].'" data-sap="'.$row['reqrtn_nodoc_sap'].'" title="Add this item" class="open-AddBookDialog badge badge-danger" href="#addBookDialog">Masukan Kode SAP</a>';
				$kode_sap=$row["reqrtn_nodoc_sap"];
			}
		
		}else{
			$kode_sap=$row["reqrtn_nodoc_sap"];
		}
}else{
	$kode_sap=$row["reqrtn_nodoc_sap"];
}

 
 $nestedData[] = '<a href="#" class="code"><b>'.$row["reqrtn_code"].'</b></a>';
 $nestedData[] = $row["req_date"]; 
	$nestedData[] = $row["req_type_name"]; 
	$nestedData[] = $row["reqrtn_type_req"]; 
	$nestedData[] = $row["reqrtn_type_prioritas"]; 
	$nestedData[] = $row["reqrtn_user"]; 
	$nestedData[] = $row["reqrtn_destination"]; 
	$nestedData[] = $row["reqrtn_note"]; 
	$nestedData[] = $reqrtn_destination_approve; 
	$nestedData[] = $action;
 $data[] = $nestedData;
    
}

$json_data = array(
			"draw"                   => intval( $requestData['draw'] ),  
			"recordsTotal"      => intval( $totalData ), 
			"recordsFiltered"  => intval( $totalFiltered ), 
			"data"                    => $data  
			);

echo json_encode($json_data); 


?>
