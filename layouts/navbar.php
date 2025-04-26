<nav class="navbar navbar-expand-lg navbar-light bg-warning">
        <a class="navbar-brand" href="#" style="font-size: 30px;"><b>INVENTORI</b></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto topnav">
        <?php
      if($_SESSION["role_id"] == '6' || $_SESSION["role_id"] == '4'){
      ?>
     <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b>Formulir Permintaan</b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <a class="nav-link" href="tps.php"><b>Permintaan Transfer Putus Toko</b></a>
        <a class="nav-link" href="tbs.php"><b>Permintaan Transfer Balik Toko</b></a>
         <a class="nav-link" href="retur.php"><b>Permintaan Retur Barang Toko</b></a>
         <a class="nav-link" href="assets.php"><b>Permintaan Asset</b></a>
          <!-- <a class="nav-link" href="uploadorder.php"><b>Upload Pemesanan Toko</b></a> -->
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b>Daftar Permintaan Barang</b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <a class="nav-link" href="listrequest.php"><b>Permintaan Barang Transfer Putus Toko</b></a>
        <a class="nav-link" href="listrequesttbs.php"><b>Permintaan Barang Transfer Balik Toko</b></a>
        </div>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b>Daftar Persetujuan Barang</b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <a class="nav-link" href="listapproverequest.php"><b>Persetujuan Barang Transfer Putus Toko</b></a>
        <a class="nav-link" href="listapproverequesttbs.php"><b>Persetujuan Barang Transfer Balik Toko</b></a>
        </div>
      </li>
      <li class="nav-item  <?php if($halaman == "listreturn") echo "active"; ?>">
        <a class="nav-link" href="listreturn.php"><b>Daftar Retur Barang </b></a>
      </li>
      <li class="nav-item  <?php if($halaman == "listrequestassets") echo "active"; ?>">
        <a class="nav-link" href="listrequestassets.php"><b>Daftar Asset Barang </b></a>
      </li>
      <li class="nav-item dropdown <?php if($halaman == "report") echo "active"; ?>">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b>Laporan</b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <a class="nav-link" href="reporttps.php"><b>Laporan Transfer Putus Toko</b></a>
        <a class="nav-link" href="reporttbs.php"><b>Laporan Transfer Balik Toko</b></a>
        <a class="nav-link" href="reportrtr.php"><b>Laporan Retur Barang Toko</b></a>
        <a class="nav-link" href="reportordertokobyck.php"><b>Laporan Pemesanan Toko (CK)</b></a>
        <a class="nav-link" href="WasteReport.php"><b>Laporan Waste</b></a>
        </div>
      </li>
      <li class="nav-item  <?php if($halaman == "resetsandi") echo "active"; ?>">
        <a class="nav-link" href="changepassword.php"><b>Reset Sandi </b></a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b><?php echo  $_SESSION["nama"]; ?></b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="logout.php">Keluar</a>
        </div>
      </li>
      <?php
      }else if($_SESSION["role_id"] == '11'){
      ?>
      <li class="nav-item  <?php if($halaman == "listrequest") echo "active"; ?>">
        <a class="nav-link" href="listrequest.php"><b>Daftar Permintaan Barang </b></a>
      </li>
      <li class="nav-item  <?php if($halaman == "listrequestassets") echo "active"; ?>">
        <a class="nav-link" href="listrequestassets.php"><b>Daftar Asset Barang </b></a>
      </li>
      <li class="nav-item  <?php if($halaman == "listreturn") echo "active"; ?>">
        <a class="nav-link" href="listreturn.php"><b>Daftar Retur Barang </b></a>
      </li>
      <li class="nav-item  <?php if($halaman == "masteritem") echo "active"; ?>">
        <a class="nav-link" href="masteritem.php"><b>Master Barang</b></a>
      </li>
      <li class="nav-item dropdown <?php if($halaman == "report") echo "active"; ?>">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b>Laporan</b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <a class="nav-link" href="reporttps.php"><b>Laporan Transfer Putus Toko</b></a>
        <a class="nav-link" href="reportrtr.php"><b>Laporan Retur Barang Toko</b></a>
         <a class="nav-link" href="reportitem.php"><b>Master Barang</b></a>
        <a class="nav-link" href="ReportOrderCK.php"><b>Laporan Pemesanan Toko (CK) Excel</b></a>
        <a class="nav-link" href="ReportThowingExcel.php"><b>Laporan Thowing Toko (CK) Excel</b></a>
        </div>
      </li>
      <li class="nav-item  <?php if($halaman == "resetsandi") echo "active"; ?>">
        <a class="nav-link" href="changepassword.php"><b>Reset Sandi </b></a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b><?php echo  $_SESSION["nama"]; ?></b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="logout.php">Keluar</a>
        </div>
      </li>
      <?php
      }else if($_SESSION["role_id"] == '12' || $_SESSION["role_id"] == '5'){
      ?>


      <li class="nav-item  <?php if($halaman == "listreturn") echo "active"; ?>">
        <a class="nav-link" href="listreturn.php"><b>Daftar Retur Barang </b></a>
      </li>
      <li class="nav-item  <?php if($halaman == "listrequestassets") echo "active"; ?>">
        <a class="nav-link" href="listrequestassets.php"><b>Daftar Asset Barang </b></a>
      </li>
     <li class="nav-item  <?php if($halaman == "masteritem") echo "active"; ?>">
        <a class="nav-link" href="masteritem.php"><b>Master Barang</b></a>
      </li>
      <li class="nav-item dropdown <?php if($halaman == "report") echo "active"; ?>">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b>Laporan</b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <a class="nav-link" href="reportrtr.php"><b>Laporan Retur Barang Toko</b></a>
         <a class="nav-link" href="reportitem.php"><b>Master Barang</b></a>
        </div>
      </li>
      <li class="nav-item  <?php if($halaman == "resetsandi") echo "active"; ?>">
        <a class="nav-link" href="changepassword.php"><b>Reset Sandi </b></a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b><?php echo  $_SESSION["nama"]; ?></b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="logout.php">Keluar</a>
        </div>
      </li>
      <?php
      }else if($_SESSION["role_id"] == '14' || $_SESSION["role_id"] == '15' || $_SESSION["role_id"] == '16' ){
      ?>


<li class="nav-item  <?php if($halaman == "listreturn") echo "active"; ?>">
        <a class="nav-link" href="listrequestassets.php"><b>Daftar Assets Barang </b></a>
      </li>
      <li class="nav-item  <?php if($halaman == "resetsandi") echo "active"; ?>">
        <a class="nav-link" href="changepassword.php"><b>Reset Sandi </b></a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b><?php echo  $_SESSION["nama"]; ?></b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="logout.php">Keluar</a>
        </div>
      </li>
      <?php
      }else if($_SESSION["role_id"] == '13'){
      ?>

      <li class="nav-item  <?php if($halaman == "listrequestassets") echo "active"; ?>">
        <a class="nav-link" href="listrequestassets.php"><b>Daftar Assets Barang </b></a>
      </li>
      <li class="nav-item  <?php if($halaman == "listapprovereturn") echo "active"; ?>">
        <a class="nav-link" href="listapprovereturn.php"><b>Daftar Retur Barang </b></a>
      </li>
      <li class="nav-item dropdown <?php if($halaman == "report") echo "active"; ?>">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b>Laporan</b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <a class="nav-link" href="reportrtr.php"><b>Laporan Retur Barang Toko</b></a>
        </div>
      </li>
      <li class="nav-item  <?php if($halaman == "resetsandi") echo "active"; ?>">
        <a class="nav-link" href="changepassword.php"><b>Reset Sandi </b></a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b><?php echo  $_SESSION["nama"]; ?></b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="logout.php">Keluar</a>
        </div>
      </li>


      <?php
      }else if($_SESSION["role_id"] == '8' || $_SESSION["role_id"] == '9' || $_SESSION["role_id"] == '18'  ){
      ?>
        <li class="nav-item  <?php if($halaman == "listrequestassets") echo "active"; ?>">
        <a class="nav-link" href="listrequestassets.php"><b>Daftar Assets Barang </b></a>
      </li>
        <li class="nav-item dropdown <?php if($halaman == "report") echo "active"; ?>">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b>Laporan</b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
        <a class="nav-link" href="reporttps.php"><b>Laporan Transfer Putus Toko</b></a>
        <a class="nav-link" href="reporttbs.php"><b>Laporan Transfer Balik Toko</b></a>
         <a class="nav-link" href="reportrtr.php"><b>Laporan Retur Barang Toko</b></a>
         <a class="nav-link" href="reportordertoko.php"><b>Laporan Pemesanan Toko</b></a>
         <a class="nav-link" href="reportwaste.php"><b>Laporan Waste</b></a>
        </div>
      </li>
      <li class="nav-item  <?php if($halaman == "resetsandi") echo "active"; ?>">
        <a class="nav-link" href="changepassword.php"><b>Reset Sandi </b></a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <b><?php echo  $_SESSION["nama"]; ?></b>
        </a>
        <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
          <a class="dropdown-item" href="logout.php">Keluar</a>
        </div>
      </li>
      <?php
      }
      ?>
    </ul>
        </div>

            

    </nav>