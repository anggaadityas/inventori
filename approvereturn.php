<?php
include "layouts/header.php";
error_reporting(0);
$id=$_GET['id'];
$sqlheader = "SELECT *,convert(char(10),reqrtn_date,126) req_date,convert(char(10),reqrtn_nodoc_sap_date,126) date_posting,convert(char(10),reqrtn_destination_approve_date,126) date_verifikasi,convert(char(20),a.created_date,120) date_submit FROM header_returnck a
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
<span style="font-size:18px;"><b>* View Approve Permintaan Retur Barang Store <?php echo $rowheader['reqrtn_code']; ?></b></span>
<br><br><br>

<form method="POST" action="approvereturnproses.php"  id="sender_container">
        <fieldset >
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                <div class="col-sm-3">
                <input type="hidden" class="form-control" id="inputEmail" name="reqrtn_code"  value="<?php echo $rowheader['reqrtn_code']; ?>" readonly>
                <input type="hidden" class="form-control" id="inputEmail" name="id_rtn"  value="<?php echo $rowheader['id_rtn']; ?>" readonly>
                    <input type="text" class="form-control" name="reqrtn_code_date" id="reqrtn_code_date" value="<?php echo $rowheader['req_date']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row" style="margin-top: 10px;">
                <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                    <div class="form-check"><input type="checkbox" name="rev_question" id="rev_question" value="0" class="form-check-input rev_question"> 
              <span style="font-size: 10px;"><b>Ingin Melakukan Perubahan Tanggal Pengiriman?</b></span>
              </div>
              </div>

                <div class="form-group row revisi" style="margin-top: 10px;">
                <label for="inputEmail3" class="col-sm-2 col-form-label">Perubahan Tanggal Pengiriman</label>
                <div class="col-sm-3">
              <input type="text" name="rev_date_req" id="rev_date_req" class="form-control rev_date_req" autocomplete="off">
              <span style="font-size: 10px;" class="pastdatedel"></span>
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
                    <input type="text" class="form-control"  id="inputEmail"  value="<?php echo $rowheader['req_type_name']; ?>" readonly>
                </div>
            </div>
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
                <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Tipe</label>
                <div class="col-sm-2">
                <select class="form-control" name="reqrtn_type_req" readonly>
                    <option value="1" <?php echo ($rowheader['reqrtn_type_req'] ==  '1') ? ' selected="selected"' : '';?>>Sistem</option>
                    <option value="2" <?php echo ($rowheader['reqrtn_type_req'] ==  '2') ? ' selected="selected"' : '';?>>Non Sistem</option>
                    <option value="3" <?php echo ($rowheader['reqrtn_type_req'] ==  '3') ? ' selected="selected"' : '';?>>Wadah</option>
                    <option value="4" <?php echo ($rowheader['reqrtn_type_req'] ==  '4') ? ' selected="selected"' : '';?>>NCR</option>
                    <option value="5" <?php echo ($rowheader['reqrtn_type_req'] ==  '5') ? ' selected="selected"' : '';?>>Damage</option>
                </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan Permintaan</label>
                <div class="col-sm-3">
               <textarea name="note_request" class="form-control" name="keterangan" cols="10" rows="5" readonly><?php echo $rowheader['reqrtn_note']; ?>
               </textarea>
                </div>
            </div>
                          <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Dokumen Upload</label>
                <div class="col-sm-3">

            <?php

            $sqldokumen = "SELECT * FROM retur_dokumen where reqrtn_id='$id'";

            $stmtdokumen = sqlsrv_query( $conn, $sqldokumen );
            if( $stmtdokumen === false) {
                die( print_r( sqlsrv_errors(), true) );
            }
            $no=0;
         while ($dokumen = sqlsrv_fetch_array( $stmtdokumen, SQLSRV_FETCH_ASSOC)) {
            
            echo '<a href="dokumen/'.$dokumen["nama_dokumen"].'" target="_blank"><img id="myImg" class="myImg" style="width:100%;max-width:300px"  src="dokumen/'.$dokumen["nama_dokumen"].'"></a>
            ';   

            $no++; 

      }

      ?>

       </div>
            </div>
        </fieldset>

<br>


<?php

$sqldetail = "SELECT *,convert(char(10),rtnitem_expired,126) expired FROM detail_returnck a inner join mst_item b on a.rtnitem_id=b.id_mst_item where header_idrtn='$id'";
$stmtdetail = sqlsrv_query( $conn, $sqldetail );
if( $stmtdetail === false) {
    die( print_r( sqlsrv_errors(), true) );
}

$cats = array();
while($row =  sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)) {
    $cats[$row['rtnitem_cat']][] = $row;
}
?>

