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
$role = $userdata['role'];

$type = $_POST['type'] ?? '';

// Add IP (Merchant)
if ($type == 'addIP') {
    $ip = mysqli_real_escape_string($conn, $_POST['ip']);
    
    // Basic validation
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
         echo json_encode(['status' => 400, 'msg' => 'Invalid IP Format']);
         exit;
    }
    
    $check = $conn->query("SELECT * FROM merchant_ips WHERE user_token = '$usertoken' AND ip_address = '$ip'");
    if ($check->num_rows > 0) {
        echo json_encode(['status' => 409, 'msg' => 'IP already added']);
        exit;
    }

    $insert = $conn->query("INSERT INTO merchant_ips (user_token, ip_address, status) VALUES ('$usertoken', '$ip', 'Pending')");
    if ($insert) {
        echo json_encode(['status' => 200, 'msg' => 'IP submitted for approval']);
    } else {
        echo json_encode(['status' => 500, 'msg' => 'Failed to add IP']);
    }
    exit;
}

// Delete IP (Merchant)
if ($type == 'deleteIP') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $role_clause = ($role == 'Admin') ? "" : "AND user_token = '$usertoken'"; // Admin can delete any
    
    $delete = $conn->query("DELETE FROM merchant_ips WHERE id = '$id' $role_clause");
    if ($delete) {
         echo json_encode(['status' => 200, 'msg' => 'IP deleted']);
    } else {
        echo json_encode(['status' => 500, 'msg' => 'Failed to delete']);
    }
    exit;
}

// Admin Approve/Reject
if ($type == 'updateStatus') {
    if ($role != 'Admin') {
         echo json_encode(['status' => 403, 'msg' => 'Access Denied']);
         exit;
    }
    
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']); // Approved or Rejected
    
    $update = $conn->query("UPDATE merchant_ips SET status = '$status' WHERE id = '$id'");
    if ($update) {
         echo json_encode(['status' => 200, 'msg' => 'Status updated to ' . $status]);
    } else {
         echo json_encode(['status' => 500, 'msg' => 'Update failed']);
    }
    exit;
}
?>
