<?php
$halaman = "resetsandi";
include "layouts/header.php";
include "layouts/navbar.php";
?>
<style>
  input[type="submit"]:disabled {
    background-color: red;
  }
</style>

<div class="container1">
  <div class="row">
    <div class="col-sm-12" style="margin-top: 26px;">

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

      <p style="font-size: 26px;">Reset Kata Sandi</p>
      <div class="col-md-3">
        <form action="cpproses.php" method="POST" id="myForm1" class="needs-validation" novalidate>
          <div class="form-group">
            Kata Sandi<input type="text" name="password" id="pwdId" class="form-control pwds" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one  number and one uppercase and lowercase letter, and at least 8 or more characters" autocomplete="off" required>
            <div class="valid-feedback"><b>Sudah Sesuai</b></div>
            <div class="invalid-feedback"><b>Min 8 Karakter, Harus Ada : 1 Huruf Besar, 1 Huruf Kecil, 1 Angka</b></div>
          </div>
          <div class="form-group">
            Konfirmasi Kata Sandi<input type="text" id="cPwdId" class="form-control pwds" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one  number and one uppercase and lowercase letter, and at least 8 or more characters" autocomplete="off" required>
            <div id="cPwdValid" class="valid-feedback"><b>Sudah Sesuai</b></div>
            <div id="cPwdInvalid" class="invalid-feedback"><b>Min 8 Karakter, Harus Ada : 1 Huruf Besar, 1 Huruf Kecil, 1 Angka</b></div>
          </div>
          <div class="form-group">
            <button id="submitBtn" type="submit" class="btn btn-primary submit-button" disabled>Submit</button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
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
  $(document).ready(function() {
    // Check if passwords match
    $('#pwdId, #cPwdId').on('keyup', function() {
      if ($('#pwdId').val() != '' && $('#cPwdId').val() != '' && $('#pwdId').val() == $('#cPwdId').val()) {
        $("#submitBtn").attr("disabled", false);
        $('#cPwdValid').show();
        $('#cPwdInvalid').hide();
        $('#cPwdValid').html('<b>Sudah Sesuai</b>').css('color', 'green');
        $('.pwds').removeClass('is-invalid')
      } else {
        $("#submitBtn").attr("disabled", true);
        $('#cPwdValid').hide();
        $('#cPwdInvalid').show();
        $('#cPwdInvalid').html('<b>Konfirmasi Tidak Sesuai Dengan Kata Sandi</b>').css('color', 'red');
        $('.pwds').addClass('is-invalid')
      }
    });
    let currForm1 = document.getElementById('myForm1');
    // Validate on submit:
    currForm1.addEventListener('submit', function(event) {
      if (currForm1.checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
      }
      currForm1.classList.add('was-validated');
    }, false);
    // Validate on input:
    currForm1.querySelectorAll('.form-control').forEach(input => {
      input.addEventListener(('input'), () => {
        if (input.checkValidity()) {
          input.classList.remove('is-invalid')
          input.classList.add('is-valid');
        } else {
          input.classList.remove('is-valid')
          input.classList.add('is-invalid');
        }
        var is_valid = $('.form-control').length === $('.form-control.is-valid').length;
        $("#submitBtn").attr("disabled", !is_valid);
      });
    });
  });
</script>