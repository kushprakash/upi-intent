<?php
require_once('header.php');

function fetchTokensAndCsrf() {
    // URL to fetch the initial tokens
    $url = "https://enterprise.bharatpe.in/";

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true); // Include headers in the response
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
    ]);

    // Execute the request
    $response = curl_exec($ch);

    // Get header size and extract headers
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $header_size);
    $body = substr($response, $header_size);

    // Close the cURL session
    curl_close($ch);

    // Extract _token from the response body (if present)
    preg_match('/name="_token" value="([^"]+)"/', $body, $token_match);

    // Extract cookies from headers
    preg_match('/XSRF-TOKEN=([^;]+)/', $headers, $xsrf_token_match);
    preg_match('/bharatpe_session=([^;]+)/', $headers, $session_token_match);

    if (!empty($xsrf_token_match[1]) && !empty($session_token_match[1]) && !empty($token_match[1])) {
        return [
            'XSRF-TOKEN' => $xsrf_token_match[1],
            'bharatpe_session' => $session_token_match[1],
            '_token' => $token_match[1],
        ];
    } else {
        die("Failed to fetch tokens or CSRF.");
    }
}

function verifyOtp($mobile,$otp,$uuid,$tokens) {
    // API endpoint for requesting OTP

    $url = "https://enterprise.bharatpe.in/v1/api/user/verifyotp";

    
        $postData = http_build_query([
        'mobile' => $mobile,
        'uuid' => $uuid,
        'otp' => $otp,
        '_token' => $tokens['_token'], // Use the extracted token here
    ]);

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options for POST request
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        'Accept: application/json, text/javascript, /; q=0.01',
        'X-Requested-With: XMLHttpRequest',
        'Origin: https://enterprise.bharatpe.in',
        'Referer: https://enterprise.bharatpe.in/',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        "Cookie: XSRF-TOKEN={$tokens['XSRF-TOKEN']}; bharatpe_session={$tokens['bharatpe_session']}"
    ]);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo "cURL error: " . curl_error($ch);
    } else {
     
        return $response;
    }

    // Close cURL
    curl_close($ch);
}

function getProfile($access_token){
    
// API endpoint URL
$url = 'https://api-merchant.bharatpe.in/merchant/v3/getmerchantinfo';

// Headers for the API request
$headers = array(
    'sec-ch-ua-platform: "Windows"',
    'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Safari/537.36',
    'accept: application/json, text/javascript, */*; q=0.01',
    'sec-ch-ua: "Not A(Brand";v="8", "Chromium";v="132", "Google Chrome";v="132"',
    'token: ' . $access_token,
    'sec-ch-ua-mobile: ?0',
    'origin: https://enterprise.bharatpe.in',
    'sec-fetch-site: same-site',
    'sec-fetch-mode: cors',
    'sec-fetch-dest: empty',
    'referer: https://enterprise.bharatpe.in/',
    'accept-encoding: gzip, deflate, br, zstd',
    'accept-language: en-GB,en-US;q=0.9,en;q=0.8',
    'priority: u=1, i'
);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url); // Set the URL
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Set the headers
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
curl_setopt($ch, CURLOPT_ENCODING, ''); // Automatically handle gzip/deflate encoding

// Execute cURL session and capture the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    return 'cURL Error: ' . curl_error($ch);
} else {
  
  return $response;
}

// Close the cURL session
curl_close($ch);
    
}

function getUpiid($merchantId,$access_token){
    
// API URL to get QR code
$url = "https://payments-tesseract.bharatpe.in/api/merchant/v1/downloadQr?merchantId=" . $merchantId;

// API request headers
$headers = [
    "Accept: application/json",
    "User-Agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/132.0.0.0 Mobile Safari/537.36",
    "Token: $access_token" // Secure way me store karein
];

// cURL request to fetch QR code URL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

// Response ko JSON me decode karein
$data = json_decode($response, true);

// QR Code URL extract karein
if ($data['status'] && !empty($data['data']['url'])) {
    $qrUrl = $data['data']['url'];

    // QR Code image ko decode karne ke liye ZXing API use karein
    $zxingApiUrl = "https://zxing.org/w/decode?u=" . urlencode($qrUrl);

    // cURL request ZXing API ke liye
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $zxingApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $zxingResponse = curl_exec($ch);
    curl_close($ch);

    // QR Code ke data se UPI ID extract karein
    if (preg_match('/upi:\/\/pay\?pa=([^&]+)/', $zxingResponse, $matches)) {
        $upiId = urldecode($matches[1]);
        return htmlspecialchars($upiId);
    } else {
        return "Not Found!";
    }
} else {
    return "Not Found!";
}
    
}

