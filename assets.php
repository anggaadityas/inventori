<?php
$halaman = "tps";
include "layouts/header.php";
include "layouts/navbar.php";
?>
<style>
    #itemTable input,
    #itemTable select,
    #itemTable textarea {
        width: 100%;
        /* Membuat input memenuhi sel */
        box-sizing: border-box;
        /* Agar padding tidak menambah ukuran */
    }

    #itemTable th,
    #itemTable td {
        vertical-align: middle;
        /* Pusatkan teks secara vertikal */
    }

    #itemTable .form-control {
        padding: 5px;
        /* Sesuaikan padding agar tidak terlalu besar */
    }
</style>
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

            <!-- Nav tabs -->
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#menu1"><b>Formulir Asset</b></a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">

                <div class="tab-pane container active" id="menu1">

                    <form id="myForm">
                        <br><br>
                        <fieldset>

                            <div class="form-group row">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Jenis Asset</label>
                                <div class="col-sm-3">
                                    <select name="jenis_permintaan" id="jenis_permintaan" class="form-control" required>
                                        <option value="">-- Pilih Jenis Asset --</option>
                                        <!-- <option value="1">Warehouse To Store</option> -->
                                        <!-- <option value="2">Store To Store</option> -->
                                        <option value="3">Store To Warehouse</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Jenis Prioritas </label>
                                <div class="col-sm-3">
                                    <select name="jenis_prioritas" id="jenis_prioritas" class="form-control" required>
                                        <option value="">-- Jenis Prioritas --</option>
                                        <?php
                                        if (date("H:i") <= '12:00') {
                                            ?>
                                            <option value="1">Normal</option>
                                            <option value="2">Darurat</option>
                                            <?php
                                        } else {
                                            ?>
                                            <option value="1">Normal</option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                                <div class="col-sm-3">
                                    <input type="text" class="form-control datepickers" name="tanggal_permintaan"
                                        id="tanggal_permintaan" placeholder="Pilh Tanggal Permintaan"
                                        value="<?php echo date('Y-m-d'); ?>" readonly required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan</label>
                                <div class="col-sm-3">
                                    <textarea name="keterangan" class="form-control" id="keterangan" cols="10" rows="5"
                                        maxlength="110"></textarea>
                                </div>
                            </div>
                        </fieldset>

                        <br><br>
                        <button type="button" class="btn btn-primary addmore1" id="openItemModal"><b>Tambah
                                Barang</b></button>
                        <br><br>


                        <table class="table table-striped table-bordered dt-responsive nowrap" id="itemTable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="width: 15%;">Nama Asset</th>
                                    <th style="width: 12%;">Kode Asset</th>
                                    <th style="width: 8%;">Satuan</th>
                                    <th style="width: 10%;">Stok Asset</th>
                                    <th style="width: 8%;">Jumlah</th>
                                    <th style="width: 12%;">Kondisi Asset</th>
                                    <th style="width: 10%;">Alasan</th>
                                    <th style="width: 15%;">Keterangan Asset</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>



                        <div align="left">
                            <br>
                            <button type="submit" class="btn btn-primary"><b>Proses Permintaan</b></button>
                            <br><br> <br>
                        </div>
                    </form>

                </div>


                <div class="tab-pane container fade" id="menu2">

                </div>



            </div>


        </div>

        <!-- Modal for Item Selection -->
        <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="itemModalLabel">Select Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table id="itemDataTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Warerhouse</th>
                                    <th>Item Code</th>
                                    <th>Item Name</th>
                                    <th>UOM</th>
                                    <th>AssetQuantity</th>
                                    <th>Asset Kondisi Ok (WH Verifikasi)</th>
                                    <th>Asset Kondisi Non Ok (WH Verifikasi)</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="addSelectedItems">Add Selected</button>
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
        <!-- <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script> -->
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.all.min.js"></script>
        <script src="js/jquery.inputmask.bundle.min.js" charset="utf-8"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>

            $(document).ready(function () {

                $("#jenis_prioritas").on('change', function () {
                    var prioritas = $("#jenis_prioritas :selected").val();
                    if (prioritas == 2) {
                        msg = '+1';
                        $('.datepickers').datepicker('destroy');
                        $('.datepickers').val('');
                        $('.datepickers').datepicker({
                            showOtherMonths: true,
                            selectOtherMonths: true,
                            minDate: msg,
                            dateFormat: "yy-mm-dd"
                        });
                    } else {
                        msg = '+2';
                        $('.datepickers').datepicker('destroy');
                        $('.datepickers').val('');
                        $('.datepickers').datepicker({
                            showOtherMonths: true,
                            selectOtherMonths: true,
                            minDate: msg,
                            dateFormat: "yy-mm-dd"
                        });
                    }
                });

                let selectedItems = {}; // Menyimpan item yang dipilih

                let itemTable = $('#itemDataTable').DataTable({
                    "ajax": "GetMasterAssets.php",
                    "columns": [
                        {
                            "data": null, "render": function (data) {
                                return `<input type='checkbox' class='item-checkbox'
                                 data-code='${data.ItemCode}'
                                 data-name='${data.ItemName}'
                                 data-uom='${data.ItemUom}'
                                 data-AssetQuantity='${data.AssetQuantity}'
                                 data-AssetConditionOk='${data.AssetConditionOk}'
                                 data-AssetConditionNonOk='${data.AssetConditionNonOk}'>`;
                            }
                        },
                        { "data": "Warehouse" },
                        { "data": "ItemCode" },
                        { "data": "ItemName" },
                        { "data": "ItemUom" },
                        { "data": "AssetQuantity" },
                        { "data": "AssetConditionOk" },
                        { "data": "AssetConditionNonOk" }
                    ]
                });


                $('#openItemModal').click(function () {
                    $('.item-checkbox').prop('checked', false);
                    selectedItems = {};
                    $('#itemModal').modal('show');
                });

                // Simpan item yang dipilih
                $('#itemDataTable tbody').on('change', '.item-checkbox', function () {
                    let itemCode = $(this).data('code');
                    let itemName = $(this).data('name');
                    let itemUom = $(this).data('uom');
                    let AssetQuantity = $(this).data('assetquantity');
                    let AssetConditionOk = $(this).data('assetconditionok');
                    let AssetConditionNonOk = $(this).data('assetconditionnonok');

                    if ($(this).is(':checked')) {
                        selectedItems[itemCode] = { itemCode, itemName, itemUom, AssetQuantity, AssetConditionOk, AssetConditionNonOk };
                    } else {
                        delete selectedItems[itemCode];
                    }
                });

                $('#jenis_permintaan').change(function () {
                    let jenisAsset = $(this).val().trim();
                    let itemTableRows = $('#itemTable tbody tr');

                    // Jika ada data di tabel, tampilkan konfirmasi SweetAlert
                    if (itemTableRows.length > 0) {
                        Swal.fire({
                            title: 'Konfirmasi Perubahan',
                            text: 'Mengubah jenis permintaan akan menghapus semua item di tabel. Lanjutkan?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $('#itemTable tbody').empty(); // Kosongkan tabel
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Tabel Dikosongkan!',
                                    text: 'Semua item di tabel telah dihapus.',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                // Kembalikan pilihan ke sebelumnya jika batal
                                $(this).val($(this).data('previous-value'));
                            }
                        });
                    } else {
                        // Kosongkan langsung jika tidak ada data di tabel
                        $('#itemTable tbody').empty();
                    }

                    // Simpan nilai terbaru sebagai previous value
                    $(this).data('previous-value', jenisAsset);
                });


                // Ketika klik tombol "Add Selected"
                $('#addSelectedItems').click(function () {
                    let jenisAsset = $('#jenis_permintaan').val().trim(); // Ambil jenis asset

                    if (!jenisAsset) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Jenis Asset Belum Dipilih!',
                            text: 'Silakan pilih jenis asset sebelum menambahkan item.',
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    $.each(selectedItems, function (_, item) {
                        let maxQty = parseInt(item.AssetQuantity);
                        let existingRows = $(`#itemTable tbody tr[data-code="${item.itemCode}"]`);
                        let totalExistingQty = 0;
                        let lastGoodRow = null;
                        let lastDamagedRow = null;

                        // Cek apakah kondisi "Bagus" dan "Rusak" sudah ada
                        existingRows.each(function () {
                            let kondisi = $(this).find("select[name='kondisiAsset[]']").val();
                            let qtyInput = $(this).find("input[name='quantity[]']");
                            totalExistingQty += parseInt(qtyInput.val()) || 0;

                            if (kondisi == "1") {
                                lastGoodRow = qtyInput;
                            } else if (kondisi == "2") {
                                lastDamagedRow = qtyInput;
                            }
                        });

                        if (totalExistingQty >= maxQty) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Stok Habis!',
                                text: `Total quantity untuk asset ini sudah mencapai maksimum (${maxQty}).`,
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        let remainingQty = maxQty - totalExistingQty;

                        if (lastGoodRow && lastDamagedRow) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Kondisi Sudah Ada!',
                                text: 'Kondisi "Bagus" dan "Rusak" sudah ada, tidak dapat menambahkan baris baru. Kuantitas akan diperbarui.',
                                confirmButtonText: 'OK'
                            });

                            let additionalQty = Math.min(remainingQty, 1); // Tambahkan 1 atau sisa kuantitas yang tersedia
                            lastDamagedRow.val(parseInt(lastDamagedRow.val()) + additionalQty);
                            return;
                        }

                        let defaultKondisi = lastGoodRow ? "2" : "1"; // Jika "Bagus" sudah ada, tambahkan sebagai "Rusak"
                        let kondisiOptions = `
            <option value="1" ${defaultKondisi === '1' ? 'selected' : ''}>Bagus</option>
            <option value="2" ${defaultKondisi === '2' ? 'selected' : ''}>Rusak</option>
        `;

                        let row = `<tr data-code="${item.itemCode}">
            <td class="row-number">
            </td> 
            <td>
            <input type="hidden" name="AssetConditionOk[]" class="form-control" value="${item.AssetConditionOk}" readonly>
             <input type="hidden" name="AssetConditionNonOk[]" class="form-control" value="${item.AssetConditionNonOk}" readonly>
            <input type="text" name="itemName[]" class="form-control" value="${item.itemName}" readonly>
            </td>
            <td><input type="text" name="itemCode[]" class="form-control" value="${item.itemCode}" readonly></td>
            <td><input type="text" name="itemUom[]" class="form-control" value="${item.itemUom}" readonly></td>
            <td><input type="number" name="AssetQuantity[]" class="form-control" value="${maxQty}" readonly></td>
            <td><input type="number" name="quantity[]" class="form-control quantity-input" value="1" min="1"></td>
            <td>
                <select name="kondisiAsset[]" class="form-control kondisi-asset">
                    ${kondisiOptions}
                </select>
            </td>
            <td>
                <select name="alasan[]" class="form-control select-alasan"></select>
            </td>
            <td><textarea name="keteranganAsset[]" class="form-control" rows="3"></textarea></td>
            <td><button type="button" class="btn btn-danger removeItem">Hapus</button></td>
        </tr>`;

                        $('#itemTable tbody').append(row);

                        $('.quantity-input').last().on('input', function () {
                            let qty = parseInt($(this).val());
                            let currentTotalQty = 0;

                            $(`#itemTable tbody tr[data-code="${item.itemCode}"]`).each(function () {
                                currentTotalQty += parseInt($(this).find("input[name='quantity[]']").val()) || 0;
                            });

                            if (currentTotalQty > maxQty) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Jumlah Melebihi Batas!',
                                    text: `Total quantity tidak boleh lebih besar dari AssetQuantity (${maxQty}).`,
                                    confirmButtonText: 'OK'
                                });
                                $(this).val(1);
                            }
                        });

                        $('.select-alasan').last().select2({
                            placeholder: 'Pilih Alasan',
                            allowClear: true,
                            ajax: {
                                url: 'GetMasterAssetReasons.php',
                                dataType: 'json',
                                delay: 250,
                                data: function (params) {
                                    return { q: params.term, jenis_asset: jenisAsset };
                                },
                                processResults: function (data) {
                                    return {
                                        results: data.results // Sesuai format dari PHP
                                    };
                                },
                                cache: true
                            }
                        });
                    });

                    $('#itemModal').modal('hide');
                    selectedItems = {};
                    updateRowNumbers();
                });

                // Fungsi untuk memperbarui nomor urut setelah penghapusan item
                function updateRowNumbers() {
                    $('#itemTable tbody tr').each(function (index) {
                        $(this).find('.row-number').text(index + 1); // Set nomor urut mulai dari 1
                        console.log("Updated row:", index + 1);
                    });
                }



                $(document).on('click', '.removeItem', function () {
                    let row = $(this).closest('tr');
                    Swal.fire({
                        title: "Konfirmasi",
                        text: "Apakah Anda yakin ingin menghapus item ini?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let itemCode = $(this).closest('tr').data('code');
                            delete selectedItems[itemCode];
                            $(this).closest('tr').remove();
                            updateRowNumbers();
                        }
                    });
                });

            });


            function validateForm() {
                let isValid = true;
                let errorMessage = "";

                // Cek semua input yang wajib diisi (kecuali textarea "Keterangan")
                $("#myForm input:not([type=hidden]), #myForm select").each(function () {
                    if ($(this).prop("required") && $(this).val().trim() === "") {
                        isValid = false;
                        errorMessage = "Semua field harus diisi sebelum mengajukan permintaan.";
                        return false; // Stop loop
                    }
                });

                // Cek apakah tabel memiliki setidaknya satu baris data
                if ($("#itemTable tbody tr").length === 0) {
                    isValid = false;
                    errorMessage = "Harap tambahkan setidaknya satu barang dalam tabel.";
                }

                // Jika ada kesalahan, tampilkan SweetAlert
                if (!isValid) {
                    Swal.fire("Validasi Gagal!", errorMessage, "error");
                }

                return isValid;
            }

            // Modifikasi event submit dengan validasi tambahan
            $(document).on("submit", "#myForm", function (e) {
                e.preventDefault();

                if (!validateForm()) {
                    return;
                }

                Swal.fire({
                    title: "Processing...",
                    text: "Please wait",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                $.ajax({
                    url: "assetsproses.php",
                    type: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (response) {
                        Swal.fire(response.status === "success" ? "Success" : "Error", response.message, response.status);
                        if (response.status === "success") {
                            $("#myForm")[0].reset();
                            $("#itemTable tbody").empty();
                        }
                    },
                });
            });


        </script>
        </body>

        </html>