<?php
							
// Function to sanitize user input
session_start();
include("../config.php");
include("config.php");


						
$mobile = $_SESSION['username'];
$user = $conn->query("SELECT * FROM users WHERE mobile = '$mobile'")->fetch_assoc();	

if (isset($_POST['buypluginbtn'])) {
  	    
$amount = round($_POST['amount']);
$planid = $_POST['plugin_name'];

// Define the number of days to add based on the plan ID
    switch ($planid) {
        case 'Android SDK':
            $amount = 649;
            break;
        case 'PHP SDK':
            $amount = 209;
            break;
        case 'Java SDK':
            $amount = 349;
            break;
        case 'Python SDK':
            $amount = 625;
            break;
        case 'C# SDK':
            $amount = 377;
            break;
        case 'Ruby SDK':
            $amount = 358;
            break;
        case 'JavaScript SDK':
            $amount = 399;
            break;
        case 'C++ SDK':
            $amount = 289;
            break;
        case 'Kotlin SDK':
            $amount = 279;
            break;
        case 'Swift SDK':
            $amount = 369;
            break;
        case 'WHMCS Modules':
            $amount = 649;
            break;
            case 'WordPress Plugin':
            $amount = 555;
            break;
        case 'Colour Prodction sdk':
            $amount = 250;
            break;
        default:
            $amount = 500;
            break;
    }
    
    switch($user["plan_id"]){
        case 1:
        $percent = 25;
        break;
        case 2:
        $percent = 50;
        break;
        case 3:
        $percent = 75;
        break;
        case 4:
        $percent = 100;
        break;
        default:
        $percent = 0;
        
    }
    
$finalamount = $amount - ($amount * ($percent / 100));

$order_id='IMB'.time().rand(11111,99999);
setcookie('order_id_cookie', $order_id, time() + 3600, '/'); // Expires in 1 hour (adjust the expiration time as needed)
setcookie('planid_cookie', $planid, time() + 3600, '/'); // Expires in 1 hour (adjust the expiration time as needed)
// URL of the PHP page
$url = 'https://' . $_SERVER["SERVER_NAME"] . '/api/create-order';

$callbackurl = "https://" . $_SERVER["SERVER_NAME"] . "/merchant/lib/plg_callback";
// Data to be sent in the POST request
$data = array(
    'customer_mobile' => $user["mobile"],
    'user_token' => $token,
    'amount' => $finalamount,
    'order_id' => $order_id,
    'redirect_url' => $callbackurl,
    'remark1' => $user["email"],
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
    echo 'Failed to decode JSON response or missing payment URL.';
}

}
?>