<?php
include "layouts/header.php";
// error_reporting(0);
$id = $_GET['id'];
$area_div=$_SESSION['area_div'];
$sqlheader = "SELECT a.ID,
        a.DocNum,
		convert(char(10),a.DocDate,126) DocDate,
        a.WarehouseFrom,
        a.WarehouseTo,
        b.TransName,
        a.TermsAsset,
         CASE
    WHEN a.DocPriority=1 THEN 'Normal'
    WHEN a.DocPriority=2 THEN 'Darurat'
    ELSE ''
	END as DocPriority,
        a.Remarks,
        a.ApprovalUser,
        a.ApprovalProgress,
		a.ApprovalStatus,
		a.StatusDoc,
        convert(char(20),a.CreatedDate,120) date_submit 
        FROM InventoriAssetHeader a
        inner join MasterDocTrans b on a.DocTrans=b.ID
        where a.ID='$id'";
$stmtheader = sqlsrv_query($conn, $sqlheader);
if ($stmtheader === false) {
    die(print_r(sqlsrv_errors(), true));
}
$name = $_SESSION['nama'];
$rowheader = sqlsrv_fetch_array($stmtheader, SQLSRV_FETCH_ASSOC);
include "layouts/navbar.php";
?>

<div class="container1">
    <div class="row">

        <div class="col-sm-12" style="margin-top: 26px;">
            <span style="font-size:18px;"><b>* View Approval Pengajuan Inventaris Asset #
                    <?php echo $rowheader['DocNum']; ?></b></span>
            <br><br><br>

            <form method="POST" action="approvalassetsproses.php" id="sender_container">
                <fieldset>
                    <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                        <div class="col-sm-3">
                            <input type="hidden" class="form-control" id="DocNum" name="DocNum"
                                value="<?php echo $rowheader['DocNum']; ?>" readonly>
                            <input type="hidden" class="form-control" id="TransName" name="TransName"
                                value="<?php echo $rowheader['TransName']; ?>" readonly>
                            <input type="hidden" class="form-control" id="TransID" name="TransID"
                                value="<?php echo $rowheader['ID']; ?>" readonly>
                            <input type="text" class="form-control" id="DocDate" name="DocDate"
                                value="<?php echo $rowheader['DocDate']; ?>" readonly>
                            <input type="hidden" class="form-control" id="ApprovalProgress" name="ApprovalProgress"
                                value="<?php echo $rowheader['ApprovalProgress']; ?>" readonly>
                            <input type="hidden" class="form-control" id="ApprovalStatus" name="ApprovalStatus"
                                value="<?php echo $rowheader['ApprovalStatus']; ?>" readonly>
                        </div>
                    </div>

                    <?php
                    if ($_SESSION["nama"] == 'WH DISTRIBUSI JKT' || $_SESSION["nama"] == 'WH DISTRIBUSI SBY') {
                        ?>
                        <div class="form-group row" style="margin-top: 10px;">
                            <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                            <div class="form-check"><input type="checkbox" name="rev_question" id="rev_question" value="0"
                                    class="form-check-input rev_question">
                                <span style="font-size: 10px;"><b>Ingin Melakukan Perubahan Tanggal Pengiriman?</b></span>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="form-group row revisi" style="margin-top: 10px;">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Perubahan Tanggal Pengiriman</label>
                        <div class="col-sm-3">
                            <input type="text" name="rev_date_req" id="rev_date_req" class="form-control rev_date_req"
                                autocomplete="off">
                            <span style="font-size: 10px;" class="pastdatedel"></span>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Submit Form Retur</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="inputEmail"
                                value="<?php echo $rowheader['date_submit']; ?>" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">Jenis Permintaan</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="TransName" name="TransName"
                                value="<?php echo $rowheader['TransName']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">WarehouseFrom</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="WarehouseFrom" name="WarehouseFrom"
                                value="<?php echo $rowheader['WarehouseFrom']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">WarehouseTo</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="WarehouseTo" name="WarehouseTo"
                                value="<?php echo $rowheader['WarehouseTo']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">TermsAsset</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="TermsAsset" name="TermsAsset"
                                value="<?php echo $rowheader['TermsAsset']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan Permintaan</label>
                        <div class="col-sm-3">
                            <textarea name="note_request" class="form-control" name="Remarks" id="Remarks" cols="10"
                                rows="5" readonly><?php echo $rowheader['Remarks']; ?>
               </textarea>
                        </div>
                    </div>
                </fieldset>

                <br>

                <?php
                if ($rowheader['ApprovalStatus'] == 'Menunggu Verifikasi Warehouse') {
                    $warehouseto = "AND WarehouseTo LIKE  '" . $name . "%' AND StatusApprovalAM=1 AND StatusApprovalDistribusi=1";
                } else {
                    $warehouseto = '';
                }

                if ($area_div == 'AM') {
                    $am = '';
                } else {
                    $am = 'AND StatusApprovalAM=1';
                }

                $sqldetail = "SELECT * FROM InventoriAssetDetail WHERE TransID='$id' $am $warehouseto";
                $stmtdetail = sqlsrv_query($conn, $sqldetail);
                if ($stmtdetail === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
                // echo $sqldetail;
                ?>

                <div style="overflow-x:auto;">
                    <table style="width: 100%;" border="1" cellpadding="5">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll"> Pilih Semua
                                </th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Alasan</th>
                                <th>Kondisi Asset</th>
                                <th>Qty</th>
                                <?php
                                if ($rowheader['ApprovalStatus'] == 'Menunggu Verifikasi Warehouse') {
                                    ?>
                                    <th>Qty Verifikasi</th>
                                    <?php
                                }
                                ?>
                                <th>Warehouse Tujuan</th>
                                <th>Keterangan Persetujuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = sqlsrv_fetch_array($stmtdetail, SQLSRV_FETCH_ASSOC)): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="check-item"
                                            name="check_item[<?php echo $item['ItemCode']; ?>]"
                                            data-status-id="status_<?php echo $item['ItemCode']; ?>"
                                            onchange="updateStatus('<?php echo $item['ItemCode']; ?>')">

                                        <input type="hidden" name="status_item[<?php echo $item['ItemCode']; ?>]"
                                            id="status_<?php echo $item['ItemCode']; ?>" value="0">
                                        <!-- Default: Not Approve -->
                                        <input type="hidden" name="ID[<?php echo $item['ItemCode']; ?>]"
                                            id="ID_<?php echo $item['ItemCode']; ?>" value="<?php echo $item['ID']; ?>">
                                    </td>
                                    <td><b><?php echo $item['ItemCode']; ?></b></td>
                                    <td><?php echo $item['ItemName']; ?></td>
                                    <td><?php echo $item['ItemUom']; ?></td>
                                    <td><?php echo $item['Remarks']; ?></td>
                                    <td><?php echo $item['ConditionAsset']; ?></td>
                                    <td><?php echo number_format($item['Quantity'], 2, '.', ','); ?></td>

                                    <?php
                                    if ($rowheader['ApprovalStatus'] == 'Menunggu Verifikasi Warehouse') {
                                        ?>
                                        <td>
                                            <input type="number" class="form-control"
                                                id="QuantityVer_<?php echo $item['ItemCode']; ?>"
                                                name="QuantityVer[<?php echo $item['ItemCode']; ?>]"
                                                data-qty-permintaan="<?php echo $item['Quantity']; ?>" min="0" max="<?php echo $item['Quantity']; ?>"
                                                value="<?php echo $item['Quantity']; ?>">
                                        </td>
                                        <?php
                                    }
                                    ?>
                                    <td><?php echo $item['WarehouseTo']; ?></td>
                                    <th><textarea name="RemarksItemApproval[<?php echo $item['ItemCode']; ?>]"
                                            id="RemarksItemApproval_<?php echo $item['ItemCode']; ?>" cols="30"
                                            rows="3"></textarea></th>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>


                <br>
                <div align="right">


                    <div class="col-sm-3">

                        <div class="form-group row">
                            <label for="inputEmail" class="col-sm-12 col-form-label">Keterangan Status
                            </label>
                            <div class="col-sm-12">
                                <textarea name="ApprovalRemarks" class="form-control"
                                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                    maxlength="110" cols="10" rows="5"></textarea>
                            </div>
                        </div>
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

            function updateStatus(itemCode) {
                const checkbox = document.querySelector(`input[name="check_item[${itemCode}]"]`);
                const statusInput = document.getElementById(`status_${itemCode}`);
                statusInput.value = checkbox.checked ? "1" : "0"; // 0 = Not Approve, 1 = Approve
            }

            document.getElementById('selectAll').addEventListener('change', function () {
                const allCheckboxes = document.querySelectorAll('.check-item');
                allCheckboxes.forEach(function (cb) {
                    cb.checked = event.target.checked;
                    const itemCode = cb.getAttribute('name').match(/\[(.*?)\]/)[1];
                    updateStatus(itemCode);
                });
            });

            $(".revisi").hide();
            $(".rev_question").click(function () {
                if ($(this).is(":checked")) {
                    var bookdelivery = $('#DocDate').val();
                    $(".revisi").show();
                    $(".rev_question").val(1);
                    $(".rev_date_req").val(bookdelivery);
                    $(".pastdatedel").html('<b>Tanggal Pengiriman Sebelumnya : ' + bookdelivery + '</b>');
                } else {
                    $(".revisi").hide();
                    $(".rev_question").val(0);
                    $(".rev_date_req").val('');
                    $(".pastdatedel").html('')
                }
            })

            $(function () {
                $("body").delegate("#rev_date_req", "focusin", function () {
                    var today = new Date();
                    $(this).datepicker({
                        showOtherMonths: true,
                        selectOtherMonths: true,
                        dateFormat: "yy-mm-dd",
                        minDate: today,
                        onSelect: function (selectedDate) { }
                    });
                });
            });

            $("body").on("keydown", ".keterangan_barang", function () {
                var x = event.which;
                if (x === 13) {
                    event.preventDefault();
                }
            });



            $(document).on('keyup keydown change', '.status', function () {

                id_arr = $(this).attr('id');
                id = id_arr.split("_");

                var status = $('#status_' + id[1]).val();
                var keterangan = $('#keterangan_' + id[1]).val();

                if (status == 0) {
                    $('#keterangan_' + id[1]).attr('required', false);
                } else if (status == 1) {
                    $('#keterangan_' + id[1]).attr('required', true);
                }

            });


            $('#sender_container').submit(function (e) {
                e.preventDefault();

                var valid = true;
                var firstInvalidRemarks = null; // Untuk menyimpan elemen yang pertama tidak valid
                var reqrtn_code_date = $('#reqrtn_code_date').val();
                var rev_date_req = $("#rev_date_req").val();

                if (rev_date_req == reqrtn_code_date) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Perubahan Tanggal sama dengan tanggal sebelumnya, tidak ada perubahan!'
                    });
                    $("#status_proses").focus();
                    return;
                }

                const checkboxes = document.querySelectorAll('.check-item');
                checkboxes.forEach(function (cb) {
                    const itemCode = cb.getAttribute('name').match(/\[(.*?)\]/)[1];
                    const remarks = document.getElementById(`RemarksItemApproval_${itemCode}`);
                    const isChecked = cb.checked;

                    if (!isChecked && remarks.value.trim() === '') {
                        remarks.style.border = '2px solid red';

                        // Simpan elemen pertama yang tidak valid
                        if (!firstInvalidRemarks) {
                            firstInvalidRemarks = remarks;
                        }

                        valid = false;
                    } else {
                        remarks.style.border = '';
                    }
                });

                $('.check-item:checked').each(function () {
                    var itemCode = $(this).attr('name').match(/\[(.*?)\]/)[1];
                    var qtyPermintaan = parseFloat($(`#QuantityVer_${itemCode}`).attr('data-qty-permintaan') || 0);
                    var qtyVerifikasiInput = $(`#QuantityVer_${itemCode}`);
                    var qtyVerifikasi = parseFloat(qtyVerifikasiInput.val() || 0);

                    if (qtyVerifikasi > qtyPermintaan) {
                        qtyVerifikasiInput.css('border', '2px solid red');

                        Swal.fire({
                            icon: 'error',
                            title: 'Qty Verifikasi Tidak Valid',
                            text: `Qty Verifikasi untuk item ${itemCode} tidak boleh lebih  dari qty permintaan (${qtyPermintaan}).`
                        });

                        qtyVerifikasiInput.focus();
                        valid = false;
                        return false;
                    } else {
                        qtyVerifikasiInput.css('border', '');
                    }
                });

                if (!valid) return;


                if (!valid) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi Gagal',
                        text: 'Harap isi keterangan persetujuan untuk item yang tidak dicentang.',
                    });

                    // Scroll dan fokus ke remarks pertama yang error
                    if (firstInvalidRemarks) {
                        firstInvalidRemarks.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalidRemarks.focus();
                    }

                    return;
                }

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
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                url: "approvalassetsproses.php",
                                type: "POST",
                                data: $('#sender_container').serialize(),
                                dataType: "html",
                                success: function (response) {
                                    swal.fire("Berhasil!", response, "success");
                                    setTimeout(function () {
                                        location.reload();
                                     }, 5000); 
                                   location.href = 'listrequestassets.php';
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                    setTimeout(function () {
                                        swal("Error", "Tolong Cek Koneksi Lalu Ulangi", "error");
                                    }, 5000);
                                }
                            });
                        });
                    },
                }).then(function (e) {
                    if (e.value === true) {
                        // Optional: tindakan lanjut
                    } else {
                        e.dismiss;
                    }
                }, function (dismiss) {
                    return false;
                });
            });


        </script>
        </body>

        </html>