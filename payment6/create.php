<?php
require_once("../merchant/components/main.components.php");
$output = array("status"=>false,"message"=>"Method Not Allowed","results"=>array());
if($_SERVER['REQUEST_METHOD'] === 'POST') {
$site_data = site_data();    
$post = json_decode(file_get_contents("php://input"),true);	

$token = strip_tags($post['token']);
$order_id = safe_str($post['order_id']);
$txn_amount = safe_str($post['txn_amount']);
$txn_note = safe_str($post['txn_note']);
$product_name = safe_str($post['product_name']);
$customer_name = safe_str($post['customer_name']);
$customer_mobile = safe_str($post['customer_mobile']);
$customer_email = strip_tags($post['customer_email']);
$callback_url = strip_tags($post['callback_url']);

if(isset($token)
&& !empty($token)
&& !empty($order_id)
&& !empty($txn_amount)
&& !empty($txn_note)
&& !empty($product_name)
&& !empty($customer_name) 
&& !empty($customer_mobile) 
&& !empty($customer_email)
){

if(strlen($order_id)>=6){
    
if(is_numeric($txn_amount) && (strlen($txn_amount)>=0 && strlen($txn_amount)<=5)){

if(is_numeric($customer_mobile) && strlen($customer_mobile)==10 && filter_var($customer_email, FILTER_VALIDATE_EMAIL)){

$sql = "SELECT * FROM `useraccount` WHERE token='".$token."' ";	
$userAccount = rechpay_fetch(rechpay_query($sql));
if($userAccount['user_id']>0 && $userAccount['token']==$token){
    
if($userAccount['status']=="Active"){
    
if(in_array($userAccount['is_expire'],['No','Alert'])){

if($userAccount['plan_limit']>0){
    
$old_transaction = rechpay_fetch(rechpay_query("SELECT * FROM `transaction` WHERE user_id='".$userAccount['user_id']."' and client_orderid='".$order_id."' "));
if(count($old_transaction)==0){
 
$merchant = rechpay_fetch(rechpay_query("SELECT * FROM `merchant` WHERE user_id='".$userAccount['user_id']."' and merchant_primary='Active' and status='Active' ")); 

if($merchant['merchant_id']>0 && $merchant['status']=="Active"){
    
if($merchant['merchant_name']=="PhonePe Business"){   
$merchant_auth = get_phonepe_qrcode($merchant['merchant_session']);
}else if($merchant['merchant_name']=="Paytm Business"){   
$merchant_auth = get_paytm_qrcode($merchant['merchant_session'],$merchant['merchant_csrftoken']);	
}else if($merchant['merchant_name']=="SBI Merchant"){
$merchant_auth = get_sbimerchant_profile($merchant['merchant_username'],$merchant['merchant_session']);
}else{
$merchant_auth = array();    
}

if($merchant_auth['enabled']==true || (count($merchant_auth)>0 && !empty($merchant_auth['Mobile1'])) || ($merchant_auth['statusCode']=="200" && count($merchant_auth['response'])>0) ){

$pending_transaction = rechpay_fetch(rechpay_query("SELECT count(*) as total FROM `transaction` WHERE `status`='Pending' and `merchant_id`='".$merchant['merchant_id']."' ")); 
if($pending_transaction['total']<=5 || $merchant['merchant_name']=="PhonePe Business" || $merchant['merchant_name']=="Paytm Business"){
    
$txn_date = current_timestamp();
$bank_orderid = order_txn_id("IT");
$payment_token = md5($bank_orderid);
$utr_number = date("ymdhis");
$sql = "INSERT INTO `transaction`(`txn_mode`, `user_id`, `merchant_id`, `merchant_name`, `merchant_upi`, `client_orderid`, `txn_date`, `txn_amount`, `txn_note`, `product_name`, `customer_name`, `customer_mobile`, `customer_email`, `customer_vpa`, `bank_orderid`, `utr_number`, `payment_mode`, `payment_token`, `callback_url`, `webhook_status`, `status`) 
VALUES ('INTENT','".$userAccount['user_id']."','".$merchant['merchant_id']."','".$merchant['merchant_name']."','".$merchant['merchant_upi']."','".$order_id."','".$txn_date."','".$txn_amount."','".$txn_note."','".$product_name."','".$customer_name."','".$customer_mobile."','".$customer_email."','','".$bank_orderid."','".$utr_number."','UPI','".$payment_token."','".$callback_url."','Pending','Pending')";	
$txn_id = rechpay_insert($sql);
if($txn_id){
rechpay_query("UPDATE `useraccount` SET plan_limit = (plan_limit-1) WHERE user_id='".$userAccount['user_id']."' ");


    
$results = array();
$results['txn_id'] = $txn_id;
$results['payment_url'] = $site_data['protocol'].$site_data['baseurl']."/order/payment/$payment_token";

if($merchant['merchant_payupi']=="Show"){
$upiArr = array();
$upiArr['pa'] = $merchant['merchant_upi'];
$upiArr['pn'] = $userAccount['company'];
$upiArr['am'] = $txn_amount;
$upiArr['mam'] = $txn_amount - 1;
$upiArr['tr'] = $bank_orderid;
$upiArr['tn'] = $txn_note;
$deep_link = upi_qr_code("upi",$upiArr);
$bhim_link = $deep_link['qrIntent'];
$phonepe_link = upi_qr_code("phonepe",$upiArr)['qrIntent'];
$paytm_link = upi_qr_code("paytmmp",$upiArr)['qrIntent'];
$gpay_link = upi_qr_code("tez",$upiArr)['qrIntent'];	
$results['upi_intent'] = array("bhim"=>$bhim_link,"phonepe"=>$phonepe_link,"paytm"=>$paytm_link,"gpay"=>$gpay_link);
$results['qr_image'] = $deep_link['qrCode'];
}

$output = array("status"=>true,"message"=>"Order Created Successfully","results"=>$results);    
}else{
$output = array("status"=>false,"message"=>"Server Error","results"=>array());	
}

}else{
$output = array("status"=>false,"message"=>"Maximum 5 Pending Orders Allowed","results"=>array());    
}

}else{
$output = array("status"=>false,"message"=>"Merchant Login Expired","results"=>array());	
}

}else{
$output = array("status"=>false,"message"=>"Merchant Not Active","results"=>array());	
}

  
}else{
$output = array("status"=>false,"message"=>"Duplicate OrderID","results"=>array());	
}

}else{
$output = array("status"=>false,"message"=>"Transactions Limit Not Available","results"=>array());	
}

}else{
$output = array("status"=>false,"message"=>"No Active Plan Available","results"=>array());	
}

}else{
$output = array("status"=>false,"message"=>"Account Not Active","results"=>array());	
}

}else{
$output = array("status"=>false,"message"=>"Token Not Valid","results"=>array());	
}

}else{
$output = array("status"=>false,"message"=>"Customer Mobile OR Email Not Valid","results"=>array());	
}

}else{
$output = array("status"=>false,"message"=>"Txn Amount Not Valid","results"=>array());	
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