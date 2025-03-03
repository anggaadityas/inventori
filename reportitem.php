<?php
$halaman="report";
include "layouts/header.php";
include "layouts/navbar.php";
?>

<div class="container1">
 <div class="row">

<div class="col-sm-3">
</br>
<form role="form" method="POST" action="reportitemproses.php">

<span style="font-size:18px;"><b>* Report Master item</b></span>
<br><br>



  <div class="form-group">
    <label for="exampleInputPassword1">Divisi</label>
  <input type="text" value="<?php echo $_SESSION['nama_divisi']; ?>" name="divisi" class="form-control" required readonly>
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

</body>
</html>       