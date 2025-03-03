<?php
$halaman = "tps";
include "layouts/header.php";
include "layouts/navbar.php";
if (!isset($_SESSION['uid']) || !isset($_SESSION['nama_divisi'])) {
    header('Location: index.php');
    exit;
  }else{
    $_SESSION['token'] = $_SESSION['nama'].".".bin2hex(random_bytes(32));
    $token = $_SESSION['token'];
    $apiKey ='AAS';
  }
?>

  <link href="https://cdn.jsdelivr.net/npm/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet">
  <!-- Font Awesome (optional for icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
       .dataTables_length,
        .dataTables_filter,
        .dt-buttons {
            float: right; /* Posisikan tombol di sebelah kanan */
            margin-left: 10px; /* Beri jarak kiri */
        }

        .dataTables_length {
            float: left; /* Posisikan "Show X entries" di sebelah kiri */
        }
    .is-invalid .form-control {
      border-color: #dc3545;
    }
    .is-valid .form-control {
      border-color: #28a745;
    }
    .error {
      color: #dc3545;
      margin-top: 0.25rem;
      font-size: 80%;
    }
  </style>
<div class="container1">
    <div class="row">
 <div class="col-sm-12" style="margin-top: 26px;">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#menu1"><b>Formulir Upload Pemesanan Toko</b></a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane container active" id="menu1">
    <form id="orderForm" enctype="multipart/form-data">
    <br><br>
     <fieldset>
      <div class="form-group col-sm-4">
        <label for="store">Toko</label>
        <input type="text" class="form-control" id="storeCd" name="storeCd" value ="<?php echo $_SESSION["nama"]; ?>" required autocomplete='off' readonly>
        <input type="hidden" class="form-control" id="uploadBy" name="uploadBy" value ="<?php echo $_SESSION["nama"]; ?>" required autocomplete='off' readonly>
        <input type="hidden" class="form-control" id="uploadDate" name="uploadDate" value ="<?php echo date('d-m-Y H:s') ?>" required autocomplete='off' readonly>
        <div class="invalid-feedback"></div>
      </div>
      <div class="form-group col-sm-4">
        <label for="orderDate">Tanggal Pemesanan</label>
        <input type="text" class="form-control" id="docDate" name="docDate" required autocomplete='off' readonly value="<?php echo date('Y-m-d'); ?>">
        <div class="invalid-feedback"></div>
      </div>
      <div class="form-group col-sm-4">
        <label for="dueDate">Tanggal Pengiriman</label>
        <input type="text" class="form-control" id="dueDate" name="dueDate" required autocomplete='off'>
        <div class="invalid-feedback"></div>
      </div>
      <div class="form-group col-sm-4">
        <label for="orderDate">Kategori pemesanan</label>
       <select name="categoryTp" id="categoryTp" class="form-control" required>
       <option value="">-- Kategori Pemesanan --</option>
        <option value="DailyCategory">DailyCategory</option>
        <!-- <option value="NonDailyCategory">NonDailyCategory</option> -->
       </select>
        <div class="invalid-feedback"></div>
        <a href="" id="linktemplate1" style="display:none;">Download Template</a>
         <br>
        <span style="font-size: 10px;"><b>* Setiap Upload Order Toko Di Wajibkan Untuk Selalu Download Template & Pastikan Cek Kembali Kelipatan Order Dimasing-masing Barang</b></span>
      </div>
      <div class="form-group col-sm-4" style="display:none;" id="inputjenispemesanan">
        <label for="orderDate">Jenis pemesanan</label>
       <select name="categoryCd" id="categoryCd" class="form-control" required></select>
        <div class="invalid-feedback"></div>
        <span style="display:none;" id="template">
        <a href="" id="linktemplate">Download Template</a>
        <br>
        <span style="font-size: 10px;"><b>* Setiap Upload Order Toko Di Wajibkan Untuk Selalu Download Template & Pastikan Cek Kembali Kelipatan Order Dimasing-masing Barang</b></span>
      </span>
      </div>
 
      <div class="form-group col-sm-4">
        <label for="remarks">Keterangan</label>
        <textarea class="form-control" id="remarks" name="remarks" rows="3" required autocomplete='off'></textarea>
        <div class="invalid-feedback"></div>
      </div>
      <div class="form-group col-sm-4">
        <label for="fileUpload">Upload File</label>
        <input type="file" class="form-control-file" id="fileUpload" name="fileUpload" required>
        <div class="invalid-feedback"></div>
      </div>
      </fieldset>
      <div class="form-group col-sm-4">
        <label for="fileUpload"></label>
      <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
        <div class="invalid-feedback"></div>
      </div>
    </form>

    
    <span><b>View Data Toko Order</b></span>
</br></br>
<table id="apiData" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
    <thead>
        <tr>
            <th>No Seq</th>
            <th>Store Code</th>
            <th>Order Date</th>
            <th>Due Date</th>
            <th>Category Type</th>
            <th>Category Code</th>
            <th>Item Code</th>
            <th>Item Name</th>
            <th>Item UOM</th>
            <th>Item Quantity</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

    </div>
</div>
            </div>
        </div>

        <br><br>


<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.13/js/gijgo.min.js"></script>
  <script src="js/jquery.validate.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/additional-methods.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.print.min.js"></script>
  <script>
  $(document).ready(function () {
    $("#categoryTp").change(function () {
        $('#template').hide();
        var kategori = $(this).val();
        if (kategori == "DailyCategory") {
            $('#inputjenispemesanan').hide();
            $('#linktemplate1').show();
            // $('#categoryCd').html('<option value="">--Pilih--</option><option value="Chiller">Chiller</option><option value="Dry Food">Dry Food</option><option value="Dry Non Food">Dry Non Food</option><option value="Frozen">Frozen</option>');
            $("#linktemplate1").attr('href', 'http://192.168.2.135:88/inventori/template/DailyCategory_.xlsx');
        } else if (kategori == "NonDailyCategory") {
            $('#inputjenispemesanan').show();
            $('#linktemplate1').hide();
            $('#categoryCd').html('<option value="">--Pilih--</option><option value="ATK">ATK</option><option value="Cutleries">Cutleries</option><option value="Delivery">Delivery</option><option value="Engineering">Engineering</option><option value="Marketing">Marketing</option><option value="NP-ITDP">NP-ITDP</option><option value="Obat">Obat</option><option value="Reguler <= 150rb">Reguler <= 150rb</option><option value="Reguler > 150rb">Reguler > 150rb</option><option value="Seragam">Seragam</option>');
        } else {
            $('#inputjenispemesanan').hide();
            $('#linktemplate1').hide();
            $('#categoryCd').html('');
        }
    });

    $(document).ready(function() {
            $("#categoryCd").change(function(){
              $('#template').show();
              var jenis = $(this).val();
              var kategori = $('#categoryTp :selected').val();
              
              if(kategori=='DailyCategory'){
                  fixkategori ='DailyCategory';
              }else{
                 fixkategori ='NonDailyCategory';
              }

              if(jenis =='Reguler <= 150rb'){
                  fixjenis ='RegulerKd=150rb';
              }else if(jenis =='Reguler > 150rb'){
                fixjenis ='RegulerLd150rb';
              }else{
                fixjenis =jenis.replace(/\s+/g, '-');
              }

              console.log("Jenis: " + fixjenis + ", Kategori: " + fixkategori);
              $("#linktemplate").attr('href', 'http://192.168.2.135:88/inventori/template/'+fixkategori+'_'+fixjenis+'.xlsx');
            });
          });


    var orderDateInput = $('#docDate');
    var dueDateInput = $('#dueDate');

    // orderDateInput.datepicker({
    //     uiLibrary: 'bootstrap4',
    //     format: 'yyyy-mm-dd',
    //     maxDate: new Date()
    // });

    dueDateInput.datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd',
        minDate: new Date(),
        // change: function (e) {
        //     orderDateInput.datepicker('setOptions', { maxDate: e.target.value });
        //     orderDateInput.trigger('change');
        // }
    });

    $('#orderForm').validate({
        rules: {
            store: {
                required: true,
                minlength: 2
            },
            orderDate: {
                required: true,
                date: true
            },
            dueDate: {
                required: true,
                date: true
            },
            kategori_pemesanan: {
                required: true
            },
            jenis_pemesanan: {
                required: true
            },
            remarks: {
                required: true,
                minlength: 5
            },
            fileUpload: {
                required: true,
                extension: "xlsx"
            }
        },
        messages: {
            store: {
                required: "Please enter the store name",
                minlength: "Store name must be at least 2 characters long"
            },
            orderDate: {
                required: "Please enter the order date",
                date: "Please enter a valid date"
            },
            dueDate: {
                required: "Please enter the due date",
                date: "Please enter a valid date"
            },
            remarks: {
                required: "Please enter remarks",
                minlength: "Remarks must be at least 5 characters long"
            },
            fileUpload: {
                required: "Please upload a file",
                extension: "File must be of type: xlsx"
            }
        },
        errorClass: "is-invalid",
        validClass: "is-valid",
        highlight: function (element, errorClass, validClass) {
            $(element).addClass(errorClass).removeClass(validClass);
            $(element).closest('.form-group').find('.invalid-feedback').show();
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass(errorClass).addClass(validClass);
            $(element).closest('.form-group').find('.invalid-feedback').hide();
        },
        errorPlacement: function (error, element) {
            error.appendTo(element.closest('.form-group').find('.invalid-feedback'));
        },
        submitHandler: function (form) {
            var formData = new FormData(form);

            $('#submitBtn').prop('disabled', true);

            Swal.fire({
                title: 'Mohon Tunggu...',
                html: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
                showConfirmButton: false,
                allowOutsideClick: false
            });

            $.ajax({
                url: 'http://192.168.1.61:7000/api/Upload/uExcelAddOn',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Authorization': 'Basic ' + btoa('test:test'),
                    'Accept': 'application/json'
                },
                success: function (response) {
                    $('#submitBtn').prop('disabled', false);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.responseMsg,
                        html: `
                            <strong>Store:</strong> ${response.responseId}<br>
                            <strong>Message:</strong> ${response.responseMsg}<br>
                            <strong>Date:</strong> ${response.responseDt}<br><br>
                            <strong>Data:</strong><br>
                            <pre>${JSON.stringify(response.data, null, 2)}</pre>
                        `
                    });
                    $(form).find('.form-control').removeClass('is-valid');
                    form.reset();
                    $('#apiData').DataTable().ajax.reload(null, false);
                },
                error: function (xhr) {
                    $('#submitBtn').prop('disabled', false);
                    var message = 'There was an error submitting your order.';

                    if (xhr.status === 400 || xhr.status === 401 || xhr.status === 500) {
                        var errors = xhr.responseJSON.errors;
                        if (errors) {
                            message = Object.keys(errors).map(function (key) {
                                return errors[key].join(', ');
                            }).join('<br>');
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: message,
                    });
                }
            });

            return false;
        }
    });
});

