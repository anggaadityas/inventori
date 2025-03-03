<?php
include "layouts/header.php";
$id=$_GET['id'];
$sqlheader = "SELECT *,convert(char(10),reqrtn_date,126) req_date,convert(char(10),reqrtn_nodoc_sap_date,126) date_posting,convert(char(10),reqrtn_destination_arrival_goods_date,126) date_arrival,a.reqrtn_reason,convert(char(20),a.created_date,120) date_submit FROM header_returnck a
 inner join mst_req_type b on a.reqrtn_type=b.id_mst_type
 left join mst_req_type_item  c on a.reqrtn_item_type=c.id_mst_type_item
 where id_rtn='$id'";
$stmtheader = sqlsrv_query( $conn, $sqlheader );
if( $stmtheader === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowheader = sqlsrv_fetch_array( $stmtheader, SQLSRV_FETCH_ASSOC);
include "layouts/navbar.php";
?>

<div class="container1">
 <div class="row">

<div class="col-sm-12" style="margin-top: 26px;" >
<span style="font-size:18px;"><b>* View Permintaan Retur Barang Store <?php echo $rowheader['reqrtn_code']; ?></b></span>
<br><br><br>

<form>
        <fieldset >
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail"  value="<?php echo $rowheader['req_date']; ?>" readonly>
                </div>
            </div>

                   <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Submit Form Retur</label>
                <div class="col-sm-3">
                <input type="text" class="form-control" id="inputEmail"value="<?php echo $rowheader['date_submit']; ?>" readonly>
            </div>
        </div>
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Jenis Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail"  value="<?php echo $rowheader['req_type_name']; ?>" readonly>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label" >Jenis Barang</label>
                <div class="col-sm-3">
                <input type="text" class="form-control" id="inputEmail"  value="<?php echo $rowheader['req_type_name_item']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Divisi Tujuan</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail"  value="<?php echo $rowheader['reqrtn_destination']; ?>" readonly>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Alasan Permintaan</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail"  value="<?php echo $rowheader['reqrtn_reason']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Tipe</label>
                <div class="col-sm-2">
                <select class="form-control" name="reqrtn_type_req" readonly>
                    <option value="1" <?php echo ($rowheader['reqrtn_type_req'] ==  '1') ? ' selected="selected"' : '';?>>Sistem</option>
                    <option value="2" <?php echo ($rowheader['reqrtn_type_req'] ==  '2') ? ' selected="selected"' : '';?>>Non Sistem</option>
                    <option value="3" <?php echo ($rowheader['reqrtn_type_req'] ==  '3') ? ' selected="selected"' : '';?>>Wadah</option>
                     <option value="4" <?php echo ($rowheader['reqrtn_type_req'] ==  '4') ? ' selected="selected"' : '';?>>NCR</option>
                     <option value="5" <?php echo ($rowheader['reqrtn_type_req'] ==  '5') ? ' selected="selected"' : '';?>>DAMAGE</option>
                </select>
                </div>
            </div>
           <?php
            if($rowheader['reqrtn_destination'] == 'CK JAKARTA' OR $rowheader['reqrtn_destination']== 'CK SURABAYA'){
             ?>
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">No Dokumen SAP</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control kode_sap" name="kode_sap" id="inputEmail" value="<?php echo $rowheader['reqrtn_nodoc_sap']; ?>" autocomplete="off" required readonly>
                </div>
            </div>

              <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Doc Num (Ireap)</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control no_ireap" name="no_ireap" id="inputEmail" value="<?php echo $rowheader['docnum_ireap']; ?>" autocomplete="off" required readonly>
                </div>
            </div>
             <?php
            }else if($rowheader['reqrtn_destination'] == 'ENG JAKARTA' OR $rowheader['reqrtn_destination']== 'ENG SURABAYA' 
            OR $rowheader['reqrtn_destination']== 'GA JAKARTA' OR $rowheader['reqrtn_destination']== 'GA SURABAYA'
            OR $rowheader['reqrtn_destination']== 'IT JAKARTA' OR $rowheader['reqrtn_destination']== 'IT SURABAYA' OR $rowheader['reqrtn_destination']== 'STORE'){
            ?>

            <!--    <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Alasan Retur</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control reqrtn_reason" name="reqrtn_reason" id="inputEmail" value="<?php echo $rowheader['reqrtn_reason']; ?>" autocomplete="off" required readonly>
                </div>
            </div>

            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">No PICA</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control no_pica" name="no_pica" id="inputEmail" value="<?php echo $rowheader['reqrtn_nopica']; ?>" autocomplete="off" required readonly>
                </div>
            </div>

            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">No SJT Barang Pengganti</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control kode_sap" name="kode_sap" id="inputEmail" value="<?php echo $rowheader['reqrtn_nodoc_sap']; ?>"autocomplete="off" required readonly>
                </div>
            </div> -->

              <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">File Upload</label>
                <div class="col-sm-3">

            <?php

            $sqldokumen = "SELECT * FROM retur_dokumen where reqrtn_id='$id'";

            $stmtdokumen = sqlsrv_query( $conn, $sqldokumen );
            if( $stmtdokumen === false) {
                die( print_r( sqlsrv_errors(), true) );
            }
            $no=0;
         while ($dokumen = sqlsrv_fetch_array( $stmtdokumen, SQLSRV_FETCH_ASSOC)) {
            
            echo '<a href="dokumen/'.$dokumen["nama_dokumen"].'" target="_blank"><img id="myImg" class="myImg" style="width:300px;max-width:300px"  src="dokumen/'.$dokumen["nama_dokumen"].'"></a>
            ';   

            $no++; 

      }

      ?>

       </div>
            </div>

            <?php
            }
            ?>
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Posting Dokumen SAP</label>
                <div class="col-sm-3">
                   <input type="text" class="form-control date_posting" name="date_posting" id="date_posting" value="<?php echo $rowheader['date_posting']; ?>" readonly >
                </div>
            </div>
                <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Kedatangan Barang Di Divisi Tujuan</label>
                <div class="col-sm-3">
                   <input type="text" class="form-control date_posting" name="date_posting" id="date_posting" value="<?php echo $rowheader['date_arrival']; ?>"  readonly >
                </div>
            </div>

            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan Permintaan</label>
                <div class="col-sm-3">
               <textarea name="note_request" class="form-control" id="note_request" cols="10" rows="5" readonly><?php echo $rowheader['reqrtn_note']; ?>
               </textarea>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Status Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqrtn_destination_approve']; ?>" readonly>
                </div>
            </div> -->
        </fieldset>

<br><br>


<div style="overflow-x:auto;">
   <table  class="table table-striped table-bordered dt-responsive nowrap">
   <thead>
    <?php 

        // if($rowheader['reqrtn_destination'] =='ENG JAKARTA' || $rowheader['reqrtn_destination'] == 'ENG SURABAYA' || $rowheader['reqrtn_destination'] == 'IT JAKARTA' || $rowheader['reqrtn_destination'] == 'IT SURABAYA' || $rowheader['reqrtn_destination'] == 'GA JAKARTA' || $rowheader['reqrtn_destination'] == 'GA SURABAYA' || $rowheader['reqrtn_destination'] == 'OPENING'){

           if($rowheader['reqrtn_destination'] =='ENG JAKARTA' || $rowheader['reqrtn_destination'] == 'ENG SURABAYA'){
                    ?>

                     <tr>
                 <th rowspan="2">No</th>
                   <th rowspan="2">Kode Barang</th>
                   <th rowspan="2">Nama Barang</th>
                    <th rowspan="2">Satuan</th>
                    <th rowspan="2">Jenis Barang</th>
                    <th rowspan="2">Alasan</th>
                    <th rowspan="2" class="text-center">Jumlah Toko</th>
                    <th rowspan="2">Kadaluarsa </th>
                    <th rowspan="2">Tanggal Kedatangan </th>
                    <th rowspan="2">Nomor SJT</th>
                    <th rowspan="2">Status Persetujuan</th>
                    <th rowspan="2">Keterangan Persetujuan</th>
                    <th rowspan="2" class="text-center">Jumlah Verifikasi</th>
                    <th rowspan="2">Keterangan Verfikasi</th>
<!--                     </tr>
                <tr>
                    <th>Bagus</th>
                    <th>Tidak Bagus</th>
                    <th>Bagus</th>
                    <th>Tidak Bagus</th>
                    </tr>   -->  



    <?php
    }else{

    ?>
                <tr>
                 <th rowspan="2">No</th>
                   <th rowspan="2">Kode Barang</th>
                   <th rowspan="2">Nama Barang</th>
                    <th rowspan="2">Satuan</th>
                    <th rowspan="2">Jenis Barang</th>
                    <th rowspan="2">Alasan</th>
                    <th colspan="2" class="text-center">Jumlah Toko</th>
                    <th rowspan="2">Kadaluarsa </th>
                    <th rowspan="2">Tanggal Kedatangan </th>
                    <th rowspan="2">Keterangan Toko</th>
                    <th rowspan="2">Status Persetujuan</th>
                    <th rowspan="2">Keterangan Persetujuan</th>
                    <th colspan="2" class="text-center">Jumlah Verifikasi</th>
                    <th rowspan="2">Keterangan Verfikasi</th>
                    </tr>
                <tr>
                    <th>Bagus</th>
                    <th>Tidak Bagus</th>
                    <th>Bagus</th>
                    <th>Tidak Bagus</th>
                    </tr>    

                     <?php
    }

    ?>
                </thead>
    <tbody>

<p><b>Cetak Surat Jalan : </b></p>
<?php
$sqlcat = "SELECT header_idrtn,rtnitem_cat FROM detail_returnck 
where header_idrtn='$id' AND  rtnitem_status_approve=0
GROUP BY header_idrtn,rtnitem_cat";
$stmtcat = sqlsrv_query( $conn, $sqlcat );
if( $stmtcat === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$no=0;
while($rowcat = sqlsrv_fetch_array( $stmtcat, SQLSRV_FETCH_ASSOC)){
 $no++;
?>

<p><?php echo $no; ?>.  <a target="_blank" href="cetakreturn.php?id=<?php echo $id;?>&cat=<?php echo $rowcat['rtnitem_cat']; ?>" class="badge badge-pill badge-success"><b><?php echo $rowcat['rtnitem_cat']; ?></b></a>


<?php
}
?>


<?php

        $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,126) expired,convert(char(10),rtnitem_arrival,126) arrival_date  FROM detail_returnck a 
        inner join mst_item b on a.rtnitem_id=b.id_mst_item where header_idrtn='$id' order by rtnitem_cat asc";
        $stmtdetail = sqlsrv_query( $conn, $sqldetail );
        if( $stmtdetail === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $no=0;
        while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){
        if($rowdetail['sap_flag'] == 1){
            $color="style='background-color:#FFC300;'";
        }else{
            $color="";
        }

        if($rowdetail['rtnitem_status_approve']==0){
            $status ='Disetujui';
        }else{
            $status ='Tidak Disetujui';
        }
        
        $no++;

    ?>


           <?php 

     // if($rowheader['reqrtn_destination'] =='ENG JAKARTA' || $rowheader['reqrtn_destination'] == 'ENG SURABAYA' || $rowheader['reqrtn_destination'] == 'IT JAKARTA' || $rowheader['reqrtn_destination'] == 'IT SURABAYA' || $rowheader['reqrtn_destination'] == 'GA JAKARTA' || $rowheader['reqrtn_destination'] == 'GA SURABAYA' || $rowheader['reqrtn_destination'] == 'OPENING'){

              if($rowheader['reqrtn_destination'] =='ENG JAKARTA' || $rowheader['reqrtn_destination'] == 'ENG SURABAYA'){
                    ?>
                        <tr>
              <td scope="row"><?php echo $no; ?></td>
              <td class="text" <?php echo $color; ?>><b><?php echo $rowdetail['rtnitem_code']; ?></b></td>
              <td align="left"><?php echo htmlspecialchars_decode($rowdetail['rtnitem_name']); ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_uom']; ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_cat']; ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_reason']; ?></td>
              <!-- <td align="left"><?php echo $rowdetail['rtnitem_qty_good']; ?></td>  -->
              <td align="left"><?php echo number_format($rowdetail['rtnitem_qty_not_good'],2,'.',','); ?></td> 
              <td align="left"><?php echo $rowdetail['expired']; ?></td> 
              <td align="left"><?php echo $rowdetail['arrival_date']; ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_remarks']; ?></td>
              <td align="left"><?php echo $status; ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_remarks_approve']; ?></td>
              <!-- <td align="left"><?php echo $rowdetail['rtnitem_qty_verifikasi_good']; ?></td> -->
              <td align="left"><?php echo number_format($rowdetail['rtnitem_qty_verifikasi_not_good'],2,'.',','); ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_remarks_verifikasi']; ?></td>
            </tr>

        <?php
    }else{
        ?>

              <tr>
              <td scope="row"><?php echo $no; ?></td>
              <td class="text" <?php echo $color; ?>><b><?php echo $rowdetail['rtnitem_code']; ?></b></td>
              <td align="left"><?php echo htmlspecialchars_decode($rowdetail['rtnitem_name']); ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_uom']; ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_cat']; ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_reason']; ?></td>
              <td align="left"><?php echo number_format($rowdetail['rtnitem_qty_good'],2,'.',','); ?></td> 
              <td align="left"><?php echo number_format($rowdetail['rtnitem_qty_not_good'],2,'.',','); ?></td> 
              <td align="left"><?php echo $rowdetail['expired']; ?></td> 
              <td align="left"><?php echo $rowdetail['arrival_date']; ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_remarks']; ?></td>
              <td align="left"><?php echo $status; ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_remarks_approve']; ?></td>
              <td align="left"><?php echo number_format($rowdetail['rtnitem_qty_verifikasi_good'],2,'.',','); ?></td>
              <td align="left"><?php echo number_format($rowdetail['rtnitem_qty_verifikasi_not_good'],2,'.',','); ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_remarks_verifikasi']; ?></td>
            </tr>

    <?php
    }
    ?>


    <?php
    }
    ?>

    </tbody>

