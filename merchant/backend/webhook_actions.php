<?php
session_start();
include "../config.php";

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 403, 'msg' => 'Unauthorized']);
    exit();
}

$mobile = $_SESSION['username'];
$user_res = $conn->query("SELECT * FROM users WHERE mobile = '$mobile'");
$userdata = $user_res->fetch_assoc();
$usertoken = $userdata['user_token'];

$type = $_POST['type'] ?? '';

// Add Webhook
if ($type == 'addWebhook') {
    $url = mysqli_real_escape_string($conn, $_POST['url']);
    
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
         echo json_encode(['status' => 400, 'msg' => 'Invalid URL']);
         exit;
    }
    
    // Check dupe
    $check = $conn->query("SELECT * FROM merchant_webhooks WHERE user_token = '$usertoken' AND webhook_url = '$url'");
    if ($check->num_rows > 0) {
        echo json_encode(['status' => 409, 'msg' => 'Webhook URL already exists']);
        exit;
    }

    $insert = $conn->query("INSERT INTO merchant_webhooks (user_token, webhook_url) VALUES ('$usertoken', '$url')");
    if ($insert) {
        echo json_encode(['status' => 200, 'msg' => 'Webhook added successfully']);
    } else {
        echo json_encode(['status' => 500, 'msg' => 'Failed to add webhook']);
    }
    exit;
}

// Delete Webhook
if ($type == 'deleteWebhook') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    $delete = $conn->query("DELETE FROM merchant_webhooks WHERE id = '$id' AND user_token = '$usertoken'");
    if ($delete) {
         echo json_encode(['status' => 200, 'msg' => 'Webhook deleted']);
    } else {
        echo json_encode(['status' => 500, 'msg' => 'Failed to delete']);
    }
    exit;
}
?>
