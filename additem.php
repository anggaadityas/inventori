<?php
$halaman="masteritem";
include "layouts/header.php";
include "layouts/navbar.php";
$div_area= $_SESSION['area_div'];
if($div_area =='CK JAKARTA' OR $div_area=='CK SURABAYA'){
     $div='CK';
     $jenispermintaan='<option value="1">Transfer Putus Store</option><option value="2">Retur Barang</option>';
      $tipe='<option value="1">Sistem</option><option value="2">Non Sistem</option><option value="3">Wadah</option><option value="5">Damage</option>';
}else if($div_area =='IT JAKARTA' OR $div_area=='IT SURABAYA'){
     $div='IT';
      $jenispermintaan='<option value="2">Retur Barang</option>';
      $tipe='<option value="2">Non Sistem</option>';
}else if($div_area =='ENG JAKARTA' OR $div_area=='ENG SURABAYA'){
     $div='ENG';
      $jenispermintaan='<option value="2">Retur Barang</option>';
      $tipe='<option value="2">Non Sistem</option>';
}else if($div_area =='GA JAKARTA' OR $div_area=='GA SURABAYA'){
     $div='GA';
      $jenispermintaan='<option value="2">Retur Barang</option>';
      $tipe='<option value="2">Non Sistem</option>';
}else{
     $div = '';
}

?>

<div class="container1">
 
<br>
<span style="font-size:18px;"><b>* Tambah Barang</b></span>
<br><br><br>



 <div class="row">
<div class="col-sm-6">  

<form action="additemproses.php" method="POST">
  <div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 col-form-label">Jenis Permintaan</label>
    <div class="col-sm-5">
     <select name="jenis" id="jenis" class="form-control" required>
      <option value="">--Pilih Jenis--</option>
      <?php echo $jenispermintaan; ?>
     </select>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 col-form-label">Divisi Barang</label>
    <div class="col-sm-5">
      <input type="text" autocomplete="off" class="form-control" name="divisi" id="divisi" value="<?php echo $div; ?>" required readonly>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 col-form-label">Tipe</label>
    <div class="col-sm-5">
     <select name="tipe" id="tipe" class="form-control" required>
      <option value="">--Pilih Tipe--</option>
      <?php echo $tipe; ?>
     </select>
    </div>
  </div>
    <div class="form-group row">
    <label for="inputPassword3" class="col-sm-4 col-form-label">Kode Barang</label>
    <div class="col-sm-5">
      <!-- <input type="text" autocomplete="off" class="form-control" name="kode_barang" id="inputPassword3" placeholder="Kode Barang" required> -->
      <select class="items form-control" name="kode_barang" id="kode_barang"  style="width:220px" required></select>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputPassword3" class="col-sm-4 col-form-label">Nama Barang</label>
    <div class="col-sm-5">
      <input type="text" class="form-control"  autocomplete="off" name="nama_barang" id="nama_barang" placeholder="Nama Barang" required readonly>
    </div>
  </div>

    
 </div>

 <div class="col-sm-6">  

 <div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 col-form-label">Satuan</label>
    <div class="col-sm-5">
    <input type="text" autocomplete="off" class="form-control" name="satuan_barang" id="satuan_barang" placeholder="Satuan Barang" required readonly="">
     <!-- <select name="satuan_barang" id="satuan_barang" class="form-control" required readonly>
      <option value="">--Pilih Satuan--</option>
<?php
              $sqljenisbarang = "SELECT * FROM mst_req_type_item_uom order by req_type_name_item_uom asc ";
              $stmtjenisbarang = sqlsrv_query( $conn, $sqljenisbarang );
              if( $stmtjenisbarang === false) {
                  die( print_r( sqlsrv_errors(), true) );
              }

              while( $rowjenisbarang = sqlsrv_fetch_array( $stmtjenisbarang, SQLSRV_FETCH_ASSOC) ) {
                    echo "<option value=".$rowjenisbarang['req_type_name_item_uom']."> ".$rowjenisbarang['req_type_name_item_uom']."</option>";
              }

              ?>
              
         <option value="LMBR">LMBR</option>
     </select> -->
    </div>
  </div>
    <div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 col-form-label">Jenis Barang</label>
    <div class="col-sm-5">
     <select name="jenis_barang" id="jenis_barang" class="form-control" required>
      <option value="">--Pilih Satuan--</option>
         <?php
              $sqljenisbarang = "SELECT * FROM mst_req_type_item order by req_type_name_item asc";
              $stmtjenisbarang = sqlsrv_query( $conn, $sqljenisbarang );
              if( $stmtjenisbarang === false) {
                  die( print_r( sqlsrv_errors(), true) );
              }

              while( $rowjenisbarang = sqlsrv_fetch_array( $stmtjenisbarang, SQLSRV_FETCH_ASSOC) ) {
                    echo "<option value=".$rowjenisbarang['req_type_name_item']."> ".$rowjenisbarang['req_type_name_item']."</option>";
              }

              ?>

     </select>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 col-form-label">Kondisi</label>
    <div class="col-sm-5">
     <select name="kondisi_barang" id="kondisi_barang" class="form-control" required>
      <option value="">--Pilih Kondisi--</option>
      <option value="0">Good & Non Good</option>
      <option value="1">Good</option>
      <option value="2">Non Good</option>
     </select>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputEmail3" class="col-sm-4 col-form-label"></label>
    <div class="col-sm-5">
    <button type="submit" class="btn btn-primary"><b>Submit</b></button>
  </div>
  </div>
