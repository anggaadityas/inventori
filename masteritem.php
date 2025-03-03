<?php
$halaman="masteritem";
include "layouts/header.php";
include "layouts/navbar.php";
?>

<div class="container1">
 <div class="row">



<div class="col-sm-12" style="margin-top: 26px;" >           

<a class="btn btn-primary" href="additem.php"><b>Tambah Barang</b></a>
</br></br>
 
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

    <table id="datatable" class="table table-striped table-bordered nowrap">
    <thead>
                <tr>
                   <th>Jenis</th>
                   <th>Divisi</th>
                   <th>Tipe</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                   <th>Kategori Barang</th>
                    <th>Kondisi</th>
                    <th>Kadarluarsa</th>
                    <th>Status</th>
                    <th>Detail</th>
                </tr>
            </thead> 
    </table>

    
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

