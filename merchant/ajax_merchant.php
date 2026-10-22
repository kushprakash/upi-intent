<?php
if($_SERVER['REQUEST_METHOD'] === 'POST') {
require_once("components/session.components.php");
require_once("components/main.components.php");

// ini_set("display_errors",true);
// error_reporting(E_ALL);

$site_data = site_data();
$uid_token = $_SESSION['username'];
$sql = "SELECT * FROM `users` WHERE mobile='".$uid_token."' ";	
$userAccount = rechpay_fetch(rechpay_query($sql));
if(empty($userAccount['id']) || !isset($uid_token) || empty($uid_token)){
session_destroy();	
header("location: index");
exit("Login Session is expired");
}


if(isset($_POST['get_merchant_otp']) && !empty($_POST['merchant_id'])){
$merchant_id = safe_str($_POST['merchant_id']);	
$sql = "SELECT * FROM `merchant` WHERE user_id='".$userAccount['id']."' and merchant_id='".$merchant_id."' ";
if($userAccount['role']=="Admin"){
$sql = "SELECT * FROM `merchant` WHERE merchant_id='".$merchant_id."' ";
}
$merchant = rechpay_fetch(rechpay_query($sql));
if($merchant['merchant_id']>0 && $merchant['merchant_id']==$merchant_id){

if($merchant['merchant_name']=="PhonePe Business"){
    
$response =  get_phonepe_otp($merchant['merchant_username']);
if($response['expiry']==600){
$sql = "UPDATE `merchant` SET `merchant_timestamp`='".current_timestamp()."', `merchant_session`='".$response['token']."', `merchant_csrftoken`='".$response['device_fingerprint']."', `merchant_token`='".$response['fingerprint']."', `status`='InActive' WHERE merchant_id='".$merchant['merchant_id']."' ";	
if(rechpay_query($sql)){    
$html = '<h4 class="mt-2"><b>PhonePe OTP Verification</b></h4><hr>';
$html .= '
<form method="POST" action="" class="mt-2 text-left mb-3" autocomplete="off">
    <div class="mb-2"><input type="text" name="otp" id="otp" placeholder="One Time Password" class="form-control form-control-sm" onkeypress="if(this.value.length==5) return false;" required="" /></div>
    <div class="mb-2">
        <input type="hidden" name="ip" id="ip" value="'.$response['ip'].'" class="form-control" required="" /> 
        <input type="hidden" name="merchant_id" id="merchant_id" value="'.$merchant['merchant_id'].'" class="form-control" required="" /> 
        <button type="button" name="verify_otp" id="verify_otp" onclick="get_verify_otp($(\'#merchant_id\').val(),$(\'#ip\').val(),$(\'#otp\').val())" class="btn btn-success btn-sm btn-block mt-2">Verify <i class="la la-key"></i></button>
    </div>
</form>
';
$html .= '<hr><small class="text-danger">Company is not responsible for any kind of loss.<br> Please check your URL before OTP Verify 👍</small>';

$output = array("status"=>true,"message"=>"OTP Sent Successfully","html"=>$html); 
}else{
$output = array("status"=>false,"message"=>"Server Error","html"=>"");     
}

}else{
$output = array("status"=>false,"message"=>$response['message'],"html"=>"");     
}

}else if($merchant['merchant_name']=="Paytm Business"){
    
$response =  get_paytm_otp($merchant['merchant_username'],$merchant['merchant_password']);

if($response['status']=="SUCCESS"){
$sql = "UPDATE `merchant` SET `merchant_timestamp`='".current_timestamp()."', `merchant_session`='".$response['stateCode']."', `merchant_csrftoken`='".$response['csrfToken']."', `merchant_token`='".$response['responseCode']."', `status`='InActive' WHERE merchant_id='".$merchant['merchant_id']."' ";	
if(rechpay_query($sql)){    
$html = '<h4 class="mt-2"><b>Paytm OTP Verification</b></h4><hr>';
$html .= '
<form method="POST" action="" class="mt-2 text-left mb-3" autocomplete="off">
    <div class="mb-2"><input type="text" name="otp" id="otp" placeholder="One Time Password" class="form-control form-control-sm" onkeypress="if(this.value.length==6) return false;" required="" /></div>
    <div class="mb-2">
        <input type="hidden" name="ip" id="ip" value="'.$response['ip'].'" class="form-control" required="" /> 
        <input type="hidden" name="merchant_id" id="merchant_id" value="'.$merchant['merchant_id'].'" class="form-control" required="" /> 
        <button type="button" name="verify_otp" id="verify_otp" onclick="get_verify_otp($(\'#merchant_id\').val(),$(\'#ip\').val(),$(\'#otp\').val())" class="btn btn-success btn-sm btn-block mt-2">Verify <i class="la la-key"></i></button>
    </div>
</form>
';
$html .= '<hr><small class="text-danger">Company is not responsible for any kind of loss.<br> Please check your URL before OTP Verify 👍</small>';

$output = array("status"=>true,"message"=>"OTP Sent Successfully","html"=>$html); 
}else{
$output = array("status"=>false,"message"=>"Server Error","html"=>"");     
}

}else{
$output = array("status"=>false,"message"=>$response['message'],"html"=>"");     
}

}else if($merchant['merchant_name']=="SBI Merchant"){

$response = get_sbimerchant_validation($merchant['merchant_username']); 
if(!empty($response['Result'][0]['ExistingUser'])){
$response = get_sbimerchant_login($merchant['merchant_username'],$merchant['merchant_password']);   
if(!empty($response['Result'][0]['GUID']) && $response['Result'][0]['bqr']>0){
$merchant_data = json_encode($response);  
$merchant_upi = "SBIPMOPAD.{$response['Result'][0]['MID']}-{$response['Result'][0]['FinalTID']}@SBIPAY";
$sql = "UPDATE `merchant` SET `merchant_timestamp`='".current_timestamp()."', `merchant_session`='".$response['Result'][0]['GUID']."', `merchant_csrftoken`='".$response['Result'][0]['FinalTID']."', `merchant_token`='".$response['Result'][0]['MercID']."', `merchant_data`='".$merchant_data."', `merchant_upi`='".$merchant_upi."', `status`='Active' WHERE merchant_id='".$merchant['merchant_id']."' ";	
if(rechpay_query($sql)){
rechpay_query("UPDATE `users` SET `sbi_connected`='Yes' WHERE id='{$merchant["user_id"]}'"); 

// inactive other merchant
$tablesarr = ["bharatpe_tokens","freecharge","googlepay_tokens","hdfc","paytm_tokens","phonepe_tokens"];
$connected_merarr = ["hdfc_connected","phonepe_connected","paytm_connected","freecharge_connected","bharatpe_connected","googlepay_connected"];

foreach($tablesarr as $tables){
    $fetchmerchant = rechpay_query("SELECT user_token FROM `$tables` WHERE user_token = '{$merchant["user_token"]}' AND status = 'Active'");
    if($fetchmerchant->fetchColumn() > 0){
        rechpay_query("UPDATE $tables SET status = 'Deactive' WHERE user_token = '{$merchant["user_token"]}'");
    }
}

foreach($connected_merarr as $connected){
   rechpay_query("UPDATE users SET $connected = 'No' WHERE user_token = '{$merchant["user_token"]}'");
}

$html = '<h4 class="text-success mt-3"><b>Verified Successfully</b></h4>';
$html .= '
<table class="table table-bordered mt-4 mb-0"> 
<thead>';

$html .= '
<tr>
<th><small class="f-400">Merchant MID</small><br>'.$response['Result'][0]['MID'].'</th>
</tr>';

$html .= '
<tr>
<th><small class="f-400">Merchant TID</small><br>'.$response['Result'][0]['FinalTID'].'</th>
</tr>';


$html .= '
<tr>
<th><small class="f-400">Merchant UPI Name</small><br>'.$response['Result'][0]['MName'].'</th>
</tr>';

$html .= '
<tr>
<th><small class="f-400">Merchant UPI ID</small><br>'.$merchant_upi.'</th>
</tr>';

$html .= '</thead></table>';

$output = array("status"=>true,"message"=>"Verified Successfully","html"=>$html);     
}else{
$output = array("status"=>false,"message"=>"Server Error","html"=>"");     
}
 
}else{
$output = array("status"=>false,"message"=>$response['Result'][0]['Message'],"html"=>"");    
}

}else{
$output = array("status"=>false,"message"=>$response['Result'][0]['Message'],"html"=>"");    
}

}else{
$output = array("status"=>false,"message"=>"Merchant Not Selected","html"=>"");    
}

}else{
$output = array("status"=>false,"message"=>"Merchant is Not Found","html"=>"");
}


header('Content-Type: application/json');
echo json_encode($output);
}


if(isset($_POST['get_verify_otp']) && !empty($_POST['merchant_id'])  && !empty($_POST['ip']) && !empty($_POST['otp']) ){
$ip = safe_str($_POST['ip']);    
$merchant_id = safe_str($_POST['merchant_id']);
$otp = safe_str($_POST['otp']);
if($merchant_id>0 && is_numeric($merchant_id)){

if(is_numeric($otp) && strlen($otp)>0){

$merchant = rechpay_fetch(rechpay_query("SELECT * FROM `merchant` WHERE user_id='".$userAccount['id']."' and merchant_id='".$merchant_id."' "));
if($merchant['merchant_id']==$merchant_id){
	
if($merchant['merchant_name']=="PhonePe Business"){
	
$authorization = array("csrftoken"=>$csrftoken,"merchant_session"=>$merchant_session);
$results = get_phonepe_verify($merchant['merchant_username'],$merchant['merchant_session'],$merchant['merchant_token'],$merchant['merchant_csrftoken'],$ip,$otp);
if($results['success']==true){

$fingerprint = $merchant['merchant_token'];
$deviceFingerprint = $merchant['merchant_csrftoken'];


$merchant_data =  array();  
$merchant_data['number'] = $merchant['merchant_username'];  
$merchant_data['userid'] = $results['userId'];    

$results = get_phonepe_refresh($results['token'],$results['refreshToken'],$fingerprint,$deviceFingerprint,$ip);    

if($results['expiresAt']>0){
$merchant_data['token'] = $results['token'];     
$merchant_data['refresh'] = $results['refreshToken'];     
$merchant_data['fingerprint'] = $merchant['merchant_csrftoken'];  
$merchant_data['device_fingerprint'] = $merchant['merchant_token'];  
$merchant_data['ip'] = $ip; 


$results = get_phonepe_group($results['token'],$fingerprint,$deviceFingerprint,$results['farm'],$ip);
if(count($results)>0){
$merchant_data['groupData'] = $results;   
  
$merchantData =  json_encode($merchant_data);
$sql = "UPDATE `merchant` SET `merchant_timestamp`='".current_timestamp()."', `merchant_session`='".$merchant_data['token']."', `merchant_csrftoken`='".$merchant_data['refresh']."', `merchant_data`='".$merchantData."', `status`='InActive' WHERE merchant_id='".$merchant['merchant_id']."' ";	
if(rechpay_query($sql)){
$html = '<h4 class="mt-2"><b>Select Business</b></h4><hr>';
$html .= '
<table class="table mt-3"> 
<thead>';
foreach($results as $key=>$value){
$html .= '
<tr>
<th scope="col" class="text-dark border-0">
<button class="btn btn-outline-primary btn-block" onclick="set_business(\''.$merchant['merchant_id'].'\',\''.$value['userGroupNamespace']['groupId'].'\')">'.$value['merchantName'].' - '.$value['userGroupNamespace']['groupValue'].' <i class="la la-angle-right la-lg"></i></button>
</th>
</tr>
';    
}
$html .= '</thead></table>';
$html .= '<hr><small class="text-danger">Company is not responsible for any kind of loss.<br> Please check your URL before Select Business 👍</small>';
$output = array("status"=>true,"message"=>"OTP Verified Successfully","html"=>$html);
}else{
$output = array("status"=>false,"message"=>"Server is Down","html"=>"");
}    
    
}else{
$output = array("status"=>false,"message"=>$results['message'],"html"=>"");
}   
    
}else{
$output = array("status"=>false,"message"=>"Login Error Try Again","html"=>"");
}


}else{
$output = array("status"=>false,"message"=>$results['message'],"html"=>"");
}

}else if($merchant['merchant_name']=="Paytm Business"){
$results = get_paytm_verify($merchant['merchant_session'],$merchant['merchant_csrftoken'],$otp);
if($results['status']=="SUCCESS"){
$paytm_qrcode = get_paytm_qrcode($results['merchant_session'],$results['merchant_csrftoken']);	
if($paytm_qrcode['statusCode']=="200" && count($paytm_qrcode['response'])>0){
$merchant_data = json_encode($paytm_qrcode);  
$resData =  $paytm_qrcode['response'][0];
parse_str($resData['deepLink'], $upiData);
$merchant_upi = $upiData['upi://pay?pa'];
$sql = "UPDATE `merchant` SET `merchant_timestamp`='".current_timestamp()."', `merchant_session`='".$results['merchant_session']."', `merchant_csrftoken`='".$results['merchant_csrftoken']."', `merchant_token`='".$resData['stickerId']."', `merchant_data`='".$merchant_data."', `merchant_upi`='".$merchant_upi."', `status`='Active' WHERE merchant_id='".$merchant['merchant_id']."' ";	
if(rechpay_query($sql)){

$html = '<h4 class="mt-3"><b>UPI Merchant Details</b></h4>';
$html .= '
<table class="table table-bordered mt-4 mb-0"> 
<thead>';

$html .= '
<tr>
<th><small class="f-400">Mobile Number</small><br>'.$merchant['merchant_username'].'</th>
<th><small class="f-400">Merchant Name</small><br>'.$resData['displayName'].'</th>
</tr>';

$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant ID</small><br>'.$resData['mappingId'].'</th>
</tr>';

$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant UPI Name</small><br>'.$resData['displayName'].'</th>
</tr>';

$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant UPI ID</small><br>'.$merchant_upi.'</th>
</tr>';

$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant Sticker ID</small><br>'.$resData['stickerId'].'</th>
</tr>';

$html .= '
<tr>
<th><small class="f-400">Merchant Primary</small><br>
<div class="form-check form-switch d-flex flex-row pt-2 p-0 justify-content-center">
<label class="form-check-label" for="merchant_primary">InActive</label>
<input class="mx-2 form-check-input" id="merchant_primary" type="checkbox" value="Active" name="merchant_primary" onchange="set_merchant_primary(\''.$merchant['merchant_id'].'\',\'#merchant_primary\')" '.$merchant_primary.'>
<label class="form-check-label" for="merchant_primary">Active</label>
</div>
</th>

<th><small class="f-400">Pay via UPI Button</small><br>
<div class="form-check form-switch d-flex flex-row pt-2 p-0 justify-content-center">
<label class="form-check-label" for="merchant_payupi">Show</label>
<input class="mx-2 form-check-input" id="merchant_payupi" type="checkbox" value="Show" name="merchant_payupi" onchange="set_merchant_payupi(\''.$merchant['merchant_id'].'\',\'#merchant_payupi\')" '.$merchant_payupi.'>
<label class="form-check-label" for="merchant_payupi">Hide</label>
</div>
</th>
</tr>';

$html .= '</thead></table>';
$output = array("status"=>true,"message"=>"OTP Verified Successfully","html"=>$html);
}else{
$output = array("status"=>false,"message"=>"Server Error","html"=>"");	
}
	

}else{
$output = array("status"=>false,"message"=>$paytm_qrcode['message'],"html"=>"");	
}	
	
}else{
$output = array("status"=>false,"message"=>$results['message'],"html"=>"");	
}
	
}else{
$output = array("status"=>false,"message"=>"Merchant Not Valid","html"=>"");	
}

	
}else{
$output = array("status"=>false,"message"=>"Merchant Is Not Available","html"=>"");
}

}else{
$output = array("status"=>false,"message"=>"OTP Is Not Valid","html"=>"");
}

}else{
$output = array("status"=>false,"message"=>"Merchant Is Not Valid","html"=>"");
}


header('Content-Type: application/json');
echo json_encode($output);
}




if(isset($_POST['set_business']) && !empty($_POST['merchant_id'])  && !empty($_POST['groupId'])){
$merchant_id = safe_str($_POST['merchant_id']);
$groupId = safe_str($_POST['groupId']);
if($merchant_id>0 && is_numeric($merchant_id)){

if(is_numeric($groupId) && strlen($groupId)>0){

$merchant = rechpay_fetch(rechpay_query("SELECT * FROM `merchant` WHERE user_id='".$userAccount['id']."' and merchant_id='".$merchant_id."' "));

if($merchant['merchant_id']==$merchant_id){
$merchantData = json_decode($merchant['merchant_data'],true);
$response = set_phonepe_group($merchant['merchant_session'],$merchantData['fingerprint'],$merchantData['device_fingerprint'],$groupId,$merchantData['ip']);
if($response['success']==true){
    
$merchant_data =  array();  
$merchant_data['number'] = $merchant['merchant_username'];  
$merchant_data['userid'] = $response['userId'];    
$merchant_data['name'] = $response['name'];
$merchant_data['token'] = $response['token'];     
$merchant_data['refresh'] = $response['refreshToken'];     
$merchant_data['fingerprint'] = $merchantData['fingerprint'];  
$merchant_data['device_fingerprint'] = $merchantData['device_fingerprint'];  
$merchant_data['ip'] = $merchantData['ip'];     
$merchant_data['groupData'] = $response;  
$merchantData =  json_encode($merchant_data); 

$qrdata = get_phonepe_qrcode($response['token']);
if($qrdata['enabled']==true){
$merchant_qrdata = json_encode($qrdata);
$merchant_upi = "{$qrdata['qrCodeId']}@{$qrdata['pspHandle']}";
$sql = "UPDATE `merchant` SET `merchant_timestamp`='".current_timestamp()."', `merchant_session`='".$merchant_data['token']."', `merchant_csrftoken`='".$merchant_data['refresh']."', `merchant_data`='".$merchantData."', `merchant_qrdata`='".$merchant_qrdata."', `merchant_upi`='".$merchant_upi."', `status`='Active' WHERE merchant_id='".$merchant['merchant_id']."' ";	
if(rechpay_query($sql)){    
$output = array("status"=>true,"message"=>"Selected Successfully"); 
}else{
$output = array("status"=>false,"message"=>"Server Error");     
}  

}else{
$output = array("status"=>false,"message"=>$qrdata['message']);
}
    
}else{
$output = array("status"=>false,"message"=>$results['message']);
}

}else{
$output = array("status"=>false,"message"=>"Merchant Is Not Available");
}

}else{
$output = array("status"=>false,"message"=>"Business Not Valid");
}

}else{
$output = array("status"=>false,"message"=>"Merchant Is Not Valid");
}


header('Content-Type: application/json');
echo json_encode($output);
}



if(isset($_POST['get_merchant_view']) && !empty($_POST['merchant_id'])){
$merchant_id = safe_str($_POST['merchant_id']);
if($merchant_id>0 && is_numeric($merchant_id)){

$merchant = rechpay_fetch(rechpay_query("SELECT * FROM `merchant` WHERE user_id='".$userAccount['id']."' and merchant_id='".$merchant_id."' "));
if($merchant['merchant_id']==$merchant_id){
    
if($merchant['status']=="Active"){
    
$merchant_primary = $merchant['merchant_primary']=="Active" ? "checked" : "";
$merchant_payupi = $merchant['merchant_payupi']=="Show" ? "checked" : "";

if($merchant['merchant_name']=="PhonePe Business"){
    
$merchant_qrdata = json_decode($merchant['merchant_qrdata'],true);
$merchant_data = json_decode($merchant['merchant_data'],true);


$html = '<h4 class="mt-3"><b>UPI Merchant Details</b></h4>';
$html .= '
<table class="table table-bordered mt-4 mb-0"> 
<thead>';

$html .= '
<tr>
<th><small class="f-400">Mobile Number</small><br>'.$merchant['merchant_username'].'</th>
<th><small class="f-400">Merchant Name</small><br>'.$merchant_qrdata['mapping']['merchant']['fullName'].'</th>
</tr>';

$html .= '
<tr>
<th><small class="f-400">Merchant ID</small><br>'.$merchant_qrdata['mapping']['merchant']['merchantId'].'</th>
<th><small class="f-400">Merchant Role</small><br>'.$merchant_data['groupData']['roleName'].'</th>
</tr>';

$html .= '
<tr>
<th><small class="f-400">Merchant UPI Name</small><br>'.$merchant_qrdata['mapping']['merchant']['displayName'].'</th>
<th><small class="f-400">Merchant UPI ID</small><br>'.$merchant['merchant_upi'].'</th>
</tr>';

$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant Store ID</small><br>'.$merchant_qrdata['mapping']['storeId'].'</th>
</tr>';

$html .= '
<tr>
<th><small class="f-400">Merchant Primary</small><br>
<div class="form-check form-switch d-flex flex-row pt-2 p-0 justify-content-center">
<label class="form-check-label" for="merchant_primary">InActive</label>
<input class="mx-2 form-check-input" id="merchant_primary" type="checkbox" value="Active" name="merchant_primary" onchange="set_merchant_primary(\''.$merchant['merchant_id'].'\',\'#merchant_primary\')" '.$merchant_primary.'>
<label class="form-check-label" for="merchant_primary">Active</label>
</div>
</th>

<th><small class="f-400">Pay via UPI Button</small><br>
<div class="form-check form-switch d-flex flex-row pt-2 p-0 justify-content-center">
<label class="form-check-label" for="merchant_payupi">Show</label>
<input class="mx-2 form-check-input" id="merchant_payupi" type="checkbox" value="Show" name="merchant_payupi" onchange="set_merchant_payupi(\''.$merchant['merchant_id'].'\',\'#merchant_payupi\')" '.$merchant_payupi.'>
<label class="form-check-label" for="merchant_payupi">Hide</label>
</div>
</th>
</tr>';

$html .= '</thead></table>';
$output = array("status"=>true,"message"=>"Data Fetched Successfully","html"=>$html);

}else if($merchant['merchant_name']=="Paytm Business"){
    
$merchant_qrdata = json_decode($merchant['merchant_qrdata'],true);
$merchant_data = json_decode($merchant['merchant_data'],true);
$resData =  $merchant_data['response'][0];
parse_str($resData['deepLink'], $upiData);
$merchant_upi = $upiData['upi://pay?pa'];

$html = '<h4 class="mt-3"><b>UPI Merchant Details</b></h4>';
$html .= '
<table class="table table-bordered mt-4 mb-0"> 
<thead>';

$html .= '
<tr>
<th><small class="f-400">Mobile Number</small><br>'.$merchant['merchant_username'].'</th>
<th><small class="f-400">Merchant Name</small><br>'.$resData['displayName'].'</th>
</tr>';

$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant ID</small><br>'.$resData['mappingId'].'</th>
</tr>';

$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant UPI Name</small><br>'.$resData['displayName'].'</th>
</tr>';

$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant UPI ID</small><br>'.$merchant_upi.'</th>
</tr>';

$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant Sticker ID</small><br>'.$resData['stickerId'].'</th>
</tr>';

$html .= '
<tr>
<th><small class="f-400">Merchant Primary</small><br>
<div class="form-check form-switch d-flex flex-row pt-2 p-0 justify-content-center">
<label class="form-check-label" for="merchant_primary">InActive</label>
<input class="mx-2 form-check-input" id="merchant_primary" type="checkbox" value="Active" name="merchant_primary" onchange="set_merchant_primary(\''.$merchant['merchant_id'].'\',\'#merchant_primary\')" '.$merchant_primary.'>
<label class="form-check-label" for="merchant_primary">Active</label>
</div>
</th>

<th><small class="f-400">Pay via UPI Button</small><br>
<div class="form-check form-switch d-flex flex-row pt-2 p-0 justify-content-center">
<label class="form-check-label" for="merchant_payupi">Show</label>
<input class="mx-2 form-check-input" id="merchant_payupi" type="checkbox" value="Show" name="merchant_payupi" onchange="set_merchant_payupi(\''.$merchant['merchant_id'].'\',\'#merchant_payupi\')" '.$merchant_payupi.'>
<label class="form-check-label" for="merchant_payupi">Hide</label>
</div>
</th>
</tr>';

$html .= '</thead></table>';
$output = array("status"=>true,"message"=>"Data Fetched Successfully","html"=>$html);

}else if($merchant['merchant_name']=="SBI Merchant"){
    
$merchant_data = json_decode($merchant['merchant_data'],true);    
$html = '<h4 class="mt-3"><b>UPI Merchant Details</b></h4>';
$html .= '
<table class="table table-bordered mt-4 mb-0"> 
<thead>';

$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant MID</small><br>'.$merchant_data['Result'][0]['MID'].'</th>
</tr>';

$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant TID</small><br>'.$merchant_data['Result'][0]['FinalTID'].'</th>
</tr>';


$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant UPI Name</small><br>'.$merchant_data['Result'][0]['MName'].'</th>
</tr>';

$html .= '
<tr>
<th colspan="2"><small class="f-400">Merchant UPI ID</small><br>'.$merchant['merchant_upi'].'</th>
</tr>';

$html .= '
<tr>
<th><small class="f-400">Merchant Primary</small><br>
<div class="form-check form-switch d-flex flex-row pt-2 p-0 justify-content-center">
<label class="form-check-label" for="merchant_primary">InActive</label>
<input class="mx-2 form-check-input" id="merchant_primary" type="checkbox" value="Active" name="merchant_primary" onchange="set_merchant_primary(\''.$merchant['merchant_id'].'\',\'#merchant_primary\')" '.$merchant_primary.'>
<label class="form-check-label" for="merchant_primary">Active</label>
</div>
</th>

<th><small class="f-400">Pay via UPI Button</small><br>
<div class="form-check form-switch d-flex flex-row pt-2 p-0 justify-content-center">
<label class="form-check-label" for="merchant_payupi">Show</label>
<input class="mx-2 form-check-input" id="merchant_payupi" type="checkbox" value="Show" name="merchant_payupi" onchange="set_merchant_payupi(\''.$merchant['merchant_id'].'\',\'#merchant_payupi\')" '.$merchant_payupi.'>
<label class="form-check-label" for="merchant_payupi">Hide</label>
</div>
</th>
</tr>';


$html .= '</thead></table>';
$output = array("status"=>true,"message"=>"Data Fetched Successfully","html"=>$html);
}else{
$output = array("status"=>false,"message"=>"Merchant Not Selected","html"=>"");
}

}else{
$output = array("status"=>false,"message"=>"Merchant is InActive","html"=>"");
}

}else{
$output = array("status"=>false,"message"=>"Merchant is Not Available","html"=>"");
}

}else{
$output = array("status"=>false,"message"=>"Merchant is Not Valid","html"=>"");
}


header('Content-Type: application/json');
echo json_encode($output);
}


if(isset($_POST['set_merchant_primary']) && !empty($_POST['merchant_id']) && !empty($_POST['merchant_primary'])){
$merchant_id = safe_str($_POST['merchant_id']);
$merchant_primary = safe_str($_POST['merchant_primary']);
if($merchant_id>0 && is_numeric($merchant_id)){

if(in_array($merchant_primary,["Active","InActive"])){

$merchant = rechpay_fetch(rechpay_query("SELECT * FROM `merchant` WHERE user_id='".$userAccount['id']."' and merchant_id='".$merchant_id."' "));
if($merchant['merchant_id']==$merchant_id){
    
if($merchant['status']=="Active"){
$sql = "UPDATE `merchant` SET `merchant_primary`='".$merchant_primary."' WHERE merchant_id='".$merchant['merchant_id']."' ";	
if(rechpay_query($sql)){
    
$sql = "UPDATE `merchant` SET `merchant_primary`='InActive' WHERE user_id='".$userAccount['id']."' and merchant_id!='".$merchant['merchant_id']."'  ";	
if(rechpay_query($sql)){
$output = array("status"=>true,"message"=>"Settings Update Successfully");
}else{
$output = array("status"=>false,"message"=>"Server Error");
}

}else{
$output = array("status"=>false,"message"=>"Server Error");
}

}else{
$output = array("status"=>false,"message"=>"Merchant is InActive");
}

}else{
$output = array("status"=>false,"message"=>"Merchant is Not Available");
}

}else{
$output = array("status"=>false,"message"=>"Something Error");
}

}else{
$output = array("status"=>false,"message"=>"Merchant is Not Valid");
}


header('Content-Type: application/json');
echo json_encode($output);
}



if(isset($_POST['set_merchant_payupi']) && !empty($_POST['merchant_id']) && !empty($_POST['merchant_payupi'])){
$merchant_id = safe_str($_POST['merchant_id']);
$merchant_payupi = safe_str($_POST['merchant_payupi']);
if($merchant_id>0 && is_numeric($merchant_id)){

if(in_array($merchant_payupi,["Show","Hide"])){

$merchant = rechpay_fetch(rechpay_query("SELECT * FROM `merchant` WHERE user_id='".$userAccount['id']."' and merchant_id='".$merchant_id."' "));
if($merchant['merchant_id']==$merchant_id){
    
if($merchant['status']=="Active"){
    
$sql = "UPDATE `merchant` SET `merchant_payupi`='".$merchant_payupi."' WHERE merchant_id='".$merchant['merchant_id']."' ";	
if(rechpay_query($sql)){
$output = array("status"=>true,"message"=>"Settings Update Successfully");
}else{
$output = array("status"=>false,"message"=>"Server Error");
}

}else{
$output = array("status"=>false,"message"=>"Merchant is InActive");
}

}else{
$output = array("status"=>false,"message"=>"Merchant is Not Available");
}

}else{
$output = array("status"=>false,"message"=>"Something Error");
}

}else{
$output = array("status"=>false,"message"=>"Merchant is Not Valid");
}


header('Content-Type: application/json');
echo json_encode($output);
}


}



