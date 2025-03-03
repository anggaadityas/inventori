<?php
include "layouts/header.php";
$id=$_GET['id'];
$sqlheader = "SELECT *,convert(char(10),reqtp_date,126) req_date,convert(char(10),reqtp_nodoc_sap_date,126) date_posting,convert(char(10),reqtp_user_verifikasi_date,126) req_date_verifikasi FROM header_tp a
 left join mst_req_type b on a.reqtp_type=b.id_mst_type
 left join mst_req_type_item  c on a.reqtp_item_type=c.id_mst_type_item
 where id_tp='$id'";
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
<span style="font-size:18px;"><b>* View Permintaan Transfer Putus Store <?php echo $rowheader['reqtp_code']; ?></b></span>
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
                <label for="inputPassword" class="col-sm-2 col-form-label">Toko Pengirim</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail"  value="<?php echo $rowheader['reqtp_destination']; ?>" readonly>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Alasan Permintaan</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail"  value="<?php echo $rowheader['reqtp_reason']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan Permintaan</label>
                <div class="col-sm-3">
               <textarea name="note_request" class="form-control" id="note_request" cols="10" rows="5" readonly><?php echo $rowheader['reqtp_note']; ?>
               </textarea>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Kode SAP</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqtp_nodoc_sap']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Verifikasi Toko Penerima</label>
                <div class="col-sm-3">
                   <input type="text" class="form-control req_date_verifikasi" name="req_date_verifikasi" id="req_date_verifikasi" value="<?php echo $rowheader['req_date_verifikasi']; ?>" placeholder="Pilih Tanggal Posting SAP" readonly >
                </div>
            </div>
               <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Posting Dokumen SAP</label>
                <div class="col-sm-3">
                   <input type="text" class="form-control date_posting" name="date_posting" id="date_posting" value="<?php echo $rowheader['date_posting']; ?>" placeholder="Pilih Tanggal Posting SAP" readonly >
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Status Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqtp_destination_approve']; ?>" readonly>
                </div>
            </div> -->
        </fieldset>

<br><br>


   <table  class="table table-striped table-bordered dt-responsive nowrap">

                <tr>
                 <th rowspan="2">No</th>
                   <th rowspan="2">Kode Barang</th>
                   <th rowspan="2">Nama Barang</th>
                    <th rowspan="2">Satuan</th>
                    <th rowspan="2">Jenis Barang</th>
                    <th rowspan="2">Alasan</th>
                    <th rowspan="2">Jumlah Permintaan</th>
                    <th rowspan="2">Jumlah Disetujui</th>
                    <!-- <th>Kondisi Barang Disetujui</th> -->
                    <th rowspan="2">Kadaluarsa Barang</th>
                    <th rowspan="2">Keterangan Disetujui</th>
                    <th colspan="2"  class="text-center">Jumlah Verfikasi </th>
                    <!-- <th>Kondisi Barang Verfikasi</th> -->
                    <th rowspan="2">Keterangan Verfikasi</th>
                </tr>
                <tr>
                    <th>Bagus</th>
                    <th>Tidak Bagus</th>
                    </tr>    

                </thead>
    <tbody>
<?php

        $sqldetail = "SELECT *,convert(char(10),tpitem_expired,126) expired FROM detail_tp where header_idtp='$id'";
        $stmtdetail = sqlsrv_query( $conn, $sqldetail );
        if( $stmtdetail === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $no=0;
        while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){
            // $fixqtyver =0;
            // $fixqtyver = $rowdetail['tpitem_qty_verifikasi_good'] + $rowdetail['tpitem_qty_verifikasi_not_good'];
        $no++;

    ?>
    <tr>
              <td scope="row"><?php echo $no; ?></td>
              <td class="text-muted"><?php echo $rowdetail['tpitem_code']; ?></td>
              <td align="left"><?php echo htmlspecialchars_decode($rowdetail['tpitem_name']); ?></td>
              <td align="left"><?php echo $rowdetail['tpitem_uom']; ?></td>
              <td align="left"><?php echo $rowdetail['tpitem_cat']; ?></td>
              <td align="left"><?php echo $rowdetail['tpitem_reason']; ?></td>
              <td align="left"><?php echo number_format($rowdetail['tpitem_qty'],2,'.',','); ?></td> 
              <td align="left"><?php echo number_format($rowdetail['tpitem_qty_approve'],2,'.',','); ?></td>
              <td align="left"><?php echo $rowdetail['expired']; ?></td>
              <td align="left"><?php echo $rowdetail['tpitem_remarks_approve']; ?></td>
              <td align="left"><?php echo number_format($rowdetail['tpitem_qty_verifikasi_good'],2,'.',','); ?></td>
              <td align="left"><?php echo number_format($rowdetail['tpitem_qty_verifikasi_not_good'],2,'.',','); ?></td>
              <td align="left"><?php echo  $rowdetail['tpitem_remarks_verifikasi']; ?></td>
            </tr>

    <?php
    }
    ?>

    </tbody>

</table> 
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