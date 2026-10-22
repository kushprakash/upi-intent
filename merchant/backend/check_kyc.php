<?php
// kyc_update_and_redirect.php
session_start();

// 1) Must have logged-in user (username = mobile)
if (empty($_SESSION['username'])) {
    header('Location: '.$site_url.'/merchant/dashboard');
    exit;
}
$mobile = $_SESSION['username'];

// Include config file with DB and API credentials
require_once 'imb-config.php';
require_once '../config.php';

// 2) DB connect using config variables
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
if ($mysqli->connect_errno) {
    // Fail closed -> just redirect
    // Fail closed -> just redirect
    header('Location: '.$site_url.'/merchant/dashboard');
    exit;
}
$mysqli->set_charset('utf8mb4');

// 3) Fetch user by mobile
$table = 'users'; // change to 'user' if your table is singular
$stmt = $mysqli->prepare("SELECT id, mobile, kyc_refid FROM `$table` WHERE mobile = ? LIMIT 1");
$stmt->bind_param('s', $mobile);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if (!$user || empty($user['kyc_refid'])) {
    // no user or no kyc_refid -> redirect
    $mysqli->close();
    $mysqli->close();
    header('Location: '.$site_url.'/merchant/dashboard');
    exit;
}

$kycRefId = $user['kyc_refid'];

// 4) Call API: downloadAadhaar with configured client id and secret
function downloadAadhaarReturnArray($requestId, $client_id, $client_secret) {
    $url = "https://secure.imbpayment.in/api/v1/digilocker/downloadAadhaar/" . rawurlencode($requestId);

    $headers = [
        "Content-Type: application/json",
        "x-client-id: $client_id",
        "x-client-secret: $client_secret"
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 30
    ]);

    $responseBody = curl_exec($ch);
    $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErrNo    = curl_errno($ch);
    $curlErr      = curl_error($ch);
    curl_close($ch);

    if ($curlErrNo) {
        return [
            'status' => 'error',
            'http_code' => $httpCode,
            'message' => $curlErr,
            'raw' => null
        ];
    }

    $decoded = json_decode($responseBody, true);
    return [
        'status' => ($httpCode === 200 ? 'success' : 'failed'),
        'http_code' => $httpCode,
        'raw' => $decoded ?? $responseBody
    ];
}

$apiResult = downloadAadhaarReturnArray($kycRefId, $client_id, $client_secret);

// 5) If success -> update aadhar_kyc=1 and store full response JSON in kyc_response
if (isset($apiResult['status']) && $apiResult['status'] === 'success') {
    // store entire response safely as JSON string
    $jsonToStore = json_encode($apiResult['raw'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $upd = $mysqli->prepare("UPDATE `$table` SET aadhar_kyc = 1, kyc_response = ? WHERE mobile = ? LIMIT 1");
    $upd->bind_param('ss', $jsonToStore, $mobile);
    $upd->execute();
    $upd->close();
}

// 6) Clean up and redirect
$mysqli->close();
$mysqli->close();
header('Location: '.$site_url.'/merchant/dashboard');
exit;
