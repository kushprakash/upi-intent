<?php

require_once '../config.php';
include('../smtp/PHPMailerAutoload.php');

function sendEmail($to, $subject, $message) {  
    global $website_settings, $conn; // Access global settings
    
    $mail = new PHPMailer(); 
    	$mail->IsSMTP(); 
    	$mail->SMTPAuth = true; 
    	$mail->SMTPSecure = $website_settings['smtp_encryption']; 
    	$mail->Host = $website_settings['smtp_host'];
    	$mail->Port = $website_settings['smtp_port']; 
    	$mail->IsHTML(true);
    	$mail->CharSet = 'UTF-8';
    	//$mail->SMTPDebug = 2; 
    	$mail->Username = $website_settings['smtp_username'];
    	$mail->Password = $website_settings['smtp_password'];
    	$mail->SetFrom($website_settings['smtp_from_email'], $website_settings['smtp_from_name']);
    	$mail->Subject = $subject;
    	$mail->Body =$message;
    	$mail->AddAddress($to);
    	$mail->SMTPOptions=array('ssl'=>array(
    		'verify_peer'=>false,
    		'verify_peer_name'=>false,
    		'allow_self_signed'=>false
    	));
    	if(!$mail->Send()){
    		return $mail->ErrorInfo;
    	}else{
    	    return true;
    	}
}


// reCAPTCHA removed: proceed without server-side CAPTCHA verification

// Check if reCAPTCHA verification passed
if (true) {

    $username = filter_var($_POST['mobile'], FILTER_SANITIZE_NUMBER_INT);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
  
  
    $query = "SELECT * FROM users WHERE mobile = '$username'";
    $run = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($run);
  

    if (mysqli_num_rows($run) > 0) {
        $hashFromDatabase = $row['password'];
        $acc_lock = $row['acc_lock'];
        $acc_ban = $row['acc_ban'];
        $userId = $row['id'];
        $pgmode = $row['pg_mode'];
        $two_factor = $row['two_factor'];

        if ($acc_ban == 'on') {
            echo json_encode(["status" => 5, "msg" => 'Your account is locked. Please email us at info@upigateways.com to unlock it.']);
            exit;
        }

        if (password_verify($password, $hashFromDatabase)) {
            session_start();
            $query = "UPDATE users SET acc_lock = 0 WHERE mobile = '$username'";
            mysqli_query($conn, $query);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            if ($two_factor == 0 && $pgmode == 1) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $userId;
                $_SESSION['login_time'] = time();
                echo json_encode(["status" => 11, "msg" => 'login success', "userid" => $userId]);
                die;
            }

            $otp = rand(100000, 999999);
            $sql = "UPDATE users SET otp = $otp WHERE mobile = '$username'";
            if (mysqli_query($conn, $sql)) {
                $toemail = $row["email"];
                $strmail = substr($toemail, -15);
                $strmobile = substr($username, -4);
                $msg = "Login OTP is sent to your Mobile No - XXXXXX$strmobile And Email XXXXXX$strmail";

                $mailmsg = '<html>
<head>
  <title>Login OTP - UPIGateways</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #007BFF;
      margin: 0;
      padding: 0;
      color: #ffffff;
    }
    .container {
      max-width: 600px;
      margin: 20px auto;
      background-color: #ffffff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      color: #333333;
    }
    .header {
      text-align: center;
      padding: 10px;
      border-bottom: 1px solid #dddddd;
    }
    .header img {
      max-width: 150px;
    }
    .content {
      padding: 20px;
    }
    .otp-info {
      background-color: #f9f9f9;
      border: 1px solid #dddddd;
      padding: 10px;
      border-radius: 5px;
      margin-top: 10px;
      text-align: center;
      font-size: 24px;
      font-weight: bold;
      color: #007BFF;
    }
    .footer {
      text-align: center;
      font-size: 12px;
      color: #777777;
      margin-top: 20px;
    }
    a {
      color: #007BFF;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <div class="container">
      <div class="header">
      <img src="' . $site_url . '/' . $website_settings['logo'] . '" alt="' . $website_settings['title'] . '">
    </div>
    <div class="content">
      <p>Dear User,</p>
      <p>To complete your login to the ' . $website_settings['title'] . ', please use the following One-Time Password (OTP):</p>
      <div class="otp-info">' . htmlspecialchars($otp) . '</div>
      <p>This OTP is valid for 10 minutes. If you did not request an OTP, please ignore this email or contact our support team.</p>
      <p>You can log in to your account using the following link: <a href="' . $site_url . '/merchant/index">Login</a></p>
      <p>If you have any questions or need further assistance, feel free to contact our support team.</p>
      <p>Best regards,<br>The UPIGateways Team</p>
    </div>
    <div class="footer">
      <p>&copy; ' . date("Y") . ' UPIGateways. All rights reserved.</p>
    </div>
  </div>
</body>
</html>';

                sendEmail($toemail, "Login OTP Verification", $mailmsg);
                echo json_encode(["status" => 1, "msg" => $msg, "userid" => $userId]);
                die;
            }
        } else {
            $acc_lock++;
            $query = "UPDATE users SET acc_lock = $acc_lock WHERE mobile = '$username'";
            mysqli_query($conn, $query);
            echo json_encode(["status" => 2, "msg" => 'Invalid password']);
            exit;
        }
    } else {
        echo json_encode(["status" => 4, "msg" => 'Username Does not Exist!']);
        exit;
    }

}
?>
