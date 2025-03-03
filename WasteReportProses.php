<?php
$halaman = "viewwaste";
include "layouts/header.php";
include "layouts/navbar.php";
$dsn = "hanamrnprod";
$user = "DBADMIN";
$password = "Passw0rd";

$conn = odbc_connect($dsn, $user, $password);

// if (!$conn) {
//     exit("Connection failed: " . odbc_errormsg());
// } else {
//     echo "Connected successfully!";
// }

$DocEntry =$_GET['id'];
$Fstore =$_SESSION['nama'];

if($Fstore =='T01'){
    $store ='F01';
}elseif ($Fstore =='T02'){
     $store ='F02';
}else{
    $store =$_SESSION['nama'];
}


$queryh = 'SELECT CAST(WASTEH."U_DocDate" AS DATE) "U_DocDate",WASTEH."U_StoreCode",WASTEH."DocEntry",WASTEH."DocNum",WASTEH."U_Remarks",
WASTEH."U_ApprStatus"
FROM "MRN_LIVE"."@ST_GIW_DOCWEBH" WASTEH 
INNER JOIN (
	SELECT "U_DocDate","U_StoreCode","U_Remarks",MAX("DocEntry") AS "MAX_DocEntry"  FROM 
	"MRN_LIVE"."@ST_GIW_DOCWEBH" WHERE "DocEntry" =  ' . $DocEntry . ' AND "U_StoreCode" = \'' . $store . '\'
	GROUP BY "U_DocDate","U_StoreCode","U_Remarks"
) C ON WASTEH."U_DocDate"=C."U_DocDate" AND WASTEH."U_StoreCode"=C."U_StoreCode" AND WASTEH."DocEntry"=C."MAX_DocEntry"
WHERE 
WASTEH."DocEntry"= ' . $DocEntry . ' AND WASTEH."U_StoreCode" = \'' . $store . '\'
GROUP BY CAST(WASTEH."U_DocDate" AS DATE),WASTEH."U_StoreCode",WASTEH."DocEntry",WASTEH."DocNum",
WASTEH."U_Remarks",
WASTEH."U_ApprStatus"
ORDER BY CAST(WASTEH."U_DocDate" AS DATE),WASTEH."U_StoreCode" ASC;';


$resulth = odbc_exec($conn, $queryh);

if (!$resulth) {
    die("Query gagal dijalankan: " . odbc_errormsg());
}
$rowh = odbc_fetch_array($resulth);

$queryd= 'SELECT 
    CAST(WASTEH."U_DocDate" AS DATE) AS "U_DocDate",
    WASTEH."U_StoreCode",
    WASTEH."DocEntry",
    WASTEH."DocNum",
    WASTEH."U_Remarks",
    WASTEH."U_ApprStatus",
    WASTED."LineId",
    WASTED."U_ItemCode",
    WASTED."U_ItemDesc",
    WASTED."U_UomCode",
    WASTED."U_InvUom",
    WASTED."U_IssueType",
    WASTED."U_Qty",
    WASTED."U_InvQty"
