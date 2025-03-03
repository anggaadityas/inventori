<?php
include "db.php";
$store = $_SESSION['nama'];
$area_div = $_SESSION['area_div'];
$requestData= $_REQUEST;

if($requestData['searchByStorePenerima'] == ''){
	$storepenerima = " ";
}else{
	$storepenerima = "AND reqtb_user = '".$requestData['searchByStorePenerima']."'";
}  
if($requestData['searchByStorePengirim'] == ''){
	$storepengirim = " ";
}else{
	$storepengirim = "AND reqtb_destination = '".$requestData['searchByStorePengirim']."'";
}


if($requestData['searchByStartdate'] == '' OR $requestData['searchByEnddate'] == '' ){
	$tanggalpengiriman = " ";
}else{
	$tanggalpengiriman = "AND convert(char(10),a.reqtb_date,126)  between '".$requestData['searchByStartdate']."' and '".$requestData['searchByEnddate']."' ";
}



if($requestData['searchByStatusDokumen'] == ''){
	$statusdokumen = " ";
}else{

	if($requestData['searchByStatusDokumen'] == 2){
	$statusdokumen = "AND reqtb_user_retur = 'Verifikasi' AND reqtb_destination_retur_verifikasi ='Verifikasi' ";
	}else{
	$statusdokumen = "AND reqtb_user_retur != 'Verifikasi' AND reqtb_destination_retur_verifikasi !='Verifikasi'";
	}

}

$columns = array( 
	0 => 'id_tb',
	1 => 'reqtb_code',  
	2 => 'req_date',
	4 => 'req_type_name',
	5 => 'req_type_name_item',
	6 => 'reqtb_user',
	7 => 'reqtb_destination',
	8 => 'reqtb_reason',
	9 => 'reqtb_note',	
	10 => 'reqtb_destination_approve',	
	12 => 'action'
);

