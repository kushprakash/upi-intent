<?php
require_once("../merchant/components/session.components.php");
require_once("../merchant/components/main.components.php");
$site_data = site_data(); 
$baseurl = $site_data['protocol'].$site_data['baseurl'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title><?=$site_data['brand']?> Payments</title>
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <link rel="icon" href="<?=$site_data['logo']?>" type="image/*" />
	<link rel="stylesheet" href="<?=$baseurl?>/merchant/assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?=$baseurl?>/merchant/assets/css/ready.css">
	<link rel="stylesheet" href="<?=$baseurl?>/merchant/assets/css/demo.css">
	<link rel="stylesheet" href="<?=$baseurl?>/merchant/assets/css/payment.css?<?=time()?>">
	<script src="<?=$baseurl?>/merchant/assets/js/payment.js?<?=time()?>"></script>
	<script src="<?=$baseurl?>/merchant/assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<meta name="theme-color" content="#1976d2">
<meta name="msapplication-navbutton-color" content="#1976d2">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="#1976d2">
<!--<link rel="stylesheet" href="https://file.objectsdata.com/common/upiwapv2/css/app.css">-->
<!--<link rel="stylesheet" href="https://file.objectsdata.com/common/upiwapv2/css/style.css?v=1">-->
<!--<link rel="stylesheet" href="https://file.objectsdata.com/common/upiwapv2/css/chunk-vendors.d6751c8d.css">-->
<link rel="stylesheet" href="app.css">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="chunk-vendors.d6751c8d.css">

<style>
   
      
    .checkout-upi-box .chk-upi-option-group .chk-upi-option .noborder{
        border: none;
        border-left: 1px solid #e3eef6;
        border-right: 1px solid #e3eef6;
    }

    .checkout-upi-box .chk-upi-option-group .chk-upi-option .topborder{
        border-top: 1px solid #e3eef6;
    }

    .checkout-upi-box .chk-upi-option-group .chk-upi-option .bottompborder{
        border-bottom: 1px solid #e3eef6;
    }

    .checkbox-part{
        float: right;
        margin-right: 1rem;
    }
    
     .footer {
    text-align: center;
    padding: 10px;
}

.powered-by {
    margin-top: 20px;
    text-align: center;
}

.powered-by p {
    display: inline-block;
    margin: 0;
    font-size: 14px;
    vertical-align: middle;
}

.logo {
    display: inline-block;
    width: 151px; /* Adjust the size according to your logo */
    vertical-align: middle;
    margin-left: 5px; /* Space between text and logo */
}
  
     
    .label-pay{
        font-size: 20px;
        width: 20px;
        height: 20px;
    }

    .click2pay{
        width: 100%;
        max-width: 375px;
        transform: translate(-50%, -50%);
        margin-left: 50%;
        left: 50%;
        position: fixed;
        bottom: -25px;
        height: 50px;
    }
    .checkout-bg{
        height: 89px;
    }

    .checkout-upi-box .chk-upi-option-group .chk-upi-option label {
        padding-top: 10px;
        padding-bottom: 10px;
        padding-left: 10px;
        width: 100%;
        height: 45px;
        border: 1px solid #e3eef6;
        cursor: pointer;
    }
    .download-button {
            display: block;
            margin-top: 10px;
            text-align: center;
            background-color: #ff0; /* Yellow background color */
            border: none;
            border-radius: 20px; /* Rounded corners */
            padding: 10px 0px; /* Padding for button */
            color: #000; /* Black text color */
            text-decoration: none; /* Remove underline */
            cursor: pointer;
        }
        .download-button:hover {
            background-color: #e0e000; /* Darker yellow on hover */
        }
    .mt-3, .my-3 {
        margin-top: 0.1rem !important;
    }
        .card-size {
            max-width: 320px;
        }
    </style>
    </head>
<body class="noselect">
<noscript>
        <strong>Your browser does not support JavaScript, please enable JavaScript before paying</strong>
</noscript>

<?php
$payment_token = $_GET["token"];

// Fetch order_id based on the token from the payment_links table
$result = rechpay_fetch(rechpay_query("SELECT order_id, created_at FROM payment_links WHERE link_token = '$payment_token'"));

if (count($result) === 0) {
    // Token not found or expired
    echo "Token not found or expired";
    exit;
}

$order_id = $result['order_id'];
$created_at = strtotime($result['created_at']);
$current_time = time();

// Check if the token has expired (more than 5 minutes)
if (($current_time - $created_at) > (5 * 60)) {
    echo "Token has expired";
    exit;
}


$transaction = rechpay_fetch(rechpay_query("SELECT * FROM `orders` WHERE order_id='".$order_id."' "));
if(count($transaction)>0 && $transaction['order_id']==$order_id){

$userAccount = rechpay_fetch(rechpay_query("SELECT * FROM `users` WHERE id='".$transaction['user_id']."' ")); 
if($userAccount['id']>0){

$callback_url = $userAccount["callback_url"];    
$user_token = $userAccount["user_token"];    

if($transaction['status']=="PENDING"){
    
$_SESSION['payment_token'] = $payment_token;
$merchant = rechpay_fetch(rechpay_query("SELECT * FROM `merchant` WHERE user_id='".$transaction['user_id']."' "));

if($merchant['merchant_id']>0 && $merchant['status']=="Active"){
$merchant_auth = get_sbimerchant_profile($merchant['merchant_username'],$merchant['merchant_session']);
// echo $merchant_auth;
// exit;
if($merchant_auth['enabled']==true || (count($merchant_auth)>0 && !empty($merchant_auth['Mobile1'])) || ($merchant_auth['statusCode']=="200" && count($merchant_auth['response'])>0)){
    
$upiArr = array();
$upiArr['pa'] = $merchant['merchant_upi'];
$upiArr['pn'] = $userAccount['company'];
$upiArr['cu'] = "INR";
$upiArr['am'] = $transaction['amount'];
$upiArr['mam'] = $transaction['amount'];

$upiArr['tr'] = $transaction['bank_orderid'];
$upiArr['tn'] = 'Payment For '.$userAccount['company'];
$upi = upi_qr_code("upi",$upiArr);   
$bhim = upi_qr_code("upi",$upiArr)['qrIntent'];
$phonepe = upi_qr_code("phonepe",$upiArr)['qrIntent'];
$paytm = upi_qr_code("paytmmp",$upiArr)['qrIntent'];
$googlePayUrl = upi_qr_code("intent",$upiArr)['qrIntent'];
?>

<script>
check_payment_status("<?=$baseurl?>","<?=$transaction['order_id']?>");
</script>

<div id="app" class="d-flex justify-content-center bd-highlight mb-3 ">
    <section class="w-100">

        <!-- Updated background color here -->
        <div class="checkout-bg custom-background p-header-top-sub-container" style="background-color: #2596be;">

            <div style="margin: 0px; padding: 20px 15px 0 15px; height: 80px; display: flex; color: #fff; justify-content: space-between;">
                <div style="display: flex; font-size: 12px; flex-direction: column; justify-content: center; text-align: left;">
                    <h6 style="font-weight: bold;">Amount Payable</h6>
                    <div>
                        <span style="font-size: 28px; margin-right: 2px; font-weight: bold;">INR</span>
                        <span style="font-size: 24px; margin-right: 2px;" id="amount"><?= $transaction['amount'] ?>.00 </span>
                    </div>
                </div> 
                <div style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                    <div>
                    

<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGAAAABgCAYAAADimHc4AAAACXBIWXMAABYlAAAWJQFJUiTwAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAQ0SURBVHgB7Z0vcxsxEMWfOwWBgYGGhYGGgoGFgfkI/Qj5GIWBhYGFgYGBhYKBhmGpdiK3npv7I+3unRzf+814PGPfne709HTSntYGCCGEEEIIIYSQNbGBM+/v71fp7Qrnx5u8NptNhCNuAqSK36W3kF4XOG/26fWUhHiBAy4CpMoP+Kj8NSEiPMHIFxhJlX+J9VW+EHJ3a8IsANZZ+QduYMQkQG7911gv21QHWxj4ChsBemLqQx9KN04XKkJ/L9z8seYmmY4tx9U2pJBeD1CidoBD668dRVzOtK0QocfkAksXFGAjoo45BfgDGwFKVAI4tP7X1EXsK/eZTYB0LjLJitCjdoHWAQE2IuqZ0wFCExdUC+A08tFc7KkLoHKBxgEBNva18ZQsehW1++Qu8RU2AiqpEsCp9UfUo2nRmpjU4i6odUCAHU0QS1OZmjBBhJ1Qs3GxAE6tXxvO1VRmtWj53N5go8oFNQ4IsBOhQ+MATbcleISZQ+mGRQI4xny0fazGAVoBrPcBodgFpQ4I8EF7cUvdAwQZCVm7ISGUbDQpgGPrj3nGqWGpUdBhVmwdjgpFLihxQIAPqr41XYRUpKYyL/K+Gjy6ISFMbTAqgHO8P0KHti8XtAK4PO9FgQumHBDggyb4dsDykF91H3AIzh0Txr4cFOBEWr9gee5qcU+ED6MuGHNAgB8WS1sccAoCCGHoi14BnFu/BN8sowqLA9Ti5VmxttvsMuiCIQd4Vb4QYWPxe8ARXqMhYdv34ZAAFut2sY4oWoyCDngK0HsdHuuCRnFYS2kRwNqQPCZko8wuwGfGMHMvhgI0hgI0ZnYBNM9zPfY9lfKnWMIBt9Bj2fffMQxBuTvMzBICXKUKqF5FnHMOPDJt5BgBleTyz8IBwq7mOWleiBvgxy4fs7T8b87lD7LkTfg2X9goeRvzuvsebgrL36J8FbaZoeXpc0xApB8WEWRm/NQNT+cLv8Z8+QZT5UtXtZux/Nj3YW+OWL5p/cC8CXd7/A92XWKB/rZh+TKh+9n3TGQwSa8yIYKM8ztV/nPfF6NZkjn1dI7+eE2MZutMpqnmycgd6i0qdnvEh/1uV7r/r6lnIcV5wkdDw6kTkQIljPt8HMxa2f4vQ11Ol+pE7Txa2OYTuegUHKcUX/v+hBBCyAHzr6UMBLmiYSXcISzRHW1U55Z5kofj2+7n1p+tsf5UgdA3W5bxs+XE+mJCcryIdmzRf60mAfhIsjEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEeqyLusWI2m809DHg4IGK9RBjxEMC0LOOTY752swB5YdIaRXjx+C8xzz9yC/hIcpszr+wUkJyBZ4//EBNc/8owJ/fJ+vk5E95aIsstX5f4FRVCCCGEEEIIIeQ8+Qv+KYyvXyOfaAAAAABJRU5ErkJggg==" alt class="storeImg" style="width: 48px; height: 48px;">
</div>
<div style="display: flex;">
<div style="display: flex;align-items: center;font-size: 17px;font-weight: normal;top: 0px;color: rgb(255, 255, 255);margin-right: 2px;flex-direction: row;align-items: center;">
<span class="title-count-down mt-3" style="width: 100%; font-size: 15px; color: #fff;  text-align: center;">
        <span id="timeout"></span>
        
    </span>
</div>

</div>
</div>
</div>

</div>

<div class="template-header">
    <div class="card-body p-0 mb-50px nb-xs">
        <div class="text-left">
            <div class="text-left payment-methods-view-container">
                <div class="checkout-upi-box">
                    <div class="chk-upi-option d-flex justify-content-center" style="margin: 1.5rem 0;">
                        <label for="phonepe-upi" class="custom-border-bottom" style="height: auto; padding-right: 10px;">
                            <div id="upi_qr_container">
                                <div style="text-align: center;">
    <img src="<?=$upi['qrCode']?>" width="230" style="max-width:100%; border-radius: 5px; margin: 0 auto;">
</div>
<h6 style="font-weight: bold; text-align: center; color: red; font-size: 10px;">Do not use same QR code to pay multiple times..</h6>
<?php if(isMobile() && $merchant['merchant_payupi']=="Show"){ ?>
<?php if(user_os()=="android"){ ?>
<a href="<?=$bhim?>" class="download-button btn btn-primary" style="padding: 5px 10px; font-size: 14px; font-weight: bold; color: white;" download="qr_code.png">Pay Using UPI App</a>
<!-- UPI Logos Section -->
<div style="margin-top: 20px; text-align: center;">
    <p style="font-size: 14px; font-weight: bold; color: #333;">Or pay using any UPI app:</p>
    <div style="display: flex; justify-content: center; gap: 15px;">
        <a href="<?=$googlePayUrl?>"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyVO9LUWF81Ov6LZR50eDNu5rNFCpkn0LwYQ&s" alt="Google Pay" style="width: 50px; transition: transform 0.3s;"></a>
        <a href="<?=$phonepe?>"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTo4x8kSTmPUq4PFzl4HNT0gObFuEhivHOFYg&s" alt="PhonePe" style="width: 50px; transition: transform 0.3s;"></a>
        <a href="<?=$paytm?>"><img src="https://cdn.icon-icons.com/icons2/730/PNG/512/paytm_icon-icons.com_62778.png" alt="Paytm" style="width: 50px; transition: transform 0.3s;"></a>
        <a href="<?=$bhim?>"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSlGaaC6rR3bpSoKXyOfHOrgeH5uD0ULZCiCA&s" alt="BHIM" style="width: 50px; transition: transform 0.3s;"></a>
        <!-- Add more logos as needed -->
    </div>
</div>



<!-- Hover Effect -->
<style>
    img:hover {
        transform: scale(1.1);
    }
</style>
<?php }else{ ?> 
<?php }?> 
<?php }?>
                            </div>
                        </label>
                    </div>
                    
<div class="footer">
     <p class="secure-payment">100% Secure Payment</p>
    <div class="powered-by">
        <p>Powered by</p>
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT2oExSwfHnW_-oJs_DurZXFwPjc6DguDvEfQ&s" alt="Logo" class="logo">
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
</div>

  

<!--button class="btn btn-secondary-outline btn-app-logo" onclick="window.location.href = '<?=$phonepe?>' "><img src="<?=$baseurl?>/merchant/assets/img/phonepe.png" alt="Pay with PhonePe"></button>
<!--button class="btn btn-secondary-outline btn-app-logo" onclick="window.location.href = '<?=$paytm?>' "><img src="<?=$baseurl?>/merchant/assets/img/paytmbank.png" alt="Pay with Paytm"></button--> 
<!--button class="btn btn-secondary-outline btn-app-logo" onclick="window.location.href = '<?=$gpay?>' "><img src="<?=$baseurl?>/merchant/assets/img/gpay.png" alt="Pay with Google Pay"></button--> 
<!--button class="btn btn-secondary-outline btn-app-logo" onclick="window.location.href = '<?=$bhim?>' "><img src="<?=$baseurl?>/merchant/assets/img/bhim.png" alt="Pay with Bhim UPI"></button--> 

</div>
</div>
</div>
</div>
<script>
countdown("timeout",005,00,window.location.href);
</script>
<?php
}else{
transaction_failed($transaction,"UPI","Merchant Login Expired",$transaction['order_id']);
redirect("",2000);    
?> 
<div class="col-md-4 card p-4">	
<div class="row">
<div class="col-md-12 text-center align-items-center pt-2">  
<div><h6><?=$userAccount['company']?><hr></h6><b>Merchant Login Expired</b></div>
<div class="text-center mt-4">
<img src="<?=$baseurl?>/merchant/assets/img/loading-money.gif" alt="chck" width="60" style="max-width:100%">  
</div>
<div class="mb-4 mt-4">
<span class="text-center font-weight-normal">Transaction ID: <?=$transaction['order_id']?></span></p> 
</div>
<div class="mb-2"><hr>
<small class="mb-4">Powered by</small><br>
<img src="<?=$baseurl?>/merchant/assets/img/Bhim-Upi-Logo-PNG.png" alt="Bhim" height="20px" style="max-width:100%"> 
</div>
</div>
</div>
</div> 
<?php    
}

}else{
transaction_failed($transaction,"UPI","Merchant Not Active",$transaction['order_id']);
redirect("",2000);
?> 
<div class="col-md-4 card p-4">	
<div class="row">
<div class="col-md-12 text-center align-items-center pt-2">  
<div><h6><?=$userAccount['company']?><hr></h6><b>Merchant Not Active</b></div>
<div class="text-center mt-4">
<img src="<?=$baseurl?>/merchant/assets/img/loading-money.gif" alt="chck" width="60" style="max-width:100%">  
</div>
<div class="mb-4 mt-4">
<span class="text-center font-weight-normal">Transaction ID: <?=$transaction['order_id']?></span></p> 
</div>
<div class="mb-2"><hr>
<small class="mb-4">Powered by</small><br>
<img src="<?=$baseurl?>/merchant/assets/img/Bhim-Upi-Logo-PNG.png" alt="Bhim" height="20px" style="max-width:100%"> 
</div>
</div>
</div>
</div>    
<?php    
}

}else if($transaction['status']=="SUCCESS"){
?> 
<?php
if(empty($transaction['redirect_url'])){
    
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
    
sdk_response($transaction['status'], $transaction['order_id'], $transaction['order_id']);   
?>
<div class="col-md-4 card p-4">	
<div class="row">
<div class="col-md-12 text-center align-items-center pt-2">  
<div><h6><?=$userAccount['company']?><hr></h6>Payment Completed</b></div>
<div class="text-center mt-4">
<img src="<?=$baseurl?>/merchant/assets/img/success-icon.svg" alt="chck" width="80" style="max-width:100%"> 
</div>
<div class="mb-4 mt-4">
<span class="text-center font-weight-normal">You have successfully paid ₹<?=$transaction['txn_amount']?><br>Paid Using: <?=$transaction['payment_mode']?><br>Transaction ID: <?=$transaction['order_id']?></span></p> 
</div>
<div class="mb-2"><hr>
<small class="mb-4">Powered by</small><br>
<img src="<?=$baseurl?>/merchant/assets/img/Bhim-Upi-Logo-PNG.png" alt="Bhim" height="20px" style="max-width:100%"> 
</div>
</div>
</div>
</div>
<?php
}else{

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
    
$InputArray = array();
$InputArray['status'] = true;
$InputArray['message'] = "Transaction Successfully"; 
$InputArray['order_id'] = $transaction['order_id']; 
$InputArray['token'] = $user_token; 
form_create("GET",$transaction['redirect_url'],$InputArray,2000,true);

?>
<div class="col-md-4 card p-4">	
<div class="row">
<div class="col-md-12 text-center align-items-center pt-2">  
<div><h6><?=$userAccount['company']?><hr></h6><b>Processing your payment...</b></div>
<div class="text-center mt-4">
<img src="<?=$baseurl?>/merchant/assets/img/loading-money.gif" alt="chck" width="60" style="max-width:100%"> 
</div>
<div class="mb-4 mt-4">
<span class="text-center font-weight-normal">Please do not refresh the page because we are processing your payment.</span></p>  
</div>
<div class="mb-2"><hr>
<small class="mb-4">Powered by</small><br>
<img src="<?=$baseurl?>/merchant/assets/img/Bhim-Upi-Logo-PNG.png" alt="Bhim" height="20px" style="max-width:100%"> 
</div>
</div>
</div>
</div>
<?php    
}
?>

<?php    
}else{
?> 


<?php
if(empty($transaction['redirect_url'])){
sdk_response($transaction['status'], $transaction['order_id'], $transaction['order_id']);    
?>
<div class="col-md-4 card p-4">	
<div class="row">
<div class="col-md-12 text-center align-items-center pt-2">  
<div><h6><?=$userAccount['company']?><hr></h6><b>Payment Failed</b></div>
<div class="text-center mt-4">
<img src="<?=$baseurl?>/merchant/assets/img/failed-icon.svg" alt="chck" width="60" style="max-width:100%"> 
</div>
<div class="mb-4 mt-4">
<span class="text-center font-weight-normal">Transaction ID: <?=$transaction['order_id']?></span></p> 
</div>
<div class="mb-2"><hr>
<small class="mb-4">Powered by</small><br>
<img src="<?=$baseurl?>/merchant/assets/img/Bhim-Upi-Logo-PNG.png" alt="Bhim" height="20px" style="max-width:100%"> 
</div>
</div>
</div>
</div>
<?php
}else{

// Data to be sent
$postData = array(
    'status' => 'FAILED',
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
    
$InputArray = array();
$InputArray['status'] = false;
$InputArray['message'] = "Transaction Failed"; 
$InputArray['order_id'] = $transaction['order_id']; 
$InputArray['token'] = $user_token; 
form_create("GET",$transaction['redirect_url'],$InputArray,2000,true);
?>
<div class="col-md-4 card p-4">	
<div class="row">
<div class="col-md-12 text-center align-items-center pt-2">  
<div><h6><?=$userAccount['company']?><hr></h6><b>Processing your payment...</b></div>
<div class="text-center mt-4">
<img src="<?=$baseurl?>/merchant/assets/img/loading-money.gif" alt="chck" width="60" style="max-width:100%"> 
</div>
<div class="mb-4 mt-4">
<span class="text-center font-weight-normal">Please do not refresh the page because we are processing your payment.</span></p>  
</div>
<div class="mb-2"><hr>
<small class="mb-4">Powered by</small><br>
<img src="<?=$baseurl?>/merchant/assets/img/Bhim-Upi-Logo-PNG.png" alt="Bhim" height="20px" style="max-width:100%"> 
</div>
</div>
</div>
</div>
<?php    
}
?>

<?php   
}

}else{
transaction_failed($transaction,"UPI","Merchant Not Active",$transaction['order_id']);
?>
<div class="col-md-4 card p-4">	
<div class="row">
<div class="col-md-12 text-center align-items-center">  
<img src="<?=$baseurl?>/merchant/assets/img/unauthorized-blue.svg" width="200" style="max-width:100%">
<div class="mb-4">
<h5 class="f-20">Unauthorized Access</h5>
</div>
<div class="">
<button class="btn btn-outline-danger btn-sm" onclick="history.back()">Go Back</button>    
</div>
</div>
</div>
</div>
<?php    
}

}else{
sdk_error("Payment Link Not Found");     
?>
<div class="col-md-4 card p-4">	
<div class="row">
<div class="col-md-12 text-center align-items-center">  
<img src="<?=$baseurl?>/merchant/assets/img/page-not-found-5-530376.webp" width="200" style="max-width:100%">
<div class="mb-4">
<h5 class="f-20">Payment Link Not Found.</h5>
</div>
<div class="">
<button class="btn btn-outline-danger btn-sm" onclick="history.back()">Go Back</button>    
</div>
</div>
</div>
</div>
<?php
}
?>

</div> 
</div>
</div>

<script>
    function openUpiIntent(url) {
        // You can perform additional actions if needed before opening the URL
        window.location.href = url; // This will navigate to the specified URL
    }
    
   

</script>
<?php include "../Qrcode/security.php"; ?>
</body>
</html>