// $(document).ready(function(){
//     var storeCdText = $('#storeCd').val();
//             $.ajax({
//                 url: 'http://192.168.1.61:8906/api/Upload/iDataAddOn', 
//                 method: 'GET',
//                 dataType: 'json',
//                 data: {
//                     storeCd:storeCdText
//                 },
//                 headers: {
//                     'Authorization': 'Basic ' + btoa('test:test'),
//                     'Accept': 'application/json'
//                 },
//                 success: function(response) {
//                     if (response.responseCd === '1000') {
//                         var data = response.data;
//                         var html = '<table border="1"><tr><th>Store Code</th><th>Category Type</th><th>Category Code</th><th>Item Code</th><th>Item Name</th><th>Item Quantity</th><th>Item Min Order</th><th>Item UOM</th></tr>';
                        
//                         $.each(data, function(index, item) {
//                             html += '<tr>';
//                             html += '<td>' + item.storeCd + '</td>';
//                             html += '<td>' + item.categoryTp + '</td>';
//                             html += '<td>' + item.categoryCd + '</td>';
//                             html += '<td>' + item.itemCode + '</td>';
//                             html += '<td>' + item.itemName + '</td>';
//                             html += '<td>' + item.itemQty + '</td>';
//                             html += '<td>' + item.itemMinOrder + '</td>';
//                             html += '<td>' + item.itemUom + '</td>';
//                             html += '</tr>';
//                         });
                        
