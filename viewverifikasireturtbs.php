<?php
include "layouts/header.php";
include "layouts/navbar.php";
$id=$_GET['id'];
$sqlheader = "SELECT *
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
) sub WHERE id_tb='$id'";

$stmtheader = sqlsrv_query( $conn, $sqlheader );
if( $stmtheader === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowheader = sqlsrv_fetch_array( $stmtheader, SQLSRV_FETCH_ASSOC);

?>

<style>.error {
    border: 1px solid red;
}</style>

<div class="container1">
 <div class="row">

<div class="col-sm-12" style="margin-top: 26px;" >
<span style="font-size:18px;"><b>* View Verifikasi Pengembalian Transfer Balik Store <?php echo $rowheader['reqtb_code']; ?></b></span>
<br><br><br>

<form method="POST" action="viewverifikasirequesttbsproses.php" id="sender_container">
        <fieldset >
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                <div class="col-sm-3">
                <input type="hidden" class="form-control" id="inputEmail" name="reqtb_code"  value="<?php echo $rowheader['reqtb_code']; ?>" readonly>
                <input type="hidden" class="form-control" id="inputEmail" name="id_tb"  value="<?php echo $rowheader['id_tb']; ?>" readonly>
                 <input type="hidden" class="form-control" id="inputEmail" name="reqtb_req" value="<?php echo $rowheader['req_date']; ?>" readonly>
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['req_date']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Jenis Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control"  id="inputEmail"  value="<?php echo $rowheader['req_type_name']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Toko Asal</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail" name="reqtb_user" value="<?php echo $rowheader['reqtb_user']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Toko Tujuan</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail" name="reqtb_destination" value="<?php echo $rowheader['reqtb_destination']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan Permintaan</label>
                <div class="col-sm-3">
               <textarea name="note_request" class="form-control" name="keterangan" cols="10" rows="5" readonly><?php echo $rowheader['reqtb_note']; ?>
               </textarea>
                </div>
            </div>
<!--             <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Status Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" name="status_header" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqtb_destination_retur_verifikasi']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row" style="display: none">
            <input type="text" id="subTotalpeminjaman" name="subTotalpeminjaman" value="<?php echo number_format($rowheader['selisi'],2,'.',','); ?>" readonly >
              <input type="text" id="subTotalpengembalian" class="subTotalpengembalian" name="subTotalpengembalian" readonly >
                <input type="text" id="totalselisi" class="totalselisi" name="totalselisi" readonly>
            <input type="text" id="subkelebihan" name="subTotalkelebihan" value="<?php echo number_format($rowheader['kelebihan'],2,'.',','); ?>" readonly>
            <input type="text" id="totalkelebihan" name="totalkelebihan" readonly>
            </div>
        </fieldset>

<br><br>

<div style="overflow-x:auto;">
   <table  class="table table-striped table-bordered dt-responsive nowrap">

                <tr>
                 <th  rowspan="2">No</th>
                   <th  rowspan="2">Kode Barang</th>
                   <th  rowspan="2">Nama Barang</th>
                    <th  rowspan="2">Satuan</th>
                    <th  rowspan="2">Jenis Barang</th>
                    <th  rowspan="2">Pengembalian Ke</th>
                    <th  rowspan="2">Jumlah Pengembalian</th>
                    <!-- <th>Verifikasi</th> -->
                    <!-- <th>Kondisi Barang</th> -->
                    <th  rowspan="2" class="text-center">Verifikasi Barang </th>
                    <th  rowspan="2">Selisih</th>
                    <th  rowspan="2">Kadaluarsa Barang</th>
                    <th  rowspan="2">Keterangan Barang</th>
                    <th rowspan="2">Verifikasi Keterangan Barang</th>
                </tr>
                <!-- <tr>
                    <th>Barang Sesuai</th>
                    <th>Barang Kelebihan</th>
                    </tr> -->

                </thead>
    <tbody>
<?php

        $sqldetail = "SELECT
        *, CONVERT ( CHAR ( 10 ), a.rtrtbitem_expired_retur, 126 ) expiredpeminjaman 
    FROM
        detail_returntb a inner join detail_tb b on a.header_detailid=b.id_detailtb
    WHERE
        header_idrtrtb = '$id' 
        AND ( rtrtbitem_qty_retur IS NOT NULL) 
        AND ( rtrtbitem_qty_retur_verifikasi IS NULL) 
    ORDER BY
        flag ASC";
        $stmtdetail = sqlsrv_query( $conn, $sqldetail );
        if( $stmtdetail === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $no=0;
        while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){
        $no++;
        $qtyver =$rowdetail['rtrtbitem_qty_retur_verifikasi'];
        if($qtyver == ''){
            $fixqtyver=0;
        }else{
            $fixqtyver=$qtyver;
        }
        if( $rowdetail['rtrtbflag_tp'] ==""){
            $doktp ='<select style="display:none;" name="tp['.$rowdetail['id_detail_returtb'].']"  id="tp_'.$no.'" class="form-control tp"><option value="0"></option></select>';
            $color='';
            $qty = $rowdetail['rtrtbitem_qty_retur_verifikasi'];
            $readonly =''; 
        $selisi =  $fixqtyver - $rowdetail['rtrtbitem_qty_retur'];
        $plus =  $fixqtyver - $rowdetail['rtrtbitem_qty_retur'];
        }else{
            $doktp= '<p style="font-size:14px;"><b>Yakin dilakukan Transfer Putus?</p><select name="tp['.$rowdetail['id_detail_returtb'].']"  id="tp_'.$no.'" class="form-control tp" required><option value="">--Pilih Status--</option><option value="1">Ya</option><option value="2">Tidak</option></select>';
            $color="style='background-color:#FFC300;'";
            $qty = $rowdetail['rtrtbitem_qty_retur'];
            $readonly ='readonly';
             $selisi = 0;
            $plus =  0;
        }
    ?>
    <tr>
              <td scope="row"><?php echo $no; ?>
              <input type="hidden" name="id_barang[<?php echo $rowdetail['id_detail_returtb']; ?>]" value="<?php echo $rowdetail['tbitem_id']; ?>">
              <input type="hidden" name="id_detail_returtb[<?php echo $rowdetail['id_detail_returtb']; ?>]" value="<?php echo $rowdetail['id_detail_returtb']; ?>">
            </td>
              <td class="text-muted">
              <input type="hidden" name="item_code[<?php echo $rowdetail['id_detail_returtb']; ?>]" value="<?php echo $rowdetail['tbitem_code']; ?>">
                  <?php echo $rowdetail['tbitem_code']; ?>
                </td>
              <td align="left">
              <input type="hidden" name="item_name[<?php echo $rowdetail['id_detail_returtb']; ?>]" value="<?php echo $rowdetail['tbitem_name']; ?>">
                  <?php echo $rowdetail['tbitem_name']; ?>
                </td>
              <td align="left">
              <input type="hidden" name="item_uom[<?php echo $rowdetail['id_detail_returtb']; ?>]" value="<?php echo $rowdetail['tbitem_uom']; ?>">
                  <?php echo $rowdetail['tbitem_uom']; ?>
                </td>
              <td align="left">
              <input type="hidden" name="item_cat[<?php echo $rowdetail['id_detail_returtb']; ?>]" value="<?php echo $rowdetail['tbitem_cat']; ?>">
              <input type="hidden" name="tbitem_reason[<?php echo $rowdetail['id_detail_returtb']; ?>]" value="<?php echo $rowdetail['tbitem_reason']; ?>">
                  <?php echo $rowdetail['tbitem_cat']; ?>
            </td> 
              <td align="left"><?php echo $rowdetail['flag']; ?></td>
              <td align="left" <?php echo $color; ?>><p id="retur_<?php echo $no; ?>"><?php echo number_format($rowdetail['rtrtbitem_qty_retur'],2,'.',','); ?></p><br><br>
              <?php echo $doktp; ?>
              <input type='hidden' id='qtyapprove_<?php echo $no; ?>'  name='qtyapprove[<?php echo $rowdetail['id_detail_returtb']; ?>]' value="<?php echo number_format($rowdetail['rtrtbitem_qty_retur'],2,'.',','); ?>" style='width:70px' class="form-control" autocomplete="off" readonly required/>
            </td> 
              <td align="left">
                  <input type='text' id='qtygood_<?php echo $no; ?>' value="<?php echo number_format($qty,2,'.',','); ?>" name="qtygood[<?php echo $rowdetail['id_detail_returtb']; ?>]" style='width:70px' class="qtygood" autocomplete="off" <?php echo $readonly; ?> required/>
                  <input type='hidden' id='qtyverifikasiplus_<?php echo $no; ?>' value="0" name='qtyverifikasiplus[<?php echo $rowdetail['id_detail_returtb']; ?>]' style='width:70px' class="form-control qtyverifikasiplus" autocomplete="off" required/>
                </td>
                <!-- <td>
                <input type='number' id='qtyverifikasiplus_<?php echo $no; ?>' min="0" value="0" name='qtyverifikasiplus[<?php echo $rowdetail['id_detail_returtb']; ?>]' style='width:70px' class="form-control qtyverifikasiplus" autocomplete="off" required/>
                </td> -->
                <td>
                <input type='text' id='qtyverifikasi_<?php echo $no; ?>'  name='qtyverifikasi[<?php echo $rowdetail['id_detail_returtb']; ?>]' style='width:70px' value="<?php echo number_format($selisi,2,'.',','); ?>" class="qtyverifikasi" autocomplete="off" readonly required/>
                </td>
                <td><?php echo $rowdetail['expiredpeminjaman']; ?></td>
              <td align="left"><?php echo $rowdetail['rtrtbitem_remarks_retur']; ?></td>
              <td align="left"><textarea type='text' class="form-control keterangan_barang"  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110" id='keteranganbarang_0' name='keterangan_barangverifikasi[<?php echo $rowdetail['id_detail_returtb']; ?>]' cols="30" rows="5"></textarea></td>
            </tr>

    <?php
    }
    ?>

    </tbody>

</table> 

</div>

        
        <div align="right">    
 

        <div class="col-sm-3"> 

        <div class="form-group row">
                <label for="inputEmail" class="col-sm-12 col-form-label">Keterangan Status Permintaan</label>
                <div class="col-sm-12">
               <textarea name="note_request_verifikasi" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110" class="form-control"  cols="10" rows="5"></textarea>
                </div>
            </div>

            <select name="status_request" id="status_request" class="form-control" required style="display: none;">
        <option value="Verifikasi" selected>Disetujui</option>
        <!-- <option value="Reject" <?php if($rowheader['reqtb_user_retur_verifikasi']=="Reject") echo "selected"; ?>>Ditolak</option> -->
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

    $('.qtygood, .qtyverifikasi, .subTotalpengembalian, .totalselisi').inputmask({
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

$(document).on('keyup keydown change','.tp',function(){

    id_arr = $(this).attr('id');
    id = id_arr.split("_");
    var tp = $('#tp_'+id[1]).val();
    var retur =  $('#retur_'+id[1]).html();
    // alert(retur);

    if(tp==1){
       var qtygood = $('#qtygood_'+id[1]).val(retur);
    }else{
        var qtygood = $('#qtygood_'+id[1]).val(0);
    }


});

$(document).on('keyup keydown change','.qtygood, .tp',function(){

       id_arr = $(this).attr('id');
       id = id_arr.split("_");

       var qtyapprove= $('#qtyapprove_'+id[1]).val();
       var qtygood = $('#qtygood_'+id[1]).val();
       var totalqty = Number(qtygood);
       var subtotalqty =  Number(totalqty) - Number(qtyapprove);
    

       $('#qtyverifikasi_'+id[1]).val(parseFloat(subtotalqty.toFixed(2)));


    if(subtotalqty > 0){
         $('#qtyverifikasi_'+id[1]).css({'background-color' : '#F61656'}); 
            $('#qtyverifikasiplus_'+id[1]).css({'background-color' : '#F61656'}); 
            $('#qtyverifikasiminus_'+id[1]).css({'background-color' : '#FFFFFF'}); 
           $('#qtyverifikasiplus_'+id[1]).val(parseFloat(subtotalqty.toFixed(2))); 
            $('#qtyverifikasiminus_'+id[1]).val(0);
            calculateTotalKekurangan(); 
            $('#qtyverifikasi_'+id[1]).show(); 
    }else if(subtotalqty < 0){
         $('#qtyverifikasi_'+id[1]).css({'background-color' : '#F61656'}); 
           $('#qtyverifikasiplus_'+id[1]).val(0); 
             $('#qtyverifikasiminus_'+id[1]).css({'background-color' : '#F61656'}); 
             $('#qtyverifikasiplus_'+id[1]).css({'background-color' : '#FFFFFF'}); 
            $('#qtyverifikasiminus_'+id[1]).val(parseFloat(subtotalqty.toFixed(2))); 
            calculateTotalKekurangan(); 
            $('#qtyverifikasi_'+id[1]).show(); 
    }else{
         $('#qtyverifikasi_'+id[1]).css({'background-color' : '#FFFFFF'}); 
          $('#qtyverifikasiplus_'+id[1]).css({'background-color' : '#FFFFFF'}); 
           $('#qtyverifikasiminus_'+id[1]).css({'background-color' : '#FFFFFF'}); 
         $('#qtyverifikasiplus_'+id[1]).val(0); 
         $('#qtyverifikasiminus_'+id[1]).val(0); 
         calculateTotalKekurangan(); 
         $('#qtyverifikasi_'+id[1]).hide(); 
    }
  

});


    //total price calculation plus
    function calculateTotalKekurangan(){
        total = 0 ;
        selisi=0;
        var peminjaman = $('#subTotalpeminjaman').val(); 
        var fixpeminjaman = isNaN(Number(peminjaman)) ? 0 : Number(peminjaman);    
        $('.qtygood').each(function(){
          if($(this).val() != '' )
          total += parseFloat( $(this).val());
        });
        $('#subTotalpengembalian').val( parseFloat(total.toFixed(2)) );
        var totalselisi = Number(peminjaman) + Number(total);
        $('#totalselisi').val(parseFloat(totalselisi.toFixed(2)));
      }



// $(document).on('keyup keydown change','.qtyverifikasiplus',function(){

// id_arr = $(this).attr('id');
// id = id_arr.split("_");

// var qtyapprove= $('#qtyapprove_'+id[1]).val();
// var qtygood = $('#qtygood_'+id[1]).val(); 
// var qtyverifikasiplus = $('#qtyverifikasiplus_'+id[1]).val();
// var totalqty = parseInt(qtygood);
// var subtotalqty =  parseInt(totalqty) - parseInt(qtyapprove) +  parseInt(qtyverifikasiplus);




// if(subtotalqty > 0){
//   $('#qtyverifikasi_'+id[1]).css({'background-color' : '#F61656'}); 
//      $('#qtyverifikasiplus_'+id[1]).css({'background-color' : '#F61656'}); 
//      calculateTotalKelebihan()

// }else if(subtotalqty < 0){
//   $('#qtyverifikasi_'+id[1]).css({'background-color' : '#F61656'}); 

//      calculateTotalKelebihan()
// }else{
//   $('#qtyverifikasi_'+id[1]).css({'background-color' : '#FFFFFF'}); 
//    $('#qtyverifikasiplus_'+id[1]).css({'background-color' : '#FFFFFF'}); 
//     $('#qtyverifikasiminus_'+id[1]).css({'background-color' : '#FFFFFF'}); 

//     calculateTotalKelebihan()
// }


// });


//           //total price calculation plus
//           function calculateTotalKelebihan(){
//          total = 0 ;
//         $('.qtyverifikasiplus').each(function(){
//           if($(this).val() != '' )
//           total += parseFloat( $(this).val());
//         });
//         // $('.subTotalminus').val(0);
//         $('#totalkelebihan').val(total);
//       }




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
     }else if(status_verifikasi ==""){
        Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Wajib Pilih Status Verifikasi!'
        });
        $('#status_request').focus();
        valid = false;
     } 
