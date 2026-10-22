<?php
require_once("database.components.php");
require_once(".paytm_csrf_token.php");

// ini_set("display_errors",true);
// error_reporting(E_ALL);

function error_page($title,$error){
echo '<link rel="stylesheet" href="//'.$_SERVER['SERVER_NAME'].'/merchant/error/error.css"/>
<title>'.$title.'</title>
<div id="container">
<h1>'.$title.'</h1>
<p>'.$error.'</p>	
</div>
';
exit();
}

function api_token_gen(){ 
return implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6));
}

function awal_alert_msg($msg,$type='primary'){
echo '
<script src="assets/js/core/jquery.3.2.1.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
Swal.fire("'.$msg.'", "", "'.$type.'");
});
</script>
';
}

function alert_msg($msg,$type='primary'){
echo '<div class="alert alert-'.$type.'">
<span data-notify="icon" class="la la-bell"></span>
<button type="button" class="close" data-dismiss="alert">x</button>
'.$msg.'
</div>';
}


function data_table($value='#dataTable'){
echo '
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css"/>
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function () {
    $("'.$value.'").DataTable();
});
</script>
';
}
function datepicker($value='.datepicker'){
echo '
<link href="https://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" ></script>
<script>
$(document).ready(function () {
$( "'.$value.'" ).datepicker({
  dateFormat: "dd-mm-yy"
});
});
</script>
';
}


function form_create($method,$action,$StringArray,$time=0,$submit=false){
$StringArray =  json_encode($StringArray);
echo '
<script>
setTimeout(function(){ 
var StringArray = JSON.parse(\''.$StringArray.'\');
form_create("'.$method.'","'.$action.'",StringArray,'.$submit.');
}, '.$time.');</script>
';
}

function sdk_response($txnStatus, $orderId, $txnId){
echo '
<script src="//'.$_SERVER['SERVER_NAME'].'/merchant/assets/js/android.js"></script>
<script>
sdk_response("'.$txnStatus.'", "'.$orderId.'", "'.$txnId.'");
</script>
';
}

function sdk_error($errorMessage){
echo '
<script src="//'.$_SERVER['SERVER_NAME'].'/merchant/assets/js/android.js"></script>
<script>
sdk_error("'.$errorMessage.'");
</script>
';
}

function upi_qr_code($intent,$array){
$param = http_build_query($array);
$qrIntent = "$intent://pay?$param";
$data = urldecode($qrIntent);
require_once("../merchant/components/qrcode.components/qrlib.php");
$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
//$PNG_WEB_DIR = 'tmp/';
if (!file_exists($PNG_TEMP_DIR))
    mkdir($PNG_TEMP_DIR);
$filename = $PNG_TEMP_DIR.rand().'.png';
QRcode::png($data, $filename, "H", 15, 2);
$qrcode = imgbase64(file_get_contents($filename));
unlink($filename);
return array("qrCode"=>$qrcode,"qrIntent"=>urldecode($qrIntent));
}


function imgbase64($image){  
if ($image !== false){
    return 'data:image/jpg;base64,'.base64_encode($image);
} 
}

function _get($key){
return str_replace(['.php'," "],['','+'],$_GET[$key]);  
}

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function getDomain($url){
    $pieces = parse_url($url);
    $domain = isset($pieces['host']) ? $pieces['host'] : '';
    if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)){
        return $regs['domain'];
    }
    return FALSE;
}

function user_os(){
    $iPod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
    $iPhone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
    $iPad = strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
    $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
    if($iPad||$iPhone||$iPod){
        return 'ios';
    }else if($android){
        return 'android';
    }else{
        return 'pc';
    }
}

function _ago($tm,$rcs = 0) {
$cur_tm = time(); 
$dif = $cur_tm-$tm;
$pds = array('second','minute','hour','day','week','month','year','decade');
$lngh = array(1,60,3600,86400,604800,2630880,31570560,315705600);

for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);
    $no = floor($no);
    if($no <> 1)
        $pds[$v] .='s';
    $x = sprintf("%d %s ",$no,$pds[$v]);
    if(($rcs == 1)&&($v >= 1)&&(($cur_tm-$_tm) > 0))
        $x .= time_ago($_tm);
    return $x;
}

function current_timestamp(){
    return date("Y-m-d H:i:s");
}


