<?php
$halaman="report";
include "layouts/header.php";
include "layouts/navbar.php";
?>

<div class="container1">
 <div class="row">

<div class="col-sm-12">
</br>
<form id="reportForm" role="form" method="POST">

<span style="font-size:18px;"><b>* Laporan Pemesanan Toko</b></span>
<br><br>

<?php
if($_SESSION["id_divisi"] == 12 || $_SESSION["id_divisi"] == 11 || $_SESSION["id_divisi"] == 19 ){
?>

<div class="form-group col-sm-2">
    <label for="exampleInputPassword1">Store</label>
   <select name="storeCdText[]" id="storeCdText" class="form-control" required multiple >
      <option value="999">Pilih Store</option>
     <!-- <option value="999">All Store</option> -->
     <?php 
        date_default_timezone_set("Asia/Bangkok");
        $hostName = "192.168.2.136";
        $username = "root";
        $password = "aas260993";
        $dbname = "voucher_trial";
        $mysqli = new mysqli($hostName, $username, $password, $dbname);
        $store = mysqli_query($mysqli,"SELECT nama from mst_user where role_id=6 order by nama asc");
        while($d = mysqli_fetch_array($store)){
     ?>
         <option value="<?php echo $d['nama']; ?>"><?php echo $d['nama']; ?></option>
     <?php 
        }
     ?>
   </select>
</div>

<?php
}else{
?>

<div class="form-group col-sm-2">
    <label for="exampleInputPassword1">Toko</label>
    <select name="storeCdText[]" id="storeCdText" class="form-control" multiple>
        <option value="<?php echo $_SESSION["nama"]; ?>" selected><?php echo $_SESSION["nama"]; ?></option>
    </select>
  <!-- <input type="text" name="storeCdText" id="storeCdText" class="form-control" value="" required readonly> -->
</div>

<?php
}
?>

<div class="form-group col-sm-2">
    <label for="exampleInputPassword1">Mulai Tanggal Pemesanan</label>
  <input type="text" name="start_date" class="form-control start_date" required readonly value="2024-07-01">
</div>

<div class="form-group col-sm-2">
    <label for="exampleInputPassword1">Akhir Tanggal Pemesanan</label>
    <input type="text" name="end_date" class="form-control end_date" required readonly value="2024-07-31">
</div>


<div class="form-group col-sm-2">
    <label for="exampleInputPassword1"></label>
<button type="submit" class="btn btn-primary">Submit</button>
</div>



<table id="apiDataITR" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
    <thead>
        <tr>
            <th>docEntry</th>
            <th>docDate</th>
            <th>dueDate</th>
            <th>remarks</th>
            <th>formWarehouse</th>
            <th>toWarehouse</th>
            <th>taxDate</th>
            <th>totalQty</th>
            <th>toStatus</th>
            <th>itType</th>
            <th>storeCd</th>
            <th>transferType</th> 
            <th>orderPurpose</th>
            <th>disType</th>
            <th>reason</th>
            <th>dailyCategory</th>
            <th>nonDailyCategory</th>
            <th>linenum</th>
            <th>itemCode</th>
            <th>qty</th>
            <th>quality</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

