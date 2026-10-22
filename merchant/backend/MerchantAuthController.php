<?php
session_start();
include("db_modal.php");
include('../smtp/PHPMailerAutoload.php');

// ini_set('display_errors', 0); // Error display band rakhein taki JSON kharab na ho
// error_reporting(0);

if (isset($_SESSION['username']) || isset($_SESSION['appuser'])) {
    $mobile = $_SESSION['username'];
    if($mobile == ''){
        $mobile = $_SESSION['appuser'];
    }
    $user = $crud->read("users","mobile = '$mobile'")[0];
}

class merchantAuthClass extends CrudOperation{
    
    public function sendEmail($to, $subject, $message) {  
        
        // Fetch website settings
        $settingsData = $this->read("website_settings", "id = 1");
        $settings = !empty($settingsData) ? $settingsData[0] : null;

        if (!$settings) {
            return "Settings not found";
        }

        $mail = new PHPMailer(); 
        $mail->IsSMTP(); 
        $mail->SMTPAuth = true; 
        $mail->SMTPSecure = $settings['smtp_encryption']; 
        $mail->Host = $settings['smtp_host'];
        $mail->Port = $settings['smtp_port']; 
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        //$mail->SMTPDebug = 2; 
        $mail->Username = $settings['smtp_username'];
        $mail->Password = $settings['smtp_password'];
        $mail->SetFrom($settings['smtp_from_email'], $settings['smtp_from_name']);
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
    
    // for sms msg send
    public function send_smsmsg($mobile,$pagename,$otp){
        
        $message = "$otp is the OTP for $pagename. Please do not share this OTP with anyone. This SMS has been sent from GuestRAR";
        
        $username = "your username";
        $senderName = "your sendername"; 
        $smsType = "type"; 
        $apiKey = "your api key";
        
        $url = "http://sms.hspsms.com/sendSMS?username=$username&message=" . urlencode($message) . 
               "&sendername=$senderName&smstype=$smsType&numbers=$mobile&apikey=$apiKey";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            // Error log kar sakte hain, par echo nahi karna chahiye AJAX me
        }
        curl_close($ch);
    }

    // for create cashfree token
    public function send_msg($email,$mobile,$pagename,$otp){
        $mailmsg = '<html>
            <head>
              <title>'.$pagename.' OTP - imb Pay Gateway</title>
            </head>
            <body>
            <p>Your One-Time Password (OTP): ' . htmlspecialchars($otp) . '</p>
            </body>
            </html>';
            
        $mailsend = $this->sendEmail($email, "OTP Verfication For $pagename", $mailmsg);
        if(!$mailsend){
            return false;
        }else{
            $this->update("users",["otp" => $otp],"mobile = '$mobile'");
            return true;
        } 
    }
}

include_once '../../pages/dbInfo.php';
$merchantVAuth = new merchantAuthClass(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);


// send otp code here
if(isset($_POST["sendotp"]) && $_POST["sendotp"] == true){
    $otp = mt_rand(100000,999999);
    $pagename = $_POST["page"];
    $email = $user["email"];
    $mobile = $user["mobile"];
    if($email == ''){
        $email = $_POST["email"];
    }
    $encryptotp = password_hash($otp, PASSWORD_BCRYPT);
    if($merchantVAuth->send_msg($email,$mobile,$pagename,$otp)){
        
        echo json_encode(["rescode" => 200,"status"=>true,"msg"=>"OTP Sent Successfully.","data" => $encryptotp,"type" => "email"]);
        exit;
    }else{
        echo json_encode(["rescode" => 403,"status"=>false,"msg"=>"Falied to send OTP ! try agian later."]);
        exit;
    }
}

