<?php
include "db.php";
$store = $_SESSION['nama'];
$area_div = $_SESSION['area_div'];
$requestData= $_REQUEST;

if($requestData['searchByStorePenerima'] == ''){
	$storepenerima = " ";
}else{
	$storepenerima = "AND reqtp_user = '".$requestData['searchByStorePenerima']."'";
}  
if($requestData['searchByStorePengirim'] == ''){
	$storepengirim = " ";
}else{
	$storepengirim = "AND reqtp_destination = '".$requestData['searchByStorePengirim']."'";
}


if($requestData['searchByStartdate'] == '' OR $requestData['searchByEnddate'] == '' ){
	$tanggalpengiriman = " ";
	$tanggalpengiriman1=" ";
}else{
	$tanggalpengiriman = "AND convert(char(10),a.reqtp_date,126)  between '".$requestData['searchByStartdate']."' and '".$requestData['searchByEnddate']."' ";
	$tanggalpengiriman1 = "AND convert(char(10),req_date,126)  between '".$requestData['searchByStartdate']."' and '".$requestData['searchByEnddate']."' ";
}



if($requestData['searchByStatusDokumen'] == ''){
	$statusdokumen = " ";
}else{

	if($requestData['searchByStatusDokumen'] == 2){
	$statusdokumen = "AND (reqtp_nodoc_sap != '' OR reqtp_nodoc_sap is not null) ";
	}else{
	$statusdokumen = "AND (reqtp_nodoc_sap = '' OR reqtp_nodoc_sap is null)";
	}

}

if($store == 'CK JAKARTA' OR $store == 'CK SURABAYA'){
	$where ="WHERE reqtp_user !='' AND reqtp_destination !='' and reqtp_user_verifikasi='Verifikasi' and reqtp_ck_destination='$area_div'";
	$filter="WHERE reqtp_user !='' AND reqtp_destination !='' and reqtp_user_verifikasi='Verifikasi' and reqtp_ck_destination='$area_div' AND ";
	$filter1="WHERE reqtp_user !='' AND reqtp_destination !='' and reqtp_user_verifikasi='Verifikasi' and reqtp_ck_destination='$area_div' ".$storepenerima." ".$storepengirim." ".$tanggalpengiriman." ".$statusdokumen." ";
	$filter2="WHERE reqtp_user !='' AND reqtp_destination !='' and reqtp_user_verifikasi='Verifikasi' and reqtp_ck_destination='$area_div' ".$storepenerima." ".$storepengirim." ".$tanggalpengiriman1." ".$statusdokumen." ";
}else{
	$where="WHERE  reqtp_user !='' AND reqtp_destination !='' and reqtp_user='".$store."'";
	$filter ="WHERE reqtp_user !='' AND reqtp_destination !='' and reqtp_user='".$store."' AND ";
	$filter1="WHERE reqtp_user !='' AND reqtp_destination !='' and reqtp_user='".$store."' ".$storepenerima." ".$storepengirim." ".$tanggalpengiriman." ".$statusdokumen." ";
	$filter2="WHERE reqtp_user !='' AND reqtp_destination !='' and reqtp_user='".$store."' ".$storepenerima." ".$storepengirim." ".$tanggalpengiriman1." ".$statusdokumen." ";
}

$columns = array( 
   0 => 'id_tp',
   1 => 'reqtp_code',  
			2 => 'req_date',
			3 => 'due_date',
			4 => 'req_type_name',
			5 => 'req_type_name_item',
			6 => 'reqtp_user',
			7 => 'reqtp_destination',
			8 => 'reqtp_reason',
			9 => 'reqtp_note',	
			10 => 'reqtp_destination_approve',	
			11 => 'reqtp_nodoc_sap',
			12 => 'action'
);

