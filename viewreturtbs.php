<?php
include "layouts/header.php";
include "layouts/navbar.php";
$id=$_GET['id'];
$sqlheader = "SELECT *
FROM 
( 
      SELECT 
				a.id_tb,
				a.reqtb_code,
				convert(char(10),a.reqtb_date,126) req_date,
				b.req_type_name,
				c.req_type_name_item,
			  a.reqtb_user,
			  a.reqtb_destination,
				a.reqtb_reason,
				a.reqtb_note,
				a.reqtb_destination_approve,
				a.reqtb_user_verifikasi,
				reqtb_user_retur,
				reqtb_destination_retur_verifikasi,
				d.sumpeminjaman,
				d.sumpengembalian,
				COALESCE(d.sumpengembalian,0) - COALESCE(d.sumpeminjaman,0) as selisi,
                COALESCE(d.sumreturplus,0) - COALESCE(d.sumreturverifikasiplus,0) as kelebihan,
        ROW_NUMBER() OVER (ORDER BY id_tb desc) as rowNum 
      FROM header_tb a inner join mst_req_type b on a.reqtb_type=b.id_mst_type
			left join mst_req_type_item c on a.reqtb_item_type=c.id_mst_type_item
			LEFT JOIN (
                SELECT
        a.header_idtb,
				sum(a.tbitem_qty_verifikasi) sumpeminjaman,
				sum(b.sumreturverifikasi) sumpengembalian,
				sum(b.sumreturplus) sumreturplus,
				sum(b.sumreturverifikasiplus) sumreturverifikasiplus
    FROM
        detail_tb a
        LEFT JOIN (
									SELECT
											header_idrtrtb,
											header_detailid,
											rtrtbitem_id,
											SUM ( rtrtbitem_qty_retur ) AS sumretur,
											SUM ( rtrtbitem_qty_retur_verifikasi ) AS sumreturverifikasi,
											SUM ( rtrtbitem_qty_retur_plus ) AS sumreturplus,
											SUM ( rtrtbitem_qty_retur_verifikasi_plus ) AS sumreturverifikasiplus 
										FROM
											detail_returntb 
										GROUP BY
											header_idrtrtb,
											header_detailid,
											rtrtbitem_id
        ) AS b ON a.header_idtb= b.header_idrtrtb 
        AND a.id_detailtb= b.header_detailid 
        AND a.tbitem_id= b.rtrtbitem_id
				GROUP BY header_idtb
			) as d ON a.id_tb=d.header_idtb
) sub
 where id_tb='$id'";
$stmtheader = sqlsrv_query( $conn, $sqlheader );
if( $stmtheader === false) {
    die( print_r( sqlsrv_errors(), true) );
}
$rowheader = sqlsrv_fetch_array( $stmtheader, SQLSRV_FETCH_ASSOC);

// if($rowheader['reqtb_item_plus'] ==NULL){
//     $item_plus=0;
// }else{
//      $item_plus=$rowheader['reqtb_item_plus'];
// }

// if( $rowheader['reqtb_item_minus'] ==NULL){
//     $item_minus=0;
// }else{
//     $item_minus=$rowheader['reqtb_item_minus'];
// }

// $selisi = $item_plus + str_replace("-","",$item_minus);
?>

<div class="container1">
 <div class="row">

<div class="col-sm-12" style="margin-top: 26px;" >
<span style="font-size:18px;"><b>* View Pengembalian Barang Permintaan Transfer Balik Store <?php echo $rowheader['reqtb_code']; ?></b></span>
<br><br><br>

