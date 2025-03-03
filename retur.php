<?php
$halaman="tps";
include "layouts/header.php";
include "layouts/navbar.php";
?>

<div class="container1">
 <div class="row">

<div class="col-sm-12" style="margin-top: 26px;" >

<?php

// menampilkan pesan jika ada pesan
if (isset($_SESSION['pesan']) && $_SESSION['pesan'] <> '') {
    echo '<div class="alert alert-warning alert-dismissible fade show col-sm-5" role="alert">
  <strong>Info!</strong> '.$_SESSION['pesan'].'
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>';
}

  // mengatur session pesan menjadi kosong
  $_SESSION['pesan'] = '';

?>

<!-- Nav tabs -->
<ul class="nav nav-tabs">
  <!-- <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#menu1"><b>Formulir Permintaan Transfer Putus Store</b></a>
  </li> -->
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#menu2"><b>Formulir Permintaan Retur Barang</b></a>
  </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

  <div class="tab-pane container fade" id="menu1">

   
  </div>


  <div class="tab-pane container active" id="menu2">
<br><br>
  <form action="requestproses.php" method="POST" enctype="multipart/form-data">

              
  <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Jenis Permintaan</label>
                <div class="col-sm-3">
                   <select name="jenis_permintaan" id="jenis_permintaan1" class="form-control" readonly required>
                   <option value="2">Retur Barang</option>
                   </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Tipe Pengiriman</label>
                <div class="col-sm-3">
                   <select name="pickup_type" id="pickup_type" class="form-control" required>
                   <option value="">-- Pilih Tipe Pengiriman --</option>
                   <option value="1">CK</option>
                   <option value="2">Divisi Tujuan</option>
                   </select>
                </div>
                
            </div>

            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Dep Tujuan</label>
                <div class="col-sm-3">
                   <select name="divisi" id="divisi1" class="form-control" required>
                   <option value="">-- Loading --</option>
                   <!-- <?php if($_SESSION['area_ck']=='1'){
                   ?>
                   <option value="CK JAKARTA">CK JAKARTA</option>
                   <option value="ENG JAKARTA">ENG JAKARTA</option>
                   <option value="IT JAKARTA">IT JAKARTA</option>
                   <option value="GA JAKARTA">GA JAKARTA</option>
                   <option value="OPENING">OPENING</option>
                   <?php
                   }
                   else if($_SESSION['area_ck']=='2'){
                   ?>
                  <option value="CK SURABAYA">CK SURABAYA</option>
                   <option value="ENG SURABAYA">ENG SURABAYA</option>
                   <option value="IT SURABAYA">IT SURABAYA</option>
                   <option value="GA SURABAYA">GA SURABAYA</option>
                   <option value="OPENING">OPENING</option>
                   <?php
                   }else{}
                   ?>            -->
                   </select>
                   <span style="font-size: 10px;"><b>* Pilih tipe pengambilan untuk menampilkan divisi</b></span>
                   <!-- <p id="departement"></p> -->
                   <!-- <input type="text" name="txt1" id="txt1"> -->
                </div>
                
            </div>


            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Tipe</label>
                <div class="col-sm-3">
                   <select name="tipe" id="tipe" class="form-control" required>
                   <option value="">--Loading--</option>
                   </select>
                   <span style="font-size: 10px;"><b>* Pilih divisi untuk menampilkan tipe</b></span>
                </div>
                
            </div>

                  
            <div class="priodate">

            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Jenis Prioritas </label>
                <div class="col-sm-3">
                   <select name="jenis_prioritas" id="jenis_prioritas2" class="form-control" required>
                   <option value="">-- Jenis Prioritas --</option>
                     <?php
                      if(date("H:i") <= '12:00'){
                    ?>
                       <option value="1">Normal</option>
                       <option value="2">Darurat</option>
                     <?php
                      }else{
                     ?>
                    <option value="1">Normal</option>
                    <?php
                      }
                     ?>
                   </select>
                </div>
            </div>


            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Pengambilan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control readonly" name="tanggal_permintaan" id="tanggal_permintaan1"  placeholder="Pilh Tanggal Pengambilan" required autocomplete="off" >
                    <span style="font-size: 10px;"><b>* Pilih jenis prioritas untung menampilkan tanggal</b></span>
                  </div>
            </div>
          
           </div>

           <div id="sjt" style="display: none;">


             <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Upload File</label>
                <div class="col-sm-3">
               <input type="file" class="form-control readonly" name="dokumensjt[]" id="dokumensjt"  placeholder="Upload Dokumen SJT" autocomplete="off" multiple accept="image/jpeg,image/jpg,image/png,application/pdf">
                </div>
            </div>

          </div>

            <!-- <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Jenis Barang</label>
                <div class="col-sm-3">
                   <select name="jenis_barang" id="jenis_barang" class="form-control" required>
                   <option value="">-- Pilih Jenis Barang --</option>
                   <option value="1">Food</option>
                   <option value="4">Packaging</option>
                   <option value="5">Merchandise</option>
                   <option value="6">Marketing</option>
                   <option value="7">Others Not Food</option>
                   </select>
                </div>
            </div> -->

            <!-- <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Alasan</label>
                <div class="col-sm-3">
             <select name="alasan" id="alasan1" class="form-control" required>
             <option value="">-- Pilih Alasan --</option>
                      <?php

                        $sql = "SELECT * FROM mst_req_reason where reqtype_id='3'";
                        $stmt = sqlsrv_query( $conn, $sql );
                        if( $stmt === false) {
                            die( print_r( sqlsrv_errors(), true) );
                        }
                          
                        
                          while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){
                        
                          echo "<option value='".$row['reason_name']."'>".$row['reason_name']."</option>";
                        
                        }

              ?>
             </select>
                </div>
            </div> -->

            
            <div class="form-group row" style="display:none;" id="entry_docnum">
                <label for="inputEmail" class="col-sm-2 col-form-label">Doc Num Ireap</label>
                <div class="col-sm-3">
              <input type="text" name="no_ireap" id="no_ireap" class="form-control" placeholder="Input No Doc">
                </div>
            </div>


            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-3">
               <textarea name="keterangan" class="form-control" id="keterangan1" cols="10" rows="5" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110"></textarea>
                </div>
            </div>

<br><br>
<button type="button" class="btn btn-primary addmore2"><b>Tambah Barang</b></button>
<button type="button" class="btn btn-danger delete2" ><b>Hapus Barang</b></button>
<br><br>

