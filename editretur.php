<?php
include "layouts/header.php";
$id=$_GET['id'];
$sqlheader = "SELECT *,convert(char(10),reqrtn_date,126) req_date,convert(char(10),reqrtn_nodoc_sap_date,126) date_posting,convert(char(10),reqrtn_destination_approve_date,126) date_verifikasi FROM header_returnck a
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
<span style="font-size:18px;"><b>* View Revisi Permintaan Retur Barang (Wadah) Store <?php echo $rowheader['reqrtn_code']; ?></b></span>
<br><br><br>

<form method="POST" action="editreturproses.php"  id="sender_container">
        <fieldset >
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                <div class="col-sm-3">
                <input type="hidden" class="form-control" id="inputEmail" name="reqrtn_code"  value="<?php echo $rowheader['reqrtn_code']; ?>" readonly>
                <input type="hidden" class="form-control" id="inputEmail" name="id_rtn"  value="<?php echo $rowheader['id_rtn']; ?>" readonly>
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['req_date']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Jenis Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control"  id="inputEmail"  value="<?php echo $rowheader['req_type_name']; ?>" readonly>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label" >Jenis Barang</label>
                <div class="col-sm-3">
                <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['req_type_name_item']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Toko Asal</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail" name="reqrtn_user" value="<?php echo $rowheader['reqrtn_user']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Divisi Tujuan</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail" name="reqrtn_destination" value="<?php echo $rowheader['reqrtn_destination']; ?>" readonly>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Alasan Permintaan</label>
                <div class="col-sm-2">
                <input type="text" name="alasan" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqrtn_reason']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan Permintaan</label>
                <div class="col-sm-3">
               <textarea name="note_request" class="form-control" name="keterangan" cols="10" rows="5" readonly><?php echo $rowheader['reqrtn_note']; ?>
               </textarea>
                </div>
            </div>
            <?php
            if($_SESSION['area_div'] == 'CK JAKARTA' OR $_SESSION['area_div']== 'CK SURABAYA'){
             ?>
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">No Dokumen SAP</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control kode_sap" name="kode_sap" id="inputEmail" value="<?php echo $rowheader['reqrtn_nodoc_sap']; ?>"  placeholder="Input No Dokumen SAP" autocomplete="off" required>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Posting Dokumen SAP</label>
                <div class="col-sm-3">
                   <input type="text" class="form-control date_posting" name="date_posting" id="date_posting" value="<?php echo $rowheader['date_posting']; ?>" placeholder="Pilih Tanggal Posting SAP"  autocomplete="off" required>
                </div>
            </div>
             <?php
            }else if($_SESSION['area_div'] == 'ENG JAKARTA' OR $_SESSION['area_div']== 'ENG SURABAYA'){
            ?>
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">No Dokumen SAP (SJT)</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control kode_sap" name="kode_sap" id="inputEmail" value="<?php echo $rowheader['reqrtn_nodoc_sap']; ?>"  placeholder="Input No Dokumen SAP" autocomplete="off" required>
                </div>
            </div>
            <?php
            }
            ?>
            <!-- <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Status Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqrtn_destination_approve']; ?>" readonly>
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
                    <th colspan="2" class="text-center">Jumlah Barang</th>
                    <th rowspan="2">Kadaluarsa</th>
                    <th rowspan="2">Keterangan</th>
                    <tr>
                    <th>Bagus</th>
                    <th>Tidak Bagus</th>
                    </tr>
                </tr>
   

                </thead>
    <tbody>