// Fetch tokens and CSRF

// Verify CSRF token
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verifyotp'])) {
    
   
    // Sanitize and validate the inputs
    $no = filter_var($_REQUEST['number'], FILTER_VALIDATE_INT);
    $otp = filter_var($_POST['otp'], FILTER_VALIDATE_INT);
    $otpId = filter_var($_POST['uuid'], FILTER_SANITIZE_STRING);
    $merchant_id= $_POST["merchant_id"];
    $tokens = fetchTokensAndCsrf();
    $bbytebharatpeuserid=  $userdata['user_token'];
    $bbbyteuserid=$_SESSION['user_id'];
    
       
   $verifyotpres = verifyOtp($no,$otp,$otpId,$tokens);
    // Decode the response from the server
    $responseArray = json_decode($verifyotpres, true);

    if (isset($responseArray['success']) && $responseArray['success'] == true) {
        $access_token = $responseArray['data']["accessToken"];
            

      $profiledetails_res = getProfile($access_token);
      $responseArray1 = json_decode($profiledetails_res, true);
      
      
      $merchantid = $responseArray1['data']['merchantId'];
      
      $upi_id = getUpiid($merchantid,$access_token);
    
    
$sqlw = "UPDATE bharatpe_tokens SET merchantId='$merchantid', token='$access_token', status='Active', Upiid = '$upi_id', user_id='$bbbyteuserid' WHERE user_token='$bbytebharatpeuserid' AND id = '$merchant_id'";
$result = mysqli_query($conn, $sqlw);

            if ($result && mysqli_affected_rows($conn) > 0) {
                
               $sqlUpdateUser = "UPDATE users SET bharatpe_connected='Yes' WHERE user_token='$bbytebharatpeuserid'";
    $resultUpdateUser = mysqli_query($conn, $sqlUpdateUser); 

$fetchuser = $conn->query("SELECT route FROM `users` WHERE user_token='$bbytebharatpeuserid'")->fetch_assoc();

if($fetchuser["route"] == 0){       
  // inactive other merchant
$tablesarr = ["hdfc","freecharge","googlepay_tokens","merchant","paytm_tokens","phonepe_tokens"];
$connected_merarr = ["sbi_connected","phonepe_connected","paytm_connected","freecharge_connected","hdfc_connected","googlepay_connected"];

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
echo '<script>$("#loading_ajax").hide();
    Swal.fire({
        icon: "success",
        title: "Congratulations! Your BharatPe Has been Connected Successfully!",
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

} else {
    // Show SweetAlert2 error message
            
$error="hi";
    echo '<script src="js/jquery-3.2.1.min.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
    echo '<script>$("#loading_ajax").hide();
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Failed to update the database. Error: ' . $error . '",
            showConfirmButton: true,
            confirmButtonText: "Ok",
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    </script>';
}

        } else {
            $errorMessage = isset($responseArray['message']) ? $responseArray['message'] : 'Unknown error';

            // Show SweetAlert2 error message
            
            echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>
            $("#loading_ajax").hide();
                Swal.fire({
                    icon: "error",
                    title: "' . $errorMessage . '!",
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
            exit();
        }

    // Close the cURL session
    curl_close($ch);
}
?>

<!--bootstrap js-->
  <script src="assets/js/bootstrap.bundle.min.js"></script>

  <!--plugins-->
  <script src="assets/js/jquery.min.js"></script>
  <!--plugins-->
  <script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="assets/plugins/metismenu/metisMenu.min.js"></script>
  <script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="assets/js/main.js"></script>


</body>

</html>