

<?php
include "../Qrcode/security.php";
require_once("../merchant/components/session.components.php");
require_once("../merchant/components/main.components.php");
$site_data = site_data(); 
$baseurl = $site_data['protocol'].$site_data['baseurl'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$site_data['brand']?> Payments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?=$baseurl?>/merchant/assets/css/payment.css?<?=time()?>">
	<script src="<?=$baseurl?>/merchant/assets/js/payment.js?<?=time()?>"></script>
	<script src="<?=$baseurl?>/merchant/assets/js/core/jquery.3.2.1.min.js"></script>
	<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .payment-container {
            max-width: 400px;
            margin: 15px auto;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            
        }

        .header-section {
            background-color: #2596be;
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-section img {
            height: 30px;
        }

        .qr-code img {
            width: 150px;
            height: 150px;
        }

        .timer {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 20px;
            text-align: center;
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
    width: 67px; /* Adjust the size according to your logo */
    vertical-align: middle;
    margin-left: 5px; /* Space between text and logo */
}
  
        

        .payment-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            transition: background-color 0.3s ease;
        }

        .payment-option:hover {
            background-color: #e9ecef;
        }

        .payment-option img {
            width: 30px;
            height: 30px;
        }

        .footer-text {
            font-size: 12px;
            text-align: center;
            margin-top: 20px;
            color: gray;
        }
        
        a {
    text-decoration: none;
    color: #212529;
    font-weight: 600;
}
    </style>
</head>
<body>
    
    
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


$paytmintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$cxrbytectxnref&tr=$cxrbytectxnref&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

?>

<script>
check_payment_status("<?=$baseurl?>","<?=$transaction['order_id']?>");
</script>


<div class="payment-container">
    <!-- Header Section -->
    <div class="header-section">
        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGAAAABgCAYAAADimHc4AAAACXBIWXMAABYlAAAWJQFJUiTwAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAQ0SURBVHgB7Z0vcxsxEMWfOwWBgYGGhYGGgoGFgfkI/Qj5GIWBhYGFgYGBhYKBhmGpdiK3npv7I+3unRzf+814PGPfne709HTSntYGCCGEEEIIIYSQNbGBM+/v71fp7Qrnx5u8NptNhCNuAqSK36W3kF4XOG/26fWUhHiBAy4CpMoP+Kj8NSEiPMHIFxhJlX+J9VW+EHJ3a8IsANZZ+QduYMQkQG7911gv21QHWxj4ChsBemLqQx9KN04XKkJ/L9z8seYmmY4tx9U2pJBeD1CidoBD668dRVzOtK0QocfkAksXFGAjoo45BfgDGwFKVAI4tP7X1EXsK/eZTYB0LjLJitCjdoHWAQE2IuqZ0wFCExdUC+A08tFc7KkLoHKBxgEBNva18ZQsehW1++Qu8RU2AiqpEsCp9UfUo2nRmpjU4i6odUCAHU0QS1OZmjBBhJ1Qs3GxAE6tXxvO1VRmtWj53N5go8oFNQ4IsBOhQ+MATbcleISZQ+mGRQI4xny0fazGAVoBrPcBodgFpQ4I8EF7cUvdAwQZCVm7ISGUbDQpgGPrj3nGqWGpUdBhVmwdjgpFLihxQIAPqr41XYRUpKYyL/K+Gjy6ISFMbTAqgHO8P0KHti8XtAK4PO9FgQumHBDggyb4dsDykF91H3AIzh0Txr4cFOBEWr9gee5qcU+ED6MuGHNAgB8WS1sccAoCCGHoi14BnFu/BN8sowqLA9Ti5VmxttvsMuiCIQd4Vb4QYWPxe8ARXqMhYdv34ZAAFut2sY4oWoyCDngK0HsdHuuCRnFYS2kRwNqQPCZko8wuwGfGMHMvhgI0hgI0ZnYBNM9zPfY9lfKnWMIBt9Bj2fffMQxBuTvMzBICXKUKqF5FnHMOPDJt5BgBleTyz8IBwq7mOWleiBvgxy4fs7T8b87lD7LkTfg2X9goeRvzuvsebgrL36J8FbaZoeXpc0xApB8WEWRm/NQNT+cLv8Z8+QZT5UtXtZux/Nj3YW+OWL5p/cC8CXd7/A92XWKB/rZh+TKh+9n3TGQwSa8yIYKM8ztV/nPfF6NZkjn1dI7+eE2MZutMpqnmycgd6i0qdnvEh/1uV7r/r6lnIcV5wkdDw6kTkQIljPt8HMxa2f4vQ11Ol+pE7Txa2OYTuegUHKcUX/v+hBBCyAHzr6UMBLmiYSXcISzRHW1U55Z5kofj2+7n1p+tsf5UgdA3W5bxs+XE+mJCcryIdmzRf60mAfhIsjEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEUoDEeqyLusWI2m809DHg4IGK9RBjxEMC0LOOTY752swB5YdIaRXjx+C8xzz9yC/hIcpszr+wUkJyBZ4//EBNc/8owJ/fJ+vk5E95aIsstX5f4FRVCCCGEEEIIIeQ8+Qv+KYyvXyOfaAAAAABJRU5ErkJggg==" alt="Logo"> <!-- Replace with actual logo image URL -->
        <span> Amount : ₹<?= $transaction['amount'] ?>.00</span>
    </div>

    <!-- Main Section: QR Code and Timer -->
    <div class="text-center qr-code mt-3">
        
        <img src="<?=$upi['qrCode']?>" alt="QR Code"> <!-- Replace with actual QR code image URL -->
    </div>
    <div id="timeout" class="timer">04:56</div>
    <!--<span id="timeout"></span>-->

    <!-- Add this CSS to hide payment options on desktop/laptop and show them only on mobile -->
<style>
    /* Hide payment options on screens larger than 768px (laptops/desktops) */
    @media only screen and (min-width: 768px) {
        .payment-option {
            display: none;
        }
    }

    /* Show payment options on screens smaller than 768px (mobile devices) */
    @media only screen and (max-width: 768px) {
        .payment-option {
            display: flex;
        }
    }
</style>

<!-- Payment Options -->
    <a href="<?= $phonepe ?>">
<div class="payment-option">
    <div>
        <img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/phonepe-circle.svg" alt="PhonePe UPI"> <!-- Replace with PhonePe icon -->
        <span class="ms-2">PhonePe UPI</span>
        </div>
    <span><img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/arrow.svg" alt="Other UPI"></span>
</div>
    </a>

    <a href="<?= $paytm ?>">
<div class="payment-option">
    <div>
        <img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/paytm.png" alt="Paytm UPI"> <!-- Replace with Paytm icon -->
        <span class="ms-2">Paytm UPI</span>
        </div>
    <span><img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/arrow.svg" alt="Other UPI"></span>
</div>
    </a>

    <a href="<?= $googlePayUrl ?>#Intent;scheme=upi;package=com.google.android.apps.nbu.paisa.user;end">
<div class="payment-option">
    <div>
        <img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/googlepay-circle.svg" alt="Google Pay UPI"> <!-- Replace with Google Pay icon -->
        <span class="ms-2">Google Pay UPI</span>
        </div>
    <span><img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/arrow.svg" alt="Other UPI"></span>
</div>
    </a>

    <a href="<?= $bhim ?>">
<div class="payment-option">
    <div>
        <img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/upi-icon.svg" alt="Other UPI"> <!-- Replace with Other UPI icon -->
        <span class="ms-2">Other UPI App</span>
        </div>
    <span><img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/arrow.svg" alt="Other UPI"></span>
</div>
    </a>


    <!-- Footer Text -->
    <div class="footer-text">
        This is a One-Time Payment link, do not use it again. For any other concern contact admin.
    </div>
</div>
                            </div>
     

<div class="footer">
     <p class="secure-payment">100% Secure Payment</p>
    <div class="powered-by">
        <p>Powered by</p>
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT2oExSwfHnW_-oJs_DurZXFwPjc6DguDvEfQ&s" alt="Logo" class="logo">
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
    'message' => 'Transaction Successfully',
    'result' => array(
            "txnStatus" => "COMPLETED",
            "resultInfo" => "Transaction Success",
            "orderId" => $order_id,
            'amount' => $transaction["amount"],
            'date' => $transaction['create_date'],
            'utr' => '',
            'customer_mobile' => $transaction['customer_mobile'],
            'remark1' => $bbbyteremark1,
            'remark2' => $transaction["remark2"]
        )
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
// Data to be sent
$postData = array(
    'status' => 'SUCCESS',
    'order_id' => $order_id,
    'message' => 'Transaction Successfully',
    'result' => array(
            "txnStatus" => "COMPLETED",
            "resultInfo" => "Transaction Success",
            "orderId" => $order_id,
            'amount' => $transaction["amount"],
            'date' => $transaction['create_date'],
            'utr' => '',
            'customer_mobile' => $transaction['customer_mobile'],
            'remark1' => $bbbyteremark1,
            'remark2' => $transaction["remark2"]
        )
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
$InputArray['status'] = 'success';
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
$InputArray['status'] = 'failed';
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
</body>
</html>