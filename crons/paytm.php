<?php
// Load DB credentials
define('ROOT_DIR', realpath(dirname(__FILE__)) . '/../');
include ROOT_DIR . 'pages/dbFunctions.php';
include ROOT_DIR . 'pages/dbInfo.php';

date_default_timezone_set("Asia/Kolkata");

// Fetch all pending orders
$sql = "SELECT byteTransactionId FROM orders WHERE status='PENDING'";
$orders = getXbyY($sql);

if (!$orders || count($orders) == 0) {
    echo "No pending orders\n";
    exit;
}

foreach ($orders as $o) {

    $byteTx = $o["byteTransactionId"];

    // Prepare POST data
    $postData = http_build_query([
        "byte_order_status" => $byteTx
    ]);

    // Target URL (your API)
    $url = "https://" . $_SERVER["SERVER_NAME"] . "/order3/payment-status";

    // Initialize cURL
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    echo "\nChecking: $byteTx => Response: $response | HTTP: $httpcode\n";

    // If success returned, update orders table directly
    if (trim($response) === "success") {
        runQuery("UPDATE orders SET status='SUCCESS' WHERE byteTransactionId='$byteTx'");
        echo "Updated ORDER => SUCCESS\n";
    }

    // If failed
    if (trim($response) === "FAILURE" || trim($response) === "FAILED") {
        runQuery("UPDATE orders SET status='FAILED' WHERE byteTransactionId='$byteTx'");
        echo "Updated ORDER => FAILED\n";
    }
}

echo "Cron cycle completed.\n";
?>
