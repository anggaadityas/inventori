<?php
include "layouts/header.php";
include "layouts/navbar.php";
$id=$_GET['id'];
$sqlheader = "SELECT *,convert(char(10),reqtp_date,126) req_date,convert(char(10),reqtp_user_verifikasi_date,126) date_verifikasi FROM header_tp a
 left join mst_req_type b on a.reqtp_type=b.id_mst_type
 left join mst_req_type_item  c on a.reqtp_item_type=c.id_mst_type_item
 where id_tp='$id'";

$stmtheader = sqlsrv_query( $conn, $sqlheader );
if( $stmtheader === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowheader = sqlsrv_fetch_array( $stmtheader, SQLSRV_FETCH_ASSOC);
if($rowheader['reqtp_destination_approve'] == 'Approved'){
    $statusheader = 'Disetujui';
 }else{
    $statusheader = 'Tidak disetujui';
 }
?>

<style>.error {
    border: 1px solid red;
}</style>

<div class="container1">
 <div class="row">

<div class="col-sm-12" style="margin-top: 26px;" >
<span style="font-size:18px;"><b>* View Verifikasi Permintaan Transfer Putus Store <?php echo $rowheader['reqtp_code']; ?></b></span>
<br><br><br>

<form method="POST" action="viewverifikasirequestproses.php" id="sender_container">
        <fieldset >
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                <div class="col-sm-3">
                <input type="hidden" class="form-control" id="inputEmail" name="reqtp_code"  value="<?php echo $rowheader['reqtp_code']; ?>" readonly>
                <input type="hidden" class="form-control" id="inputEmail" name="id_tp"  value="<?php echo $rowheader['id_tp']; ?>" readonly>
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
                <label for="inputPassword" class="col-sm-2 col-form-label">Toko Penerima</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail" name="reqtp_user" value="<?php echo $rowheader['reqtp_user']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Toko Pengirim</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail" name="reqtp_destination" value="<?php echo $rowheader['reqtp_destination']; ?>" readonly>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Alasan Permintaan</label>
                <div class="col-sm-2">
                <input type="text" name="alasan" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqtp_reason']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan Permintaan</label>
                <div class="col-sm-3">
               <textarea name="note_request" class="form-control" name="keterangan" cols="10" rows="5" readonly><?php echo $rowheader['reqtp_note']; ?>
               </textarea>
                </div>
            </div>

            <!-- <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Kode SAP</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqtp_nodoc_sap']; ?>" readonly>
                </div>
            </div> -->
            <!-- <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Status Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $statusheader; ?>" readonly>
                </div>
            </div> -->
            <!-- <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Sampai Barang</label>
                <div class="col-sm-3">
                   <input type="text" class="form-control date_verifikasi" name="date_verifikasi" id="date_verifikasi" value="<?php echo $rowheader['date_verifikasi']; ?>" placeholder="Pilih Tanggal Sampai Barang" autocomplete="off" required>
                </div>
            </div> -->
        </fieldset>

<br><br>


   <table  class="table table-striped table-bordered dt-responsive nowrap">

                <tr>
                 <th  rowspan="2">No</th>
                   <th  rowspan="2">Kode Barang</th>
                   <th  rowspan="2">Nama Barang</th>
                    <th  rowspan="2">Satuan</th>
                    <th  rowspan="2">Jenis Barang</th>
                    <th  rowspan="2">Jumlah Disetujui</th>
                    <!-- <th>Verifikasi</th> -->
                    <!-- <th>Kondisi Barang</th> -->
                    <th  colspan="2" class="text-center">Verifikasi  Barang </th>
                    <th  rowspan="2">Selisih</th>
                    <th  rowspan="2">Kadaluarsa Barang</th>
                    <th  rowspan="2">Keterangan Barang</th>
                    <th rowspan="2">Verifikasi Keterangan Barang</th>
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
        $no++;
        $qtyver = number_format($rowdetail['tpitem_qty_verifikasi_good'],2,'.',',') + number_format($rowdetail['tpitem_qty_verifikasi_not_good'],2,'.',',');
        if($qtyver == ''){
            $fixqtyver='0.00';
        }else{
            $fixqtyver=$qtyver;
        }
        $selisi =  number_format($fixqtyver,2,'.',',') - number_format($rowdetail['tpitem_qty_approve'],2,'.',',');
    ?>
    <tr>
              <td scope="row"><?php echo $no; ?><input type="hidden" name="id_barang[]" value="<?php echo $rowdetail['tpitem_id']; ?>"></td>
              <td class="text-muted"><?php echo $rowdetail['tpitem_code']; ?></td>
              <td align="left"><?php echo $rowdetail['tpitem_name']; ?></td>
              <td align="left"><?php echo $rowdetail['tpitem_uom']; ?></td>
              <td align="left"><?php echo $rowdetail['tpitem_cat']; ?></td>
              <td align="left">
                 <?php echo number_format($rowdetail['tpitem_qty_approve'],2,'.',','); ?>
              <input type='hidden' id='qtyapprove_<?php echo $no; ?>' min="0" max="<?php echo number_format($rowdetail['tpitem_qty_approve'],2,'.',','); ?>"  name='qtyapprove[]' value="<?php echo number_format($rowdetail['tpitem_qty_approve'],2,'.',','); ?>" style='width:70px' class="form-control" autocomplete="off" readonly required/>
            </td> 
              <td align="left">
                  <input type='text' id='qtygood_<?php echo $no; ?>' name='qtyverifikasi_good[]' value="<?php echo number_format($rowdetail['tpitem_qty_verifikasi_good'],2,'.',','); ?>" style='width:70px' class="form-control qtygood" autocomplete="off" required/>
                </td>
              <!-- <td><?php echo $rowdetail['tpitem_item_condition_approve']; ?></td> -->
              <!-- <td><select name="kondisi[]" id="kondidi_0" required>
                <option value="">Pilih Kondisi</option>
                    <option value="Bagus" <?php if($rowdetail['tpitem_item_condition_verifikasi']=="Bagus") echo "selected"; ?>>Bagus</option>
                    <option value="Tidak Bagus" <?php if($rowdetail['tpitem_item_condition_verifikasi']=="Tidak Bagus") echo "selected"; ?>>Tidak Bagus</option>
                </select></td> -->
                <td>
                    <input type='text' id='qtynotgood_<?php echo $no; ?>' name='qtyverifikasi_not_good[]' value="<?php echo number_format($rowdetail['tpitem_qty_verifikasi_not_good'],2,'.',','); ?>" style='width:70px' class="form-control qtynotgood" autocomplete="off" required/>
                </td>
                <td>
                <input type='text' id='qtyverifikasi_<?php echo $no; ?>' name='qtyverifikasi[]' style='width:70px' value="<?php echo number_format($selisi,2,'.',','); ?>" class="qtyverifikasi" autocomplete="off" readonly required/>
                </td>
                <td><?php echo $rowdetail['expired']; ?></td>
              <td align="left"><?php echo $rowdetail['tpitem_remarks_approve']; ?></td>
              <td align="left"><textarea type='text' class="form-control keterangan_barang"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110" id='keteranganbarang_0' name='keterangan_barangverifikasi[]' cols="30" rows="5"><?php echo $rowdetail['tpitem_remarks_verifikasi']; ?></textarea></td>
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
                <label for="inputEmail" class="col-sm-12 col-form-label">Keterangan Status Permintaan</label>
                <div class="col-sm-12">
               <textarea name="note_request_verifkasi" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110" class="form-control note_request_verifkasi"  cols="10" rows="5"><?php echo $rowheader['reqtp_user_verifikasi_note']; ?></textarea>
                </div>
            </div>

            <select name="status_request" id="status_request" class="form-control" required>
        <option value="Verifikasi" selected>Disetujui</option>
        <option value="Reject" <?php if($rowheader['reqtp_user_verifikasi']=="Reject") echo "selected"; ?>>Ditolak</option>
        </select>
        <br>
<button type="submit" class="btn btn-primary" ><b>Proses Permintaan</b></button>  
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

  $('.qtygood, .qtynotgood, .qtyverifikasi').inputmask({
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

$("body").on("focus", "#date_verifikasi", function() {
    $(this).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd",
        // maxDate: 0,
        onSelect: function(selectedDate) {}
      });
});


// $("body").on("focus", ".datepickers", function() {
//     $(this).datepicker({
//         showOtherMonths: true,
//         selectOtherMonths: true,
//         dateFormat: "yy-mm-dd",
//         // minDate: 0,
//         onSelect: function(selectedDate) {}
//       });
// });

$(document).on('keyup keydown change','.qtygood',function(){

       id_arr = $(this).attr('id');
       id = id_arr.split("_");
      
       var qtyapprove= $('#qtyapprove_'+id[1]).val();
       var qtygood = $('#qtygood_'+id[1]).val();
       var qtynotgood = $('#qtynotgood_'+id[1]).val();
       var totalqty = Number(qtygood) + Number(qtynotgood);
       var subtotalqty =  parseFloat(totalqty) - parseFloat(qtyapprove);
       
$('#qtyverifikasi_'+id[1]).val(parseFloat(subtotalqty.toFixed(2)));
    if(subtotalqty > 0){
        // $('#qtynotgood_'+id[1]).val(0);
        // $('#qtygood_'+id[1]).val(0);
        // $('#qtyverifikasi_'+id[1]).val(0);
         $('#qtyverifikasi_'+id[1]).css({'background-color' : '#F61656'});
         $('#qtyverifikasi_'+id[1]).show(); 
    }else if(subtotalqty < 0){
         $('#qtyverifikasi_'+id[1]).css({'background-color' : '#F61656'}); 
         $('#qtyverifikasi_'+id[1]).show();
    }else{
         $('#qtyverifikasi_'+id[1]).css({'background-color' : '#FFFFFF'}); 
         $('#qtyverifikasi_'+id[1]).hide();
    }

});

$(document).on('keyup keydown change','.qtynotgood',function(){

id_arr = $(this).attr('id');
id = id_arr.split("_");

var qtyapprove= $('#qtyapprove_'+id[1]).val();
var qtygood = $('#qtygood_'+id[1]).val();
var qtynotgood = $('#qtynotgood_'+id[1]).val();
var totalqty = Number(qtygood) + Number(qtynotgood);
var subtotalqty =  parseFloat(totalqty) - parseFloat(qtyapprove);

$('#qtyverifikasi_'+id[1]).val(parseFloat(subtotalqty).toFixed(2));
if(subtotalqty > 0){
    // $('#qtynotgood_'+id[1]).val(0);
    // $('#qtygood_'+id[1]).val(0);
    // $('#qtyverifikasi_'+id[1]).val(0);
    $('#qtyverifikasi_'+id[1]).css({'background-color' : '#F61656'}); 
    $('#qtyverifikasi_'+id[1]).show();
}else if(subtotalqty < 0){
    $('#qtyverifikasi_'+id[1]).css({'background-color' : '#F61656'}); 
    $('#qtyverifikasi_'+id[1]).show();
}else{
    $('#qtyverifikasi_'+id[1]).css({'background-color' : '#FFFFFF'}); 
    $('#qtyverifikasi_'+id[1]).hide();
}

});

$('#sender_container').submit(function(e){

    e.preventDefault();   
    var valid=true;
    var dateverifikasi = $('#date_verifikasi').val();
    var status_verifikasi = $('#status_request :selected').val();

    if(dateverifikasi ==""){
        Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Wajib Pilih Tanggal Verifikasi!'
        });
        $('#date_verifikasi').focus();
        valid = false;
     }else{


   if(status_verifikasi =="Verifikasi"){
      $(this).find('.qtyverifikasi').each(function(){
             if($(this).val() == "" ){
         Swal.fire({
             icon: 'error',
             title: 'Peringatan!',
             text: 'Tolong di cek kembali qty verifikasi!',
              onAfterClose: () => {
                 setTimeout(() => $(this).focus(), 110);
             }
             });
         valid = false;
     }else if($(this).val() > 0 ){
                Swal.fire({
                    icon: 'error',
                    title: 'Peringatan!',
                    text: 'Qty Verifikasi Lebih Banyak Dari Qty Approve!',
                     onAfterClose: () => {
                        setTimeout(() => $(this).focus(), 110);
                    }
                    });
                valid = false;
            }else if($(this).val() < 0){
                Swal.fire({
                    icon: 'error',
                    title: 'Peringatan!',
                    text: 'Qty Verifikasi Harus Sesuai Dengan Qty Approve!',
                     onAfterClose: () => {
                        setTimeout(() => $(this).focus(), 110);
                    }
                    });
                valid = false;
            }
    }); 
    }
} 
    if (valid){
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
                            url: "viewverifikasirequestproses.php",
                            type: "POST",
                            data: $('#sender_container').serialize(), //serialize() untuk mengambil semua data di dalam form
                            dataType: "html",
                            success: function (response) {
                    swal.fire("Berhasil!", response, "success");
                                    // refresh page after 2 seconds
                                    setTimeout(function(){
                                        location.reload();
                                    },5000);
                                    location.href='listrequest.php';
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


                }
        });


$(document).on('change', '#status_request', function() {
  // Does some stuff and logs the event to the console
  var status_request = $('#status_request :selected').val();

  if(status_request =='Approved'){
    $(".qtygood").prop('required',true);
     $(".qtynotgood").prop('required',true);
  }else if (status_request =='Reject') {
    $(".qtygood").prop('required',false);
    $(".qtynotgood").prop('required',false);
     $(".note_request_verifkasi").prop('required',true);
  }else{
      $(".qtygood").prop('required',true);
     $(".qtynotgood").prop('required',true);
  }

});



</script>
</body>
</html>       