</form>
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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.print.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.start_date, .end_date').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd"
    });
    
    var storeCdText = $('#storeCdText').val();
    var fromDate = $('#fromDate').val();
    var toDate = $('#toDate').val();
    // Inisialisasi DataTables
    var table = $('#apiDataITR').DataTable({
    responsive: true,
    dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: 'Export to Excel',
                    filename: function() {
                    return 'order_toko_' + new Date().toISOString().slice(0, 10); // Contoh: 
                },
                  customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    $('sheetData row:first', sheet).remove();
                    var rows = $('sheetData row', sheet);
                    rows.each(function(index, element) {
                        var originalIndex = index + 2;
                         $(element).attr('r', originalIndex);
                    });
                }
            }
            ],
    ajax: {
        url: 'http://192.168.1.61:7000/api/Report/iReportITR', // URL endpoint yang sesuai
        dataSrc: 'data', // Nama properti dalam respons JSON yang berisi data
        headers: {
            'Authorization': 'Basic ' + btoa('MultirasaIT:Multirasa2024!'),
            'Accept': 'application/json'
        },
        type: 'POST',
        dataType: 'json', // Perbaikan: Tentukan tipe data yang diterima dari server
        contentType: 'application/json', // Perbaikan: Tentukan tipe data yang dikirimkan ke server
        data: function(d) {
            return JSON.stringify({
                storeCd: storeCdText
            });
        }
    },
    columns: [
        { data: 'docEntry', title: 'Doc Entry' },
        { data: 'docDate', title: 'Doc Date' },
        { data: 'dueDate', title: 'Due Date' },
        { data: 'remarks', title: 'Remarks' },
        { data: 'fromWarehouse', title: 'From Warehouse' },
        { data: 'toWarehouse', title: 'To Warehouse' },
        { data: 'taxDate', title: 'Tax Date' },
        { data: 'totalQty', title: 'Total Qty' },
        { data: 'toStatus', title: 'To Status' },
        { data: 'itType', title: 'IT Type' },
        { data: 'storeCd', title: 'Store Code' },
        { data: 'transferType', title: 'Transfer Type' },
        { data: 'orderPurpose', title: 'Order Purpose' },
        { data: 'distType', title: 'Dist Type' },
        { data: 'reason', title: 'Reason' },
        { data: 'dailyCategory', title: 'Daily Category' },
        { data: 'nonDailyCategory', title: 'Non-Daily Category' },
        { data: 'lineNum', title: 'Line Number' },
        { data: 'itemCode', title: 'Item Code' },
        { data: 'qty', title: 'Quantity' },
        { data: 'quality', title: 'Quality' }
    ],
    columnDefs: [
        {
            targets: '_all',
            render: function(data, type, row, meta) {
                // Menggunakan title dari kolom sebagai deskripsi tooltip
                return '<span title="' + table.columns(meta.col).header().innerHTML + '">' + data + '</span>';
            }
        }
    ],
        order: [
        [0, 'asc'], // Mengurutkan berdasarkan Doc Entry descending (kolom pertama)
        [16, 'asc'] // Mengurutkan berdasarkan Line Number descending (kolom ke-17)
    ]
});

    $('#reportForm').validate({
        rules: {
            storeCdText: {
                required: true
            }
        },
        messages: {
            storeCdText: {
                required: "Silakan pilih store."
            }
        },
        submitHandler: function(form) {
            var storeCdText = $('#storeCdText').val();
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            Swal.fire({
                title: 'Loading...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            $.ajax({
                url: 'http://192.168.1.61:7000/api/Report/iReportITR',
                type: 'POST',
                headers: {
                    'Authorization': 'Basic ' + btoa('test:test'),
                    'Accept': 'application/json'
                },
                dataType: 'json', // Perbaikan: Tentukan tipe data yang diterima dari server
                contentType: 'application/json', // Perbaikan: Tentukan tipe data yang dikirimkan ke server
                data: JSON.stringify({
                    storeCd: storeCdText,
                    fromDate: fromDate,
                    toDate: toDate
                }),
                success: function(response) {
                    Swal.close();
                    var data = response.data; // Data yang diterima dari API

                    // Hapus data yang ada sebelumnya dari tabel
                    table.clear().draw();

                    // Tambahkan data baru ke dalam tabel
                    table.rows.add(data).draw();

                    Swal.fire({
                        title: 'Success!',
                        text: 'The report has been generated successfully.',
                        icon: 'success'
                    });
                },
                error: function() {
                    Swal.close();
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error processing your request.',
                        icon: 'error'
                    });
                }
            });
        }
    });
});

</script>

</body>
</html>
