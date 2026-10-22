
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include SweetAlert2 and jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>imb Pay</title>
    <style>
        body {
            background: #667eea;
            background: -webkit-linear-gradient(to right, pink, green);
            background: linear-gradient(to right, #BFF7F2, #A0E3A4);
            display: flex;
            justify-content: center;
            align-items: center;
            /*height: 100vh;*/
            margin: 0;
            font-family: Arial, sans-serif;
        }
        
        input, textarea {
  border: solid 1px lightgray;
  border-radius: 5px;
  padding: 10px 20px 10px 10px;
  width: 100%;
  box-sizing: border-box;
}

input:focus{
    border-color: #25a6a1;
    transition: all .2s;
}

.pay-button{
    all: unset;
    display:block !important;
    height: 40px;
    width: 100%;
    padding: 0 !important;
    background: linear-gradient(#25a6a1, #097ea0);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    color: #ffffff;
    margin-top: 18px;
    cursor: pointer;
}
        
        .qr-wrapper {
            padding: 10px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
        }
        
        .qr-container {
            background: #fff;
            padding: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 300px;
            border-radius: 8px;
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

        
        .qr-title {
            background: #343a40;
            color: #fff;
            padding: 10px;
            font-size: 18px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        
        .qr-code {
            padding: 10px;
            margin: 20px auto;
            display: inline-block;
            border: 4px solid; /* Required for gradient borders */
            border-image-slice: 1;
            border-width: 4px;
            border-image-source: linear-gradient(45deg, #f3ec78, #af4261); /* Gradient border */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .amount {
            font-size: 16px;
            margin: 20px 0;
            color: #343a40;
        }
        
        .validity {
            font-size: 12px;
            margin-top:18px;
            color: #000000; /* Changing color to black */
            /* Other properties remain unchanged */
        }

        .pay-button {
            display: none;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .paytm-button {
            display: block; /* Change from inline-block to block */
            width: 85%; /* Set width to 100% */
            background-color: #E5E5E5 ;
            color: black;
            padding: 10px 20px;
            margin: 10px 0;
            border: 2px solid #1e88e5; /* Dark blue border */
            border-radius: 5px;
            text-decoration: none; /* Remove underline */
            font-size: 16px;
            transition: background-color 0.3s, color 0.3s;
        }

        .paytm-button:hover {
            background-color: #1e88e5; /* Dark blue on hover */
            color: white;
        }

        @media screen and (max-width: 768px) {
            .pay-button {
                display: inline-block;
            }
        }  
       .order-container {
  background-color: #0b95bd; /* Sets the background color of the container to dark blue */
  color: white;               /* Sets the text color inside the container to white */
  border-radius: 10px;        /* Gives the container rounded corners with a radius of 10px */
  padding: 20px;              /* Adds 20px padding inside the container for spacing */
  width: 86%;                 /* Sets the container's width to 86% of its parent element */
  height: 70px;              /* Sets the container's height to 300 pixels */
 
}

    .order-amount {
      font-size: 20px;
      text-align: left;
      font-weight: bold; /* Making text bold */
    }

    .amount {
  font-size: 30px;
  text-align: center;
  font-weight: bold; /* Making text bold */
  color: white; /* Changing text color to white */
}
/* Hide method-box by default */
.method-box {
    display: block;
}

/* Show method-box on screens smaller than 768px */
@media screen and (max-width: 768px) {
    .method-box {
        display: block;
    }
}


.method-box {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      margin-top: 20px; /* Add some space between payment and method box */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add shadow */
    }

    .method-header {
      background-color: #0b95bd;
      color: white;
      padding: 10px 0; /* Adjust padding to make the header full width */
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      margin-bottom: 20px; /* Add some space between header and boxes */
    }
    .qr-box {
      background-color: white;
      border-radius: 10px;
      padding: 20px;
      margin-top: 20px; /* Add some space between method box and QR box */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add shadow */
    }

    .qr-header {
      background-color: #0b95bd;
      color: white;
      padding: 10px 0; /* Adjust padding to make the header full width */
      border-top-left-radius: 10px;
      border-top-right-radius: 10px;
      margin-bottom: 20px; /* Add some space between header and box */
    }

    .qr-image {
      display: block; /* Ensure the image is a block element */
      margin: 0 auto; /* Center the image */
      max-width: 50%; /* Ensure the image does not exceed the container width */
      height: 50%; /* Maintain aspect ratio */
    }
    /* Hide OR text by default */
.or-text {
    display: block;
}

/* Show OR text on screens smaller than 768px */
@media screen and (max-width: 768px) {
    .or-text {
        display: block;
    }
}

    </style>
     <script>
        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Disable Ctrl+U
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'u') {
                e.preventDefault();
            }
        });
    </script>
</head>

<?php
date_default_timezone_set("Asia/Kolkata");
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$site_url = $protocol . "://" . $_SERVER['HTTP_HOST'];



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
//$cxrbytectxnref=$res_p[0]['paytm_txn_ref'];

if($redirect_url==''){
$redirect_url=$site_url.'/';    
}



    
$slq_p = "SELECT * FROM bharatpe_tokens where user_token='$user_token'";
        $res_p = getXbyY($slq_p);    
 $upi_id = $res_p[0]['Upiid']; //upi id from paytm tokens
 
 $slq_p = "SELECT * FROM users where user_token='$user_token'";
        $res_p = getXbyY($slq_p);    
 $unitId=$res_p[0]['name'];
 
 $asdasd23="ARC".rand(111,999).time().rand(1,100);
 $cxrbytectxnref=time().rand(11111,99999);
$orders="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tr=$cxrbytectxnref";



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

$imbqr_code_url = 'data:../Qrcode/image/png;base64,' . $imageData;

//------imb QR Code End-------//



 ?>
 
 <body>
    <div class="qr-wrapper">
        <div class="qr-container">
  <!--          <div class="order-container">-->
  <!--  <div class="order-amount">-->
  <!--    Order Amount-->
  <!--  </div>-->
  <!--  <div class="amount">-->
  <!--   ₹ <?php echo $amount; ?>    </div>-->
  <!--</div>-->

    
    <div class="qr-box">
      <div class="qr-header">Scan UPI QR</div>
     <span style="color: red; font-weight: bold; font-size: 10px;">Don't scan the same QR code for multiple times!</span>

      <img class="qr-image" src="<?php echo $imbqr_code_url; ?>" alt="QR Code">
    </div><br>
    
    <div class="payment-container">
    <div class="method-box">
      <div class="method-header">Verify Txn After Payment Click Below</div>
      <form id="paymentForm" method="post">
    <!-- Other input fields -->
    <input type="text" name="utr" placeholder="UTR Number" class="form-control">
    <input type="hidden" name="TransactionId" value="<?php echo $cxrkalwaremark; ?>">
     <button class="pay-button" type="submit">Confirm Payment</button>
</form>
    </div>
    </div>
    
    <div class="validity">Valid until: <span id="timeout"></span></div>
<div class="footer">
    <p class="secure-payment">100% Secure Payment</p>
    <div class="powered-by">
        <p>Powered by</p>
       <img src="<?= $site_url ?>/newassets/images/Logo.png" style="width: 113px;margin-left: -7px;margin-top: -3px;">
    </div>
</div>


    <script>
        function payViaUPI() {
    document.getElementById('paymentForm').submit();
}


function upiCountdown(elm, minute, second, url) {
    document.getElementById(elm).innerHTML = minute + ":" + second;
    startTimer();

    function startTimer() {
        var presentTime = document.getElementById(elm).innerHTML;
        var timeArray = presentTime.split(/[:]+/);
        var m = timeArray[0];
        var s = checkSecond((timeArray[1] - 1));
        if(s == 59){m = m - 1}
        if(m < 0){
            Swal.fire({
              title: 'Oops',
              text: 'Transaction Timeout!',
              icon: 'error'
            });
            window.location.href = "<?= $site_url ?>";
        }
        document.getElementById(elm).innerHTML = m + ":" + s;
        setTimeout(startTimer, 1000);
    }

    function checkSecond(sec) {
        if (sec < 10 && sec >= 0) { sec = "0" + sec };
        if (sec < 0) { sec = "59" };
        return sec;
    }
}

upiCountdown("timeout", 5, 0, location.href);

$("#paymentForm").submit(function(e){
    e.preventDefault()
   $.ajax({
                type: 'post',
                url: '<?= $site_url ?>/order4/payment-status',
                data: new FormData(this),
        		processData: false,
        		contentType: false,
                success: function (data) {
                    let rslt = JSON.parse(data);
                    if(rslt.status == 'success'){
                        Swal.fire({
                            title: '',
                            text: 'Your Payment Received Successfully 👍',
                            icon: 'success'
                        }).then(() => {
                    		window.location.href = "<?= $redirect_url ?>";
                    	});
                        
                    } else if(rslt.status == 'pending'){
                        Swal.fire({
                            title: 'Processing !',
                            text: 'Your Payment in Processing',
                            icon: 'warning'
                        }).then(() => {
                    		window.location.href = "<?= $redirect_url ?>";
                    	});
                    } else if(rslt.status == 'invalid'){
                        Swal.fire({
                            title: 'Transaction Failed !',
                            text: rslt.error,
                            icon: 'error'
                        });
                    } else if(rslt.error !== ''){
                        Swal.fire({
                            title: ' Oops error !',
                            text: rslt.error,
                            icon: 'error'
                        });
                       
                    }
                }
            }); 
});

</script>

<?php include "../Qrcode/security.php"; ?>
</body>
</html>

