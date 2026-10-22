<?php include "header.php";

// SQL query to count rows
$sql = "SELECT COUNT(*) AS count FROM reports WHERE mobile = '$mobile'";

// Execute the query
$result = $conn->query($sql);

if ($result === false) {
    $rowCount = 0;
} else {
    // Fetch the result
    $row = $result->fetch_assoc();
    $rowCount = $row['count'];
}

// Check account status
$expiryDate = $userdata['expiry'];
$today = date('Y-m-d');
$status = (strtotime($expiryDate) >= strtotime($today)) ? "Active" : "Expired";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    
    <style>
        .checkmark-wrapper {
            margin-bottom: 20px;
        }
        
        .checkmark {
            width: 80px;
            height: 80px;
            display: block;
            stroke-width: 2;
            stroke: #4caf50;
            stroke-miterlimit: 10;
            margin: 15px auto;
            animation: scale 0.5s ease-in-out;
        }
        
        .checkmark__circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 2;
            stroke: #4caf50;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        
        .checkmark__check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.4s forwards;
        }
        
        @keyframes stroke {
            100% { stroke-dashoffset: 0; }
        }
        
        @keyframes scale {
            0%, 100% { transform: none; }
            50% { transform: scale3d(1.1, 1.1, 1); }
        }
        
        .invoice-body {
            padding-top: 10px;
        }
        
        .user-details {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .user-image img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-right: 20px;
        }
        
        .user-info h3 {
            margin: 0;
            font-size: 22px;
            color: #333;
        }
        
        .user-info p {
            margin: 10px 0;
            color: #666;
        }
        
        .swiper-container {
            width: 100%;
            height: 400px;
        }
        
        .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .swiper-pagination-bullet-active {
            background-color: #007bff;
        }
        
        .icon-circle {
            background-color: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }
        
        .card-border {
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.375rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .swiper-container {
                height: 250px;
            }
            
            .user-details {
                flex-direction: column;
                text-align: center;
            }
            
            .user-image img {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .user-info {
                text-align: center;
                margin-left: 0 !important;
            }
            
            .col-md-2.position-absolute {
                position: relative !important;
            }
            
            .card-body .row > div {
                padding-top: 15px;
            }
        }
        
        @media (max-width: 575.98px) {
            .app-title h1 {
                font-size: 1.5rem;
            }
            
            .breadcrumb {
                font-size: 0.8rem;
            }
            
            .card-body h3 {
                font-size: 1.2rem;
            }
            
            .card-body h6 {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <audio id="loginSound" src="assets/notification.mp3" preload="auto"></audio>
    
    <main class="app-content">
        <div class="app-title">
            <div>
                <h1><i class="fa fa-dashboard"></i> Dashboard</h1>
            </div>
            <ul class="app-breadcrumb breadcrumb">
                <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            </ul>
        </div>
        
        <!--<div class="row mb-5">-->
        <!--    <div class="col-md-12">-->
        <!--        <div class="mb-lg-0 mb-2 me-8">-->
        <!--            <h1 class="pg-title">Welcome, <?= htmlspecialchars($userdata["name"]) ?></h1>-->
        <!--            <p>Accept payments online hassle-free with our QR code service and other UPI Services.</p>-->
        <!--        </div>-->
        <!--    </div>-->
            
            <?php if(isset($_GET["aadhar_kyc"]) && $_GET["aadhar_kyc"] == 0): ?>
            <div class="col-md-12">
                <div class="mb-lg-0 mb-2 me-8 alert alert-danger">
                    Your Aadhaar KYC Is Not Completed! Kindly Complete Your KYC For Using Payment Page & Link 
                    <a id="aadhar_verify" href="#"><span><u>Verify Now</u></span></a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Stats Cards Row -->
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-lg rounded-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <div class="icon-circle rounded-circle p-3">
                                <i class="bi bi-cash-coin fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-2">Today's Transactions</h6>
                            <h3 class="mb-0">₹<?= number_format($todaysuccesspayment["amt"], 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-lg rounded-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <div class="icon-circle rounded-circle p-3">
                                <i class="bi bi-cash-coin fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-2">Today Success Payments</h6>
                            <h3 class="mb-0">₹<?= number_format($todayallpayment["amt"], 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-lg rounded-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <div class="icon-circle rounded-circle p-3">
                                <i class="bi bi-cash-coin fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-2">Today Pending Payment</h6>
                            <h3 class="mb-0">₹<?= number_format($todaypendingpayment["amt"], 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-lg rounded-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <div class="icon-circle rounded-circle p-3">
                                <i class="bi bi-bank fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-2">Today's Settlement</h6>
                            <h3 class="mb-0">₹<?= number_format($todaysuccesspayment["amt"], 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Second Stats Row -->
        <div class="row mt-3">
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-lg rounded-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <div class="icon-circle rounded-circle p-3">
                                <i class="bi bi-hourglass fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-2">Plan Expire</h6>
                            <h5 class="mb-0"><?= date("d M, Y", strtotime($userdata['expiry'])) ?></h5>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-lg rounded-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <div class="icon-circle rounded-circle p-3">
                                <i class="bi bi-wallet fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-2">Used Transactions</h6>
                            <h3 class="mb-0"><?= $rowCount ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-lg rounded-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <div class="icon-circle rounded-circle p-3">
                                <i class="bi bi-person fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-2">Account Status</h6>
                            <h3 class="mb-0"><?= htmlspecialchars($status) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card shadow-lg rounded-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="mr-3">
                            <div class="icon-circle rounded-circle p-3">
                                <i class="bi bi-cash-coin fs-4"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="card-subtitle mb-2">Today Failed Payment</h6>
                            <h3 class="mb-0">₹<?= number_format($todayfail["amt"], 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- KYC and Feature Cards -->
        <div class="col-12 my-3">
            <div class="card card-border overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-5 position-absolute d-none d-md-block">
                            <div class="d-flex align-items-center justify-content-center w-100">
                                <div class="d-flex position-relative w-100"></div>
                                <img class="d-flex" style="position: absolute;top: -15px;" width="140px" src="assets/imbpg_img/demo_aadhar.png" alt="imb PG">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-5"></div>
                        <div class="col-md-7 col-sm-8">
                            <div class="d-flex align-items-start justify-content-center flex-column h-100 py-2">
                                <div class="d-flex flex-column align-items-start">
                                    <h5 class="fw-medium text-dark">Complete KYC process quickly in just 10 seconds</h5>
                                    <div class="fs-7">Complete KYC Verification &amp; Enjoy Uninterrupted Access to imb Pay Gateway Services</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="mt-3 mt-md-0 d-md-flex align-items-center justify-content-center flex-column h-100">
                                <?php if($userdata['aadhar_kyc'] == 0): ?>  
                                <a class="d-flex flex-row" id="aadhar_verify" href="#">
                                    <span class="icon me-1 text-center">
                                        <span class="feather-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                        </span>
                                    </span>
                                    <span><u>Verify Now</u></span>
                                </a>
                                <?php else: ?>  
                                <span class="d-flex flex-row text-success">
                                    <span class="icon me-1 text-center">
                                        <span class="feather-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                        </span>
                                    </span>
                                    <span><u>Aadhaar Verified</u></span>
                                </span>
                                <?php endif; ?>  
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 mb-4">
            <div class="card card-border overflow-hidden">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-sm-5 position-absolute d-none d-md-block">
                            <div class="d-flex align-items-center justify-content-center w-100">
                                <div class="d-flex position-relative w-100"></div>
                                <img class="d-flex" style="position: absolute;top: -15px;" width="140px" src="assets/imbpg_img/upiQr01.jpg" alt="imb PG">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-5"></div>
                        <div class="col-md-7 col-sm-8">
                            <div class="d-flex align-items-start justify-content-center flex-column h-100 py-2">
                                <div class="d-flex flex-column align-items-start">
                                    <h5 class="fw-medium text-dark">New Feature: Make Payments Using UPI Apps Or Via Payment Link</h5>
                                    <div class="fs-7">*This feature is exclusively available in the imb Pay Pro Plan. It may not be supported by all UPI apps.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="mt-3 mt-md-0 d-md-flex align-items-center justify-content-center flex-column h-100">
                                <a class="d-flex flex-row" id="new_features" href="PaymentPage">
                                    <span class="icon me-1 text-center">
                                        <span class="feather-icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check-circle">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                        </span>
                                    </span>
                                    <span><u>Check Now</u></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts and Transactions -->
        <div class="row row-card-no-pd">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-dark">
                                <h5 class="mb-0 text-white"><i class="bi bi-graph-up-arrow mr-2"></i>Transaction Analytics (Last 12 Months)</h5>
                            </div>
                            <div class="card-body">
                                <div id="transactionChart" style="height: 300px;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-12 mt-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0 text-white"><i class="bi bi-clock-history mr-2"></i>Recent Transactions</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Sr.No</th>
                                                <th>Date</th>
                                                <th>Order Id</th>
                                                <th>Transaction Id</th>
                                                <th>UTR No</th>
                                                <th>Remark</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $transactions = $conn->query("SELECT * FROM `orders` WHERE `user_id` = '{$userdata["id"]}' ORDER BY id DESC LIMIT 10");
                                            $i = 1;
                                            
                                            if($transactions->num_rows > 0) {
                                                while($txn = $transactions->fetch_assoc()) {
                                                    echo "<tr>
                                                        <td>{$i}</td>
                                                        <td>" . date("d M Y", strtotime($txn['create_date'])) . "</td>
                                                        <td>" . htmlspecialchars($txn['order_id']) . "</td>
                                                        <td>" . htmlspecialchars($txn['byteTransactionId']) . "</td>
                                                        <td>" . htmlspecialchars($txn['utr']) . "</td>
                                                        <td>" . htmlspecialchars($txn['remark1']) . "</td>
                                                        <td>₹" . number_format($txn['amount'], 2) . "</td>
                                                        <td><span class='badge bg-" . 
                                                            ($txn['status'] === 'SUCCESS' ? 'success' : 
                                                             ($txn['status'] === 'FAILED' ? 'danger' : 'warning text-dark')) . 
                                                            "'>" . ucfirst(strtolower($txn['status'])) . "</span></td>
                                                    </tr>";
                                                    $i++;
                                                }
                                            } else {
                                                echo "<tr><td colspan='8'>No Transaction Found!</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Aadhar Verification Modal -->
    <div class="modal fade" id="aadhar_modal" tabindex="-1" aria-labelledby="aadhar_send_otp" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="aadhar_verify_form" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Verify Your Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2">
                            <div class="col-3 col-sm-2">
                                <img src="assets/imbpg_img/aadhaar.svg" alt="icon" style="width: 100%;">
                                <input name="type" type="hidden" value="aadharSendOtp">
                            </div>
                            <div class="col-12 col-sm-10 py-2">
                                <h5 class="mb-2">Verify using Aadhaar Number</h5>
                                <div class="d-flex justify-content-start align-items-baseline">
                                    <p class="text-muted">Kindly enter your Aadhaar number to verify your account. We will send you an OTP on your Aadhaar registered mobile number.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary text-white" type="submit">Proceed to KYC</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php 
    $popupalertdata = $conn->query("SELECT * FROM `popup_alert`");
    if($popupalertdata->num_rows > 0 && isset($_GET["login"])):
    ?>
    <div class="modal fade" id="showalertmsgModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">News Alert</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-12 col-sm-12 py-2 text-center">
                            <div class="swiper-container">
                                <div class="swiper-wrapper">
                                    <?php while($row = $popupalertdata->fetch_assoc()): ?>
                                    <div class="swiper-slide">
                                        <img src="<?= htmlspecialchars($row["img"]) ?>" alt="Slide <?= $row["id"] ?>">
                                    </div>
                                    <?php endwhile; ?>
                                </div>
                                
                                <div class="swiper-pagination"></div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/mainscript.js"></script>
    <script src="js/plugins/pace.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <script>
        // Function to play sound if two minutes have passed
        window.onload = function() {
            var lastPlayed = localStorage.getItem("lastPlayed");
            var currentTime = new Date().getTime();
            var twoMinutes = 4 * 120 * 2000;

            if (!lastPlayed || (currentTime - lastPlayed) > twoMinutes) {
                var sound = document.getElementById("loginSound");
                sound.play();
                localStorage.setItem("lastPlayed", currentTime);
            }
            
            <?php if($popupalertdata->num_rows > 0 && isset($_GET["login"])): ?>
            $("#showalertmsgModal").modal("show");
            <?php endif; ?>
        };
        
        // Request notification permission
        function requestNotificationPermission() {
            if (Notification.permission === "granted") {
                console.log("Notification permission already granted.");
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(function(permission) {
                    if (permission === "granted") {
                        console.log("Notification permission granted.");
                    }
                });
            }
        }
        
        // Call this function when the page loads to ask for permission
        requestNotificationPermission();
        
        // Initialize Swiper
        var swiper = new Swiper('.swiper-container', {
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
        });
        
        // Initialize ApexCharts
        const options = {
            chart: {
                type: 'area',
                height: 300,
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#0d6efd'],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            series: [{
                name: "Transactions (₹)",
                data: [1200, 1500, 1800, 2200, 2000, 2500, 2700, 3000, 3200, 3400, 3600, 4000]
            }],
            xaxis: {
                categories: [
                    'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ]
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return "₹" + val.toFixed(0);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "₹" + val.toLocaleString();
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: "vertical",
                    shadeIntensity: 0.5,
                    gradientToColors: ['#66b2ff'],
                    inverseColors: true,
                    opacityFrom: 0.6,
                    opacityTo: 0.1,
                    stops: [0, 100]
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#transactionChart"), options);
        chart.render();
        
        // Google analytics script
        if(document.location.hostname == 'pratikborsadiya.in') {
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-72504830-1', 'auto');
            ga('send', 'pageview');
        }
    </script>
</body>
</html>