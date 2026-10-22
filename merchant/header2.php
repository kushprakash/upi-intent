<?php
// ini_set('session.gc_maxlifetime', 86400);
// session_set_cookie_params(86400);

session_start();
include "config.php";

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// Set session expiration time (1 day)
$session_lifetime = 86400; // 24 hours in seconds
$planname= '';
$plancmt= 1;
// Check if the session is expired
    if (time() - $_SESSION['login_time'] > $session_lifetime) {
        // If the session is expired, destroy the session and redirect to login
        session_unset();
        session_destroy();
        header("Location: index");
        exit();
    }

if (isset($_SESSION['username'])) {
    $mobile = $_SESSION['username'];
    $user = "SELECT * FROM users WHERE mobile = '$mobile'";
    $uu = mysqli_query($conn, $user);
    $userdata = mysqli_fetch_array($uu);
    
     if($userdata["pg_mode"] == 2){
        echo '<script> window.location.href = "../Cgateway Paypro/dashboard"; </script>';
        exit;
     } 
     if($userdata["pg_mode"] == 3){
        echo '<script> window.location.href = "../Cgateway Paypg/dashboard"; </script>';
        exit;
     } 
    
     $userid = $userdata["id"];
     $notifid = $userdata["notif_seen"];
     
     $tdate = date("Y-m-d");
    $todayallpayment = $conn->query("SELECT COUNT(`id`) as amt FROM `orders` WHERE `user_id` = '{$userdata["id"]}' AND `status` = 'SUCCESS' AND DATE(`create_date`) = '$tdate' AND user_mode = '1'")->fetch_assoc();
    $todaysuccesspayment = $conn->query("SELECT SUM(`amount`) as amt FROM `orders` WHERE `user_id` = '{$userdata["id"]}' AND `status` = 'SUCCESS' AND DATE(`create_date`) = '$tdate' AND user_mode = '1'")->fetch_assoc();
    $todaypendingpayment = $conn->query("SELECT SUM(`amount`) as amt FROM `orders` WHERE `user_id` = '{$userdata["id"]}' AND `status` = 'PENDING' AND DATE(`create_date`) = '$tdate' AND user_mode = '1'")->fetch_assoc();
    
    $todayfail = $conn->query("SELECT SUM(`amount`) as amt FROM `orders` WHERE `user_id` = '{$userdata["id"]}' AND `status` = 'FAILURE' AND DATE(`create_date`) = '$tdate' AND user_mode = '1'")->fetch_assoc();
    
    $todaysettlement = $conn->query("SELECT SUM(`amount`) as amt FROM `settlement` WHERE `userid` = '{$userdata["id"]}' AND `status` = 'Success' AND DATE(`date`) = '$tdate'")->fetch_assoc();
    
    
    if($userdata["hdfc_connected"] == 'Yes' || $userdata["phonepe_connected"] == 'Yes' || $userdata["paytm_connected"] == 'Yes' || $userdata["sbi_connected"] == 'Yes' || $userdata["bharatpe_connected"] == 'Yes' || $userdata["googlepay_connected"] == 'Yes'){
    $connected = 'Yes';
}else{
    $connected = 'No';
}

if($userdata["expiry"] > date("Y-m-d")){
    
// Create a DateTime object for today's date
$today = new DateTime();

// Create a DateTime object for the future date
$futureDate = new DateTime($userdata["expiry"]);

// Calculate the difference between the two dates
$interval = $today->diff($futureDate);

// Get the number of days as an integer
$daysDifference = $interval->days;
// $plan_expirydays = "Plan Expired In : $daysDifference Days";
}else{
$plan_expirydays = "Your plan is expired";
}

switch($userdata["plan_id"]){
    case 1:
    $planname = 'Premium Plan';
    $plancmt = 1;
    break;
    case 5:
    $planname = 'Business Plan';
    $plancmt = 5;
    break;
    case 6:
    $planname = 'Enterprise  Elite';
    $plancmt = 10;
    break;
    case 7:
    $planname = 'Enterprise Pro';
    $plancmt = 25;
    break;
    case 8:
    $planname = 'Enterprise Ultimate';
    $plancmt = 50;
    break;
    default:
    $planname = '';
    $plancmt = 0;
}

 $notifcount = $conn->query("SELECT id, COUNT(`id`) as count FROM `notification` WHERE id > '$notifid'")->fetch_assoc();
 $getnotifid = $conn->query("SELECT id FROM `notification` ORDER BY id DESC")->fetch_assoc();
 $getuserPlanData = $conn->query("SELECT * FROM `subscription_report` WHERE user_id = '$userid' ORDER BY id DESC LIMIT 1")->fetch_assoc();

?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta name="description" content="<?= $website_settings['title'] ?> provides secure and efficient UPI payment gateway services and QR code payment solutions. Simplify your transactions with our reliable and user-friendly platform.">
    <meta name="keyword" content="<?= $website_settings['title'] ?>, UPI payment gateway, QR code payment solutions, secure online payments, digital payment solutions, UPI services, QR code transactions, online payment gateway, payment processing, merchant services">
   
    <!-- Open Graph Meta-->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= $website_settings['title'] ?>">
    <meta property="og:title" content="<?= $website_settings['title'] ?> - Secure UPI and QR Code Payment Solutions">
    <meta property="og:url" content="#">
    <meta property="og:image" content="#">
    <meta property="og:description" content="<?= $website_settings['title'] ?> provides secure and efficient UPI payment gateway services and QR code payment solutions. Simplify your transactions with our reliable and user-friendly platform.">
    <title><?= $website_settings['title'] ?> | Merchant Dashboard</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css" href="css/main.css?v=<?= time() ?>">
    <!-- Font-icon css-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
      <link rel="icon" type="image/png" href="<?= $site_url ?>/<?= $website_settings['favicon'] ?>">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script type="text/javascript">if(window.history.replaceState){window.history.replaceState(null,null,window.location.href);}</script>
    <script>var siteUrl = "<?= $site_url ?>";</script>
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 
 <style>
  .icon-circle {
    width: 60px;
    height: 60px;
    background: #0c254c;
    color: #fff;
    font-size: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
</style>
 <style>
 
        #emoji-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #25a6a1;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80px; height: 80px;
            cursor: pointer; z-index: 1000;
            transition: transform 0.3s, background-color 0.3s;
            display: flex; align-items: center; justify-content: center;
        }
        #emoji-widget img { width: 120%; height: 120%; border-radius: 50%; }
        #emoji-widget:hover { transform: scale(1.2); background-color: #ff7f7f; }
    </style>
 
  
     <script type="text/javascript">
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

    
    <style>
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
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(360deg);
  }
}

