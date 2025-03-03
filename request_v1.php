<?php
$halaman="request_new";
include "layouts/header.php";
include "layouts/navbar.php";
?>

<div class="container1">
 <div class="row">

<div class="col-sm-12" style="margin-top: 26px;" >

<!-- Nav tabs -->
<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link active" data-toggle="tab" href="#menu1"><b>Formulir Permintaan Transfer Putus Store</b></a>
  </li>
  <li class="nav-item">
    <a class="nav-link" data-toggle="tab" href="#menu2"><b>Formulir Permintaan Retur Barang</b></a>
  </li>
</ul>

<!-- Tab panes -->
<div class="tab-content">

  <div class="tab-pane container active" id="menu1">

  <form action="requestproses.php" method="POST" >
  <br><br>
<fieldset >

    
<div class="form-group row">
        <label for="inputPassword" class="col-sm-2 col-form-label">Jenis Permintaan</label>
        <div class="col-sm-3">
           <select name="jenis_permintaan" id="jenis_permintaan" class="form-control" readonly required>
           <option value="1">Transfer Putus Store</option>
           <!-- <option value="2">Transfer Balik Store</option> -->
           <!-- <option value="3">Retur Barang</option> -->
           </select>
        </div>
    </div>
    
    <div class="form-group row">
        <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
        <div class="col-sm-3">
            <input type="text" class="form-control datepickers" name="tanggal_permintaan" id="tanggal_permintaan" placeholder="Pilh Tanggal Permintaan" readonly required>
        </div>
    </div>

    

    <div class="form-group row store">
        <label for="inputPassword" class="col-sm-2 col-form-label">Toko Tujuan </label>
        <div class="col-sm-2">
           <select name="store" id="store" class="form-control">
           <option value="">-- Pilih Toko --</option>
         <?php
         $store =  $_SESSION["nama"];

              $serverNameHO = "portal.multirasa.co.id";
              $connectionInfoHO = array( "Database"=>"role", "UID"=>"sa", "PWD"=>"Mrn.14");
              $connHO = sqlsrv_connect( $serverNameHO, $connectionInfoHO );
              if( $connHO === false ) {
                  die( print_r( sqlsrv_errors(), true));
              }

              $sqlstore = "SELECT upper(uid) store FROM usrlogin where dep='STORE' and uid not in ('$store')";
              $stmtstore = sqlsrv_query( $connHO, $sqlstore );
              if( $stmtstore === false) {
                  die( print_r( sqlsrv_errors(), true) );
              }

              while( $rowstore = sqlsrv_fetch_array( $stmtstore, SQLSRV_FETCH_ASSOC) ) {
                    echo "<option value=".$rowstore['store']."> ".$rowstore['store']."</option>";
              }

              ?>
           </select>
        </div>
    </div> 

    <div class="form-group row">
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
    </div>

    <div class="form-group row">
        <label for="inputEmail" class="col-sm-2 col-form-label">Alasan</label>
        <div class="col-sm-3">
     <select name="alasan" id="alasan" class="form-control" required>
       <option value="">-- Pilih Alasan --</option>
       <?php

         $sql = "SELECT * FROM mst_req_reason where reqtype_id='1'";
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
    </div>
    <div class="form-group row">
        <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan</label>
        <div class="col-sm-3">
       <textarea name="keterangan" class="form-control" id="keterangan" cols="10" rows="5"></textarea>
        </div>
    </div>
</fieldset>

<br><br>
<button type="button" class="btn btn-primary addmore1"><b>Tambah Barang</b></button>
<button type="button" class="btn btn-danger delete1" ><b>Hapus Barang</b></button>
<br><br>

<table  class="table table-striped table-bordered dt-responsive nowrap"  id="itemsDetail1">

        <tr>
        <th><input class='check_all' type='checkbox' onclick="select_all()"/></th>
           <th style="width: 10%;">Nama Barang</th>
           <th>Kode Barang</th>
            <th>UOM</th>
            <th>Jumlah</th>
            <th>Keterangan Barang</th>
        </tr>