<span style="font-size: 15px;"><b> * List item yang muncul berdasarkan divisi yang dipilih</b></span>
   <table  class="table table-striped table-bordered dt-responsive nowrap"  id="itemsDetail2">

                <!-- <tr>
                <th  rowspan="2"><input class='check_all1' type='checkbox' onclick="select_all1()"/></th>
                <th  rowspan="2">No</th>
                   <th  rowspan="2">Nama Barang</th>
                   <th  rowspan="2">Kode Barang</th>
                    <th  rowspan="2">Satuan</th>
                    <th  rowspan="2">Jenis Barang</th>
                    <th  rowspan="2">Alasan</th>
                    <th  colspan="2" class="text-center">Jumlah Barang</th>
                    <th  rowspan="2">Kadaluarsa</th>
                    <th  rowspan="2">Keterangan</th>
                </tr>
                <tr>
                    <th>Bagus</th>
                    <th>Tidak Bagus</th>
                    </tr> -->

                    <tr>
                <th><input class='check_all1' type='checkbox' onclick="select_all1()"/></th>
                <th>No</th>
                    <th>Nama Barang</th>
                    <th>Kode Barang</th>
                    <th>Satuan</th>
                    <th>Jenis Barang</th>
                    <th>Alasan</th>
                    <th class="jumlahbagus">Jumlah Bagus</th>
                    <th class="jumlahtidakbagus">Jumlah Tidak Bagus</th>
                    <th class="kadaluarsa">Kadaluarsa</th>
                    <th>Tanggal Terima Barang Dari CK</th>
                    <th  class="keterangantoko">Keterangan Toko</th>
                </tr>
     
 <!-- <tr class="returtr">
    <td>
      <input type='checkbox' class='case1'/>
      <input type='hidden' class='form_control' name='id_barang[]' id='idbarang1_0'/>
    </td>
    <td><p id='snum_0'>1.</p></td>
    <td><select class="items form-control" name="nama_barang[]" id="namabarang1_0"  style="width:220px" name="order_id" id="order_id" required></select></td>
    <td>
      <span class='txtkodebarang1_0'></span>
      <input type='hidden' id='kodebarang1_0' name='kode_barang[]' style='width:140px' class="form-control" readonly required/>
  </td>
    <td>
      <span class='txtuom1_0'></span>
      <input type='hidden' id='uom1_0' name='uom[]'  style='width:100px' class="form-control" readonly required/>
  </td>
      <td>
      <span class='txtjenisbarang1_0'></span>
        <input type='hidden' id='jenisbarang1_0' name='jenis_barang[]'  style='width:100px' class="form-control" readonly required/></td>
    </td>
      <td>
        <select name="alasan[]" id="alasan1_0" class="form-control alasan1" required>
      <option value="">Pilih Alasan</option>
      <?php

$sql = "SELECT * FROM mst_req_reason where reqtype_id='2'";
$stmt = sqlsrv_query( $conn, $sql );
if( $stmt === false) {
    die( print_r( sqlsrv_errors(), true) );
}
  

  while($row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)){

  echo "<option value='".$row['reason_name']."'>".$row['reason_name']."</option>";

}

?>
      </select>
    
      <span class='txtalasan1_0' style="font-size: 10px;"></span>
    </td>
      <td><input type='number' id='qtygood1_0' name='qty_good[]' min="0" style='width:70px' class="form-control" autocomplete="off" required/>
      <td><input type='number' id='qtynotgood1_0' name='qty_notgood[]' min="0" style='width:70px' class="form-control" autocomplete="off" required/></td>
      <td>
      <input type="text"id='expireddate1_0' name='expired_date[]' class="datepickers1 form-control" placeholder="YYYY-MM-DD" autocomplete='off'>
    </td>
      <td><textarea type='text'  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110" class="form-control keterangan_barang" id='keteranganbarang1_0' name='keterangan_barang[]' cols="30" rows="5"></textarea></td>
    </tr> -->




        </table>       
        

        <div align="left">    
        <br>
<button type="submit" class="btn btn-primary prosespermintaan" ><b>Proses Permintaan</b></button>  
<br><br> <br>               
        </div>
        

        </form>

  </div>



</div>


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
<!-- <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script> -->
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.all.min.js"></script>
   <script src="js/jquery.inputmask.bundle.min.js" charset="utf-8"></script>
<script>

  // To make field readonly
$(".readonly").keydown(function(e){
        e.preventDefault();
    });

  
$('#keterangan').on('keydown', function(event) {
   var x = event.which;
   if (x === 13) {
       event.preventDefault();
   } 
});
$('#keterangan1').on('keydown', function(event) {
   var x = event.which;
   if (x === 13) {
       event.preventDefault();
   } 
});  
$("body").on("keydown", ".keterangan_barang", function() {
   var x = event.which;
   if (x === 13) {
       event.preventDefault();
   } 
});



$('#pickup_type').on('change', function() {
    var pickup_type = $('#pickup_type').val();
    var ckarea =  <?php echo $_SESSION['area_ck']; ?>;
    var store = '<?php echo $_SESSION['nama']; ?>';
      if(pickup_type == 1){

        if(ckarea == 1){
          $("#divisi1").html('<option value="">-- Pilih Divisi --</option>'+
                   '<option value="CK JAKARTA">CK JAKARTA</option>'+
                   '<option value="ENG JAKARTA">ENG JAKARTA</option>'+
                   '<option value="IT JAKARTA">IT JAKARTA</option>'+
                   '<option value="GA JAKARTA">GA JAKARTA</option>'+
                   '<option value="OPENING">OPENING</option>');
        }else if(ckarea == 2){
          if(store =='MPK' || store =='MRI' || store =='TSK' || store =='NML' ){
            $("#divisi1").html('<option value="">-- Pilih Divisi --</option>'+
                   '<option value="CK SURABAYA">CK SURABAYA</option>'+
                   '<option value="ENG SURABAYA">ENG JAKARTA</option>'+
                   '<option value="IT SURABAYA">IT SURABAYA</option>'+
                   '<option value="GA SURABAYA">GA JAKARTA</option>'+
                   '<option value="OPENING">OPENING</option>');
          }else{
          $("#divisi1").html('<option value="">-- Pilih Divisi --</option>'+
                   '<option value="CK SURABAYA">CK SURABAYA</option>'+
                   '<option value="ENG SURABAYA">ENG SURABAYA</option>'+
                   '<option value="IT SURABAYA">IT SURABAYA</option>'+
                   '<option value="GA SURABAYA">GA JAKARTA</option>'+
                   '<option value="OPENING">OPENING</option>');
          }
        }else{
          $("#divisi1").html('');
        }

    }else if(pickup_type == 2){

        if(ckarea == 1){
          $("#divisi1").html('<option value="">-- Pilih Divisi --</option>'+
                   '<option value="ENG JAKARTA">ENG JAKARTA</option>'+
                   '<option value="GA JAKARTA">GA JAKARTA</option>'+
                   '<option value="IT JAKARTA">IT JAKARTA</option>');
        }else if(ckarea == 2){
          if(store =='MPK' || store =='MRI' || store =='TSK' || store =='NML' ){
          $("#divisi1").html('<option value="">-- Pilih Divisi --</option>'+
                   '<option value="ENG SURABAYA">ENG JAKARTA</option>'+
                   '<option value="GA JAKARTA">GA JAKARTA</option>'+
                   '<option value="IT SURABAYA">IT SURABAYA</option>');
          }else{
          $("#divisi1").html('<option value="">-- Pilih Divisi --</option>'+
                   '<option value="ENG SURABAYA">ENG SURABAYA</option>'+
                   '<option value="GA JAKARTA">GA JAKARTA</option>'+
                   '<option value="IT SURABAYA">IT SURABAYA</option>');
          }
        }else{
          $("#divisi1").html('');
        }

  }else{
    $("#divisi1").html('');
  }
    
});

