
<?php
session_start();
include "config.php";

//   ini_set("display_errors",true);
//   error_reporting(E_ALL);
 // used to decrypt values
function decrypt_token($encryption){
    $ciphering = "AES-128-CTR";
     $decryption_iv = 'ThisIsSecretKeyForEncrytionByJisneYeBnaya!@#$%^&*()';
    $decryption_key = "imbbank";
    // Using openssl_decrypt() function to decrypt the data 
    $decryption = openssl_decrypt(base64_decode($encryption), $ciphering, $decryption_key, 0, $decryption_iv);
    return $decryption;
}
      $token = $_GET["token"];
      $json = decrypt_token($token);
      $data = json_decode($json,true);
      
   $name =  $data['name'];
   $mobile =  $data['mobile'];
  $remark = $data['remark'];
   $amount =  $data['amount'];
   $amount_type =  $data['amount_type'];
   $contact_form =  $data['contact_form'];
   $usertoken =  $data['user_token'];
   $getuser = $conn->query("SELECT company FROM users WHERE user_token = '$usertoken'")->fetch_assoc();
  
  if (isset($_POST['pay'])) {
     
     if($amount_type == 1){
         $amount = $amount;
     }else{
         $amount = $_POST["amount"];
     }
       $orderid = mt_rand(10000000000,9999999999999);
       
       $data = array(
    'customer_mobile' => $mobile,
    'user_token' => $usertoken,
    'amount' => $amount,
    'order_id' => $orderid,
    'redirect_url' => $site_url.'/success?orderid=' . $orderid . '&token=' . $usertoken,
    'remark1' => $remark,
);
  
        $curl = curl_init();

curl_setopt_array($curl, array(
   CURLOPT_URL => $site_url.'/api/create-order',
   CURLOPT_RETURNTRANSFER => true,
   CURLOPT_ENCODING => '',
   CURLOPT_MAXREDIRS => 10,
   CURLOPT_TIMEOUT => 0,
   CURLOPT_FOLLOWLOCATION => true,
   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
   CURLOPT_CUSTOMREQUEST => 'POST',
   CURLOPT_POSTFIELDS => http_build_query($data),
   CURLOPT_HTTPHEADER => array(
      'User-Agent: Apidog/1.0.0 (https://apidog.com)'
   ),
));

$response = curl_exec($curl);

curl_close($curl);

$jsondatares = json_decode($response,true);
 
      $paymentlink = '';
       if($jsondatares["status"] == true){
      $paymentlink = $jsondatares["result"]["payment_url"];
      header("location:".$paymentlink);
       }else{
            echo '
    <script>
        Swal.fire({
            title: "Opps! Failed To Create Payment Link!",
            text: "'.$jsondatares["message"].'",
            confirmButtonText: "Ok",
            icon: "error"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "paymentLink"; // Replace with your desired redirect URL
            }
        });
    </script>
';
exit;
       }
  }
  ?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <link rel="stylesheet" href="css/paymentd.css">
    
    

    <title>IMB Payment Details For Checkout!</title>
    <style>
        body{
            background: linear-gradient(118deg, #ffffff 60%, #25a6a1 40%);
            background-repeat: no-repeat;
        }
        
        img.logopic {
    width: 100px;
    margin-left: 12px;
}
    </style>
  </head>
  <body>
    <div class="container d-lg-flex">
	<div class="box-1 user">
		<div class="d-flex align-items-center mb-3"> <img src="https://images.pexels.com/photos/4925916/pexels-photo-4925916.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=500" class="pic rounded-circle" alt="">
			<p class="ps-2 name"><?php echo $name ?></p>
		</div>
		<div class="box-inner-1 pb-3 mb-3 ">
			<div class="d-flex justify-content-between mb-3 userdetails">
				<p class="fw-bold"><?php echo $remark ?></p>
				<p class="fw-lighter">₹<?php echo number_format($amount,2) ?></p>
			</div>
		
			<p class="dis my-3 info">Dear Customer Kindly Pay Securely to our client and follow the below step correctly. </p>
			<p class="dis mb-3 updates">No Extra Charge For This Payment</p>
			<p class="mb-3 different">Three Step To Follow :</p>
		
			<div>
				<p class="dis"><span class="fas fa-arrow-right mb-3 me-2"></span>  Click Pay Button</p>
				<p class="dis"><span class="fas fa-arrow-right mb-3 me-2"></span>  Scan The QR Code Or Enter UPI Id</p>
				<p class="dis"><span class="fas fa-arrow-right mb-3 me-2"></span>  Payment The Amount</p>
			</div>
				<div class="termsc-details">
			    <p class="fw-bold" tabindex="1">Terms &amp; Conditions:</p>
  <p class="dis mb-2" tabindex="1">You agree to share information entered on this page with <?= $getuser["company"] ?> (owner of this page) and IMB Payment, adhering to applicable laws.</p>
  </div>
		</div>
	</div>
	<div class="box-2">
		<div class="box-inner-2">
			<div>
				<p class="fw-bold">Payment Details</p>
				<p class="dis mb-3">Complete your payment by providing your payment details</p>
			</div>
			<form action="" method="POST">
			<?php if($contact_form == 1){ ?>
				<div class="mb-3">
					<p class="dis fw-bold mb-2">Name</p>
					<input class="form-control" type="text" value="" id="name" name="name" required>
					</div>
				<div class="mb-3">
					<p class="dis fw-bold mb-2">Email address</p>
					<input class="form-control" type="email" value="" id="email" name="email" required>
				</div>
				<div class="mb-3">
					<p class="dis fw-bold mb-2">Phone</p>
					<input class="form-control" type="number" value="" id="mobile" name="mobile" required>
				</div>
				<?php } ?>
					<div class="mb-3">
					<p class="dis fw-bold mb-2">Amount</p>
					<?php if($amount_type == 1){ ?>
					<input class="form-control" type="text" value="₹ <?php echo number_format($amount,2) ?>" id="amount" name="amount" readonly required>
				<?php }else{
				?>
					<input class="form-control" type="text" value="0.00" id="amount" name="amount" required>
				
				<?php }
				?>
					</div>
				<div class="mb-3">
					<button type="submit" name="pay" class="btn btn-primary mt-2 w-100"><?php $pay = ($amount_type == 1) ? 'Pay ₹'.number_format($amount,2) : 'Pay'; echo $pay ?></button>
				</div>
				<div class="d-flex align-items-center justify-content-center mb-3">
			<p class="me-5 dis">Powered By </p>
			<img src="Logo/logo.png" class="logopic" alt="Logo">
		</div>
			</form>
		</div>
	</div>
</div>
    

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
  </body>
</html>