<div style="overflow-x:auto;">


        <?php 
        $no=1;
        foreach($cats as $author_id => $values):
            $katsu=array();
            $ketsu = array_unique($values);
          ?>
          <br>
            <tr>
            <td class = "row_group">  <p style="font-size:20px;"><b><?php echo $no; ?>. <?php echo $author_id; ?></b></p></td>
                <td>
                <td style="font-size:15px;">
                <input type="hidden" name="cat[<?php echo $no; ?>]" value="<?php echo $author_id; ?>" readonly>
                <?php foreach($ketsu as $author_book) {

                    if(is_null($author_book['rtnitem_status_approve'])){
                        $approve ='';
                        $notapprove ='';
                    }else{

                        if($author_book['rtnitem_status_approve']==0){
                            $approve ='selected';
                        }else{
                            $approve ='';
                        }
                        if($author_book['rtnitem_status_approve']==1){
                            $notapprove ='selected';
                        }else{
                            $notapprove ='';
                        }
    

                    }
                    

	                	echo '<select name="status['.$no.']"  class="status" id="status_'.$no.'" required>
                        <option value="">--Pilih Status--</option>
                         <option value="0" '.$approve.'>Approve</option>
                         <option value="1" '.$notapprove.'>Not Approve</option>
                        </select>';
	                	}
                	?> 
                
                <br>  <br>
                <?php foreach($ketsu as $author_book) {
	                	echo '<textarea name="keterangan['.$no.']" class="keterangan" id="keterangan_'.$no.'" cols="30" rows="5" >'.$author_book['rtnitem_remarks_approve'] . '</textarea>';
	                	}
                	?> 
                
              </td>
        </tr>
        <table style="width: 100%;">
              <thead>
          <th rowspan='2'>Kode Barang</th>
          <th rowspan='2'>Nama Barang</th>
            <th rowspan='2'>Satuan</th>
            <th rowspan='2'>Alasan</th>
            <th colspan='2' class='text-center'>Jumlah Barang</th>
            <th rowspan='2'>Kadaluarsa</th>
            <th rowspan='2'>Keterangan Toko</th>
            <tr>
            <th>Bagus</th>
            <th>Tidak Bagus</th>
            </tr>
              </thead>
              <tbody>
          <tr>
            <td>
                	<?php foreach($values as $author_book) {
	                	echo '<b>'.$author_book['rtnitem_code'] . '<b><br/><hr>';
	                	}
                	?>              	
                </td>
                <td class="right">
                	<?php foreach($values as $author_book) {
	                	echo $author_book['rtnitem_name']. '<br/><hr>';
		                }
	                ?>           	
                </td>
                 <td class="right">
                 	<?php foreach($values as $author_book) {
                     	echo $author_book['rtnitem_uom']. '<br/><hr>';
	                 	} 
                 	?> 	
                 </td>
                 <td class="right">
                 	<?php foreach($values as $author_book) {
                     	echo $author_book['rtnitem_reason']. '<br/><hr>';
	                 	} 
                 	?> 	
                 </td>
                 <td class="right">
                 	<?php foreach($values as $author_book) {
                     	echo number_format($author_book['rtnitem_qty_good'],2,'.',','). '<br/><hr>';
	                 	} 
                 	?> 	
                 </td>
                 <td class="right">
                 	<?php foreach($values as $author_book) {
                     	echo number_format($author_book['rtnitem_qty_not_good'],2,'.',','). '<br/><hr>';
	                 	} 
                 	?> 	
                 </td>
                 <td class="right">
                 	<?php foreach($values as $author_book) {
                     	echo $author_book['expired']. '<br/><hr>';
	                 	} 
                 	?> 	
                 </td>
                 
                 <td class="right">
                 	<?php foreach($values as $author_book) {
                     	echo $author_book['rtnitem_remarks']. '<br/><hr>';
	                 	} 
                 	?> 	
                 </td>
            </tr>
            </tbody>
</table>
            <?php 
            $no++;
      endforeach;
      ?>
</div>       

<br>
        <div align="right">    
 

        <div class="col-sm-3">

        <div class="form-group row">
                <label for="inputEmail" class="col-sm-12 col-form-label">Keterangan Status Verifikasi</label>
                <div class="col-sm-12">
               <textarea name="note_request_verifkasi" class="form-control" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110"  cols="10" rows="5"><?php echo $rowheader['reqrtn_ck_approve_note']; ?></textarea>
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
<script>

$(".revisi").hide();
$(".rev_question").click(function(){
    if($(this).is(":checked")){
        var bookdelivery = $('#reqrtn_code_date').val();
         $(".revisi").show();
         $(".rev_question").val(1);
         $(".rev_date_req").val(bookdelivery);
         $(".pastdatedel").html('<b>Tanggal Pengiriman Sebelumnya : '+bookdelivery+'</b>');
    }else{
        $(".revisi").hide();
        $(".rev_question").val(0);
        $(".rev_date_req").val('');
        $(".pastdatedel").html('')
    }
})

$(function() {
    $("body").delegate("#rev_date_req", "focusin", function(){
        var today = new Date();
        $(this).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,    
        dateFormat: "yy-mm-dd",
        minDate: today,
        onSelect: function(selectedDate) {}
      });
    });
});

$("body").on("keydown", ".keterangan_barang", function() {
   var x = event.which;
   if (x === 13) {
       event.preventDefault();
   } 
});



$(document).on('keyup keydown change','.status',function(){

id_arr = $(this).attr('id');
id = id_arr.split("_");

var status = $('#status_'+id[1]).val();
var keterangan = $('#keterangan_'+id[1]).val();

if(status==0){
  $('#keterangan_'+id[1]).attr('required', false); 
}else if(status==1){
  $('#keterangan_'+id[1]).attr('required', true); 
}

});


$('#sender_container').submit(function(e){

e.preventDefault();   
var valid=true;  
var reqrtn_code_date = $('#reqrtn_code_date').val();
var rev_date_req = $("#rev_date_req").val(); 
 if(rev_date_req == reqrtn_code_date ){
        Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Perubahan Tanggal sama dengan tanggal sebelumnya, tidak ada perubahan!'
        });
        $("#status_proses").focus();
        valid = false;
 }else{
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
                     url: "approvereturnproses.php",
                     type: "POST",
                     data: $('#sender_container').serialize(), //serialize() untuk mengambil semua data di dalam form
                     dataType: "html",
                     success: function (response) {
             swal.fire("Berhasil!", response, "success");
                            //  refresh page after 2 seconds
                             setTimeout(function(){
                                 location.reload();
                             },5000);
                            location.href='listapprovereturn.php';
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
  }
 });




</script>
</body>
</html>       