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

<span style="font-size:18px;"><b>* Report Thowing CK</b></span>
<br><br>

<?php
if($_SESSION["id_divisi"] == 12 || $_SESSION["id_divisi"] == 11 || $_SESSION["id_divisi"] == 19 ){
?>

<div class="form-group col-sm-2">
    <label for="exampleInputPassword1">Store</label>
   <select name="storeCdText[]" id="storeCdText" class="form-control" multiple style="height:380px;">
      <option value="">Pilih Store</option> 
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
    <label for="exampleInputPassword1">Warehouse Code</label>
    <select name="ckArea" id="ckArea" class="form-control">
    <option value="">All Warehouse</option>
        <option value="CKWH0002">CKWH0002</option>
        <option value="CKWH0004">CKWH0004</option>
        <option value="CKWH0005">CKWH0005</option>
        <option value="CKWH0006">CKWH0006</option>
        <option value="CKWH0007">CKWH0007</option>
        <option value="CKWH0008">CKWH0008</option>
        <option value="CKWH0009">CKWH0009</option>
        <option value="CKWH0010">CKWH0010</option>
    </select>
  <!-- <input type="text" name="storeCdText" id="storeCdText" class="form-control" value="" required readonly> -->
</div>

<div class="form-group col-sm-2">
    <label for="exampleInputPassword1">Mulai Tanggal Pemesanan</label>
  <input type="text" name="fromDate"  id="fromDate" value="2024-07-01"  class="form-control start_date" required readonly>
</div>

<div class="form-group col-sm-2">
    <label for="exampleInputPassword1">Akhir Tanggal Pemesanan</label>
    <input type="text" name="toDate"  id="toDate" value="2024-07-31" class="form-control end_date" required readonly>
</div>


<div class="form-group col-sm-2">
    <label for="exampleInputPassword1"></label>
<button type="submit" class="btn btn-primary">Submit</button>
</div>
</form>

</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            var ckArea = $('#ckArea').val();
            Swal.fire({
                title: 'Loading...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Check if storeCdText has values selected
            if (storeCdText && storeCdText.length > 0) {
                // If there are selected values, include storeCd in requestData
                var requestData = {
                    "reportCond": {
                        "storeCd": storeCdText,
                        "ckArea": ckArea,
                        "fromDate": fromDate,
                        "toDate": toDate
                    }
                };
            } else {
                // If no storeCdText values selected, exclude storeCd from requestData
                var requestData = {
                    "reportCond": {
                        "ckArea": ckArea,
                        "fromDate": fromDate,
                        "toDate": toDate
                    }
                };
            }

            $.ajax({
                url: 'http://192.168.1.61:7000/api/Report/dlExcelReportThowing',
                type: 'POST',
                headers: {
                    'Authorization': 'Basic ' + btoa('MultirasaIT:Multirasa2024!'),
                    'Accept': 'application/json'
                },
                contentType: 'application/json', 
                data: JSON.stringify(requestData),
                xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(blob, status, xhr) {
                    Swal.close(); 
                    var url = window.URL.createObjectURL(blob);                
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'ReportThowingExcel.xlsx';
                    document.body.append(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
            },
            error: function(xhr, status, error) {
                console.error('Error:', status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Failed to generate Excel file.',
                });
            }
            });
        }
    });
});


</script>
</body>
</html>