</form>
 
 <!-- <table id="datatable" class="table table-striped table-bordered nowrap">
    <thead>
                <tr>
                   <th>Jenis Permintaan</th>
                   <th>Divisi Barang</th>
                   <th>Tipe</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                   <th>Jenis Barang</th>
                    <th>Kondisi</th>
                    <th>Kadarluarsa</th>
                    <th>Status</th>
                    <th>Detail</th>
                </tr>
            </thead> 
    </table> -->

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

  $('#kode_barang').select2({
                            placeholder: 'Pilih Nama Barang',
                            // allowClear: true,
                            ajax: {
                              url: 'getitemsap.php',
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
                                            id: obj.id,
                                            text: obj.text,
                                            uom: obj.uom,
                                            itemname:obj.itemname
                                        };
                                     
                                        })
                                    }; 
                            },
                              cache: true
                            },
                       }).on('change', function (e) {
                        var data = $('#kode_barang').select2('data');
                        $('#kode_barang').val(data[0].id);
                        $('#nama_barang').val(data[0].itemname);
                        $('#satuan_barang').val(data[0].uom);
                        
                    });

 //Datatables Basic server side initilization
$(document).ready(function() {
                var dataTable = $('#datatable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ordering": true,
                    "responsive": true,
                    "ajax":{
                        url :"listdatamasteritem.php", // json datasource
                        type: "post",  // method  , by default get
                        error: function(){  // error handling
                            // $(".lookup-error").html("");
                            // $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                            // $("#lookup_processing").css("display","none");
                            
                        }
                    },
                    "aaSorting": [
                      [0, "desc"]
                    ],
                    "createdRow": function( row, data, dataIndex){
                        if( data[2] == 'Non System'){
                                $(row).css('background-color', '#FFF');
                            }
                            else{
                                $(row).css('background-color', '#F39B9B');
                            }
                        },
                        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                        var data = row.data();
                        return 'Detail '+data[0];
                    }
                } ),
                renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
                    tableClass: 'table'
                } )
            }
        }
                    });
     });


     $(document).on("click", ".open-AddBookDialog", function () {
     var myBookId = $(this).data('id');
     var code = $(this).data('code');
     var delivery = $(this).data('delivery');
     var store = $(this).data('store');
     var divisi = $(this).data('divisi');

     $("#myModalLabel").html("No Dokumen #"+ code);
     $(".modal-body #bookId").val( myBookId );
     $(".modal-body #bookcode").val( code );
     $(".modal-body #bookdelivery").val( delivery );
     $(".modal-body #bookstore").val( store );
     $(".modal-body #bookdivisi").val( divisi );
    //  $('#addBookDialog').modal('show');
});

$(document).on("click", ".addreturnproses", function () {
     var id_rtn = $("#bookId").val();
     var code = $("#bookcode").val();
     var delivery = $("#bookdelivery").val();
     var store = $("#bookstore").val();
     var divisi = $("#bookdivisi").val();
     var status = $("#status :selected").val();
     var note = $("#note").val();
    
     if(status ==""){
        Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Wajib Pilih Status!'
        });
        $("#status_proses").focus();
     }else{

      if(status =='Approved'){

       swal.fire({
            title: "Proses?",
            icon: 'question',
            text: "Yakin Ingin Proses Data Ini?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Ya, Proses!",
            cancelButtonText: "Tidak, Proses!",
            reverseButtons: !0
        }).then(function (e) {

            if (e.value === true) {

        $.ajax({
        url: "approvereturnproses.php",
        type: "post",
        data: {
            id:id_rtn,
            status:status,
            note:note,
            code:code,
            store:store,
            divisi:divisi,
            delivery:delivery
        },
        success: function (response) {
            swal.fire("Berhasil!", response, "success");
                                    // refresh page after 2 seconds
                                    setTimeout(function(){
                                        location.reload();
                                    },26000);
            $('#datatable').DataTable().ajax.reload();
            $('#addBookDialog').modal('hide');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });



    } else {
                e.dismiss;
            }

        }, function (dismiss) {
            return false;
        })

      }else{

       if(note ==""){
        Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Wajib Input Keterangan!'
        });
        $("#note").focus();
     }else{

        swal.fire({
            title: "Proses?",
            icon: 'question',
            text: "Yakin Ingin Proses Data Ini?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Ya, Proses!",
            cancelButtonText: "Tidak, Proses!",
            reverseButtons: !0
        }).then(function (e) {

            if (e.value === true) {

        $.ajax({
        url: "approvereturnproses.php",
        type: "post",
        data: {
         id:id_rtn,
            status:status,
            note:note,
            code:code,
            store:store,
            divisi:divisi,
            delivery:delivery
        },
        success: function (response) {
            swal.fire("Berhasil!", response, "success");
                                    // refresh page after 2 seconds
                                    setTimeout(function(){
                                        location.reload();
                                    },2000);
            $('#datatable').DataTable().ajax.reload();
            $('#addBookDialog').modal('hide');
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
        }
    });



    } else {
                e.dismiss;
            }

        }, function (dismiss) {
            return false;
        })

    }


      }


     }
   
});

</script>
</body>
</html>       

