<?php
error_reporting(0);

// Define the base directory constant
define('ROOT_DIR', realpath(dirname(__FILE__)) . '/../');

// Securely include files using the ROOT_DIR constant
include ROOT_DIR . 'pages/dbFunctions.php';
include ROOT_DIR . 'merchant/config.php';
include ROOT_DIR . 'pages/dbInfo.php';

// ini_set('display_errors', 1);
// error_reporting(E_ALL);


$txnrefnote = $_POST['orderid']; 
$txnid = $_POST['txnid'];




$sqlSelectOrderscxr = "SELECT * FROM orders WHERE order_id=?";
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
$bytehackamount=$cxrrrowOrders['amount'];





// Check if the order has already been processed
$sqlCheckStatus = "SELECT status FROM orders WHERE order_id=?";
$stmtCheckStatus = $conn->prepare($sqlCheckStatus);
$stmtCheckStatus->bind_param("s", $order_id);
$stmtCheckStatus->execute();
$resultCheckStatus = $stmtCheckStatus->get_result();
if ($resultCheckStatus->num_rows > 0) {
    $rowCheckStatus = $resultCheckStatus->fetch_assoc();
    
    if ($rowCheckStatus['status'] === 'SUCCESS') {
        // echo 'Order already proceed';
        echo 'success';
        $stmtCheckStatus->close();
        $conn->close();
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
$orderamount = $rowOrders['amount'];
$gateway_txn = $rowOrders['gateway_txn'];
$cxrremark1 = $rowOrders['remark1'];
$mid = $rowOrders['merchant_id'];


$sqlSelectUser = "SELECT * FROM users WHERE user_token=?";
$stmtSelectUser = $conn->prepare($sqlSelectUser);
$stmtSelectUser->bind_param("s", $user_token);
$stmtSelectUser->execute();
$resultSelectUser = $stmtSelectUser->get_result();
$rowUser = $resultSelectUser->fetch_assoc();
$stmtSelectUser->close();

$callback_url = $rowUser['callback_url'];
$megabyteuserid=$rowUser['id'];
$db_amount = '';

// Fetch cookie from amazon_pay table
$sqlSelectMid = "SELECT cookie FROM amazon_pay WHERE user_token=?";
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

$found = false;

$reqdata = json_encode(["mrt_token" => $cookie ,"txnid" => $txnid, "amount" => $orderamount]);
$url = 'http://imbx.in/api/amazonpay/payment_verify.php';
  

// Initialize cURL session
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_POST, true);  // Set method to POST
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // Follow redirects
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTREDIR, CURL_REDIR_POST_ALL);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);  // Set content type to JSON
curl_setopt($ch, CURLOPT_POSTFIELDS, $reqdata);  // Send data as JSON

// Execute the cURL request
$response = curl_exec($ch);

// echo $response;
// exit;

$result = json_decode($response,true);

curl_close($curl);
            
            if($result["status"] == 'SUCCESS'){
                
            $bankReferenceId = $result["utr"];    
            $paymentMethod = $result["payment_method"];    
            $sender = $result["sender"];    
            
            $sqlInsertReport = "INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, merchantTransactionId, user_id, user_mode) VALUES ('$txnrefnote', 'SUCCESS', '$order_id', '$sender', '', '$paymentMethod', '$amount', '$user_token', '', '$txnrefnote', '$megabyteuserid','1')";
            
$stmtInsertReport = $conn->query($sqlInsertReport);

$mcq = "SELECT id FROM callback_report WHERE order_id = '$order_id'";
$ccw = mysqli_query($conn, $mcq); 

if (mysqli_num_rows($ccw) == 0) {
// Data to be sent
$postData = array(
    'order_id' => htmlspecialchars_decode($order_id),
    'status' => 'SUCCESS',
    'remark1' => $cxrremark1
);

// URL to which the request is sent
$url = $callback_url;

// Initialize cURL
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // This will not output the response
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

// Execute the POST request
curl_exec($ch);



// Close cURL session
curl_close($ch);

$bytecallbackresponse = "Successfully webhook sent";

        $call = "INSERT INTO `callback_report`(`order_id`, `request_url`, `response`, `user_token`, `mobile`, `name`) 
                 VALUES ('$order_id', '$callback_url', '$bytecallbackresponse', '$user_token', '', '')";
        $in = mysqli_query($conn, $call);
        
}

    $sql = "UPDATE orders SET status='SUCCESS',utr='$bankReferenceId' WHERE order_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $stmt->close();

            
            echo 'success';
            
            }else if($result["status"] == 'FAILED'){
            echo 'FAILED';
                
            } else {
            echo 'PENDING';
        }
   


?>