else{
    //   $(this).find('.qtyverifikasi').each(function(){
    //          if($(this).val() == "" ){
    //      Swal.fire({
    //          icon: 'error',
    //          title: 'Peringatan!',
    //          text: 'Tolong di cek kembali qty verifikasi!',
    //           onAfterClose: () => {
    //              setTimeout(() => $(this).focus(), 110);
    //          }
    //          });
    //      valid = false;
    //  }else if($(this).val() > 0 ){
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: 'Peringatan!',
    //                 text: 'Qty Verifikasi Lebih Banyak Dari Qty Approve!',
    //                  onAfterClose: () => {
    //                     setTimeout(() => $(this).focus(), 110);
    //                 }
    //                 });
    //             valid = false;
    //         }else if($(this).val() < 0){
    //             Swal.fire({
    //                 icon: 'error',
    //                 title: 'Peringatan!',
    //                 text: 'Qty Verifikasi Harus Sesuai Dengan Qty Approve!',
    //                  onAfterClose: () => {
    //                     setTimeout(() => $(this).focus(), 110);
    //                 }
    //                 });
    //             valid = false;
    //         }
    // }); 
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
                            url: "viewverifikasireturtbsproses.php",
                            type: "POST",
                            data: $('#sender_container').serialize(), //serialize() untuk mengambil semua data di dalam form
                            dataType: "html",
                            success: function (response) {
                    swal.fire("Berhasil!", response, "success");
                                    // refresh page after 2 seconds
                                    setTimeout(function(){
                                        location.reload();
                                    },5000);
                                  location.href='listapproverequesttbs.php';
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



$(document).on('keyup keydown change','.qtyverfikasirevisireturminus',function(){

id_arr = $(this).attr('id');
id = id_arr.split("_");

var qtyverkekurangann= $('#qtyverfikasirevisireturminus_'+id[1]).val();
var qtyterima = $('#qtygood_'+id[1]).val(); 
var qtypengembalian = $('#qtyapprove_'+id[1]).val(); 
var total = (Number(qtyverkekurangann) +  Number(qtyterima));
var selisi = Number(total) - Number(qtypengembalian);
console.log(selisi);

$('.subTotalminus').val(parseFloat(selisi.toFixed(2)));


});


</script>
</body>
</html>       