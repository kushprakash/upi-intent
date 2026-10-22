<?php
// merged file with QR fix (uses api.qrserver.com for reliable QR generation)
// Place this file where ../pages/ and ../Qrcode/ includes work.

date_default_timezone_set("Asia/Kolkata");

include "../Qrcode/security.php";
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$site_url = $protocol . "://" . $_SERVER['HTTP_HOST'];

// ===== BACKEND: token, order, merchant, intents =====
$link_token = isset($_GET["token"]) ? $_GET["token"] : '';

if (empty($link_token)) {
    die("Invalid payment link");
}

// fetch payment_links
$sql_fetch_order_id = "SELECT order_id, created_at FROM payment_links WHERE link_token = '" . addslashes($link_token) . "'";
$result = getXbyY($sql_fetch_order_id);

if (!is_array($result) || count($result) === 0) {
    die("Token not found or expired");
}

$order_id = $result[0]['order_id'];
$created_at = strtotime($result[0]['created_at']);
$current_time = time();
if (($current_time - $created_at) > (5 * 60)) {
    die("Payment link has expired");
}

// fetch order
$slq_p = "SELECT * FROM orders where order_id='" . addslashes($order_id) . "'";
$res_p = getXbyY($slq_p);
if (!is_array($res_p) || count($res_p) === 0) {
    die("Order not found");
}

$amount = $res_p[0]['amount'];
$user_token = $res_p[0]['user_token'];
$redirect_url = $res_p[0]['redirect_url'];
$cxrkalwaremark = $res_p[0]['byteTransactionId'];  // remark / merchant txn id
if (empty($redirect_url)) $redirect_url = $site_url.'/';

// fetch user / merchant
$slq_pmode = "SELECT * FROM users where user_token='" . addslashes($user_token) . "'";
$res_pmode = getXbyY($slq_pmode);

// admin fallback logic as in your last file
if ($res_pmode[0]["pg_mode"] == 2) {
    $slq_admingetdata = "SELECT * FROM users where id=157 AND role = 'Admin'";
    $adminarraydata = getXbyY($slq_admingetdata);
    $usermodetoken = $adminarraydata[0]["user_token"];
} else {
    $usermodetoken = $user_token;
}

// optional external txn fetch (kept as you had)
$txn_data = @file_get_contents($site_url.'/phnpe/user_txn.php?no=' . urlencode($usermodetoken) . '');
if ($txn_data && strlen($txn_data) > 6) {
    $txn_data = substr($txn_data, 6);
    @json_decode($txn_data, true);
}

// get store/unit info and upi id
$slq_p = "SELECT * FROM store_id where user_token='" . addslashes($usermodetoken) . "'";
$res_pstore = getXbyY($slq_p);
$unitId = $res_pstore[0]['unitId'] ?? ($res_pmode[0]['company'] ?? 'Merchant');

$slq_p = "SELECT * FROM users where user_token='" . addslashes($usermodetoken) . "'";
$res_p_user = getXbyY($slq_p);
$upi_id = $res_p_user[0]['upi_id'] ?? ($res_pmode[0]['Upiid'] ?? 'fallback@upi');

$cxrmerchantTransactionId = $cxrkalwaremark;
$asdasd23 = "TXN" . rand(111, 999) . time() . rand(1, 100);

// Prepare UPI deeplink string (use proper formatting and 2 decimal amount)
$amount_formatted = number_format((float)$amount, 2, '.', '');
$orders = "upi://pay?pa=" . $upi_id . "&pn=" . rawurlencode($unitId) . "&am=" . $amount_formatted . "&cu=INR&tn=" . rawurlencode($asdasd23) . "&tr=" . rawurlencode($cxrmerchantTransactionId);

// ===== FIX: Use api.qrserver.com for QR image (reliable & scannable) =====
// encode the raw UPI string in the data parameter
$imbqr_code_url = "https://api.qrserver.com/v1/create-qr-code/?data=" . rawurlencode($orders) . "&size=400x400&ecc=H&margin=0";

