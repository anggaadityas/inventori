<?php
session_start();
error_reporting(0);
include "db.php";

$divisi = $_POST['divisi'];
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=report_item" . $divisi . ".xls");
header('Cache-Control: max-age=0');

?>

<!DOCTYPE html>
<html>

<head>
  <style>
    table {
      font-family: arial, sans-serif;
      border-collapse: collapse;
      width: 100%;
    }

    td,
    th {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 5px;
    }

    tr:nth-child(even) {
      background-color: #dddddd;
    }
  </style>
</head>

<body>

  <table>
    <tr>
      <th>Jenis</th>
      <th>Divisi</th>
      <th>Tipe</th>
      <th>Kode Barang</th>
      <th>Nama Barang</th>
      <th>Satuan</th>
      <th>Kategori Barang</th>
      <th>Kondisi</th>
      <th>Kadarluarsa</th>
      <th>Status</th>
    </tr>

    <?php

    if ($divisi == 'CK') {
      $fixdivisi = "(a.div_name ='CK' OR a.div_name ='STORE')";
    } else {
      $fixdivisi = "a.div_name ='$divisi'";
    }


    $sql = "SELECT 
        a.id_mst_item,
        b.req_type_name,
        a.div_name,
        CASE
        WHEN sap_flag=1 THEN 'System'
        WHEN sap_flag=2 THEN 'Non System'
        WHEN sap_flag=3 THEN 'Wadah'
        WHEN sap_flag=4 THEN 'Damage'
        ELSE '' END as sap_flag,
        a.item_cat,
        a.item_code,
        a.item_name,
        a.item_uom,
        CASE
        WHEN kondisi_flag=0 THEN 'Good & Non Good'
        WHEN kondisi_flag=1 THEN 'Good'
        WHEN kondisi_flag=2 THEN 'Non Good'
        ELSE '' END as kondisi_flag,
          CASE
        WHEN exp_flag=0 THEN 'Tidak Ada Kadarluarsa'
        WHEN exp_flag=1 THEN 'Kadarluarsa'
        ELSE '' END as exp_flag,
    a.active,
        ROW_NUMBER() OVER (ORDER BY id_mst_item desc) as rowNum 
      FROM mst_item a inner join mst_req_type b on a.item_type=b.id_mst_type
      where $fixdivisi order by req_type_name,item_code asc ";
    $stmtdetail = sqlsrv_query($conn, $sql);
    if ($stmtdetail === false) {
      die(print_r(sqlsrv_errors(), true));
    }
    $no = 0;
    while ($row = sqlsrv_fetch_array($stmtdetail, SQLSRV_FETCH_ASSOC)) {


      if ($row['active'] == 0) {
        $status = 'Aktif';
      } elseif ($row['active'] == 1) {
        $status = 'Tidak Aktif';
      } else {
        $status = '';
      }


      echo " <tr>
    <td>" . $row['req_type_name'] . "</td>
    <td>" . $row['div_name'] . "</td>
    <td>" . $row['sap_flag'] . "</td>
    <td>" . $row['item_code'] . "</td> 
    <td>" . $row['item_name'] . "</td>
    <td>" . $row['item_uom'] . "</td>
    <td>" . $row['item_cat'] . "</td>
    <td>" . $row['kondisi_flag'] . "</td>
    <td>" . $row['exp_flag'] . "</td>
    <td>" . $status . "</td>
  </tr>";
    }


    ?>

    <!-- <?php echo  $sql; ?>   -->

  </table>


</body>

</html>