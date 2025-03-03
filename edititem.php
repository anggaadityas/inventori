<?php
$halaman = "masteritem";
include "layouts/header.php";
include "layouts/navbar.php";


$id = $_GET['id'];
$sqldetail = "SELECT * from mst_item where id_mst_item='$id'";
$stmtdetail = sqlsrv_query($conn, $sqldetail);
if ($stmtdetail === false) {
  die(print_r(sqlsrv_errors(), true));
}
$result = sqlsrv_fetch_array($stmtdetail);

$div_area = $_SESSION['area_div'];

if ($result['item_type'] == 1) {
  $jenisselected = 'selected';
} else {
  $jenisselected = '';
}

if ($result['item_type'] == 2) {
  $jenisselected1 = 'selected';
} else {
  $jenisselected1 = '';
}



if ($result['sap_flag'] == 1) {
  $sap_flagselected = 'selected';
} else {
  $sap_flagselected = '';
}

if ($result['sap_flag'] == 2) {
  $sap_flagselected1 = 'selected';
} else {
  $sap_flagselected1 = '';
}

if ($result['sap_flag'] == 3) {
  $sap_flagselected1 = 'selected';
} else {
  $sap_flagselected1 = '';
}

if ($result['sap_flag'] == 5) {
  $sap_flagselected1 = 'selected';
} else {
  $sap_flagselected1 = '';
}


if ($div_area == 'CK JAKARTA' or $div_area == 'CK SURABAYA') {
  $div = 'CK';
  $jenispermintaan = '<option value="1" ' . $jenisselected . '>Transfer Putus Store</option><option value="2" ' . $jenisselected1 . '>Retur Barang</option>';
  $tipe = '<option value="1" ' . $sap_flagselected . '>Sistem</option><option value="2" ' . $sap_flagselected1 . '>Non Sistem</option><option value="3" ' . $sap_flagselected1 . '>Wadah</option><option value="5" ' . $sap_flagselected1 . '>Damage</option>';
} else if ($div_area == 'IT JAKARTA' or $div_area == 'IT SURABAYA') {
  $div = 'IT';
  $jenispermintaan = '<option value="2" selected>Retur Barang</option>';
  $tipe = '<option value="2" selected>Non Sistem</option>';
} else if ($div_area == 'ENG JAKARTA' or $div_area == 'ENG SURABAYA') {
  $div = 'ENG';
  $jenispermintaan = '<option value="2" selected>Retur Barang</option>';
  $tipe = '<option value="2" selected>Non Sistem</option>';
} else if ($div_area == 'GA JAKARTA' or $div_area == 'GA SURABAYA') {
  $div = 'GA';
  $jenispermintaan = '<option value="2"selected>Retur Barang</option>';
  $tipe = '<option value="2" selected>Non Sistem</option>';
} else {
  $div = '';
}

?>


