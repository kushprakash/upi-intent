<?php
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";

$servername = DB_HOST;
$username = DB_USERNAME;
$password = DB_PASSWORD;
$dbname = DB_NAME;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $merchantRequestId = $_POST['txnid']; // 'txn_id' key जरूरी!
} else {
    header('HTTP/1.1 403 Forbidden');
    exit('Forbidden');
}

if (!ctype_alnum($merchantRequestId)) {
    die("Invalid txn_id");
}

// Order fetch by merchantRequestId
$sqlSelectOrders = "SELECT * FROM orders WHERE paytm_txn_ref=?";
$stmtSelectOrders = $conn->prepare($sqlSelectOrders);
$stmtSelectOrders->bind_param("s", $merchantRequestId);
$stmtSelectOrders->execute();
$resultSelectOrders = $stmtSelectOrders->get_result();
$orderRow = $resultSelectOrders->fetch_assoc();
$stmtSelectOrders->close();

if (!$orderRow) {
    die("PENDING");
}

$order_id = $orderRow['order_id'];
$db_amount = $orderRow['amount'];
$user_token = $orderRow['user_token'];
$remark1 = $orderRow['remark1'];
$db_merchantRequestId = $orderRow['paytm_txn_ref'];
$mid = $orderRow['merchant_id'];

// Googlepay AccessToken तथा user_id प्राप्त करें
$sqlUser = "SELECT * FROM users WHERE user_token=?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $user_token);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$userRow = $resultUser->fetch_assoc();
$stmtUser->close();

if (!$userRow) {
    die("User not connected to Googlepay");
}
$user_id = $userRow['id'];
$callback_url = $userRow['callback_url'];

// Quintus tokens table से accessToken प्राप्त करें
$sqlToken = "SELECT * FROM gpay_tokens WHERE user_token=? AND id=?";
$stmtToken = $conn->prepare($sqlToken);
$stmtToken->bind_param("si", $user_token, $mid);
$stmtToken->execute();
$resultToken = $stmtToken->get_result();
$tokenRow = $resultToken->fetch_assoc();
$stmtToken->close();

if (!$tokenRow || empty($tokenRow['cokkie'])) {
    die("Cookie not found");
}

$AT = $tokenRow['at'];
$FREQ_RAW = $tokenRow['f-req'];

$FSID = '-1165716089292329246';
$REQID = '962571';
$BL = 'boq_payments-merchant-console-ui_20250803.08_p0';
$HL = 'en-GB';
$SOURCE_PATH = '/g4b/transactions/BCR2DN4T5HYOZ4AP';
$ORIGIN = 'https://pay.google.com';

$COOKIE = $tokenRow['cokkie'];

// yaha per order id paas karna hai 
$FILTER_REMARKS = $merchantRequestId;

$base = 'https://pay.google.com/g4b/_/SMBConsoleUI/data/batchexecute';
$q = http_build_query([
  'rpcids'       => 'RPtkab',
  'source-path'  => $SOURCE_PATH,
  'f.sid'        => $FSID,
  'bl'           => $BL,
  'hl'           => $HL,
  'soc-app'      => '1',
  'soc-platform' => '1',
  'soc-device'   => '2',
  '_reqid'       => $REQID,
  'rt'           => 'c',
], '', '&', PHP_QUERY_RFC3986);
$url = $base.'?'.$q;

function cookie_value(string $blob, string $name): ?string {
  foreach (explode(';', $blob) as $p) { 
    $kv = explode('=', trim($p), 2); 
    if (count($kv)==2 && $kv[0]===$name) return $kv[1]; 
  }
  return null;
}
function sapisidhash(string $cookie, string $origin): ?string {
  $sapisid = cookie_value($cookie, 'SAPISID') ?: cookie_value($cookie, '__Secure-3PAPISID');
  if (!$sapisid) return null;
  $ts = (string) time();
  return 'SAPISIDHASH '.$ts.'_'.sha1($ts.' '.$sapisid.' '.$origin);
}

