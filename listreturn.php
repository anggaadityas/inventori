<?php
$halaman = "listreturn";
include "layouts/header.php";
include "layouts/navbar.php";
?>

<div class="container1">
  <div class="row">

    <div class="col-sm-12" style="margin-top: 26px;">

      <?php

      // menampilkan pesan jika ada pesan
      if (isset($_SESSION['pesan']) && $_SESSION['pesan'] <> '') {
        echo '<div class="alert alert-warning alert-dismissible fade show col-sm-5" role="alert">
  <strong>Info!</strong> ' . $_SESSION['pesan'] . '
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

          <div class="form-group" style=margin-left:-20px;>
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

          <div class="form-group" style=margin-left:-20px;>
            <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Jenis Sistem :</b> </label>
            <div class="col-sm-12">
              <select id='searchByJenisSistem'>
                <option value=''>-- Select Status --</option>
                <option value='1'>Sistem</option>
                <option value='2'>Non Sistem</option>
                <option value='3'>Wadah</option>
                <option value='4'>NCR</option>
                <option value='5'>DAMAGE</option>
              </select>
            </div>
          </div>


        </div>

        <div class="col-sm-4">

          <div class="form-group" style=margin-left:-20px;>
            <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Tanggal Pengiriman :</b> </label>
            <div class="col-sm-12">
              <input type="text" name="store" placeholder="Awal (YYYY-MM-DD)" id="startdate">
              <input type="text" name="store" placeholder="Akhir (YYYY-MM-DD)" id="enddate">
            </div>
          </div>


          <div class="form-group" style=margin-left:-20px;>
            <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Inisial Store :</b> </label>
            <div class="col-sm-6">
              <input type="text" name="store" placeholder="Input Inisial Store" id="store">
            </div>
          </div>

        </div>

        <div class="col-sm-2">

          <div class="form-group" style=margin-left:-20px;>
            <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Status Dokumen :</b> </label>
            <div class="col-sm-12">
              <select id='searchByStatusDokumen'>
                <option value=''>-- Select Status --</option>
                <option value='1'>Belum Selesai</option>
                <option value='2'>Selesai</option>
                <option value='3'>Reject</option>
              </select>
            </div>
          </div>


        </div>

      </div>

      
      <?php
      if ($_SESSION["area_div"]  == 'STORE') {
      ?>
        <br>
        <a href="revisiitem.php" class="btn btn-warning"><b>Revisi Retur Barang</b></a>
        <br>
      <?php
      }
      ?>

      <br>

      <table id="datatable" class="table table-striped table-bordered nowrap">
        <thead>
          <tr>
            <th style="width:10px;">Nomor Dokumen</th>
            <th>Tanggal Pengiriman</th>
            <th>Tenggat Waktu</th>
            <th>Jenis Permintaan</th>
            <th>Jenis Sistem</th>
            <th>Jenis Prioritas</th>
            <th>Toko </th>
            <th>Divisi Tujuan</th>
            <!-- <th>Alasan Permintaan</th> -->
            <th>Keterangan Permintaan</th>
            <th>Kode SAP</th>
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
              <label for="inputEmail3" class="col-sm-3 col-form-label"> No Dokumen SAP</label>
              <div class="col-sm-6">
                <input type="hidden" class="form-control" name="bookId" id="bookId" />
                <input type="text" class="form-control" name="kodesap" id="kodesap" placeholder="Input Kode SAP" autocomplete="off" />
              </div>
            </div>
            <div class="form-group row" style="margin-top: 10px;">
              <label for="inputEmail3" class="col-sm-3 col-form-label"> Tanggal Posting Dokumen SAP</label>
              <div class="col-sm-6">
                <input type="text" class="form-control" placeholder="Tanggal Posting Dokumen SAP" name="date_posting" id="date_posting" autocomplete="off" readonly />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            <button type="button" class="btn btn-primary addkodesap">Yes</button>
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
      //Datatables Basic server side initilization
      $(document).ready(function() {
        var dataTable = $('#datatable').DataTable({
          "processing": true,
          "serverSide": true,
          "ordering": true,
          'serverMethod': 'post',
          "ajax": {
            url: "listdatareturn.php", // json datasource
            type: "post", // method  , by default get
            error: function() { // error handling
              // $(".lookup-error").html("");
              // $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
              // $("#lookup_processing").css("display","none");

            },
            data: function(data) {
              // Read values
              var JenisPrioritas = $('#searchByJenisPrioritas').val();
              var JenisSistem = $('#searchByJenisSistem').val();
              var Store = $('#store').val();
              var Startdate = $('#startdate').val();
              var Enddate = $('#enddate').val();
              var StatusDokumen = $('#searchByStatusDokumen').val();
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
          "createdRow": function(row, data, dataIndex) {
            if (data[10] == '<span class="badge badge-success">Selesai</span>') {
              $(row).css('background-color', '#FFF');
            } else {
              $(row).css('background-color', '#F39B9B');
            }
          },
          responsive: {
            details: {
              display: $.fn.dataTable.Responsive.display.modal({
                header: function(row) {
                  var data = row.data();
                  return 'Detail ' + data[0];
                }
              }),
              renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                tableClass: 'table'
              })
            }
          }
        });
        $('#searchByJenisPrioritas, #searchByJenisSistem, #searchByStatusDokumen').change(function() {
          dataTable.draw();
          console.log('ok');
        });

        $('#store, #startdate, #enddate').keyup(function() {
          dataTable.draw();
          console.log('ok');
        });
      });

      $(document).on("click", ".open-AddBookDialog", function() {
        var myBookId = $(this).data('id');
        var code = $(this).data('code');
        var sap = $(this).data('sap');

        $("#myModalLabel").html("No Dokumen #" + code);
        $(".modal-body #bookId").val(myBookId);
        $(".modal-body #kodesap").val(sap);
        //  $('#addBookDialog').modal('show');
      });

      $(document).on("click", ".addkodesap", function() {
        var id_rtn = $("#bookId").val();
        var kodesap = $("#kodesap").val();
        var date_posting = $("#date_posting").val();

        if (kodesap == "") {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Wajib Input Kode SAP!'
          });
          $("#kodesap").focus();
        } else if (date_posting == "") {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Wajib Input Tanggal Posting SAP!'
          });
          $("#date_posting").focus();
        } else {

          swal.fire({
            title: "Proses?",
            icon: 'question',
            text: "Yakin Ingin Proses Data Ini?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Ya, Proses!",
            cancelButtonText: "Tidak, Proses!",
            reverseButtons: !0
          }).then(function(e) {

            if (e.value === true) {

              $.ajax({
                url: "inputkodesaprtn.php",
                type: "post",
                data: {
                  id: id_rtn,
                  kodesap: kodesap,
                  date_posting: date_posting
                },
                success: function(response) {
                  swal.fire("Berhasil!", response, "success");
                  // refresh page after 2 seconds
                  setTimeout(function() {
                    location.reload();
                  }, 2000);
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

          }, function(dismiss) {
            return false;
          })

        }

      });

      $(function() {
        $("body").delegate("#date_posting", "focusin", function() {
          $(this).datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: "yy-mm-dd",
            maxDate: 0,
            onSelect: function(selectedDate) {}
          });
        });
      });
    </script>
    </body>

    </html>