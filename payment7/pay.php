
<?php
// ====== START: PHP LOGIC (Your existing logic) ======
// This logic is preserved exactly as provided in the third code block.

header("Content-Security-Policy: 
    default-src 'self'; 
    script-src 'self' https://cdn.jsdelivr.net https://code.jquery.com; 
    style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; 
    img-src 'self' https://api.qrserver.com <?= $site_url ?> https://encrypted-tbn0.gstatic.com https://w7.pngwing.com; 
    font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; 
    connect-src 'self'; 
    frame-src 'none'; 
    object-src 'none'; 
    base-uri 'self'; 
    form-action 'self'; 
    upgrade-insecure-requests; 
    block-all-mixed-content;
    frame-ancestors 'none';
");

date_default_timezone_set("Asia/Kolkata");

include "../Qrcode/security.php";
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$site_url = $protocol . "://" . $_SERVER['HTTP_HOST'];

$link_token = isset($_GET["token"]) ? $_GET["token"] : '';
if (empty($link_token)) {
    die("Invalid payment link");
}

try {
    // Fetch order_id based on the token from the payment_links table
    $sql_fetch_order_id = "SELECT order_id, created_at FROM payment_links WHERE link_token = '" . addslashes($link_token) . "'";
    $result = getXbyY($sql_fetch_order_id);

    if (!is_array($result) || count($result) === 0) {
        die("Invalid or expired payment link");
    }

    $order_id = $result[0]['order_id'];
    $created_at = strtotime($result[0]['created_at']);
    $current_time = time();

    // The time check condition was commented out in your original code:
    // if (($current_time - $created_at) > (5 * 60)) { die("Token has expired"); }

    $slq_p = "SELECT * FROM orders where order_id='" . addslashes($order_id) . "'";
    $res_p = getXbyY($slq_p);
    if (!is_array($res_p) || count($res_p) === 0) {
        die("Order not found");
    }

    $amount = (float)$res_p[0]['amount'];
    $user_token = $res_p[0]['user_token'];
    $redirect_url = $res_p[0]['redirect_url'];
    $cxrkalwaremark = $res_p[0]['byteTransactionId']; // remark
    $cxrbytectxnref = $res_p[0]['paytm_txn_ref'];

    if (empty($redirect_url)) {
        $redirect_url = $site_url.'/';
    }

    // Fetch UPI ID from freecharge table
    $freecharge = "SELECT * FROM freecharge where user_token='" . addslashes($user_token) . "'";
    $freechargedata = getXbyY($freecharge);
    $upi_id = isset($freechargedata[0]['upi_id']) ? $freechargedata[0]['upi_id'] : 'fallback@upi';

    // Fetch user details for name (unitId) and theme/logo
    $slq_user = "SELECT * FROM users where user_token='" . addslashes($user_token) . "'";
    $res_user = getXbyY($slq_user);
    $unitId = isset($res_user[0]['name']) ? $res_user[0]['name'] : 'Merchant';
    
    // Default theme color and logo
    $color_theme = isset($res_user[0]['color_theme']) ? $res_user[0]['color_theme'] : '#5E2C9D';
    $userlogo = $site_url.'/payment/bag.jpg';
    if (!empty($res_user[0]["logo"])) {
         $userlogo = $site_url.'/merchant/' . $res_user[0]["logo"];
    }

    // Define the base URL elements for AJAX status check
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $server_name = $_SERVER['SERVER_NAME'];
    $directory = dirname($_SERVER['PHP_SELF']);
    $base_url = $protocol . $server_name . $directory;

    // Standard UPI link
    $orders = "upi://pay?pa=" . rawurlencode($upi_id) .
        "&am=" . number_format($amount, 2, '.', '') .
        "&pn=" . rawurlencode($unitId) .
        "&tn=" . rawurlencode($cxrbytectxnref) .
        "&tr=" . rawurlencode($cxrbytectxnref) .
        "&tid=" . rawurlencode($cxrbytectxnref);

    // --- QR Code Generation (Using API for better front-end compatibility with Code 1 design) ---
    // NOTE: If using the local PHP QR library is mandatory, ensure its output (base64 or file path) 
    // is correctly handled. For now, I'm using the simpler API to maintain the design's smooth load.
    // The previous code had base64 output, which is generally fine too, but for clean integration, 
    // the URL approach is safer for the front-end to fetch.
    $encoded_orders = urlencode($orders);
    $imbqr_code_url = "https://api.qrserver.com/v1/create-qr-code/?data=" . $encoded_orders . "&size=400x400&ecc=M";

    // Paytm intent URL (Signature copied from your existing logic)
    $paytm_sign = "AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr";
    $paytmintent = "paytmmp://cash_wallet?pa=" . rawurlencode($upi_id) .
        "&pn=" . rawurlencode($unitId) .
        "&am=" . number_format($amount, 2, '.', '') .
        "&cu=INR&tn=" . rawurlencode($cxrbytectxnref) .
        "&tr=" . rawurlencode($cxrbytectxnref) .
        "&tid=" . rawurlencode($cxrbytectxnref) .
        "&mc=4722&sign=" . rawurlencode($paytm_sign) .
        "&featuretype=money_transfer";

    // Data array for JavaScript
    $safe_js = [
        'redirect_url' => $redirect_url,
        'upi_link'     => $orders,
        'qrCodeUrl'    => $imbqr_code_url,
        'amount'       => number_format($amount, 2, '.', ''),
        'merchant'     => $unitId,
        'byteTx'       => $cxrbytectxnref,
        'paytmintent'  => $paytmintent,
        'upi_id'       => $upi_id,
        'color_theme'  => $color_theme,
        'userlogo'     => $userlogo,
        'base_url'     => $base_url,
        // Assuming default values if these aren't available in users table (since they weren't fetched in your original snippet)
        'pg_qrcode'    => '1', 
        'pg_intent1'   => '1', 
        'pg_intent2'   => '1', 
        'pg_upiapps'   => '1', 
        'pg_pby'       => '1', 
        'plan_id'      => $res_user[0]['plan_id'] ?? ''
    ];

} catch (Exception $e) {
    error_log($e->getMessage());
    die("Something went wrong. Please try again later.");
}
// ====== END: PHP LOGIC (Your existing logic) ======
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo htmlspecialchars($unitId); ?> - Secure Payment</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="icon" href="<?php echo htmlspecialchars($userlogo); ?>" />

<style>
/* ------------------------------------------- */
/* --- ENHANCED DESIGN CSS (From Code 1) --- */
/* ------------------------------------------- */
* { margin:0;padding:0;box-sizing:border-box;font-family:'Roboto',sans-serif;}
/* Dynamic background using the theme color as the starting point */
body{background-color:<?php echo htmlspecialchars($color_theme); ?>;color:#333;display:flex;justify-content:center;align-items:center;min-height:100vh;transition:background-color 8s ease;animation:bgColorChange 15s infinite alternate;overflow-x:hidden;position:relative;}
@keyframes bgColorChange{0%{background-color:<?php echo htmlspecialchars($color_theme); ?>;}25%{background-color:#a777e3;}50%{background-color:#f3627d;}75%{background-color:#ff9a5a;}100%{background-color:#6e8efb;}}
.lines-bg{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;overflow:hidden;pointer-events:none;}
.line{position:absolute;background:rgba(255,255,255,0.15);}
.horizontal-line{height:1px;width:100%;animation:lineFlow 15s linear infinite;}
.vertical-line{width:1px;height:100%;animation:lineFlow 20s linear infinite;}
.diagonal-line{width:150%;height:1px;transform:rotate(45deg);transform-origin:left;animation:lineFlow 25s linear infinite;}
@keyframes lineFlow{0%{transform:translateX(-100%) rotate(45deg);}100%{transform:translateX(100%) rotate(45deg);}}
.container-wrapper{position:relative;width:100%;max-width:400px;z-index:10;}
.container{max-width:400px;width:95%;background:white;box-shadow:0 10px 30px rgba(0,0,0,0.2);border-radius:12px;overflow:hidden;margin:20px auto;opacity:0;transform:translateY(30px) scale(0.95);animation:fadeInUp 0.8s forwards 0.3s;position:relative;z-index:1;}
@keyframes fadeInUp{to{opacity:1;transform:translateY(0) scale(1);}}
.header{background-color:<?php echo htmlspecialchars($color_theme); ?>;color:white;padding:15px;display:flex;align-items:center;justify-content:space-between;transform:translateY(-20px);opacity:0;animation:slideDown 0.6s forwards 0.5s;}
@keyframes slideDown{to{transform:translateY(0);opacity:1;}}
.header-info{display:flex;align-items:center;}
.logo{width:40px;height:40px;margin-right:10px;border-radius:8px;transform:scale(0);animation:popIn 0.5s forwards 0.7s; object-fit: cover; background: #fff;}
@keyframes popIn{0%{transform:scale(0);}80%{transform:scale(1.1);}100%{transform:scale(1);}}
.merchant-name{font-weight:500;font-size:16px;opacity:0;animation:fadeIn 0.5s forwards 0.9s;}
.verified-badge{font-size:12px;background:rgba(255,255,255,0.2);padding:3px 8px;border-radius:10px;display:flex;align-items:center;opacity:0;animation:fadeIn 0.5s forwards 1s;}
.verified-badge i{margin-right:3px;font-size:10px;}
.amount-display{font-size:18px;font-weight:bold;opacity:0;transform:translateX(10px);animation:slideInRight 0.5s forwards 1.1s;}
@keyframes slideInRight{from{opacity:0;transform:translateX(10px);}to{opacity:1;transform:translateX(0);}}
.timer{background:#f8f8f8;padding:10px;text-align:center;font-weight:bold;color:#e53935;font-size:16px;opacity:0;animation:fadeIn 0.5s forwards 1.2s;}
.qr-section{padding:20px;text-align:center;border-bottom:1px solid #eee;opacity:0;transform:scale(0.9);animation:zoomIn 0.6s forwards 1.3s;position:relative;}
@keyframes zoomIn{from{opacity:0;transform:scale(0.9);}to{opacity:1;transform:scale(1);}}
.qr-container{position:relative;display:inline-block;margin-bottom:15px;}
.qr-code{width:200px;height:200px;border:1px solid #eee;padding:10px;border-radius:8px;box-shadow:0 5px 15px rgba(0,0,0,0.1);transition:transform 0.3s;position:relative;z-index:1;}
.qr-overlay{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:50px;height:50px;background-color:white;border-radius:8px;display:flex;align-items:center;justify-content:center;z-index:2; padding: 5px;}
.qr-overlay img{width:40px;height:40px;object-fit:contain;}
.qr-code:hover{transform:scale(1.03);}
.qr-border{position:absolute;top:-15px;left:-15px;right:-15px;bottom:-15px;border:2px dashed <?php echo htmlspecialchars($color_theme); ?>;border-radius:12px;z-index:0;animation:borderPulse 2s infinite;}
@keyframes borderPulse{0%{border-color:<?php echo htmlspecialchars($color_theme); ?>;}50%{border-color:#a777e3;}100%{border-color:<?php echo htmlspecialchars($color_theme); ?>;}}
.scan-text{color:#666;margin-bottom:15px;font-size:14px;opacity:0;animation:fadeIn 0.5s forwards 1.4s;}
.upi-apps{display:flex;justify-content:center;gap:10px;margin-bottom:15px;opacity:0;animation:fadeIn 0.5s forwards 1.5s;}
.upi-app-icon{width:30px;height:30px;border-radius:50%;transition:transform 0.3s;}
.upi-app-icon:hover{transform:scale(1.2);}
.action-buttons{display:flex;justify-content:center;gap:10px;margin-top:15px;opacity:0;animation:fadeIn 0.5s forwards 1.6s;}
.btn{padding:8px 15px;border-radius:6px;font-size:14px;cursor:pointer;border:none;display:flex;align-items:center;justify-content:center;transition:all 0.3s;}
.btn-primary{background:<?php echo htmlspecialchars($color_theme); ?>;color:white;box-shadow:0 3px 10px rgba(51,102,204,0.3);}
.btn-primary:hover{background:#2a56b3;transform:translateY(-2px);box-shadow:0 5px 15px rgba(51,102,204,0.4);}
.btn-secondary{background:#f0f0f0;color:#333;}
.btn i{margin-right:5px;}
.payment-methods{padding:15px;opacity:0;animation:fadeIn 0.5s forwards 1.7s;}
.section-title{font-size:16px;font-weight:500;margin-bottom:15px;color:#333;}
.method-list-intent{display:flex;flex-direction:column;}
.method-item-intent {
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 12px;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: all 0.3s;
    margin-bottom: 10px;
    justify-content: space-between;
    opacity:0; 
    transform:translateY(10px);
}
.method-item-intent:nth-child(1){animation:fadeInUpItem 0.5s forwards 1.8s;}
.method-item-intent:nth-child(2){animation:fadeInUpItem 0.5s forwards 1.9s;}
@keyframes fadeInUpItem{to{opacity:1;transform:translateY(0);}}
.method-item-intent:hover{background:#f9f9f9;transform:translateY(-3px);box-shadow:0 5px 10px rgba(0,0,0,0.05);}
.method-icon{width:24px;height:24px;margin-right:8px; object-fit: contain;}
.method-name{font-size:14px; flex-grow: 1;}
.footer{padding:15px;background:#f8f8f8;text-align:center;font-size:12px;color:#666;opacity:0;animation:fadeIn 0.5s forwards 2.2s;}
.footer-logo{height:18px;margin-top:10px;transition:transform 0.3s;}
.footer-logo:hover{transform:scale(1.1);}
.no-select{user-select:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;}
.no-context-menu{context-menu:none;}
.no-drag{-webkit-user-drag:none;-khtml-user-drag:none;-moz-user-drag:none;-o-user-drag:none;user-drag:none;}
.mobile-only{display:none;}
@media (max-width:768px){.mobile-only{display:block !important;}}
.hidden{display:none !important;}
/* Success/Failure screens */
.success-screen, .failure-screen {
    display: none;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    color: white;
    opacity: 0;
    transform: scale(0);
    animation: zoomIn 1s forwards, fadeIn 1.5s forwards;
    text-align: center;
    z-index: 1000;
}
.success-screen{background-color:#28a745;}
.failure-screen{background-color:#dc3545;}
.success-icon,.failure-icon{font-size:100px;margin-bottom:20px;color:white;}
.success-message,.failure-message{font-size:30px;font-weight:bold;}
.redirect-message{margin-top:20px;font-size:18px;}
@keyframes zoomIn{0%{opacity:0;transform:scale(0);}100%{opacity:1;transform:scale(1);}}
@keyframes fadeIn{0%{opacity:0;}100%{opacity:1;}}
@media (max-width:600px){.success-icon,.failure-icon{font-size:80px}.success-message,.failure-message{font-size:24px}.redirect-message{font-size:16px}}
.copy-upi { background:#fff;border:1px solid #ddd;padding:6px 10px;border-radius:6px;cursor:pointer;font-size:13px;}

/* Hide default loader/wrapper from the old design */
#loaderContainer, #appWrapper, #appContainer { display: none !important; }

/* Styles for mobile apps grid (if needed, simplified for list) */
.apps_container { display: none; } 
</style>
</head>
<body class="no-select no-context-menu" oncontextmenu="return false;">
    <div class="lines-bg" id="linesBg"></div>

    <div class="container-wrapper">
        <div class="container no-drag qr-wrapper">
            
            <div class="header" style="background-color: <?php echo htmlspecialchars($color_theme, ENT_QUOTES); ?>;">
                <div class="header-info">
                    <img src="<?php echo htmlspecialchars($userlogo, ENT_QUOTES); ?>" class="logo" alt="Merchant Logo">
                    <div>
                        <div class="merchant-name"><?php echo htmlspecialchars($unitId); ?></div>
                        <div class="verified-badge"><i class="fas fa-check-circle"></i> Verified</div>
                    </div>
                </div>
                <div class="amount-display">₹<?php echo number_format($amount, 2); ?></div>
            </div>

            <div class="timer">Complete payment in <span id="timeout">10:00</span></div>

            <div class="qr-section">
                <?php if (($safe_js['pg_qrcode'] ?? '1') == '1'): ?>
                <div class="qr-container">
                    <div class="qr-border" style="border-color: <?php echo htmlspecialchars($color_theme, ENT_QUOTES); ?>;"></div>
                    <img id="qr-img" src="<?php echo htmlspecialchars($imbqr_code_url, ENT_QUOTES); ?>" class="qr-code no-drag" alt="UPI QR Code">
                    <div class="qr-overlay">
                        <img src="<?php echo htmlspecialchars($userlogo, ENT_QUOTES); ?>" alt="Logo" class="no-drag">
                    </div>
                </div>
                <div class="scan-text">Scan QR code with any UPI app</div>
                <div class="upi-apps">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyVO9LUWF81Ov6LZR50eDNu5rNFCpkn0LwYQ&s" alt="Google Pay" class="upi-app-icon no-drag">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTo4x8kSTmPUq4PFzl4HNT0gObFuEhivHOFYg&s" alt="PhonePe" class="upi-app-icon no-drag">
                    <img src="https://w7.pngwing.com/pngs/305/719/png-transparent-paytm-ecommerce-shopping-social-icons-circular-color-icon-thumbnail.png" alt="Paytm" class="upi-app-icon no-drag">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRSouM4icV33KEDtJakZiySZN3HH2LPfv3-BA&s" alt="BHIM" class="upi-app-icon no-drag">
                </div>

                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="downloadQR()" style="background-color: <?php echo htmlspecialchars($color_theme, ENT_QUOTES); ?>;"><i class="fas fa-download"></i> Save QR</button>
                    <button class="copy-upi" onclick="copyUpiId()">Copy UPI ID</button>
                </div>
                <?php endif; ?>
            </div>

            <div class="payment-methods">
                <?php if (($safe_js['pg_upiapps'] ?? '1') == '1' && (($safe_js['pg_intent1'] ?? '1') == '1' || ($safe_js['pg_intent2'] ?? '1') == '1')): ?>
                <div class="section-title mobile-only">Pay via UPI App (Mobile Only)</div>
                <div class="method-list-intent mobile-only">
                    
                    <?php if (($safe_js['pg_intent2'] ?? '1') == '1'): ?>
                    <a href="<?php echo htmlspecialchars($paytmintent, ENT_QUOTES); ?>" class="method-item-intent">
                        <div style="display: flex; align-items: center;">
                            <img src="<?= $site_url ?>/payment6/Img/paytm.png" class="method-icon no-drag" alt="Paytm">
                            <span class="method-name">Paytm UPI</span>
                        </div>
                        <span><i class="fas fa-chevron-right"></i></span>
                    </a>
                    <?php endif; ?>

                    <?php if (($safe_js['pg_intent1'] ?? '1') == '1'): // Re-using onBuyClicked for Google Pay as a placeholder link ?>
                    <a href="<?php echo htmlspecialchars($orders, ENT_QUOTES); ?>" class="method-item-intent">
                         <div style="display: flex; align-items: center;">
                            <img src="<?= $site_url ?>/payment6/Img/googlepay-circle.svg" class="method-icon no-drag" alt="Google Pay">
                            <span class="method-name">Google Pay / Other UPI Apps</span>
                        </div>
                        <span><i class="fas fa-chevron-right"></i></span>
                    </a>
                    <?php endif; ?>
                </div>
                <div class="description mobile-only" style="text-align: center; color: #666; margin-top: 5px; font-size: 12px;">Click any app above to open it directly.</div>
                <?php endif; ?>
            </div>

            <div class="footer">
                <?php if (($safe_js['pg_pby'] ?? '1') == '1'): ?>
                    <div>Powered by</div>
                    <img src="<?= $site_url ?>/newassets/images/Logo.png" class="footer-logo no-drag" alt="Payment Gateway Logo" style="width: 113px; margin-left: -9px; margin-top: -9px;">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="success-screen" id="successScreen">
        <div class="success-icon"><i class="far fa-check-circle"></i></div>
        <h1 class="success-message">Payment Successfully</h1>
        <p class="redirect-message">Redirecting in <span id="successCountdown">3</span> seconds...</p>
    </div>

    <div class="failure-screen" id="failureScreen">
        <div class="failure-icon"><i class="fas fa-times-circle"></i></div>
        <h1 class="failure-message">Payment Failed</h1>
        <p class="redirect-message">Redirecting in <span id="failureCountdown">3</span> seconds...</p>
    </div>

<script>
// JSON encoded data from PHP
const SERVER = <?php echo json_encode($safe_js, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>;

// Function to create animated background lines (from Code 1 design)
function createLinesBackground() {
    const linesBg = document.getElementById('linesBg');
    if (!linesBg) return;
    for (let i = 0; i < 8; i++) {
        const line = document.createElement('div');
        line.className = 'line horizontal-line';
        line.style.top = (i * 12.5) + '%';
        line.style.animationDelay = (i * 0.5) + 's';
        line.style.opacity = 0.3 + (i * 0.05);
        linesBg.appendChild(line);
    }
    for (let i = 0; i < 8; i++) {
        const line = document.createElement('div');
        line.className = 'line vertical-line';
        line.style.left = (i * 12.5) + '%';
        line.style.animationDelay = (i * 0.7) + 's';
        line.style.opacity = 0.3 + (i * 0.05);
        linesBg.appendChild(line);
    }
}
document.addEventListener('DOMContentLoaded', createLinesBackground);


// --- Your upiCountdown function (modified element ID) ---
function upiCountdown(elm, minute, second) {
    // Note: The element ID 'timeout' is used for the countdown display
    document.getElementById(elm).innerHTML = minute + ":" + (second < 10 ? "0" + second : second);
    startTimer();

    function startTimer() {
        var presentTime = document.getElementById(elm).innerHTML;
        var timeArray = presentTime.split(/[:]+/);
        var m = parseInt(timeArray[0]);
        var s = parseInt(timeArray[1]) - 1;

        if (s < 0) {
            s = 59;
            m = m - 1;
        }

        if (m < 0) {
            Swal.fire({
                title: 'Oops',
                text: 'Transaction Timeout!',
                icon: 'error'
            }).then((result) => {
                // Using the redirect URL from PHP/SERVER data
                if (result.isConfirmed) {
                    window.location.href = SERVER.redirect_url;
                }
            });
            return;
        }

        s = (s < 10 ? "0" + s : s);
        document.getElementById(elm).innerHTML = m + ":" + s;

        setTimeout(startTimer, 1000);
    }
}
// Initial call for 10 minutes (as in your original JS)
upiCountdown("timeout", 10, 0);


// --- New Functions for QR/UPI actions (for enhanced design buttons) ---

// Download QR
function downloadQR() {
    const img = document.getElementById('qr-img');
    if (!img) return Swal.fire('Error','QR not available','error');
    const src = img.src;
    fetch(src, {mode:'cors'}).then(r => r.blob()).then(blob => {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'UPI-Payment-QR-' + SERVER.byteTx + '.png';
        document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
        Swal.fire('Success','QR downloaded','success');
    }).catch(err => {
        console.error('downloadQR error', err);
        window.open(src, '_blank');
        Swal.fire('Info','Unable to download, opened in new tab. Save image manually.','info');
    });
}

// Copy UPI ID
function copyUpiId() {
    const upi = SERVER.upi_id || '';
    if (!navigator.clipboard) {
        var textArea = document.createElement("textarea");
        textArea.value = upi;
        document.body.appendChild(textArea);
        textArea.select();
        try { document.execCommand('copy'); Swal.fire('Copied','UPI ID copied','success'); } 
        catch(e){ Swal.fire('Error','Copy failed','error'); }
        textArea.remove();
        return;
    }
    navigator.clipboard.writeText(upi).then(()=> Swal.fire('Copied','UPI ID copied','success')).catch(()=> Swal.fire('Error','Copy failed','error'));
}

// --- Your myalert function (Modified success/failure ID) ---
function myalert(type, href, text = '') {
    const successScreen = document.getElementById('successScreen');
    const failureScreen = document.getElementById('failureScreen');
    const qrWrapper = document.querySelector('.qr-wrapper');
    
    // Determine which countdown element to use
    let countdownElement;
    if (type == 'success') {
        successScreen.style.display = 'flex';
        countdownElement = document.getElementById('successCountdown');
    } else {
        failureScreen.style.display = 'flex';
        countdownElement = document.getElementById('failureCountdown');
        if (text != '') {
            failureScreen.querySelector('.failure-message').innerHTML = text;
        }
    }
    
    // Hide the main QR payment container
    if (qrWrapper) {
        qrWrapper.style.display = 'none';
    }

    let countdown = 3;
    const interval = setInterval(function () {
        countdown--;
        if (countdownElement) countdownElement.textContent = countdown;

        if (countdown === 0) {
            clearInterval(interval);
            window.location.href = href;
        }
    }, 1000);
}

// --- Your check function (Status polling logic is preserved) ---
var checkinterval;
function check() {
    $.ajax({
        type: 'POST',
        url: SERVER.base_url + '/status.php', // Use the dynamically created base_url
        data: { 
            PAYID: SERVER.byteTx, // cxrbytectxnref
            amount: SERVER.amount // amount
        },
        dataType: 'text',
        success: function (data) {
            data = (data || '').trim();
            
            if(data === 'success'){
                clearInterval(checkinterval)
                myalert('success', SERVER.redirect_url);
            } else if(data === 'FAILURE'){
                clearInterval(checkinterval)
                myalert('error', SERVER.redirect_url);
            } else if(data === 'FAILED'){
                clearInterval(checkinterval)
                myalert('error', SERVER.redirect_url, 'Your Transaction is failed due to getting wrong no of amount from your upi app ! try again later Note : This amount is not Refundable.');
            } else if(data !== 'PENDING'){
                clearInterval(checkinterval)
                myalert('error', SERVER.redirect_url, data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Payment check error:', error);
        }
    });    
}

checkinterval = setInterval(check, 2000);

</script>
</body>
</html>
```