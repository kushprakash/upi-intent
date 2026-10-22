<?php include "header.php"; ?>
    <!-- Include the SweetAlert CDN link -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

    
<?php
  include "config.php";
  
//   ini_set("display_errors",true);
// error_reporting(E_ALL);
  
  if($userdata["role"] != 'Admin'){
   echo '<script>
 window.location.href = "dashboard";
</script>';

    exit;
}
  
  if (isset($_POST['create'])) {
      
   $mobile =  $_POST['mobile'];
    $email = $_POST['email'];

    // Check if the mobile number already exists in the database
    $checkMobileQuery = "SELECT * FROM `users` WHERE `mobile` = '$mobile'";
    $checkMobileResult = mysqli_query($conn, $checkMobileQuery);

    // Check if the email already exists in the database
    $checkEmailQuery = "SELECT * FROM `users` WHERE `email` = '$email'";
    $checkEmailResult = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($checkMobileResult) > 0) {
        // The mobile number already exists, display an error message
        echo '
    <script>
        Swal.fire({
            title: "Opps! Your Mobile no Already Exist!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "adduser"; // Replace with your desired redirect URL
            }
        });
    </script>
';
    } elseif (mysqli_num_rows($checkEmailResult) > 0) {
        // The email already exists, display an error message
        echo '
    <script>
        Swal.fire({
            title: "Opps! Your Email no Already Exist!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "adduser"; // Replace with your desired redirect URL
            }
        });
    </script>
';
exit;
    } else {
        // Proceed with user registration
        $password =  $_POST['password'];
    $name =  $_POST['name'];
    $company =  $_POST['company'];
    $pin =  $_POST['pin'];
    $pan =  $_POST['pan'];
    $aadhaar =  $_POST['aadhaar'];
    $location =  $_POST['location'];
        $key = md5(rand(00000000, 99999999));
        $pass = password_hash($password, PASSWORD_BCRYPT);
        $today = (date("Y-m-d") + 3);

        $register = "INSERT INTO `users`(`name`, `mobile`, `role`, `password`, `email`, `company`, `pin`, `pan`, `aadhaar`, `location`, `user_token`, `expiry`) VALUES ('$name','$mobile','User','$pass','$email','$company','$pin','$pan','$aadhaar','$location','$key','$today')";
        $result = mysqli_query($conn, $register);

        $msg = "Dear $name thanks For Registering Us
        Your Username: $mobile
        Your Password: $password
        Thanks & Regards
        Khilaadixpro";
        $encodedMsg = urlencode($msg);
   
   if($result){
       //file_get_contents("https://wamsg.tk/wa.php?api_key=Wn62PIQ09X8BiY7iOtnEmgBCFFTDM3&sender=918145511275&number=91$mobile&message=$encodedMsg");
        echo '
    <script>
        Swal.fire({
            title: "Congratulations! User Added!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "success"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "dashboard"; // Replace with your desired redirect URL
            }
        });
    </script>
';
exit;
   }else{
        echo '
    <script>
        Swal.fire({
            title: "Opps! Something Went Wrong!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "adduser"; // Replace with your desired redirect URL
            }
        });
    </script>
';
exit;
   }
  }
  }
  ?>
<main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-user-plus"></i> Add User</h1>
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

						<h4 class="page-title">My Profile</h4>	
										
						<div class="row row-card-no-pd">							
							<div class="col-md-12">

 <form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <div class="col-md-6 mb-2">
  <label>Mobile Number</label>
  <input type="text" name="mobile" placeholder="Enter Mobile Number" class="form-control"
         oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required />
</div>
    <div class="col-md-6 mb-2"><label>Password</label> <input type="password" name="password" placeholder="Enter Password" class="form-control" required="" /></div>
  
    <div class="col-md-6 mb-2"><label>Email Address</label> <input type="email" name="email" placeholder="Enter Email Address" class="form-control" required="" /></div>
    <div class="col-md-6 mb-2"><label>Name</label> <input type="text" name="name" placeholder="Enter Name" class="form-control" required="" /></div>
    <div class="col-md-6 mb-2"><label>Company</label> <input type="text" name="company" placeholder="Enter Company" class="form-control" required="" /></div>
    
    <div class="col-md-6 mb-2">
  <label>Area Pin</label>
  <input type="text" name="pin" placeholder="Area Pin" class="form-control"
         oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);" required />
</div>

    <div class="col-md-6 mb-2"><label>PAN Number</label> <input type="text" name="pan" placeholder="Enter PAN Number" class="form-control" onkeypress="if(this.value.length==10) return false;" required="" /></div>
    
    <div class="col-md-6 mb-2">
  <label>Aadhaar Number</label>
  <input type="text" name="aadhaar" placeholder="Enter Aadhaar Number" class="form-control"
         oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);" required />
</div>
    <div class="col-md-12 mb-2"><label>Location</label> <input type="text" name="location" placeholder="Enter Location" class="form-control" required="" /></div>
    <div class="col-md-12 mb-2 mt-2"><button type="submit" name="create" class="btn btn-primary btn-sm">Add Now</button>
    </div>

</form>


              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <script src="js/main.js"></script>
    <script src="js/mainscript.js"></script>
    
    </body>
    </html>
    