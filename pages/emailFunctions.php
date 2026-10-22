<?php
// Centralized Email Function
// Requires PHPMailer and Database Connection

if (file_exists(__DIR__ . '/../smtp/PHPMailerAutoload.php')) {
    require_once __DIR__ . '/../smtp/PHPMailerAutoload.php';
} elseif (file_exists(__DIR__ . '/../../smtp/PHPMailerAutoload.php')) {
    require_once __DIR__ . '/../../smtp/PHPMailerAutoload.php';
}

function systemSendEmail($to, $subject, $message, $conn) {
    if (!$conn) {
        return "Database connection required for email settings.";
    }

    // Fetch SMTP Settings
    $sql = "SELECT * FROM website_settings WHERE id = 1";
    $result = mysqli_query($conn, $sql);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        return "Website settings not found.";
    }

    $settings = mysqli_fetch_assoc($result);

    $mail = new PHPMailer(); 
    $mail->IsSMTP(); 
    $mail->SMTPAuth = true; 
    $mail->SMTPSecure = $settings['smtp_encryption']; 
    $mail->Host = $settings['smtp_host'];
    $mail->Port = $settings['smtp_port']; 
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = $settings['smtp_username'];
    $mail->Password = $settings['smtp_password'];
    $mail->SetFrom($settings['smtp_from_email'], $settings['smtp_from_name']);
    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AddAddress($to);
    $mail->SMTPOptions = array('ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => false
    ));

    if(!$mail->Send()){
        return $mail->ErrorInfo;
    } else {
        return true;
    }
}
?>