$sql  = "SELECT * 
FROM 
( 
      SELECT 
				a.id_tp,
				a.reqtp_code,
				convert(char(10),a.reqtp_date,126) req_date,
				convert(char(10),a.reqtp_nodoc_sap_date,126) due_date,
				b.req_type_name,
				c.req_type_name_item,
			 a.reqtp_user,
			 a.reqtp_destination,
				a.reqtp_reason,
				a.reqtp_note,
				a.reqtp_destination_approve,
				a.reqtp_nodoc_sap,
				convert(char(10),a.reqtp_nodoc_sap_posting_date,126) posting_date_sap,
				a.reqtp_user_verifikasi,
				a.reqtp_ck_destination,
        ROW_NUMBER() OVER (ORDER BY id_tp desc) as rowNum 
      FROM header_tp a inner join mst_req_type b on a.reqtp_type=b.id_mst_type
						left join mst_req_type_item c on a.reqtp_item_type=c.id_mst_type_item
						".$where."
) sub ".$where." ";
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
					a.id_tp,
					a.reqtp_code,
					convert(char(10),a.reqtp_date,126) req_date,
					convert(char(10),a.reqtp_nodoc_sap_date,126) due_date,
					b.req_type_name,
					c.req_type_name_item,
						a.reqtp_user,
						a.reqtp_destination,
					a.reqtp_reason,
					a.reqtp_note,
					a.reqtp_destination_approve,
					a.reqtp_nodoc_sap,
					convert(char(10),a.reqtp_nodoc_sap_posting_date,126) posting_date_sap,
					a.reqtp_user_verifikasi,
					a.reqtp_ck_destination,
									ROW_NUMBER() OVER (ORDER BY id_tp desc) as rowNum 
							FROM header_tp a inner join mst_req_type b on a.reqtp_type=b.id_mst_type
							left join mst_req_type_item c on a.reqtp_item_type=c.id_mst_type_item ";
	$sql.=" ".$filter." reqtp_code LIKE '".$requestData['search']['value']."%' "; 
	$sql.=" OR reqtp_code LIKE '".$requestData['search']['value']."%' "; 
	$sql.=") sub ";
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO1".$sql."");
	$totalFiltered = sqlsrv_num_rows($query);
	$sql.=" WHERE rowNum > ".$requestData['start']." AND rowNum <= ".$requestData['start']." + ".$requestData['length'].""; 
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO2".$sql."");
	
}else if( !empty($_POST['searchByStorePenerima']) OR !empty($_POST['searchByStorePengirim']) OR !empty($_POST['searchByStartdate']) OR !empty($_POST['searchByEnddate']) OR !empty($_POST['searchByStatusDokumen'])){
 $sql  = "SELECT * 
	FROM 
	( 
					SELECT 
					a.id_tp,
					a.reqtp_code,
					convert(char(10),a.reqtp_date,126) req_date,
					convert(char(10),a.reqtp_nodoc_sap_date,126) due_date,
					b.req_type_name,
					c.req_type_name_item,
						a.reqtp_user,
						a.reqtp_destination,
					a.reqtp_reason,
					a.reqtp_note,
					a.reqtp_destination_approve,
					a.reqtp_nodoc_sap,
					convert(char(10),a.reqtp_nodoc_sap_posting_date,126) posting_date_sap,
					a.reqtp_user_verifikasi,
					a.reqtp_ck_destination,
									ROW_NUMBER() OVER (ORDER BY id_tp desc) as rowNum 
							FROM header_tp a inner join mst_req_type b on a.reqtp_type=b.id_mst_type
							left join mst_req_type_item c on a.reqtp_item_type=c.id_mst_type_item ";
	$sql.=" ".$filter1." ";  
	$sql.=") sub ".$filter2." ";
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO1".$sql."");
	$totalFiltered = sqlsrv_num_rows($query);
	$sql.=" AND rowNum > ".$requestData['start']." AND rowNum <= ".$requestData['start']." + ".$requestData['length'].""; 
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO2".$sql."");
	
} else {	

	$sql  = "SELECT * 
	FROM 
	( 
					SELECT 
					a.id_tp,
					a.reqtp_code,
					convert(char(10),a.reqtp_date,126) req_date,
					convert(char(10),a.reqtp_nodoc_sap_date,126) due_date,
					b.req_type_name,
					c.req_type_name_item,
						a.reqtp_user,
						a.reqtp_destination,
					a.reqtp_reason,
					a.reqtp_note,
					a.reqtp_destination_approve,
					a.reqtp_nodoc_sap,
					convert(char(10),a.reqtp_nodoc_sap_posting_date,126) posting_date_sap,
					a.reqtp_nodoc_sap_posting_date,
					a.reqtp_user_verifikasi,
					a.reqtp_ck_destination,
					ROW_NUMBER() OVER (ORDER BY id_tp desc) as rowNum 
							FROM header_tp a inner join mst_req_type b on a.reqtp_type=b.id_mst_type
					left join mst_req_type_item c on a.reqtp_item_type=c.id_mst_type_item
					".$where."
	) sub ";
	$sql.=" ".$where." AND rowNum > ".$requestData['start']." AND rowNum <= ".$requestData['start']." + ".$requestData['length'].""; 
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
 $query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO3'".$sql."'");

}

$data = array();
while( $row=sqlsrv_fetch_array($query) ) {  
 $nestedData=array(); 

	if($row['reqtp_destination_approve'] =='On Progress'){
			$reqtp_destination_approve='<span class="badge badge-warning">Menunggu Persetujuan - '.$row["reqtp_destination"].'</span>';
	}else if($row['reqtp_destination_approve'] =='Approved'){
			if($row['reqtp_user_verifikasi'] =='Verifikasi'){
				if($row['reqtp_nodoc_sap']==''){
				$reqtp_destination_approve='<span class="badge badge-warning">Menunggu Transfer SAP -  '.$row["reqtp_ck_destination"].'</span>';
				}else{
					$reqtp_destination_approve='<span class="badge badge-success">Selesai</span>';
				}
			}else if($row['reqtp_user_verifikasi'] =='Reject'){
					$reqtp_destination_approve='<span class="badge badge-danger">Ditolak - '.$row["reqtp_user"].'</span>';
			}else{
				$reqtp_destination_approve='<span class="badge badge-warning">Menunggu Pengecekan - '.$row["reqtp_user"].'</span>';
			}
		
	}else if($row['reqtp_destination_approve'] =='Reject'){
		$reqtp_destination_approve='<span class="badge badge-danger">Ditolak - '.$row["reqtp_destination"].'</span>';
	}else{
		$reqtp_destination_approve='';
	}


	if($area_div == 'CK JAKARTA' OR $area_div == 'CK SURABAYA'){
			if($row["reqtp_nodoc_sap"] == ""){
				if($row['reqtp_user_verifikasi']=='Verifikasi'){
						$kode_sap='<a data-toggle="modal" data-id="'.$row['id_tp'].'" data-code="'.$row['reqtp_code'].'" data-sap="'.$row['reqtp_nodoc_sap'].'" data-tokoasal="'.$row['reqtp_user'].'" data-tokodestination="'.$row['reqtp_destination'].'" title="Add this item" class="open-AddBookDialog badge badge-danger" href="#addBookDialog">Masukan Kode SAP</a>';
					}else{
						$kode_sap='';
					}
			}else{
				$kode_sap=$row["reqtp_nodoc_sap"].'<br><a data-toggle="modal" data-id="'.$row['id_tp'].'" data-code="'.$row['reqtp_code'].'" data-sap="'.$row['reqtp_nodoc_sap'].'" data-tokoasal="'.$row['reqtp_user'].'" data-tanggalsap="'.$row['posting_date_sap'].'" data-tokoasal="'.$row['reqtp_user'].'" data-tokodestination="'.$row['reqtp_destination'].'" title="Add this item" class="open-AddBookDialog badge badge-danger" href="#addBookDialog">Edit Kode SAP</a>';
			}
}else{
		$kode_sap=$row["reqtp_nodoc_sap"];
}

if( $_SESSION["id_divisi"] == 9 ||  $_SESSION["id_divisi"] == 5){

	if($row['reqtp_destination_approve'] =='Approved'){
			if($row['reqtp_user_verifikasi']=='Verifikasi'){
				$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewrequest.php?id='.$row["id_tp"].'"><b>Lihat Permintaan</b></a></br>
				<a data-toggle="tooltip" title="Cetak Dokumen" class="badge badge-primary" href="cetakpermintaan.php?id='.$row["id_tp"].'"><b>Cetak Permintaan</b></a>'; 
			
			}else if($row['reqtp_user_verifikasi']=='Reject'){
				$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewrequest.php?id='.$row["id_tp"].'"><b>Lihat Permintaan</b></a>'; 
			
			}else{
				if($row['reqtp_user'] == $store ){
				$action='<a title="Verfikasi Dokumen" class="badge badge-danger" href="viewverifikasirequest.php?id='.$row["id_tp"].'"><b>Belum Verifikasi</b></a>'; 
			}else{
				$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewrequest.php?id='.$row["id_tp"].'"><b>Lihat Permintaan</b></a>'; 
			}
			
			}
}else{
		$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewrequest.php?id='.$row["id_tp"].'"><b>Lihat Permintaan</b></a>'; 
	}

}else{
	if($row['reqtp_destination_approve'] =='Reject' || $row['reqtp_user_verifikasi']=='Reject'){
		$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewrequest.php?id='.$row["id_tp"].'"><b>Lihat Permintaan</b></a>';
	}else{
	$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewrequest.php?id='.$row["id_tp"].'"><b>Lihat Permintaan</b></a></br>
	<a data-toggle="tooltip" title="Cetak Dokumen" class="badge badge-primary" href="cetakpermintaan.php?id='.$row["id_tp"].'"><b>Cetak Permintaan</b></a>';
	} 
}


 $nestedData[] = '<a href="#" class="code"><b>'.$row["reqtp_code"].'</b></a>';	
 $nestedData[] = $row["req_date"]; 
	$nestedData[] = $row["due_date"]; 
	$nestedData[] = $row["req_type_name"]; 
	// $nestedData[] = $row["req_type_name_item"]; 
	$nestedData[] = $row["reqtp_user"]; 
	$nestedData[] = $row["reqtp_destination"]; 
	// $nestedData[] = $row["reqtp_reason"]; 
	$nestedData[] = $row["reqtp_note"]; 
	$nestedData[] = $kode_sap;
	$nestedData[] = $reqtp_destination_approve; 
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
