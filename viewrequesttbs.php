<?php
include "layouts/header.php";
$id=$_GET['id'];
$sqlheader = "SELECT *,convert(char(10),reqtb_date,126) req_date FROM header_tb a
 left join mst_req_type b on a.reqtb_type=b.id_mst_type
 left join mst_req_type_item  c on a.reqtb_item_type=c.id_mst_type_item
 where id_tb='$id'";
$stmtheader = sqlsrv_query( $conn, $sqlheader );
if( $stmtheader === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowheader = sqlsrv_fetch_array( $stmtheader, SQLSRV_FETCH_ASSOC);
include "layouts/navbar.php";
?>

<div class="container1">
 <div class="row">

<div class="col-sm-12" style="margin-top: 26px;" >
<span style="font-size:18px;"><b>* View Permintaan Transfer Balik Store <?php echo $rowheader['reqtb_code']; ?></b></span>
<br><br><br>

<form>
        <fieldset >
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail"  value="<?php echo $rowheader['req_date']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Jenis Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail"  value="<?php echo $rowheader['req_type_name']; ?>" readonly>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label" >Jenis Barang</label>
                <div class="col-sm-3">
                <input type="text" class="form-control" id="inputEmail"  value="<?php echo $rowheader['req_type_name_item']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Toko Pengirim</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail"  value="<?php echo $rowheader['reqtb_destination']; ?>" readonly>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Alasan Permintaan</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail"  value="<?php echo $rowheader['reqtb_reason']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan Permintaan</label>
                <div class="col-sm-3">
               <textarea name="note_request" class="form-control" id="note_request" cols="10" rows="5" readonly><?php echo $rowheader['reqtb_note']; ?>
               </textarea>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Status Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqtb_destination_approve']; ?>" readonly>
                </div>
            </div> -->
        </fieldset>

<br><br>

<div style="overflow-x:auto;">
<table id="example" class="table table-striped table-bordered table-sm" cellspacing="0"
  width="100%" >

                <tr>
                 <th rowspan="2">No</th>
                 <th rowspan="2"> Pengembalian Ke </th>
                   <th rowspan="2">Kode Barang</th>
                   <th rowspan="2">Nama Barang</th>
                    <th rowspan="2">Satuan</th>
                    <th rowspan="2">Jenis Barang</th>
                    <th rowspan="2">Alasan</th>
                    <th rowspan="2">Jumlah Permintaan</th>
                    <th rowspan="2">Jumlah Disetujui</th>
                    <th rowspan="2">Kadaluarsa Disetujui</th>
                    <th rowspan="2">Keterangan Disetujui</th>
                    <th rowspan="2">Jumlah Verfikasi </th>
                    <th rowspan="2">Keterangan Verfikasi</th>
                    <th rowspan="2">Jumlah Pengembalian </th>
                    <th rowspan="2">Jumlah Verifikasi Pengembalian </th>
                    <th rowspan="2">Selisih Pengembalian </th>
                    <th rowspan="2">No Dokumen TPS</th>
                </tr>
                <!-- <tr>
                    <th>Bagus</th>
                    <th>Tidak Bagus</th>
                    </tr>     -->

                </thead>
    <tbody>

        <p><b>Cetak Surat Jalan : </b></p>
<?php
$sqlcat = "SELECT header_idrtrtb,flag,rtrtbflag_tp,rtrtb_doktp from detail_returntb where  header_idrtrtb ='$id'
 GROUP BY header_idrtrtb,flag,rtrtbflag_tp,rtrtb_doktp";
$stmtcat = sqlsrv_query( $conn, $sqlcat );
if( $stmtcat === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$no=0;
while($rowcat = sqlsrv_fetch_array( $stmtcat, SQLSRV_FETCH_ASSOC)){
 $no++;
        if($rowcat['rtrtbflag_tp']==1){
            $halamancetak ='cetakpermintaan.php?id='.$rowcat['rtrtb_doktp'].'';         
            $ket ='Cetak Transfer Putus';      
        }else{ 
            $halamancetak ='cetakpengembaliantbs.php?id='.$rowcat['header_idrtrtb'].'&flag='.$rowcat['flag'].'';
            $ket ='Cetak Pengembalian';      
        }
?>

 
    <?php
                if($rowheader['reqtb_user_retur']=='Approved' OR $rowheader['reqtb_user_retur']=='Pending' OR $rowheader['reqtb_user_retur']=='Verifikasi'){

                ?>
                  <p><?php echo $no; ?>.    <a data-toggle="tooltip" title="Cetak Dokumen" class="badge badge-success" href="<?php echo $halamancetak; ?>"><b> <?php echo $ket; ?> #<?php echo $rowcat['flag']; ?></b></a></p>   
                <?php
                }
                ?>


<?php
}
?>

<?php

        // $sqldetail = "SELECT 
        // a.id_detailtb,
        // a.header_idtb,
        // a.tbitem_id,
        // a.tbitem_code,
        // a.tbitem_name,
        // a.tbitem_uom,
        // a.tbitem_cat,
        // a.tbitem_reason,
        // a.tbitem_qty,
        // a.tbitem_qty_approve,
        // a.tbitem_qty_verifikasi,
        // b.sumretur,
        // b.sumreturverifikasi,
        // CONVERT ( CHAR ( 10 ), a.tbitem_expired, 126 ) expiredpeminjaman,
        // a.tbitem_remarks,
        // a.tbitem_remarks_approve,
        // a.tbitem_remarks_verifikasi,
        // c.pengembalianke,
        // (b.sumreturverifikasi - a.tbitem_qty_verifikasi) as selisi
        // FROM
        //     detail_tb a 
        // LEFT JOIN (
        // SELECT header_idrtrtb,header_detailid,rtrtbitem_id,sum(rtrtbitem_qty_retur) sumretur,sum(rtrtbitem_qty_retur_verifikasi) as sumreturverifikasi from detail_returntb GROUP BY header_idrtrtb,header_detailid,rtrtbitem_id
        // ) as b
        // ON a.header_idtb=b.header_idrtrtb and a.id_detailtb=b.header_detailid and a.tbitem_id=b.rtrtbitem_id
        // LEFT JOIN (
        // SELECT header_idrtrtb,header_detailid,rtrtbitem_id,max(flag) as pengembalianke from detail_returntb 
        //     GROUP BY header_idrtrtb,header_detailid,rtrtbitem_id
        // ) as c
        // ON a.header_idtb=c.header_idrtrtb and a.id_detailtb=c.header_detailid and a.tbitem_id=c.rtrtbitem_id
        // WHERE
        //     header_idtb ='$id'";

        $sqldetail="SELECT 
        a.id_detailtb,
        a.header_idtb,
        a.tbitem_id,
        a.tbitem_code,
        a.tbitem_name,
        a.tbitem_uom,
        a.tbitem_cat,
        a.tbitem_reason,
        a.tbitem_qty,
        a.tbitem_qty_approve,
        a.tbitem_qty_verifikasi,
        b.rtrtbitem_qty_retur,
        b.rtrtbitem_qty_retur_verifikasi,
        CONVERT ( CHAR ( 10 ), a.tbitem_expired, 126 ) expiredpeminjaman,
        a.tbitem_remarks,
        a.tbitem_remarks_approve,
        a.tbitem_remarks_verifikasi,
        b.rtrtb_doktp,
        (b.rtrtbitem_qty_retur - b.rtrtbitem_qty_retur_verifikasi) as selisi,
				flag as pengembalianke
        FROM
            detail_tb a 
        LEFT JOIN (
        SELECT header_idrtrtb,header_detailid,rtrtbitem_id,rtrtbitem_qty_retur,rtrtbitem_qty_retur_verifikasi,flag,rtrtb_doktp from detail_returntb GROUP BY header_idrtrtb,header_detailid,rtrtbitem_id,rtrtbitem_qty_retur,rtrtbitem_qty_retur_verifikasi,flag,rtrtb_doktp
        ) as b
        ON a.header_idtb=b.header_idrtrtb and a.id_detailtb=b.header_detailid and a.tbitem_id=b.rtrtbitem_id
        WHERE
            header_idtb ='$id'
						order by flag asc";
        $stmtdetail = sqlsrv_query( $conn, $sqldetail );
        if( $stmtdetail === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $no=0;
        while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){
            // $fixqtyver =0;
            // $fixqtyver = $rowdetail['tbitem_qty_verifikasi_good'] + $rowdetail['tbitem_qty_verifikasi_not_good'];
        $no++;

    ?>
    <tr>
              <td scope="row"><?php echo $no; ?></td>
              <td align="left"><?php echo $rowdetail['pengembalianke']; ?>
              <td class="text-muted"><?php echo $rowdetail['tbitem_code']; ?>
    </td>
              <td align="left"><?php echo htmlspecialchars_decode($rowdetail['tbitem_name']); ?></td>
              <td align="left"><?php echo $rowdetail['tbitem_uom']; ?></td>
              <td align="left"><?php echo $rowdetail['tbitem_cat']; ?></td>
              <td align="left"><?php echo $rowdetail['tbitem_reason']; ?></td>
              <td align="left"><?php echo number_format($rowdetail['tbitem_qty'],2,'.',','); ?></td> 
              <td align="left"><?php echo number_format($rowdetail['tbitem_qty_approve'],2,'.',','); ?></td>
              <td align="left"><?php echo $rowdetail['expiredpeminjaman']; ?></td>
              <td align="left"><?php echo $rowdetail['tbitem_remarks_approve']; ?></td>
              <td align="left"><?php echo number_format($rowdetail['tbitem_qty_verifikasi'],2,'.',','); ?></td>
              <td align="left"><?php echo $rowdetail['tbitem_remarks_verifikasi']; ?></td>
              <td align="left"><?php echo  number_format($rowdetail['rtrtbitem_qty_retur'],2,'.',','); ?></td>
              <td align="left"><?php echo number_format($rowdetail['rtrtbitem_qty_retur_verifikasi'],2,'.',','); ?></td>
<!--               <br>
                <?php
                if($rowheader['reqtb_user_retur']=='Approved' OR $rowheader['reqtb_user_retur']=='Pending' OR $rowheader['reqtb_user_retur']=='Verifikasi'){
                    if($rowdetail['pengembalianke'] !=''){
                ?>
                   <a data-toggle="tooltip" title="Cetak Dokumen" class="badge badge-success" href="cetakpengembaliantbs.php?id=<?php echo $rowdetail['header_idtb']; ?>&flag=<?php echo $rowdetail['pengembalianke']; ?>"><b>Cetak Pengembalian</b></a>    
                <?php
                }
                }
                ?>
                 -->
        </td>
              <td align="left"><?php echo number_format($rowdetail['selisi'],2,'.',','); ?></td>
              <td align="left"><?php echo $rowdetail['rtrtb_doktp']; ?></td>
            </tr>

    <?php
    }
    ?>

    </tbody>

</table> 
</div>
<br> <br> <br>       
        
        <!-- <div align="right">    
        <br>

        <div class="col-sm-3">
        <select name="status_request" id="status_request" class="form-control">
        <option value="">-- Pilih Status Permintaan --</option>
        <option value="Approved">Di Setujui</option>
        <option value="Reject">Di Tolak</option>
        </select>
        <br>   
<button type="button" class="btn btn-primary" ><b>Proses Permintaan</b></button>  
<br><br><br>  
        </div>    

        </div> -->

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
 <script src="js/jquery-ui.js"></script>
 <script src="js/sweetalert2.all.min.js"></script>
<script>


</script>
</body>
</html>       