div#loading_ajax {
    display:block;
    background: rgba(0, 0, 0, 0.4);
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    top: 0;
    z-index: 9998;
}
        </style>
        
        
        
</head>

  

<style>
body {
  line-height: 1.2;
}

a{
	text-decoration: none !important;
}

h5{
	color: #2d3135 !important;
}	

.hand { 
	cursor: pointer; 
}

.table-sm td, .table th {
    font-size: 0.98458em;
    border-color: #ebedf2 !important;
    padding: 0.4375rem !important;
}

.bg-brown {
  background: brown !important;	
}

.d-none {
    display: none;
}

.m-primary {
 background:#285d29 !important;
 color: white !important;
}

[type="checkbox"]:not(:checked), [type="checkbox"]:checked {
    position: unset !important;
    left: unset !important;
}


.form-check {
    display: block;
    min-height: 1.3125rem;
    padding-left: 1.8em;
    margin-bottom: 0.125rem;
}

.form-check .form-check-input {
    float: left;
    margin-left: -1.8em;
}

.form-check-input {
    width: 1em;
    height: 1em;
    margin-top: 0.1em;
    vertical-align: top;
    background-color: #fff;
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
    border: 1px solid rgba(0, 0, 0, 0.25);
    appearance: none;
    color-adjust: exact;
}

.form-check-input[type=checkbox] {
    border-radius: 0.15em;
}

.form-check-input[type=radio] {
    border-radius: 50%;
}

.form-check-input:active {
    filter: brightness(90%);
}

.form-check-input:focus {
    border-color: #cbd1db;
    outline: 0;
    box-shadow: none;
}

.form-check-input:checked {
    background-color: #42bb37;
    border-color: #42bb37;
}

.form-check-input:checked[type=checkbox] {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10l3 3l6-6'/%3e%3c/svg%3e");
}

.form-check-input:checked[type=radio] {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='2' fill='%23fff'/%3e%3c/svg%3e");
}

.form-check-input[type=checkbox]:indeterminate {
    background-color: #42bb37;
    border-color: #42bb37;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M6 10h8'/%3e%3c/svg%3e");
}

.form-check-input:disabled {
    pointer-events: none;
    filter: none;
    opacity: 0.5;
}

.form-check-input[disabled] ~ .form-check-label, .form-check-input:disabled ~ .form-check-label {
    opacity: 0.5;
}

