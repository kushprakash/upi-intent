<?php include "config.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>imb Pay - Forgot Password</title>
    
    <!-- Favicons -->
    <link href="<?= $site_url ?>/newassets/images/favicon.png" rel="icon">
    <link href="<?= $site_url ?>/newassets/images/favicon.png" rel="apple-touch-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-color: #2c3eef;
            --secondary-color: #1a237e;
            --accent-color: #00c853;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
            --light-text: #7f8c8d;
        }
        
        body {
            font-family: 'DM Sans', sans-serif;
            background: #f2f3f8 !important;
            color: var(--dark-text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo img {
            max-width: 205px;
            max-height: 150px;
        }
        
        .auth-container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        
        .auth-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        .auth-heading {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .auth-icon {
            margin-right: 10px;
        }
        
        .form-control {
            height: 45px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(44, 62, 239, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px;
            font-weight: 500;
            border-radius: 5px;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .otp-inputs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 10px;
        }
        
        .otp-input {
            width: 45px;
            height: 45px;
            font-size: 18px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .otp-input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 0.25rem rgba(44, 62, 239, 0.25);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 20px;
        }
        
        #resendOtpBtn {
            cursor: pointer;
            font-weight: 500;
        }
        
        #resendOtpBtn.disabled {
            color: var(--light-text) !important;
            cursor: not-allowed;
        }
        
        .simple-spinner {
            width: 30px;
            height: 30px;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        
        .simple-spinner span {
            display: block;
            width: 60px;
            height: 60px;
            border: 3px solid transparent;
            border-radius: 50%;
            border-right-color: rgba(255, 255, 255, 0.7);
            animation: spinner-anim 0.8s linear infinite;
        }
        
        @keyframes spinner-anim {
            from { transform: rotate(0); }
            to { transform: rotate(360deg); }
        }
        
        #loading_ajax {
            display: none;
            background: rgba(0, 0, 0, 0.4);
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            top: 0;
            z-index: 9998;
        }
        
        /* Hide all forms initially */
        #otpformbox, #changepassformbox {
            display: none;
        }
        
        /* Show active form */
        .active-form {
            display: block;
        }
    </style>
</head>
<body>
    <div id="loading_ajax">
        <div class="simple-spinner">
            <span></span>
        </div>
    </div>

    <div class="container d-flex flex-column justify-content-center py-5">
        <div class="logo">
            <img src="<?= $site_url ?>/newassets/images/Logo.png" alt="imb Pay Logo">
        </div>
        
        <div class="auth-container">
            <!-- Forgot Password Form -->
            <div class="auth-card active-form" id="forgotformbox">
                <form method="POST" id="forgot_form">
                    <h3 class="auth-heading">
                        <i class="fas fa-lock auth-icon"></i>FORGOT PASSWORD
                    </h3>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Mobile Number/Email ID</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Enter your mobile number or email" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Send OTP
                        </button>
                    </div>
                    
                    <div class="auth-footer">
                        <p class="mb-0">Remember your password? <a href="index">Login here</a></p>
                    </div>
                </form>
            </div>
            
            <!-- OTP Verification Form -->
            <div class="auth-card" id="otpformbox">
                <form method="POST" id="forgototp_form">
                    <input type="hidden" name="useridmodal" id="useridmodal">
                    <h3 class="auth-heading">Verify Your Account</h3>
                    <p class="text-center">We have sent a verification code to your registered email/mobile. Please enter the code below.</p>
                    
                    <div class="otp-inputs">
                        <input type="text" maxlength="1" class="otp-input form-control" id="otp1" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input form-control" id="otp2" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input form-control" id="otp3" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input form-control" id="otp4" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input form-control" id="otp5" autocomplete="off">
                        <input type="text" maxlength="1" class="otp-input form-control" id="otp6" autocomplete="off">
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle me-2"></i>Verify OTP
                        </button>
                    </div>
                    
                    <p class="text-center resend-otp pt-3">
                        Didn't receive the code? 
                        <span id="resendOtpBtn" class="disabled text-primary">Resend OTP</span> 
                        in <span id="timer">30</span>s
                    </p>
                </form>
            </div>
            
            <!-- Change Password Form -->
            <div class="auth-card" id="changepassformbox">
                <form method="POST" id="changepass_form">
                    <h3 class="auth-heading">
                        <i class="fas fa-key auth-icon"></i>CHANGE PASSWORD
                    </h3>
                    
                    <div class="mb-3">
                        <label for="npass" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="npass" name="npass" 
                               placeholder="Enter new password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cnpass" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="cnpass" name="cnpass" 
                               placeholder="Confirm new password" required>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Password
                        </button>
                    </div>
                    
                    <div class="auth-footer">
                        <p class="mb-0">Remember your password? <a href="index">Login here</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/forgot.js"></script>

</body>
</html>