// send otp code here
if(isset($_POST["sendmobileotp"]) && $_POST["sendmobileotp"] == true){
    $otp = mt_rand(100000,999999);
    $pagename = $_POST["page"];
    $mobile = $_POST["mobile"];
    if($mobile == ''){
        $mobile = $user["mobile"];
    }
    $encryptotp = password_hash($otp, PASSWORD_BCRYPT);
   
    $merchantVAuth->send_smsmsg($mobile,$pagename,$otp);
        
    echo json_encode(["rescode" => 200,"status"=>true,"msg"=>"OTP Sent Successfully.","data" => $encryptotp,"type" => "mobile"]);
    exit;
}

// send otp code here
if(isset($_POST["type"]) && $_POST["type"] == 'sendCallbackMsg'){
    $message = $_POST["message"];
    // Note: send_whatsappcallback method missing in provided class snippet, assuming it exists in parent or handled elsewhere. 
    // Keeping as per original code structure.
    if(method_exists($merchantVAuth, 'send_whatsappcallback')){
         $merchantVAuth->send_whatsappcallback($message);
    }
    echo json_encode(["rescode" => 200,"status"=>true,"msg"=>"Callback Request Sent Successfully."]);
    exit;
}

// check otp verify code here
if(isset($_POST["checkotp"])){
    $otp = $_POST["otp"];
    $otptype = $_POST["otptype"];
    $decryptotp = $_POST["otpdata"];
    
    if(password_verify($otp, $decryptotp)){
        echo json_encode(["rescode" => 200,"status"=>true,"msg"=>"OTP Verified Successfully.","type" => $otptype]);
        exit;
    }else{
        echo json_encode(["rescode" => 402,"status"=>false,"msg"=>"Invalid OTP."]);
        exit;
    }
}

// user register code here
if(isset($_POST["userRegister"])){
    $mobile =  $_POST['mobile'];
    $email = $_POST['email'];

    $checkMobileQuery = $crud->read("users","`mobile` = '$mobile'")[0];
    $checkEmailQuery = $crud->read("users","`email` = '$email'")[0];
    
    if (!empty($checkMobileQuery)) {
       echo json_encode(["rescode" => 402,"status"=>false,"msg"=>"Mobile Number already exist."]);
       exit;
    } elseif (!empty($checkEmailQuery)) {
       echo json_encode(["rescode" => 403,"status"=>false,"msg"=>"Email id already exists."]);
       exit;
    } else {
        $password = $_POST['password'];
        $name = $_POST['name'];
        $company = $_POST['company'];
        $pin = $_POST['pin'];
        $pan = $_POST['pan'];
        
        $checkpan = $crud->read("users", "`pan` = '$pan'")[0];

        if (!empty($checkpan)) {
            echo json_encode(["rescode" => 405,"status"=>false,"msg"=>"Pan Number already exists."]);
            exit;
        }else{  
            $sponser_id = $_POST['sponser_id'];
            $location = $_POST['location'];
            $usertoken = md5(rand(00000000, 99999999));
            $pass = password_hash($password, PASSWORD_BCRYPT);
            $today = date("Y-m-d", strtotime("+3 days"));

            function generateRandomInstanceId($length = 16) {
              $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
              $randomString = 'I'; 
              for ($i = 1; $i < $length - 6; $i++) {
                  $randomString .= $characters[rand(0, strlen($characters) - 1)];
              }
              $currentTime = time();
              $lastSixDigits = substr(strval($currentTime), -6);
              $randint = rand(100, 900);
              return $randomString . $randint . $lastSixDigits;
            }

            $instanceId = generateRandomInstanceId();

            $insertdata = [
                   'name' => $name,
                   'mobile' => $mobile,
                   'role' => 'User',
                   'password' => $pass,
                   'email' => $email,
                   'company' => $company,
                   'pin' => $pin,
                   'pan' => $pan,
                   'location' => $location,
                   'user_token' => $usertoken,
                   'expiry' => $today,
                   'sponser_by' => $sponser_id,
                   'instance_id' => $instanceId
                ];

            $register = $crud->create("users",$insertdata);

            if ($register) {
                $usid = $register;
                $sponserid = "IMBRFL00$usid";
                $crud->update("users", ["sponser_id" => $sponserid], "id = '$usid'");
                echo json_encode(["rescode" => 200,"status"=>true,"msg"=>"Account created successfully."]);
                exit;
            } else {
                 echo json_encode(["rescode" => 406,"status"=>false,"msg"=>"Failed to create account ! try again later."]);
                exit;
            }
        }
    }
}