<form method="POST" id="pengembalian">
        <fieldset >
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Tanggal Permintaan</label>
                <div class="col-sm-3">
                <input type="hidden" class="form-control" id="inputEmail" name="reqtb_code"  value="<?php echo $rowheader['reqtb_code']; ?>" readonly>
                <input type="hidden" class="form-control" id="inputEmail" name="id_tb"  value="<?php echo $rowheader['id_tb']; ?>" readonly>
                    <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['req_date']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Jenis Permintaan</label>
                <div class="col-sm-3">
                    <input type="text" class="form-control"  id="inputEmail"  value="<?php echo $rowheader['req_type_name']; ?>" readonly>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label" >Jenis Barang</label>
                <div class="col-sm-3">
                <input type="text" class="form-control" id="inputEmail" value="<?php echo $rowheader['req_type_name_item']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Toko Asal</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail" name="reqtb_user" value="<?php echo $rowheader['reqtb_user']; ?>" readonly>
                </div>
            </div>
            <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Toko Tujuan</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="inputEmail" name="reqtb_destination" value="<?php echo $rowheader['reqtb_destination']; ?>" readonly>
                </div>
            </div>
            <!-- <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Alasan Permintaan</label>
                <div class="col-sm-2">
                <input type="text" name="alasan" class="form-control" id="inputEmail" value="<?php echo $rowheader['reqtb_reason']; ?>" readonly>
                </div>
            </div> -->
            <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Keterangan Permintaan</label>
                <div class="col-sm-3">
               <textarea name="note_request" class="form-control" name="keterangan" cols="10" rows="5" readonly><?php echo $rowheader['reqtb_note']; ?>
               </textarea>
                </div>
            </div>

                <div class="form-group row">
                <label for="inputbassword" class="col-sm-2 col-form-label">Tanggal Pengembalian</label>
                <div class="col-sm-2">
                <input type="text" class="form-control" id="tanggal_pengembalian" name="tanggal_pengembalian" required autocomplete="off">
                </div>
            </div>

          <!--    <div class="form-group row">
                <label for="inputEmail" class="col-sm-2 col-form-label">Status Permintaan</label>
                <div class="col-sm-3">
                <input type="text" name="status_destination_retur_verifikasi" value="<?php echo $rowheader['reqtb_destination_retur_verifikasi']; ?>" class="form-control" readonly>
               </textarea>
                </div>
            </div> -->

            <div class="form-group row" style="display: none;">
                <label for="inputbassword" class="col-sm-2 col-form-label">Balance Item</label>
                <div class="col-sm-2">
                <!-- <input type="text" class="form-control subTotalplus" id="inputEmail" name="itempluscal" value="<?php echo $rowheader['reqtb_item_plus']; ?>" readonly>
                  <input type="text" class="form-control subTotalminus" id="inputEmail" name="itemminuscal" value="<?php echo $rowheader['reqtb_item_minus']; ?>" readonly>
                 <input type="text" class="form-control" id="inputEmail" name="itemplus" value="<?php echo $rowheader['reqtb_item_plus']; ?>" readonly>
                   <input type="text" class="form-control" id="inputEmail" name="itemminus" value="<?php echo $rowheader['reqtb_item_minus']; ?>" readonly> -->
                   <input type="text" class="form-control selisi" id="inputEmail" name="selisi" value="<?php echo $rowheader['selisi']; ?>" readonly>
                </div>
            </div> 

       
        </fieldset>

<br><br>

<span style="font-size: 16px;"><b>* Checklist dibagian kolom nomor untuk melakukan pengembalian barang</b></span>
<br><br>

<div style="overflow-x:auto;">

   <table  class="table table-striped table-bordered dt-responsive nowrap">

                <tr>
                 <th>Nomor </th>
                   <th>Kode Barang</th>
                   <th>Nama Barang</th>
                    <th>Satuan</th>
                    <th>Jenis Barang</th>
                    <th>Alasan</th>
                    <th>Jumlah Peminjaman</th>
                    <th>Jumlah Sudah Dilakukan Pengembalian</th>
                    <th>Selisi Pengembalian</th>
                    <th>Jumlah Pengembalian</th>
                    <th>Tanggal Kadaluarsa</th>
                    <th>Keterangan Barang Pengembalian</th>
                </tr>
   

                </thead>
    <tbody>
