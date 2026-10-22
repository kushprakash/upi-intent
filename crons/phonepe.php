<?php
// LOAD DB
define('ROOT_DIR', realpath(dirname(__FILE__)) . '/../');
include ROOT_DIR . 'pages/dbFunctions.php';
include ROOT_DIR . 'pages/dbInfo.php';

date_default_timezone_set("Asia/Kolkata");

// FIND ALL PENDING ORDERS
$pending = getXbyY("SELECT order_id, byteTransactionId FROM orders WHERE status='PENDING'");

if (!$pending || count($pending) == 0) {
    echo "No pending orders\n";
    exit;
}

foreach ($pending as $row) {

    $order_id = $row["order_id"];
    $byteTx   = $row["byteTransactionId"]; // यही वही है जो नीचे वाले code में merchantTransactionId से मिला था

    echo "\nChecking Order ID: $order_id | ByteTxn: $byteTx\n";

    // POST DATA EXACT वही जो तुमारे JS भेज रहा है
    $postData = http_build_query([
        "byte_order_status" => $byteTx
    ]);

    // SAME ENDPOINT (आपके new code में यह order2/payment-status है)
    $url = "https://" . $_SERVER["SERVER_NAME"] . "/order2/payment-status";

    // CURL INIT
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);

    $response = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    echo "Server Response: $response | HTTP: $http\n";

    $clean = trim($response);

    if ($clean === "success") {

        runQuery("UPDATE orders SET status='SUCCESS' WHERE order_id='$order_id'");
        runQuery("UPDATE reports SET status='SUCCESS' WHERE order_id='$order_id'");

        echo "✔ SUCCESS Updated in DB\n";
    }

    else if ($clean === "FAILURE" || $clean === "FAILED" || $clean === "UPI_BACKBONE_ERROR") {

        runQuery("UPDATE orders SET status='FAILURE' WHERE order_id='$order_id'");
        runQuery("UPDATE reports SET status='FAILURE' WHERE order_id='$order_id'");

        echo "❌ FAILURE Updated in DB\n";
    }

    else {
        echo "… Pending\n";
    }
}

echo "\nCron Execution Completed\n";
?>
