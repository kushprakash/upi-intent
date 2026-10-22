<?php
include "header.php";

// ini_set("display_errors",true);
// error_reporting(E_ALL);

function bharatpe_trans($merchantId, $token, $cookie) {
    // Calculate the date range
    $fromDate = date('Y-m-d', strtotime('-2 days'));
    $toDate = date('Y-m-d');

    // Initialize cURL
    $curl = curl_init();

    // Set up cURL options
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://payments-tesseract.bharatpe.in/api/v1/merchant/transactions?module=PAYMENT_QR&merchantId=' . $merchantId . '&sDate=' . $fromDate . '&eDate=' . $toDate,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'token: ' . $token,
            'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Mobile Safari/537.36',
            'Cookie: ' . $cookie
        ),
    ));

    // Execute cURL request
    $response = curl_exec($curl);
    curl_close($curl);

    // Decode the JSON response
    $decodedResponse = json_decode($response, true);

    // Return the decoded JSON response
    return $decodedResponse;
}






if(isset($_POST['verifyotp'])) {
    
    $bbbyteuserid=$_SESSION['user_id'];
    
    // Sanitize Inputs to prevent SQL Injection and String Breakage
    $bbytebharatpeuserid = $userdata['user_token']; 
    $bbytebharatpeusermid = mysqli_real_escape_string($conn, $_POST["MID"] ?? '');
    $bbytebharatpeusertoken = mysqli_real_escape_string($conn, $_POST["token"] ?? '');
    $bbytebharatpeusercookie = mysqli_real_escape_string($conn, $_POST["cookie"] ?? '');
    $upi_id = mysqli_real_escape_string($conn, $_POST["upi_id"] ?? '');
    
    // Raw inputs for API call (cURL handles its own encoding usually, but we keep them raw for the API)
    $raw_mid = $_POST["MID"] ?? '';
    $raw_token = $_POST["token"] ?? '';
    $raw_cookie = $_POST["cookie"] ?? '';
    
     // Check if we have an internal ID to update (fixes duplicate issue)
    $internal_id_to_update = isset($_POST['internal_merchant_id']) ? intval($_POST['internal_merchant_id']) : 0;


    // Call the function and store the response in a variable
    $response = bharatpe_trans($raw_mid, $raw_token, $raw_cookie);
    
    $bbytebharatpestatus = false; // Default to false
    $debug_msg = "";

    // Check if the response is an array or an object
    if (is_array($response) || is_object($response)) {
    
        // Check if the response has 'message' as 'SUCCESS' and 'status' as true
        if (isset($response['message']) && $response['message'] === 'SUCCESS' &&
            isset($response['status']) && $response['status'] === true) {
            
            $bbytebharatpestatus = true;

        } else {
             $bbytebharatpestatus = false;
             $debug_msg = "API Response not SUCCESS. Msg: " . ($response['message'] ?? 'N/A');
        }

    } else {
        // Not a valid JSON response
        $bbytebharatpestatus = false;
        // Check if response is empty string or raw html
        $debug_msg = "Invalid/Empty Response from API";
        if(!empty($response) && is_string($response)) {
             // Limit length to avoid breaking JS
             $debug_msg .= ": " . substr(strip_tags($response), 0, 100);
        }
    }


    if($bbytebharatpestatus) {   
        
        // Success Block
        
        // 1. Update User Status
        $sqlUpdateUser = "UPDATE users SET bharatpe_connected='Yes' WHERE user_token='$bbytebharatpeuserid'";
        $resultUpdateUser = mysqli_query($conn, $sqlUpdateUser);

        // 2. Insert/Update Token
        
        // Logic to prevent duplicate "Active" entries if we are just verifying an existing one
        if ($internal_id_to_update > 0) {
            // Case A: specific update requested (from Dashboard Verify button)
             $sqlw = "UPDATE bharatpe_tokens SET merchantId='$bbytebharatpeusermid', token='$bbytebharatpeusertoken', status='Active', Upiid = '$upi_id', user_id='$bbbyteuserid', cookie='$bbytebharatpeusercookie', user_token='$bbytebharatpeuserid' WHERE id='$internal_id_to_update'";
        } else {
            // Case B: New insertion (Always Insert for multiple support)
            // Removed "Find Deactive" logic to allow unlimited new additions
            
             $sqlw = "INSERT INTO bharatpe_tokens (merchantId, token, status, Upiid, user_id, cookie, user_token) 
             VALUES ('$bbytebharatpeusermid', '$bbytebharatpeusertoken', 'Active', '$upi_id', '$bbbyteuserid', '$bbytebharatpeusercookie', '$bbytebharatpeuserid')";
             
             // Optional: Add ON DUPLICATE KEY UPDATE only if merchantId is unique key and we want to update THAT specific merchant
             // But for safety to allow duplicates if DB allows, we stick to simple INSERT. 
             // If merchantId must be unique, the previous ON DUPLICATE KEY UPDATE was fine, but we'll try simple INSERT to act like "Add New".
             // If DB errors on duplicate merchantId, we'll see.
        }
        
        $result = mysqli_query($conn, $sqlw);

        if ($result) {
            
           // Code to deactivate other merchants removed as per user request (Fix #1)
           // If needed, insert correct logic here, but for now we allow multiple.
        
            // Show SweetAlert2 success message
             echo '<script src="js/jquery-3.2.1.min.js"></script>';
             echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
             echo '<script>
            $("#loading_ajax").hide();
                Swal.fire({
                    icon: "success",
                    title: "Congratulations! Your Bharatpe Hasbeen Connected Successfully!",
                    showConfirmButton: true,
                    confirmButtonText: "Ok!",
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "upisettings";
                    }
                });
            </script>';
        
        } else {
            // Query failed (SQL Error)
            $error_d = mysqli_error($conn);
             echo '<script src="js/jquery-3.2.1.min.js"></script>';
             echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
             echo '<script>
            $("#loading_ajax").hide();
                Swal.fire({
                    icon: "error",
                    title: "Database Error!",
                    text: "Could not save details. Please contact admin.",
                    footer: '. json_encode($error_d) .',
                    showConfirmButton: true,
                    confirmButtonText: "Ok!",
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "upisettings";
                    }
                });
            </script>';
            exit;
        }

    } else {
        
        // Failure Block (Invalid Details or API Error)
        
         // Prepare Debug Info safe for JS
         $js_debug = json_encode($response); // Encode the whole array/object
         if(!$js_debug) $js_debug = '"' . $debug_msg . '"';

         echo '<script src="js/jquery-3.2.1.min.js"></script>';
         echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
         echo '<script>
        $("#loading_ajax").hide();
            Swal.fire({
                icon: "error",
                title: "Invalid BharatPe Details !!",
                text: "Response: " + '. $js_debug .',
                showConfirmButton: true,
                confirmButtonText: "Ok!",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "upisettings";
                }
            });
        </script>';
        exit;
    }
    
}

