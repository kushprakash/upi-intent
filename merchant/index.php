<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Favicons -->
    <link href="<?= $site_url ?>/<?= $website_settings['favicon'] ?>" rel="icon">
    <link href="<?= $site_url ?>/<?= $website_settings['favicon'] ?>" rel="apple-touch-icon">
    
    <title><?= $website_settings['title'] ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- reCAPTCHA removed -->
    
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
      
      a {
        text-decoration: none !important;
        color: var(--primary-color);
      }
      
      a:hover {
        color: var(--secondary-color);
      }
      
      .logo {
        max-width: 78%;
        height: auto;
        margin-bottom: 20px;
        text-align: center;
      }
      
      .login-container {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-top: 20px;
      }
      
      .login-head {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 25px;
        text-align: center;
      }
      
      .form-control {
        height: 45px;
        border-radius: 5px !important;
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
        border-radius: 5px !important;
      }
      
      .btn-primary:hover {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
      }
      
      .card {
        border-radius: 5px !important;
        border: none;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
      }
      
      #otpformbox {
        display: none;
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
        border-radius: 5px !important;
      }
      
      .otp-input:focus {
        border-color: var(--primary-color);
        outline: none;
        box-shadow: 0 0 0 0.25rem rgba(44, 62, 239, 0.25);
      }
      
      .login-animation {
        width: 100%;
        height: 300px;
        margin: 0 auto;
      }
      
      .login-features {
        margin-top: 30px;
      }
      
      .login-features p {
        color: #666;
        font-size: 15px;
        line-height: 1.6;
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
      
      .stay-signed {
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      
      @media (max-width: 992px) {
        .login-animation-container {
          display: none;
        }
        
        .login-container {
          max-width: 500px;
          margin: 20px auto;
        }
      }
    </style>
  </head>
  
  <body>
    <div id="loading_ajax">
      <div class="simple-spinner">
        <span></span>
      </div>
    </div>
    
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-lg-10">
          
          
          <div class="row g-4">
            <!-- Left Column (Animation & Features) -->
            <div class="col-lg-6 login-animation-container">
              <div id="login_anim" class="login-animation"></div>
              
              <div class="login-features">
                <h4 class="text-center mb-3">Fastest API For UPI Payments</h4>
                <p class="text-center">Accept payments from your customers through our easy-to-use QR code service, with 0% transaction fees. So Go and Use Dragons Pay ASAP.</p>
                
                <div class="text-center mt-4">
                  <a href="https://dragonspay.com/demo" class="btn btn-outline-primary me-2">Check Demo</a>
                  <a href="#" class="btn btn-primary">Contact Sales</a>
                </div>
              </div>
            </div>
            
            <!-- Right Column (Login Forms) -->
            <div class="col-lg-6">
     <div class="login-container">
                <!-- Login Form -->
                <div id="loginformbox">
                  <form class="login-form" method="POST" id="login_form">
                    <h3 class="login-head"><i class="fas fa-user me-2"></i>SIGN IN</h3>
                    
                    <div class="mb-3">
                      <label for="mobile" class="form-label">Mobile Number</label>
                      <input class="form-control" id="mobile" name="username" type="number" 
                             placeholder="Enter 10-digit mobile number" minlength="10" maxlength="10" 
                             oninput="this.value = this.value.replace(/[^0-9]/g, '');" required>
                    </div>
                    
                    <div class="mb-3">
                      <label for="passwordlogin" class="form-label">Password</label>
                      <input class="form-control" type="password" id="passwordlogin" 
                             name="password" placeholder="Enter your password" required>
                    </div>
                    
                    <div class="mb-3 stay-signed">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="staySigned">
                        <label class="form-check-label" for="staySigned">Stay Signed in</label>
                      </div>
                      <a href="forgotpassword" class="text-primary">Forgot Password?</a>
                    </div>
                    
                    <!-- Google reCAPTCHA -->
                    <!-- <div class="g-recaptcha mb-3" data-sitekey="<?= $website_settings['recaptcha_site_key'] ?>"></div> -->
                    
                    <button class="btn btn-primary w-100 mb-3" type="submit" name="submit">
                      <i class="fas fa-sign-in-alt me-2"></i>SIGN IN
                    </button>
                    
                    <div class="text-center pt-2">
                      <p class="mb-0">Don't have an account? <a href="register">Register</a></p>
                    </div>
                  </form>
                </div>
                
                <!-- OTP Form -->
                <div id="otpformbox">
                  <form class="login-form" method="POST" id="loginotpform">
                    <input type="hidden" name="useridmodal" id="useridmodal">
                    <h3 class="login-head">Verify Your Account</h3>
                    <p class="text-center">We have sent a verification code to your mobile. Please enter the code below to verify your account.</p>
                    
                    <div class="otp-inputs">
                      <input type="text" maxlength="1" class="otp-input form-control" id="otp1" autocomplete="off">
                      <input type="text" maxlength="1" class="otp-input form-control" id="otp2" autocomplete="off">
                      <input type="text" maxlength="1" class="otp-input form-control" id="otp3" autocomplete="off">
                      <input type="text" maxlength="1" class="otp-input form-control" id="otp4" autocomplete="off">
                      <input type="text" maxlength="1" class="otp-input form-control" id="otp5" autocomplete="off">
                      <input type="text" maxlength="1" class="otp-input form-control" id="otp6" autocomplete="off">
                    </div>
                    
                    <button class="btn btn-primary w-100 mb-3" type="submit" name="submit">
                      <i class="fas fa-check-circle me-2"></i>VERIFY OTP
                    </button>
                    
                    <p class="text-center resend-otp pt-2">
                      Didn't receive the code? 
                      <span id="resendOtpBtn" class="disabled text-primary">Resend OTP</span> 
                      in <span id="timer">30</span>s
                    </p>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Footer -->
    <footer class="mt-auto py-3 text-center">
      <div class="container">
        <p class="mb-0 text-muted">© 2023 Dragons Pay. All rights reserved.</p>
      </div>
    </footer>
    
    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.9.6/lottie.min.js"></script>
    <script src="js/login.js"></script>
    
    <!-- Custom Scripts -->
    <script>
      // Initialize Lottie Animation
      document.addEventListener('DOMContentLoaded', function() {
        lottie.loadAnimation({
          container: document.getElementById('login_anim'),
          renderer: 'svg',
          loop: true,
          autoplay: true,
          path: 'assets/imbpg_img/loginanimationpage.json'
        });
        
        // OTP input auto-focus
        const otpInputs = document.querySelectorAll('.otp-input');
        otpInputs.forEach((input, index) => {
          input.addEventListener('input', (e) => {
            if (e.target.value.length === 1 && index < otpInputs.length - 1) {
              otpInputs[index + 1].focus();
            }
          });
          
          input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && index > 0 && !e.target.value) {
              otpInputs[index - 1].focus();
            }
          });
        });
        
        // OTP timer functionality
        let timeLeft = 30;
        const timerElement = document.getElementById('timer');
        const resendBtn = document.getElementById('resendOtpBtn');
        
        const timer = setInterval(() => {
          timeLeft--;
          timerElement.textContent = timeLeft;
          
          if (timeLeft <= 0) {
            clearInterval(timer);
            resendBtn.classList.remove('disabled');
            timerElement.style.display = 'none';
          }
        }, 1000);
        
        resendBtn.addEventListener('click', function() {
          if (!this.classList.contains('disabled')) {
            // Add resend OTP logic here
            timeLeft = 30;
            timerElement.textContent = timeLeft;
            timerElement.style.display = 'inline';
            this.classList.add('disabled');
            
            // Show success message
            Swal.fire({
              icon: 'success',
              title: 'OTP Resent',
              text: 'A new OTP has been sent to your mobile number',
              timer: 2000,
              showConfirmButton: false
            });
            
            // Restart timer
            const newTimer = setInterval(() => {
              timeLeft--;
              timerElement.textContent = timeLeft;
              
              if (timeLeft <= 0) {
                clearInterval(newTimer);
                resendBtn.classList.remove('disabled');
                timerElement.style.display = 'none';
              }
            }, 1000);
          }
        });
      });
      
  
    </script>
  </body>
</html>