// paytm intent (restore scheme with sign like before)
$paytm_sign = "AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr";
$paytmintent = "paytmmp://cash_wallet?pa=" . rawurlencode($upi_id) . "&pn=" . rawurlencode($unitId) . "&am=" . $amount_formatted . "&cu=INR&tn=" . rawurlencode($cxrmerchantTransactionId) . "&tr=" . rawurlencode($cxrmerchantTransactionId) . "&mc=4722&&sign=" . rawurlencode($paytm_sign) . "&featuretype=money_transfer";

// prepare safe_js for front-end
$safe_js = [
    'redirect_url' => $redirect_url,
    'upi_link' => $orders,
    'qrCodeUrl' => $imbqr_code_url,
    'amount' => $amount_formatted,
    'merchant' => $unitId,
    'byteTx' => $cxrmerchantTransactionId,
    'paytmintent' => $paytmintent,
    'upi_id' => $upi_id,
    'userlogo' => ($res_pmode[0]["logo"] ?? ''),
    'color_theme' => ($res_pmode[0]['color_theme'] ?? '#5E2C9D'),
    'pg_mode' => ($res_pmode[0]['pg_mode'] ?? 1),
    'pg_pby' => ($res_pmode[0]['pg_pby'] ?? 1),
    'plan_id' => ($res_pmode[0]['plan_id'] ?? '')
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?php echo htmlspecialchars($unitId); ?> - Pay</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="icon" href="<?php echo htmlspecialchars($res_pmode[0]["logo"] ? $site_url.'/merchant/'.$res_pmode[0]["logo"] : $site_url.'/payment/bag.jpg'); ?>" />

<style>
/* design from first code (kept same) */
* { margin:0;padding:0;box-sizing:border-box;font-family:'Roboto',sans-serif;}
body{background-color:#3366cc;color:#333;display:flex;justify-content:center;align-items:center;min-height:100vh;transition:background-color 8s ease;animation:bgColorChange 15s infinite alternate;overflow-x:hidden;position:relative;}
@keyframes bgColorChange{0%{background-color:#3366cc;}25%{background-color:#a777e3;}50%{background-color:#f3627d;}75%{background-color:#ff9a5a;}100%{background-color:#6e8efb;}}
.lines-bg{position:fixed;top:0;left:0;width:100%;height:100%;z-index:0;overflow:hidden;pointer-events:none;}
.line{position:absolute;background:rgba(255,255,255,0.15);}
.horizontal-line{height:1px;width:100%;animation:lineFlow 15s linear infinite;}
.vertical-line{width:1px;height:100%;animation:lineFlow 20s linear infinite;}
.diagonal-line{width:150%;height:1px;transform:rotate(45deg);transform-origin:left;animation:lineFlow 25s linear infinite;}
@keyframes lineFlow{0%{transform:translateX(-100%) rotate(45deg);}100%{transform:translateX(100%) rotate(45deg);}}
.container-wrapper{position:relative;width:100%;max-width:400px;z-index:10;}
.container{max-width:400px;width:95%;background:white;box-shadow:0 10px 30px rgba(0,0,0,0.2);border-radius:12px;overflow:hidden;margin:20px auto;opacity:0;transform:translateY(30px) scale(0.95);animation:fadeInUp 0.8s forwards 0.3s;position:relative;z-index:1;}
@keyframes fadeInUp{to{opacity:1;transform:translateY(0) scale(1);}}
.header{background-color:#3366cc;color:white;padding:15px;display:flex;align-items:center;justify-content:space-between;transform:translateY(-20px);opacity:0;animation:slideDown 0.6s forwards 0.5s;}
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
.qr-code{width:200px;height:200px;border:1px solid #eee;padding:10px;border-radius:8px;box-shadow:0 5px 15px rgba(0,0,0,0.1);transition:transform 0.3s;position:relative;z-index:1;object-fit:cover;}
.qr-overlay{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:50px;height:50px;background-color:white;border-radius:8px;display:flex;align-items:center;justify-content:center;z-index:2; padding: 5px;}
.qr-overlay img{width:40px;height:40px;object-fit:contain;}
.qr-code:hover{transform:scale(1.03);}
.qr-border{position:absolute;top:-15px;left:-15px;right:-15px;bottom:-15px;border:2px dashed #3366cc;border-radius:12px;z-index:0;animation:borderPulse 2s infinite;}
@keyframes borderPulse{0%{border-color:#3366cc;}50%{border-color:#a777e3;}100%{border-color:#3366cc;}}
.scan-text{color:#666;margin-bottom:15px;font-size:14px;opacity:0;animation:fadeIn 0.5s forwards 1.4s;}
.upi-apps{display:flex;justify-content:center;gap:10px;margin-bottom:15px;opacity:0;animation:fadeIn 0.5s forwards 1.5s;}
.upi-app-icon{width:30px;height:30px;border-radius:50%;transition:transform 0.3s;}
.upi-app-icon:hover{transform:scale(1.2);}
.action-buttons{display:flex;justify-content:center;gap:10px;margin-top:15px;opacity:0;animation:fadeIn 0.5s forwards 1.6s;}
.btn{padding:8px 15px;border-radius:6px;font-size:14px;cursor:pointer;border:none;display:flex;align-items:center;justify-content:center;transition:all 0.3s;}
.btn-primary{background:#3366cc;color:white;box-shadow:0 3px 10px rgba(51,102,204,0.3);}
.btn-primary:hover{background:#2a56b3;transform:translateY(-2px);box-shadow:0 5px 15px rgba(51,102,204,0.4);}
.btn-secondary{background:#f0f0f0;color:#333;}
.btn i{margin-right:5px;}
.payment-methods{padding:15px;opacity:0;animation:fadeIn 0.5s forwards 1.7s;}
.section-title{font-size:16px;font-weight:500;margin-bottom:15px;color:#333;}
.method-list{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
.method-item{border:1px solid #eee;border-radius:8px;padding:12px;display:flex;align-items:center;cursor:pointer;transition:all 0.3s;opacity:0;transform:translateY(10px);}
.method-item:nth-child(1){animation:fadeInUpItem 0.5s forwards 1.8s;}
.method-item:nth-child(2){animation:fadeInUpItem 0.5s forwards 1.9s;}
@keyframes fadeInUpItem{to{opacity:1;transform:translateY(0);}}
.method-item:hover{background:#f9f9f9;transform:translateY(-3px);box-shadow:0 5px 10px rgba(0,0,0,0.05);}
.method-icon{width:24px;height:24px;margin-right:8px;}
.method-name{font-size:14px;}
.footer{padding:15px;background:#f8f8f8;text-align:center;font-size:12px;color:#666;opacity:0;animation:fadeIn 0.5s forwards 2.2s;}
.footer-logo{height:18px;margin-top:10px;transition:transform 0.3s;}
.footer-logo:hover{transform:scale(1.1);}
.no-select{user-select:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;}
.no-context-menu{context-menu:none;}
.no-drag{-webkit-user-drag:none;-khtml-user-drag:none;-moz-user-drag:none;-o-user-drag:none;user-drag:none;}
.mobile-only{display:none;}
@media (max-width:768px){.mobile-only{display:block !important;}}
.hidden{display:none !important;}
.success-screen,.failure-screen{display:none;justify-content:center;align-items:center;flex-direction:column;position:fixed;top:0;left:0;width:100%;height:100%;color:white;opacity:0;transform:scale(0);animation:zoomIn 1s forwards,fadeIn 1.5s forwards;text-align:center;z-index:1000;}
.success-screen{background-color:#28a745;}
.failure-screen{background-color:#dc3545;}
.success-icon,.failure-icon{font-size:100px;margin-bottom:20px;color:white;}
.success-message,.failure-message{font-size:30px;font-weight:bold;}
.redirect-message{margin-top:20px;font-size:18px;}
@keyframes zoomIn{0%{opacity:0;transform:scale(0);}100%{opacity:1;transform:scale(1);}}
@keyframes fadeIn{0%{opacity:0;}100%{opacity:1;}}
@media (max-width:600px){.success-icon,.failure-icon{font-size:80px}.success-message,.failure-message{font-size:24px}.redirect-message{font-size:16px}}
.copy-upi { background:#fff;border:1px solid #ddd;padding:6px 10px;border-radius:6px;cursor:pointer;font-size:13px;}
</style>
</head>
<body class="no-select no-context-menu" oncontextmenu="return false;">
    <div class="lines-bg" id="linesBg"></div>

    <div class="container-wrapper">
        <div class="container no-drag">
            <div class="header" style="background-color: <?php echo htmlspecialchars($safe_js['color_theme'] ?? '#5E2C9D', ENT_QUOTES); ?>;">
                <div class="header-info">
                    <img src="<?php echo htmlspecialchars($res_pmode[0]["logo"] ? $site_url.'/merchant/'.$res_pmode[0]["logo"] : $site_url.'/payment/bag.jpg'); ?>" class="logo" alt="Merchant Logo">
                    <div>
                        <div class="merchant-name"><?php echo htmlspecialchars($unitId); ?></div>
                        <div class="verified-badge"><i class="fas fa-check-circle"></i> Verified</div>
                    </div>
                </div>
                <div class="amount-display">₹<?php echo number_format($amount, 2); ?></div>
            </div>

            <div class="timer">Complete payment in <span id="timeout">10:00</span></div>

            <div class="qr-section">
                <?php if (($res_pmode[0]['pg_qrcode'] ?? '1') == '1'): ?>
                <div class="qr-container">
                    <div class="qr-border" style="border-color: <?php echo htmlspecialchars($safe_js['color_theme'] ?? '#5E2C9D', ENT_QUOTES); ?>;"></div>
                    <!-- Use external QR (api.qrserver.com) so scanners can read it reliably -->
                    <img id="qr-img" src="<?php echo htmlspecialchars($imbqr_code_url, ENT_QUOTES); ?>" class="qr-code no-drag" alt="UPI QR Code">
                    <div class="qr-overlay">
                        <img src="<?php echo htmlspecialchars($res_pmode[0]["logo"] ? $site_url.'/merchant/'.$res_pmode[0]["logo"] : $site_url.'/payment/bag.jpg'); ?>" alt="Logo" class="no-drag">
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
                    <button class="btn btn-primary" onclick="downloadQR()" style="background-color: <?php echo htmlspecialchars($safe_js['color_theme'] ?? '#5E2C9D', ENT_QUOTES); ?>;"><i class="fas fa-download"></i> Save QR</button>
                    <button class="copy-upi" onclick="copyUpiId()">Copy UPI ID</button>
                </div>
                <?php endif; ?>
            </div>

            <div class="payment-methods">
                <div class="section-title">Pay with other methods</div>
                <div class="method-list">
                    <?php if (($res_pmode[0]['pg_intent1'] ?? '1') == '1'): ?>
                    <div class="method-item" onclick="shareQRCode();">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyVO9LUWF81Ov6LZR50eDNu5rNFCpkn0LwYQ&s" class="method-icon no-drag" alt="Google Pay">
                        <span class="method-name">Google Pay (Share QR)</span>
                    </div>
                    <?php endif; ?>

                    <?php if (($res_pmode[0]['pg_intent2'] ?? '1') == '1'): ?>
                    <div class="method-item" id="paytmMethod" onclick="openPaytmIntent('<?php echo htmlspecialchars($paytmintent, ENT_QUOTES); ?>','<?php echo htmlspecialchars($orders, ENT_QUOTES); ?>')">
                        <img src="https://w7.pngwing.com/pngs/305/719/png-transparent-paytm-ecommerce-shopping-social-icons-circular-color-icon-thumbnail.png" class="method-icon no-drag" alt="Paytm">
                        <span class="method-name">Pay via Paytm / UPI App</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer">
                <?php if (($res_pmode[0]['pg_pby'] ?? '1') == '1'): ?>
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
const SERVER = <?php echo json_encode($safe_js, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); ?>;

// background lines
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

// timer (10 minutes)
function upiCountdown(elm, minute, second) {
    document.getElementById(elm).innerHTML = minute + ":" + (second < 10 ? "0" + second : second);
    function startTimer() {
        var presentTime = document.getElementById(elm).innerHTML;
        var timeArray = presentTime.split(/[:]+/);
        var m = parseInt(timeArray[0],10);
        var s = parseInt(timeArray[1],10) - 1;
        if (s < 0) { s = 59; m = m - 1; }
        if (m < 0) {
            Swal.fire({ title: 'Timeout', text: 'Payment session has expired', icon: 'error' })
            .then(() => { window.location.href = SERVER.redirect_url; });
            return;
        }
        document.getElementById(elm).innerHTML = m + ":" + (s < 10 ? "0" + s : s);
        setTimeout(startTimer, 1000);
    }
    startTimer();
}
upiCountdown("timeout", 10, 0);

// Download QR
function downloadQR() {
    const img = document.getElementById('qr-img');
    if (!img) return Swal.fire('Error','QR not available','error');
    const src = img.src;
    if (src.startsWith('data:')) {
        const a = document.createElement('a');
        a.href = src;
        a.download = 'upi-<?php echo addslashes($cxrmerchantTransactionId); ?>.png';
        document.body.appendChild(a);
        a.click();
        a.remove();
        Swal.fire('Success','QR downloaded','success');
        return;
    }
    fetch(src, {mode:'cors'}).then(r => r.blob()).then(blob => {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'upi-<?php echo addslashes($cxrmerchantTransactionId); ?>.png';
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
        Swal.fire('Success','QR downloaded','success');
    }).catch(err => {
        console.error('downloadQR error', err);
        window.open(src, '_blank');
        Swal.fire('Info','Unable to download, opened in new tab. Save image manually.','info');
    });
}

// copy upi id
function copyUpiId() {
    const upi = SERVER.upi_id || '<?php echo htmlspecialchars($upi_id, ENT_QUOTES); ?>';
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

// share QR
async function shareQRCode() {
    try {
        const img = document.getElementById('qr-img');
        if (!img) return Swal.fire('Error','QR not found','error');
        const resp = await fetch(img.src, {mode:'cors'});
        const blob = await resp.blob();
        const file = new File([blob], 'upi-qr.png', { type: blob.type });
        if (navigator.canShare && navigator.canShare({ files: [file] })) {
            await navigator.share({ title: 'UPI QR Code', text: 'Scan this QR code to pay via UPI', files: [file] });
        } else if (navigator.share) {
            await navigator.share({ title: 'UPI Payment', text: 'Scan this UPI QR or use UPI link: ' + SERVER.upi_link });
        } else {
            window.open(img.src, '_blank');
            Swal.fire('Info', 'Sharing not supported. QR opened in new tab - save or share manually.', 'info');
        }
    } catch (err) {
        console.error('shareQRCode error', err);
        Swal.fire('Error', 'Could not share QR automatically. Opened image in new tab.', 'error');
        const img = document.getElementById('qr-img');
        if (img && img.src) window.open(img.src, '_blank');
    }
}

// platform helpers
function isMobileUA() { return /Android|iPhone|iPad|iPod/i.test(navigator.userAgent); }
function isIOS() { return /iPhone|iPad|iPod/i.test(navigator.userAgent) && !/Android/i.test(navigator.userAgent); }
function isAndroid() { return /Android/i.test(navigator.userAgent); }

// Paytm intent
function openPaytmIntent(paytmIntentUrl, fallbackUpi) {
    const upiLink = fallbackUpi || SERVER.upi_link;
    const paytmIntent = paytmIntentUrl || SERVER.paytmintent || '';

    if (isIOS()) {
        try {
            window.location.href = upiLink;
            setTimeout(function(){
                if (document.hidden || document.webkitHidden) return;
                Swal.fire({
                    icon: 'info',
                    title: 'Open UPI App',
                    html: 'If your UPI app did not open automatically, please open your UPI app and use this UPI ID:<br><b>' + (SERVER.upi_id || '<?php echo htmlspecialchars($upi_id, ENT_QUOTES); ?>') + '</b>',
                    showCancelButton: true,
                    confirmButtonText: 'Copy UPI ID',
                    cancelButtonText: 'Close'
                }).then((res) => {
                    if (res.isConfirmed) { copyUpiId(); }
                });
            }, 1200);
            return;
        } catch (e) {
            Swal.fire('Error', 'Unable to open UPI app automatically. Please copy the UPI ID and open your UPI app.', 'error');
            return;
        }
    }

    if (isAndroid() && paytmIntent) {
        const start = Date.now();
        window.location.href = paytmIntent;
        setTimeout(function() {
            if (Date.now() - start < 1400) {
                window.location.href = upiLink;
            }
        }, 900);
        return;
    }

    if (!isMobileUA()) {
        Swal.fire({
            icon: 'info',
            title: 'Open on Mobile',
            html: 'This payment method opens in mobile UPI apps. Please scan the QR or copy the UPI ID:<br><b>' + (SERVER.upi_id || '<?php echo htmlspecialchars($upi_id, ENT_QUOTES); ?>') + '</b>'
        });
        return;
    }

    try {
        window.location.href = upiLink;
    } catch (e) {
        Swal.fire('Error','Unable to open payment app automatically. Please scan the QR or copy the UPI ID.','error');
    }
}

// Polling payment status (order2 endpoint)
var checkinterval;
function check() {
    $.ajax({
        type: 'post',
        url: '<?= $site_url ?>/order2/payment-status',
        data: { byte_order_status: '<?php echo htmlspecialchars($cxrmerchantTransactionId, ENT_QUOTES); ?>' },
        success: function (data) {
            data = (data || '').trim();
            if (data == 'success') {
                clearInterval(checkinterval);
                myalert('success', "<?php echo $redirect_url ?>");
            } else if (data == 'FAILURE') {
                clearInterval(checkinterval);
                myalert('error', "<?php echo $redirect_url ?>");
            } else if (data == 'FAILED') {
                clearInterval(checkinterval);
                myalert('error', "<?php echo $redirect_url ?>", 'Your Transaction is failed due to getting wrong no of amount from your upi app ! try again later Note : This amount is not Refundable.');
            } else if (data != 'PENDING' && data != '') {
                clearInterval(checkinterval);
                myalert('error', "<?php echo $redirect_url ?>", data);
            }
        },
        error: function(xhr, status, error) {
            console.error('Payment check error:', error);
        }
    });
}
checkinterval = setInterval(check, 2000);

// success/failure UI
function myalert(type, href, text = '') {
    if (type == 'success') {
        document.getElementById('successScreen').style.display = 'flex';
        let countdown = 3; const countdownElement = document.getElementById('successCountdown');
        const interval = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;
            if (countdown === 0) { clearInterval(interval); window.location.href = href; }
        }, 1000);
    } else {
        document.getElementById('failureScreen').style.display = 'flex';
        if (text != '') { document.querySelector('.failure-message').innerHTML = text; }
        let countdown = 3; const countdownElement = document.getElementById('failureCountdown');
        const interval = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;
            if (countdown === 0) { clearInterval(interval); window.location.href = href; }
        }, 1000);
    }
}
</script>
</body>
</html>
