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

// Quintus AccessToken तथा user_id प्राप्त करें
$sqlUser = "SELECT * FROM users WHERE user_token=?";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->bind_param("s", $user_token);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$userRow = $resultUser->fetch_assoc();
$stmtUser->close();

if (!$userRow) {
    die("User not connected to QuintusPay");
}
$user_id = $userRow['id'];
$callback_url = $userRow['callback_url'];

// Quintus tokens table से accessToken प्राप्त करें
$sqlToken = "SELECT * FROM quintus_tokens WHERE user_token=? AND id=?";
$stmtToken = $conn->prepare($sqlToken);
$stmtToken->bind_param("si", $user_token, $mid);
$stmtToken->execute();
$resultToken = $stmtToken->get_result();
$tokenRow = $resultToken->fetch_assoc();
$stmtToken->close();

if (!$tokenRow || empty($tokenRow['accessToken'])) {
    die("AccessToken not found");
}
$accessToken = $tokenRow['accessToken'];

// QuintusTxn API call
$api = "https://miniapi.shop/api/quintustech/txn.php?token=".urlencode($accessToken)."&orderId=".urlencode($merchantRequestId);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
    $conn->close();
    exit;
}
curl_close($ch);

$resArr = json_decode($response, true);

if (!is_array($resArr) || !isset($resArr['merchantRequestId'])) {
    echo "PENDING";
    $conn->close();
    exit;
}

if ($resArr['merchantRequestId'] !== $merchantRequestId) {
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
    $transactionId = $resArr['referenceNo'];
    $amount = $resArr['amount'];
    $vpa = $resArr['payerVPA'];
    $user_name = $resArr['payerName'];
    $paymentApp = $resArr['payeeVPA'];
    $transactionNote = $resArr['transactionTimestamp'];
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