$sql  = "SELECT *
FROM 
( 
      SELECT 
				a.id_tb,
				a.reqtb_code,
				convert(char(10),a.reqtb_date,126) req_date,
				b.req_type_name,
				c.req_type_name_item,
			  a.reqtb_user,
			  a.reqtb_destination,
				a.reqtb_reason,
				a.reqtb_note,
				a.reqtb_destination_approve,
				a.reqtb_user_verifikasi,
				reqtb_user_retur,
				reqtb_destination_retur_verifikasi,
				d.sumpeminjaman,
				d.sumpengembalian,
				COALESCE(d.sumpengembalian,0) - COALESCE(d.sumpeminjaman,0) as selisi,
				COALESCE(d.sumreturplus,0) - COALESCE(d.sumreturverifikasiplus,0) as kelebihan,
        ROW_NUMBER() OVER (ORDER BY id_tb desc) as rowNum 
      FROM header_tb a inner join mst_req_type b on a.reqtb_type=b.id_mst_type
			left join mst_req_type_item c on a.reqtb_item_type=c.id_mst_type_item
			LEFT JOIN (
					SELECT
        a.header_idtb,
				sum(a.tbitem_qty_verifikasi) sumpeminjaman,
				sum(b.sumreturverifikasi) sumpengembalian,
				sum(b.sumreturplus) sumreturplus,
				sum(b.sumreturverifikasiplus) sumreturverifikasiplus
    FROM
        detail_tb a
        LEFT JOIN (
									SELECT
											header_idrtrtb,
											header_detailid,
											rtrtbitem_id,
											SUM ( rtrtbitem_qty_retur ) AS sumretur,
											SUM ( rtrtbitem_qty_retur_verifikasi ) AS sumreturverifikasi,
											SUM ( rtrtbitem_qty_retur_plus ) AS sumreturplus,
											SUM ( rtrtbitem_qty_retur_verifikasi_plus ) AS sumreturverifikasiplus 
										FROM
											detail_returntb 
										GROUP BY
											header_idrtrtb,
											header_detailid,
											rtrtbitem_id
        ) AS b ON a.header_idtb= b.header_idrtrtb 
        AND a.id_detailtb= b.header_detailid 
        AND a.tbitem_id= b.rtrtbitem_id
				GROUP BY header_idtb
			) as d ON a.id_tb=d.header_idtb
			   WHERE reqtb_user !='' AND reqtb_destination !='' and reqtb_destination='$store'
) sub WHERE reqtb_destination='$store'";
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
					a.id_tb,
					a.reqtb_code,
					convert(char(10),a.reqtb_date,126) req_date,
					b.req_type_name,
					c.req_type_name_item,
						a.reqtb_user,
						a.reqtb_destination,
					a.reqtb_reason,
					a.reqtb_note,
					a.reqtb_destination_approve,
					a.reqtb_user_verifikasi,
					reqtb_user_retur,
					reqtb_destination_retur_verifikasi,
					d.sumpeminjaman,
					d.sumpengembalian,
					COALESCE(d.sumpengembalian,0) - COALESCE(d.sumpeminjaman,0) as selisi,
					COALESCE(d.sumreturplus,0) - COALESCE(d.sumreturverifikasiplus,0) as kelebihan,
									ROW_NUMBER() OVER (ORDER BY id_tb desc) as rowNum 
							FROM header_tb a inner join mst_req_type b on a.reqtb_type=b.id_mst_type
				left join mst_req_type_item c on a.reqtb_item_type=c.id_mst_type_item
				LEFT JOIN (
						SELECT
									a.header_idtb,
					sum(a.tbitem_qty_verifikasi) sumpeminjaman,
					sum(b.sumreturverifikasi) sumpengembalian,
				sum(b.sumreturplus) sumreturplus,
				sum(b.sumreturverifikasiplus) sumreturverifikasiplus
					FROM
									detail_tb a
									LEFT JOIN (
										SELECT
											header_idrtrtb,
											header_detailid,
											rtrtbitem_id,
											SUM ( rtrtbitem_qty_retur ) AS sumretur,
											SUM ( rtrtbitem_qty_retur_verifikasi ) AS sumreturverifikasi,
											SUM ( rtrtbitem_qty_retur_plus ) AS sumreturplus,
											SUM ( rtrtbitem_qty_retur_verifikasi_plus ) AS sumreturverifikasiplus 
										FROM
											detail_returntb 
										GROUP BY
											header_idrtrtb,
											header_detailid,
											rtrtbitem_id
									) AS b ON a.header_idtb= b.header_idrtrtb 
									AND a.id_detailtb= b.header_detailid 
									AND a.tbitem_id= b.rtrtbitem_id
					GROUP BY header_idtb
				) as d ON a.id_tb=d.header_idtb ";		
	$sql.=" WHERE reqtb_user !='' AND reqtb_destination !='' and reqtb_destination='$store' AND reqtb_code LIKE '".$requestData['search']['value']."%' "; 
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
					a.id_tb,
					a.reqtb_code,
					convert(char(10),a.reqtb_date,126) req_date,
					b.req_type_name,
					c.req_type_name_item,
						a.reqtb_user,
						a.reqtb_destination,
					a.reqtb_reason,
					a.reqtb_note,
					a.reqtb_destination_approve,
					a.reqtb_user_verifikasi,
					reqtb_user_retur,
					reqtb_destination_retur_verifikasi,
					d.sumpeminjaman,
					d.sumpengembalian,
					COALESCE(d.sumpengembalian,0) - COALESCE(d.sumpeminjaman,0) as selisi,
					COALESCE(d.sumreturplus,0) - COALESCE(d.sumreturverifikasiplus,0) as kelebihan,
									ROW_NUMBER() OVER (ORDER BY id_tb desc) as rowNum 
							FROM header_tb a inner join mst_req_type b on a.reqtb_type=b.id_mst_type
				left join mst_req_type_item c on a.reqtb_item_type=c.id_mst_type_item
				LEFT JOIN (
						SELECT
									a.header_idtb,
					sum(a.tbitem_qty_verifikasi) sumpeminjaman,
					sum(b.sumreturverifikasi) sumpengembalian,
				sum(b.sumreturplus) sumreturplus,
				sum(b.sumreturverifikasiplus) sumreturverifikasiplus
					FROM
									detail_tb a
									LEFT JOIN (
										SELECT
											header_idrtrtb,
											header_detailid,
											rtrtbitem_id,
											SUM ( rtrtbitem_qty_retur ) AS sumretur,
											SUM ( rtrtbitem_qty_retur_verifikasi ) AS sumreturverifikasi,
											SUM ( rtrtbitem_qty_retur_plus ) AS sumreturplus,
											SUM ( rtrtbitem_qty_retur_verifikasi_plus ) AS sumreturverifikasiplus 
										FROM
											detail_returntb 
										GROUP BY
											header_idrtrtb,
											header_detailid,
											rtrtbitem_id
									) AS b ON a.header_idtb= b.header_idrtrtb 
									AND a.id_detailtb= b.header_detailid 
									AND a.tbitem_id= b.rtrtbitem_id
					GROUP BY header_idtb
				) as d ON a.id_tb=d.header_idtb ";		
	$sql.=" WHERE reqtb_user !='' AND reqtb_destination !='' and reqtb_destination='$store' ".$storepenerima." ".$storepengirim." ".$tanggalpengiriman." ".$statusdokumen." "; 
	$sql.=") sub ";
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO1".$sql."");
	$totalFiltered = sqlsrv_num_rows($query);
	$sql.=" WHERE rowNum > ".$requestData['start']." AND rowNum <= ".$requestData['start']." + ".$requestData['length'].""; 
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO2".$sql."");
	
} else {	

	$sql  = "SELECT *
	FROM 
	( 
							SELECT 
					a.id_tb,
					a.reqtb_code,
					convert(char(10),a.reqtb_date,126) req_date,
					b.req_type_name,
					c.req_type_name_item,
						a.reqtb_user,
						a.reqtb_destination,
					a.reqtb_reason,
					a.reqtb_note,
					a.reqtb_destination_approve,
					a.reqtb_user_verifikasi,
					reqtb_user_retur,
					reqtb_destination_retur_verifikasi,
					d.sumpeminjaman,
					d.sumpengembalian,
					COALESCE(d.sumpengembalian,0) - COALESCE(d.sumpeminjaman,0) as selisi,
					COALESCE(d.sumreturplus,0) - COALESCE(d.sumreturverifikasiplus,0) as kelebihan,
									ROW_NUMBER() OVER (ORDER BY id_tb desc) as rowNum 
							FROM header_tb a inner join mst_req_type b on a.reqtb_type=b.id_mst_type
				left join mst_req_type_item c on a.reqtb_item_type=c.id_mst_type_item
				LEFT JOIN (
						SELECT
									a.header_idtb,
					sum(a.tbitem_qty_verifikasi) sumpeminjaman,
					sum(b.sumreturverifikasi) sumpengembalian,
				sum(b.sumreturplus) sumreturplus,
				sum(b.sumreturverifikasiplus) sumreturverifikasiplus
					FROM
									detail_tb a
									LEFT JOIN (
										SELECT
											header_idrtrtb,
											header_detailid,
											rtrtbitem_id,
											SUM ( rtrtbitem_qty_retur ) AS sumretur,
											SUM ( rtrtbitem_qty_retur_verifikasi ) AS sumreturverifikasi,
											SUM ( rtrtbitem_qty_retur_plus ) AS sumreturplus,
											SUM ( rtrtbitem_qty_retur_verifikasi_plus ) AS sumreturverifikasiplus 
										FROM
											detail_returntb 
										GROUP BY
											header_idrtrtb,
											header_detailid,
											rtrtbitem_id
									) AS b ON a.header_idtb= b.header_idrtrtb 
									AND a.id_detailtb= b.header_detailid 
									AND a.tbitem_id= b.rtrtbitem_id
					GROUP BY header_idtb
				) as d ON a.id_tb=d.header_idtb
				WHERE reqtb_user !='' AND reqtb_destination !='' and reqtb_destination='$store'
	) sub ";
	$sql.=" WHERE reqtb_destination='$store' AND rowNum > ".$requestData['start']." AND rowNum <= ".$requestData['start']." + ".$requestData['length'].""; 
	$params = array();
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
 $query=sqlsrv_query($conn, $sql, $params, $options)  or die("data.php: get PO3'".$sql."'");

}

