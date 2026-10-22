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

// Add Domain (Merchant)
if ($type == 'addDomain') {
    $domain = mysqli_real_escape_string($conn, $_POST['domain']);
    
    // Basic validation
    if (!filter_var($domain, FILTER_VALIDATE_URL) && !preg_match("/^[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.[a-zA-Z]{2,}$/", $domain)) {
         echo json_encode(['status' => 400, 'msg' => 'Invalid Domain Format. Use format like example.com or https://example.com']);
         exit;
    }
    
    // Normalize domain (remove protocol for storage consistency if desired, otherwise keep as is)
    // Here we store exactly what user provides but ensuring it has no trailing slash might be good
    $domain = rtrim($domain, '/');

    // Check limit? (Optional)
    
    $check = $conn->query("SELECT * FROM merchant_domains WHERE user_token = '$usertoken' AND domain_url = '$domain'");
    if ($check->num_rows > 0) {
        echo json_encode(['status' => 409, 'msg' => 'Domain already added']);
        exit;
    }

    $insert = $conn->query("INSERT INTO merchant_domains (user_token, domain_url, status) VALUES ('$usertoken', '$domain', 'Pending')");
    if ($insert) {
        echo json_encode(['status' => 200, 'msg' => 'Domain submitted for approval']);
    } else {
        echo json_encode(['status' => 500, 'msg' => 'Failed to add domain']);
    }
    exit;
}

// Delete Domain (Merchant)
if ($type == 'deleteDomain') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $role_clause = ($role == 'Admin') ? "" : "AND user_token = '$usertoken'"; // Admin can delete any
    
    $delete = $conn->query("DELETE FROM merchant_domains WHERE id = '$id' $role_clause");
    if ($delete) {
         echo json_encode(['status' => 200, 'msg' => 'Domain deleted']);
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
    
    $update = $conn->query("UPDATE merchant_domains SET status = '$status' WHERE id = '$id'");
    if ($update) {
         echo json_encode(['status' => 200, 'msg' => 'Status updated to ' . $status]);
    } else {
         echo json_encode(['status' => 500, 'msg' => 'Update failed']);
    }
    exit;
}
?>
