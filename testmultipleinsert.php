<?php

if(isset($_POST)){

$servername = "localhost";
$username = "root";
$password = "aas260993";
$dbname = "voucher_trial";
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
;

$querydatadb ="SELECT id_user FROM mst_user";
$stmtdatadb = mysqli_query($conn,$querydatadb);

// get data in database
while ($row = mysqli_fetch_array($stmtdatadb)) {
    $a[] = $row['id_user'];
}

// get data in form input
foreach($_POST['id_barang'] as $option => $opt){
    $b[]=$_POST['id_barang'][$option];
}

$del = array_diff(array_merge($a), array_merge($b));
$ins =array_diff(array_merge($b), array_merge($a));
$upd = array_intersect(array_merge($b), array_merge($a));

// update     
$sqldetailupdate = "";
foreach ($upd as $id) {

    $sqldetailupdate .= "UPDATE detail_tp SET 
    created_by='".htmlspecialchars(addslashes(trim(strip_tags($_POST['nama_barang'][$id]))))."'
    WHERE
     and tpitem_id='$id'";
    $sqldetailupdate .= ";";

}

$sqldetailfixupdate = rtrim($sqldetailupdate,";");
$stmt1 = mysqli_query($conn,$sqldetailfixupdate);

$sqldetailinsert = "INSERT INTO detail_returnck  (
    header_idrtn,
    created_by) values ";
    
// insert
foreach ($ins as $id) {
                   $sqldetailinsert .= "(
                   '$id',
                   'AAS')";
                   $sqldetailinsert .= ",";
      }
 

$sqlfixdetailinsert = rtrim($sqldetailinsert,",");
$stmt1 = mysqli_query($conn,$sqlfixdetailinsert);

echo $sqldetailfixupdate;
// var_dump($ins);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Document</title>
</head>
<body>
 <form action="" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
 <input type="text" name="id_barang[1]">
 <input type="text" name="nama_barang[1]"><br>
 <input type="text" name="id_barang[2]">
 <input type="text" name="nama_barang[2]"><br>
 <input type="text" name="id_barang[3]">
 <input type="text" name="nama_barang[3]">
 <button type="submit" value="cek">Proses</button>
 </form>
</body>
</html>