<?php

        $sqldetail = "SELECT *,convert(char(10),rtnitem_expired,126) expired FROM detail_returnck a inner join mst_item b on a.rtnitem_id=b.id_mst_item where header_idrtn='$id'";
        $stmtdetail = sqlsrv_query( $conn, $sqldetail );
        if( $stmtdetail === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $no=0;
        while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){
        $no++;

        $qtyver =$rowdetail['rtnitem_qty_verifikasi_good'] + $rowdetail['rtnitem_qty_verifikasi_not_good'];
        $qtyreq = $rowdetail['rtnitem_qty_good']+ $rowdetail['rtnitem_qty_not_good'];
        $selisi = $qtyver - $qtyreq;

        if($rowdetail['rtnitem_qty_good'] ==""){
            $vergood = 0;
        }else{
            $vergood = $rowdetail['rtnitem_qty_good'];
        }
        if($rowdetail['rtnitem_qty_not_good'] ==""){
            $vernotgood = 0;
        }else{
            $vernotgood = $rowdetail['rtnitem_qty_not_good'];
        }

        if($rowdetail['kondisi_flag']==1){
            $good="<input type='text' id='qtygood_".$no."' min='".$rowdetail['rtnitem_qty_good']."' name='qty_good[]' value='".$vergood."' style='width:70px' class='form-control qtygood' autocomplete='off' required/><span style='font-size:12px;'><b>Revisi</b></span>";
            $notgood="<input type='hidden' id='qtynotgood_".$no."' min='".$rowdetail['rtnitem_qty_not_good']."' name='qty_notgood[]' value='".$vernotgood."' style='width:70px' class='form-control qtynotgood' autocomplete='off'/>";
        }else if($rowdetail['kondisi_flag']==2){
            $good="<input type='hidden' id='qtygood_".$no."'  min='".$rowdetail['rtnitem_qty_good']."' name='qty_good[]' value='".$vergood."' style='width:70px' class='form-control qtygood' autocomplete='off'/>";
            $notgood="<input type='text' id='qtynotgood_".$no."'  min='".$rowdetail['rtnitem_qty_not_good']."' name='qty_notgood[]' value='".$vernotgood."' style='width:70px' class='form-control qtynotgood' autocomplete='off' required/><span style='font-size:12px;'><b>Revisi</b></span>";
        }else{
            // if($rowdetail['rtnitem_qty_good'] <= 0){
            //     $good="<input type='hidden' id='qtygood_".$no."' min='0' max='".$rowdetail['rtnitem_qty_good']."' name='qty_good[]' value='".$vergood."' style='width:70px' class='form-control' autocomplete='off' required/></span>";
            //     $notgood="<input type='number' id='qtynotgood_".$no."' min='0' max='".$rowdetail['rtnitem_qty_not_good']."' name='qty_notgood[]' value='".$rowdetail['rtnitem_qty_verifikasi_not_good']."' style='width:70px' class='form-control qtynotgood' autocomplete='off' required/><span style='font-size:12px;'><b>Verifikasi</b></span>";
            // }else if($rowdetail['rtnitem_qty_not_good'] <= 0 ){
            //     $good="<input type='number' id='qtygood_".$no."' min='0' max='".$rowdetail['rtnitem_qty_good']."' name='qty_good[]' value='".$rowdetail['rtnitem_qty_verifikasi_good']."' style='width:70px' class='form-control qtygood' autocomplete='off' required/><span style='font-size:12px;'><b>Verifikasi</b></span>";
            //     $notgood="<input type='hidden' id='qtynotgood_".$no."' min='0' max='".$rowdetail['rtnitem_qty_not_good']."' name='qty_notgood[]' value='".$vernotgood."' style='width:70px' class='form-control' autocomplete='off' required/>";
            // }else{
                $good="<input type='text' id='qtygood_".$no."' min='0'  name='qty_good[]' value='".number_format($vergood,2,'.',',')."' style='width:70px' class='form-control qtygood' autocomplete='off' required/><span style='font-size:12px;'><b>Revisi</b></span>";
                $notgood="<input type='text' id='qtynotgood_".$no."' min='0' name='qty_notgood[]'  value='".number_format($vernotgood,2,'.',',')."' style='width:70px' class='form-control qtynotgood' autocomplete='off' required/><span style='font-size:12px;'><b>Revisi</b></span>";
            // }
        }

        if($rowdetail['sap_flag'] == 1){
            $color="style='background-color:#FFC300;'";
        }else{
            $color="";
        }

    ?>
    <tr>
              <td scope="row"><?php echo $no; ?><input type="hidden" name="id_barang[]" value="<?php echo $rowdetail['rtnitem_id']; ?>"></td>
              <td class="text" <?php echo $color; ?>><b><?php echo $rowdetail['rtnitem_code']; ?></b></td>
              <td align="left"><?php echo $rowdetail['rtnitem_name']; ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_uom']; ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_cat']; ?></td>
              <td align="left"><?php echo $rowdetail['rtnitem_reason']; ?></td>
              <td align="center">
                  <?php echo $good; ?>
            </td> 
              <td align="center">
              <?php echo $notgood; ?> 
              <td align="left"><?php echo $rowdetail['expired']; ?></td> 
                <td align="left"><textarea type='text' class="form-control keterangan_barang"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110" id='keteranganbarang_0' name='keterangan_barangverifikasi[]' cols="30" rows="5"><?php echo $rowdetail['rtnitem_remarks']; ?></textarea>
            </td>
 </tr>

    <?php
    }
    ?>

    </tbody>

</table> 
        
        <div align="right">    
 

        <div class="col-sm-3">

        <div class="form-group row">
                <label for="inputEmail" class="col-sm-12 col-form-label">Keterangan Status Revisi</label>
                <div class="col-sm-12">
               <textarea name="note_request_verifkasi" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110"  cols="10" rows="5"><?php echo $rowheader['reqrtn_destination_approve_note']; ?></textarea>
                </div>
            </div>

            <select name="status_request" id="status_request" class="form-control" required style="display: none;">
        <option value="Verifikasi" <?php if($rowheader['reqrtn_destination_approve']=="Verifikasi") echo "selected"; ?>>Di Setujui</option>
        <!-- <option value="Reject" <?php if($rowheader['reqrtn_destination_approve']=="Reject") echo "selected"; ?>>Di Tolak</option> -->
        </select>
        <br>