FROM "MRN_LIVE"."@ST_GIW_DOCWEBH" WASTEH 
INNER JOIN "MRN_LIVE"."@ST_GIW_DOCWEBD" WASTED 
ON WASTEH."DocEntry"=WASTED."DocEntry"
INNER JOIN (
	SELECT "U_DocDate","U_StoreCode","U_Remarks",MAX("DocEntry") AS "MAX_DocEntry"  FROM 
	"MRN_LIVE"."@ST_GIW_DOCWEBH" 
    WHERE "DocEntry"= ' . $DocEntry . ' AND "U_StoreCode" = \'' . $store . '\'
	GROUP BY "U_DocDate","U_StoreCode","U_Remarks"
) C ON WASTEH."U_DocDate"=C."U_DocDate" AND WASTEH."U_StoreCode"=C."U_StoreCode" AND WASTEH."DocEntry"=C."MAX_DocEntry"
WHERE 
WASTEH."DocEntry"= ' . $DocEntry . ' AND WASTEH."U_StoreCode" = \'' . $store . '\' AND WASTED."U_IssueType" IN (
\'Operation - Filtering Minyak Tepung\',
\'Operation - Filtering Minyak Non Tepung\',
\'Operation - Filtering Minyak Mix\'
)
GROUP BY 
    CAST(WASTEH."U_DocDate" AS DATE),
    WASTEH."U_StoreCode",
    WASTEH."DocEntry",
    WASTEH."DocNum",
    WASTEH."U_Remarks",
    WASTEH."U_ApprStatus",
    WASTED."LineId",
    WASTED."U_ItemCode",
    WASTED."U_ItemDesc",
    WASTED."U_Qty",
    WASTED."U_UomCode",
    WASTED."U_InvQty",
    WASTED."U_InvUom",
     WASTED."U_IssueType"
    ORDER BY 
    CAST(WASTEH."U_DocDate" AS DATE),
    WASTEH."U_StoreCode",
    WASTED."LineId" 
    ASC;
';

$resultd = odbc_exec($conn, $queryd);

if (!$resultd) {
    die("Query gagal dijalankan: " . odbc_errormsg());
}

