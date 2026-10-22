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

$txnrefnote = $_POST['PAYID'];  
$amount = $_POST['amount'];

$sqlSelectOrderscxr = "SELECT * FROM orders WHERE paytm_txn_ref=?";
$stmtSelectOrderscxr = $conn->prepare($sqlSelectOrderscxr);
$stmtSelectOrderscxr->bind_param("s", $txnrefnote);
$stmtSelectOrderscxr->execute();
$resultSelectOrders = $stmtSelectOrderscxr->get_result();
$cxrrrowOrders = $resultSelectOrders->fetch_assoc();
$stmtSelectOrderscxr->close();

if (!$cxrrrowOrders) {
    die("Byter error");
}

$order_id = $cxrrrowOrders['order_id'];

$sqlCheckStatus = "SELECT status FROM orders WHERE order_id=?";
$stmtCheckStatus = $conn->prepare($sqlCheckStatus);
$stmtCheckStatus->bind_param("s", $order_id);
$stmtCheckStatus->execute();
$resultCheckStatus = $stmtCheckStatus->get_result();

if ($resultCheckStatus->num_rows > 0) {
    $rowCheckStatus = $resultCheckStatus->fetch_assoc();
    if ($rowCheckStatus['status'] === 'SUCCESS') {
        echo 'success';
        exit;
    }
}
$stmtCheckStatus->close();

$sqlDelete = "DELETE FROM reports WHERE status='' AND order_id=?";
$stmtDelete = $conn->prepare($sqlDelete);
$stmtDelete->bind_param("s", $order_id);
$stmtDelete->execute();
$stmtDelete->close();

$sqlSelectOrders = "SELECT * FROM orders WHERE order_id=?";
$stmtSelectOrders = $conn->prepare($sqlSelectOrders);
$stmtSelectOrders->bind_param("s", $order_id);
$stmtSelectOrders->execute();
$resultSelectOrders = $stmtSelectOrders->get_result();
$rowOrders = $resultSelectOrders->fetch_assoc();
$stmtSelectOrders->close();

if (!$rowOrders) {
    die("Order not found");
}

$user_token = $rowOrders['user_token'];
$cxrbytectxnref = $rowOrders['paytm_txn_ref'];
$cxrremark1 = $rowOrders['remark1'];

$sqlSelectUser = "SELECT * FROM users WHERE user_token=?";
$stmtSelectUser = $conn->prepare($sqlSelectUser);
$stmtSelectUser->bind_param("s", $user_token);
$stmtSelectUser->execute();
$resultSelectUser = $stmtSelectUser->get_result();
$rowUser = $resultSelectUser->fetch_assoc();
$stmtSelectUser->close();

$callback_url = $rowUser['callback_url'];
$megabyteuserid = $rowUser['id'];

$sqlSelectMid = "SELECT cookie FROM freecharge WHERE user_token=?";
$stmtSelectMid = $conn->prepare($sqlSelectMid);
$stmtSelectMid->bind_param("s", $user_token);
$stmtSelectMid->execute();
$resultSelectMid = $stmtSelectMid->get_result();
$rowMid = $resultSelectMid->fetch_assoc();
$stmtSelectMid->close();

if ($rowMid) {
    $cookie = $rowMid['cookie'];
} else {
    die("cookie not found for user_token: $user_token");
}

//
//  ✅ NEW OFFICIAL API (Hostinger)
//
$url = "https://srv1070916.hstgr.cloud/freecharge/check_txn/";

// form-data payload
$postFields = http_build_query([
    'appfc' => $cookie,
    'orderid' => $cxrbytectxnref
]);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "content-type: application/x-www-form-urlencoded"
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    die("cURL error: " . curl_error($ch));
}

curl_close($ch);

// echo $response; exit;

$response_data = json_decode($response, true);

if (!is_array($response_data)) {
    echo "PENDING";
    exit;
}

// SUCCESS CONDITION
if (isset($response_data['status']) && $response_data['status'] === "SUCCESS") {

    $utr = $response_data['data']['UTR'] ?? '';

    // Insert Report
    $sqlInsertReport = "INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, merchantTransactionId, user_id, user_mode) 
                        VALUES ('$txnrefnote', 'SUCCESS', '$order_id', 'test@ibl', '', '', '$amount', '$user_token', '', '$cxrremark1', '$megabyteuserid','1')";
    $conn->query($sqlInsertReport);

    // Callback send
    $postData = [
        'status' => 'SUCCESS',
        'order_id' => $order_id,
        'message' => 'Transaction Successfully',
        'result' => [
            "txnStatus" => "COMPLETED",
            "resultInfo" => "Transaction Success",
            "orderId" => $order_id,
            'amount' => $amount,
            'date' => $rowOrders['create_date'],
            'utr' => $utr,
            'customer_mobile' => $rowOrders['customer_mobile'],
            'remark1' => $cxrremark1,
            'remark2' => $rowOrders["remark2"]
        ]
    ];

    $ch = curl_init($callback_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    // Update status
    $update_query = "UPDATE orders SET status='SUCCESS', utr=? WHERE order_id=? AND user_id=?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sss", $utr, $order_id, $megabyteuserid);
    $stmt->execute();

    echo "success";
    exit;
}

echo "PENDING";
?>
