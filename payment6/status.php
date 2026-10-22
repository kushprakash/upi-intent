<?php
require_once("../auth/components/main.components.php");
$output = array("status"=>false,"message"=>"Method Not Allowed","results"=>array());
if($_SERVER['REQUEST_METHOD'] === 'POST') {
$site_data = site_data();    
$post = json_decode(file_get_contents("php://input"),true);	

$token = strip_tags($post['token']);
$order_id = safe_str($post['order_id']);

if(isset($token)
&& !empty($token)
&& !empty($order_id)
){

if(strlen($order_id)>=6){

$sql = "SELECT * FROM `useraccount` WHERE token='".$token."' ";	
$userAccount = rechpay_fetch(rechpay_query($sql));
if($userAccount['user_id']>0 && $userAccount['token']==$token){
    
if($userAccount['status']=="Active"){
    
$transaction = rechpay_fetch(rechpay_query("SELECT * FROM `transaction` WHERE user_id='".$userAccount['user_id']."' and client_orderid='".$order_id."' "));
if(count($transaction)>0){

if($transaction['status']=="Pending"){
$merchant = rechpay_fetch(rechpay_query("SELECT * FROM `merchant` WHERE merchant_id='".$transaction['merchant_id']."' ")); 
if($merchant['merchant_id']>0 && $merchant['status']=="Active"){	

if($transaction['merchant_name']=="PhonePe Business"){
$merchant_data = json_decode($merchant['merchant_data'],true);    
$txn_response = get_phonepe_transaction($merchant['merchant_session'],$merchant_data['fingerprint'],$merchant_data['device_fingerprint'],$merchant_data['ip'],$transaction['bank_orderid']);
if($txn_response['success']==true && $txn_response['data']['paymentState']=="COMPLETED"){
$payment_mode = $txn_response['data']['paymentApp']['displayText']=="Wallet"? "WALLET" : "UPI";     
$utr_number = empty($txn_response['data']['utr'])? $txn_response['data']['transactionId'] : $txn_response['data']['utr']; 
$customer_vpa = empty($txn_response['data']['vpa'])? $txn_response['data']['customerDetails']['userName'] : $txn_response['data']['vpa'];
$txn_amount = $txn_response['data']['amount'] / 100;
if(transaction_success($transaction,$payment_mode,$customer_vpa,$utr_number,$txn_amount)){
$transaction['status'] = "Success";  
$transaction['utr_number'] = $utr_number;
$transaction['payment_mode'] = $payment_mode;
$transaction['customer_vpa'] = $customer_vpa;
$transaction['txn_amount'] = $txn_amount;
}

}else if($txn_response['success']==true && in_array($txn_response['data']['paymentState'],array("ERRORED","CANCELLED","FAILED"))){
$payment_mode = $txn_response['data']['paymentApp']['displayText']=="Wallet"? "WALLET" : "UPI";     
$utr_number = empty($txn_response['data']['utr'])? $txn_response['data']['transactionId'] : $txn_response['data']['utr']; 
$customer_vpa = empty($txn_response['data']['vpa'])? $txn_response['data']['customerDetails']['userName'] : $txn_response['data']['vpa'];
if(transaction_failed($transaction,$payment_mode,$customer_vpa,$utr_number)){
$transaction['status'] = "Failed";
}    
}else if(strtotime("-30 minutes")>strtotime($transaction['txn_date'])) {
if(transaction_failed($transaction,"UPI","Transaction Timeout",$transaction['txn_id'])){
$transaction['status'] = "Failed";  
}   
}

}else if($transaction['merchant_name']=="Paytm Business"){	
$transaction_response = get_paytm_transaction($merchant['merchant_session'],$merchant['merchant_csrftoken'],$transaction['bank_orderid']);
$txn_response = $transaction_response['orderList'][0]; 
if($txn_response['orderStatus']=="SUCCESS" && $txn_response['merchantTransId']==$transaction['bank_orderid']){
$data = json_decode($txn_response['extendInfo'],true,JSON_UNESCAPED_SLASHES);	
$orderInfo = json_decode($data['ORDER_CREATE_EXTEND_INFO'],true);
$payment_mode = $txn_response['payMethod']=="UPI" ? "UPI" : "WALLET";
$utr_number = json_decode($data['FLUXNET_EXTEND_INFO'],true)['referenceNo'];
$utr_number = empty($utr_number) ? $txn_response['bizOrderId'] : $utr_number;
$customer_vpa = empty($orderInfo['virtualPaymentAddr']) ? $orderInfo['payerName'] : $orderInfo['virtualPaymentAddr'];
$txn_amount = $txn_response['payMoneyAmount']['value'] / 100;
if(transaction_success($transaction,$payment_mode,$customer_vpa,$utr_number,$txn_amount)){
$transaction['status'] = "Success";  
$transaction['utr_number'] = $utr_number;
$transaction['payment_mode'] = $payment_mode;
$transaction['customer_vpa'] = $customer_vpa;
$transaction['txn_amount'] = $txn_amount;  
}

}else if(in_array($txn_response['orderStatus'],array("ERRORED","CANCELLED","FAILED","FAILURE"))){
$data = json_decode($txn_response['extendInfo'],true,JSON_UNESCAPED_SLASHES);	
$orderInfo = json_decode($data['ORDER_CREATE_EXTEND_INFO'],true);
$payment_mode = $txn_response['payMethod']=="UPI" ? "UPI" : "WALLET";
$utr_number = json_decode($data['FLUXNET_EXTEND_INFO'],true)['referenceNo'];
$utr_number = empty($utr_number) ? $txn_response['bizOrderId'] : $utr_number;
$customer_vpa = empty($orderInfo['virtualPaymentAddr']) ? $orderInfo['payerName'] : $orderInfo['virtualPaymentAddr'];
$txn_amount = $txn_response['payMoneyAmount']['value'] / 100;
if(transaction_failed($transaction,$payment_mode,$customer_vpa,$utr_number)){
$transaction['status'] = "Failed";  
}  
}else if(strtotime("-30 minutes")>strtotime($transaction['txn_date'])){
if(transaction_failed($transaction,"UPI","Transaction Timeout",$transaction['txn_id'])){
$transaction['status'] = "Failed";  
}    
}


}else if($transaction['merchant_name']=="SBI Merchant"){
$txn_response = get_sbimerchant_transaction($merchant['merchant_username'],$merchant['merchant_csrftoken'],$merchant['merchant_session'],$transaction['bank_orderid']);
if($txn_response['Transaction_Status']=="Paid" && $txn_response['Invoice_Number']==$transaction['bank_orderid']){
$payment_mode = "UPI";     
$utr_number = $txn_response['RRN'];
$customer_vpa = $txn_response['Auth_Code'];
$txn_amount = $txn_response['Transaction_Amount'];
if(transaction_success($transaction,$payment_mode,$customer_vpa,$utr_number,$txn_amount)){
$transaction['status'] = "Success";  
$transaction['utr_number'] = $utr_number;
$transaction['payment_mode'] = $payment_mode;
$transaction['customer_vpa'] = $customer_vpa;
$transaction['txn_amount'] = $txn_amount;    
}

}else if(strtotime("-30 minutes")>strtotime($transaction['txn_date'])) {
if(transaction_failed($transaction,"UPI","Transaction Timeout",$transaction['txn_id'])){
$transaction['status'] = "Failed";
}
}

} 



}	
}

$results = array(
    "txn_id" => $transaction['txn_id'],
    "order_id" => $transaction['client_orderid'],
    "merchant_id" => $transaction['merchant_id'],
    "merchant_name" => $transaction['merchant_name'],
    "merchant_vpa" => $transaction['merchant_upi'],
    "txn_date" => $transaction['txn_date'],
    "txn_amount" => $transaction['txn_amount'],
    "txn_note" => $transaction['txn_note'],
    "product_name" => $transaction['product_name'],
    "customer_name" => $transaction['customer_name'],
    "customer_mobile" => $transaction['customer_mobile'],
    "customer_email" => $transaction['customer_email'],
    "customer_vpa" => $transaction['customer_vpa'],
    "bank_orderid" => $transaction['bank_orderid'],
    "utr_number" => $transaction['utr_number'],
    "payment_mode" => $transaction['payment_mode'],
    "status" => $transaction['status'],
);

$output = array("status"=>true,"message"=>"Transaction Details","results"=>$results);	
}else{
$output = array("status"=>false,"message"=>"OrderID Not Found","results"=>array());	
}


}else{
$output = array("status"=>false,"message"=>"Account Not Active","results"=>array());	
}

}else{
$output = array("status"=>false,"message"=>"Token Not Valid","results"=>array());	
}


}else{
$output = array("status"=>false,"message"=>"Minimum OrderID 6 Digits Required","results"=>array());	
}

}else{
$output = array("status"=>false,"message"=>"Parameter Missing OR Invalid","results"=>array());	
}

}
header('Content-Type: application/json');
echo json_encode($output,JSON_NUMERIC_CHECK);