function generateNumericOTP($n) { 
      
    // Take a generator string which consist of 
    // all numeric digits 
    $generator = "1357902468"; 
  
    // Iterate for n-times and pick a single character 
    // from generator and append it to $result 
      
    // Login for generating a random character from generator 
    //     ---generate a random number 
    //     ---take modulus of same with length of generator (say i) 
    //     ---append the character at place (i) from generator to result 
  
    $result = ""; 
  
    for ($i = 1; $i <= $n; $i++) { 
        $result .= substr($generator, (rand()%(strlen($generator))), 1); 
    } 
  
    // Return result 
    return $result; 
} 

function order_txn_id($fs=''){
 return $fs.date("ymdHis").generateNumericOTP(8);   
}

function safe_str($value){
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a", "-","+","=");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z", "","","");
    return strip_tags(str_replace($search, $replace, $value));
}

function str_escape($value){
    $connection = db();
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"',  '_');
    $replace = array("","","", "", '\"', "\'"," ");
    return $connection->quote(strip_tags(str_replace($search, $replace, $value)));
}


function real_escape_string($value){
    $connection = db();
    return $connection->quote($value);
}

function StrReplace($str,$fnd,$rep){
    
return str_replace($fnd,$rep,$str);   
    
}

function safe_encrypt($string_to_encrypt,$password){
return openssl_encrypt($string_to_encrypt,"AES-128-ECB",$password);
}


function safe_decrypt($encrypted_string,$password){
return openssl_decrypt($encrypted_string,"AES-128-ECB",$password);
}

function curl_request($mathod=null,$url,$postData,$header=array(),$hreturn=0,$cookie=false,$cookieType='w',$timeout=0,$ssl=false){
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => $timeout,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_HTTPHEADER => $header,
));

if(!empty($postData)){
curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
}


if($hreturn==true){
curl_setopt($curl, CURLOPT_HEADER, $hreturn);
}

if(!empty($mathod)){
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $mathod);
}

if(!empty($cookie) && $cookieType="w"){
unlink("components/tmp/$cookie.txt");    
curl_setopt($curl, CURLOPT_COOKIEJAR, "components/tmp/$cookie.txt");
curl_setopt($curl, CURLOPT_COOKIEFILE,"components/tmp/$cookie.txt");
}

if(!empty($cookie) && $cookieType="r"){
curl_setopt($curl, CURLOPT_COOKIEFILE,"components/tmp/$cookie.txt");
//curl_setopt($curl, CURLOPT_COOKIEJAR, "components/tmp/$cookie.txt");
}

if($ssl=true){
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);    
}

$response = curl_exec($curl);
curl_close($curl);
return $response;
}


function get_headers_from_curl_response($response) {
   
    $body = substr($response, strpos($response, "\r\n\r\n"));
    
    $headers = array();

    $header_text = substr($response, 0, strpos($response, "\r\n\r\n"));
    foreach (explode("\r\n", $header_text) as $i => $line)
        if ($i === 0)
            $headers['http_code'] = $line;
        else
        {
            list ($key, $value) = explode(': ', $line);

            $headers[$key] = $value;
        }
        
preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
$cookies = array();
foreach($matches[1] as $item) {
parse_str($item, $cookie);
$cookies = array_merge($cookies, $cookie);
}
     
    return array("headers"=>$headers,"body"=>$body,"cookies"=>$cookies);
}

function get_http_code($response) {
return explode(" ",$response['headers']['http_code'])[1];
}


function send_post_webhook($url,$webhookData,$timeout=0){
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curl, CURLOPT_POSTFIELDS, $webhookData);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($curl);
$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);	
return $httpcode;
}


function rechpay_query($sql){
$connection = db();
try {
$result = $connection->query($sql);
$connection = null;
 return $result;
} catch (PDOException $e ) {
error_page("SQL Query Exception",$e->getMessage());
}
}

function rechpay_insert($sql){
$connection = db();
try {
$connection->query($sql);
$insert_id = $connection->lastInsertId();
$connection = null;
 return $insert_id;
} catch (PDOException $e ) {
error_page("SQL Query Exception",$e->getMessage());
}
}

function rechpay_fetch($query){
$res = $query->fetch(PDO::FETCH_ASSOC);   
 return !empty($res) ? $res : array();
}

function rechpay_fetch_all($query){
 $res = $query->fetchALL(PDO::FETCH_ASSOC);   
 return !empty($res) ? $res : array();
}

function pt($value){
	echo "<pre>";
	print_r($value);
	echo "</pre>";
}

function redirect($url,$time){
echo '<script>
setTimeout(
function(){
window.location = "'.$url.'" 
},'.$time.');
</script>';
}

