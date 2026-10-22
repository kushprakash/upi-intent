<?php
session_start();
ob_start(); // So that stray output doesn't block headers

require_once 'imb-config.php';
include "../config.php"; 
include("db_modal.php");

// Use client credentials from config
$IMB_CLIENT_ID     = $client_id;
$IMB_CLIENT_SECRET = $client_secret;
$DIGILOCKER_URL    = 'https://secure.imbpayment.in/api/v1/digilocker/initiate';

function out_json($code, $ok, $msg, $extra = []) {
    http_response_code($code);
    echo json_encode(array_merge(['res_code'=>$code,'status'=>$ok,'msg'=>$msg], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

// ---- USER CONTEXT ----
$mobile = '';
if (!empty($_SESSION['username'])) {
    $mobile = $_SESSION['username'];
} elseif (!empty($_SESSION['appuser'])) {
    $mobile = $_SESSION['appuser'];
}

$demo = isset($_GET['demo']) && $_GET['demo'] == '1';

if (!$demo) {
    if ($mobile === '') out_json(401, false, 'Not authenticated.');
    $userList = $crud->read("users","mobile = '$mobile'");
    if (!$userList || !isset($userList[0])) out_json(404, false, 'User not found.');
    $user   = $userList[0];
    $name   = (!empty($user['name'])  ? $user['name']  : 'User');
    $email  = (!empty($user['email']) ? $user['email'] : 'test@gmail.com');
    $msisdn = preg_replace('/\D/', '', $mobile);
} else {
    $name   = isset($_GET['name'])   ? trim($_GET['name'])   : 'imb Pay';
    $email  = isset($_GET['email'])  ? trim($_GET['email'])  : 'info@pay.garudhub.in';
    $msisdn = isset($_GET['mobile']) ? preg_replace('/\D/', '', $_GET['mobile']) : '9999999999';
    if ($msisdn === '') $msisdn = '9999999999';
}

$payload = [
    "customer_details" => [
        "full_name"      => "imb Pay",
        "mobile_number"  => "9876543210",
        "user_email"     => "info@pay.garudhub.in"
    ],
    "expiry_minutes" => 10,
    "return_url"     => $site_url."/merchant/backend/check_kyc",
    "state"          => "test"
];

// If API requires user consent parameter, uncomment this line (check IMB docs!)
// $payload["consent"] = "Y";

// Debug: Log outgoing payload (for troubleshooting)
file_put_contents('/tmp/imb_out_payload.json', json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// ---- CURL CALL ----
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $DIGILOCKER_URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 5,
    CURLOPT_TIMEOUT        => 20,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST  => 'POST',
    CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        'Content-Type: application/json',
        'x-client-id: ' . $IMB_CLIENT_ID,
        'x-client-secret: ' . $IMB_CLIENT_SECRET,
    ],
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);
$response = curl_exec($ch);
$curlErr  = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Debug: Log raw response
file_put_contents('/tmp/imb_in_response.json', $response);

// Handle errors
if ($curlErr) out_json(502, false, 'Transport error: '.$curlErr);

$json = json_decode($response, true);
if (!is_array($json)) out_json(502, false, 'Invalid JSON from Digilocker API (HTTP '.$httpCode.')', ['raw'=>$response]);

$ok         = ($json['status'] ?? '') === 'success';
$data       = $json['data'] ?? [];
$request_id = $data['request_id'] ?? '';
$redirect   = $data['digilocker_url'] ?? '';
$message    = $json['message'] ?? 'Failed';

// Provide full API payload info in 400/failure for better debugging
if (!$ok || $request_id === '' || $redirect === '') {
    out_json(400, false, $message, [
        'api_http' => $httpCode,
        'api'      => $json,
        'payload'  => $payload // full info for debugging
    ]);
}

// DB me request_id save
if (!$demo) {
    $saved = $crud->update("users", ["kyc_refid" => $request_id], "mobile = '$mobile'");
    if (!$saved) out_json(502, false, 'Failed to store request_id to user.', ['request_id'=>$request_id]);
}

// AJAX? JSON output, else 302 redirect
$isAjax = (
    (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    || (isset($_POST['ajax']) && $_POST['ajax'] == '1')
    || (isset($_GET['ajax']) && $_GET['ajax'] == '1')
    || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
);

if ($isAjax) {
    out_json(200, true, 'redirect', [
        'redirect'    => $redirect,
        'request_id'  => $request_id
    ]);
}
if (ob_get_length()) { @ob_end_clean(); }
if (!headers_sent()) {
    header('Cache-Control: no-store');
    header('Location: '.$redirect, true, 302);
    exit;
}

// Last resort JS redirect
echo "<script>window.top.location.href=".json_encode($redirect).";</script>";
exit;
?>