$headers = [
  'accept: */*',
  'content-type: application/x-www-form-urlencoded;charset=UTF-8',
  'origin: '.$ORIGIN,
  'referer: '.$ORIGIN.'/',
  'accept-encoding: gzip, deflate, br, zstd',
  'accept-language: en-US,en;q=0.9,hi;q=0.8',
  'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Mobile Safari/537.36',
  'sec-fetch-dest: empty',
  'sec-fetch-mode: cors',
  'sec-fetch-site: same-origin',
  'x-browser-channel: stable',
  'x-browser-year: 2025',
  'x-same-domain: 1',
  'x-client-data: CIu2yQEIprbJAQipncoBCLbgygEIk6HLAQiko8sBCIWgzQEI/qXOAQjrgM8BCPaDzwEIgYTPAQiVhM8BCKCFzwEY4eLOARjS/s4B',
  "cookie: $COOKIE",
];
if ($auth = sapisidhash($COOKIE, $ORIGIN)) {
  $headers[] = 'authorization: '.$auth;
  $headers[] = 'x-origin: '.$ORIGIN;
}


$useRaw = (strncmp($FREQ_RAW, '%5B', 3) === 0 || strncmp($FREQ_RAW, '%5b', 3) === 0);
if ($useRaw) {
  $postBody = 'f.req='.$FREQ_RAW.'&at='.rawurlencode($AT);
} else {
  $postBody = http_build_query(['f.req' => $FREQ_RAW, 'at' => $AT], '', '&', PHP_QUERY_RFC3986);
}

$ch = curl_init();
curl_setopt_array($ch, [
  CURLOPT_URL            => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST           => true,
  CURLOPT_HTTPHEADER     => $headers,
  CURLOPT_POSTFIELDS     => $postBody,
  CURLOPT_ENCODING       => '',
  CURLOPT_TIMEOUT        => 30,
]);
$raw  = curl_exec($ch);
$err  = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($err) { header('Content-Type:text/plain'); echo "PENDING"; exit; }

function parse_chunks(string $resp): array {
  if (strpos($resp, ")]}'") === 0) $resp = substr($resp, 4);
  $resp = ltrim($resp, "\r\n");
  $lines = preg_split("/\r\n|\n|\r/", $resp);
  $out = [];
  for ($i=0; $i<count($lines); ) {
    while ($i<count($lines) && trim($lines[$i])==='') $i++;
    if ($i>=count($lines)) break;
    $len = trim($lines[$i++]);
    while ($i<count($lines) && trim($lines[$i])==='') $i++;
    if ($i>=count($lines)) break;
    $json = $lines[$i++];

    $arr = json_decode($json, true);
    $rpc = $arr[0][1] ?? null;
    $payload = $arr[0][2] ?? null;

    for ($k=0; $k<3; $k++) {
      if (is_string($payload)) { 
        $tmp = json_decode($payload, true); 
        if ($tmp !== null) $payload = $tmp; else break; 
      }
    }
    $out[] = ['rpc'=>$rpc, 'payload'=>$payload, 'raw'=>$json, 'len'=>ctype_digit($len)?(int)$len:null];
  }
  return $out;
}

if ($code !== 200) {
  echo "PENDING";
    $conn->close();
    exit;
}

$chunks = parse_chunks((string)$raw);
$payload = null;
foreach ($chunks as $c) if (($c['rpc'] ?? null) === 'RPtkab') { $payload = $c['payload']; break; }

if ($payload === null) {
  echo "PENDING";
    $conn->close();
    exit;
}

function map_txns(array $payload): array {
  if (!isset($payload[0]) || !is_array($payload[0])) return [];
  $txns = $payload[0]; $out = [];
  foreach ($txns as $t) {
    if (!is_array($t)) continue;
    $sec = $t[2][0] ?? null;
    $out[] = [
      'txn_id'     => $t[0] ?? '',
      'order_id'   => $t[1] ?? '',
      'time'       => $sec ? date('Y-m-d H:i:s', (int)$sec) : '',
      'amount'     => $t[3][1] ?? null,
      'currency'   => $t[3][0] ?? '',
      'payer_name' => $t[8][0] ?? '',
      'payer_upi'  => $t[8][1] ?? '',
      'remarks'    => $t[9] ?? '',
      'status'     => (isset($t[10]) && (int)$t[10] === 5) ? 'SUCCESS' : 'PENDING/FAILED',
    ];
  }
  return $out;
}

