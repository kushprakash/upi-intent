<?php


function upi_qr_code($intent,$array){
$param = http_build_query($array);
$qrIntent = "$intent://pay?$param";
$data = urldecode($qrIntent);
// require_once("../merchant/components/qrcode.components/qrlib.php");
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



function baseurl(){
 return str_replace("www.","",$_SERVER['SERVER_NAME']);
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
$response = json_decode($response,true);
foreach($response['Result'] as $value){
return $value;   
}

}


function transaction_failed($transaction,$payment_mode,$customer_vpa,$utr_number){
    return rechpay_query("UPDATE `orders` SET status='FAILURE', utr='".$utr_number."' WHERE order_id='".$transaction['order_id']."' ");
}

function transaction_success($transaction,$payment_mode,$customer_vpa,$utr_number,$txn_amount){
   return rechpay_query("UPDATE `orders` SET status='SUCCESS', utr='".$utr_number."' WHERE order_id='".$transaction['order_id']."' ");
   
}
