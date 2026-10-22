

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

// ini_set("display_errors",true);
// error_reporting(E_ALL);

$link_token = ($_GET["token"]);

// Fetch order_id based on the token from the payment_links table
$sql_fetch_order_id = "SELECT order_id, created_at FROM payment_links WHERE link_token = '$link_token'";
$result = getXbyY($sql_fetch_order_id);

if (count($result) === 0) {
    echo "Token not found or expired";
    exit;
}

$order_id = $result[0]['order_id'];
$created_at = strtotime($result[0]['created_at']);
$current_time = time();

if (($current_time - $created_at) > (5 * 60)) {
    echo "Token has expired";
    exit;
}

$slq_p = "SELECT * FROM orders where order_id='$order_id'";
$res_p = getXbyY($slq_p);    
$amount = $res_p[0]['amount'];
$user_token = $res_p[0]['user_token'];
$redirect_url = $res_p[0]['redirect_url'];
$cxrkalwaremark = $res_p[0]['byteTransactionId'];
$cxrbytectxnref = $res_p[0]['paytm_txn_ref'];
$cxruser_id = $res_p[0]['user_id'];

if ($redirect_url == '') {
    $redirect_url = $site_url.'/success';
}

$slq_p = "SELECT * FROM mobikwik_token where user_token='$user_token'";
$res_p = getXbyY($slq_p);
$upi_id = $res_p[0]['merchant_upi'];

$slq_p = "SELECT * FROM users where user_token='$user_token'";
$res_p = getXbyY($slq_p);
$unitId = $res_p[0]['name'];

$asdasd23 = "ARC" . rand(111, 999) . time() . rand(1, 100);

$orders = "upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$cxrbytectxnref&tr=$cxrkalwaremark";
$encoded_orders = urlencode($orders);

// Redirect URL for payment confirmation
$payment_verification_url = $site_url."/payment8/verify/" . ($link_token);

$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . $encoded_orders;
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

      <img class="qr-image" src="<?php echo $qr_code_url; ?>" alt="QR Code">
    </div><br>
    
    <div class="payment-container">
    <div class="method-box">
      <div class="method-header">Verify Txn After Payment Click Below</div>
      <form id="paymentForm" method="post">
    <!-- Other input fields -->
    <input type="hidden" name="utrverify" value="">
    <input type="text" name="utr_number" placeholder="Enter UTR Number" class="form-control">
    <input type="hidden" name="token" value="<?php echo $link_token; ?>">
     <button class="pay-button" type="submit">Confirm Payment</button>
</form>
    </div>
    </div>
    
    <div class="validity">Valid until: <span id="timeout"></span></div>
<div class="footer">
    <p class="secure-payment">100% Secure Payment</p>
    <div class="powered-by">
        <p>Powered by</p>
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQnPPNk18hwNHCN7mRVmmt9VtSel0JjWa_Lxw&usqp=CAU" alt="Logo" class="logo">
    </div>
</div>


    <script>
        // function payViaUPI() {
        //     window.location.href = "<?php echo $payment_verification_url; ?>";
        // }

        window.onload = function () {
            var fiveMinutes = 60 * 5,
                display = document.querySelector('#timeout');
            startTimer(fiveMinutes, display);
        };

        function startTimer(duration, display) {
            var timer = duration, minutes, seconds;
            setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(this);
                }
            }, 1000);
        }
        
        $("#paymentForm").submit(function(e){
            e.preventDefault()
           $.ajax({
                type: 'post',
                url: '<?= $site_url ?>/order8/payment-status',
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
                        
                    } else if(rslt.status == 'info'){
                        Swal.fire({
                            title: 'Processing !',
                            text: rslt.message,
                            icon: 'info'
                        }).then(() => {
                    		window.location.href = "<?= $redirect_url ?>";
                    	});
                    } else if(rslt.status == 'error'){
                        Swal.fire({
                            title: 'Error !',
                            text: rslt.message,
                            icon: 'error'
                        });
                    }
                }
            }); 
});
        
    </script>
</body>
</html>