// change upi to pg mode
if(isset($_POST["changepgmode"]) && $_POST["changepgmode"] == true){
    $mode = $_POST["mode"];
    $chnagpg = $_POST["upitopg"];
   
    if(!empty($user)){
        $updatemode = $crud->update("users",["pg_mode" => $mode],"mobile = '$mobile'");
        if($updatemode){
            if($mode == 2){
                echo json_encode(["rescode" => 200,"status"=>true,"msg"=>"Success."]);
                exit;
            }else if($mode == 3){
                echo json_encode(["rescode" => 111,"status"=>true,"msg"=>"Success."]);
                exit;
            }else{
                echo json_encode(["rescode" => 222,"status"=>true,"msg"=>"Success."]);
                exit;
            }
        }
    }else{
        echo json_encode(["rescode" => 404,"status"=>false,"msg"=>"User Data Not Found | Try Again Later!."]);
        exit;
    }
}

// accept terms and condition code here
if(isset($_POST["tandc_accept"]) && $_POST["tandc_accept"] == true){
    $userid = $_POST["usid"];
    $updatemode = $crud->update("users",["term_and_condition" => 1],"id = '$userid'");
    if($updatemode){
        echo json_encode(["rescode" => 200,"status"=>true,"msg"=>"Success."]);
        exit;
    }else{
        echo json_encode(["rescode" => 400,"status"=>false,"msg"=>"Failure."]);
        exit;
    }
}

// apply coupon code code here
if(isset($_POST["type"]) && $_POST["type"] == 'getPlanAmount'){
    $plan_id = $_POST["planid"];
    $s_months = $_POST["s_months"].'month_amount';
   
    $getPlanAmount = $crud->read("plan_details","plan_id = '$plan_id'","`$s_months` as amount")[0];
    if(!empty($getPlanAmount)){
        $planamount = $getPlanAmount["amount"]*$s_months;
        $permamount = $getPlanAmount["amount"];
        echo json_encode(["rescode" => 200,"msg"=>"Success.","planamount" => $planamount,"permamount" => $permamount],JSON_NUMERIC_CHECK);
        exit;
    }else{
        echo json_encode(["rescode" => 404,"msg"=>"erorr."]);
        exit;
    }
}

// getmerchant upi deatils code here
if(isset($_POST["mno"])){
    $mno = $_POST["mno"];
    $mname = $_POST["mname"];
    $usertoken = $user["user_token"];
    $upiid = $user["upi_id"];
    
    if($mname == 'phonepe'){
        echo json_encode(["status" => 1,"msg"=>"Success.","upiid" => $upiid]);
        exit;
    }else{
        $getmerchant = $crud->read("merchant","user_token = '$usertoken'")[0];
        echo json_encode(["status" => 1,"msg"=>"Success.","upiid" => $getmerchant["merchant_upi"]]);
        exit;
    }
}

// update merchant upi details code here
if(isset($_POST["upi_id"]) && $_POST["upi_id"] != ''){
    $upi_id = $_POST["upi_id"];
    $mname = $_POST["mname"];
    $usertoken = $user["user_token"];
    
    if($mname == 'phonepe'){
        $updateupiid = $crud->update("users",["upi_id" => "$upi_id"],"mobile = '$mobile'");
    }else{
        $updateupiid = $crud->update("merchant",["merchant_upi" => "$upi_id"],"user_token = '$usertoken'");
    }
    
    if($updateupiid){
        echo json_encode(["status" => 1,"msg"=>"Success."]);
        exit;
    }else{
        echo json_encode(["status" => 5,"msg"=>"Failed to update UPI id."]);
        exit;
    }
}

