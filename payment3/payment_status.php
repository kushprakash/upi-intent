<?php
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

$servername = DB_HOST;
$username = DB_USERNAME;
$password = DB_PASSWORD;
$dbname = DB_NAME;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $byteTransactionId = $_POST['byte_order_status'];
} else {
    header('HTTP/1.1 403 Forbidden');
    exit('Forbidden');
}

// Validate to prevent SQL injection
if (!ctype_alnum($byteTransactionId)) {
    die("Invalid order_id");
}

$sqlSelectOrderscxr = "SELECT * FROM orders WHERE byteTransactionId=?";
$stmtSelectOrderscxr = $conn->prepare($sqlSelectOrderscxr);
$stmtSelectOrderscxr->bind_param("s", $byteTransactionId);
$stmtSelectOrderscxr->execute();
$resultSelectOrders = $stmtSelectOrderscxr->get_result();
$cxrrrowOrders = $resultSelectOrders->fetch_assoc();
$stmtSelectOrderscxr->close();

if (!$cxrrrowOrders) {
    die("Byter error");
}

$order_id = $cxrrrowOrders['order_id'];
$db_amount = $cxrrrowOrders['amount']; // Order amount from DB
$bytepaytmtxnref = $cxrrrowOrders['paytm_txn_ref'];
$mid = $cxrrrowOrders['merchant_id'];
$db_merchantTransactionId = $bytepaytmtxnref;

// Check if the order is already successful
$sqlCheckStatus = "SELECT status FROM orders WHERE order_id=?";
$stmtCheckStatus = $conn->prepare($sqlCheckStatus);
$stmtCheckStatus->bind_param("s", $order_id);
$stmtCheckStatus->execute();
$resultCheckStatus = $stmtCheckStatus->get_result();

if ($resultCheckStatus->num_rows > 0) {
    $rowCheckStatus = $resultCheckStatus->fetch_assoc();
    if ($rowCheckStatus['status'] === 'SUCCESS') {
        echo 'success';
        $stmtCheckStatus->close();
        $conn->close();
        exit;
    }
}
$stmtCheckStatus->close();

// Delete old empty status reports if any
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

$res_pmode = $conn->query("SELECT * FROM users where user_token='$user_token'")->fetch_assoc();

if ($res_pmode["pg_mode"] == 2) {
    $adminarraydata = $conn->query("SELECT * FROM users where id=157 AND role = 'Admin'")->fetch_assoc();
    $usermodetoken = $adminarraydata["user_token"];
    $rmode = 2;
} else {
    $rmode = 1;
    $usermodetoken = $user_token;
}

// Fetch MID from paytm_tokens
$sqlSelectMid = "SELECT MID FROM paytm_tokens WHERE user_token=? AND id=?";
$stmtSelectMid = $conn->prepare($sqlSelectMid);
$stmtSelectMid->bind_param("ss", $usermodetoken, $mid);
$stmtSelectMid->execute();
$resultSelectMid = $stmtSelectMid->get_result();
$rowMid = $resultSelectMid->fetch_assoc();
$stmtSelectMid->close();

if ($rowMid) {
    $bytemerchantid = $rowMid['MID'];
} else {
    die("MID not found for user_token: $user_token");
}

$mid = $bytemerchantid;
$txn_ref_id = $bytepaytmtxnref;

// Prepare JSON for Paytm status API
$JsonData = json_encode(array("MID" => $mid, "ORDERID" => $txn_ref_id));

// cURL call to Paytm
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://securegw.paytm.in/order/status?JsonData=" . urlencode($JsonData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'cURL Error: ' . curl_error($ch);
}

curl_close($ch);

$responseArray = json_decode($response, true);


$newResponse = json_encode($responseArray);
$ref         = $txn_ref_id;

$sql = "UPDATE logs SET response = ? WHERE ref = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $newResponse, $ref);
$stmt->execute();
$stmt->close();


