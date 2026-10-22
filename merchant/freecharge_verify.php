<?php
require_once('header.php');
session_start(); // Make sure session is started

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verifyotp'])) {

    // Step 1: Sanitize inputs
    $no = filter_var($_REQUEST['number'], FILTER_SANITIZE_STRING);
    $otp = filter_var($_POST['OTP'], FILTER_VALIDATE_INT);
    $otpId = filter_var($_POST['otpid'], FILTER_SANITIZE_STRING);

    // Step 2: Verify OTP via API
    $url = "https://miniapi.shop/freecharge/f-verify_otp";
    $data = [
        'otp' => $otp,
        'otpId' => $otpId
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
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
        $cookie = $responseArray['app_fc'];
        $upiid = $responseArray['upi_id'];

        // Step 4: Get user token using mobile from session
        $ssid = $_SESSION['username'];
        $getUserTokenQuery = "SELECT user_token FROM users WHERE mobile = '$ssid'";
        $getUserTokenResult = mysqli_query($conn, $getUserTokenQuery);
        $user_token = '';

        if ($getUserTokenResult && mysqli_num_rows($getUserTokenResult) > 0) {
            $userData = mysqli_fetch_assoc($getUserTokenResult);
            $user_token = $userData['user_token'];
        }

        // Step 5: Update freecharge table
        $updateQuery = "UPDATE freecharge SET cookie='$cookie', upi_id='$upiid', status='Active', user_token='$user_token' WHERE number='$no'";
        $result = mysqli_query($conn, $updateQuery);

        if ($result && mysqli_affected_rows($conn) > 0) {

            // Step 6: Update users table freecharge_connected = Yes
            $updateUserStatus = "UPDATE users SET freecharge_connected='Yes' WHERE mobile='$ssid'";
            mysqli_query($conn, $updateUserStatus);

            // Step 7: Check route and deactivate other merchants
            $fetchRouteQuery = $conn->query("SELECT route FROM users WHERE user_token = '$user_token'");
            if ($fetchRouteQuery && $fetchRouteQuery->num_rows > 0) {
                $routeData = $fetchRouteQuery->fetch_assoc();
                $route = $routeData['route'];

                if ($route == 0) {
                    $tablesarr = ["bharatpe_tokens", "hdfc", "googlepay_tokens", "merchant", "paytm_tokens", "phonepe_tokens"];
                    $connected_merarr = ["sbi_connected", "phonepe_connected", "paytm_connected", "hdfc_connected", "bharatpe_connected", "googlepay_connected"];

                    foreach ($tablesarr as $table) {
                        $conn->query("UPDATE `$table` SET status = 'Deactive' WHERE user_token = '$user_token'");
                    }

                    foreach ($connected_merarr as $connected) {
                        $conn->query("UPDATE users SET $connected = 'No' WHERE user_token = '$user_token'");
                    }
                }
            }

            // ✅ Show success
            echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>$("#loading_ajax").hide();
                Swal.fire({
                    icon: "success",
                    title: "Congratulations! Your Freecharge has been Connected Successfully!",
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
}
?>

<!-- Bootstrap & Plugins -->
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
<script src="assets/plugins/metismenu/metisMenu.min.js"></script>
<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
<script src="assets/js/main.js"></script>
