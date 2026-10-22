
<!DOCTYPE html>
<html lang="en">
<link type="text/css" rel="stylesheet" id="dark-mode-custom-link">
<link type="text/css" rel="stylesheet" id="dark-mode-general-link">
<style lang="en" type="text/css" id="dark-mode-custom-style"></style>
<style lang="en" type="text/css" id="dark-mode-native-style"></style>
<style lang="en" type="text/css" id="dark-mode-native-sheet"></style>

<head>
    <meta charset="utf-8">
    <title>imb Pay</title>
    <meta name="Description" content="Pay via Imb Payment | UPI Payments Gateway">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,minimum-scale=1,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <!--<link rel="icon" href="/linchpin/uat/favicon.ico">-->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="https://pay.garudhub.in/payment/bhim_icon.png" />
    <meta name="theme-color" content="#1bb1dc">
    <style>
        *,
        :after,
        :before {
            box-sizing: inherit
        }

        html {
            box-sizing: border-box
        }

        .processText {
            margin-top: 235px !important;
        }

        @media only screen and (max-width:567px) {
            .processText {
                margin-top: 195px !important;
            }
        }

        .vAlign-wrapper {
            background: #fff;
            display: table;
            height: 100%;
            left: 0;
            position: absolute;
            text-align: center;
            top: 0;
            width: 100%;
            z-index: 98;
        }

        .arial * {
            font-family: Arial, Helvetica, sans-serif
        }

        .vAlign-wrapper .vAlign-inner-wrap {
            display: table-cell;
            vertical-align: middle;
        }

        .vAlign-wrapper .vAlign-inner-wrap .vAling-content {
            margin: 2em auto
        }

        .vAlign-wrapper .vAlign-inner-wrap .vAling-content div.logo-icon {
            height: 64px;
            margin: auto auto;
            width: 64px;
            display: inline-block
        }

        .vAlign-wrapper .vAlign-inner-wrap .vAling-content img {
            height: 64px;
            margin: auto auto 0.8em;
            width: 64px
        }

        .vAlign-wrapper .vAlign-inner-wrap .vAling-content h2 {
            font-size: 1.5em;
            font-weight: 400;
            line-height: 1.75rem;
            margin-top: 0;
        }

        .vAlign-wrapper .vAlign-inner-wrap .vAling-content h2 span {
            display: block
        }

        .vAlign-wrapper .vAlign-inner-wrap .vAling-content P {
            font-size: 1.1em
        }

        @media only screen and (max-width:567px) {
            .vAlign-wrapper .vAlign-inner-wrap .vAling-content P {
                font-size: 1em
            }
        }

        .vAlign-wrapper .vAlign-inner-wrap .vAling-content P.backbtn-text {
            color: #9e9e9e;
            font-size: 14px;
            line-height: 16px;
            padding: .25rem 1rem;
            position: absolute;
            bottom: 10px;
            width: 100%
        }

        @media only screen and (max-width:567px) {
            .vAlign-wrapper .vAlign-inner-wrap .vAling-content P.backbtn-text {
                font-size: 14px
            }
        }
        
        @media (min-width: 768px) {
            .mobile-only {
                display: none !important;
            }
        }

        .vAlign-wrapper .vAlign-inner-wrap .vAling-content .spinner-box {
            margin: 3em auto;
            height: 56px;
        }

        #loaderContainer {
            display: block
        }

        #appWrapper {
            display: none
        }

        #appContainer {
            display: none
        }

        .error {
            border: 1px solid red;
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
            margin-right: 10px;
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

.success-screen {
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #28a745; /* Green background */
            color: white;
            opacity: 0;
            transform: scale(0); /* Start small */
            animation: zoomIn 1s forwards, fadeIn 1.5s forwards; /* Zoom and fade-in animation */
            text-align: center;
        }

        .success-icon {
            font-size: 100px; /* Large size for the thumbs up icon */
            margin-bottom: 20px;
            color: white; /* White color for the icon */
        }

        .success-message {
            font-size: 30px;
            font-weight: bold;
        }

        .redirect-message {
            margin-top: 20px;
            font-size: 18px;
        }

        @keyframes zoomIn {
            0% {
                opacity: 0;
                transform: scale(0); /* Start small */
            }
            100% {
                opacity: 1;
                transform: scale(1); /* End at normal scale */
            }
        }

        @keyframes fadeIn {
            0% {
                opacity: 0; /* Start invisible */
            }
            100% {
                opacity: 1; /* End visible */
            }
        }

        /* For responsiveness: Make sure the icon and message scale well on smaller screens */
        @media (max-width: 600px) {
            .success-icon {
                font-size: 80px; /* Smaller icon size on mobile */
            }

            .success-message {
                font-size: 24px;
            }
            
             .failure-icon {
                font-size: 80px; /* Smaller icon size on mobile */
            }

            .failure-message {
                font-size: 24px;
            }

            .redirect-message {
                font-size: 16px;
            }
        }
        
         .failure-screen {
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #dc3545; /* Red background */
            color: white;
            opacity: 0;
            transform: scale(0); /* Start small */
            animation: zoomIn 1s forwards, fadeIn 1.5s forwards; /* Zoom and fade-in animation */
            text-align: center;
        }

        .failure-icon {
            font-size: 100px; /* Large size for the cross icon */
            margin-bottom: 20px;
            color: white; /* White color for the icon */
        }

        .failure-message {
            font-size: 30px;
            font-weight: bold;
        }
    </style>
    
    <link href="<?= $site_url ?>/payment3/assets/css/checkout.css" rel="preload" as="style">
    <link rel="stylesheet" type="text/css" href="<?= $site_url ?>/payment3/assets/css/checkout.css">
    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/vendors~route-mIntentPay~route-onboard~route-pay.da67a.chunk.js"></script>
    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/0.48066.chunk.js"></script>
    <link rel="stylesheet" type="text/css" href="https://mercury-uat.phonepe.com/linchpin/uat/route-mIntentPay~route-onboard~route-pay.chunk.fecfc.css">
    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/route-mIntentPay~route-onboard~route-pay.38f75.chunk.js"></script>
    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/3.46df2.chunk.js"></script>
    <link rel="stylesheet" type="text/css" href="<?= $site_url ?>/payment3/assets/css/route-onboard.chunk.css">
    <link rel="stylesheet" type="text/css" href="https://mercury-uat.phonepe.com/linchpin/uat/6.chunk.efe86.css">
    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/6.f43aa.chunk.js"></script>
    <link rel="stylesheet" type="text/css" href="https://mercury-uat.phonepe.com/linchpin/uat/10.chunk.df817.css">
    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/10.4f218.chunk.js"></script>
    <link rel="stylesheet" type="text/css" href="https://mercury-uat.phonepe.com/linchpin/uat/5.chunk.a90d3.css">
    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/5.2fbb3.chunk.js"></script>
    <link rel="stylesheet" type="text/css" href="https://mercury-uat.phonepe.com/linchpin/uat/8.chunk.660f3.css">
    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/8.9902d.chunk.js"></script>
    <link rel="stylesheet" type="text/css" href="https://mercury-uat.phonepe.com/linchpin/uat/9.chunk.660f3.css">
    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/9.3bd9c.chunk.js"></script>
    <link rel="stylesheet" type="text/css" href="https://mercury-uat.phonepe.com/linchpin/uat/7.chunk.660f3.css">
    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/7.25a8f.chunk.js"></script>

    <link rel="stylesheet" type="text/css" href="<?= $site_url ?>/payment3/assets/css/checkout.chunk.css">

    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/4.ad930.chunk.js"></script>
    <link rel="stylesheet" type="text/css" href="https://mercury-uat.phonepe.com/linchpin/uat/19.chunk.869a7.css">
    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/19.f9034.chunk.js"></script>
    <script charset="utf-8" src="https://mercury-uat.phonepe.com/linchpin/uat/4dbf6fa7d9a68c2d2d40/jse.cc96e.chunk.js"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-W4LJDQ1YTQ');
    </script>
</head>

<?php
date_default_timezone_set("Asia/Kolkata");
include "../Qrcode/security.php";
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$site_url = $protocol . "://" . $_SERVER['HTTP_HOST'];

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

$link_token = $_GET["token"];

// Fetch order_id based on the token from the payment_links table
$sql_fetch_order_id = "SELECT order_id, created_at FROM payment_links WHERE link_token = '$link_token'";
$result = getXbyY($sql_fetch_order_id);

if (count($result) === 0) {
    // Token not found or expired
    echo "Token not found or expired";
    exit;
}

$order_id = $result[0]['order_id'];
$created_at = strtotime($result[0]['created_at']);
$current_time = time();

// Check if the token has expired (more than 5 minutes)
if (($current_time - $created_at) > (5 * 60)) {
    echo "Token has expired";
    exit;
}


$slq_p = "SELECT * FROM orders where order_id='$order_id'";
$res_p = getXbyY($slq_p);    
$amount = $res_p[0]['amount'];
$user_token = $res_p[0]['user_token'];
$redirect_url = $res_p[0]['redirect_url'];
$cxrkalwaremark = $res_p[0]['byteTransactionId'];  //remark
$cxrbytectxnref=$res_p[0]['paytm_txn_ref'];
$mid=$res_p[0]['merchant_id'];

if($redirect_url==''){
$redirect_url=$site_url.'/';    
}


$slq_pmode = "SELECT * FROM users where user_token='$user_token'";
        $res_pmode = getXbyY($slq_pmode);
        $usermodetoken = $user_token;
    
$slq_p = "SELECT * FROM amazon_pay where user_token='$usermodetoken' AND id='$mid'";
        $res_p = getXbyY($slq_p);    
 $upi_id = $res_p[0]['upi_id']; //upi id from paytm tokens
 
 $slq_p = "SELECT * FROM users where user_token='$user_token'";
        $res_p = getXbyY($slq_p);    
 $unitId=$res_p[0]['company'];
 echo ' <script> document.title = "'.$unitId.'"</script>';
 
 $transactionId=$cxrbytectxnref;
$orders="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tid=$transactionId";



//------Imb QR Code Start-------//

// PHP QR Code library path
include('../Qrcode/phpqrcode/qrlib.php');

// Data to encode
$data = $orders;

// File path to save the QR code image
//$file = 'image/qrcode.png';
$file = '../Qrcode/image/qrcode_' . uniqid() . '.png';

// ECC level (L, M, Q, H)
$ecc = 'L';

// QR code size
$size = 10;

// Generate QR code image
QRcode::png($data, $file, $ecc, $size);

// Encode the image to base64
$imageData = base64_encode(file_get_contents($file));

// Output the QR code as a base64 data URI
$imbqr_code_url = 'data:../Qrcode/image/png;base64,' . $imageData;
// $imbqr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($orders);  //google chartapi


//------imb QR Code End-------//
$paytmintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$cxrbytectxnref&tid=$transactionId&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

 if($res_pmode[0]["logo"] == ''){
      $userlogo = "https://pay.garudhub.in/payment/bag.jpg";
  }else{
      $userlogo = 'https://pay.garudhub.in/merchant/'.$res_pmode[0]["logo"];
  }
 
 ?>
 
  <style>
      .b2bHeader__cfNq8{
            background: <?= $res_pmode[0]['color_theme'] ?>;
        }
 </style>
 
 <body class="">
    <div class="qr-wrapper">
    <div id="loaderContainer">
        <div class="vAlign-wrapper">
            <div class="vAlign-inner-wrap">
                <div class="vAling-content">
                    <div id="pageLoader"><img alt="" class="logo-icon dsk-icon" src="<?= $site_url ?>/payment3/assets/favicon.png">
                        <h2>Please wait...</h2>
                        <div class="spinner-box">
                            <app-spinner>
                                <div class="preloader-wrapper active">
                                    <div class="spinner-layer spinner-purple-only">
                                        <div class="circle-clipper left">
                                            <div class="circle"></div>
                                        </div>
                                        <div class="gap-patch">
                                            <div class="circle"></div>
                                        </div>
                                        <div class="circle-clipper right">
                                            <div class="circle"></div>
                                        </div>
                                    </div>
                                </div>
                            </app-spinner>
                        </div>
                        <p class="backbtn-text">Please do not press back or close this window</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="overlay-hold-on"><span>Hold a sec...</span></div>
    <div id="appContainer">
        <div id="appWrapper" class="onboarding    offersPopupShownBottom  ">
            <div id="container" class="b2bContainer noniframeDesktop">
                <div class="b2bHeader__cfNq8 b2bHeaderOuterContainer b2bHeaderWithShadow__4a9v0"><span class="b2bMerchantLogoWrapper__A1NyU"><img
							src="<?= $userlogo ?>"></span>
							<label for="upi-account-undefined" class="instrument-checked label__L2Gx2" style="display: inline;margin-top: 10px;">Payble Amount</label>
                    <div class="b2bHeaderDetails__OSniC"><span class="b2bHeaderAmount__ZMDgD ">
							<currency>₹<?php echo $amount; ?></currency>
						</span></div>
                </div>
                <div class="contentContainerOnboarding">
                    <div class="ios-keyboard-handler   ">
                        <div>
                            <form class="main-form b2bOnboardingForm__dOjRj" method="POST" action="">
                                <div class="form-content-container onboarding__MAGwm onboardingB2B__9je1X">
                                    <div class="b2bOnboardContentContainer__fC+5S">
                                        <div>
                                            <div class="instrumentItem no-border-bottom no-padding-bottom">
                                                
                                                <?php if($res_pmode[0]['pg_qrcode'] == '1'){ ?>
                                                <div>
                                                    <div class="formField mdc-form-field">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20px" height="20px" style="" class="instrumentLogo qrLogo__jivfq">
															<path fill="#424242"
																d="M4,4h6v6H4V4M20,4v6H14V4h6M14,15h2V13H14V11h2v2h2V11h2v2H18v2h2v3H18v2H16V18H13v2H11V16h3V15m2,0v3h2V15H16M4,20V14h6v6H4M6,6V8H8V6H6M16,6V8h2V6H16M6,16v2H8V16H6M4,11H6v2H4V11m5,0h4v4H11V13H9V11m2-5h2v4H11V6M2,2V6H0V2A2,2,0,0,1,2,0H6V2H2M22,0a2,2,0,0,1,2,2V6H22V2H18V0h4M2,18v4H6v2H2a2,2,0,0,1-2-2V18H2m20,4V18h2v4a2,2,0,0,1-2,2H18V22Z">
															</path>
														</svg>
                                                        <label for="upi-account-undefined" class="instrument-checked label__L2Gx2">Scan QR Code to Pay</label>
                                                        <div class="absolute-right instrument-control-fix mdc-radio mdc-ripple-upgraded mdc-ripple-upgraded--unbounded" style="--mdc-ripple-fg-size: 24px; --mdc-ripple-fg-scale: 1.6666666666666667; --mdc-ripple-left: 8px; --mdc-ripple-top: 8px;">
                                                            <input type="radio" id="qr-pay" class="mdc-radio__native-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="description">Open UPI app and scan</div>
                                                <?php } ?>
                                                
                                                <div class="b2bContainerWrapper__UUuVJ wrapper__4ljkS">
                                                    <?php if($res_pmode[0]['pg_qrcode'] == '1'){ ?>
                                                    <img src="<?php echo $imbqr_code_url; ?>" class="qr__ynSSq">
                                                    <?php } ?>
                                                    <div class="loader__HVNkL">
                                                        <div class="countdown__RBXqR">
                                                            <div class="hint__g3IAI">Checking payment status…</div>
                                                            <div class="timer__oDTpG" id="timeout">0:00</div>
                                                            <div class="loaderContainer__qoK5u">
                                                                <img src="https://pay.garudhub.in/payment3/assets/images/backgrounds/spinner-1.gif" alt="Loading...">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php if(($res_pmode[0]["plan_id"] != '' || $res_pmode[0]["plan_id"] != '0') && $res_pmode[0]['pg_upiapps'] == '1'){ ?>
                                                    
                                                    <?php if($res_pmode[0]['pg_intent1'] == '1' || $res_pmode[0]['pg_intent2'] == '1'){ ?>
                                                     <div class="mobile-only">
                                                    <div class="formField mdc-form-field">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" class="bi bi-send-check instrumentLogo qrLogo__jivfq" viewBox="0 0 16 16">
  <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855a.75.75 0 0 0-.124 1.329l4.995 3.178 1.531 2.406a.5.5 0 0 0 .844-.536L6.637 10.07l7.494-7.494-1.895 4.738a.5.5 0 1 0 .928.372zm-2.54 1.183L5.93 9.363 1.591 6.602z"/>
  <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.774.773a.75.75 0 0 0 1.174-.144l1.335-2.226a.5.5 0 0 0-.172-.686"/>
</svg>
                                                        <label for="upi-account-undefined" class="instrument-checked label__L2Gx2">Pay Using UPI Apps</label>
                                                        <div class="absolute-right instrument-control-fix mdc-radio mdc-ripple-upgraded mdc-ripple-upgraded--unbounded" style="--mdc-ripple-fg-size: 24px; --mdc-ripple-fg-scale: 1.6666666666666667; --mdc-ripple-left: 8px; --mdc-ripple-top: 8px;">
                                                            <input type="radio" id="qr-pay" class="mdc-radio__native-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="description mobile-only" style="margin-right: 40%;margin-left: 0;">Click UPI App and Pay</div>
                                                <?php } ?>
                                                    
                                                    <div style="margin-top: 7%">
                                                        
                                
                                <?php if($res_pmode[0]['pg_intent2'] == '1'){ ?>
                                 <a href="<?= $paytmintent ?>" class="mobile-only">
<div class="payment-option">
    <div style="display: flex;align-items: center;">
        <img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/paytm.png" alt="Paytm UPI">
        <span class="ms-2">Paytm UPI</span>
        </div>
    <span><img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/arrow.svg" alt="Other UPI"></span>
</div>
    </a>
    <?php } ?>
    
    <?php if($amount <= 2000 && $res_pmode[0]['pg_intent1'] == '1'){ ?>
    <a href="#" class="mobile-only" onclick="shareQRCode();">
    <div class="payment-option">
        <div style="display: flex; align-items: center;">
            <img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/googlepay-circle.svg" alt="Google Pay"> <!-- Replace with Google Pay icon -->
            <span class="ms-2">Share Google Pay</span>
        </div>
        <span><img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/arrow.svg" alt="Other UPI"></span>
    </div>
</a>
<?php } ?>


<!--Start Google pay per images share karwane ke liye hai-->

<script>
    async function shareQRCode() {
        // Fetch the QR code base64 data from the server or image source
        const qrCodeData = "<?php echo $imbqr_code_url; ?>"; // Example: 'data:image/png;base64,...'

        try {
            // Convert the Base64 data to a Blob
            const response = await fetch(qrCodeData);
            const blob = await response.blob();

            // Create a File object from the Blob (Web Share API requires files for sharing images)
            const file = new File([blob], "qr-code.png", { type: "image/png" });

            // Prepare the share data
            const shareData = {
                title: "UPI QR Code",
                text: "Scan this QR code to make a payment via UPI.",
                files: [file] // Add the QR code file
            };

            // Check if the Web Share API is supported
            if (navigator.share) {
                await navigator.share(shareData);
                console.log("QR code shared successfully.");
            } else {
                console.warn("Sharing is not supported on this device/browser.");
            }
        } catch (error) {
            console.error("Error sharing QR code:", error);
            // Silently handle the error without showing an alert
        }
    }
</script>

<!--End Google pay per images share karwane ke liye hai-->
                                
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="b2bFooterContainer__aWDqd fixed-footer">
                                    <div class="horizontalSeparatorOnboarding__Yo-hz"></div>

                                <?php if($res_pmode[0]['pg_pby'] == '1'){ ?>
                                    <div class="phonepeBrandInfo__PONIY">
                                        <span class="phonepeLabel__xk3Bl">
									        Powered by
											<img src="<?= $site_url ?>/newassets/images/Logo.png" style="width: 113px;margin-left: -9px;margin-top: -9px;">
										</span>
                                    </div>
                                 <?php } ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="success-screen" id="successScreen">
    <!-- Thumbs Up Icon from Font Awesome -->
    <div class="success-icon"><i class="far fa-check-circle"></i></div>
    <h1 class="success-message">Payment Successfully</h1>
    <p class="redirect-message">Redirecting in <span id="countdown">3</span> seconds...</p>
</div>

<div class="failure-screen" id="failureScreen">
    <!-- Cross Icon from Font Awesome -->
    <div class="failure-icon"><i class="fas fa-times-circle"></i></div>
    <h1 class="failure-message">Payment Failed</h1>
    <p class="redirect-message">Redirecting in <span id="countdown">3</span> seconds...</p>
</div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        function payViaUPI() {
            document.getElementById('paymentForm').submit();
        }

        function upiCountdown(elm, minute, second, url) {
            document.getElementById(elm).innerHTML = minute + ":" + (second < 10 ? "0" + second : second); // Initial display
            startTimer();

            function startTimer() {
                var presentTime = document.getElementById(elm).innerHTML;
                var timeArray = presentTime.split(/[:]+/);
                var m = parseInt(timeArray[0]);
                var s = parseInt(timeArray[1]) - 1;

                if (s < 0) {
                    s = 59;
                    m = m - 1;
                }

                if (m < 0) {
                    Swal.fire({
                        title: 'Oops',
                        text: 'Transaction Timeout!',
                        icon: 'error'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "https://pay.img.org.in";
                        }
                    });
                    return;
                }

                s = (s < 10 ? "0" + s : s);
                document.getElementById(elm).innerHTML = m + ":" + s;

                setTimeout(startTimer, 1000);
            }
        }


        upiCountdown("timeout", 3, 0, location.href);
        
        var checkinterval;
        
        function myalert(type,href,text='') {
       
       if(type == 'success'){
           document.getElementById('successScreen').style.display = 'flex';
       }else{
           document.getElementById('failureScreen').style.display = 'flex';
           if(text != ''){
               document.querySelector('.failure-message').innerHTML = text;
           }
       }
        document.querySelector('.qr-wrapper').style.display = 'none';

        let countdown = 3;
        const countdownElement = document.getElementById('countdown');
        const interval = setInterval(function () {
            countdown--;
            countdownElement.textContent = countdown;

            if (countdown === 0) {
                clearInterval(interval);
                window.location.href = href;
            }
        }, 1000);
    }
        
        function check() {
    $.ajax({
        type: 'post',
        url: '<?= $site_url ?>/order9/payment-status',
        data: {'orderid' : '<?php echo $order_id?>','txnid' : '<?= $transactionId ?>'},
        success: function (data) {
            if(data == 'success'){
                clearInterval(checkinterval)
                myalert('success',"<?php echo $redirect_url ?>");
            } else if(data == 'FAILURE'){
               clearInterval(checkinterval)
               myalert('error',"<?php echo $redirect_url ?>");
            }else if(data == 'FAILED'){
                 clearInterval(checkinterval)
                 myalert('error',"<?php echo $redirect_url ?>",'Your Transaction is failed due to getting wrong no of amount from your upi app ! try again later Note : This amount is not Refundable.');
            }else{
                if(data != 'PENDING'){
                    clearInterval(checkinterval)
                    myalert('error',"<?php echo $redirect_url ?>",data);
                }
            }
        }
    });    
}

  checkinterval = setInterval(check, 2000);

    </script>
</body>
</html>



