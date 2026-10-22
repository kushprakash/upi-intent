<?php
include "header.php";
// Function to sanitize user input

    

if(isset($_POST['verifyotp'])) {
   $bbbyteuserid=$_SESSION['user_id'];
    
  $bbytepaytmuserid=  $userdata['user_token'];
  $bbytepaytmusermid = sanitizeInput($_POST["MID"]);
  $bbytepaytmuserupiid = sanitizeInput($_POST["UPI"]);
  $merchant_id = sanitizeInput($_POST["merchant_id"]);
  
     $sqlUpdateUser = "UPDATE users SET paytm_connected='Yes' WHERE user_token='$bbytepaytmuserid'";
    $resultUpdateUser = mysqli_query($conn, $sqlUpdateUser);
    
   $sqlw = "UPDATE paytm_tokens SET MID='$bbytepaytmusermid', Upiid='$bbytepaytmuserupiid', status='Active', user_id=$bbbyteuserid WHERE user_token='$bbytepaytmuserid' AND id = '$merchant_id'";
$result = mysqli_query($conn, $sqlw);


   if ($result) {
       
$fetchuser = $conn->query("SELECT route FROM `users` WHERE user_token='$bbytepaytmuserid'")->fetch_assoc();

if($fetchuser["route"] == 0){
    
  // inactive other merchant
$tablesarr = ["bharatpe_tokens","freecharge","googlepay_tokens","merchant","hdfc","phonepe_tokens"];
$connected_merarr = ["sbi_connected","phonepe_connected","hdfc_connected","freecharge_connected","bharatpe_connected","googlepay_connected"];

foreach($tablesarr as $tables){
    $fetchmerchant = $conn->query("SELECT user_token FROM `$tables` WHERE user_token = '$bbytepaytmuserid' AND status = 'Active'");
    if($fetchmerchant->num_rows > 0){
        $conn->query("UPDATE $tables SET status = 'Deactive' WHERE user_token = '$bbytepaytmuserid'");
    }
}

foreach($connected_merarr as $connected){
   $conn->query("UPDATE users SET $connected = 'No' WHERE user_token = '$bbytepaytmuserid'");
}
   
} 
     
    // Show SweetAlert2 success message
    echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
    Swal.fire({
        icon: "success",
        title: "Congratulations! Your Paytm Hasbeen Connected Successfully!",
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

if(isset($_POST['Verify'])) {
    
    if ($userdata['paytm_connected']=="Yes" && $userdata['plan_id'] < 5){
        
         
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

    $paytm_mobile = sanitizeInput($_POST["paytm_mobile"]);
    $merchant_id = sanitizeInput($_POST["merchant_id"]);

    // Now, you can use the $paytm_mobile variable as needed
?>

<main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-user"></i> Paytm UPI Settings</h1>
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
                <h4 class="page-title">Paytm UPI Settings</h4>
                <div class="row row-card-no-pd">
                    <div class="col-md-12">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mb-2">
                            <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>">
                            <div class="row" id="merchant">
                                <div class="col-md-4 mb-2"> 
                                    <label>Enter Merchant ID</label> 
                                    <input type="text" name="MID" placeholder="Enter Merchant ID" class="form-control" required=""> 
                                </div>
                                <div class="col-md-4 mb-2"> 
                                    <label>Enter Number</label> 
                                    <input type="number" name="Number" placeholder="Enter Number" value="<?php echo $paytm_mobile; ?>" class="form-control" required=""> 
                                </div>
                                <div class="col-md-4 mb-2"> 
                                    <label>Enter UPI</label> 
                                    <input type="text" name="UPI" placeholder="Enter UPI" class="form-control" required="" value="dummy@Paytm">
                                </div>
                                <div class="col-md-4 mb-2"> 
                                    <label>&nbsp;</label> 
                                    <button type="submit" name="verifyotp" class="btn btn-primary btn-block">Verify Paytm</button> 
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    </body>
    </html>
<?php
} // End of if(isset($_POST['Verify']))

?>
