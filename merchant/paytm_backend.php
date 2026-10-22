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
$sql = "SELECT * FROM `paytm_tokens` WHERE id='$merchant_id'";
if($userAccount['role']=="Admin"){
$sql = "SELECT * FROM `paytm_tokens` WHERE id='$merchant_id'";
}
$merchant = rechpay_fetch(rechpay_query($sql));
if($merchant['id']>0 && $merchant['id']==$merchant_id){
    
$response =  get_paytm_otp($_POST['username'],$_POST['password']);

if($response['status']=="SUCCESS"){
   
$html = '<h4 class="mt-2"><b>Paytm OTP Verification</b></h4><hr>';
$html .= '
<form method="POST" action="" class="mt-2 text-left mb-3" autocomplete="off">
    <div class="mb-2"><input type="text" name="otp" id="otp" placeholder="One Time Password" class="form-control form-control-sm" onkeypress="if(this.value.length==6) return false;" required="" /></div>
    <div class="mb-2">
        <input type="hidden" name="merchant_csrftoken" id="merchant_csrftoken" value="'.$response['csrfToken'].'" class="form-control" required="" /> 
        <input type="hidden" name="merchant_session" id="merchant_session" value="'.$response['stateCode'].'" class="form-control" required="" /> 
        <input type="hidden" name="ip" id="ip" value="'.$response['ip'].'" class="form-control" required="" /> 
        <input type="hidden" name="merchant_id" id="merchant_id" value="'.$merchant['merchant_id'].'" class="form-control" required="" /> 
        <button type="button" name="verify_otp" id="verify_otp" onclick="get_verify_otp($(\'#merchant_id\').val(),$(\'#ip\').val(),$(\'#otp\').val(),$(\'#merchant_csrftoken\').val(),$(\'#merchant_session\').val())" class="btn btn-success btn-sm btn-block mt-2">Verify <i class="la la-key"></i></button>
    </div>
</form>
';
$html .= '<hr><small class="text-danger">Company is not responsible for any kind of loss.<br> Please check your URL before OTP Verify 👍</small>';

$output = array("status"=>true,"message"=>"OTP Sent Successfully","html"=>$html); 

}else{
$output = array("status"=>false,"message"=>$response['message'],"html"=>"");     
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

$merchant = rechpay_fetch(rechpay_query("SELECT * FROM `paytm_tokens` WHERE id='$merchant_id'"));
if($merchant['id']==$merchant_id){
	
$results = get_paytm_verify($_POST['merchant_session'],$_POST['merchant_csrftoken'],$otp);
if($results['status']=="SUCCESS"){
$paytm_qrcode = get_paytm_qrcode($results['merchant_session'],$results['merchant_csrftoken']);
// echo json_encode($results).' ----- '.json_encode($paytm_qrcode);
// exit;
if($paytm_qrcode['statusCode']=="200" && count($paytm_qrcode['response'])>0){
$merchant_data = json_encode($paytm_qrcode);  
$resData =  $paytm_qrcode['response'][0];
parse_str($resData['deepLink'], $upiData);
$merchant_upi = $upiData['upi://pay?pa'];
$mid = $resData['mappingId'];
$sql = "UPDATE paytm_tokens SET MID='$mid', Upiid='$merchant_upi', status='Active', user_id='".$userAccount['id']."' WHERE user_token='".$userAccount['user_token']."' AND id = '$merchant_id'";
if(rechpay_query($sql)){

$pcnsql = "UPDATE users SET paytm_connected='Yes' WHERE user_token='".$userAccount['user_token']."'";
rechpay_query($pcnsql);

$html = '<h4 class="mt-3"><b>UPI Merchant Details</b></h4>';
$html .= '
<table class="table table-bordered mt-4 mb-0"> 
<thead>';

$html .= '
<tr>
<th><small class="f-400">Mobile Number</small><br>'.$merchant['phoneNumber'].'</th>
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


$html .= '</thead></table>';
$output = array("status"=>true,"message"=>"OTP Verified Successfully","html"=>$html);
}else{
$output = array("status"=>false,"message"=>"Server Error","html"=>"");	
}
	

}else{
$output = array("status"=>false,"message"=>$paytm_qrcode['message'],"html"=>"qr");	
}	
	
}else{
$output = array("status"=>false,"message"=>$results['message'],"html"=>"verify");	
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


}

?>

