<?php
include "layouts/header.php";
$id=$_GET['id'];
$sqlheader = "SELECT *,convert(char(10),reqtb_date,126) req_date,convert(char(10),reqtb_destination_approve_date,126) date_approve FROM header_tb a
 left join mst_req_type b on a.reqtb_type=b.id_mst_type
 left join mst_req_type_item  c on a.reqtb_item_type=c.id_mst_type_item
 where id_tb='$id'";
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
<span style="font-size:18px;"><b>* View Persetujuan Permintaan Transfer Balik Store <?php echo $rowheader['reqtb_code']; ?></b></span>
<br><br><br>

<form method="POST" action="viewapproverequesttbsproses.php">
        <fieldset >
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                <div class="col-sm-3">
                <input type="hidden" class="form-control" id="inputEmail" name="reqtb_code"  value="<?php echo $rowheader['reqtb_code']; ?>" readonly>
                <input type="hidden" class="form-control" id="inputEmail" name="id_tb"  value="<?php echo $rowheader['id_tb']; ?>" readonly>
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['req_date']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Jenis Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control"  id="inputEmail"  value="<?php echo $rowheader['req_type_name']; ?>" readonly>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label" >Jenis Barang</label>
                <div class="col-sm-3">
                <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['req_type_name_item']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Toko Penerima</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail" name="reqtb_user" value="<?php echo $rowheader['reqtb_user']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Toko Pengirim</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail" name="reqtb_destination" value="<?php echo $rowheader['reqtb_destination']; ?>" readonly>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Alasan Permintaan</label>
                <div class="col-sm-2">
                <input type="text" name="alasan" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqtb_reason']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan Permintaan</label>
                <div class="col-sm-3">
               <textarea name="note_request" class="form-control" name="keterangan" cols="10" rows="5" readonly><?php echo $rowheader['reqtb_note']; ?>
               </textarea>
                </div>
            </div>
            
            <!-- <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Kirim Barang</label>
                <div class="col-sm-3">
                   <input type="text" class="form-control date_approve" name="date_approve" id="date_approve" value="<?php echo $rowheader['date_approve']; ?>" placeholder="Pilih Tanggal Kirim Barang" autocomplete="off" required>
                </div>
            </div> -->

            <!-- <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Kode SAP</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqtb_nodoc_sap']; ?>" readonly>
                </div>
            </div> -->
            <!-- <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Status Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqtb_destination_approve']; ?>" readonly>
                </div>
            </div> -->
        </fieldset>

<br><br>


   <table  class="table table-striped table-bordered dt-responsive nowrap">

                <tr>
                 <th>Nomor</th>
                   <th>Kode Barang</th>
                   <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Jenis Barang</th>
                    <th>Alasan</th>
                    <th>Jumlah</th>
                    <th>Jumlah Disetujui</th>
                    <!-- <th>Kondisi Barang</th> -->
                    <th>Tanggal Kadaluarsa</th>
                    <!-- <th>Keterangan Barang</th> -->
                    <th>Keterangan Barang Disetujui</th>
                </tr>
   

                </thead>
    <tbody>
<?php

        $sqldetail = "SELECT *,convert(char(10),tbitem_expired,126) expired FROM detail_tb a inner join mst_item b on a.tbitem_id=b.id_mst_item where header_idtb='$id'";
        $stmtdetail = sqlsrv_query( $conn, $sqldetail );
        if( $stmtdetail === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $no=0;
        while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){
        $no++;

        if($rowdetail['exp_flag']==1){
            $kadarluarsa="<input type='text' id='expireddate_".$no."' name='expired_date[".$rowdetail['id_detailtb']."]' value='".$rowdetail['expired']."' class='datepickers form-control' placeholder='YYYY-MM-DD' autocomplete='off' required>";
        }else{
            $kadarluarsa="";
        }
        

    ?>
    <tr>
              <td scope="row"><?php echo $no; ?><input type="hidden" name="id_barang[<?php echo $rowdetail['id_detailtb']; ?>]" value="<?php echo $rowdetail['tbitem_id']; ?>"></td>
              <td class="text-muted"><?php echo $rowdetail['tbitem_code']; ?></td>
              <td align="left"><?php echo $rowdetail['tbitem_name']; ?></td>
              <td align="left"><?php echo $rowdetail['tbitem_uom']; ?></td>
              <td align="left"><?php echo $rowdetail['tbitem_cat']; ?></td>
              <td align="left"><?php echo $rowdetail['tbitem_reason']; ?></td>
              <td align="left"><?php echo number_format($rowdetail['tbitem_qty'],2,'.',','); ?></td> 
              <td align="left"><input type='text' id='qty_0'  name='qtyverifikasi[<?php echo $rowdetail['id_detailtb']; ?>]' value="<?php echo $rowdetail['tbitem_qty_approve']; ?>" style='width:70px' class="form-control qty" autocomplete="off" required/></td>
              <!-- <td><select name="kondisi[]" id="kondidi_0" required>
                <option value="">Pilih Kondisi</option>
                    <option value="Bagus">Bagus</option>
                    <option value="Tidak Bagus">Tidak Bagus</option>
                </select></td> -->
                <td><?php echo  $kadarluarsa; ?></td>
              <!-- <td align="left"><?php echo $rowdetail['tbitem_remarks']; ?></td> -->
              <td align="left"><textarea type='text' class="form-control keterangan_barang" id='keteranganbarang_0'  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110"  name='keterangan_barangverifikasi[<?php echo $rowdetail['id_detailtb']; ?>]' cols="30" rows="5"><?php echo $rowdetail['tbitem_remarks_approve']; ?></textarea></td>
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
               <textarea name="note_request_approve" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110" class="form-control note_request_approve"  cols="10" rows="5"><?php echo $rowheader['reqtb_destination_approve_note']; ?></textarea>
                </div>
            </div>

       <select name="status_request" id="status_request" class="form-control" required >
        <option value="Approved" <?php if($rowheader['reqtb_destination_approve']=="Approved") echo "selected"; ?>>Di Setujui</option>
        <option value="Reject" <?php if($rowheader['reqtb_destination_approve']=="Reject") echo "selected"; ?>>Di Tolak</option>
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

     $('.qty').inputmask({
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
    
// $('form').on('focus', 'input[type=number]', function (e) {
//   $(this).on('wheel.disableScroll', function (e) {
//     e.preventDefault()
//   })
// })
// $('form').on('blur', 'input[type=number]', function (e) {
//   $(this).off('wheel.disableScroll')
// })

// $("body").on("focus", "input[type=number]", function() {
//     $(this).blur();
// });

$("body").on("keydown", ".keterangan_barang", function() {
   var x = event.which;
   if (x === 13) {
       event.preventDefault();
   } 
});

$("body").on("focus", ".datepickers", function() {
    $(this).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd",
        // minDate: 0,
        onSelect: function(selectedDate) {}
      });
});

$("body").on("focus", "#date_approve", function() {
    $(this).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd",
        // maxDate: 0,
        onSelect: function(selectedDate) {}
      });
});

$(document).on('change', '#status_request', function() {
  // Does some stuff and logs the event to the console
  var status_request = $('#status_request :selected').val();

  if(status_request =='Approved'){
    $(".qty").prop('required',true);
    $(".datepickers").prop('required',true);
  }else if (status_request =='Reject') {
    $(".qty").prop('required',false);
     $(".datepickers").prop('required',false);
     $(".note_request_approve").prop('required',true);
  }else{
    $(".qty").prop('required',true);
        $(".datepickers").prop('required',true);
  }

});

</script>
</body>
</html>       