<?php

        $sqldetail = "SELECT 
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
        b.sumretur,
        b.sumreturverifikasi,
        CONVERT ( CHAR ( 10 ), a.tbitem_expired, 126 ) expiredpeminjaman,
        a.tbitem_remarks,
        a.tbitem_remarks_approve,
        a.tbitem_remarks_verifikasi,
        c.pengembalianke,
        (COALESCE(b.sumreturverifikasi,0) - COALESCE(a.tbitem_qty_verifikasi,0)) as selisi,
        (COALESCE(a.tbitem_qty_verifikasi,0) - COALESCE(b.sumreturverifikasi,0)) as selisivalidasi,
        d.exp_flag
        FROM
            detail_tb a 
        LEFT JOIN (
        SELECT header_idrtrtb,header_detailid,rtrtbitem_id,sum(rtrtbitem_qty_retur) sumretur,sum(rtrtbitem_qty_retur_verifikasi) as sumreturverifikasi from detail_returntb GROUP BY header_idrtrtb,header_detailid,rtrtbitem_id
        ) as b
        ON a.header_idtb=b.header_idrtrtb and a.id_detailtb=b.header_detailid and a.tbitem_id=b.rtrtbitem_id
        LEFT JOIN (
        SELECT header_idrtrtb,header_detailid,rtrtbitem_id,max(flag) as pengembalianke from detail_returntb 
            GROUP BY header_idrtrtb,header_detailid,rtrtbitem_id
        ) as c
        ON a.header_idtb=c.header_idrtrtb and a.id_detailtb=c.header_detailid and a.tbitem_id=c.rtrtbitem_id
        inner join mst_item d on a.tbitem_id=d.id_mst_item
        WHERE
            header_idtb ='$id'";
        $stmtdetail = sqlsrv_query( $conn, $sqldetail );
        if( $stmtdetail === false) {
            die( print_r( sqlsrv_errors(), true) );
        }
        $no=0;
        while($rowdetail = sqlsrv_fetch_array( $stmtdetail, SQLSRV_FETCH_ASSOC)){
        $no++; 
        if($rowdetail['selisi']==0){
            $checkbox='';
        }else{

                if($rowdetail['sumretur'] !=''){
                    if($rowdetail['selisi']==0){
                       $checkbox='';
                    }else{
                        $checkbox="<input type='checkbox' class='check_box'
                        id='".$rowdetail['id_detailtb']."'
                        data-idreq='".$rowdetail['header_idtb']."'
                        data-idbarang='".$rowdetail['tbitem_id']."'
                        data-kodebarang='".$rowdetail['tbitem_code']."'
                        data-namabarang='".$rowdetail['tbitem_name']."'
                        data-uom='".$rowdetail['tbitem_uom']."'
                        data-cat='".$rowdetail['tbitem_cat']."'
                        data-reason='".$rowdetail['tbitem_reason']."'
                        data-qtyverifikasi='".number_format($rowdetail['tbitem_qty_verifikasi'],2,'.',',')."'
                        data-flagkadaluarsa='".$rowdetail['exp_flag']."'
                        data-qtyreturver='".number_format($rowdetail['sumreturverifikasi'],2,'.',',')."'
                        data-selisi='".number_format($rowdetail['selisi'],2,'.',',')."'
                        data-selisivalidasi='".number_format($rowdetail['selisivalidasi'],2,'.',',')."'
                        data-nourut='".$no."'
                        '".$no."'";
                    }
                }else{
                $checkbox="<input type='checkbox' class='check_box'
                id='".$rowdetail['id_detailtb']."'
                data-idreq='".$rowdetail['header_idtb']."'
                data-idbarang='".$rowdetail['tbitem_id']."'
                data-kodebarang='".$rowdetail['tbitem_code']."'
                data-namabarang='".$rowdetail['tbitem_name']."'
                data-uom='".$rowdetail['tbitem_uom']."'
                data-cat='".$rowdetail['tbitem_cat']."'
                data-reason='".$rowdetail['tbitem_reason']."'
                data-qtyverifikasi='".number_format($rowdetail['tbitem_qty_verifikasi'],2,'.',',')."'
                data-flagkadaluarsa='".$rowdetail['exp_flag']."'
                data-qtyreturver='".number_format($rowdetail['sumreturverifikasi'],2,'.',',')."'
                data-selisi='".number_format($rowdetail['selisi'],2,'.',',')."'
                data-selisivalidasi='".number_format($rowdetail['selisivalidasi'],2,'.',',')."'
                data-nourut='".$no."'
                '".$no."'";
                }
        }    

    ?>
    <tr>
              <td scope="row">
              <?php echo $checkbox; ?>
              <p> <b><?php echo $no; ?></b></p>
            </td>
              <td class="text-muted"><?php echo $rowdetail['tbitem_code']; ?></td>
              <td align="left"><?php echo htmlspecialchars_decode($rowdetail['tbitem_name']); ?></td>
              <td align="left"><?php echo $rowdetail['tbitem_uom']; ?></td>
              <td align="left"><?php echo $rowdetail['tbitem_cat']; ?></td> 
              <td align="left"><?php echo $rowdetail['tbitem_reason']; ?></td> 
              <td align="left"><?php echo number_format($rowdetail['tbitem_qty_verifikasi'],2,'.',','); ?></td> 
              <td align="left"><?php echo number_format($rowdetail['sumreturverifikasi'],2,'.',','); ?></td> 
              <td align="left"><?php echo number_format($rowdetail['selisi'],2,'.',','); ?></td> 
              <td align="left"></td>
              <td></td>
              <td align="left"></td>
            </tr>

    <?php
    }
    ?>

    </tbody>