<tr>
<td>
<input type='checkbox' class='case'/>
<input type='hidden' class='form_control' name='id_barang[]' id='idbarang_0'/>
</td>
<td><select class="items form-control" name="nama_barang[]" id="namabarang_0"  style="width:220px" name="order_id" id="order_id" required></select></td>
<td><input type='text' id='kodebarang_0' name='kode_barang[]' style='width:140px' class="form-control" readonly required/></td>
<td><input type='text' id='uom_0' name='uom[]'  style='width:100px' class="form-control" readonly required/></td>
<td><input type='number' id='qty_0' name='qty[]' style='width:70px' class="form-control" autocomplete="off" required/></td>
<td><textarea type='text' class="form-control" id='keteranganbarang_0' name='keterangan_barang[]' cols="30" rows="5"></textarea></td>
</tr>




</table>         


<div align="right">    
<br>
<button type="submit" class="btn btn-primary" ><b>Proses Permintaan</b></button>  
<br><br> <br>               
</div>
</form>
   
  </div>


  <div class="tab-pane container fade" id="menu2">
<br><br>
  <form action="requestproses.php" method="POST" >

              
  <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Jenis Permintaan</label>
                <div class="col-sm-3">
                   <select name="jenis_permintaan" id="jenis_permintaan1" class="form-control" readonly required>
                   <!-- <option value="">-- Pilih Jenis Permintaan --</option> -->
                   <!-- <option value="1">Transfer Putus Store</option> -->
                   <!-- <option value="2">Transfer Balik Store</option> -->
                   <option value="3">Retur Barang</option>
                   </select>
                </div>
            </div>

        <fieldset >
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control datepickers1" name="tanggal_permintaan" id="tanggal_permintaan1" placeholder="Pilh Tanggal Permintaan" readonly required>
                </div>
            </div>

            <div class="form-group row">
                <label for="inputPassword" class="col-sm-2 col-form-label">Dep Tujuan</label>
                <div class="col-sm-3">
                   <select name="jenis_barang" id="jenis_barang" class="form-control" required>
                   <option value="">-- Pilih Dep Tujuan --</option>
                   <option value="CK">CK</option>
                   <option value="IT">IT</option>
                   <option value="ENG">ENG</option>
                   <option value="HRGA">HRGA</option>
                   </select>
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


            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan</label>
                <div class="col-sm-3">
               <textarea name="keterangan" class="form-control" id="keterangan" cols="10" rows="5"></textarea>
                </div>
            </div>
        </fieldset>

<br><br>
<button type="button" class="btn btn-primary addmore2"><b>Tambah Barang</b></button>
<button type="button" class="btn btn-danger delete2" ><b>Hapus Barang</b></button>
<br><br>

   <table  class="table table-striped table-bordered dt-responsive nowrap"  id="itemsDetail2">

                <tr>
                <th><input class='check_all' type='checkbox' onclick="select_all()"/></th>
                   <th style="width: 10%;">Nama Barang</th>
                   <th>Kode Barang</th>
                    <th>Satuan</th>
                    <th>Jenis Barang</th>
                    <th>Jumlah</th>
                    <th>Alasan</th>
                    <th>Kondisi Bagus</th>
                    <th>Kondisi Tidak Bagus</th>
                    <th>Kadarluarsa</th>
                    <th>Keterangan</th>
                </tr>
   
     
 <tr>
    <td>
      <input type='checkbox' class='case'/>
      <input type='hidden' class='form_control' name='id_barang[]' id='idbarang1_0'/>
    </td>
    <td><select class="items form-control" name="nama_barang[]" id="namabarang1_0"  style="width:220px" name="order_id" id="order_id" required></select></td>
    <td><input type='text' id='kodebarang1_0' name='kode_barang[]' style='width:140px' class="form-control" readonly required/></td>
    <td><input type='text' id='uom1_0' name='uom[]'  style='width:100px' class="form-control" readonly required/></td>
      <td><input type='text' id='uom1_0' name='uom[]'  style='width:100px' class="form-control" readonly required/></td>
      <td><input type='text' id='uom1_0' name='uom[]'  style='width:100px' class="form-control"  required/></td>
      <td><select name="kondisi[]" id="kondisi1_0" required>
      <option value="">Pilih Alasan</option>
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
      </select></td>
      <td><input type='number' id='qty1_0' name='qty[]' style='width:70px' class="form-control" autocomplete="off" required/></td>
      <td><input type='number' id='qty1_0' name='qty[]' style='width:70px' class="form-control" autocomplete="off" required/></td>
      <td>
      <input type="text"id='expireddate1_0' name='expired_date[]' class="datepickers form-control" placeholder="YYYY-MM-DD" autocomplete='off'>
      <!-- <input type='text' id='expireddate_0' name='expired_date[]' class="expired_date"  style='width:70px'> -->
    </td>
      <td><textarea type='text' class="form-control" id='keteranganbarang1_0' name='keterangan_barang[]' cols="30" rows="5"></textarea></td>
    </tr>




        </table>         
     
        
        <div align="right">    
        <br>