<button type="submit" class="btn btn-primary"><b>Proses Permintaan</b></button>  
<br><br><br>  
        </div>    

        </div>

 </div>

   </form>

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
    <script src="js/jquery.inputmask.bundle.min.js" charset="utf-8"></script>
<script>

       $('.qtygood, .qtynotgood').inputmask({
    alias:"decimal",
    digits:2,
    repeat:13,
    digitsOptional:false,
    decimalProtect:true,
    groupSeparator:".",
    placeholder: '0',
    radixPoint:".",
    radixFocus:true,
    autoGroup:true,
    autoUnmask:false,
    onBeforeMask: function (value, opts) {
        return value;
    },
    removeMaskOnSubmit:true
}); 


$("body").on("keydown", ".keterangan_barang", function() {
   var x = event.which;
   if (x === 13) {
       event.preventDefault();
   } 
});

$("body").on("focus", "#date_posting", function() {
    $(this).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd",
        maxDate: 0,
        onSelect: function(selectedDate) {}
      });
});

$("body").on("focus", "#date_verifikasi", function() {
    $(this).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd",
        // maxDate: 0,
        onSelect: function(selectedDate) {}
      });
});

$(document).on('keyup keydown change','.qtygood',function(){

id_arr = $(this).attr('id');
id = id_arr.split("_");


var qtyreq= $('#qtyreq_'+id[1]).val();
var qtygood = $('#qtygood_'+id[1]).val();
var qtynotgood = $('#qtynotgood_'+id[1]).val();
var totalqty = parseInt(qtygood) + parseInt(qtynotgood);
var subtotalqty =  parseInt(totalqty) - parseInt(qtyreq);
$('#qtyverifikasi_'+id[1]).val(subtotalqty);

if(subtotalqty > 0){
    var ver = $('#qtyverifikasi1_'+id[1]).val();
    $('#qtynotgood_'+id[1]).val(0);
        $('#qtygood_'+id[1]).val(0);
        $('#qtyverifikasi_'+id[1]).val(ver);
    $('#qtyverifikasi_'+id[1]).css({'background-color' : '#F61656'}); 
}else if(subtotalqty < 0){
   $('#qtyverifikasi_'+id[1]).css({'background-color' : '#F61656'}); 
}else{
    $('#qtyverifikasi_'+id[1]).css({'background-color' : '#FFFFFF'}); 
}

});

$(document).on('keyup keydown change','.qtynotgood',function(){

id_arr = $(this).attr('id');
id = id_arr.split("_");

var qtyreq= $('#qtyreq_'+id[1]).val();
var qtygood = $('#qtygood_'+id[1]).val();
var qtynotgood = $('#qtynotgood_'+id[1]).val();
var totalqty = parseInt(qtygood) + parseInt(qtynotgood);
var subtotalqty =  parseInt(totalqty) - parseInt(qtyreq);
$('#qtyverifikasi_'+id[1]).val(subtotalqty);

if(subtotalqty > 0){
    var ver = $('#qtyverifikasi1_'+id[1]).val();
    $('#qtynotgood_'+id[1]).val(0);
        $('#qtygood_'+id[1]).val(0);
        $('#qtyverifikasi_'+id[1]).val(ver);
    $('#qtyverifikasi_'+id[1]).css({'background-color' : '#F61656'}); 
}else if(subtotalqty < 0){
   $('#qtyverifikasi_'+id[1]).css({'background-color' : '#F61656'}); 
}else{
    $('#qtyverifikasi_'+id[1]).css({'background-color' : '#FFFFFF'}); 
}

});

$('#sender_container').submit(function(e){

e.preventDefault();   
var valid=true;  
var status_verifikasi = $('#status_request :selected').val();
var date_verifikasi = $('#date_verifikasi').val();

 console.log('ok');
 swal.fire({
     title: "Proses?",
     icon: 'question',
     text: "Yakin Ingin Proses Data Ini?",
     type: "warning",
     showCancelButton: !0,
     confirmButtonText: "Ya, Proses!",
     cancelButtonText: "Tidak, Proses!",
     closeOnConfirm: false,
     showLoaderOnConfirm: true,
         preConfirm: function() {
             return new Promise(function(resolve, reject) {
             // here should be AJAX request
             $.ajax({
                     url: "editreturproses.php",
                     type: "POST",
                     data: $('#sender_container').serialize(), //serialize() untuk mengambil semua data di dalam form
                     dataType: "html",
                     success: function (response) {
             swal.fire("Berhasil!", response, "success");
                             // refresh page after 2 seconds
                             setTimeout(function(){
                                 location.reload();
                             },5000);
                            location.href='listreturn.php';
              },
                     error: function (xhr, ajaxOptions, thrownError) {
                         setTimeout(function(){
                             swal("Error", "Tolong Cek Koneksi Lalu Ulangi", "error");
                         }, 5000);}
             });

             });
         },
 }).then(function (e) {

     if (e.value === true) {       

     } else {
         e.dismiss;
     }

 }, function (dismiss) {
     return false;
 })


 });




</script>
</body>
</html>       