<div class="container1">

  <br>
  <span style="font-size:18px;"><b>* Edit Barang</b></span>
  <br><br><br>

  <?php

  // menampilkan pesan jika ada pesan
  if (isset($_SESSION['pesan']) && $_SESSION['pesan'] <> '') {
    echo '<div class="alert alert-warning alert-dismissible fade show col-sm-12" role="alert">
  <strong>Info!</strong> ' . $_SESSION['pesan'] . '
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>';
  }

  // mengatur session pesan menjadi kosong
  $_SESSION['pesan'] = '';

  ?>


  <div class="row">
    <div class="col-sm-6">

      <form action="edititemproses.php" method="POST">
        <div class="form-group row">
          <label for="inputEmail3" class="col-sm-4 col-form-label">Jenis Permintaan</label>
          <div class="col-sm-5">
            <select name="jenis" id="jenis" class="form-control" required>
              <option value="">--Pilih Jenis--</option>
              <?php echo $jenispermintaan; ?>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label for="inputEmail3" class="col-sm-4 col-form-label">Divisi Barang</label>
          <div class="col-sm-5">
            <input type="text" autocomplete="off" class="form-control" name="divisi" id="divisi" value="<?php echo $div; ?>" required readonly>
          </div>
        </div>
        <div class="form-group row">
          <label for="inputEmail3" class="col-sm-4 col-form-label">Tipe</label>
          <div class="col-sm-5">
            <select name="tipe" id="tipe" class="form-control" required>
              <option value="">--Pilih Tipe--</option>
              <?php echo $tipe; ?>
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label for="inputPassword3" class="col-sm-4 col-form-label">Kode Barang</label>
          <div class="col-sm-5">
            <input type="hidden" autocomplete="off" class="form-control" name="id" id="inputPassword3" placeholder="id Barang" value="<?php echo $result['id_mst_item']; ?>" required>
            <input type="text" autocomplete="off" class="form-control" name="kode_barang_edit" id="kode_barang_edit" placeholder="Kode Barang" value="<?php echo $result['item_code']; ?>" readonly style="display: none;">
            <span id="text_kodebarang"><?php echo $result['item_code']; ?></span>
            <select class="items form-control" name="kode_barang" id="kode_barang" style="width:220px"></select>
          </div>
        </div>
        <div class="form-group row">
          <label for="inputPassword3" class="col-sm-4 col-form-label">Nama Barang</label>
          <div class="col-sm-5">
            <input type="text" class="form-control" autocomplete="off" name="nama_barang" id="nama_barang" placeholder="Nama Barang" value="<?php echo $result['item_name']; ?>" required readonly>
          </div>
        </div>


    </div>

    <div class="col-sm-6">

      <div class="form-group row">
        <label for="inputEmail3" class="col-sm-4 col-form-label">Satuan</label>
        <div class="col-sm-5">
          <input type="text" autocomplete="off" class="form-control" name="satuan_barang" id="satuan_barang" placeholder="Satuan Barang" required readonly value="<?php echo $result['item_uom']; ?>">
          <!--      <select name="satuan_barang" id="satuan_barang" class="form-control" required>
      <option value="">--Pilih Satuan--</option>
     <?php
      $sqljenisbarang = "SELECT * FROM mst_req_type_item_uom order by req_type_name_item_uom asc ";
      $stmtjenisbarang = sqlsrv_query($conn, $sqljenisbarang);
      if ($stmtjenisbarang === false) {
        die(print_r(sqlsrv_errors(), true));
      }

      while ($rowjenisbarang = sqlsrv_fetch_array($stmtjenisbarang, SQLSRV_FETCH_ASSOC)) {

        if ($result['item_uom'] == $rowjenisbarang['req_type_name_item_uom']) {
          $selected = 'selected';
        } else {
          $selected = '';
        }
        echo "<option " . $selected . " value=" . $rowjenisbarang['req_type_name_item_uom'] . "> " . $rowjenisbarang['req_type_name_item_uom'] . "</option>";
      }

      ?>
     </select> -->
        </div>
      </div>
      <div class="form-group row">
        <label for="inputEmail3" class="col-sm-4 col-form-label">Jenis Barang</label>
        <div class="col-sm-5">
          <select name="jenis_barang" id="jenis_barang" class="form-control" required>
            <option value="">--Pilih Jenis Barang--</option>
            <?php
            $sqljenisbarang = "SELECT * FROM mst_req_type_item order by req_type_name_item asc ";
            $stmtjenisbarang = sqlsrv_query($conn, $sqljenisbarang);
            if ($stmtjenisbarang === false) {
              die(print_r(sqlsrv_errors(), true));
            }

            while ($rowjenisbarang = sqlsrv_fetch_array($stmtjenisbarang, SQLSRV_FETCH_ASSOC)) {

              if ($result['item_cat'] == $rowjenisbarang['req_type_name_item']) {
                $selected = 'selected';
              } else {
                $selected = 'dsadas';
              }
              echo "<option value=" . $rowjenisbarang['req_type_name_item'] . " " . $selected . "> " . $rowjenisbarang['req_type_name_item'] . "</option>";
            }

            ?>

          </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="inputEmail3" class="col-sm-4 col-form-label">Kondisi</label>
        <div class="col-sm-5">
          <select name="kondisi_barang" id="kondisi_barang" class="form-control" required>
            <option value="">--Pilih Satuan--</option>
            <option value="0" <?php if ($result['kondisi_flag'] == '0') echo "selected"; ?>>Good & Non Good</option>
            <option value="1" <?php if ($result['kondisi_flag'] == '1') echo "selected"; ?>>Good</option>
            <option value="2" <?php if ($result['kondisi_flag'] == '2') echo "selected"; ?>>Non Good</option>
          </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="inputEmail3" class="col-sm-4 col-form-label">Status Barang</label>
        <div class="col-sm-5">
          <select name="status" id="status" class="form-control" required>
            <option value="">--Pilih Satuan--</option>
            <option value="0" <?php if ($result['Active'] == '0') echo "selected"; ?>>Aktif</option>
            <option value="1" <?php if ($result['Active'] == '1') echo "selected"; ?>>Tidak Aktif</option>
          </select>
        </div>
      </div>
      <div class="form-group row">
        <label for="inputEmail3" class="col-sm-4 col-form-label"></label>
        <div class="col-sm-5">
          <button type="submit" class="btn btn-primary"><b>Update</b></button>
        </div>
      </div>
      </form>

      <!-- <table id="datatable" class="table table-striped table-bordered nowrap">
    <thead>
                <tr>
                   <th>Jenis Permintaan</th>
                   <th>Divisi Barang</th>
                   <th>Tipe</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Satuan</th>
                   <th>Jenis Barang</th>
                    <th>Kondisi</th>
                    <th>Kadarluarsa</th>
                    <th>Status</th>
                    <th>Detail</th>
                </tr>
            </thead> 
    </table> -->

    </div>

  </div>