<button type="submit" class="btn btn-primary" ><b>Proses Permintaan</b></button>  
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
<script>

$('#jenis_permintaan').on('change', function() {
    var jp = $('#jenis_permintaan').val();
      if(jp == 1 || jp == 2){
        $(".store").show();
      }else{
        $(".store").hide();
      }
});
    
$('#namabarang_0').select2({
                            placeholder: 'Pilih Nama Barang',
                            allowClear: true,
                            ajax: {
                              url: 'getitem.php',
                              data: function (params) {
                                  return {
                                    q: params.term, // search term
                                    page: params.page,
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
                                            id: obj.text,
                                            text: obj.text,
                                            uom: obj.uom,
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
                        $('#idbarang_0').val(data[0].id_mst_item);
                    });

                    $('#namabarang1_0').select2({
                            placeholder: 'Pilih Nama Barang',
                            allowClear: true,
                            ajax: {
                              url: 'getitem.php',
                              data: function (params) {
                                  return {
                                    q: params.term, // search term
                                    page: params.page,
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
                                            id: obj.text,
                                            text: obj.text,
                                            uom: obj.uom,
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
                        $('#idbarang1_0').val(data[0].id_mst_item);
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

    $('.datepickers').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd"
      });


    $('#tanggal_permintaan1').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd"
      });

      $("body").on("focus", ".datepickers1", function() {
    $(this).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd",
        // minDate: 0,
        onSelect: function(selectedDate) {}
      });
});


var i= 1;
$(".addmore1").on('click',function(){
        i++;
    var data1=
        "<tr>"
        +"<td>"
        +"<input type='checkbox' class='case'/>"
        +"<input type='hidden' class='form_control id_barang' name='id_barang[]' id='idbarang_"+i+"'/>"
        +"</td>"
        +"<td><select class='items form-control' name='nama_barang[]' id='namabarang_"+i+"'  style='width:220px' required></select></td>"
        +"<td><input type='text' class='form-control kode_barang' id='kodebarang_"+i+"' name='kode_barang[]' style='width:140px' readonly required/></td>"
        +"<td><input type='text' class='form-control uom' id='uom_"+i+"' name='uom[]'  style='width:100px' readonly required/></td>"
        +"<td><input type='number' class='form-control qty' id='qty_"+i+"' name='qty[]' style='width:70px' autocomplete='off' required/></td>"
        +"<td><textarea type='text' class='form-control keterangan_barang' id='keteranganbarang_"+i+"' name='keterangan_barang[]' cols='30' rows='5'></textarea></td>"
        +"</tr>";
        newEntry =  $('table#itemsDetail1').append(data1);   

   $('#namabarang_'+i+'').select2({
    placeholder: 'Pilih Nama Barang',
                            allowClear: true,
                            ajax: {
                              url: 'getitem.php',
                              data: function (params) {
                                  return {
                                    q: params.term, // search term
                                    page: params.page
                                  };
                                },
                              type: 'GET',
                              dataType: 'json',
                              delay: 250,
                            processResults: function (data) {
                                return {
                                        results: $.map(data, function(obj) {
                                        return {
                                          id: obj.text,
                                            text: obj.text,
                                            uom: obj.uom,
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
                        $('#idbarang_'+id[1]).val(data[0].id_mst_item);
                        });

});

var i= 1;
$(".addmore2").on('click',function(){
        i++;
        var data2=
        "<tr>"
        +"<td>"
        +"<input type='checkbox' class='case'/>"
        +"<input type='hidden' class='form_control id_barang' name='id_barang[]' id='idbarang1_"+i+"'/>"
        +"</td>"
        +"<td><select class='items form-control' name='nama_barang[]' id='namabarang1_"+i+"'  style='width:220px' required></select></td>"
        +"<td><input type='text' class='form-control kode_barang' id='kodebarang1_"+i+"' name='kode_barang[]' style='width:140px' readonly required/></td>"
        +"<td><input type='text' class='form-control uom' id='uom1_"+i+"' name='uom[]'  style='width:100px' readonly required/></td>"
        +"<td><input type='number' class='form-control qty' id='qty1_"+i+"' name='qty[]' style='width:70px' autocomplete='off'required/></td>"
        +" <td><select name='kondisi[]' id='kondisi1_"+i+"' required> <option value=''>Pilih Kondisi</option><option value='Bagus'>Bagus</option><option value='Bagus'>Tidak Bagus</option></select></td>"
        +"<td>"
        +"<input type='text' id='expireddate1_"+i+"' name='expired_date[]' class='form-control datepickers1' placeholder='YYYY-MM-DD' autocomplete='off'>"
        // +"<input type='text' class='expired_date' id='expireddate_"+i+"' name='expired_date[]'  style='width:70px'/>"
        +"</td>"
        +"<td><textarea type='text' class='form-control keterangan_barang' id='keteranganbarang1_"+i+"' name='keterangan_barang[]' cols='30' rows='5'></textarea></td>"
        +"</tr>";
        newEntry =  $('table#itemsDetail2').append(data2).datepicker();   

   $('#namabarang1_'+i+'').select2({
    placeholder: 'Pilih Nama Barang',
                            allowClear: true,
                            ajax: {
                              url: 'getitem.php',
                              data: function (params) {
                                  return {
                                    q: params.term, // search term
                                    page: params.page
                                  };
                                },
                              type: 'GET',
                              dataType: 'json',
                              delay: 250,
                            processResults: function (data) {
                                return {
                                        results: $.map(data, function(obj) {
                                        return {
                                          id: obj.text,
                                            text: obj.text,
                                            uom: obj.uom,
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
                        $('#idbarang1_'+id[1]).val(data[0].id_mst_item);
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

   //to check all checkboxes
   $(document).on('change','#check_all',function(){
        $('input[class=case]:checkbox').prop("checked", $(this).is(':checked'));
      });

      //deletes the selected table rows
      $(".delete1").on('click', function() {
        $('.case:checkbox:checked').parents("tr").remove();
        $('#check_all').prop("checked", false); 
      });

          //deletes the selected table rows
          $(".delete2").on('click', function() {
        $('.case:checkbox:checked').parents("tr").remove();
        $('#check_all').prop("checked", false); 
      });

      
    function check(){
      obj=$('table tr').find('span');
      $.each( obj, function( key, value ) {
      id=value.id;
      $('#'+id).html(key+1);
      });
      }

      $('#jenis_permintaan').change(function() { 
     var jenis_permintaan = $(this).val(); 
     $.ajax({
            type: 'POST', 
          url: 'getreason.php', 
         data: 'jenis_permintaan=' + jenis_permintaan, 
         success: function(response) { 
              $('#alasan').html(response); 
            }
       });
      });

      $('#jenis_permintaan1').change(function() { 
     var jenis_permintaan = $(this).val(); 
     $.ajax({
            type: 'POST', 
          url: 'getreason.php', 
         data: 'jenis_permintaan=' + jenis_permintaan, 
         success: function(response) { 
              $('#alasan1').html(response); 
            }
       });
      });


</script>
</body>
</html>       