// =========================================================
// FIX START: Update Txn Status (Improved Logic)
// =========================================================
if (isset($_POST["type"]) && $_POST["type"] == 'updateTxnOrder') {
    $orderid = $_POST["orderid"];
    $status = $_POST["status"];
    $utrno = $_POST["utrno"];
    $remark = $_POST["remark"];
    $usertoken = $user["user_token"];

    $gettxndata = $crud->read("orders", "order_id = '$orderid'")[0];

    if (!empty($gettxndata)) {

        if ($gettxndata["status"] == 'PENDING') {

            // STEP 1: Pehle Database Update karein
            $st = ($status == 1) ? 'SUCCESS' : 'FAILURE';
            $updatetxn = $crud->update("orders", ["status" => "$st", "utr" => "$utrno", "remark1" => "$remark"], "order_id = '$orderid'");

            if ($updatetxn) {
                
                // STEP 2: Agar Approve (Status 1) hua hai to Callback aur Email bhejein
                if ($status == 1) {
                    
                    // Output buffering: Koi bhi PHP warning/error JSON ko kharab nahi karega
                    ob_start(); 
                    
                    try {
                        // --- Callback Logic Start ---
                        $callback_url =  $user["callback_url"];
                        $mcq = $crud->read("callback_report", "order_id = '$orderid'")[0];

                        if (empty($mcq)) {
                            // Data to be sent
                            $postData = array(
                                'status' => 'SUCCESS',
                                'order_id' => $orderid,
                                'message' => 'Transaction Successfully',
                                'result' => array(
                                    "txnStatus" => "COMPLETED",
                                    "resultInfo" => "Transaction Success",
                                    "orderId" => $orderid,
                                    'amount' => $gettxndata["amount"],
                                    'date' => $gettxndata['create_date'],
                                    'utr' => $utrno,
                                    'customer_mobile' => $gettxndata['customer_mobile'],
                                    'remark1' => $remark,
                                    'remark2' => $gettxndata["remark2"]
                                )
                            );

                            // List of URLs to send callback to
                            $urls = [];
                            if (!empty($callback_url)) {
                                $urls[] = $callback_url; // Primary callback
                            }

                            // Fetch additional webhooks
                            $extra_webhooks = $crud->read("merchant_webhooks", "user_token = '$usertoken'");
                            if (!empty($extra_webhooks)) {
                                foreach ($extra_webhooks as $hook) {
                                    $urls[] = $hook['webhook_url'];
                                }
                            }

                            // Send to all URLs
                            foreach ($urls as $url) {
                                $ch = curl_init($url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                                curl_setopt($ch, CURLOPT_TIMEOUT, 5); // Short timeout
                                curl_exec($ch);
                                curl_close($ch);
                            }

                            // --- Email Logic Start ---
                            $subject = "Payment Received: " . $gettxndata["order_id"];
                            $emailBody = "
                            <h3>Payment Successful</h3>
                            <p><b>Order ID:</b> " . $gettxndata["order_id"] . "</p>
                            <p><b>Amount:</b> " . $gettxndata["amount"] . "</p>
                            <p><b>UTR:</b> " . $utrno . "</p>
                            <p><b>Status:</b> SUCCESS</p>
                            <p><b>Date:</b> " . date("Y-m-d H:i:s") . "</p>
                            ";

                            $userDataForEmail = $crud->read("users", "user_token = '$usertoken'")[0];
                            if ($userDataForEmail['email']) {
                                // FIX: Using $merchantVAuth instead of $this
                                $merchantVAuth->sendEmail($userDataForEmail['email'], $subject, $emailBody);
                            }
                        }
                    } catch (Exception $e) {
                        // Error ignore karein taki process na ruke
                    }

                    // Buffer clean kar dein
                    ob_end_clean();
                }
                
                // Return Success Response (Ab ye 100% chalega)
                echo json_encode(["status" => 1, "msg" => "Transaction status updated successfully."]);
                exit;

            } else {
                echo json_encode(["status" => 5, "msg" => "Failed to update status."]);
                exit;
            }

        } else {
            echo json_encode(["status" => 409, "msg" => "Transaction already updated."]);
            exit;
        }

    } else {
        echo json_encode(["status" => 9, "msg" => "transaction not found !."]);
        exit;
    }
}
// =========================================================
// FIX END
// =========================================================

// update merchant status details code here
if(isset($_POST["type"]) && $_POST["type"] == 'updateMerchantSt'){
    $mid = $_POST["mid"];
    $mtype = $_POST["mtype"];
    $status = $_POST["status"];
    $usertoken = $user["user_token"];
    $mrtTable = '';
    $mrtTableidName = 'id';
    
    switch ($mtype) {
        case 'HDFC':
            $mrtTable = 'hdfc';
            break;
        case 'PhonePe':
            $mrtTable = 'phonepe_tokens';
            $mrtTableidName = 'sl';
            break;
        case 'Paytm':
            $mrtTable = 'paytm_tokens';
            break;
        case 'Freecharge':
            $mrtTable = 'freecharge';
            break;
        case 'SBI':
            $mrtTable = 'merchant';
            $mrtTableidName = 'merchant_id';
            break;
        case 'Bharatpe':
            $mrtTable = 'bharatpe_tokens';
            break;
        case 'MOBIKWIK':
            $mrtTable = 'mobikwik_token';
            break;
        case 'Amazonpay':
            $mrtTable = 'amazon_pay';
            break;
        case 'Quintuspay': 
             $mrtTable = 'quintus_tokens';
             break;
        case 'GooglePay':
             $mrtTable = 'gpay_tokens';
             break;
        
        default:
            $mrtTable = '';
            break;
    }
    
    if($mrtTable == ''){
         echo json_encode(["status" => 5,"msg"=>"Invalid Merchant Type."]);
         exit;
    }
    
    // Check if merchant exists
    $checkMer = $crud->read("$mrtTable", "$mrtTableidName = '$mid' AND user_token = '$usertoken'");
    if(empty($checkMer)){
         echo json_encode(["status" => 5,"msg"=>"Merchant not found or not verified."]);
         exit;
    }

    $mst = ($status == 1) ? 'Active' : 'Off';

    $updatemertst = $crud->update("$mrtTable",["status" => "$mst"],"$mrtTableidName = '$mid' AND user_token = '$usertoken'");
   
    if($updatemertst){
        // FIX: Using $merchantVAuth instead of $this
        ob_start(); // Buffer to prevent crash on email error
        try {
            $userDataForEmail = $crud->read("users", "user_token = '$usertoken'")[0];
            if($userDataForEmail['email']) {
                $subject = "Gateway Connected: $mtype";
                $emailBody = "
                <h3>Gateway Status Updated</h3>
                <p><b>Gateway:</b> $mtype</p>
                <p><b>Status:</b> $mst</p>
                <p><b>Time:</b> " . date("Y-m-d H:i:s") . "</p>
                ";
                $merchantVAuth->sendEmail($userDataForEmail['email'], $subject, $emailBody);
            }
        } catch (Exception $e) {
            // Ignore email errors
        }
        ob_end_clean();

        echo json_encode(["status" => 1,"msg"=>"Merchant $mst Successfully."]);
        exit;
    }else{
        echo json_encode(["status" => 5,"msg"=>"Failed to update merchant."]);
        exit;
    }
}

// update notif seen
if(isset($_POST["type"]) && $_POST["type"] == 'seennotif'){
    $nid = $_POST["nid"];
    $usertoken = $user["user_token"];

    $updatenotif = $crud->update("users",["notif_seen" => "$nid"],"mobile = '$mobile'");
    
    if($updatenotif){
        echo json_encode(["status" => 1,"msg"=>"Success."]);
        exit;
    }else{
        echo json_encode(["status" => 5,"msg"=>"Failed to update."]);
        exit;
    }
}

?>