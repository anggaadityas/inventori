<?php
include "layouts/header.php";
include "layouts/navbar.php";
error_reporting(0);
if (isset($_POST['showData'])) {
    $_SESSION['store'] = $_POST['store'];
    $store = $_SESSION['store'];
    $_SESSION['nama_barang'] = $_POST['nama_barang'];
    $nama_barang = $_SESSION['nama_barang'];
    $_SESSION['tanggal_retur_awal'] = $_POST['tanggal_retur_awal'];
    $tanggal_retur_awal = $_SESSION['tanggal_retur_awal'];
    $_SESSION['tanggal_retur_akhir'] = $_POST['tanggal_retur_akhir'];
    $tanggal_retur_akhir = $_SESSION['tanggal_retur_akhir'];

    $data .= "
            <table class='table table-bordered unitBaruUb'>
              <thead>
              <tr>
              <th rowspan='2'>No</th>
              <th rowspan='2'>No Dokumen</th>
              <th rowspan='2'>Tanggal Permintaan</th>
                <th rowspan='2'>Kode Barang</th>
                <th rowspan='2'>Nama Barang</th>
                 <th rowspan='2'>Satuan</th>
                 <th rowspan='2'>Jenis Barang</th>
                 <th rowspan='2'>Alasan</th>
                 <th colspan='2' class='text-center'>Jumlah Toko</th>
                 <th rowspan='2'>Keterangan</th>
                 <th colspan='2' class='text-center'>Jumlah Verifikasi</th>
                 <th rowspan='2'>Keterangan Verfikasi</th>
                 <th rowspan='2'>Balance</th>
                 </tr>
             <tr>
                 <th>Bagus</th>
                 <th>Tidak Bagus</th>
                 <th>Bagus</th>
                 <th>Tidak Bagus</th>
                 </tr>    
              </thead>
              <tbody>";

    $sqldetail = "SELECT
        a.id_detailrtn,
        c.id_rtn,
        reqrtn_code,
        c.reqrtn_user,
        CONVERT ( CHAR ( 10 ), reqrtn_date, 126 ) tanggal_retur,
        a.rtnitem_code,
        a.rtnitem_name,
        a.rtnitem_uom,
        a.rtnitem_cat,       
        a.rtnitem_reason,
        a.rtnitem_remarks,
        a.rtnitem_qty_good,
        a.rtnitem_qty_not_good,
        a.rtnitem_qty_verifikasi_good,
        a.rtnitem_qty_verifikasi_not_good,
        a.rtnitem_remarks_verifikasi,
        ( ISNULL( a.rtnitem_qty_good, 0 ) + ISNULL( a.rtnitem_qty_not_good, 0 ) ) AS qty_kirim,
	( ISNULL( a.rtnitem_qty_verifikasi_good, 0 ) + ISNULL( a.rtnitem_qty_verifikasi_not_good, 0 ) ) AS qty_verifikasi,
	( ISNULL( a.rtnitem_qty_good, 0 ) + ISNULL( a.rtnitem_qty_not_good, 0 ) ) - ( ISNULL( a.rtnitem_qty_verifikasi_good, 0 ) + ISNULL( a.rtnitem_qty_verifikasi_not_good, 0 ) ) AS balance
    FROM
        detail_returnck a
        INNER JOIN mst_item b ON a.rtnitem_id= b.id_mst_item
        INNER JOIN header_returnck c ON a.header_idrtn= c.id_rtn 
    WHERE
        b.item_name LIKE '%$nama_barang%' 
        AND reqrtn_user = '$store' 
        AND reqrtn_date BETWEEN '$tanggal_retur_awal' 
        AND '$tanggal_retur_akhir' 
    ORDER BY
        rtnitem_cat ASC";
    $stmtdetail = sqlsrv_query($conn, $sqldetail);
    if ($stmtdetail === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    $no = 0;

    while ($e = sqlsrv_fetch_array($stmtdetail, SQLSRV_FETCH_ASSOC)) {
        $no++;
        $data .= "<tr>
        <td>" . $no . "</td>
                  <td>
                    <input type='hidden' class='form-control' name='id_detailrtn[]'' value='" . $e['id_detailrtn'] . "'>
                    <input type='hidden' class='form-control' name='id_rtn[]' value='" . $e['id_rtn'] . "' readonly>
                    <p>" . $e['reqrtn_code'] . "</p>
                    <input type='hidden' class='form-control' name='id_barang[]' value='" . $e['rtnitem_id'] . "' readonly>
                   </td>
                    <td><p>" . $e['tanggal_retur'] . "</p> </td>
                    <td><p>" . $e['rtnitem_code'] . "</p> </td>
                    <td><p>" . $e['rtnitem_name'] . "</p> </td>
                    <td><p>" . $e['rtnitem_uom'] . "</p> </td>
                    <td><p>" . $e['rtnitem_cat'] . "</p> </td>
                    <td><p>" . $e['rtnitem_reason'] . "</p> </td>
                    <td>
                    <input type='hidden' readonly size='5' class='qtygoodold' name='qtygoodold[]' id='qtygoodold_" . $no . "' value='" . $e['rtnitem_qty_good'] . "'>
                    <input type='text' size='5' class='qtygood' name='qtygood[]' id='qtygood_" . $no . "' value='" . $e['rtnitem_qty_good'] . "'>
                    </td>
                    <td>
                    <input type='hidden' readonly size='5' class='qtynotgoodold' name='qtynotgoodold[]' id='qtynotgoodold_" . $no . "' value='" . $e['rtnitem_qty_not_good'] . "'>
                    <input type='text' size='5' class='qtynotgood' name='qtynotgood[]' id='qtynotgood_" . $no . "' value='" . $e['rtnitem_qty_not_good'] . "'>
                    </td>
                    <td>
                    <input type='hidden' readonly size='5' class='rtnitem_remarks' name='rtnitem_remarks_old[]' id='rtnitemremarksold_" . $no . "' value='" . $e['rtnitem_remarks'] . "'>
                    <textarea name='rtnitem_remarks[]' id='rtnitem_remarks' rows='5'>" . $e['rtnitem_remarks'] . "</textarea>
                    </td>
                    <td><p>" . $e['rtnitem_qty_verifikasi_good'] . "</p>
                    <input type='hidden' readonly size='5' class='qtyverifikasigood' name='qtyverifikasigood[]' id='qtyverifikasigood_" . $no . "' value='" . $e['rtnitem_qty_verifikasi_good'] . "'>
                    </td>
                    <td><p>" . $e['rtnitem_qty_verifikasi_not_good'] . "</p>
                    <input type='hidden' readonly size='5' class='qtyverifikasinotgood' name='qtyverifikasinotgood[]' id='qtyverifikasinotgood_" . $no . "' value='" . $e['rtnitem_qty_verifikasi_not_good'] . "'>
                    </td>
                    <td>
                    <p>" . $e['rtnitem_remarks_verifikasi'] . "</p>
                    </td>
                    <td><p class='balance' id='balance_" . $no . "'>" . $e['balance'] . "</p></td>
                </tr>";
    }
    $data .= "</tbody></table>";
    $data .= " <div style='margin-top: 20px;'>
        <button type='submit' class='btn btn-info' name='insertData' value='Save'><b>Simpan</b></button>
      </div><br><br><br>";
} else if (isset($_POST['insertData'])) {

    $id_rtn = $_POST['id_rtn'];
    $id_detailrtn = $_POST['id_detailrtn'];
    $store =  $_POST['store'];
    $created_by =  $_SESSION['nama'];

    /* Initiate transaction. */
    /* Exit script if transaction cannot be initiated. */
    if (sqlsrv_begin_transaction($conn) === false) {
        echo "Could not begin transaction.\n";
        die(print_r(sqlsrv_errors(), true));
    }

    $sqldetail = "";
    foreach ($_POST['id_barang'] as $option => $opt) {

        $sqldetail .= "UPDATE detail_returnck SET 
            rtnitem_qty_good='" . htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option])))) . "',
             rtnitem_qty_not_good='" . htmlspecialchars(addslashes(trim(strip_tags($_POST['qtynotgood'][$option])))) . "',
            rtnitem_remarks='" . htmlspecialchars(addslashes(trim(strip_tags($_POST['rtnitem_remarks'][$option])))) . "',
            update_date=getdate(),
            updated_by='$created_by'
            WHERE 
            header_idrtn='" . htmlspecialchars(addslashes(trim(strip_tags($_POST['id_rtn'][$option])))) . "'
          and id_detailrtn='" . htmlspecialchars(addslashes(trim(strip_tags($_POST['id_detailrtn'][$option])))) . "'";
        $sqldetail .= ";";
    }

    $sqldetailfix = rtrim($sqldetail, ";");
    $stmt1 = sqlsrv_query($conn, $sqldetailfix);

    $sqllog = "INSERT INTO log_retur_item  (
        detailrtr_id,
        headerrtr_id,
        qty_good_old,
        qty_notgood_old,
        qty_good_new,
        qty_notgood_new,
        remarks_old,
        remarks_new,
        created_date,
        created_by) values ";

    foreach ($_POST['id_barang'] as $option => $opt) {
        $sqllog .= "(
                   '" . htmlspecialchars(addslashes(trim(strip_tags($_POST['id_detailrtn'][$option])))) . "',
                   '" . htmlspecialchars(addslashes(trim(strip_tags($_POST['id_rtn'][$option])))) . "',
                   '" . htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygoodold'][$option])))) . "',
                   '" . htmlspecialchars(addslashes(trim(strip_tags($_POST['qtynotgoodold'][$option])))) . "',
                   '" . htmlspecialchars(addslashes(trim(strip_tags($_POST['qtygood'][$option])))) . "',
                   '" . htmlspecialchars(addslashes(trim(strip_tags($_POST['qtynotgood'][$option])))) . "',
                   '" . htmlspecialchars(addslashes(trim(strip_tags($_POST['rtnitem_remarks_old'][$option])))) . "',
                   '" . htmlspecialchars(addslashes(trim(strip_tags($_POST['rtnitem_remarks'][$option])))) . "',
                   getdate(),
                   '$created_by')";
        $sqllog .= ",";
    }

    $sqlfixlog = rtrim($sqllog, ",");
    $stmt2 = sqlsrv_query($conn, $sqlfixlog);

    if ($stmt1 && $stmt2) {
        sqlsrv_commit($conn);
        echo '<div class="container1">
        <div class="row">
          <div class="col-sm-12" style="margin-top: 18px;">
          <div class="alert alert-warning alert-dismissible fade show col-sm-5" role="alert">
        <strong>Info!</strong> Data Berhasil Dilakukan Revisi
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      </div>
      </div>
      </div>';
    } else {
        sqlsrv_rollback($conn);
        echo '<div class="container1">
        <div class="row">
          <div class="col-sm-12" style="margin-top: 18px;">
        <div class="alert alert-danger alert-dismissible fade show col-sm-5" role="alert">
        <strong>Info!</strong> Data Gagal Dilakukan Revisi
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      </div>
      </div>
      </div>';
        echo $sqlheader . "\n";
        echo $sqldetail . "\n";
        echo $sqllog . "\n";
    }

    /* Free statement and connection resources. */
    sqlsrv_free_stmt($stmt);
    sqlsrv_free_stmt($stmt1);
    sqlsrv_free_stmt($stmt2);
    sqlsrv_close($conn);
} else {
    $nama_barang = '';
    $store = '';
    $tanggal_retur_awal = '';
    $tanggal_retur_akhir = '';
}

