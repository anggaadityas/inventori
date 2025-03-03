<?php
$halaman="listapprovereturn";
include "layouts/header.php";
include "layouts/navbar.php";
?>

<div class="container1">
 <div class="row">

<div class="col-sm-12" style="margin-top: 26px;" >     

<div class="row">

  <div class="col-sm-2">

<div class="form-group"style=margin-left:-20px;>
    <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Jenis Prioritas :</b> </label>
    <div class="col-sm-12">
    <select id='searchByJenisPrioritas'>
           <option value=''>-- Select Status --</option>
           <option value='1'>Normal</option>
           <option value='2'>Darurat</option>
           <option value='3'>Hari H</option>
         </select>
    </div>
  </div>

  <div class="form-group"style=margin-left:-20px;>
    <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Jenis Sistem :</b> </label>
    <div class="col-sm-12">
    <select id='searchByJenisSistem' >
           <option value=''>-- Select Status --</option>
           <option value='1'>Sistem</option>
           <option value='2'>Non Sistem</option>
           <option value='3'>Wadah</option>
           <option value='4'>NCR</option>
         </select>
    </div>
  </div>


</div>

  <div class="col-sm-4">

    <div class="form-group"style=margin-left:-20px;>
    <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Tanggal Pengiriman :</b> </label>
    <div class="col-sm-12">
    <input type="text" name="store" placeholder="Awal (YYYY-MM-DD)" id="startdate">
      <input type="text" name="store" placeholder="Akhir (YYYY-MM-DD)" id="enddate">
  </div>
</div>


    <div class="form-group"style=margin-left:-20px;>
    <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Inisial Store :</b> </label>
    <div class="col-sm-6">
    <input type="text" name="store" placeholder="Input Inisial Store" id="store">
  </div>
</div>


  <div class="form-group"style=margin-left:-20px;>
    <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Status Dokumen :</b> </label>
    <div class="col-sm-12">
    <select id='searchByStatusDokumen' >
           <option value=''>-- Select Status --</option>
           <option value='1'>Belum Dilakukan Persetujuan</option>
           <option value='2'>Sudah Dilakukan Persetujuan</option>
         </select>
    </div>
  </div>

  </div>




</div>

  <br>               

    <table id="datatable" class="table table-striped table-bordered nowrap">
    <thead>
                <tr>
                   <th style="width:10px;">Nomor Dokumen</th>
                   <th>Tanggal Pengiriman</th>
                   <th>Jenis Permintaan</th>
                   <th>Jenis Sistem</th>
                   <th>Jenis Prioritas</th>
                    <th>Toko Asal</th>
                    <th>Divisi Tujuan</th>
                    <th>Keterangan Permintaan</th>
                    <th>Status</th>
                    <th>Detail Permintaan</th>
                </tr>
            </thead> 
    </table>

    
 </div>

 <div class="modal fade" id="addBookDialog" tabindex="-1" role="dialog" aria-labelledby="my_modalLabel">
<div class="modal-dialog" role="dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">No Dokumen</h4>
        </div>
        

        <div class="modal-body">
  <div class="form-group row" style="margin-top: 10px;">
    <label for="inputEmail3" class="col-sm-3 col-form-label">Status</label>
    <div class="col-sm-6">
    <input type="hidden" class="form-control" name="bookId" id="bookId"/>
    <input type="hidden" class="form-control" name="bookcode" id="bookcode"/>
    <input type="hidden" class="form-control" name="bookdelivery" id="bookdelivery"/>
    <input type="hidden" class="form-control" name="bookstore" id="bookstore"/>
    <input type="hidden" class="form-control" name="bookdivisi" id="bookdivisi"/>
   <select name="status" id="status" class="form-control">
    <option value="">-- Pilih Status --</option>
    <option value="Approved">Disetujui</option>
    <option value="Reject">Ditolak</option>
   </select>
    </div>
     </div>

    <div class="form-group row" style="margin-top: 10px;">
    <label for="inputEmail3" class="col-sm-3 col-form-label"></label>
        <div class="form-check"><input type="checkbox" name="rev_question" id="rev_question" value="0" class="form-check-input rev_question"> 
   <span style="font-size: 10px;"><b>Ingin Melakukan Perubahan Tanggal Pengiriman?</b></span>
   </div>
   </div>

    <div class="form-group row revisi" style="margin-top: 10px;">
    <label for="inputEmail3" class="col-sm-3 col-form-label">Perubahan Tanggal Pengiriman</label>
    <div class="col-sm-6">
   <input type="text" name="rev_date_req" id="rev_date_req" class="form-control rev_date_req">
   <span style="font-size: 10px;" class="pastdatedel"></span>
  </div>
   </div>

  <div class="form-group row" style="margin-top: 10px;">
    <label for="inputEmail3" class="col-sm-3 col-form-label"> Keterangan</label>
    <div class="col-sm-6">
   <textarea name="note" id="note" cols="30" rows="8" class="form-control"></textarea>
  </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-primary addreturnproses">Yes</button>
        </div>

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
 <script src="js/jquery-ui.js"></script>
 <script src="js/sweetalert2.all.min.js"></script>
<script>

$(".revisi").hide();
$(".rev_question").click(function(){
    if($(this).is(":checked")){
        var bookdelivery = $('#bookdelivery').val();
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

//Datatables Basic server side initilization
$(document).ready(function() {
                var dataTable = $('#datatable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ordering": true,
                    "responsive": true,
                      'serverMethod': 'post',
                    "ajax":{
                        url :"listdataapprovereturn.php", // json datasource
                        type: "post",  // method  , by default get
                        error: function(){  // error handling
                            // $(".lookup-error").html("");
                            // $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                            // $("#lookup_processing").css("display","none");
                            
                        },
                      data: function(data){
                      // Read values
                      var JenisPrioritas = $('#searchByJenisPrioritas').val();
                      var JenisSistem = $('#searchByJenisSistem').val();
                      var Store = $('#store').val();
                       var Startdate = $('#startdate').val();
                        var Enddate = $('#enddate').val();  
                          var StatusDokumen= $('#searchByStatusDokumen').val();
                      // Append to data
                      data.searchByJenisPrioritas = JenisPrioritas;
                      data.searchByJenisSistem = JenisSistem;
                       data.searchByStore = Store;
                       data.searchByStartdate = Startdate;
                       data.searchByEnddate = Enddate;
                        data.searchByStatusDokumen = StatusDokumen;
                        }
                    },
                    "aaSorting": [
                      [0, "desc"]
                    ],
                    "createdRow": function( row, data, dataIndex){
                        if( data[8] == '<span class="badge badge-success">Selesai</span>'){
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

                  $('#searchByJenisPrioritas, #searchByJenisSistem, #searchByStatusDokumen').change(function(){
                        dataTable.draw();
                        console.log('ok');
                      });

                      $('#store, #startdate, #enddate').keyup(function(){
                        dataTable.draw();
                        console.log('ok');
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
     var rev_date_req = $("#rev_date_req").val(); 
     var rev_question = $("#rev_question").val();
     // alert(rev_question);
    
     if(status ==""){
        Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Wajib Pilih Status!'
        });
        $("#status_proses").focus();
     }else if(rev_date_req == delivery ){
        Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Perubahan Tanggal sama dengan tanggal sebelumnya, tidak ada perubahan!'
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
            rev_date_req:rev_date_req,
            rev_question:rev_question,
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