function baseurl(){
 return str_replace("www.","",$_SERVER['SERVER_NAME']);
}

function site_data(){
  return rechpay_fetch(rechpay_query("SELECT * FROM `siteconfig` WHERE baseurl='".baseurl()."' "));
}

function gateway($type){
  return json_decode(site_data()['gateway'],true)[$type];
}


function send_sms($number,$message){
$smsapi_url = site_data()['smsapi_url'];    
$message = urlencode($message);  
$find  = array("{NUMBER}","{MSG}");
$repls  = array($number,$message);
$url = StrReplace($smsapi_url,$find,$repls);
$response = file_get_contents($url);
return $response;
}



function merchant_accounts($userAccount){ 
$sql = "SELECT * FROM `merchant` WHERE user_id='".$userAccount['user_id']."' ORDER BY `merchant_id` DESC ";   
if($userAccount['role']=="Admin"){
$sql = "SELECT * FROM `merchant` ORDER BY `merchant_id` DESC "; 
}
return rechpay_fetch_all(rechpay_query($sql));
}



function merchant_authorization($merchant_id,$userAccount){
  return rechpay_fetch(rechpay_query("SELECT * FROM `merchant` WHERE user_id='".$userAccount['user_id']."' and merchant_id='".$merchant_id."' "));
}


function all_user_account(){
  return rechpay_fetch_all(rechpay_query("SELECT * FROM `useraccount` ORDER BY `user_id` DESC "));
}


function get_upi_validation($upi_id){
$response = curl_request("POST","https://upibankvalidator.com/api/upiValidation?upi=$upi_id",json_decode(["upi"=>$upi_id]),array(),true);
$response = get_headers_from_curl_response($response);
$body = json_decode($response['body'],true);
$output = array("success"=>false,"message"=>"Server is down","name"=>array());
if($body['isUpiRegistered']==true){
$output = array("success"=>true,"message"=>"Data Fetched Successfully","name"=>$body['name']);   
}

if($body['isUpiRegistered']!=true && !empty($body['message'])){
$output['message'] = $body['message'];  
}

return $output;
}


function get_rand_ip(){
$z=rand(1,240);
$x=rand(1,240);
$c=rand(1,240);
$v=rand(1,240);
$ip = $z.".".$x.".".$c.".".$v;    
return $ip;
}

function RandomStriing($length) {
    $keys = array_merge(range('9', '0'), range('a', 'f'));
    for($i=0; $i < $length; $i++) {
        $key .= $keys[array_rand($keys)];
    }
    return $key;
}

function RandomNumber($length){
$str="";
for($i=0;$i<$length;$i++){
$str.=mt_rand(0,9);
}
return $str;
}

function get_farm_gen(){
$mom = RandomStriing(16);
$mon = RandomStriing(64);
$mo = RandomStriing(4);
$ma = RandomStriing(12);
$mb = RandomStriing(8);
$mc = RandomStriing(4);
$md = RandomStriing(4);
$me = RandomStriing(4);
$mf = RandomStriing(12);
$mg = RandomStriing(8);
$mh = RandomStriing(4);
$mi = RandomStriing(4);
$mj = RandomStriing(11);
$mk = RandomStriing(35);
$farm="$mb-$md-$mc-$mh-$mf";
$sfarm="$mg-$mh-$mi-$me-$ma";
return array("farm"=>$farm,"sfarm"=>$sfarm);
}



function get_sbimerchant_validation($merchant_username){
$ip = get_rand_ip();    
$postData = array();
$postData['UserID'] = $merchant_username;
$postData['UUID'] = $merchant_username;
$postData['version'] = $sbiversion;
$postData['lang'] = "en";
$postData = json_encode($postData);
$lenth = strlen($postData);

$url = "https://merchantapp.hitachi-payments.com/YMAVOLBP/MercMobAppResAPI/RestService.svc/UserValidation";
$headers = array(
    "Content-Length: $lenth",
    "Content-Type: application/json",
    "user-agent: okhttp/3.12.13",
    "X-Forwarded-For: $ip"
);

$response = curl_request("POST",$url,$postData,$headers,false,false,false,0,true);
return json_decode($response,true);
}