$txnList = map_txns($payload);

// 🔎 Filter सिर्फ वही txn दिखेगा जिसका remarks match करे
if ($FILTER_REMARKS) {
  $txnList = array_values(array_filter($txnList, function($t) use ($FILTER_REMARKS) {
    return isset($t['remarks']) && $t['remarks'] === $FILTER_REMARKS;
  }));
}
$resArr = $txnList[0];

if (!is_array($resArr) || !isset($resArr['remarks'])) {
    echo "PENDING";
    $conn->close();
    exit;
}

if ($resArr['remarks'] !== $merchantRequestId) {
    echo "PENDING";
    $conn->close();
    exit;
}

// Amount MATCH check
if (floatval($resArr['amount']) != floatval($db_amount)) {
    echo "FAILED";
    $conn->close();
    exit;
}

// SUCCESS/FAIL/PENDING logic
if ($resArr['status'] === "SUCCESS") {
    $transactionId = $resArr['txn_id'];
    $amount = $resArr['amount'];
    $vpa = $resArr['payer_upi'];
    $user_name = $resArr['payer_name'];
    $paymentApp = $resArr['payer_name'];
    $transactionNote = $resArr['time'];
    $UTR = $transactionId;

    // Report insert/update
    $sqlInsertReport = "INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, merchantTransactionId, user_id, user_mode)
     VALUES (?, 'TXN_SUCCESS', ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
     ON DUPLICATE KEY UPDATE status='TXN_SUCCESS', transactionId=?, amount=?, vpa=?, user_name=?, paymentApp=?, transactionNote=?";
    $stmtInsertReport = $conn->prepare($sqlInsertReport);
    $stmtInsertReport->bind_param("sssssssssssss", $transactionId, $order_id, $vpa, $user_name, $paymentApp, $amount, $user_token, $transactionNote, $merchantRequestId, $user_id, $transactionId, $amount, $vpa, $user_name, $paymentApp, $transactionNote);
    $stmtInsertReport->execute();
    $stmtInsertReport->close();

    // CALLBACK (one time)
    $mcq = "SELECT id FROM callback_report WHERE order_id = ?";
    $stmtMcq = $conn->prepare($mcq);
    $stmtMcq->bind_param("s", $order_id);
    $stmtMcq->execute();
    $resultMcq = $stmtMcq->get_result();
    $rowMcq = $resultMcq->fetch_assoc();
    $stmtMcq->close();

    if (!$rowMcq) {
        $postData = array(
            'status' => 'SUCCESS',
            'order_id' => $order_id,
            'message' => 'Transaction Successfully',
            'result' => array(
                "txnStatus" => "COMPLETED",
                "resultInfo" => "Transaction Success",
                "orderId" => $order_id,
                'amount' => $amount,
                'date' => $orderRow['create_date'],
                'utr' => $UTR,
                'customer_mobile' => $orderRow['customer_mobile'],
                'remark1' => $remark1,
                'remark2' => $orderRow["remark2"]
            )
        );

        $ch = curl_init($callback_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_exec($ch);
        curl_close($ch);

        $bytecallbackresponse = "Successfully webhook sent";

        $call = "INSERT INTO callback_report(order_id, request_url, response, user_token, mobile, name)
            VALUES (?, ?, ?, ?, '', '')";
        $stmtCall = $conn->prepare($call);
        $stmtCall->bind_param("ssss", $order_id, $callback_url, $bytecallbackresponse, $user_token);
        $stmtCall->execute();
        $stmtCall->close();
    }

    // UPDATE orders, reports, UTR
    $conn->query("UPDATE orders SET status='SUCCESS', utr='$UTR' WHERE order_id='$order_id'");
    $conn->query("UPDATE reports SET status='TXN_SUCCESS' WHERE order_id='$order_id'");

    echo 'success';
} else {
    echo 'PENDING';
}
$conn->close();
?>