//                         html += '</table>';
//                         $('#apiData').html(html);
//                     } else {
//                         $('#apiData').html('<p>Error: ' + response.responseMsg + '</p>');
//                     }
//                 },
//                 error: function(xhr, status, error) {
//                     $('#apiData').html('<p>An error occurred: ' + error + '</p>');
//                 }
//             });
//         });


$(document).ready(function() {
      var table = $('#apiData').DataTable({
            dom: 'lBfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: 'Export to Excel',
                    filename: function() {
                    return 'order_toko_' + new Date().toISOString().slice(0, 10); // Contoh: 
                },
                  customize: function(xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    $('sheetData row:first', sheet).remove();
                    var rows = $('sheetData row', sheet);
                    rows.each(function(index, element) {
                        var originalIndex = index + 2;
                         $(element).attr('r', originalIndex);
                    });
                }
            }
            ],
            lengthMenu: [[10, 50, 100,-1],[10,50,100,"ALL"]],
            ajax: {
                url: 'http://192.168.1.61:7000/api/Upload/iDataAddOn',
                method: 'GET',
                dataType: 'json',
                data: function(d) {
                    d.storeCd = $('#storeCd').val();
                },
                headers: {
                    'Authorization': 'Basic ' + btoa('test:test'),
                    'Accept': 'application/json'
                },
                dataSrc: 'data'
            },
            columns: [
                { title: "No Seq", data: "docEntry" },
                { title: "Store Code", data: "storeCd" },
                { title: "Order Date", data: "docDate" },
                { title: "Due Date", data: "dueDate" },
                { title: "Category Type", data: "categoryTp" },
                { title: "Category Code", data: "categoryCd" },
                { title: "Item Code", data: "itemCode" },
                { title: "Item Name", data: "itemName" },
                { title: "Item UOM", data: "itemUom" },
                { title: "Item Quantity", data: "itemQty" }
            ]
        });

           $('#apiData thead input').on('keyup change clear', function() {
            var index = $(this).closest('th').index();
            table.column(index).search(this.value).draw();
        });

});




  </script>
</body>
</html>
