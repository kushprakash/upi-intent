<?php
session_start();
require_once('../config.php');
require_once('config.php');

$orderid = $_COOKIE['order_id_cookie'];

// API endpoint URL
$url = "https://" . $_SERVER["SERVER_NAME"] . "/api/check-order-status";

// POST data
$postData = array(
    "user_token" => $token,
    "order_id" => $orderid
);

// Initialize cURL session
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

// Execute cURL session and get the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
    exit;
}

// Close cURL session
curl_close($ch);

// Decode the JSON response
$responseData = json_decode($response, true);

// Check if the API call was successful
if ($responseData["status"] === "COMPLETED") {
    $planid = $_COOKIE['planid_cookie'];
    $email = $_SESSION['username'];
    $amount = $responseData["result"]["amount"];
    $user = $conn->query("SELECT * FROM users WHERE mobile = '$email'")->fetch_assoc();

    // Update the expiry date in the database
    $sql = "INSERT INTO `plugins_list`(`user_id`, `plugin_name`, `amount`, `paid_status`)
    VALUES ('{$user["id"]}','$planid','$amount','{$responseData["status"]}')";
    $updateResult = mysqli_query($conn, $sql);

    if ($updateResult) {
        
        header("Location: https://" . $_SERVER["SERVER_NAME"] . "/merchant/plugin");
        exit;
    } else {
        // Redirect to subscription page on error
        header("Location: https://" . $_SERVER["SERVER_NAME"] . "/merchant/plugin");
        exit;
    }
} else {
    // API call failed
    $errorMessage = $responseData["message"];
    echo "API Error: $errorMessage";
}
?>
