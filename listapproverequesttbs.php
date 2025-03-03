<?php
$halaman="listapproverequest";
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


<div class="row">

  <div class="col-sm-2">

    <div class="form-group"style=margin-left:-20px;>
    <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Store Penerima :</b> </label>
    <div class="col-sm-6">
    <input type="text" name="store" placeholder="Input Store Penerima" id="storepenerima">
  </div>
</div>

    <div class="form-group"style=margin-left:-20px;>
    <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Store Pengirim :</b> </label>
    <div class="col-sm-6">
    <input type="text" name="store" placeholder="Input Store Pengirim" id="storepengirim">
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
    <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Status Dokumen :</b> </label>
    <div class="col-sm-12">
    <select id='searchByStatusDokumen'>
           <option value=''>-- Select Status --</option>
           <option value='1'>Belum Selesai</option>
           <option value='2'>Selesai</option>
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
                   <!-- <th>Jenis Barang</th> -->
                   <th>Toko Penerima</th>
                    <th>Toko Pengirim</th>
                    <!-- <th>Alasan Permintaan</th> -->
                    <th>Keterangan Permintaan</th>
                    <th>Status</th>
                    <th>Detail Permintaan</th>
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
                        url :"listdataapproverequesttbs.php", // json datasource
                        type: "post",  // method  , by default get
                        error: function(){  // error handling
                            // $(".lookup-error").html("");
                            // $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                            // $("#lookup_processing").css("display","none");
                            
                        }, data: function(data){
                      // Read values
                      var StorePenerima = $('#storepenerima').val();
                      var StorePengirim = $('#storepengirim').val();
                       var Startdate = $('#startdate').val();
                        var Enddate = $('#enddate').val(); 
                        var StatusDokumen = $('#searchByStatusDokumen').val();
                      // Append to data
                       data.searchByStorePenerima= StorePenerima;
                       data.searchByStorePengirim= StorePengirim;
                       data.searchByStartdate = Startdate;
                       data.searchByEnddate = Enddate;
                        data.searchByStatusDokumen = StatusDokumen;
                        }
                    },
                    "aaSorting": [
                      [0, "desc"]
                    ],
                    "createdRow": function( row, data, dataIndex){
                        if( data[6] == '<span class="badge badge-success">Selesai</span>'){
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

                
                $('#searchByStatusDokumen').change(function(){
                        dataTable.draw();
                        console.log('ok');
                      });

                      $('#storepenerima, #storepengirim, #startdate, #enddate').keyup(function(){
                        dataTable.draw();
                        console.log('ok');
                      });
     });

</script>
</body>
</html>       