function get_sbimerchant_login($merchant_username,$merchant_password){
$s1= substr(hash('sha256', RandomNumber(13)),0,32);
$s2= substr(hash('sha256', RandomNumber(19)),0,32);
$deviceFingerprint = RandomStriing(16).'c2RtNjM2-cWNvbQ-';
$fingerprint = "$s1.$s2.Xiaomi.".RandomStriing(64);
$ip = get_rand_ip();

$postData = array();
$postData['username'] = $merchant_username;
$postData['password'] = "$merchant_password|LOGIN";
$postData['UUID'] = $merchant_username;
$postData['mpin'] = "";
$postData['guid'] = "";
$postData['IPAddress'] = "";
$postData['MobileInfo'] = "Version.release : 11, Version.incremental : V12.5.1.0.".RandomStriing(6).", Version.sdk.number : 31, Board : Raphaelin, Bootloader : Unknown, Brand : Xiaomi, Cpu_abi : Arm64v8a, Cpu_abi2 : , Display : Rkq1.200826.002 Testkeys, Fingerprint : $fingerprint, Hardware : Qcom, Host : $deviceFingerprint, Id : Rkq1.200826.002, Manufacturer : Xiaomi, Model : Redmi 7, Product : Raphaelin, Serial : Unknown, Tags : Releasekeys, Type : User, Unknown : Unknown, User : Builder, App Version: $sbiversion";
$postData['version'] = $sbiversion;
$postData['lang'] = "en";
$postData = json_encode($postData);
$lenth = strlen($postData);

$url = "https://merchantapp.hitachi-payments.com/YMAVOLBP/MercMobAppResAPI/RestService.svc/Login";
$headers = array(
    "Content-Length: $lenth",
    "Content-Type: application/json",
    "user-agent: okhttp/3.12.13",
    "X-Forwarded-For: $ip"
);

$response = curl_request("POST",$url,$postData,$headers,false,false,false,0,true);
return json_decode($response,true);
}



function get_sbimerchant_profile($mid,$guid){
$ip = get_rand_ip();

$postData = array();
$postData['MID'] = $mid;
$postData['GUID'] = $guid;
$postData['UserName'] = $mid;
$postData = json_encode($postData);
$lenth = strlen($postData);

$url = "https://merchantapp.hitachi-payments.com/YMAVOLBP/MercMobAppResAPI/RestService.svc/GetProfileDetails";
$headers = array(
    "Content-Length: $lenth",
    "Content-Type: application/json",
    "user-agent: okhttp/3.12.13",
    "X-Forwarded-For: $ip"
);

$response = curl_request("POST",$url,$postData,$headers,false,false,false,0,true);
return $response;   
exit;
$response = json_decode($response,true);
foreach($response['Result'] as $value){
return $value;   
}

}



function get_sbimerchant_transaction($mid,$tid,$guid,$transactionId){
$ip = get_rand_ip();

$postData = array();
$postData['MerchantID'] = $mid;
$postData['TID'] = $tid;
$postData['GUID'] = $guid;
$postData['UserName'] = $mid;
$postData['lang'] = "en";
$postData = json_encode($postData);
$lenth = strlen($postData);

$url = "https://merchantapp.hitachi-payments.com/YMAVOLBP/MercMobAppResAPI/RestService.svc/GetLast7Transaction";
$headers = array(
    "Content-Length: $lenth",
    "Content-Type: application/json",
    "user-agent: okhttp/3.12.13",
    "X-Forwarded-For: $ip"
);

$response = curl_request("POST",$url,$postData,$headers,false,false,false,0,true);
$response = json_decode($response,true);
$results = $response;
foreach($response['Result'] as $row){
foreach($row as $value){
foreach($value as $k => $val){
if($val['Invoice_Number']==$transactionId){
$results = $val;    
}
}
}
}

return $results;

}

function get_paytm_otp($mobile_number,$password){
$ip = get_rand_ip();

$response = trim(paytm_csrf_token($ip));
$response = json_decode($response, true); 

if(!empty($response["csrfToken"])){
$csrfToken = $response['csrfToken'];
$postData = array();
$postData['userName'] = $mobile_number;
$postData['password'] = $password;
$postData['clientId'] = "paytm-unified-merchant-panel";
$postData['csrfToken'] = $csrfToken;
$postData = json_encode($postData);
$url = "https://accounts.paytm.com/um/authorize/proceed";

$headers = array(
    "Host: accounts.paytm.com",
    "content-type: application/json",
    "user-agent: {$_SERVER['HTTP_USER_AGENT']}",
	"X-Forwarded-For: $ip:443"
);

$response = curl_request("POST",$url,$postData,$headers); 
$response = json_decode($response,true);

}

return array_merge($response,array("csrfToken"=>$csrfToken,"ip"=>$ip));
}