</table> 

</div>
        
        <div align="right">    
 

        <div class="col-sm-3"> 

        <div class="form-group row">
                <label for="inputEmail" class="col-sm-12 col-form-label">Keterangan Status Permintaan</label>
                <div class="col-sm-12">
               <textarea name="note_request_approve" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110" class="form-control"  cols="10" rows="5"></textarea> 
                </div>
            </div>

       <select name="status_request" id="status_request" class="form-control" required style="display: none;">
        <option value="Approved" <?php if($rowheader['reqtb_user_retur']=="Approved") echo "selected"; ?>>Di Setujui</option>
        <!-- <option value="Reject" <?php if($rowheader['reqtb_user_retur']=="Reject") echo "selected"; ?>>Di Tolak</option> -->
        </select>
        <br>
<button type="submit" class="btn btn-primary" ><b>Proses Permintaan</b></button>  
<br><br><br>  
        </div>    

        </div>

 </div>

   </form>

   

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


$(document).on('click', '.check_box', function(){
        var html = '';
        var kadaluarsa = '';

        if(this.checked)
        {
         
            if($(this).data('flagkadaluarsa')==1){
                var kadaluarsa = '<input type="text"  name="expired_date['+$(this).attr('id')+']"  id="expired_'+$(this).attr('id')+'" class="datepickers form-control" placeholder="YYYY-MM-DD" autocomplete="off" required>';
            }else{
                var kadaluarsa ='<input type="text"  name="expired_date['+$(this).attr('id')+']"  id="expired_'+$(this).attr('id')+'" class="datepickers form-control" placeholder="YYYY-MM-DD" autocomplete="off" required>';
            }

            html = '<td><input type="checkbox" id="'+$(this).attr('id')+'"';
            html +='data-idbarang="'+$(this).data('idbarang')+'"';
            html +='data-kodebarang="'+$(this).data('kodebarang')+'"';
            html +='data-namabarang="'+$(this).data('namabarang')+'"'; 
            html +='data-uom="'+$(this).data('uom')+'"';
            html +='data-cat="'+$(this).data('cat')+'"';
            html +='data-reason="'+$(this).data('reason')+'"';
            html +='data-qtyverifikasi="'+$(this).data('qtyverifikasi')+'"';
            html +='data-flagkadaluarsa="'+$(this).data('flagkadaluarsa')+'"';
            html +='data-qtyreturver="'+$(this).data('qtyreturver')+'"';
            html +='data-selisi="'+$(this).data('selisi')+'"';
            html +='data-selisivalidasi="'+$(this).data('selisivalidasi')+'"';
            html +='data-nourut="'+$(this).data('nourut')+'"'; 
            html +='class="check_box" checked /> '+$(this).data('nourut')+'</td>';
            html += '<td>'+$(this).data("kodebarang")+'</td>';
            html += '<td>'+$(this).data("namabarang")+'</td>';
            html += '<td>'+$(this).data("uom")+'</td>';
            html += '<td>'+$(this).data("cat")+'</td>';
            html += '<td>'+$(this).data("reason")+'</td>';
            html += '<td>'+$(this).data("qtyverifikasi")+'</td>';
            html += '<td>'+$(this).data("qtyreturver")+'<br>';
            html += '<input type="checkbox" class="doktp" name="doktp['+$(this).attr('id')+']"  id="doktp_'+$(this).attr('id')+'"><p style="font-size:14px;"> <b>Dilakukan transfer putus?</b></p><br>';
            html +='<input type="text" autocomplete="off" class="form-control inputtp" id="inputtp_'+$(this).attr('id')+'"  style="display:none;" name="input_tp['+$(this).attr('id')+']"></td>';
            html +='<div id="select2_'+$(this).attr('id')+'" style="display:none;"><select class="form-control inputtp" id="inputtp_'+$(this).attr('id')+'" style="display:none;" name="input_tp['+$(this).attr('id')+']"></select></div></td>';
            html += '<td>'+$(this).data("selisi")+'</td>';
            html +='<td><input type="hidden" name="detail_idtb['+$(this).attr('id')+']" value="'+$(this).attr('id')+'">';
            html +='<input type="hidden" name="id_barang['+$(this).attr('id')+']" value="'+$(this).data('idbarang')+'">';
            html +='<input type="number"  min="'+$(this).data("selisivalidasi")+'" max="'+$(this).data("selisivalidasi")+'" name="qtyretur['+$(this).attr('id')+']"  style="width:70px" value="'+$(this).data("selisivalidasi")+'" class="form-control" autocomplete="off" required readonly/></td>';
            html +='<td>'+ kadaluarsa +'</td>';
            html +='<td><textarea name="keterangan_barangretur['+$(this).attr('id')+']" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength = "110" class="form-control"  cols="10" rows="5"></textarea><input type="hidden" name="hidden_id['+$(this).attr('id')+']" value="'+$(this).attr('id')+'" /></td>';
        }
        else
        {

            html = '<td><input type="checkbox" id="'+$(this).attr('id')+'"';
            html +='data-idbarang="'+$(this).data('idbarang')+'"';
            html +='data-kodebarang="'+$(this).data('kodebarang')+'"';
            html +='data-namabarang="'+$(this).data('namabarang')+'"'; 
            html +='data-uom="'+$(this).data('uom')+'"';
            html +='data-cat="'+$(this).data('cat')+'"';
            html +='data-reason="'+$(this).data('reason')+'"';
            html +='data-qtyverifikasi="'+$(this).data('qtyverifikasi')+'"';
            html +='data-flagkadaluarsa="'+$(this).data('flagkadaluarsa')+'"';
            html +='data-qtyreturver="'+$(this).data('qtyreturver')+'"';
            html +='data-selisi="'+$(this).data('selisi')+'"';
            html +='data-selisivalidasi="'+$(this).data('selisivalidasi')+'"';
            html +='data-nourut="'+$(this).data('nourut')+'"';
            html +='class="check_box"/> '+$(this).data('nourut')+'</td>';
            html += '<td>'+$(this).data("kodebarang")+'</td>';
            html += '<td>'+$(this).data("namabarang")+'</td>';
            html += '<td>'+$(this).data("uom")+'</td>';
            html += '<td>'+$(this).data("cat")+'</td>';
            html += '<td>'+$(this).data("reason")+'</td>';
            html += '<td>'+$(this).data("qtyverifikasi")+'</td>';
            html += '<td>'+$(this).data("qtyreturver")+'</td>';
            html += '<td>'+$(this).data("selisi")+'</td>';
            html += '<td></td>';
            html += '<td></td>';
            html += '<td></td>';          
        }
        $(this).closest('tr').html(html);
        // $('#gender_'+$(this).attr('id')+'').val($(this).data('gender'));
        $('#inputtp_'+$(this).attr('id')+'').select2({
    placeholder: 'Pilih Nama Barang',
                            // allowClear: true,
                            ajax: {
                              url: 'gettps.php',
                              data: function (params) {
                                  return {
                                    q: params.term, // search term
                                    page: params.page,
                                    tipe: ''
                                  };
                                },
                              type: 'GET',
                              dataType: 'json',
                              delay: 250,
                            processResults: function (data) {
                                return {
                                        results: $.map(data, function(obj) {
                                        return {
                                          id: obj.id,
                                            text: obj.text,
                                            uom: obj.uom,
                                            item_cat: obj.item_cat,
                                            id_mst_item: obj.id_mst_item,
                                            item_code: obj.item_code
                                        };
                                     
                                        })
                                    }; 
                            },
                              cache: true
                            },
                       }).on('change', function (e) {


                        });

    });

    

    // $('#pengembalian').on('submit', function(event){
    //     event.preventDefault();
    //     if($('.check_box:checked').length > 0)
    //     {
    //         $.ajax({
    //             url:"multiple_update.php",
    //             method:"POST",
    //             data:$('#pengembalian').serialize(),
    //             success:function()
    //             {
    //                 alert('Data Updated');
    //             }
    //         })
    //     }
    // });

