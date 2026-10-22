<?php
require_once("components/main.components.php");
$site_data = site_data();
$cron_token = strip_tags($_GET['cron_token']);
if($site_data['cron_token']==$cron_token){
$merchant = rechpay_fetch_all(rechpay_query("SELECT merchant_id,merchant_username,merchant_session,merchant_data FROM `merchant` WHERE `merchant_name`='SBI Merchant' AND `status`='Active' AND (NOW() - INTERVAL 300 MINUTE)>=merchant_timestamp ORDER BY merchant_timestamp ASC"));

$results = array();
$output = array("status"=>true,"message"=>"Data Not Found","results"=>$results);
foreach ($merchant as $key => $value){
$merchant = json_decode($value['merchant_data'],true);
$response = get_sbimerchant_profile($value['merchant_username'],$value['merchant_session']);
if(count($response)>0){
$sql = "UPDATE `merchant` SET `merchant_timestamp`='".current_timestamp()."', `status`='Active' WHERE merchant_id='".$value['merchant_id']."' ";	
if(rechpay_query($sql)){
$results[] = array("merchant_id"=>$value['merchant_id'],"user_id"=>$value['merchant_username']);
}
}else{
if(rechpay_query("UPDATE `merchant` SET `merchant_timestamp`='".current_timestamp()."', `status`='InActive' WHERE `merchant_id`='".$value['merchant_id']."' ")){
$results[] = array("merchant_id"=>$value['merchant_id'],"user_id"=>$value['merchant_username']);
}	
}
}

if(count($results)>0){
$output = array("status"=>true,"message"=>"Updated Successfully","results"=>$results);
}

header('Content-Type: application/json');
echo json_encode($output);
}else{
error_page("401 Unauthorized","The page you requested was Unauthorized");
}