function get_paytm_verify($state,$csrfToken,$otp){

$ip = get_rand_ip();
$merchant_session = "";
$merchant_csrftoken = "";
$postData = array();
$postData['otp'] = $otp;
$postData['state'] = $state;
$postData['csrfToken'] = $csrfToken;
$postData = json_encode($postData);
$url = "https://accounts.paytm.com/login/validate/otp";

$headers = array(
    "Host: accounts.paytm.com",
    "content-type: application/json",
    "user-agent: {$_SERVER['HTTP_USER_AGENT']}",
	"X-Forwarded-For: $ip:443"
);

$response = curl_request("POST",$url,$postData,$headers); 
$response = json_decode($response,true);
if(!empty($response["redirectUri"])){
$url = "https://accounts.paytm.com/um/authorize/proceed";

$headers = array(
    "Host: dashboard.paytm.com",
    "user-agent: {$_SERVER['HTTP_USER_AGENT']}",
	"X-Forwarded-For: $ip:443"
);

$response = curl_request("GET",$response["redirectUri"],array(),$headers,true); 
$response = get_headers_from_curl_response($response);
$merchant_session = $response['cookies']['SESSION'];
$merchant_csrftoken = $response['cookies']['XSRF-TOKEN'];
$response = array("status"=>"SUCCESS");
}

return array_merge($response,array("merchant_session"=>$merchant_session,"merchant_csrftoken"=>$merchant_csrftoken,"ip"=>$ip));
}



function get_paytm_qrcode($merchant_session,$merchant_csrftoken){

$ip = get_rand_ip();
$url = "https://dashboard.paytm.com/api/v1/qrcode/wallet/product/?type=all&pageNo=1&pageSize=100";

$headers = array(
    "Host: dashboard.paytm.com",
    "XSRF-TOKEN: {$merchant_csrftoken}",
    "Cookie: SESSION={$merchant_session}",
    "user-agent: {$_SERVER['HTTP_USER_AGENT']}",
	"X-Forwarded-For: $ip:443"
);

$response = curl_request("GET",$url,array(),$headers); 
$response = json_decode($response,true);
return array_merge($response,array("merchant_session"=>$merchant_session,"merchant_csrftoken"=>$merchant_csrftoken,"ip"=>$ip));
}


function get_paytm_userinfo($merchant_session,$merchant_csrftoken){

$ip = get_rand_ip();
$url = "https://dashboard.paytm.com/api/v1/context";

$headers = array(
    "Host: dashboard.paytm.com",
    "XSRF-TOKEN: {$merchant_csrftoken}",
    "Cookie: SESSION={$merchant_session}",
    "user-agent: {$_SERVER['HTTP_USER_AGENT']}",
	"X-Forwarded-For: $ip:443"
);

$response = curl_request("GET",$url,array(),$headers); 
$response = json_decode($response,true);
return array_merge($response,array("merchant_session"=>$merchant_session,"merchant_csrftoken"=>$merchant_csrftoken,"ip"=>$ip));
}




function get_paytm_transaction($merchant_session,$merchant_csrftoken,$merchantTransId){

$ip = get_rand_ip();
$postData = array();
$postData['bizTypeList'] = ["ACQUIRING"];
$postData['pageSize'] = 1;
$postData['pageNum'] = 1;
$postData['merchantTransId'] = $merchantTransId;
$postData['isSort'] = true;
$postData = json_encode($postData);
$url = "https://dashboard.paytm.com/api/v3/order/list";

$headers = array(
    "Host: dashboard.paytm.com",
    "content-type: application/json",
    "X-XSRF-TOKEN: {$merchant_csrftoken}",
    "Cookie: SESSION={$merchant_session}",
    "user-agent: {$_SERVER['HTTP_USER_AGENT']}",
	"X-Forwarded-For: $ip:443"
);

$response = curl_request("POST",$url,$postData,$headers); 
$response = json_decode($response,true);
return array_merge($response,array("merchant_session"=>$merchant_session,"merchant_csrftoken"=>$merchant_csrftoken,"ip"=>$ip));
}




function transaction_failed($transaction,$payment_mode,$customer_vpa,$utr_number){
    return rechpay_query("UPDATE `orders` SET status='FAILURE', utr='".$utr_number."' WHERE order_id='".$transaction['order_id']."' ");
}

function transaction_success($transaction,$payment_mode,$customer_vpa,$utr_number,$txn_amount){
   return rechpay_query("UPDATE `orders` SET status='SUCCESS', utr='".$utr_number."' WHERE order_id='".$transaction['order_id']."' ");
   
}