$('#jenis_permintaan').on('change', function() {
    var jp = $('#jenis_permintaan').val();
      if(jp == 1 || jp == 2){
        $(".store").show();
      }else{
        $(".store").hide();
      }
});

var jenis_permintaan = $("#jenis_permintaan").val();
var div =$("#divisi").val(); 
    
$('#namabarang_0').select2({
                            placeholder: 'Pilih Nama Barang',
                            // allowClear: true,
                            ajax: {
                              url: 'getitem.php',
                              data: function (params) {
                                  return {
                                    q: params.term, // search term
                                    page: params.page,
                                    jenis_permintaan: jenis_permintaan,
                                    div: div,
                                    tipe: '',
                                    currentSearchTerm: params.term, // search term,
                                     page: params.page || 1
                                  };
                                },
                              type: 'GET',
                              dataType: 'json',
                              delay: 250,
                            //   processResults: function (data) {
                            //     return {
                            //         results: data['id'] + '-'+ data['kat_voucher']
                            //     };
                            //   },
                            processResults: function (data) {
                                return {
                                        results: $.map(data, function(obj) {
                                        return {
                                            id: obj.id,
                                            text: obj.text,
                                            uom: obj.uom,
                                            item_cat: obj.item_cat,
                                            id_mst_item: obj.id_mst_item,
                                            item_code:obj.item_code
                                        };
                                     
                                        })
                                    }; 
                            },
                              cache: true
                            },
                       }).on('change', function (e) {
                        var data = $('#namabarang_0').select2('data');
                        $('#kodebarang_0').val(data[0].item_code);
                        $('#uom_0').val(data[0].uom);
                        $('#jenisbarang_0').val(data[0].item_cat);
                        $('#idbarang_0').val(data[0].id_mst_item);

                        $('.txtkodebarang_0').html('<b>'+data[0].item_code+'</b>');
                        $('.txtuom_0').html('<b>'+data[0].uom+'</b>');
                        $('.txtjenisbarang_0').html('<b>'+data[0].item_cat+'</b>');
                        
                    });


   window.onunload = function () {
      localStorage.removeItem('departement');
      localStorage.removeItem('tipe');
    }

    $("#divisi1").bind("click", function(e){
    lastValue = $(this).val();
      }).bind("change", function(e){
        value = $(this).val();

        // swal.fire({
        //         title: "Notice",
        //         text: "Are you sure ?",
        //         showCancelButton: true,
        //         cancelButtonColor: '#d33',
        //     }) .then((res) => {
        //             if(res.value){
        //                 console.log('confirmed');
        //             }else if(res.dismiss == 'cancel'){
        //                 console.log(localStorage.getItem('lastdepartement'));
        //                 $('#divisi1').val('IT');
        //             }
        //             else if(res.dismiss == 'esc'){
        //                 console.log('cancle-esc**strong text**');
        //             }
        //         });


          changeConfirmation = confirm("Yakin ingin melakukan pemilihan divisi "+value+"?");
          
          if (changeConfirmation) {
            if(value == ''){
              alert('wajib dilakukan pemilihan divisi!');
              $(this).val(lastValue)
        }else{
            var departement =  localStorage.setItem('departement',value);
              $("table#itemsDetail2").find('input[type="checkbox"]').each(function(){
                  if(!$(this).is(":checked")){
                    $(this).parents("tr.returtr").remove();
                  }
                });
                localStorage.removeItem('tipe');
                if(value == 'CK JAKARTA' || value == 'CK SURABAYA'){
                $('#tipe').html('<option value="">-- Pilih Tipe --</option><option value="1">SYSTEM</option><option value="2">NON SYSTEM</option><option value="3">WADAH</option><option value="4">NCR</option><option value="5">DAMAGE</option>');
                  $("#sjt").hide();
                  $(".jumlahbagus").show();
                  $(".kadaluarsa").show();
                  $(".jumlahtidakbagus").html('Jumlah Tidak Bagus'); 
                  $(".keterangan_barang").removeAttr('required');
                  // $("#reason_retur_eng").removeAttr('required');
                  // $("#nopica").removeAttr('required');
                  // $("#nosjt").removeAttr('required');
                  $(".keterangantoko").html('Keterangan Toko');
                  $("#dokumensjt").removeAttr('required');
                }else if(value == 'ENG JAKARTA' || value == 'ENG SURABAYA' || value == 'ENG PU'){
                  $('#tipe').html('<option value="">-- Pilih Tipe --</option><option value="2">NON SYSTEM</option>');
                  $("#sjt").show();
                  $("#entry_docnum").hide();
                  $("#no_ireap").removeAttr('required');
                  $(".jumlahbagus").hide();
                  $(".kadaluarsa").hide();
                  $(".jumlahtidakbagus").html('Jumlah Kirim'); 
                  $(".keterangan_barang").attr("required","true");
                  // $("#reason_retur_eng").attr("required", "true");
                  // $("#nopica").attr("required", "true");
                  // $("#nosjt").attr("required", "true");
                  $(".keterangantoko").html('Nomor SJT');
                  $("#dokumensjt").attr("required", "true");
                }else if(value == 'IT JAKARTA' || value == 'IT SURABAYA' || value == 'OPENING'){
                  $('#tipe').html('<option value="">-- Pilih Tipe --</option><option value="2">NON SYSTEM</option>');
                  $("#sjt").show();
                  $("#entry_docnum").hide();
                  $("#no_ireap").removeAttr('required');
                  $(".jumlahbagus").show();
                  $(".kadaluarsa").show();
                  $(".jumlahtidakbagus").html('Jumlah Tidak Bagus');
                  $(".keterangan_barang").removeAttr('required');
                  // $("#reason_retur_eng").removeAttr('required');
                  // $("#nopica").removeAttr('required');
                  // $("#nosjt").removeAttr('required');
                  $(".keterangantoko").html('Keterangan Toko');
                  $("#dokumensjt").removeAttr('required');
                }else if(value == 'GA JAKARTA' || value == 'GA SURABAYA'){
                  $('#tipe').html('<option value="">-- Pilih Tipe --</option><option value="2">NON SYSTEM</option>');
                  $("#sjt").show();
                  $("#entry_docnum").hide();
                  $("#no_ireap").removeAttr('required');
                  $(".jumlahbagus").show();
                  $(".kadaluarsa").show();
                  $(".jumlahtidakbagus").html('Jumlah Tidak Bagus');
                  $(".keterangan_barang").removeAttr('required');
                  // $("#reason_retur_eng").removeAttr('required');
                  // $("#nopica").removeAttr('required');
                  // $("#nosjt").removeAttr('required');
                  $(".keterangantoko").html('Keterangan Toko');
                  $("#dokumensjt").attr("required", "true");
                }else{
                  $(".jumlahbagus").show();
                  $(".kadaluarsa").show();
                  $(".jumlahtidakbagus").html('Jumlah Tidak Bagus');
                  $(".keterangan_barang").removeAttr('required');
                  // $("#reason_retur_eng").attr("required", "false");
                  // $("#nopica").aremoveAttr('required');
                  // $("#nosjt").removeAttr('required');
                  $(".keterangantoko").html('Keterangan Toko');
                  $("#dokumensjt").removeAttr('required');
                  $("#sjt").hide();
                  $("#entry_docnum").hide();
                  $("#no_ireap").removeAttr('required');
                  $('#tipe').html('<option value="">-- Pilih Tipe --</option><option value="2">NON SYSTEM</option>');
                }
              }
          } else {
              $(this).val(lastValue);
          }
        
      });

      $("#tipe").bind("click", function(e){
    lastValue = $(this).val();
      }).bind("change", function(e){
        value = $(this).val();

        if(value == 1 || value == 4){
          $("#entry_docnum").show();
          $("#no_ireap").attr("required","true");
        }else{
          $("#entry_docnum").hide();
          $("#no_ireap").removeAttr('required');
        }

        // swal.fire({
        //         title: "Notice",
        //         text: "Are you sure ?",
        //         showCancelButton: true,
        //         cancelButtonColor: '#d33',
        //     }) .then((res) => {
        //             if(res.value){
        //                 console.log('confirmed');
        //             }else if(res.dismiss == 'cancel'){
        //                 console.log(localStorage.getItem('lastdepartement'));
        //                 $('#divisi1').val('IT');
        //             }
        //             else if(res.dismiss == 'esc'){
        //                 console.log('cancle-esc**strong text**');
        //             }
        //         });

          //  var todayDate = new Date().toISOString().slice(0, 10);
          changeConfirmation = confirm("Yakin ingin melakukan pemilihan tipe ini?");
          // var today = new Date();
          // var time = today.getHours() + ":" + today.getMinutes();
          var time = '<?php echo date("H:i"); ?>';
          var wadah_flag = <?php echo $_SESSION['wadah_flag']; ?>;
          var store = '<?php echo $_SESSION['nama']; ?>';
          
          if (changeConfirmation) {
                  if(value == ''){
                    alert('wajib dilakukan pemilihan tipe!');
                    $(this).val(lastValue)
              }else{
                  var tipe =  localStorage.setItem('tipe',value);
                    $("table#itemsDetail2").find('input[type="checkbox"]').each(function(){
                        if(!$(this).is(":checked")){
                          $(this).parents("tr.returtr").remove();
                        }
                      });
                            if(value == 3){

                              
                                  if(wadah_flag == 1){

                                    if(time <= '16:00'){
                                  $('#jenis_prioritas2').html('<option value="">-- Jenis Prioritas --</option><option value="1">Normal</option><option value="2">Darurat</option><option value="3">Hari H</option>');
                                  }else{
                                    $('#jenis_prioritas2').html('<option value="">-- Jenis Prioritas --</option><option value="1">Normal</option><option value="3">Hari H</option>');
                                  }

                                  }else{

                                    if(time <= '16:00'){
                                  $('#jenis_prioritas2').html('<option value="">-- Jenis Prioritas --</option><option value="1">Normal</option><option value="2">Darurat</option>');
                                  }else{
                                    $('#jenis_prioritas2').html('<option value="">-- Jenis Prioritas --</option><option value="1">Normal</option>');
                                  }

                                }

                              $('.priodate').show(); 
                              $('#jenis_prioritas2').val('');
                              $('#tanggal_permintaan1').val('');
                            }else{
                              if(store =='ZZZZ'){

                                 if(time <= '16:00'){
                              $('#jenis_prioritas2').html('<option value="">-- Jenis Prioritas --</option><option value="1">Normal</option><option value="2">Darurat</option><option value="3">Hari H</option>');
                              }else{
                                $('#jenis_prioritas2').html('<option value="">-- Jenis Prioritas --</option><option value="1">Normal</option>');
                              }

                              }else {

                              if(time <= '16:00'){
                              $('#jenis_prioritas2').html('<option value="">-- Jenis Prioritas --</option><option value="1">Normal</option><option value="2">Darurat</option>');
                              }else{
                                $('#jenis_prioritas2').html('<option value="">-- Jenis Prioritas --</option><option value="1">Normal</option>');
                              }

                              $('.priodate').show();
                              $('#jenis_prioritas2').val('');
                              $('#tanggal_permintaan1').val('');
                            }

                          }
                    }
          } else {
              $(this).val(lastValue);
          }

 
        
      });