$('#pengembalian').on('submit', function(e){
 e.preventDefault();
 if($('.check_box:checked').length > 0){
    console.log('ok');
    swal.fire({
        title: "Proses?",
        icon: 'question',
        text: "Yakin Ingin Proses Data Ini?",
        type: "warning",
        showCancelButton: !0,
        confirmButtonText: "Ya, Proses!",
        cancelButtonText: "Tidak, Proses!",
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
            preConfirm: function() {
                return new Promise(function(resolve, reject) {
                // here should be AJAX request

                            $.ajax({
                                    url: "viewreturtbsproses.php",
                                    type: "POST",
                                    data:$('#pengembalian').serialize(),
                                    dataType: "html",
                                    success: function (response) {
                            swal.fire("Berhasil!", response, "success");
                                            // refresh page after 2 seconds
                                            setTimeout(function(){
                                                location.reload();
                                            },5000);
                                            location.href='listrequesttbs.php';
                            },
                                    error: function (xhr, ajaxOptions, thrownError) {
                                        setTimeout(function(){
                                            swal("Error", "Tolong Cek Koneksi Lalu Ulangi", "error");
                                        }, 5000);}
                            });
           

                });
            
            },
    }).then(function (e) {

        if (e.value === true) {       

        } else {
            e.dismiss;
        }

    }, function (dismiss) {
        return false;
    })

}
    });


