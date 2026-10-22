<?php
require_once '../config.php';
include('../smtp/PHPMailerAutoload.php');

if(isset($_POST["type"]) && $_POST["type"] == 'forgot'){
    $username = $_POST['mobile'];

    $query = "SELECT * FROM users WHERE mobile = '$username' OR email = '$username'";
    $run = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($run);

    if (mysqli_num_rows($run) > 0) {
        $otp = rand(100000, 999999);
        $sql = "UPDATE `users` SET `otp` = $otp WHERE `mobile` = '$username'";

        if (mysqli_query($conn, $sql)) {
            $toemail = $row["email"];
            $userId = $row["id"];
            $strmail = substr($toemail, -15);
            $strmobile = substr($username, -4);
            $msg = "Forgot OTP is sent to your Mobile No - XXXXXX$strmobile And Email XXXXXX$strmail";

            $mailmsg = '<html>
<head><title>Forgot OTP - UPIGateways</title></head>
<body>
 <p>Your One-Time Password (OTP): ' . htmlspecialchars($otp) . '</p>
</body>
</html>';

            $mail = new PHPMailer(); 
            $mail->IsSMTP(); 
            $mail->SMTPAuth = true; 
            $mail->SMTPSecure = 'tls'; 
            $mail->Host = "mail.pay.garudhub.in";
            $mail->Port = 587; 
            $mail->IsHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Username = "support@garudhub.in";
    	    $mail->Password = "Raushan7x@@@";
    	    $mail->SetFrom("support@garudhub.in","Greenpay");
    	    $mail->Subject = 'Forgot OTP Verfication';
            $mail->Body = $mailmsg;
            $mail->AddAddress($toemail);
            $mail->SMTPOptions = array('ssl'=>array(
                'verify_peer'=>false,
                'verify_peer_name'=>false,
                'allow_self_signed'=>false
            ));
            if(!$mail->Send()){
                echo $mail->ErrorInfo;
            } else {
                echo json_encode(["status"=> 1,"msg"=>'forgot success',"userid"=>$userId]);
                exit;
            }
        }
    } else {
        echo json_encode(["status"=> 4,"msg"=>'User Does not Exist!']);
        exit;
    }
}

if(isset($_POST["type"]) && $_POST["type"] == 'otp'){
    $id = mysqli_real_escape_string($conn,$_POST['id']);
    $otp = mysqli_real_escape_string($conn,$_POST['otp']);

    $query = $conn->query("SELECT * FROM users WHERE id='$id' AND otp ='$otp'");
    $row = $query->num_rows;

    if($row != 0){
        $conn->query("UPDATE users SET otp_attempts = '3', blocked_until = '' WHERE id = '$id'");
        echo '1';
    } else {
        echo '0';
    }
}

if(isset($_POST["type"]) && $_POST["type"] == 'change'){
    $id = mysqli_real_escape_string($conn,$_POST['id']);
    $npass = mysqli_real_escape_string($conn,$_POST['npass']);
    $cnpass = mysqli_real_escape_string($conn,$_POST['cnpass']);

    if($npass != $cnpass){
        echo json_encode(["status" => 2 , "msg"=>"Confirm Password Does Not Matched !" , "Status"=> false]);
        exit;
    }

    $pass = password_hash($npass, PASSWORD_BCRYPT);
    $sql = "UPDATE `users` SET `password` = '$pass' WHERE `id` = '$id'";
    $run = mysqli_query($conn, $sql);

    if($run){
        echo json_encode(["status" => 1 , "msg"=>"Password Change Successfully !" , "Status"=> false]);
        exit;
    } else {
        echo json_encode(["status" => 3 , "msg"=>"Failed to change password !" , "Status"=> false]);
        exit;
    }
}
?>