// bharatpe end verify


//form start

if(isset($_POST['Verify'])) {


    // Constraint Removed: Allow multiple merchants
    // if ($userdata['bharatpe_connected']=="Yes"){ ... }
    
    $bharatpe_mobile = $_POST["bharatpe_mobile"];
    $passed_merchant_id = isset($_POST["merchant_id"]) ? $_POST["merchant_id"] : '';

    // Now, you can use the $bharatpe_mobile variable as needed
?>

<main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-user"></i> Bharatpe UPI Settings</h1>
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
                <h4 class="page-title">Bharatpe UPI Settings</h4>
                <div class="row row-card-no-pd">
                    <div class="col-md-12">
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mb-2">
                             <!-- Hidden field to pass the Merchant ID -->
                            <input type="hidden" name="internal_merchant_id" value="<?php echo htmlspecialchars($passed_merchant_id); ?>">
                            
                            <div class="row" id="merchant">
                                <div class="col-md-4 mb-2"> 
                                    <label>Enter Merchant ID</label> 
                                    <input type="text" name="MID" placeholder="Enter Merchant ID" class="form-control" required=""> 
                                </div>
                                <div class="col-md-4 mb-2"> 
                                    <label>Enter BharatPe Cookie</label> 
                                    <input type="text" name="cookie" placeholder="Enter Bharatpe Cookie"  class="form-control" required=""> 
                                </div>
                                <div class="col-md-4 mb-2"> 
                                    <label>Enter BharatPe Token</label> 
                                    <input type="text" name="token" placeholder="Enter BharatPe Token" class="form-control" required="">
                                </div>
                                <div class="col-md-6 mb-2"> 
                                    <label>Enter BharatPe UPI Id</label> 
                                    <input type="text" name="upi_id" placeholder="Enter BharatPe UPI Id" class="form-control" required="">
                                </div>
                                <div class="col-md-4 mb-2"> 
                                    <label>&nbsp;</label> 
                                    <button type="submit" name="verifyotp" class="btn btn-primary btn-block">Verify BharatPe</button> 
                                </div>
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
    </body>
    </html>
<?php
} // End of if(isset($_POST['Verify']))

?>