var jenis_permintaan1 = $("#jenis_permintaan1").val();

                    $('#namabarang1_0').select2({
                            placeholder: 'Pilih Nama Barang',
                            ajax: {
                              url: 'getitem.php',
                              data: function (params) {
                                  return {
                                    q: params.term, // search term
                                    page: params.page,
                                    jenis_permintaan: jenis_permintaan1,
                                    div: localStorage.getItem('departement'),
                                    tipe: localStorage.getItem('tipe'),
                                    currentSearchTerm: params.term, // search term,
                                     page: params.page || 1
                                  };
                                },
                              type: 'GET',
                              dataType: 'json',
                              delay: 250,
                            //   processResults: function (data) {
                            //     return {
                            //         results: data['id'] + '-'+ data['kat_voucher']
                            //     };
                            //   },
                            processResults: function (data) {
                                return {
                                        results: $.map(data, function(obj) {
                                        return {
                                          id: obj.id,
                                            text: obj.text,
                                            uom: obj.uom,
                                            item_cat: obj.item_cat,
                                            exp_flag: obj.exp_flag,
                                            kondisi_flag: obj.kondisi_flag,
                                            id_mst_item: obj.id_mst_item,
                                            item_code:obj.item_code
                                        };
                                     
                                        })
                                    }; 
                            },
                              cache: true
                            },
                       }).on('change', function (e) {
                        
                        var data = $('#namabarang1_0').select2('data');
                        $('#kodebarang1_0').val(data[0].item_code);
                        $('#uom1_0').val(data[0].uom);
                        $('#jenisbarang1_0').val(data[0].item_cat);
                        $('#idbarang1_0').val(data[0].id_mst_item);

                        $('.txtkodebarang1_0').html('<b>'+data[0].item_code+'</b>');
                        $('.txtuom1_0').html('<b>'+data[0].uom+'</b>');
                        $('.txtjenisbarang1_0').html('<b>'+data[0].item_cat+'</b>');

                          if(data[0].exp_flag == 1){
                            $('#expireddate1_0').show();
                          }else if(data[0].exp_flag == 0){
                            $('#expireddate1_0').hide();
                            $("#expireddate1_0").prop('required',false);
                          }

                          if(data[0].kondisi_flag == 1){
                            $('#qtygood1_0').show();
                            $('#qtynotgood1_0').hide();
                            $("#qtynotgood1_0").prop('required',false);
                          }else if(data[0].kondisi_flag == 2){
                            $('#qtygood1_0').hide();
                            $("#qtygood1_0").prop('required',false);
                            $('#qtynotgood1_0').show();
                         }else{
                           $('#qtygood1_0').show();
                            $('#qtynotgood1_0').show();
                         }

                    });
                    
        $('.expired_date').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });

         $('.daterequest').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });

 var datePickerOption = {
    showOtherMonths: true,
    selectOtherMonths: true,
    dateFormat: "yy-mm-dd",
    // minDate: 0,
    onSelect: function(selectedDate) {}
}

