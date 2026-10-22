


<?php
include "header.php";
if (isset($_REQUEST['update'])) {
    // Assuming $mobile is already defined in header.php
    $sanitizedMobile =  $mobile;
    
    // Sanitize input using mysqli_real_escape_string
    $current_password =  $_REQUEST['current_password'];
    $new_password =  $_REQUEST['new_password'];
    $confirm_password =  $_REQUEST['confirm_password'];

    // Retrieve the hashed password from the database
    $query = "SELECT `password` FROM `users` WHERE `mobile` = '$sanitizedMobile'";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $hashedPasswordFromDB = $row['password'];
        
        // Check if the current password matches the stored hashed password
        if (password_verify($current_password, $hashedPasswordFromDB)) {
            if ($new_password === $confirm_password) {
                // Hash the new password using bcrypt
                $newpass = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update the password in the database
                $passwor = "UPDATE `users` SET `password` = '$newpass' WHERE `mobile` = '$sanitizedMobile'";
                $up = mysqli_query($conn, $passwor);
                
                if ($up) {
                    echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
                        Swal.fire({
                            icon: "success",
                            title: "Password Changed Successfully",
                            text: "Your password has been updated.",
                            showConfirmButton: true,
                            confirmButtonText: "Ok",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "dashboard.php";
                            }
                        });
                    </script>';
                    exit;
                } else {
                    echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
                        Swal.fire({
                            icon: "error",
                            title: "Password Update Failed",
                            text: "Please try again later.",
                            showConfirmButton: true,
                            confirmButtonText: "Try Again",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "changepassword.php";
                            }
                        });
                    </script>';
                    exit;
                }
            } else {
               echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
                    Swal.fire({
                        icon: "error",
                        title: "New Password and Confirm Password Do Not Match",
                        showConfirmButton: true,
                        confirmButtonText: "Try Again",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "changepassword.php";
                        }
                    });
                </script>';
                exit;
            }
        } else {
            echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
                Swal.fire({
                    icon: "error",
                    title: "Current Password Does Not Match",
                    showConfirmButton: true,
                    confirmButtonText: "Try Again",
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "changepassword.php";
                    }
                });
            </script>';
            exit;
        }
    } else {
        echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
            Swal.fire({
                icon: "error",
                title: "Please try again later.",
                text: "Please try again later.",
                showConfirmButton: true,
                confirmButtonText: "Try Again",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "changepassword.php";
                }
            });
        </script>';
        exit;
    }
}
?>

<style>
   .switch {
    position: relative;
    display: inline-block;
    width: 65px;
    height: 33px;
}

.switch input {
    display: none;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #fff;
    transition: .4s;
    border:2px solid #25a6a1;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 5px;
    bottom: 4px;
    background-color: #25a6a1;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #25a6a1;
}

input:checked + .slider:before {
    transform: translateX(30px);
    background-color: #fff;
}

.switch-title {
    font-size: 16px;
    font-weight: 500;
    margin:0 10px 10px 0;
}

  </style>
    
<main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-key"></i> Change Password</h1>
          <!-- <p>Start a beautiful journey here</p> -->
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
        </ul>
      </div>
      </div>
      <div class="tile mb-4">
        <div class="page-header">
          <div class="row">
            <div class="col-lg-12">
						<!-- <h4 class="page-title">UPI Settings</h4> -->
						<div class="row row-card-no-pd">
							<div class="col-md-12">

                            <div class="main-panel">
<div class="content">
    <div class="container-fluid">

        <!-- <h4 class="page-title">Change Password</h4>	 -->
                        
        <div class="row row-card-no-pd">							
            <div class="col-md-12">
                <form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                     <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <div class="col-md-4 mb-3">
                    <label>Current Password</label>
                    <input type="password" name="current_password" placeholder="Current Password" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="New Password" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <button type="submit" name="update" class="btn btn-primary btn-block">Change Password</button>
                </div>

              </form>
            </div>
            
             <div class="col-md-12 my-4">
                      <h3 class="mb-1 content-heading">Manage 2FA Security</h3>
                    <p class="text-muted mb-3">Manage when you login if 2fa enabled we send 6 digit OTP code to your email so your account as more secure and safe.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        
                     <div class="switch-container d-flex align-items-center">
                         <span class="switch-title">Two Factor Authentication</span>
                    <label class="switch">
                        <input type="checkbox" <?php if($userdata['two_factor'] == 1){ echo 'checked'; } ?> class="update2fa_btn">
                        <span class="slider"></span>
                    </label>
                   </div>
                   
                    </div>
                  </div>
            
        </div>
    </div>
</div>
</div>
</div>
</div>
</body>
<!-- Essential javascripts for application to work-->
<script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/mainscript.js"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="js/plugins/pace.min.js"></script>
    <!-- Page specific javascripts-->
    <!-- Google analytics script-->
    <script type="text/javascript">
      if(document.location.hostname == 'pratikborsadiya.in') {
      	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
      	ga('create', 'UA-72504830-1', 'auto');
      	ga('send', 'pageview');
      }
    </script>
    <script type="text/javascript">
function utr_search(utr_number){
if(getCurentFileName()=="transactions"){	
if(utr_number.length==12){
search_txn('2023-10-01','2023-10-20','',utr_number);
}else{
Swal.fire('Enter Valid UTR Number!');	
}
}else{
location.href ='transactions';
}
}
</script>
</html>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css"/>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
$("#dataTable").DataTable();
});
</script>
<script src="assets/js/bharatpe.js?1697765682"></script>
  </body>
</html>