$("body").on("keydown", ".keterangan_barang", function() {
   var x = event.which;
   if (x === 13) {
       event.preventDefault();
   } 
});

$("body").on("focus", ".datepickers", function() {
    $(this).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd",
        // minDate: 0,
        onSelect: function(selectedDate) {}
      });
});

$("body").on("focus", "#date_approve", function() {
    $(this).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd",
        // maxDate: 0,
        onSelect: function(selectedDate) {}
      });
});


$("body").on("focus", "#tanggal_pengembalian", function() {
    $(this).datepicker({
        showOtherMonths: true,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd",
        // maxDate: 0,
        onSelect: function(selectedDate) {}
      });
});

$(document).on('click','.doktp',function(){

id_arr = $(this).attr('id');
id = id_arr.split("_");

if($('#doktp_'+id[1]).is(":checked")){
    
    // $('#inputtp_'+id[1]).show();
     $('#inputtp_'+id[1]).val('1');
        //  $('#select2_'+id[1]).show();
         $('#expired_'+id[1]).prop('required',false);
         $('#expired_'+id[1]).hide();
        //  $('#select2_'+id[1]).prop('required',true);
         $('#inputtp_'+id[1]).prop('required',true);
    }else{
        $('#inputtp_'+id[1]).hide();
        $('#inputtp_'+id[1]).val('');
        $('#select2_'+id[1]).hide();
        $('#expired_'+id[1]).show();
        $('#expired_'+id[1]).prop('required',true);
        $('#select2_'+id[1]).prop('required',false);
        $('#inputtp_'+id[1]).prop('required',false);
    }

});

$(document).on('keyup keydown change','.qtyrevisireturplus',function(){

id_arr = $(this).attr('id');
id = id_arr.split("_");

var qtypengembalian= $('#qtyverifikasi_'+id[1]).val();
var qtyverpengembalian = $('#qtyverifikasiretur_'+id[1]).val(); 
var qtyrevisireturplus = $('#qtyrevisireturplus_'+id[1]).val();
var totalpengembalian = (parseInt(qtyverpengembalian) -  parseInt(qtypengembalian));
var selisi = (parseInt(totalpengembalian) -  parseInt(qtyrevisireturplus));
console.log(selisi);

$('.subTotalplus').val(selisi);


});

$(document).on('keyup keydown change','.qtyrevisireturminus',function(){

id_arr = $(this).attr('id');
id = id_arr.split("_");
var qtypengembalian= $('#qtyverifikasi_'+id[1]).val();
var qtyverpengembalian = $('#qtyverifikasiretur_'+id[1]).val(); 
var qtyrevisireturplus = $('#qtyrevisireturminus_'+id[1]).val();
var totalpengembalian = (parseInt(qtyverpengembalian) -  parseInt(qtypengembalian));
var selisi = (parseInt(totalpengembalian) +  parseInt(qtyrevisireturplus));
console.log(selisi);

$('.subTotalminus').val(selisi);


});

</script>
</body>
</html>       