$("#jenis_prioritas1").on('change',function(){
    var prioritas = $("#jenis_prioritas1 :selected").val();
    var wadah_flag = <?php echo $_SESSION['wadah_flag']; ?>;
    var tipe = $("#tipe :selected").val();

    alert(tipe);

    if($tipe ==3){

      if(prioritas ==2){
      msg='+1';
      $('.datepickers').datepicker('destroy');
      $('.datepickers').val('');
      $('.datepickers').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        minDate: msg,
        dateFormat: "yy-mm-dd"
      });
    }else{
      msg='+0';
      $('.datepickers').datepicker('destroy');
      $('.datepickers').val('');
      $('.datepickers').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        minDate: msg,
        dateFormat: "yy-mm-dd"
      });
    }

    }else{

    if(prioritas ==2){
      msg='+1';
      $('.datepickers').datepicker('destroy');
      $('.datepickers').val('');
      $('.datepickers').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        minDate: msg,
        dateFormat: "yy-mm-dd"
      });
    }else{
      msg='+2';
      $('.datepickers').datepicker('destroy');
      $('.datepickers').val('');
      $('.datepickers').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        minDate: msg,
        dateFormat: "yy-mm-dd"
      });
    }

  }

});

$("#jenis_prioritas2").on('change',function(){
    var prioritas = $("#jenis_prioritas2 :selected").val();
    var wadah_flag = <?php echo $_SESSION['wadah_flag']; ?>;
    var tipe = $("#tipe :selected").val();
    
if(tipe == 1 || tipe == 2 || tipe == 4 ||  tipe == 5){

          if(prioritas ==2){
            msg='+1';
            $('#tanggal_permintaan1').datepicker('destroy');
            $('#tanggal_permintaan1').val('');
            // $('#tanggal_permintaan1').attr('readonly', true);
            $('#tanggal_permintaan1').attr('required', true);
            $('#tanggal_permintaan1').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                minDate: msg,
                dateFormat: "yy-mm-dd"
              });
          }else if(prioritas ==1){
            msg='+2';
            $('#tanggal_permintaan1').datepicker('destroy');
            $('#tanggal_permintaan1').val('');
            // $('#tanggal_permintaan1').attr('readonly', true);
            $('#tanggal_permintaan1').attr('required', true);
            $('#tanggal_permintaan1').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                minDate: msg,
                dateFormat: "yy-mm-dd"
              });
          }else if(prioritas ==3){
             msg='+0';
            $('#tanggal_permintaan1').datepicker('destroy');
            $('#tanggal_permintaan1').val('');
            // $('#tanggal_permintaan1').attr('readonly', true);
            $('#tanggal_permintaan1').attr('required', true);
            $('#tanggal_permintaan1').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                minDate: msg,
                dateFormat: "yy-mm-dd"
              });

          }else{
            $('#tanggal_permintaan1').datepicker('destroy');
            $('#tanggal_permintaan1').val('');
            // $('#tanggal_permintaan1').attr('readonly', true);
            $('#tanggal_permintaan1').attr('required', true);
            $('#tanggal_permintaan1').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                minDate: msg,
                dateFormat: "yy-mm-dd"
              });
          }

    }else if(tipe == 3){
      // alert(tipe);

      if(wadah_flag == 1){

      if(prioritas ==2){
            msg='+1';
            $('#tanggal_permintaan1').datepicker('destroy');
            $('#tanggal_permintaan1').val('');
            // $('#tanggal_permintaan1').attr('readonly', true);
            $('#tanggal_permintaan1').attr('required', true);
            $('#tanggal_permintaan1').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                minDate: msg,
                dateFormat: "yy-mm-dd"
              });
          }else if(prioritas ==1){
            msg='+2';
            $('#tanggal_permintaan1').datepicker('destroy');
            $('#tanggal_permintaan1').val('');
            // $('#tanggal_permintaan1').attr('readonly', true);
            $('#tanggal_permintaan1').attr('required', true);
            $('#tanggal_permintaan1').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                minDate: msg,
                dateFormat: "yy-mm-dd"
              });
          }else if(prioritas ==3){
            msg='+0';
            $('#tanggal_permintaan1').datepicker('destroy');
            $('#tanggal_permintaan1').val('');
            // $('#tanggal_permintaan1').attr('readonly', true);
            $('#tanggal_permintaan1').attr('required', true);
            $('#tanggal_permintaan1').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                minDate: msg,
                dateFormat: "yy-mm-dd"
              });
          }else{
            $('#tanggal_permintaan1').datepicker('destroy');
            $('#tanggal_permintaan1').val('');
            // $('#tanggal_permintaan1').attr('readonly', true);
            $('#tanggal_permintaan1').attr('required', true);
            $('#tanggal_permintaan1').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                minDate: msg,
                dateFormat: "yy-mm-dd"
              });
          }

        }else{

          if(prioritas ==2){
            msg='+1';
            $('#tanggal_permintaan1').datepicker('destroy');
            $('#tanggal_permintaan1').val('');
            // $('#tanggal_permintaan1').attr('readonly', true);
            $('#tanggal_permintaan1').attr('required', true);
            $('#tanggal_permintaan1').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                minDate: msg,
                dateFormat: "yy-mm-dd"
              });
          }else if(prioritas ==1){
            msg='+2';
            $('#tanggal_permintaan1').datepicker('destroy');
            $('#tanggal_permintaan1').val('');
            // $('#tanggal_permintaan1').attr('readonly', true);
            $('#tanggal_permintaan1').attr('required', true);
            $('#tanggal_permintaan1').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                minDate: msg,
                dateFormat: "yy-mm-dd"
              });
          }else{
            $('#tanggal_permintaan1').datepicker('destroy');
            $('#tanggal_permintaan1').val('');
            // $('#tanggal_permintaan1').attr('readonly', true);
            $('#tanggal_permintaan1').attr('required', true);
            $('#tanggal_permintaan1').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                minDate: msg,
                dateFormat: "yy-mm-dd"
              });
          }


        }

      
    }else{
      location.reload();
    }

});