$queryd1='SELECT 
    CAST(WASTEH."U_DocDate" AS DATE) AS "U_DocDate",
    WASTEH."U_StoreCode",
    WASTEH."DocEntry",
    WASTEH."DocNum",
    WASTEH."U_Remarks",
    WASTEH."U_ApprStatus",
    WASTED."U_ItemCode",
    WASTED."U_ItemDesc",
    WASTED."U_UomCode",
    SUM(CASE WHEN WASTED."U_IssueType" = \'Operation - Waste Outlet - Overholding Time\' THEN WASTED."U_Qty" ELSE 0 END) AS "Overholding_Qty",
    SUM(CASE WHEN WASTED."U_IssueType" = \'Operation - Waste Outlet - Human Error\' THEN WASTED."U_Qty" ELSE 0 END) AS "Human_Error_Qty",
    SUM(CASE WHEN WASTED."U_IssueType" = \'Operation - Waste Outlet - Sisa Closing\' THEN WASTED."U_Qty" ELSE 0 END) AS "Sisa_Closing_Qty",
    (SUM(CASE WHEN WASTED."U_IssueType" = \'Operation - Waste Outlet - Overholding Time\' THEN WASTED."U_Qty" ELSE 0 END) +
    SUM(CASE WHEN WASTED."U_IssueType" = \'Operation - Waste Outlet - Human Error\' THEN WASTED."U_Qty" ELSE 0 END) +
    SUM(CASE WHEN WASTED."U_IssueType" = \'Operation - Waste Outlet - Sisa Closing\' THEN WASTED."U_Qty" ELSE 0 END)) AS "TotalWaste",
    SUM("U_InvQty") AS "SummaryInvQty",
    WASTED."U_InvUom"
FROM "MRN_LIVE"."@ST_GIW_DOCWEBH" WASTEH 
INNER JOIN "MRN_LIVE"."@ST_GIW_DOCWEBD" WASTED 
ON WASTEH."DocEntry" = WASTED."DocEntry"
INNER JOIN (
    SELECT "U_DocDate", "U_StoreCode","U_Remarks", MAX("DocEntry") AS "MAX_DocEntry"  
    FROM "MRN_LIVE"."@ST_GIW_DOCWEBH" 
   WHERE "DocEntry"= ' . $DocEntry . ' AND "U_StoreCode" = \'' . $store . '\'
    GROUP BY "U_DocDate", "U_StoreCode","U_Remarks"
) C 
ON WASTEH."U_DocDate" = C."U_DocDate" 
AND WASTEH."U_StoreCode" = C."U_StoreCode" 
AND WASTEH."DocEntry" = C."MAX_DocEntry"
WHERE WASTEH."DocEntry"= ' . $DocEntry . ' AND WASTEH."U_StoreCode" = \'' . $store . '\'
AND WASTED."U_IssueType" NOT IN (
\'Operation - Filtering Minyak Tepung\',
\'Operation - Filtering Minyak Non Tepung\',
\'Operation - Filtering Minyak Mix\'
)
GROUP BY 
    CAST(WASTEH."U_DocDate" AS DATE),
    WASTEH."U_StoreCode",
    WASTEH."DocEntry",
    WASTEH."DocNum",
    WASTEH."U_Remarks",
    WASTEH."U_ApprStatus",
    WASTED."U_ItemCode",
    WASTED."U_ItemDesc",
    WASTED."U_UomCode",
    WASTED."U_InvUom"
ORDER BY 
    CAST(WASTEH."U_DocDate" AS DATE),
    WASTEH."U_StoreCode", WASTED."U_ItemCode" ASC;';

    $resultd1 = odbc_exec($conn, $queryd1);

if (!$resultd1) {
    die("Query gagal dijalankan: " . odbc_errormsg());
}

if($rowh){

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
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4); /* Optional shadow */
}

/* Adjust the second header row's sticky position */
.table-responsive thead tr:nth-child(2) th {
    top: 36px; /* Set top value based on the height of the first row */
    background-color: #ffffff;
    z-index: 1; /* Adjust z-index for proper layering */
}

/* Optional: ensure column headers have a fixed width if needed
.table-responsive th, .table-responsive td {
    white-space: nowrap;  
} */

/* Adjust for better visibility when frozen */
.table-responsive thead th, .table-responsive thead tr:nth-child(2) th {
    border-bottom: 2px solid #dee2e6; /* Optional: Add a thicker border for visibility */
}
</style>
<div class="container1">
    <div class="row">
        <div class="col-sm-12" style="margin-top: 26px;">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#menu1"><b>Formulir Waste</b></a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane container active" id="menu1">
                        <br><br>
                        <fieldset>

                        <div class="form-group row">
                            <label for="tanggal_input" class="col-sm-2 col-form-label">Posting Date <?php echo $store; ?></label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="tanggal_input" value="<?php echo $rowh['U_DocDate']; ?>" id="tanggal_input" placeholder="Pilih Tanggal Input" readonly required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="docnum" class="col-sm-2 col-form-label">GI Waste No. </label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="docnum" id="docnum" value="<?php echo $rowh['DocNum']; ?>" placeholder="Pilih Tanggal Waste" readonly required>
                                <div class="invalid-feedback">DocNum harus diisi.</div>
                            </div>
                        </div>

                        <div class="form-group row store">
                            <label for="store" class="col-sm-2 col-form-label">Store</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="store" id="store" value="<?php echo $rowh['U_StoreCode']; ?>" readonly required>
                            </div>
                        </div>

                        <div class="form-group row store">
                            <label for="store" class="col-sm-2 col-form-label">Status</label>
                            <div class="col-sm-2">
                                <input type="text" class="form-control" name="status" id="status" value="<?php echo $rowh['U_ApprStatus']; ?>" readonly required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="keterangan" class="col-sm-2 col-form-label">Remarks</label>
                            <div class="col-sm-3">
                                <textarea name="keterangan" readonly class="form-control" id="keterangan" cols="10" rows="5" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="110"><?php echo $rowh['U_Remarks']; ?></textarea>
                            </div>
                        </div>
                    </fieldset>
                        <br>

                        <b>Filtering Minyak</b>
      <div class="table-responsive">
            <table class="table table-bordered table-striped"  style="width: 100%;">
            <thead>
            <tr>
                <th>No</th>
                <th>Item Code</th>
                <th>Item Description</th>
                <th>UoM</th>
                <th>Qty</th>
            </tr>
            </thead>
            <tbody>
<?php
$no=1;
$grandTotalQty = 0;
while ($rowd = odbc_fetch_array($resultd)) {
    echo "<tr>";
    echo "<td>" . $no . "</td>";
    echo "<td>" . $rowd['U_ItemCode'] . "</td>";
    echo "<td>" . $rowd['U_ItemDesc'] . "</td>";
    echo "<td>" . $rowd['U_UomCode'] . "</td>";
    echo "<td>" . number_format($rowd['U_Qty']) . "</td>";
    echo "</tr>";
    $no++;
    $grandTotalQty += $rowd['U_Qty'];
}
echo "<tr style='font-weight: bold;'>";
echo "<td colspan='4' class='text-center'>Grand Total</td>";
echo "<td>" . number_format($grandTotalQty) . "</td>";
echo "</tr>";
echo " </tbody></table></div>";
?>

<b>Non Filtering</b>

<div class="table-responsive">
            <table class="table table-bordered table-striped"  style="width: 100%;">
            <thead>
            <tr>
                <th rowspan='2'>No</th>
                <th rowspan='2'>Item Code</th>
                <th rowspan='2'>Item Description</th>
                <th colspan='5' class='text-center'>Waste Book</th>
                <th colspan='2' class='text-center'>Inventori</th>
                <tr>
                <th>UoM</th>
                <th>Overholding Time</th>
                <th>Human Error</th>
                <th>Sisa Closing</th>
                <th>Total Waste</th>
                <th>Qty</th>
                <th>UoM</th>
                 </tr>    
            </tr>
            </thead>
            <tbody>
<?php
$no=1;
$totalOverholding = 0;
$totalHumanError = 0;
$totalSisaClosing = 0;
$totalWaste = 0;
$totalSummaryInvQty = 0;
while ($rowd1 = odbc_fetch_array($resultd1)) {
    echo "<tr>";
    echo "<td>" . $no . "</td>";
    echo "<td>" . $rowd1['U_ItemCode'] . "</td>";
    echo "<td>" . $rowd1['U_ItemDesc'] . "</td>";
    echo "<td>" . $rowd1['U_UomCode'] . "</td>";
    echo "<td>" . number_format($rowd1['Overholding_Qty']) . "</td>";
    echo "<td>" . number_format($rowd1['Human_Error_Qty']) . "</td>";
    echo "<td>" . number_format($rowd1['Sisa_Closing_Qty']) . "</td>";
    echo "<td>" . number_format($rowd1['TotalWaste']) . "</td>";
    echo "<td>" . number_format($rowd1['SummaryInvQty'], 4, '.', '') . "</td>";
    echo "<td>" . $rowd1['U_InvUom'] . "</td>";
    echo "</tr>";
    $no++;
    $totalOverholding += $rowd1['Overholding_Qty'];
    $totalHumanError += $rowd1['Human_Error_Qty'];
    $totalSisaClosing += $rowd1['Sisa_Closing_Qty'];
    $totalWaste += $rowd1['TotalWaste'];
    $totalSummaryInvQty += $rowd1['SummaryInvQty'];
}
// Output the grand total row
// echo "<tr style='font-weight: bold;'>";
// echo "<td colspan='4' class='text-center'>Grand Total</td>";
// echo "<td>" . number_format($totalOverholding) . "</td>";
// echo "<td>" . number_format($totalHumanError) . "</td>";
// echo "<td>" . number_format($totalSisaClosing) . "</td>";
// echo "<td>" . number_format($totalWaste) . "</td>";
// echo "<td>" . number_format($totalSummaryInvQty, 4, '.', '') . "</td>";
// echo "<td></td>"; 
// echo "</tr>";
echo " </tbody></table></div>";
odbc_close($conn);
?>
 
                        <div align="left">
                            <br>
                            <a href="WasteReport.php" class="btn btn-primary"><b>Back</b></a>
                            <br><br><br>
                        </div>
                </div>
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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.all.min.js"></script>
<script src="js/jquery.inputmask.bundle.min.js" charset="utf-8"></script>
<?php
} else {
    echo '<div class="container1">
    <div class="row">
        <div class="col-sm-12" style="margin-top: 26px;">
        Data Waste Tidak Ada
         <div align="left">
                            <br>
                            <a href="WasteReport.php" class="btn btn-primary"><b>Back</b></a>
                            <br><br><br>
                        </div>
        </div>
        </div>';
}
?>