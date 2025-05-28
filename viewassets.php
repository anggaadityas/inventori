<?php
include "layouts/header.php";
error_reporting(0);
$id = $_GET['id'];
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
        c.Qty_Pengirim,
        c.Qty_Penerima,
        c.Qty_Penerima - c.Qty_Pengirim AS selisih_item,
        convert(char(20),a.CreatedDate,120) date_submit 
        FROM InventoriAssetHeader a
        inner join MasterDocTrans b on a.DocTrans=b.ID
         LEFT JOIN (
          SELECT TransID,SUM(Quantity) AS Qty_Pengirim,SUM(ISNULL(QuantityVer,0)) AS Qty_Penerima FROM InventoriAssetDetail 
          GROUP BY TransID 
      ) C on a.ID=c.TransID
        where a.ID='$id'";
$stmtheader = sqlsrv_query($conn, $sqlheader);
if ($stmtheader === false) {
    die(print_r(sqlsrv_errors(), true));
}
$rowheader = sqlsrv_fetch_array($stmtheader, SQLSRV_FETCH_ASSOC);
include "layouts/navbar.php";
?>

<div class="container1">
    <div class="row">

        <div class="col-sm-12" style="margin-top: 26px;">
            <span style="font-size:18px;"><b>* View Pengajuan Inventaris Asset #
                    <?php echo $rowheader['DocNum']; ?></b></span>
            <br><br><br>

            <form method="POST" action="approvereturnassetsproses.php" id="sender_container">

                <fieldset>
                    <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                        <div class="col-sm-3">
                            <input type="hidden" class="form-control" id="inputEmail" name="DocNum"
                                value="<?php echo $rowheader['DocNum']; ?>" readonly>
                            <input type="hidden" class="form-control" id="inputEmail" name="ID"
                                value="<?php echo $rowheader['ID']; ?>" readonly>
                            <input type="text" class="form-control" id="inputEmail" name="DocDate"
                                value="<?php echo $rowheader['DocDate']; ?>" readonly>
                            <input type="hidden" class="form-control" id="inputEmail" name="ApprovalProgress"
                                value="<?php echo $rowheader['ApprovalProgress']; ?>" readonly>
                        </div>
                    </div>

                    <!-- <div class="form-group row" style="margin-top: 10px;">
                        <label for="inputEmail3" class="col-sm-2 col-form-label"></label>
                        <div class="form-check"><input type="checkbox" name="rev_question" id="rev_question" value="0"
                                class="form-check-input rev_question">
                            <span style="font-size: 10px;"><b>Ingin Melakukan Perubahan Tanggal Pengiriman?</b></span>
                        </div>
                    </div> -->

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
                            <input type="text" class="form-control" id="inputEmail" name="TransName"
                                value="<?php echo $rowheader['TransName']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">WarehouseFrom</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="inputEmail" name="WarehouseFrom"
                                value="<?php echo $rowheader['WarehouseFrom']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">WarehouseTo</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="inputEmail" name="WarehouseTo"
                                value="<?php echo $rowheader['WarehouseTo']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword" class="col-sm-2 col-form-label">TermsAsset</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" id="inputEmail" name="TermsAsset"
                                value="<?php echo $rowheader['TermsAsset']; ?>" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan Permintaan</label>
                        <div class="col-sm-3">
                            <textarea name="note_request" class="form-control" name="keterangan" cols="10" rows="5"
                                readonly><?php echo $rowheader['Remarks']; ?>
               </textarea>
                        </div>
                    </div>
                </fieldset>

                <br>
                <h6>Table Progress Approval</h6>

                <?php
                $sql = "SELECT * FROM InventoriApprovalAsset WHERE TransID = ?";
                $params = array($id); // Ganti sesuai kebutuhan (misal: ID dari URL)
                $stmt = sqlsrv_query($conn, $sql, $params);

                // Tampilkan ke HTML table
                echo '<table border="1" cellpadding="5" cellspacing="0">';
                echo '<tr>
        <th>ApprovalStep</th>
        <th>UserNameApproval</th>
        <th>StatusApproval</th>
        <th>ApprovalStatus</th>
        <th>ApprovalDate</th>
        <th>ApprovalRemarks</th>
      </tr>';

                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . $row['ApprovalStep'] . '</td>';
                    echo '<td>' . $row['UserNameApproval'] . '</td>';
                    echo '<td>' . $row['StatusApproval'] . '</td>';
                    echo '<td>' . $row['ApprovalStatus'] . '</td>';
                    echo '<td>' . ($row['ApprovalDate'] ? $row['ApprovalDate']->format('Y-m-d H:i:s') : '') . '</td>';
                    echo '<td>' . $row['ApprovalRemarks'] . '</td>';
                    echo '</tr>';
                }

                echo '</table>';

                ?>

                <br>

                <p><b>Cetak Surat Jalan : </b></p>
                <?php
                if($rowheader['ApprovalProgress'] > 2) {
                ?>

                <?php
                $sqlcat = "SELECT DISTINCT WarehouseTo FROM InventoriAssetDetail WHERE TransID='$id' AND StatusApprovalAM=1
                    GROUP BY WarehouseTo
                ";
                $stmtcat = sqlsrv_query($conn, $sqlcat);
                if ($stmtcat === false) {
                    die(print_r(sqlsrv_errors(), true));
                }
                $no = 0;
                while ($rowcat = sqlsrv_fetch_array($stmtcat, SQLSRV_FETCH_ASSOC)) {
                    $no++;
                    ?>

                    <p><?php echo $no; ?>. <a target="_blank"
                            href="cetakassets.php?id=<?php echo $id; ?>&warehouse=<?php echo $rowcat['WarehouseTo']; ?>"
                            class="badge badge-pill badge-success"><b><?php echo $rowcat['WarehouseTo']; ?></b></a>


                        <?php
                }
            }
                ?>

                    <br>

                    <?php
                    $sqldetail = "SELECT * FROM InventoriAssetDetail WHERE TransID='$id'";
                    $stmtdetail = sqlsrv_query($conn, $sqldetail);
                    if ($stmtdetail === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }

                    $cats = array();
                    while ($row = sqlsrv_fetch_array($stmtdetail, SQLSRV_FETCH_ASSOC)) {
                        $prefix = substr($row['ItemCode'], 0, 3); // Ambil 3 karakter pertama dari ItemCode
                        $cats[$prefix][] = $row;
                        if ($row['ConditionAsset'] == 0) {
                            $kondisiasset = 'Tidak Bagus';
                        } else {
                            $kondisiasset = 'Bagus';
                        }
                        if($rowheader['TransName'] == 'Warehouse To Store'){
                            $status_am='';
                            $status_distribusi = '';    
                        }else{
                                    if ($row['StatusApprovalAM'] == '' || $row['StatusApprovalAM'] == null) {
                                        $status_am = 'Belum Disetujui';
                                    }else if($row['StatusApprovalAM'] == 0){
                                        $status_am = 'Tidak Disetujui';
                                    } else {
                                        $status_am = 'Disetujui';
                                    }
                                    if ($row['StatusApprovalDistribusi'] == '' || $row['StatusApprovalDistribusi'] == null) {
                                        $status_distribusi = 'Belum Disetujui';
                                    }else if($row['StatusApprovalDistribusi'] == 0){
                                        $status_am = 'Tidak Disetujui';
                                    } else {
                                        $status_distribusi = 'Disetujui';
                                    }
                        }
                    }
                    ?>

                <div style="overflow-x:auto;">

                    <?php
                    $no = 1;
                    foreach ($cats as $prefix => $values):
                        $firstRow = reset($values);

                        ?>
                        <br>
                        <tr>
                            <td class="row_group">
                                <p style="font-size:20px;"><b><?php echo $no; ?>. <?php echo $prefix; ?></b></p>
                            </td>
                            <!-- <td>
                                <input type="hidden" name="cat[<?php echo $no; ?>]" value="<?php echo $prefix; ?>" readonly>

                                <select name="status[<?php echo $no; ?>]" class="status" id="status_<?php echo $no; ?>"
                                    required>
                                    <option value="">--Pilih Status--</option>
                                    <option value="0">Approve</option>
                                    <option value="1">Not Approve</option>
                                </select>

                                <br><br>
                                <textarea name="keterangan[<?php echo $no; ?>]" class="keterangan"
                                    id="keterangan_<?php echo $no; ?>" cols="30" rows="5"></textarea>
                            </td> -->
                        </tr>

                        <table style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>    
                                    <th>Satuan</th>
                                    <th>Alasan</th>
                                    <th>Kondisi Asset</th>
                                    <th>Qty Pengiriman</th>
                                    <th>Qty Penerimaan</th>
                                    <th>Warehouse Tujuan</th>
                                    <th>Keterangan Barang</th>
                                    <th>Status Approval AM</th>
                                    <th>Status Approval RM</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <?php foreach ($values as $item)
                                            echo '<b>' . $item['ItemCode'] . '</b><br/><hr>'; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($values as $item)
                                            echo $item['ItemName'] . '<br/><hr>'; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($values as $item)
                                            echo $item['ItemUom'] . '<br/><hr>'; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($values as $item)
                                            echo $item['Reason'] . '<br/><hr>'; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($values as $item)
                                            echo $kondisiasset. '<br/><hr>'; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($values as $item)
                                            echo number_format($item['Quantity'], 2, '.', ',') . '<br/><hr>'; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($values as $item)
                                            echo number_format($item['QuantityVer'], 2, '.', ',') . '<br/><hr>'; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($values as $item)
                                            echo $item['WarehouseTo'] . '<br/><hr>'; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($values as $item)
                                            echo $item['Remarks'] . '<br/><hr>'; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($values as $item)
                                            echo $status_am . '<br/><hr>'; ?>
                                    </td>
                                    <td>
                                        <?php foreach ($values as $item)
                                            echo $status_distribusi . '<br/><hr>'; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <?php
                        $no++;
                    endforeach;
                    ?>

                </div>

                <br>
                <div align="right">


                    <!-- <div class="col-sm-3">

                        <div class="form-group row">
                            <label for="inputEmail" class="col-sm-12 col-form-label">Keterangan Status
                            </label>
                            <div class="col-sm-12">
                                <textarea name="note_request_verifkasi" class="form-control"
                                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                    maxlength="110" cols="10" rows="5"></textarea>
                            </div>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary"><b>Proses Permintaan</b></button>
                        <br><br><br>
                    </div> -->
                    <br><br><br>
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

            $(".revisi").hide();
            $(".rev_question").click(function () {
                if ($(this).is(":checked")) {
                    var bookdelivery = $('#reqrtn_code_date').val();
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
                var reqrtn_code_date = $('#reqrtn_code_date').val();
                var rev_date_req = $("#rev_date_req").val();
                if (rev_date_req == reqrtn_code_date) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Perubahan Tanggal sama dengan tanggal sebelumnya, tidak ada perubahan!'
                    });
                    $("#status_proses").focus();
                    valid = false;
                } else {
                    if (valid) {
                        console.log('ok');
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
                                    // here should be AJAX request
                                    $.ajax({
                                        url: "approvereturnproses.php",
                                        type: "POST",
                                        data: $('#sender_container').serialize(), //serialize() untuk mengambil semua data di dalam form
                                        dataType: "html",
                                        success: function (response) {
                                            swal.fire("Berhasil!", response, "success");
                                            //  refresh page after 2 seconds
                                            setTimeout(function () {
                                                location.reload();
                                            }, 5000);
                                            location.href = 'listapprovereturn.php';
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

                            } else {
                                e.dismiss;
                            }

                        }, function (dismiss) {
                            return false;
                        })


                    }
                }
            });

        </script>
        </body>

        </html>