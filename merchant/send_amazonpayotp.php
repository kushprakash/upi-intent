<?php
include "header.php";

// ini_set("display_errors",true);
// error_reporting(E_ALL);

if(isset($_POST['verifyotp'])) {
    
    $bbbyteuserid=$_SESSION['user_id'];
    
  $bbytebharatpeuserid=  $userdata['user_token'];
  $merchant_id= $_POST["merchant_id"];
  $bbytebharatpeusercookie= $_POST["cookie"];
  $upi_id = $_POST["upi_id"];
  
     $sqlUpdateUser = "UPDATE users SET amazonpay_connected='Yes' WHERE user_token='$bbytebharatpeuserid'";
    $resultUpdateUser = mysqli_query($conn, $sqlUpdateUser);
 


if($resultUpdateUser) {   
    
    
$sqlw = "UPDATE amazon_pay SET status='Active', upi_id = '$upi_id', user_id=$bbbyteuserid, cookie='$bbytebharatpeusercookie' WHERE user_token='$bbytebharatpeuserid' AND id = '$merchant_id'";
$result = mysqli_query($conn, $sqlw);



   if ($result) {
       
$fetchuser = $conn->query("SELECT route FROM `users` WHERE user_token='$bbytebharatpeuserid'")->fetch_assoc();

if($fetchuser["route"] == 0){       
  // inactive other merchant
$tablesarr = ["hdfc","freecharge","googlepay_tokens","merchant","paytm_tokens","phonepe_tokens","bharatpe_tokens"];
$connected_merarr = ["sbi_connected","phonepe_connected","paytm_connected","freecharge_connected","hdfc_connected","googlepay_connected","bharatpe_connected"];

foreach($tablesarr as $tables){
    $fetchmerchant = $conn->query("SELECT user_token FROM `$tables` WHERE user_token = '$bbytebharatpeuserid' AND status = 'Active'");
    if($fetchmerchant->num_rows > 0){
        $conn->query("UPDATE $tables SET status = 'Deactive' WHERE user_token = '$bbytebharatpeuserid'");
    }
}

foreach($connected_merarr as $connected){
   $conn->query("UPDATE users SET $connected = 'No' WHERE user_token = '$bbytebharatpeuserid'");
}

}
    
     
    // Show SweetAlert2 success message
                             echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
    Swal.fire({
        icon: "success",
        title: "Congratulations! Your Amazonpay Hasbeen Connected Successfully!",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings"; // Redirect to "upisettings" when the user clicks the confirm button
        }
    });
</script>';

    //exit;
    
    
} else {
    // Query failed
  //  echo "Error: " . mysqli_error($conn);
  
   // Show SweetAlert2 error message
                             echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
    Swal.fire({
        icon: "error",
        title: "Please Try Again Later!!",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings"; // Redirect to "upisettings" when the user clicks the confirm button
        }
    });
</script>';
exit;

  
}

}
    
}

// bharatpe end verify


//form start

if(isset($_POST['Verify'])) {


    if ($userdata['amazonpay_connected']=="Yes" && $userdata['plan_id'] < 5){
        
         
        // Show SweetAlert2 error message
      echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
Swal.fire({
icon: "error",
title: "Merchant Already Connected !!",
showConfirmButton: true, // Show the confirm button
confirmButtonText: "Ok!", // Set text for the confirm button
allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
}).then((result) => {
if (result.isConfirmed) {
window.location.href = "upisettings"; // Redirect to "upisettings" when the user clicks the confirm button
}
});
</script>';
exit;
}
    
    $amazonpay_mobile = $_POST["amazonpay_mobile"];
    $merchant_id = $_POST["merchant_id"];

    // Now, you can use the $bharatpe_mobile variable as needed
?>

<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-user"></i> Amazonpay UPI Settings</h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
        </ul>
    </div>
    <div class="tile mb-4">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row row-card-no-pd">
                        <div class="col-md-12">
                            <div class="main-panel">
                                <div class="content">
                                    <div class="container-fluid">
                                        <!--<h4 class="page-title">Amazonpay UPI Settings</h4>-->
                                        <div class="row row-card-no-pd">
                                            <div class="col-md-12">
                                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mb-2">
                                                    <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>">
                                                    <div class="row" id="merchant">
                                                        <div class="col-md-4 mb-2"> 
                                                            <label>Enter Amazonpay Cookie</label> 
                                                            <input type="text" name="cookie" placeholder="Enter Amazonpay Cookie"  class="form-control" required=""> 
                                                        </div>
                                                        <div class="col-md-6 mb-2"> 
                                                            <label>Enter Amazonpay UPI Id</label> 
                                                            <input type="text" name="upi_id" placeholder="Enter Amazonpay UPI Id" class="form-control" required="">
                                                        </div>
                                                        <div class="col-md-4 mb-2"> 
                                                            <label>&nbsp;</label> 
                                                            <button type="submit" name="verifyotp" class="btn btn-primary btn-block">Verify Amazonpay</button> 
                                                        </div>
                                                        <!-- Full-width Visit Button -->
                                                        <div class="col-md-4 mb-2">
                                                        <label>&nbsp;</label>
                                                        <a href="https://www.amazon.in/amazonpay/home?ref_=nav_cs_apay" 
                                                           target="_blank" 
                                                           class="btn btn-block custom-btn">
                                                           <i class="fa fa-link"></i> Visit Amazonpay
                                                        </a>
                                                    </div>
                                                    <style>
                                                        .custom-btn {
                                                            background-color: #ff5722; /* Unique color (e.g., orange) */
                                                            color: white;            /* White text for contrast */
                                                            border: none;            /* Remove default border */
                                                        }
                                                                                                            .custom-btn:hover {
                                                        background-color: #e64a19; /* Darker shade for hover effect */
                                                        }
                                                    </style>

                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script src="js/jquery-3.2.1.min.js"></script>
                            <script>
                                $("#loading_ajax").hide();
                            </script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
} // End of if(isset($_POST['Verify']))
?>