?>

<div class="container1">
    <div class="row">

        <div class="col-sm-12" style="margin-top: 26px;">
            <span style="font-size:18px;"><b>* Revisi Qty Retur Barang </b></span>
            <br><br><br>

            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Store</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="store" id="store" value="<?php echo $_SESSION['nama']; ?>" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Nama Barang</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="nama_barang" value="Minyak Goreng Bekas" id="nama_barang" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Retur/Tanggal Kedatangan Barang (Awal)</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="tanggal_retur_awal" id="tanggal_retur_awal" value="<?php echo  $_SESSION['tanggal_retur_awal']; ?>" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Retur/Tanggal Kedatangan Barang (Akhir)</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="tanggal_retur_akhir" id="tanggal_retur_akhir" value="<?php echo  $_SESSION['tanggal_retur_akhir']; ?>" readonly>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-2 col-form-label"></label>
                    <div class="col-sm-2">

                        <button type="submit" name="showData" class="btn btn-primary"><b>Tampilkan Data</b></button>
                    </div>
                </div>
                <br><br>
        </div>

        <div class='row'>
            <div class='bd rounded table-responsive'>
                <?php
                if (empty($data)) {
                    echo "";
                } else {
                    echo $data;
                }
                ?>
            </div>
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
<script src="js/jquery.inputmask.bundle.min.js" charset="utf-8"></script>
<script>
    $('.qtygood, .qtynotgood').inputmask({
        alias: "decimal",
        digits: 2,
        repeat: 13,
        digitsOptional: false,
        decimalProtect: true,
        groupSeparator: ".",
        placeholder: '0',
        radixPoint: ".",
        radixFocus: true,
        autoGroup: true,
        autoUnmask: false,
        onBeforeMask: function(value, opts) {
            return value;
        },
        removeMaskOnSubmit: true
    });


    $("body").on("keydown", "#keterangan", function() {
        var x = event.which;
        if (x === 13) {
            event.preventDefault();
        }
    });

    $("body").on("focus", "#tanggal_retur_awal,#tanggal_retur_akhir", function() {
        $(this).datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: "yy-mm-dd",
            // maxDate: 0,
            onSelect: function(selectedDate) {

            }
        });
    });

    $(document).on('keyup keydown change', '.qtygood', function() {

        id_arr = $(this).attr('id');
        id = id_arr.split("_");

        var qtygood = $('#qtygood_' + id[1]).val();
        var qtynotgood = $('#qtynotgood_' + id[1]).val();
        var qtyverifikasigood = $('#qtyverifikasigood_' + id[1]).val();
        var qtyverifikasinotgood = $('#qtyverifikasinotgood_' + id[1]).val();
        var totalqty = parseInt(qtygood) + parseInt(qtynotgood);
        var totalqty1 = parseInt(qtyverifikasigood) + parseInt(qtyverifikasinotgood);
        var subtotal = totalqty - totalqty1;
        $('#balance_' + id[1]).html('<p>' + totalqty + '</p>');

        if (subtotal > 0) {
            $('#balance_' + id[1]).css({
                'background-color': '#F61656'
            });
        } else if (subtotal < 0) {
            $('#balance_' + id[1]).css({
                'background-color': '#F61656'
            });
        } else {
            $('#balance_' + id[1]).css({
                'background-color': '#FFFFFF'
            });
        }

    });

    $(document).on('keyup keydown change', '.qtynotgood', function() {

        id_arr = $(this).attr('id');
        id = id_arr.split("_");

        var qtygood = $('#qtygood_' + id[1]).val();
        var qtynotgood = $('#qtynotgood_' + id[1]).val();
        var qtyverifikasigood = $('#qtyverifikasigood_' + id[1]).val();
        var qtyverifikasinotgood = $('#qtyverifikasinotgood_' + id[1]).val();
        var totalqty = parseInt(qtygood) + parseInt(qtynotgood);
        var totalqty1 = parseInt(qtyverifikasigood) + parseInt(qtyverifikasinotgood);
        var subtotal = totalqty - totalqty1;
        $('#balance_' + id[1]).html('<p>' + totalqty + '</p>');

        if (subtotal > 0) {
            $('#balance_' + id[1]).css({
                'background-color': '#F61656'
            });
        } else if (subtotal < 0) {
            $('#balance_' + id[1]).css({
                'background-color': '#F61656'
            });
        } else {
            $('#balance_' + id[1]).css({
                'background-color': '#FFFFFF'
            });
        }

    });

    $('#sender_container').submit(function(e) {

        e.preventDefault();
        var valid = true;
        var status_verifikasi = $('#status_request :selected').val();
        var date_verifikasi = $('#date_verifikasi').val();

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
            preConfirm: function() {
                return new Promise(function(resolve, reject) {
                    // here should be AJAX request
                    $.ajax({
                        url: "editreturproses.php",
                        type: "POST",
                        data: $('#sender_container').serialize(), //serialize() untuk mengambil semua data di dalam form
                        dataType: "html",
                        success: function(response) {
                            swal.fire("Berhasil!", response, "success");
                            // refresh page after 2 seconds
                            setTimeout(function() {
                                location.reload();
                            }, 5000);
                            location.href = 'listreturn.php';
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            setTimeout(function() {
                                swal("Error", "Tolong Cek Koneksi Lalu Ulangi", "error");
                            }, 5000);
                        }
                    });

                });
            },
        }).then(function(e) {

            if (e.value === true) {

            } else {
                e.dismiss;
            }

        }, function(dismiss) {
            return false;
        })


    });
</script>
</body>

</html>