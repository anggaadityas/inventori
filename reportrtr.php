<?php
$halaman="report";
include "layouts/header.php";
include "layouts/navbar.php";
?>

<div class="container1">
 <div class="row">

<div class="col-sm-3">
</br>
<form role="form" method="POST" action="reportrtrproses.php">

<span style="font-size:18px;"><b>* Laporan Retur Barang Store</b></span>
<br><br>
<?php

if($_SESSION["id_divisi"] == 12 || $_SESSION["id_divisi"] == 11){

?>

<div class="form-group">
    <label for="exampleInputPassword1">Store</label>
   <select name="toko" id="toko" class="form-control" required>
     <option value="">Pilih Store</option>
     <option value="999">All Store</option>
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
}
?>

  <div class="form-group">
    <label for="exampleInputPassword1">Status Dokumen</label>
   <select name="status_dokumen" id="status_dokumen" class="form-control" >
     <option value="">All</option>
     <option value="Selesai">Selesai</option>
    <option value="Belum Selesai">Belum Selesai</option>
    <option value="Reject">Reject</option>
    </select>
  </div>

<?php
if($_SESSION["area_div"] !=='ENG JAKARTA' OR $_SESSION["area_div"] !=='ENG SURABAYA'){

?>

    <div class="form-group">
    <label for="exampleInputPassword1">Kategori Retur</label>
   <select name="kategori_retur" id="kategori_retur" class="form-control" >
     <option value="999">All</option>
     <option value="SISTEM">Sistem</option>
     <option value="NON SISTEM">Non Sistem</option>
      <option value="WADAH">Wadah</option>
       <option value="NCR">NCR</option>
       <option value="DAMAGE">DAMAGE</option>
    </select>
  </div>

  <?php
}
?>

  <div class="form-group">
    <label for="exampleInputPassword1">Mulai Tanggal Permintaan</label>
  <input type="text" name="start_date" class="form-control start_date" required readonly>
  </div>

  <div class="form-group">
    <label for="exampleInputPassword1">Akhir Tanggal Permintaan</label>
    <input type="text" name="end_date" class="form-control end_date" required readonly>
  </div>

  <!-- <div class="form-group">
    <label for="exampleInputEmail1">Start Date</label>
    <input type="date" class="form-control" id="start_date" name="start_date"  value="<?php echo date('Y-01-01'); ?>"  required>
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">End Date</label>
    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo date('Y-m-d'); ?>"   required>
  </div> -->



  <button type="submit" class="btn btn-primary">Submit</button>
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
<!-- <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script> -->
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
         $('.start_date, .end_date').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: "yy-mm-dd"
              });

 </script>
</body>
</html>       