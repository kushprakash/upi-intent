<?php
require_once('header.php');
session_start(); // Make sure session is started

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verifyotp'])) {

    // Step 1: Sanitize inputs
    $no = filter_var($_REQUEST['number'], FILTER_SANITIZE_STRING);
    $otp = filter_var($_POST['OTP'], FILTER_VALIDATE_INT);
    $otpId = filter_var($_POST['otpid'], FILTER_SANITIZE_STRING);

    // Step 2: Verify OTP via API
    $url = "https://miniapi.shop/api/quintustech/verify_otp.php?mobile=$no&otp=$otp";
  

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_GET, true);
    $response = curl_exec($ch);

    // Handle error
    if (curl_errno($ch)) {
        echo 'Curl Error: ' . curl_error($ch);
        exit;
    }

    $responseArray = json_decode($response, true);
    curl_close($ch);

    // Step 3: If success
    if (isset($responseArray['status']) && $responseArray['status'] === 'success') {
        $accessToken = $responseArray['accessToken'];
        
         // Step 2: fetch UPI id via API
    $url = "https://miniapi.shop/api/quintustech/upi_id.php?token=$accessToken";
  

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_GET, true);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Curl Error: ' . curl_error($ch);
        exit;
    }

    $responseArray = json_decode($response, true);
    curl_close($ch);

    // Step 3: If success fetch upi id
    if (isset($responseArray['status']) && $responseArray['status'] === 'SUCCESS') {
        $upiid = $responseArray['vpa'];
        
        // Step 4: Get user token using mobile from session
        $ssid = $_SESSION['username'];
        $getUserTokenQuery = "SELECT id,user_token FROM users WHERE mobile = '$ssid'";
        $getUserTokenResult = mysqli_query($conn, $getUserTokenQuery);
        $user_token = '';

        if ($getUserTokenResult && mysqli_num_rows($getUserTokenResult) > 0) {
            $userData = mysqli_fetch_assoc($getUserTokenResult);
            $user_token = $userData['user_token'];
            $user_id = $userData['id'];
        }

        // Step 5: Update quintuspay table
        $updateQuery = "UPDATE quintus_tokens SET accessToken='$accessToken', Upiid='$upiid', status='Active', user_token='$user_token', user_id='$user_id' WHERE phoneNumber='$no'";
        $result = mysqli_query($conn, $updateQuery);

        if ($result && mysqli_affected_rows($conn) > 0) {

            // Step 6: Update users table quintuspay_connected = Yes
            $updateUserStatus = "UPDATE users SET quintuspay_connected='Yes' WHERE mobile='$ssid'";
            mysqli_query($conn, $updateUserStatus);

            // Step 7: Check route and deactivate other merchants - DISABLED
            // Step 7: Check route and deactivate other merchants - REMOVED


            // ✅ Show success
            echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>$("#loading_ajax").hide();
                Swal.fire({
                    icon: "success",
                    title: "Congratulations! Your Quintus Pay has been Connected Successfully!",
                    showConfirmButton: true,
                    confirmButtonText: "Ok!",
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "dashboard";
                    }
                });
            </script>';
        } else {
            // ❌ Failed to update database
            echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>$("#loading_ajax").hide();
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Failed to update the database.",
                    showConfirmButton: true,
                    confirmButtonText: "Ok"
                });
            </script>';
        }
        
    } else {
        // ❌ API response failed
        $errorMessage = isset($responseArray['message']) ? $responseArray['message'] : 'Unknown error';
        echo '<script src="js/jquery-3.2.1.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>$("#loading_ajax").hide();
            Swal.fire({
                icon: "error",
                title: "' . $errorMessage . '!",
                showConfirmButton: true,
                confirmButtonText: "Ok!"
            });
        </script>';
    }
        
    } else {
        // ❌ API response failed
        $errorMessage = isset($responseArray['message']) ? $responseArray['message'] : 'Unknown error';
        echo '<script src="js/jquery-3.2.1.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>$("#loading_ajax").hide();
            Swal.fire({
                icon: "error",
                title: "' . $errorMessage . '!",
                showConfirmButton: true,
                confirmButtonText: "Ok!"
            });
        </script>';
    }
}
?>

<!-- Bootstrap & Plugins -->
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
<script src="assets/plugins/metismenu/metisMenu.min.js"></script>
<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
<script src="assets/js/main.js"></script>
