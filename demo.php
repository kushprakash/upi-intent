<?php
include "merchant/config.php";
// URL of the PHP page
error_reporting(0);

$url = $site_url.'/api/create-order';
$token = '4f4f2d5860edb2ee76ba899d3b63bd02';
$orderid=rand(1000000000,9999999999);

// Data to be sent in the POST request a973542c803860e1a18b06815e59c30e
$data = array(
    'customer_mobile' => '9876543210',
    'user_token' => $token,
    'amount' => '1',
    'order_id' => $orderid,
    'redirect_url' => $site_url.'/success.php',
    'remark1' => 'imb1',
    'remark2' => 'imb2',
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

// echo $response;
// exit;

// Decode the JSON response
$jsonResponse = json_decode($response, true);

// Check if decoding was successful
if ($jsonResponse !== null) {
    
    
    // Redirect the user to the payment URL
    $paymentUrl = $jsonResponse['result']['payment_url'];
    header('Location: ' . $paymentUrl);
    exit;
    
} else {
    echo $response;
}
?>