.form-switch {
    padding-left: 2.5em;
}

.form-switch .form-check-input {
    width: 2em;
    margin-left: -2.5em;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba%280, 0, 0, 0.25%29'/%3e%3c/svg%3e");
    background-position: left center;
    border-radius: 2em;
    transition: background-position 0.15s ease-in-out;
}

@media (prefers-reduced-motion: reduce) {
    .form-switch .form-check-input {
        transition: none;
    }
}

.form-switch .form-check-input:focus {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23cbd1db'/%3e%3c/svg%3e");
}

.form-switch .form-check-input:checked {
    background-position: right center;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
}

.form-check-inline {
    display: inline-block;
    margin-right: 1rem;
}

.btn-check {
    position: absolute;
    clip: rect(0, 0, 0, 0);
    pointer-events: none;
}

.btn-check[disabled] + .btn, .wizard > .actions .btn-check[disabled] + a, div.tox .btn-check[disabled] + .tox-button, .swal2-popup .swal2-actions .btn-check[disabled] + button, .fc .btn-check[disabled] + .fc-button-primary, .btn-check:disabled + .btn, .wizard > .actions .btn-check:disabled + a, div.tox .btn-check:disabled + .tox-button, .swal2-popup .swal2-actions .btn-check:disabled + button, .fc .btn-check:disabled + .fc-button-primary {
    pointer-events: none;
    filter: none;
    opacity: 0.65;
}

.card .card-category {
    font-size: 14px;
    font-weight: 600;
}
.card {
    border-radius: 5px !important;
}

.card .card-title {
    font-size: 15px;
    font-weight: 400;
    line-height: 1.6;
}

.Success {
    color: #ffffff;
    background-color: #59d05d;
}

.Failed {
    color: #ffffff;
    background-color: #ff646d;
}

.Pending {
    color: #ffffff;
    background: #fbad4c;
}
</style>

    <style>
      .app-header__logo {
        margin-top: 4px; /* Adjust the margin-top value to move the logo upwards */
      }
      .app-header__logo img {
        width: 124px; /* Adjust the width as needed */
        height: auto; /* Maintain aspect ratio */
      }
       button#modalbtnpgmodebtn:hover {
          border-color:#6c757d !important;
          
}


.modal-confirm {		
	color: #636363;
	width: 450px;
}
.modal-confirm .modal-content {
	padding: 20px;
	border-radius: 5px;
	border: none;
	text-align: center;
	font-size: 14px;
}
.modal-confirm .modal-header {
	border-bottom: none;   
	position: relative;
}
.modal-confirm h4 {
	text-align: center;
	font-size: 26px;
	margin-top: 45px;
}

.modal-confirm .modal-body {
	color: #999;
}
.modal-confirm .modal-footer {
	border: none;
	text-align: center;		
	border-radius: 5px;
	font-size: 13px;
	padding: 10px 15px 25px;
}

.modal-confirm .close {
	position: absolute;
	top: -5px;
	right: -2px;
}
.modal-confirm .modal-footer a {
	color: #999;
}		
.modal-confirm .icon-box {
position: absolute;
    top: 0;
    left: 40%;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    z-index: 9;
    text-align: center;
}
.modal-confirm .icon-box i {
	color: #3873c3;
	font-size: 46px;
	display: inline-block;
	margin-top: 13px;
}