if ($responseArray !== null) {

    // Validate status AND MID AND ORDERID AND AMOUNT MATCH
    if (
        $responseArray['STATUS'] == "TXN_SUCCESS" &&
        $responseArray['MID'] == $mid &&
        $responseArray['ORDERID'] == $txn_ref_id &&
        floatval($responseArray['TXNAMOUNT']) == floatval($db_amount)
    ) {

        $transactionId = $responseArray['TXNID'];
        $paymentState = $responseArray['STATUS'];
        $amount = $responseArray['TXNAMOUNT'];
        $vpa = "Not Found";
        $user_name = "NULL";
        $paymentApp = $responseArray['GATEWAYNAME'];
        $transactionNote = $responseArray['MERC_UNQ_REF'];
        $cxrmerchantTransactionId = $responseArray['ORDERID'];
        $UTR = $responseArray['BANKTXNID'];

        $sqlInsertReport = "INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, merchantTransactionId, user_id, user_mode) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsertReport = $conn->prepare($sqlInsertReport);
        $stmtInsertReport->bind_param("ssssssssssss", $transactionId, $paymentState, $order_id, $vpa, $user_name, $paymentApp, $amount, $user_token, $transactionNote, $cxrmerchantTransactionId, $megabyteuserid, $rmode);

        if ($stmtInsertReport->execute() === TRUE) {
            $stmtInsertReport->close();
        }
    } else {
        // Status not success or amount mismatch
        // आप चाहे तो यहां कोई अलग मैसेज एको करवा सकते हैं जैसे pending या error
    }

} else {
    echo "Failed to decode JSON response";
}

// Fetch report status again
$sqlSelectReports = "SELECT * FROM reports WHERE order_id=?";
$stmtSelectReports = $conn->prepare($sqlSelectReports);
$stmtSelectReports->bind_param("s", $order_id);
$stmtSelectReports->execute();
$resultSelectReports = $stmtSelectReports->get_result();
$rowReports = $resultSelectReports->fetch_assoc();
$stmtSelectReports->close();

$db_status = $rowReports['status'];
$db_transactionNote = $rowReports['transactionNote'];
$db_transactionId = $rowReports['transactionId'];

// Check if payment is success and merchant transaction ids match
if ($db_status == 'TXN_SUCCESS' && $cxrmerchantTransactionId == $db_merchantTransactionId) {

    $mcq = "SELECT id FROM callback_report WHERE order_id = '$order_id'";
    $ccw = mysqli_query($conn, $mcq);

    if (mysqli_num_rows($ccw) == 0) {

        $postData = array(
            'status' => 'SUCCESS',
            'order_id' => $order_id,
            'message' => 'Transaction Successfully',
            'result' => array(
                "txnStatus" => "COMPLETED",
                "resultInfo" => "Transaction Success",
                "orderId" => $order_id,
                'amount' => $amount,
                'date' => $rowOrders['create_date'],
                'utr' => $UTR,
                'customer_mobile' => $rowOrders['customer_mobile'],
                'remark1' => $cxrremark1,
                'remark2' => $rowOrders["remark2"]
            )
        );

        $url = $callback_url;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_exec($ch);
        curl_close($ch);

        $bytecallbackresponse = "Successfully webhook sent";

        $call = "INSERT INTO `callback_report`(`order_id`, `request_url`, `response`, `user_token`, `mobile`, `name`) 
                 VALUES ('$order_id', '$callback_url', '$bytecallbackresponse', '$user_token', '', '')";
        $in = mysqli_query($conn, $call);
    }

    $sql = "UPDATE orders SET status='SUCCESS' WHERE order_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $stmt->close();

    $sql = "UPDATE reports SET status='TXN_SUCCESS' WHERE order_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $stmt->close();

    $sql = "UPDATE orders SET utr=? WHERE order_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $UTR, $order_id);
    $stmt->execute();
    $stmt->close();

    echo 'success';
} else {
    echo 'PENDING';
}

if ($db_status == 'FAILURE' || $db_status == 'FAILED' || $db_status == 'UPI_BACKBONE_ERROR') {
    echo 'FAILURE';
}

$conn->close();
?>