</div>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.11.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.1/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script src="js/tagsinput.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<script src="https://use.fontawesome.com/faa3a815de.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/10.5.1/sweetalert2.all.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
  $('#kode_barang').select2({
    placeholder: 'Pilih Nama Barang',
    // allowClear: true,
    ajax: {
      url: 'getitemsap.php',
      data: function(params) {
        return {
          q: params.term, // search term
          page: params.page,
          currentSearchTerm: params.term, // search term,
          page: params.page || 1
        };
      },
      type: 'GET',
      dataType: 'json',
      delay: 250,
      //   processResults: function (data) {
      //     return {
      //         results: data['id'] + '-'+ data['kat_voucher']
      //     };
      //   },
      processResults: function(data) {
        return {
          results: $.map(data, function(obj) {
            return {
              id: obj.id,
              text: obj.text,
              uom: obj.uom,
              itemname: obj.itemname
            };

          })
        };
      },
      cache: true
    },
  }).on('change', function(e) {
    var data = $('#kode_barang').select2('data');
    $('#kode_barang').val(data[0].id);
    $('#nama_barang').val(data[0].itemname);
    $('#satuan_barang').val(data[0].uom);
    $('#text_kodebarang').html(data[0].id);

  });


  //Datatables Basic server side initilization
  $(document).ready(function() {
    var dataTable = $('#datatable').DataTable({
      "processing": true,
      "serverSide": true,
      "ordering": true,
      "responsive": true,
      "ajax": {
        url: "listdatamasteritem.php", // json datasource
        type: "post", // method  , by default get
        error: function() { // error handling
          // $(".lookup-error").html("");
          // $("#lookup").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
          // $("#lookup_processing").css("display","none");

        }
      },
      "aaSorting": [
        [0, "desc"]
      ],
      "createdRow": function(row, data, dataIndex) {
        if (data[2] == 'Non System') {
          $(row).css('background-color', '#FFF');
        } else {
          $(row).css('background-color', '#F39B9B');
        }
      },
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function(row) {
              var data = row.data();
              return 'Detail ' + data[0];
            }
          }),
          renderer: $.fn.dataTable.Responsive.renderer.tableAll({
            tableClass: 'table'
          })
        }
      }
    });
  });


  $(document).on("click", ".open-AddBookDialog", function() {
    var myBookId = $(this).data('id');
    var code = $(this).data('code');
    var delivery = $(this).data('delivery');
    var store = $(this).data('store');
    var divisi = $(this).data('divisi');

    $("#myModalLabel").html("No Dokumen #" + code);
    $(".modal-body #bookId").val(myBookId);
    $(".modal-body #bookcode").val(code);
    $(".modal-body #bookdelivery").val(delivery);
    $(".modal-body #bookstore").val(store);
    $(".modal-body #bookdivisi").val(divisi);
    //  $('#addBookDialog').modal('show');
  });

  $(document).on("click", ".addreturnproses", function() {
    var id_rtn = $("#bookId").val();
    var code = $("#bookcode").val();
    var delivery = $("#bookdelivery").val();
    var store = $("#bookstore").val();
    var divisi = $("#bookdivisi").val();
    var status = $("#status :selected").val();
    var note = $("#note").val();

    if (status == "") {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Wajib Pilih Status!'
      });
      $("#status_proses").focus();
    } else {

      if (status == 'Approved') {

        swal.fire({
          title: "Proses?",
          icon: 'question',
          text: "Yakin Ingin Proses Data Ini?",
          type: "warning",
          showCancelButton: !0,
          confirmButtonText: "Ya, Proses!",
          cancelButtonText: "Tidak, Proses!",
          reverseButtons: !0
        }).then(function(e) {

          if (e.value === true) {

            $.ajax({
              url: "approvereturnproses.php",
              type: "post",
              data: {
                id: id_rtn,
                status: status,
                note: note,
                code: code,
                store: store,
                divisi: divisi,
                delivery: delivery
              },
              success: function(response) {
                swal.fire("Berhasil!", response, "success");
                // refresh page after 2 seconds
                setTimeout(function() {
                  location.reload();
                }, 26000);
                $('#datatable').DataTable().ajax.reload();
                $('#addBookDialog').modal('hide');
              },
              error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
              }
            });



          } else {
            e.dismiss;
          }

        }, function(dismiss) {
          return false;
        })

      } else {

        if (note == "") {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Wajib Input Keterangan!'
          });
          $("#note").focus();
        } else {

          swal.fire({
            title: "Proses?",
            icon: 'question',
            text: "Yakin Ingin Proses Data Ini?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Ya, Proses!",
            cancelButtonText: "Tidak, Proses!",
            reverseButtons: !0
          }).then(function(e) {

            if (e.value === true) {

              $.ajax({
                url: "approvereturnproses.php",
                type: "post",
                data: {
                  id: id_rtn,
                  status: status,
                  note: note,
                  code: code,
                  store: store,
                  divisi: divisi,
                  delivery: delivery
                },
                success: function(response) {
                  swal.fire("Berhasil!", response, "success");
                  // refresh page after 2 seconds
                  setTimeout(function() {
                    location.reload();
                  }, 2000);
                  $('#datatable').DataTable().ajax.reload();
                  $('#addBookDialog').modal('hide');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                  console.log(textStatus, errorThrown);
                }
              });



            } else {
              e.dismiss;
            }

          }, function(dismiss) {
            return false;
          })

        }


      }


    }

  });
</script>
</body>

</html>