<?php
$halaman = "listrequestassets";
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
                        <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Jenis Prioritas :</b>
                        </label>
                        <div class="col-sm-12">
                            <select id='searchByJenisPrioritas'>
                                <option value=''>-- Select Status --</option>
                                <option value='1'>Normal</option>
                                <option value='2'>Darurat</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style=margin-left:-20px;>
                        <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Jenis Terms :</b> </label>
                        <div class="col-sm-12">
                            <select id='searchByJenisSistem'>
                                <option value=''>-- Select Status --</option>
                                <option value='Transfer Antar Store'>Transfer Antar Store</option>
                                <option value='Retur'>Retur</option>
                                <option value='Store Closing'>Store Closing</option>
                            </select>
                        </div>
                    </div>


                </div>

                <div class="col-sm-4">

                    <div class="form-group" style=margin-left:-20px;>
                        <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Tanggal Pengiriman :</b>
                        </label>
                        <div class="col-sm-12">
                            <input type="text" name="store" placeholder="Awal (YYYY-MM-DD)" id="startdate">
                            <input type="text" name="store" placeholder="Akhir (YYYY-MM-DD)" id="enddate">
                        </div>
                    </div>


                    <div class="form-group" style=margin-left:-20px;>
                        <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Inisial Store :</b>
                        </label>
                        <div class="col-sm-6">
                            <input type="text" name="store" placeholder="Input Inisial Store" id="store">
                        </div>
                    </div>

                </div>

                <div class="col-sm-2">

                    <div class="form-group" style=margin-left:-20px;>
                        <label for="staticEmail" class="col-sm-12 col-form-label"><b>Fillter Status Dokumen :</b>
                        </label>
                        <div class="col-sm-12">
                            <select id='searchByStatusDokumen'>
                                <option value=''>-- Select Status --</option>
                                <option value='Open'>Open</option>
                                <option value='Close'>Close</option>
                                <option value='Parsial Received'>Parsial Received</option>
                                <option value='Not Received'>Not Received</option>
                                <option value='Reject'>Reject</option>
                            </select>
                        </div>
                    </div>


                </div>

            </div>

            <br>

            <table id="datatable" class="table table-striped table-bordered nowrap" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width:10px;">Nomor Dokumen</th>
                        <th>Tanggal Pengiriman</th>
                        <th>WarehouseFrom</th>
                        <th>WarehouseTo</th>
                        <th>Jenis Permintaan</th>
                        <th>Jenis Sistem</th>
                        <th>Jenis Prioritas</th>
                        <th>Keterangan Permintaan</th>
                        <th>Keterangan IAC</th>
                        <th>Status</th>
                        <th>Detail Permintaan</th>
                    </tr>
                </thead>
            </table>


        </div>

        <!-- Modal -->

        <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="my_modalLabel">
            <div class="modal-dialog" role="dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Input Remarks <span id="code"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="approval_id">
                        <textarea id="approval_remarks" class="form-control" rows="3"
                            placeholder="Tulis remarks..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                        <button type="button" class="btn btn-primary" id="submitApproval">Yes</button>
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
            $(document).ready(function () {


                $(document).on('click', '.open-modal', function () {
                    var id = $(this).data('id');
                    var code = $(this).data('code');
                    var remarks = $(this).data('remarks');
                    $('#approval_id').val(id);
                    $('#code').text(code);
                    $('#approval_remarks').val(remarks);
                    $('#approvalModal').modal('show');
                });

                // Submit
                $('#submitApproval').click(function () {
                    var id = $('#approval_id').val();
                    var remarks = $('#approval_remarks').val();

                    if (remarks.trim() === '') {
                        Swal.fire('Warning', 'Remarks tidak boleh kosong!', 'warning');
                        return;
                    }

                    $.ajax({
                        url: 'updateInputRemarks.php',
                        type: 'POST',
                        data: { id: id, remarks: remarks },
                        success: function (response) {
                            $('#approvalModal').modal('hide');
                            Swal.fire('Sukses', 'Approval remarks berhasil disimpan!', 'success');
                            $('#datatable').DataTable().ajax.reload();
                        },
                        error: function () {
                            Swal.fire('Error', 'Terjadi kesalahan saat mengirim data!', 'error');
                        }
                    });
                });

                var dataTable = $('#datatable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ordering": true,
                    'serverMethod': 'post',
                    "ajax": {
                        url: "listdataassets.php", // json datasource
                        type: "post", // method  , by default get
                        error: function () { // error handling
                            // $(".lookup-error").html("");
                            // $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                            // $("#lookup_processing").css("display","none");

                        },
                        data: function (data) {
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
                    "createdRow": function (row, data, dataIndex) {
                        if (data[9] == 'Close' || data[9] == 'Cancel' || data[9] == 'Close ()  - Not Received') {
                            $(row).css('background-color', '#FFF');
                        }else if(data[9] == 'Reject (AM)  - Cancel' || data[9] == 'Reject (RM)  - Cancel'){
                            $(row).css('background-color', '#b0a39d'); 
                        }else {
                            $(row).css('background-color', '#F39B9B');
                        }
                    },
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function (row) {
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
                $('#searchByJenisPrioritas, #searchByJenisSistem, #searchByStatusDokumen').change(function () {
                    dataTable.draw();
                    console.log('ok');
                });

                $('#store, #startdate, #enddate').keyup(function () {
                    dataTable.draw();
                    console.log('ok');
                });
            });

            $(document).on("click", ".open-AddBookDialog", function () {
                var myBookId = $(this).data('id');
                var code = $(this).data('code');
                var sap = $(this).data('sap');

                $("#myModalLabel").html("No Dokumen #" + code);
                $(".modal-body #bookId").val(myBookId);
                $(".modal-body #kodesap").val(sap);
                //  $('#addBookDialog').modal('show');
            });

            $(document).on("click", ".addkodesap", function () {
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
                    }).then(function (e) {

                        if (e.value === true) {

                            $.ajax({
                                url: "inputkodesaprtn.php",
                                type: "post",
                                data: {
                                    id: id_rtn,
                                    kodesap: kodesap,
                                    date_posting: date_posting
                                },
                                success: function (response) {
                                    swal.fire("Berhasil!", response, "success");
                                    // refresh page after 2 seconds
                                    setTimeout(function () {
                                        location.reload();
                                    }, 2000);
                                    $('#datatable').DataTable().ajax.reload();
                                    $('#addBookDialog').modal('hide');
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
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

            });

            $(function () {
                $("body").delegate("#date_posting", "focusin", function () {
                    $(this).datepicker({
                        showOtherMonths: true,
                        selectOtherMonths: true,
                        dateFormat: "yy-mm-dd",
                        maxDate: 0,
                        onSelect: function (selectedDate) { }
                    });
                });
            });
        </script>
        </body>

        </html>