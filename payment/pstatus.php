<?php
error_reporting(0);
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";
include "../merchant/config.php";

// ini_set('display_errors', 1);
// error_reporting(E_ALL);



$order_id = $_GET['order_id'];





$slq_p = "SELECT * FROM orders where order_id='$order_id'";
        $res_p = getXbyY($slq_p);    
        $user_token = $res_p[0]['user_token'];
        $db_description = $res_p[0]['description'];
        $hdfc_txn = $res_p[0]['HDFC_TXNID'];
        $db_amount = $res_p[0]['amount'];
        $db_byte_status=$res_p[0]['status'];
        $bbbyteremark1=$res_p[0]['remark1'];
        
        if ($db_byte_status=="SUCCESS"){
            echo "Order already processed";
            exit;
        }
        
$slq_pmode = "SELECT * FROM users where user_token='$user_token'";
        $res_pmode = getXbyY($slq_pmode);    
        
    if($res_pmode[0]["pg_mode"] == 2){
        $slq_admingetdata = "SELECT * FROM users where id=157 AND role = 'Admin'";
        $adminarraydata =  getXbyY($slq_admingetdata);
         $usermodetoken = $adminarraydata[0]["user_token"];
         $rmode = 2;
    }else{
         $rmode = 1;
        $usermodetoken = $user_token;
    }    
        
    $slq_p = "SELECT * FROM hdfc where user_token='$usermodetoken'";
        $res_p = getXbyY($slq_p);    
        $seassion_id_hdfc = $res_p[0]['seassion'];
        $hdfc_number = $res_p[0]['number'];        
 
 $slq_pc = "SELECT * FROM users where user_token='$user_token'";
        $res_pc = getXbyY($slq_pc);    
        $callback_url = $res_pc[0]['callback_url'];
        $mobile = $res_pc[0]['mobile'];
        $name = $res_pc[0]['name'];
        $megabyteuserid =$res_pc[0]['id'];
         
        
$txn_data = file_get_contents('https://'.$server.'/payment/mstatement.php?no='.$hdfc_number.'&session='.$seassion_id_hdfc.'');  

echo $txn_data;
exit;

$json0=json_decode($txn_data,true);
$results=$json0["transactionParams"];

$query0 = "SELECT * FROM reports WHERE order_id = '$order_id'";
    $result1 = mysqli_query($conn, $query0);
    
$mcq = "SELECT * FROM callback_report WHERE order_id = '$order_id'";
    $ccw = mysqli_query($conn, $mcq);    




$rows = count($results);

    for ($i = 0; $i < $rows; $i++) { 
        $txnid=$results[$i]["txnid"];
        $amount=$results[$i]["amount"];
        $Status=$results[$i]["status"];
        $utr=$results[$i]["utr"];
        $txnmessage=$results[$i]["txnmessage"];
        $description=$results[$i]["description"];
        $payerVpa=$results[$i]["payerVpa"];
        $issuerRefNo=$results[$i]["issuerRefNo"];
        $paymentApp=$results[$i]["paymentApp"];
        
        
if ($Status == '3' && $txnid == $hdfc_txn && $description == $db_description) {
    
    
    if (mysqli_num_rows($ccw) == 0) {
        $bytetoday = date("Y-m-d H:i:s");
        $sql = "INSERT INTO reports (transactionId, status, vpa, paymentApp, amount, user_token, UTR, description, mobile, date, user_id,user_mode)
        VALUES ('$txnid', '$Status', '$payerVpa', '$paymentApp', '$amount', '$user_token', '$issuerRefNo', '$description', '$mobile', '$bytetoday', '$megabyteuserid','$rmode')";
setXbyY($sql);

if($res_pmode[0]["pg_mode"] == 2){
    if($Status == '3' && $amount == $db_amount){
    $getuserdata = $conn->query("SELECT wallet FROM `users` WHERE `id` = '$megabyteuserid'")->fetch_assoc();
    $getchargedata = $conn->query("SELECT * FROM `charge` WHERE `status` = '1' ORDER BY id DESC LIMIT 1")->fetch_assoc();
    $getplatformcharge = ($getchargedata["platform_fee"] / 100) * $amount;
    $getgstcharge = ($getchargedata["gst_charge"] / 100) * $amount;
    $getadditionalcharge = ($getchargedata["additional_fees"] / 100) * $amount;
    if($getuserdata["plan_id"] == 4){
    $updatewalletbal = $getuserdata["wallet"]+$amount;
    }else{
    $updatewalletbal = $getuserdata["wallet"]+$amount-$getplatformcharge-$getgstcharge-$getadditionalcharge;
    }
    $conn->query("UPDATE `users` SET `wallet`='$updatewalletbal' WHERE `id` = '$megabyteuserid'");
 }
}

// Data to be sent
$postData = array(
    'status' => 'SUCCESS',
    'order_id' => $order_id,
    'remark1' => $bbbyteremark1
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
                 VALUES ('$order_id', '$url0', '$bytecallbackresponse', '$user_token', '$mobile', '$name')";
        $in = mysqli_query($conn, $call);

        $sq = "UPDATE `orders` SET status='SUCCESS' WHERE order_id='$order_id'";
        setXbyY($sq);
    }
}else if ($Status == '4' && $txnid == $hdfc_txn && $description == $db_description) {
    

    if (mysqli_num_rows($ccw) === 0) {
        $sqlv = "INSERT INTO reports (transactionId, status, vpa, paymentApp, amount, user_token, UTR, description, mobile, user_id,user_mode) 
         VALUES ('$txnid', '$Status', '$payerVpa', '$paymentApp', '$amount', '$user_token', '$issuerRefNo', '$description', '$mobile', '$megabyteuserid','$rmode')";
        setXbyY($sqlv);


          // Data to be sent
$postData = array(
    'status' => 'FAILURE',
    'utr' => 'NILL',
    'order_id' => $order_id
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

// Check if any error occurred
if (curl_errno($ch)) {
    // Optional: Handle error, log it, etc.
    // Example: error_log(curl_error($ch));
}

// Close cURL session
curl_close($ch);
        
        
            // Webhook request was sent successfully.
            $xpgreponse = "webhook sent";
        

        $callx = "INSERT INTO `callback_report`(`order_id`, `request_url`, `response`, `user_token`, `mobile`, `name`) 
                  VALUES ('$order_id', '$url_c', '$xpgreponse', '$user_token', '$mobile', '$name')";
        $inn = mysqli_query($conn, $callx);

        $sq = "UPDATE `orders` SET status='FAILURE' WHERE order_id='$order_id'";
        setXbyY($sq);
    }
}





 $sql = "UPDATE reports SET order_id='$order_id' WHERE description='$db_description'";
 setXbyY($sql);
}

$slq_p = "SELECT * FROM reports where description='$db_description'";
$res_pp = getXbyY($slq_p);  
// print_r($res_pp);
$db_status = $res_pp[0]['status'];

if($db_status=='3'){

 echo 'success';  
}elseif($db_status == '4'){
  echo 'FAILURE';    
}else{
    echo 'PENDING';
}


?>