$("body").on("focus", ".datepickers1", function() {
    $(this).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd",
        // minDate: 0,
        onSelect: function(selectedDate) {
            id_arr = $(this).attr('id');
            id = id_arr.split("_");
            $('.txtexpired1_'+id[1]).html('<b>'+selectedDate+'</b>');
        }
      });
});

$("body").on("focus", ".datepickers2", function() {
    $(this).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd",
        // minDate: 0,
        onSelect: function(selectedDate) {
            id_arr = $(this).attr('id');
            id = id_arr.split("_");
            $('.txtarrival1_'+id[1]).html('<b>'+selectedDate+'</b>');
        }
      });
});

$(".delete1").on('click', function() {
  $('.case:checkbox:checked').parents("tr").remove();
    $('.check_all').prop("checked", false); 
  check();

});

var i= 1;
var max = 11;
$(".addmore1").on('click',function(){
        i++;
    var alasan='';
    count =$('table#itemsDetail1 tr').length;
    var data1=
        "<tr>"
        +"<td>"
        +"<input type='checkbox' class='case'/>"
        +"<input type='hidden' class='form_control id_barang' name='id_barang[]' id='idbarang_"+i+"'/>"
        +"</td>"
        +"<td><p id='snum_"+i+"'>"+count+".</p></td>"
        +"<td><select class='items form-control' name='nama_barang[]' id='namabarang_"+i+"'  style='width:220px' required></select></td>"
        +"<td><span class='txtkodebarang_"+i+"'></span><input type='hidden' class='form-control kode_barang' id='kodebarang_"+i+"' name='kode_barang[]' style='width:140px' readonly required/></td>"
        +"<td><span class='txtuom_"+i+"' ></span><input type='hidden' class='form-control uom' id='uom_"+i+"' name='uom[]'  style='width:100px' readonly required/></td>"
        +"<td><span class='txtjenisbarang_"+i+"' ></span><input type='hidden' class='form-control jenisbarang' id='jenisbarang_"+i+"' name='jenisbarang[]'  style='width:100px' readonly required/></td>"
        +"<td><input type='number' class='form-control qty' id='qty_"+i+"' name='qty[]' style='width:70px' autocomplete='off' required/></td>"
        +"<td><select class='form-control alasan' name='alasan[]' id='alasan_"+i+"' required> <option value=''>Pilih Alasan</option>"+alasan+"</select><span class='txtalasan_"+i+"' style='font-size:10px;'></span></td>"
        +"<td><textarea type='text' oninput='javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);' maxlength = '110' class='form-control keterangan_barang' id='keteranganbarang_"+i+"' name='keterangan_barang[]' cols='30' rows='5'></textarea></td>"
        +"</tr>";
        if (count<max) {
        $('table#itemsDetail1').append(data1); 
        }

   $('#namabarang_'+i+'').select2({
    placeholder: 'Pilih Nama Barang',
                            // allowClear: true,
                            ajax: {
                              url: 'getitem.php',
                              data: function (params) {
                                  return {
                                    q: params.term, // search term
                                    page: params.page,
                                    jenis_permintaan: jenis_permintaan,
                                    div: div,
                                    tipe: ''
                                  };
                                },
                              type: 'GET',
                              dataType: 'json',
                              delay: 250,
                            processResults: function (data) {
                                return {
                                        results: $.map(data, function(obj) {
                                        return {
                                          id: obj.id,
                                            text: obj.text,
                                            uom: obj.uom,
                                            item_cat: obj.item_cat,
                                            id_mst_item: obj.id_mst_item,
                                            item_code: obj.item_code
                                        };
                                     
                                        })
                                    }; 
                            },
                              cache: true
                            },
                       }).on('change', function (e) {

                        var data = $(this).select2('data');
                        id_arr = $(this).attr('id');
                        id = id_arr.split("_");
                        $('#kodebarang_'+id[1]).val(data[0].item_code);
                        $('#uom_'+id[1]).val(data[0].uom);
                        $('#jenisbarang_'+id[1]).val(data[0].item_cat);
                        $('#idbarang_'+id[1]).val(data[0].id_mst_item);
                        
                        $('.txtkodebarang_'+id[1]).html('<b>'+data[0].item_code+'</b>');
                        $('.txtuom_'+id[1]).html('<b>'+data[0].uom+'</b>');
                        $('.txtjenisbarang_'+id[1]).html('<b>'+data[0].item_cat+'</b>');

                        $('.txtalasan_'+id[1]).empty();

                        var jenis_permintaan = $('#jenis_permintaan').val(); 
                            $.ajax({
                                    type: 'POST', 
                                  url: 'getreason.php', 
                                data: 'jenis_permintaan=' + jenis_permintaan, 
                                success: function(response) { 
                                      $('#alasan_'+id[1]).html(response); 
                                    }
                              });

                        });

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
  obj=$('table#itemsDetail1 tr').find('p');
  $.each( obj, function( key, value ) {
  id=value.id;
  $('#'+id).html(key+1);
  })
} 


$(".delete2").on('click', function() {
  $('.case1:checkbox:checked').parents("tr").remove();
    $('.check_all1').prop("checked", false); 
  check1();
});

var j= 1;
var max1 = 11;
$(".addmore2").on('click',function(){
if($('#divisi1 :selected').val() ==''){
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Silahkan dilakukan pemilihan divisi terlebih dahulu!'
        });
      $("#divisi1").focus();
}else{
  
  j++;
    var alasan='';
    count1 =$('table#itemsDetail2 tr').length;

    // if($('#divisi1 :selected').val() == 'ENG JAKARTA' || $('#divisi1 :selected').val() == 'ENG SURABAYA' || $('#divisi1 :selected').val() == 'ENG PU' || $('#divisi1 :selected').val() == 'IT JAKARTA' || $('#divisi1 :selected').val() == 'IT SURABAYA' || $('#divisi1 :selected').val() == 'GA JAKARTA' || $('#divisi1 :selected').val() == 'GA SURABAYA' || $('#divisi1 :selected').val() == 'OPENING'){
    if($('#divisi1 :selected').val() == 'ENG JAKARTA' || $('#divisi1 :selected').val() == 'ENG SURABAYA' || $('#divisi1 :selected').val() == 'ENG PU'){

              var data2=
        "<tr class='returtr'>"
        +"<td>"
        +"<input type='checkbox' class='case1'/>"
        +"<input type='hidden' class='form_control id_barang' name='id_barang[]' id='idbarang1_"+j+"'/>"
        +"</td>"
        +"<td><p id='snum_"+j+"''>"+count1+"</p></td>"
        +"<td><select class='items form-control' name='nama_barang[]' id='namabarang1_"+j+"'  style='width:220px' required></select></td>"
        +"<td><span class='txtkodebarang1_"+j+"'></span><input type='hidden' class='form-control kode_barang' id='kodebarang1_"+j+"' name='kode_barang[]' style='width:140px' readonly required/></td>"
        +"<td><span class='txtuom1_"+j+"'></span><input type='hidden' class='form-control uom' id='uom1_"+j+"' name='uom[]'  style='width:100px' readonly required/></td>"
        +"<td><span class='txtjenisbarang1_"+j+"'></span><input type='hidden' class='form-control jenis_barang' id='jenisbarang1_"+j+"' name='jenis_barang[]' style='width:100px' autocomplete='off' readonly required/></td>"
        +"<td><select class='form-control alasan1' name='alasan[]' id='alasan1_"+j+"' required> <option value=''>Pilih Alasan</option>"+alasan+"</select><span class='txtalasan1_"+j+"' style='font-size:10px;'></span></td>"
        +"<td style='display:none;'><input type='text' min='0' id='qtygood1_"+j+"' class='qty_good' name='qty_good[]' style='width:70px' class='form-control' autocomplete='off' required/></td>"
        +"<td ><input type='TEXT'  id='qtynotgood1_"+j+"' name='qty_notgood[]' class='qty_notgood' style='width:70px' value='0.00' class='form-control' autocomplete='off' required/></td>"
        +"<td style='display:none;'>"
        +"<input type='text' id='expireddate1_"+j+"' name='expired_date[]' class='form-control datepickers1' placeholder='YYYY-MM-DD' autocomplete='off'>"
        +"<span class='txtexpired1_"+j+"' style='font-size:13px;'></span>"
        +"</td>"
        +"<td>"
        +"<input type='text' id='arrival1_"+j+"' name='arrival_date[]' class='form-control datepickers2' placeholder='YYYY-MM-DD' autocomplete='off' required>"
        // +"<input type='text' class='expired_date' id='expireddate_"+i+"' name='expired_date[]'  style='width:70px'/>"
        +"<span class='txtarrival1_"+j+"' style='font-size:13px;'></span>"
        +"</td>"
        +"<td><textarea type='text' oninput='javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);' maxlength = '110' class='form-control keterangan_barang' id='keteranganbarang1_"+j+"' name='keterangan_barang[]' cols='30' rows='5' required></textarea></td>"
        +"</tr>";
    }else{
        var data2=
        "<tr class='returtr'>"
        +"<td>"
        +"<input type='checkbox' class='case1'/>"
        +"<input type='hidden' class='form_control id_barang' name='id_barang[]' id='idbarang1_"+j+"'/>"
        +"</td>"
        +"<td><p id='snum_"+j+"''>"+count1+"</p></td>"
        +"<td><select class='items form-control' name='nama_barang[]' id='namabarang1_"+j+"'  style='width:220px' required></select></td>"
        +"<td><span class='txtkodebarang1_"+j+"'></span><input type='hidden' class='form-control kode_barang' id='kodebarang1_"+j+"' name='kode_barang[]' style='width:140px' readonly required/></td>"
        +"<td><span class='txtuom1_"+j+"'></span><input type='hidden' class='form-control uom' id='uom1_"+j+"' name='uom[]'  style='width:100px' readonly required/></td>"
        +"<td><span class='txtjenisbarang1_"+j+"'></span><input type='hidden' class='form-control jenis_barang' id='jenisbarang1_"+j+"' name='jenis_barang[]' style='width:100px' autocomplete='off' readonly required/></td>"
        +"<td><select class='form-control alasan1' name='alasan[]' id='alasan1_"+j+"' required> <option value=''>Pilih Alasan</option>"+alasan+"</select><span class='txtalasan1_"+j+"' style='font-size:10px;'></span></td>"
        +"<td><input type='text'id='qtygood1_"+j+"' name='qty_good[]' style='width:70px' class='form-control' autocomplete='off' value='0.00' required/></td>"
        +"<td><input type='text'  id='qtynotgood1_"+j+"' value='0.00' name='qty_notgood[]' style='width:70px' class='form-control' autocomplete='off' required/></td>"
        +"<td>"
        +"<input type='text' id='expireddate1_"+j+"' name='expired_date[]' class='form-control datepickers1' placeholder='YYYY-MM-DD' autocomplete='off'>"
        +"<span class='txtexpired1_"+j+"' style='font-size:13px;'></span>"
        +"</td>"
        +"<td>"
        +"<input type='text' id='arrival1_"+j+"' name='arrival_date[]' class='form-control datepickers2' placeholder='YYYY-MM-DD' autocomplete='off' required>"
        // +"<input type='text' class='expired_date' id='expireddate_"+i+"' name='expired_date[]'  style='width:70px'/>"
        +"<span class='txtarrival1_"+j+"' style='font-size:13px;'></span>"
        +"</td>"
        +"<td><textarea type='text' oninput='javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);' maxlength = '110' class='form-control keterangan_barang' id='keteranganbarang1_"+j+"' name='keterangan_barang[]' cols='30' rows='5'></textarea></td>"
        +"</tr>";

      }
        if (count1<max1) {
          newEntry =  $('table#itemsDetail2').append(data2).datepicker();  
        }

          $('#qtygood1_'+j+', #qtynotgood1_'+j+'').inputmask({
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

   $('#namabarang1_'+j+'').select2({
    placeholder: 'Pilih Nama Barang',
                            // allowClear: true,
                            ajax: {
                              url: 'getitem.php',
                              data: function (params) {
                                  return {
                                    q: params.term, // search term
                                    page: params.page,
                                    jenis_permintaan: jenis_permintaan1,
                                    div: localStorage.getItem('departement'),
                                    tipe: localStorage.getItem('tipe')
                                  };
                                },
                              type: 'GET',
                              dataType: 'json',
                              delay: 250,
                            processResults: function (data) {
                                return {
                                        results: $.map(data, function(obj) {
                                        return {
                                          id: obj.id,
                                            text: obj.text,
                                            uom: obj.uom,
                                            item_cat: obj.item_cat,
                                            exp_flag: obj.exp_flag,
                                            kondisi_flag: obj.kondisi_flag,
                                            id_mst_item: obj.id_mst_item,
                                            item_code: obj.item_code
                                        };
                                     
                                        })
                                    }; 
                            },
                              cache: true
                            },
                       }).on('change', function (e) {

                        var data = $(this).select2('data');
                        id_arr = $(this).attr('id');
                        id = id_arr.split("_");
                        $('#kodebarang1_'+id[1]).val(data[0].item_code);
                        $('#uom1_'+id[1]).val(data[0].uom);
                        $('#jenisbarang1_'+id[1]).val(data[0].item_cat);
                        $('#idbarang1_'+id[1]).val(data[0].id_mst_item);

                        $('.txtkodebarang1_'+id[1]).html('<b>'+data[0].item_code+'</b>');
                        $('.txtuom1_'+id[1]).html('<b>'+data[0].uom+'</b>');
                        $('.txtjenisbarang1_'+id[1]).html('<b>'+data[0].item_cat+'</b>');

                        if(data[0].exp_flag == 1){
                            $('#expireddate1_'+id[1]).show(); 
                            $('#arrival1_'+id[1]).show(); 
                            $('#arrival1_'+id[1]).prop('required',true);
                          }else if(data[0].exp_flag == 0){
                            if($('#divisi1 :selected').val() == 'ENG JAKARTA' || $('#divisi1 :selected').val() == 'ENG SURABAYA' || $('#divisi1 :selected').val() == 'ENG PU'){
                            $('#expireddate1_'+id[1]).hide();
                            $('#expireddate1_'+id[1]).prop('required',false);
                            $('#arrival1_'+id[1]).show();
                            $('#arrival1_'+id[1]).prop('required',true);
                            }else{
                              if($('#tipe :selected').val() == 1 || $('#tipe :selected').val() == 3 || $('#tipe :selected').val() == 4){
                                $('#expireddate1_'+id[1]).hide();
                                $('#expireddate1_'+id[1]).prop('required',false);
                                $('#arrival1_'+id[1]).show();
                                $('#arrival1_'+id[1]).prop('required',true);
                              }else{
                                $('#expireddate1_'+id[1]).hide();
                                $('#expireddate1_'+id[1]).prop('required',false);
                                $('#arrival1_'+id[1]).hide();
                                $('#arrival1_'+id[1]).prop('required',false);
                              }
                            }
                          }

                          if(data[0].kondisi_flag == 1){
                            $('#qtygood1_'+id[1]).show();
                            $('#qtynotgood1_'+id[1]).hide();
                            $('#qtynotgood1_'+id[1]).prop('required',false);
                          }else if(data[0].kondisi_flag == 2){
                            $('#qtygood1_'+id[1]).hide();
                            $('#qtygood1_'+id[1]).prop('required',false);
                            $('#qtynotgood1_'+id[1]).show();
                         }else{
                           $('#qtygood1_'+id[1]).show();
                            $('#qtynotgood1_'+id[1]).show();
                         }

                         $('.txtalasan1_'+id[1]).empty();

                            $.ajax({
                                    type: 'POST', 
                                  url: 'getreason.php', 
                                data: 'jenis_permintaan=' + jenis_permintaan1, 
                                success: function(response) { 
                                      $('#alasan1_'+id[1]).html(response); 
                                    }
                              });

                        });
          }

});

 


      function select_all1() {
    $('input[class=case1]:checkbox').each(function(){ 
      if($('input[class=check_all1]:checkbox:checked').length == 0){ 
        $(this).prop("checked", false); 
      } else {
        $(this).prop("checked", true); 
      } 
    });
  }

      
    function check1(){
      obj=$('table#itemsDetail2 tr').find('p');
      $.each( obj, function( key, value ) {
      id=value.id;
      var ids =$('#'+id).html(key+1);
      console.log(ids);
      });
      }

      

$("body").on("change", ".alasan", function() {
                        id_arr = $(this).attr('id');
                        id = id_arr.split("_");
                        var alasan =    $('#alasan_'+id[1]).val();
                        $('.txtalasan_'+id[1]).html('<b>'+alasan+'</b>');
                        
  });

  $("body").on("change", ".alasan1", function() {
                        id_arr = $(this).attr('id');
                        id = id_arr.split("_");
                        var alasan =    $('#alasan1_'+id[1]).val();
                        $('.txtalasan1_'+id[1]).html('<b>'+alasan+'</b>');
                        
  });

    $("body").on("change", ".alasan1", function() {
                        id_arr = $(this).attr('id');
                        id = id_arr.split("_");
                        var alasan =    $('#alasan1_'+id[1]).val();
                        $('.txtalasan1_'+id[1]).html('<b>'+alasan+'</b>');
                        
  });


    $('#tanggal_permintaan1').datepicker({
    //altField: , //do not use

}).change(function(){
    var getdate = new Date();
    var today = getdate.getFullYear()+'-'+(getdate.getMonth()+1)+'-'+getdate.getDate();
    var dateretur = $('#tanggal_permintaan1').val();
    var jenis_prioritas2 = $('#jenis_prioritas2 :selected').val();
    var pickup_type = $('#pickup_type :selected').val();

     var d = new Date(),
      h = (d.getHours()<10?'0':'') + d.getHours(),
      m = (d.getMinutes()<10?'0':'') + d.getMinutes();
    var time = h + ':' + m;

    var str1= dateretur.split('-');
    var str2= today.split('-');

    //                yyyy   , mm       , dd
    var t1 = new Date(str1[0], str1[1]-1, str1[2]);
    var t2 = new Date(str2[0], str2[1]-1, str2[2]);

    var diffMS = t1 - t2;    
    console.log(diffMS + ' ms');

    var diffS = diffMS / 1000;    
    console.log(diffS + ' ');

    var diffM = diffS / 60;
    console.log(diffM + ' minutes');

    var diffH = diffM / 60;
    console.log(diffH + ' hours');

    var diffD = diffH / 24;
    var diffD = diffH / 24;

    if(pickup_type == 1){


    if(jenis_prioritas2 == 1){
      // Normal

      if(diffD == 2){

        if(time > '15:00'){
          alert("Mohon maaf retur barang min H-2 & min jam 15:00");
          $('.addmore2').attr('disabled','disabled');
          $('.prosespermintaan').attr('disabled','disabled');
        }else{
          $('.addmore2').removeAttr('disabled');
          $('.prosespermintaan').removeAttr('disabled');
        }

      }else if(diffD > 2){
         $('.addmore2').removeAttr('disabled');
         $('.prosespermintaan').removeAttr('disabled');
      }else if(diffD < 2){
         alert("Mohon maaf retur barang min H-2");
         $('.addmore2').attr('disabled','disabled');
         $('.prosespermintaan').attr('disabled','disabled');
      }else{

      }

    }else if(jenis_prioritas2 == 2){
      // Darurat

        if(diffD == 1){

        if(time > '16:00'){
          alert("Mohon maaf retur barang min H-1 & min jam 12:00");
          $('.addmore2').attr('disabled','disabled');
          $('.prosespermintaan').attr('disabled','disabled');
        }else{
          $('.addmore2').removeAttr('disabled');
          $('.prosespermintaan').removeAttr('disabled');
        }

      }else if(diffD > 1){
          $('.addmore2').removeAttr('disabled');
          $('.prosespermintaan').removeAttr('disabled');
      }else if(diffD < 1){
          alert("Mohon maaf retur barang min H-1");
          $('.addmore2').attr('disabled','disabled');
          $('.prosespermintaan').attr('disabled','disabled');
      }else{

      }

    }else if(jenis_prioritas2 == 3){
      // Hari ini
      $('.addmore2').removeAttr('disabled');
      $('.prosespermintaan').removeAttr('disabled');
    }else{

    }

  }else{
    
  }

});

</script>
</body>
</html>       