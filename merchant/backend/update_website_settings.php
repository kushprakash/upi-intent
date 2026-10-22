<?php
session_start();
include "../config.php";

if (!isset($_SESSION['username'])) {
    header("Location: ../index");
    exit();
}

$mobile = $_SESSION['username'];
$user = mysqli_query($conn, "SELECT role FROM users WHERE mobile = '$mobile'");
$userdata = mysqli_fetch_array($user);

// Admin Role Check
if ($userdata['role'] != 'Admin') {
    header("Location: ../index");
    exit();
}

if (isset($_POST['update_settings'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $contact_email = mysqli_real_escape_string($conn, $_POST['contact_email']);
    $contact_phone = mysqli_real_escape_string($conn, $_POST['contact_phone']);
    $contact_address = mysqli_real_escape_string($conn, $_POST['contact_address']);
    $recaptcha_site_key = mysqli_real_escape_string($conn, $_POST['recaptcha_site_key']);
    $recaptcha_secret_key = mysqli_real_escape_string($conn, $_POST['recaptcha_secret_key']);
    $smtp_host = mysqli_real_escape_string($conn, $_POST['smtp_host']);
    $smtp_username = mysqli_real_escape_string($conn, $_POST['smtp_username']);
    $smtp_password = mysqli_real_escape_string($conn, $_POST['smtp_password']);
    $smtp_port = mysqli_real_escape_string($conn, $_POST['smtp_port']);
    $smtp_encryption = mysqli_real_escape_string($conn, $_POST['smtp_encryption']);
    $smtp_from_email = mysqli_real_escape_string($conn, $_POST['smtp_from_email']);
    $smtp_from_name = mysqli_real_escape_string($conn, $_POST['smtp_from_name']);

    // Handle Logo Upload
    $logo_path = $website_settings['logo']; 
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $allowed = ['png', 'jpg', 'jpeg', 'webp'];
        $filename = $_FILES['logo']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
             $new_filename = "logo_" . time() . "." . $ext;
             // Correct path for merchant/backend relative to root of images
             $target_dir = "../../newassets/images/"; 
             if (!file_exists($target_dir)) {
                 mkdir($target_dir, 0777, true);
             }
             if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_dir . $new_filename)) {
                 $logo_path = "newassets/images/" . $new_filename;
             }
        }
    }

    // Handle Favicon Upload
    $favicon_path = $website_settings['favicon'];
    if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] == 0) {
        $allowed = ['png', 'jpg', 'jpeg', 'ico'];
        $filename = $_FILES['favicon']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
         if (in_array($ext, $allowed)) {
             $new_filename = "favicon_" . time() . "." . $ext;
             $target_dir = "../../newassets/images/"; 
             if (!file_exists($target_dir)) {
                 mkdir($target_dir, 0777, true);
             }
             if (move_uploaded_file($_FILES['favicon']['tmp_name'], $target_dir . $new_filename)) {
                 $favicon_path = "newassets/images/" . $new_filename;
             }
        }
    }

    $sql = "UPDATE website_settings SET 
            title = '$title',
            logo = '$logo_path',
            favicon = '$favicon_path',
            contact_email = '$contact_email',
            contact_phone = '$contact_phone',
            contact_address = '$contact_address',
            recaptcha_site_key = '$recaptcha_site_key',
            recaptcha_secret_key = '$recaptcha_secret_key',
            smtp_host = '$smtp_host',
            smtp_username = '$smtp_username',
            smtp_password = '$smtp_password',
            smtp_port = '$smtp_port',
            smtp_encryption = '$smtp_encryption',
            smtp_from_email = '$smtp_from_email',
            smtp_from_name = '$smtp_from_name'
            WHERE id = 1";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Settings updated successfully!'); window.location.href='../website_settings';</script>";
    } else {
        echo "<script>alert('Error updating settings: " . mysqli_error($conn) . "'); window.location.href='../website_settings';</script>";
    }

} else {
    header("Location: ../website_settings");
}
?>
