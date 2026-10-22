<?php
require_once 'merchant/config.php';
// Load global settings
global $website_settings;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $phone = htmlspecialchars(trim($_POST["phone"]));
    $supportType = htmlspecialchars(trim($_POST["supportType"]));

    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host = $website_settings['smtp_host']; 
        $mail->SMTPAuth = true;
        $mail->Username = $website_settings['smtp_username'];
        $mail->Password = $website_settings['smtp_password'];       
        $mail->SMTPSecure = $website_settings['smtp_encryption'];
        $mail->Port = $website_settings['smtp_port'];

        // Sender & Recipient
        $mail->setFrom($website_settings['smtp_from_email'], $website_settings['smtp_from_name']);
        $mail->addAddress('admin@pay.garudhub.in'); // Admin email

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'New Consultation Request';
        $mail->Body = "
            <strong>Name:</strong> $name<br>
            <strong>Email:</strong> $email<br>
            <strong>Phone:</strong> $phone<br>
            <strong>Support Type:</strong> $supportType
        ";

        $mail->send();
        echo "Your request has been sent successfully.";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