</table> 
</div>
<br> <br> <br>       
        
        <!-- <div align="right">    
        <br>

        <div class="col-sm-3">
        <select name="status_request" id="status_request" class="form-control">
        <option value="">-- Pilih Status Permintaan --</option>
        <option value="Approved">Di Setujui</option>
        <option value="Reject">Di Tolak</option>
        </select>
        <br>   
<button type="button" class="btn btn-primary" ><b>Proses Permintaan</b></button>  
<br><br><br>  
        </div>    

        </div> -->

 </div>

   

 <script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.validate.min.js"></script>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap4.min.js"></script>
<script src="js/dataTables.responsive.min.js"></script>
<script src="js/responsive.bootstrap4.min.js"></script>
<script src="js/tagsinput.js"></script>
<script src="js/select2.min.js"></script>
 <script src="js/jquery-ui.js"></script>
 <script src="js/sweetalert2.all.min.js"></script>
<script>
$(".delete").on('click', function() {
	$('.case:checkbox:checked').parents("tr").remove();
    $('.check_all').prop("checked", false); 
	check();

});
var i=2;
$(".addmore").on('click',function(){
	count=$('table tr').length;
    var data="<tr><td><input type='checkbox' class='case'/></td><td><span id='snum"+i+"'>"+count+".</span></td>";
    data +="<td><input type='text' id='item_code"+i+"' name='item_code[]'/></td> <td><input type='text' id='item_name"+i+"' name='item_name[]'/></td><td><input type='text' id='pcs"+i+"' name='pcs[]'/></td><td><input type='text' id='qty"+i+"' name='qty[]'/></td><td><textarea type='text' id='item_note"+i+"' name='item_note[]'></textarea></td></tr>";
	$('table').append(data);
	i++;
});

function select_all() {
	$('input[class=case]:checkbox').each(function(){ 
		if($('input[class=check_all]:checkbox:checked').length == 0){ 
			$(this).prop("checked", false); 
		} else {
			$(this).prop("checked", true); 
		} 
	});
}

function check(){
	obj=$('table tr').find('span');
	$.each( obj, function( key, value ) {
	id=value.id;
	$('#'+id).html(key+1);
	});
	}

</script>
</body>
</html>       