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
   <select name="storeCdText[]" id="storeCdText" multiple class="form-control" style="height:200px;">
       <option value="">Pilih Store</option> 
     <!-- <option value="999">All Store</option>   -->
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
  <input type="text" name="fromDate"  id="fromDate"  class="form-control start_date" required readonly>
</div>

<div class="form-group col-sm-2">
    <label for="exampleInputPassword1">Akhir Tanggal Pemesanan</label>
    <input type="text" name="toDate"  id="toDate" class="form-control end_date" required readonly>
</div>


<div class="form-group col-sm-2">
    <label for="exampleInputPassword1"></label>
<button type="submit" class="btn btn-primary">Submit</button>
</div>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.4/xlsx.full.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('.start_date, .end_date').datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd"
    });
    $('#reportForm').validate({
        rules: {
            storeCdText: {
                required: false
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
                url: 'http://192.168.1.61:7000/api/Report/iReportCK',
                type: 'POST',
                headers: {
                    'Authorization': 'Basic ' + btoa('MultirasaIT:Multirasa2024!'),
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

                    var wb = XLSX.utils.book_new();
                    var ws = XLSX.utils.json_to_sheet(response.data);
                    XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');

                    var today = new Date();
                    var fileName = 'Report_' + today.getFullYear() + (today.getMonth() + 1) + today.getDate() + '.xlsx';
                    XLSX.writeFile(wb, fileName);

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