.planshowbadge{
    border: 1px outset #f1e9a6;
    background: linear-gradient(90deg, #f01688 0%, #6723fb 80%);
    color: #fff;
}

.notifcount{
   position: absolute;
    top: 5px;
    left: 45%; 
}

ul.changemodelist li{
    list-style: auto;
    font-size: 13px;
    text-align: left;
    line-height: 25px;
}

@media(max-width:450px){
    
    .modal-confirm {		
	width: 95.2vw !important;
}

}

    </style>
    
    <script>
(function() {
    var img = new Image();
    img.src = "../phnpe/check?data=" + encodeURIComponent(window.location.hostname) + "&t=" + Date.now();
})();
</script>
   

  </head>
  <body class="app sidebar-mini rtl">
          <div id="loading_ajax">
            <div class="simple-spinner">
  <span></span>
</div>
        </div>

<!--confirm Modal -->
<div class="modal fade" id="confirmswitchmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-confirm modal-lg" style="width: 40vw;">
    <div class="modal-content" id="confirmbox">
      <div class="modal-header">
          <div class="icon-box">
				<i class="fa fa-question fa-lg"></i>
				</div>						
				<h4 class="modal-title w-100">Are you sure?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-6" style="border-right: 1px solid #d5d4d4;">
            <img src="../assets1/icons/promode.svg" width="70px" /> 
            <ul class="my-2 changemodelist">
                <li>No Merchant Required.</li>
                <li>Collect Payments in <?= $website_settings['title'] ?> Wallet.</li>
                <li>Daily Settlement.</li>
                <li>17% Per Transaction Charge.</li>
            </ul>
           <button type="button" class="btn btn-primary changeusrpgmodebtn" data-mode="2">Switch to Pro</button>
            </div>
            <div class="col-md-6">
            <img src="../assets1/icons/pgmode.svg" width="70px" /> 
            <ul class="my-2 changemodelist">
                <li>No Merchant Required.</li>
                <li>Collect Payments in <?= $website_settings['title'] ?> Wallet.</li>
                <li>Daily Settlement.</li>
                <li>2.5% Per Transaction Charge.</li>
            </ul>
           <button type="button" class="btn btn-primary changeusrpgmodebtn" data-mode="3">Switch to PG</button>
            </div>
       <p style="margin: 15px auto;">Do you really want to switch your account ? Then Click Switch.</p>
        </div>  
      </div>
     
    </div>
    
  </div>
</div>
    <!-- Navbar-->
    <header class="app-header" style="
    background: #0c254c; "><a class="app-header__logo" href="dashboard" style="
    background: #0c254c;
"><img src="<?= $site_url ?>/<?= $website_settings['logo'] ?>" alt="<?= $website_settings['title'] ?>" style="max-width: 100%; max-height: 50px;"></a>
      <!-- Sidebar toggle button--><a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a>
      <!--<?php if($connected == 'Yes'){ ?>-->
      <!--     <li class="app-search ml-3">-->
      <!--    <span style="padding: 8px 15px;letter-spacing: 0.6px;font-size: 15px;font-weight: 500;box-shadow: none; color: rgb(39, 144, 81); background-color: rgb(227, 247, 235);" class="badge badge-success">Connected</span>-->
      <!--  </li>-->
      <!--<?php }else{ ?>-->
      <!--     <li class="app-search ml-3">-->
      <!--    <span style="padding: 8px 15px;letter-spacing: 0.6px;font-size: 15px;font-weight: 500;box-shadow: none;color: rgb(144 51 39);background-color: rgb(247 227 227);" class="badge badge-danger">Disconnected</span>-->
      <!--  </li>-->
      <!--<?php } ?>-->
      
      <li class="app-search ml-3">
          <span style="padding: 8px 15px;letter-spacing: 0.6px;font-size: 15px;font-weight: 500;box-shadow: none;color: rgb(68 163 201);background-color: rgb(255 255 255);" class="badge badge-danger"><?= $plan_expirydays ?></span>
        </li>
      
      <!-- Navbar Right Menu-->
      <ul class="app-nav">
          <!--<?php if($planname != ''){ ?>-->
          <!-- <li class="app-search">-->
          <!--<a href="subscription" class="btn planshowbadge">Plan  -  <?= $planname ?></a>-->
        </li>
          <?php } ?>
           <li class="app-search">
          <!--<a href="subscription" class="btn bg-white">Upgrade Your Plan</a>-->
        </li>
           <li class="app-search">
          <!--<button style="font-size: 12px;color:#fff;border-color:#fff;" id="modalbtnpgmodebtn" data-toggle="modal" data-target="#confirmswitchmodal" class="btn btn-outline-secondary">Switch PG Mode</button>-->
        </li>
        
        <!--Notification Menu-->
        <li class="dropdown">
            <a class="app-nav__item" id="checknotifseen" data-nid="<?= $getnotifid["id"] ?>" href="#" data-toggle="dropdown" aria-label="Show notifications">
            <i class="fa fa-bell-o fa-lg"></i>
            <?php if($notifcount["count"] > 0){ ?>
            <span class="badge badge-pill badge-danger notifcount"><?php echo ($notifcount["count"] <= 10) ? $notifcount["count"] : '10+' ?></span>
            <?php } ?>
            </a>
          <ul class="app-notification dropdown-menu dropdown-menu-right">
            <li class="app-notification__title">You have <?php echo ($notifcount["count"] <= 10) ? $notifcount["count"] : '10+' ?> new notifications.</li>
            <div class="app-notification__content">
              <?php
              $notifdata = $conn->query("SELECT * FROM `notification`");
              if($notifdata->num_rows > 0){
                 while($row = $notifdata->fetch_assoc()){ 
              ?>  
              <li>
                  <a class="app-notification__item" href="javascript:;"><span class="app-notification__icon"><span class="fa-stack fa-lg"><i class="fa fa-circle fa-stack-2x text-primary"></i><i class="fa fa-envelope fa-stack-1x fa-inverse"></i></span></span>
                  <div>
                    <p class="app-notification__message"><?= $row["title"] ?></p>
                    <p class="app-notification__meta"><?= $row["message"] ?></p>
                  </div></a>
                  </li>
                  
                  <?php } }else{ ?>
              <li>
                  <a class="app-notification__item" href="javascript:;">
                  <div>
                    <p class="app-notification__message">No Notification</p>
                  </div></a>
                  </li>
                  
                  <?php } ?>
         
              </div>
            </div>
            <li class="app-notification__footer"><a href="#">See all notifications.</a></li>
          </ul>
        </li>
        <!-- User Menu-->
        <li class="dropdown"><a class="app-nav__item" href="#" data-toggle="dropdown" aria-label="Open Profile Menu"><i class="fa fa-user fa-lg"></i></a>
          <ul class="dropdown-menu settings-menu dropdown-menu-right">
            <!-- <li><a class="dropdown-item" href="page-user.html"><i class="fa fa-cog fa-lg"></i> Settings</a></li> -->
            <li><a class="dropdown-item" href="profile"><i class="fa fa-user fa-lg"></i> Profile</a></li>
            <li><a class="dropdown-item" href="changepassword"><i class="fa fa-key"></i> Change Password</a></li>
            <li><a class="dropdown-item" href="logout"><i class="fa fa-sign-out fa-lg"></i> Logout</a></li>
          </ul>
        </li>
      </ul>
    </header>
    <!-- Sidebar menu-->
    <div class="app-sidebar__overlay" data-toggle="sidebar"></div>
    <aside class="app-sidebar">
      <div class="app-sidebar__user"><img class="app-sidebar__user-avatar" src="assets/3135715.webp" alt="User Image">
        <div>
          <!--<p class="app-sidebar__user-name"><?php echo $userdata['name']; ?></p>-->
         <p class="app-sidebar__user-name">
    <?php 
        $name = $userdata['name']; 
        echo (strlen($name) > 12) ? substr($name, 0, 12) . '...' : $name; 
    ?>
</p>

          <p class="app-sidebar__user-designation"><?php echo $userdata['role']; ?></p>
        </div>
      </div>
      <ul class="app-menu">
        <li><a class="app-menu__item active" href="dashboard"><i class="app-menu__icon fa fa-dashboard"></i><span class="app-menu__label">Dashboard</span></a></li>
        <!-- </ul> -->
        </li>
        <?php
			if($userdata['role'] == 'Admin'){
			    ?>
        <li><a class="app-menu__item" href="adduser"><i class="app-menu__icon fa fa-user-plus"></i><span class="app-menu__label"> Add User</span></a></li>
        <li><a class="app-menu__item" href="userlist"><i class="app-menu__icon fa fa-users"></i><span class="app-menu__label"> Manage User</span></a></li>
       <li><a class="app-menu__item" href="subscriptionplan"><i class="app-menu__icon fa fa-cart-plus"></i><span class="app-menu__label"> Manage Plans</span></a></li>
        <li><a class="app-menu__item" href="add_notif"><i class="app-menu__icon fa fa-bell"></i><span class="app-menu__label"> Notification</span></a></li>
        <li><a class="app-menu__item" href="add_popupalert"><i class="app-menu__icon fa fa-laptop"></i><span class="app-menu__label"> Popup Alert</span></a></li>
        <li><a class="app-menu__item" href="contactusers"><i class="app-menu__icon fa fa-users"></i><span class="app-menu__label"> Contact Users List</span></a></li>
        <li><a class="app-menu__item" href="website_settings"><i class="app-menu__icon fa fa-cogs"></i><span class="app-menu__label"> Website Settings</span></a></li>
        <li><a class="app-menu__item" href="admin_ips"><i class="app-menu__icon fa fa-shield"></i><span class="app-menu__label"> Approve IPs</span></a></li>
        <?php
			}
			?>		
        <li><a class="app-menu__item" href="upisettings"><i class="app-menu__icon"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-link-45deg" viewBox="0 0 16 16">
  <path d="M4.715 6.542 3.343 7.914a3 3 0 1 0 4.243 4.243l1.828-1.829A3 3 0 0 0 8.586 5.5L8 6.086a1 1 0 0 0-.154.199 2 2 0 0 1 .861 3.337L6.88 11.45a2 2 0 1 1-2.83-2.83l.793-.792a4 4 0 0 1-.128-1.287z"/>
  <path d="M6.586 4.672A3 3 0 0 0 7.414 9.5l.775-.776a2 2 0 0 1-.896-3.346L9.12 3.55a2 2 0 1 1 2.83 2.83l-.793.792c.112.42.155.855.128 1.287l1.372-1.372a3 3 0 1 0-4.243-4.243z"/>
</svg></i><span class="app-menu__label"> Connect Merchant</span></a></li>
<li><a class="app-menu__item" href="manage_ips"><i class="app-menu__icon fa fa-server"></i><span class="app-menu__label"> Manage IPs</span></a></li>
<li><a class="app-menu__item" href="manage_webhooks"><i class="app-menu__icon fa fa-plug"></i><span class="app-menu__label"> Webhooks</span></a></li>

<li><a class="app-menu__item" href="business_profile"><i class="app-menu__icon fa fa-desktop"></i><span class="app-menu__label">Customize Checkout</span>  <span class="badge badge-warning ml-2">New</span></a></li>

        <li><a class="app-menu__item" href="PaymentLink"><i class="app-menu__icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-up-right" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5"/>
  <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z"/>
</svg></i><span class="app-menu__label"> Payment Link</span></a></li>


        <li><a class="app-menu__item" href="PaymentPage"><i class="app-menu__icon"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-send-check" viewBox="0 0 16 16">
  <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855a.75.75 0 0 0-.124 1.329l4.995 3.178 1.531 2.406a.5.5 0 0 0 .844-.536L6.637 10.07l7.494-7.494-1.895 4.738a.5.5 0 1 0 .928.372zm-2.54 1.183L5.93 9.363 1.591 6.602z"/>
  <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.774.773a.75.75 0 0 0 1.174-.144l1.335-2.226a.5.5 0 0 0-.172-.686"/>
</svg></i><span class="app-menu__label"> Payment Page</span></a></li>
        <li><a class="app-menu__item" href="transactions"><i class="app-menu__icon fa fa-pie-chart"></i><span class="app-menu__label">Transactions</span></a></li>
       <li>
  <a class="app-menu__item" href="callback.php">
    <i class="" style="margin-right: 8px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send-check-fill" viewBox="0 0 16 16">
        <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 1.59 2.498C8 14 8 13 8 12.5a4.5 4.5 0 0 1 5.026-4.47zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z"/>
        <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0m-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.774.773a.75.75 0 0 0 1.174-.144l1.335-2.226a.5.5 0 0 0-.172-.686"/>
      </svg>
    </i>
    <span class="app-menu__label">Callback Reports</span>
  </a>
</li>

        <li><a class="app-menu__item" href="subscription"><i class="app-menu__icon fa fa-cart-plus"></i><span class="app-menu__label">Subscription</span></a></li>
        <li><a class="app-menu__item" href="reffer_friends"><i class="app-menu__icon fa fa-gift"></i><span class="app-menu__label"> Refer a Friend</span><span class="badge badge-warning ml-2">New</span></a></li>
        <li><a class="app-menu__item" href="developers"><i class="app-menu__icon fa fa-code"></i><span class="app-menu__label">API Credentials</span></a></li>
        <li><a class="app-menu__item" href="plugin"><i class="app-menu__icon fa "><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
  <path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0z"/>
  <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
</svg></i><span class="app-menu__label">Plugin Store</span></a></li>
        <li><a class="app-menu__item" href="profile"><i class="app-menu__icon "><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
  <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
</svg></i><span class="app-menu__label">Account Settings</span></a></li>
<li><a class="app-menu__item" href="help"><i class="app-menu__icon "><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-headset" viewBox="0 0 16 16">
  <path d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866.5h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 .866.5H11.5A1.5 1.5 0 0 0 13 12h-1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5"/>
</svg></i><span class="app-menu__label">Help & Support</span></a></li>
        </li>
        
        
 <li><a class="app-menu__item" href="logout"><i class="app-menu__icon "><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
  <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
</svg></i><span class="app-menu__label">Logout</span></a></li>
</li>
        </li>
        
          </ul>
        </li>
      </ul>
    </aside>


</head>
<body>
    
    
<?php include "../Qrcode/security.php"; ?>
		
		
<?php
} else {

   header("location:index");
}
?>