$data = array();
while( $row=sqlsrv_fetch_array($query) ) {  
 $nestedData=array(); 

	if($row['reqtb_destination_approve'] =='On Progress'){
		$reqtb_destination_approve='<span class="badge badge-warning">Menunggu Persetujuan - '.$row["reqtb_destination"].'</span>';
}else if($row['reqtb_destination_approve'] =='Approved'){
	if($row['reqtb_user_verifikasi'] =='Verifikasi'){
		if($row['reqtb_user_retur']==''){
		$reqtb_destination_approve='<span class="badge badge-warning">Menunggu Pengembalian Barang -  '.$row["reqtb_user"].'</span><br><p style="font-size:12px;"><b>Selisi Barang<br> Kekurangan : '.$row['selisi'].' </b></p>';
		}else{
			if($row['reqtb_destination_retur_verifikasi']==''){
				$reqtb_destination_approve='<span class="badge badge-warning">Menunggu Verifikasi Pengembalian Barang -  '.$row["reqtb_destination"].'</span><br><p style="font-size:12px;"><b>Selisi Barang<br> Kekurangan : '.$row['selisi'].' </b></p>';
			}else if($row['reqtb_destination_retur_verifikasi']=='Pending'){
				if($row['reqtb_user_retur']=='Approved'){
					$reqtb_destination_approve='<span class="badge badge-warning">Menunggu Verifikasi Revisi Pengembalian Barang -  '.$row["reqtb_destination"].'</span><br><p style="font-size:12px;"><b>Selisi Barang<br> Kekurangan : '.$row['selisi'].' </b></p>';
				}else{
				$reqtb_destination_approve='<span class="badge badge-warning">Menunggu Perbaikan Pengembalian Barang -  '.$row["reqtb_user"].'</span><br><p style="font-size:12px;"><b>Selisi Barang<br> Kekurangan : '.$row['selisi'].' </b></p>';
				}
			}else{
			$reqtb_destination_approve='<span class="badge badge-success">Selesai</span>';
			}
		}
	}elseif($row['reqtb_user_verifikasi'] =='Reject'){
			$reqtb_destination_approve='<span class="badge badge-danger">Ditolak - '.$row["reqtb_user"].'</span>';
	}else{
		$reqtb_destination_approve='<span class="badge badge-warning">Menunggu Pengecekan - '.$row["reqtb_user"].'</span>';
	}
	
}else if($row['reqtb_destination_approve'] =='Reject'){
	$reqtb_destination_approve='<span class="badge badge-danger">Ditolak - '.$row["reqtb_destination"].'</span>';
}else{
	$reqtb_destination_approve='';
}

if( $_SESSION["id_divisi"] == 9){

	if($row['reqtb_destination_approve'] =='Approved'){
			if($row['reqtb_user_verifikasi']=='Reject'){
				$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewrequesttbs.php?id='.$row["id_tb"].'"><b>Lihat Permintaan</b></a></br>';
			}else{
		if($row['reqtb_user_retur']=='Approved'){
			if($row['reqtb_destination_retur_verifikasi']=='Verifikasi'){

				if($row['reqtb_flag_plus'] > 0){
							$flag_plus ='&nbsp;&nbsp;</br><a data-toggle="tooltip" title="Cetak Dokumen Pengembalian" class="badge badge-success" href="cetakrevisipengembaliantbs.php?id='.$row["id_tb"].'"><b>Cetak Revisi Kelebihan Pengembalian</b></a>';
						}else{
							$flag_plus ='';
						}
						if($row['reqtb_flag_minus'] > 0){
							$flag_minus ='&nbsp;&nbsp;</br><a data-toggle="tooltip" title="Cetak Dokumen Pengembalian" class="badge badge-success" href="cetakrevisipengembaliantbs.php?id='.$row["id_tb"].'&minus='.$row['reqtb_flag_minus'].'"><b>Cetak Revisi Kekurangan Pengembalian</b></a>';
						}else{
							$flag_minus ='';
						}


				$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewrequesttbs.php?id='.$row["id_tb"].'"><b>Lihat Permintaan</b></a></br>
				<a data-toggle="tooltip" title="Cetak Dokumen Permintaan" class="badge badge-primary" href="cetakpermintaantbs.php?id='.$row["id_tb"].'"><b>Cetak Permintaan</b></a>';
			}else{
				$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewrequesttbs.php?id='.$row["id_tb"].'"><b>Lihat Permintaan</b></a></br>
				<a data-toggle="tooltip" title="Cetak Dokumen" class="badge badge-primary" href="cetakpermintaantbs.php?id='.$row["id_tb"].'"><b>Cetak Permintaan</b></a></br>
				<a title="Pengembalian Dokumen" class="badge badge-danger" href="viewverifikasireturtbs.php?id='.$row["id_tb"].'"><b>Mengunggu Pengecekan Pengembalian</b></a>'; 
			}
		}elseif($row['reqtb_user_retur']=='Pending'){



				$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewrequesttbs.php?id='.$row["id_tb"].'"><b>Lihat Permintaan</b></a></br>
				<a data-toggle="tooltip" title="Cetak Dokumen Permintaan" class="badge badge-primary" href="cetakpermintaantbs.php?id='.$row["id_tb"].'"><b>Cetak Permintaan</b></a>'; 



		}else{
				$action='<a data-toggle="tooltip" title="Lihat Dokumen" class="badge badge-warning" href="viewrequesttbs.php?id='.$row["id_tb"].'"><b>Lihat Permintaan</b></a></br>
				<a data-toggle="tooltip" title="Cetak Dokumen" class="badge badge-primary" href="cetakpermintaantbs.php?id='.$row["id_tb"].'"><b>Cetak Permintaan</b></a>'; 
		}
			}
		}else{
		$action='<a title="Menyetujui Dokumen" class="badge badge-danger" href="viewapproverequesttbs.php?id='.$row["id_tb"].'"><b>Belum Menyetujui</b></a>'; 
	}

}else{
	$action=''; 
}

 
 $nestedData[] = '<a href="#" class="code"><b>'.$row["reqtb_code"].'</b></a>';
 $nestedData[] = $row["req_date"]; 
	$nestedData[] = $row["req_type_name"]; 
	// $nestedData[] = $row["req_type_name_item"]; 
	$nestedData[] = $row["reqtb_user"]; 
	$nestedData[] = $row["reqtb_destination"]; 
	// $nestedData[] = $row["reqtb_reason"]; 
	$nestedData[] = $row["reqtb_note"]; 
	$nestedData[] = $reqtb_destination_approve; 
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
