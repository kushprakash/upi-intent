<?php
require_once("../merchant/components/session.components.php");
require_once("../merchant/components/main.components.php");

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

$output = array("status"=>"FAILED","message"=>"Unauthorized Access");
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
$order_id = $_POST['order_id'];

$transaction = rechpay_fetch(rechpay_query("SELECT * FROM `orders` WHERE order_id='".$order_id."' "));
$amount = $transaction["amount"];
$users = rechpay_fetch(rechpay_query("SELECT * FROM `users` WHERE id='".$transaction['user_id']."' "));
if($users["pg_mode"] == 2){
    $txnuserid = '157';
}else{
$txnuserid = $users["id"];
}

if(count($transaction)>0 && $transaction['order_id']==$order_id){
if($transaction['status']=="PENDING"){
    
$merchant = rechpay_fetch(rechpay_query("SELECT * FROM `merchant` WHERE user_id='$txnuserid' ")); 
if($merchant['merchant_id']>0 && $merchant['status']=="Active"){

$txn_response = get_sbimerchant_transaction($merchant['merchant_username'],$merchant['merchant_csrftoken'],$merchant['merchant_session'],$transaction['bank_orderid']);
// echo "<pre>";
// print_r($txn_response);
// exit;
if($txn_response['Transaction_Status']=="Paid" && $txn_response['Invoice_Number']==$transaction['bank_orderid']){
$payment_mode = "UPI";     
$utr_number = $txn_response['RRN'];
$customer_vpa = $txn_response['Auth_Code'];
$txn_amount = $txn_response['Transaction_Amount'];

if(transaction_success($transaction,$payment_mode,$customer_vpa,$utr_number,$txn_amount)){
    
    if($users["pg_mode"] == 2){
    $getchargedata = rechpay_fetch(rechpay_query("SELECT * FROM `charge` WHERE `status` = '1' ORDER BY id DESC LIMIT 1"));
    $getplatformcharge = ($getchargedata["platform_fee"] / 100) * $txn_amount;
    $getgstcharge = ($getchargedata["gst_charge"] / 100) * $txn_amount;
    $getadditionalcharge = ($getchargedata["additional_fees"] / 100) * $txn_amount;
    $totalcharge = $getplatformcharge+$getgstcharge+$getadditionalcharge;
    $updatewalletbal = $users["wallet"]+$txn_amount-$getplatformcharge-$getgstcharge-$getadditionalcharge;
    
    rechpay_query("UPDATE `orders` SET `charge`='$totalcharge' WHERE `order_id` = '$order_id'");
    rechpay_query("UPDATE `users` SET `wallet`='$updatewalletbal' WHERE `id` = '".$transaction['user_id']."'");
}
    
 rechpay_query("INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, user_id, user_mode) VALUES ('{$transaction['bank_orderid']}','SUCCESS','$order_id','$customer_vpa','','$payment_mode','$txn_amount','{$merchant["user_token"]}','','{$transaction['user_id']}','1')");  
 
$output = array("status"=>'SUCCESS', "message"=>"Transaction Successfully !");   
}else{

rechpay_query("INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, user_id, user_mode) VALUES ('{$transaction['bank_orderid']}','FAILURE','$order_id','$customer_vpa','','$payment_mode','$txn_amount','{$merchant["user_token"]}','','{$transaction['user_id']}','1')");    
$output = array("status"=>'FAILED', "message"=>'Server Error');

}

}else{

if(transaction_failed($transaction,"UPI","Transaction Failed Due To Wrong Amount Recieved!",$transaction['order_id'])){
    rechpay_query("INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, user_id, user_mode) VALUES ('{$transaction['bank_orderid']}','FAILURE','$order_id','$customer_vpa','','$payment_mode','$txn_amount','{$merchant["user_token"]}','','{$transaction['user_id']}','1')");
    
$output = array("status"=>'FAILED', "message"=>"Transaction Failed Due To Wrong Amount Recieved! Note : This amount is not Refundable.");   
}else{
$output = array("status"=>'FAILED', "message"=>'Server Error');     
}   

}

}else if(strtotime("-30 minutes")>strtotime($transaction['create_date'])) {
    
if(transaction_failed($transaction,"UPI","Transaction Timeout",$transaction['order_id'])){
    rechpay_query("INSERT INTO reports (transactionId, status, order_id, vpa, user_name, paymentApp, amount, user_token, transactionNote, user_id, user_mode) VALUES ('{$transaction['bank_orderid']}','FAILURE','$order_id','$customer_vpa','','$payment_mode','$txn_amount','{$merchant["user_token"]}','','{$transaction['user_id']}','1')");
    
$output = array("status"=>'FAILED', "message"=>"Transaction Failed");   
}else{
$output = array("status"=>'FAILED', "message"=>'Server Error');     
}    
}else{
$output = array("status"=>'PENDING', "message"=>'Transaction Pending');   
}

}else{
$output = array("status"=>'FAILED', "message"=>'Merchant Not Active');
}

}else if($transaction['status']=="SUCCESS"){
    
$output = array("status"=>'SUCCESS', "message"=>"Transaction Successfully"); 
}else{
$output = array("status"=>'FAILED', "message"=>'Duplicate Request');
}

}else{
$output = array("status"=>"FAILED","message"=>"Transaction Not Found");    
}


header('Content-Type: application/json');
echo json_encode($output,JSON_NUMERIC_CHECK);
?>