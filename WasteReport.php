<?php
$halaman="report";
include "layouts/header.php";
include "layouts/navbar.php";
error_reporting(0);

$dsn = "hanamrnprod";
$user = "DBADMIN";
$password = "Passw0rd";
$conn = odbc_connect($dsn, $user, $password);

if (isset($_POST['showData'])) {

    $Fstore = $_SESSION['nama'];
    $_SESSION['startdate'] = $_POST['startdate'];
    $startdate = $_SESSION['startdate'];
    $_SESSION['enddate'] = $_POST['enddate'];
    $enddate = $_SESSION['enddate'];

    if($Fstore =='T01'){
       $store ='F01';
    }elseif ($Fstore =='T02'){
         $store ='F02';
    }else{
        $store =$_SESSION['nama'];
    }

    $data = "
            <div class='table-responsive'>
            <table class='table table-bordered table-striped'  style='width: 100%;'>
              <thead>
              <tr>
              <th>No</th>
              <th>Posting Date</th>
              <th>GI Waste No</th>
              <th>Store</th>
              <th>Remarks</th>
              <th>Status</th>
              <th>Reviewed</th>
              <th>Action</th>
              </tr>
              </thead>
              <tbody>";

              $queryh = 'SELECT CAST(WASTEH."U_DocDate" AS DATE) "U_DocDate",WASTEH."U_StoreCode",WASTEH."DocEntry",WASTEH."DocNum",WASTEH."U_Remarks",
              WASTEH."U_ApprStatus",CASE
        WHEN WASTEH."CreateDate" != WASTEH."UpdateDate" THEN \'Yes\'
        ELSE \'No\' 
    END  AS "DTW"
              FROM "MRN_LIVE"."@ST_GIW_DOCWEBH" WASTEH 
              WHERE 
              WASTEH."U_DocDate" BETWEEN \'' . $startdate . '\' AND \'' . $enddate . '\' AND WASTEH."U_StoreCode" = \'' . $store . '\'
              GROUP BY CAST(WASTEH."U_DocDate" AS DATE),WASTEH."U_StoreCode",WASTEH."DocEntry",WASTEH."DocNum",
              WASTEH."U_Remarks",
              WASTEH."U_ApprStatus",CASE
        WHEN WASTEH."CreateDate" != WASTEH."UpdateDate" THEN \'Yes\'
        ELSE \'No\' 
    END
              ORDER BY CAST(WASTEH."U_DocDate" AS DATE),WASTEH."U_StoreCode" ASC;';

              //               INNER JOIN (
              //   SELECT "U_DocDate","U_StoreCode","U_Remarks",MAX("DocEntry") AS "MAX_DocEntry"  FROM 
              //   "MRN_LIVE"."@ST_GIW_DOCWEBH" WHERE "U_DocDate" BETWEEN \'' . $startdate . '\' AND \'' . $enddate . '\' AND "U_StoreCode" = \'' . $store . '\'
              //   GROUP BY "U_DocDate","U_StoreCode","U_Remarks"
              // ) C ON WASTEH."U_DocDate"=C."U_DocDate" AND WASTEH."U_StoreCode"=C."U_StoreCode" AND WASTEH."DocEntry"=C."MAX_DocEntry"

              $resulth = odbc_exec($conn, $queryh);
              if (!$resulth) {
                  die("Query gagal dijalankan: " . odbc_errormsg());
              }
              $no = 0;
              while ($e = odbc_fetch_array($resulth)) {
                  $no++;
                  $data .= "<tr>
                              <td>" . $no . "</td>
                              <td><p>" . $e['U_DocDate'] . "</p> </td>
                              <td><p>" . $e['DocNum'] . "</p> </td>
                              <td><p>" . $e['U_StoreCode'] . "</p> </td>
                              <td><p>" . $e['U_Remarks'] . "</p> </td>
                              <td><p>" . $e['U_ApprStatus'] . "</p> </td>
                              <td><p>" . $e['DTW'] . "</p> </td>
                              <td><a class='btn btn-danger' href='WasteReportProses.php?id=".$e['DocEntry']."'>View</a></td>
                          </tr>";
              }
              $data .= "</tbody></table></div>";
}
?>
<style>
    /* Style untuk form validasi */
    .was-validated .form-control:invalid {
        border-color: red;
    }

    /* Style untuk textarea keterangan */
    #keterangan:invalid {
        border-color: red;
    }
           /* Make the table responsive with scroll */
           .table-responsive {
            max-height: 700px;
            overflow-y: auto;
            position: relative;
        }

        /* Style to freeze the header */
        .table-responsive thead th {
            position: sticky;
            top: 0;
            background-color: #ffffff;
            z-index: 2; /* Ensure it stays above the rows */
        }

        /* Optional: add a border shadow to make the freeze effect more visible */
        .table-responsive thead th {
            box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        }
</style>
<div class="container1">
 <div class="row">

<div class="col-sm-2">
</br>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">

<span style="font-size:18px;"><b>* Waste Report</b></span>
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
$hostName = "localhost";
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

    echo'<div class="form-group">
    <label for="exampleInputPassword1">Store</label>
  <input type="text" name="store" class="form-control" value="'.$_SESSION['nama'].'" required readonly>
  </div>';

}
?>


  <div class="form-group">
    <label for="exampleInputPassword1">From</label>
  <input type="text" name="startdate" class="form-control start_date" required readonly value="<?php echo isset($_SESSION['startdate']) ? $_SESSION['startdate'] : date('Y-m-d'); ?>">
  </div>

  <div class="form-group">
    <label for="exampleInputPassword1">To</label>
  <input type="text" name="enddate" class="form-control end_date" required readonly value="<?php echo isset($_SESSION['enddate']) ? $_SESSION['enddate'] : date('Y-m-d'); ?>">
  </div>


  <button type="submit" name="showData" class="btn btn-primary">Search</button>
</form>



 </div>

 <div class="col-sm-10">
  <br><br><br>
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
         $('.start_date,.end_date').datepicker({
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: "yy-mm-dd"
              });

 </script>

</body>
</html>       