<?php
							
// Function to sanitize user input
session_start();
include("config.php");
require_once('../config.php');
							
if (isset($_POST['upigate'])) {
  	    
$amount=$_POST['amount'];
$discount=$_POST['discountamount'];
$planid=$_POST['planid'];
$total_plan_months=$_POST['total_plan_months'];

 // Fetch user from the database
 
    $mobile = $_SESSION["username"];
    $userquery = "SELECT mobile,email FROM users WHERE mobile = '$mobile'";
    $userresult = mysqli_query($conn, $userquery);
    $userdata = mysqli_fetch_assoc($userresult);
    $mobileno = $userdata['mobile'];
    $emailid = $userdata['email'];
    
    //  $gstRate = 0.18;
    // $gstAmount = $amount * $gstRate;
    // $amount = $amount + $gstAmount;
    
    $amount = $amount-$discount;

$order_id='IMB'.time().rand(1000000000,9999999999);
setcookie('order_id_cookie', $order_id, time() + 3600, '/'); // Expires in 1 hour (adjust the expiration time as needed)
setcookie('planid_cookie', $planid, time() + 3600, '/'); // Expires in 1 hour (adjust the expiration time as needed)
setcookie('plan_months_cookie', $total_plan_months, time() + 3600, '/'); // Expires in 1 hour (adjust the expiration time as needed)
// URL of the PHP page
$url = 'https://' . $_SERVER["SERVER_NAME"] . '/api/create-order';

$callbackurl = "https://" . $_SERVER["SERVER_NAME"] . "/merchant/lib/callback";
// Data to be sent in the POST request
$data = array(
    'customer_mobile' => $mobileno,
    'user_token' => $token,
    'amount' => $amount,
    'order_id' => $order_id,
    'redirect_url' => $callbackurl,
    'remark1' => $emailid,
    'remark2' => 'test2',
);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL session and store the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);




// Decode the JSON response
$jsonResponse = json_decode($response, true);

// Check if decoding was successful
if ($jsonResponse !== null && isset($jsonResponse['result']['payment_url'])) {
    // Redirect the user to the payment URL
    $paymentUrl = $jsonResponse['result']['payment_url'];
    header('Location: ' . $paymentUrl);
    exit;
} else {
    echo $response;
}

}
?>