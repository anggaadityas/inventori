<?php
// error_reporting(0);
session_start();
if(isset($_SESSION["uid"])) header("Location: listrequest.php");


$servername = "192.168.2.136";
$username = "root";
$password = "aas260993";
$dbname = "voucher_trial";

$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());


if(isset($_POST['login'])){
    
 $email = addslashes(trim(strip_tags($_POST['email'])));
    $password = addslashes(trim(strip_tags($_POST['password'])));


    $tsql = "SELECT * FROM mst_user a INNER JOIN mst_divisi b
         on a.div_id=b.id_divisi where a.email= '$email'";   


$stmt = mysqli_query($conn,$tsql);
$user =mysqli_fetch_array($stmt);

    // jika user terdaftar
    if(strtoupper($user['email']) == strtoupper($email)){
        // verifikasi password
        if(md5($password) ==  $user["password"]){
            // buat Session
            session_start();
            $_SESSION["uid"] = strtoupper($user['id_user']);
            $_SESSION["nama"] = strtoupper($user['nama']);
            $_SESSION["area_div"] = strtoupper($user['area_div']);
            $_SESSION["area_ck"] = strtoupper($user['area_ck']);
            $_SESSION["id_divisi"] = strtoupper($user['id_divisi']);
            $_SESSION["nama_divisi"] = strtoupper($user['nama_divisi']);
            $_SESSION["inisial_divisi"] = strtoupper($user['inisial_divisi']);
            $_SESSION["role_id"] = strtoupper($user['role_id']);
            $_SESSION["wadah_flag"] = strtoupper($user['wadah_flag']);
            $_SESSION["sfgConfig"] = strtoupper($user['sfgConfig']);
            if($user['role_id'] =="1" OR $user['role_id'] =="2" OR $user['role_id'] =="3"  ){              
            header("Location: listrequesttbs.php");
            }else if($user['role_id'] =="6" ){    
              $halaman='listrequesttbs';          
              header("Location: listrequesttbs.php");
              }else if($user['role_id'] =="12" ){    
                $halaman='listreturn';          
                header("Location: listreturn.php");
                }else if($user['role_id'] =="13" ){    
                $halaman='listreturn';          
                header("Location: listapprovereturn.php");
                }else if($user['role_id'] =="5" ){    
                $halaman='listreturn';          
                header("Location: listreturn.php");
                }else if($user['role_id'] =="14"  || $user['role_id'] =="15" || $user['role_id'] =="16"){    
                  $halaman='listrequestassets';          
                  header("Location: listrequestassets.php");
                  }else if($user['nama'] =="Audit" || $user['nama'] =="Head FA" || $user['nama'] =="RM" ){    
                $halaman='laporan';          
                header("Location: reporttps.php");
                }else if($user['role_id'] =="999" ){    
                  $halaman='listwaste';          
                  header("Location: listwaste.php");
                  }else{      
                $halaman='listrequesttbs';             
            header("Location: listrequesttbs.php");
            }
        }else{
           $error = '<div><div class="alert alert-warning alert-dismissible fade show" role="alert">
              <strong>Gagal Login! </strong> Silakan Cek Password Anda!.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div></div>';
                }
    }else{
          $error = '<div><div class="alert alert-warning alert-dismissible fade show" role="alert">
  <strong>Gagal Login! </strong> Silahkan Cek Username Atau Password Anda!.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div></div>';
    }

    
}                             

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Inventori Barang - Yoshinoya</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Quicksand" />
    <style type="text/css">
    	html,
		body {
		  height: 100%;
    font-family:Quicksand;
		}

		body {
		  height: 100%;
		  font-family:Quicksand;
		  display: -ms-flexbox;
		  display: flex;
		  -ms-flex-align: center;
		  align-items: center;
		  padding-top: 8px;
		  padding-bottom: 40px;
		  background-color: #f5f5f5;
		}

		.form-signin {
		  width: 100%;
		  max-width: 330px;
		  padding: 15px;
		  margin: auto;
		}
		.form-signin .checkbox {
		  font-weight: 400;
		}
		.form-signin .form-control {
		  position: relative;
		  box-sizing: border-box;
		  height: auto;
		  padding: 10px;
		  font-size: 16px;
		}
		.form-signin .form-control:focus {
		  z-index: 2;
		}
		.form-signin input[type="username"] {
		  margin-bottom: -1px;
		  border-bottom-right-radius: 0;
		  border-bottom-left-radius: 0;
		}
		.form-signin input[type="password"] {
		  margin-bottom: 10px;
		  border-top-left-radius: 0;
		  border-top-right-radius: 0;
		}
		.form-signin input[type="checkbox"] {
		  margin-bottom: 16px;
		  margin-left: -170px;
		  border-top-left-radius: 0;
		  border-top-right-radius: 0;
		}

		.btn-primary {
		 color:#fff;
		 background-color:#fd880c;
		 border-color:#fd880c
		}
		.btn-primary:hover {
		 color:#fff;
		 background-color:#d9750c;
		 border-color:#d9750c
		}
		.btn-primary.focus,
		.btn-primary:focus {
		 box-shadow:0 0 0 .2rem rgba(0,123,255,.5)
		}
		.btn-primary.disabled,
		.btn-primary:disabled {
		 color:#fff;
		 background-color:#fd880c;
		 border-color:#fd880c
		}
		.btn-primary:not(:disabled):not(.disabled).active,
		.btn-primary:not(:disabled):not(.disabled):active,
		.show>.btn-primary.dropdown-toggle {
		 color:#fff;
		 background-color:#d9750c;
		 border-color:#d9750c
		}
		.btn-primary:not(:disabled):not(.disabled).active:focus,
		.btn-primary:not(:disabled):not(.disabled):active:focus,
		.show>.btn-primary.dropdown-toggle:focus {
		 box-shadow:0 0 0 .2rem rgba(0,123,255,.5)
		}
    </style>

  </head>

  <body class="text-center">


    <form class="form-signin" method="POST" action="">

      <img style="margin-bottom: -10px;"  src="Yoshinoya.png" alt="" width="180" height="180">

            <?php

             if(!empty($error)){
                  echo $error;
               }else{
              }



                           ?>  

      <!-- <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1> -->

      <label class="sr-only">Email</label>
      <input type="text" id="email" name="email" class="form-control" placeholder="Email" required autofocus autocomplete="off">

      <label class="sr-only">Password</label>
      <input type="password" name="password" id="password" class="form-control" placeholder="Password" required  autocomplete="off">

      <div style="margin-top: 13px;margin-bottom: 8px;">
      <label for="inputchecksbox" class="sr-only"></label>
      <input type="checkbox" value="remember-me" onclick="ShowPassword()"> Show Password
      </div>


      <button class="btn btn-lg btn-primary btn-block" name="login" type="submit">Sign in</button>
      <p class="mt-5 mb-3 text-muted"><b>&copy; Inventori Barang 2021</b></p>
    </form>


<!-- <script src="design/js/jquery-3.3.1.slim.min.js"></script>
<script src="design/js/bootstrap.min.js"></script> -->
<script type="text/javascript">

function hideAddressBar()
{
    if(!window.location.hash)
    { 
        if(document.height <= window.outerHeight + 10)
        {
            document.body.style.height = (window.outerHeight + 50) +'px';
            setTimeout( function(){ window.scrollTo(0, 1); }, 50 );
        }
        else
        {
            setTimeout( function(){ window.scrollTo(0, 1); }, 0 ); 
        }
    }
} 
 
window.addEventListener("load", hideAddressBar );
window.addEventListener("orientationchange", hideAddressBar );

					
	        function ShowPassword() {
              var x = document.getElementById("password");
              if (x.type === "password") {
                x.type = "text";
              } else {
                x.type = "password";
              }
            }


</script>






</body>
</html>
