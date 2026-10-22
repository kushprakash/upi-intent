<?php 
include "header.php"; 

// ================== HANDLE SAVE ==================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {

    $file_path = null; // default: no new logo

    // Validate and upload the logo (optional)
    if (isset($_FILES['clogo']) && $_FILES['clogo']['error'] == 0) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['clogo']['name'];
        $file_size = $_FILES['clogo']['size'];
        $file_tmp  = $_FILES['clogo']['tmp_name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_extensions)) {
            echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>$("#loading_ajax").hide();Swal.fire({icon:"error",title:"Invalid file type",text:"Only JPG, JPEG, PNG, and GIF files are allowed."}).then(()=>{window.location.href="business_profile"});</script>';
            exit;
        }

        if ($file_size > 2097152) { // 2MB
            echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>$("#loading_ajax").hide();Swal.fire({icon:"error",title:"Logo size must be less than 2MB."}).then(()=>{window.location.href="business_profile"});</script>';
            exit;
        }

        $upload_dir = 'assets/company_logo/';
        if(!is_dir($upload_dir)){ @mkdir($upload_dir,0775,true); }
        $file_path = $upload_dir . uniqid('logo_', true) . '.' . $file_ext;

        if (!move_uploaded_file($file_tmp, $file_path)) {
            echo '<script src="js/jquery-3.2.1.min.js"></script>';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
            echo '<script>$("#loading_ajax").hide();Swal.fire({icon:"error",title:"Failed to upload file!"}).then(()=>{window.location.href="business_profile"});</script>';
            exit;
        }
    }

    // Prepare other data
    $theme_color = $_POST['theme_color'];

    // Build update query (if logo not uploaded, don’t overwrite it)
    if ($file_path) {
        $query = "UPDATE users SET logo = '".$conn->real_escape_string($file_path)."', color_theme = '".$conn->real_escape_string($theme_color)."' WHERE id = '{$userdata["id"]}'";
    } else {
        $query = "UPDATE users SET color_theme = '".$conn->real_escape_string($theme_color)."' WHERE id = '{$userdata["id"]}'";
    }

    if (mysqli_query($conn, $query)) {
        echo '<script src="js/jquery-3.2.1.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>$("#loading_ajax").hide();Swal.fire({icon:"success",title:"Profile Updated Successfully."}).then(()=>{window.location.href="business_profile"});</script>';
        exit;
    } else {
        $err = mysqli_error($conn);
        echo '<script src="js/jquery-3.2.1.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>$("#loading_ajax").hide();Swal.fire({icon:"error",title:"Update failed",text:'.json_encode($err).'}).then(()=>{window.location.href="business_profile"});</script>';
        exit;
    }
}

// ================== CURRENT VALUES ==================
if($userdata["logo"] == ''){
    $userlogo = "https://upigateway.sahajbazar.in/payment/bag.jpg";
}else{
    $userlogo = 'https://'.$_SERVER["SERVER_NAME"].'/merchant/'.$userdata["logo"];
}

if($userdata["color_theme"] == ''){
    $colortheme = "#25a6a1";
}else{
    $colortheme = $userdata["color_theme"];
}
?>

<!-- Tiny helper widget -->
<div id="emoji-widget">
    <img src="https://cdnl.iconscout.com/lottie/premium/thumb/customer-care-8147491-6529814.gif" alt="GIF Widget">
</div>
<audio id="widget-audio" src="https://upigateway.sahajbazar.in/Voice/checkouts.mp3"></audio>
<script>
document.getElementById('emoji-widget').onclick = () => document.getElementById('widget-audio').play();
</script>

<!-- ================== LIGHT / WHITE THEME ================== -->
<style>
:root{
  /* Light theme variables */
  --bg: #f7f9fc;       /* page background */
  --bg2: #ffffff;      /* secondary background */
  --card: #ffffff;     /* card background */
  --card2: #fbfdff;    /* card subtle */
  --text: #0f1724;     /* main text (dark) */
  --muted: #6b7280;    /* muted text */
  --line: #e6eef8;     /* light border */
  --primary: #2563eb;  /* primary blue */
  --accent: #7c3aed;   /* accent purple */
  --success: #059669;
  --warn: #d97706;
  --danger: #dc2626;
  --shadow: 0 20px 40px rgba(16,24,40,.06);
}

/* Base / layout */
body{ background: linear-gradient(180deg,var(--bg), #eef6ff) !important; color:var(--text); line-height:1.2; font-family: "Roboto", sans-serif; }
a{ text-decoration:none !important; color:var(--primary); }
.app-content, .tile, .page-header{ background:transparent !important; border:none !important; }

/* Title bar - light */
.app-title{
  background: linear-gradient(180deg, rgba(37,99,235,0.06), rgba(124,58,237,0.02)) !important;
  border:1px solid var(--line) !important;
  border-radius:12px; box-shadow:var(--shadow); margin-bottom:14px;
}
.app-title i.fa, .app-breadcrumb .breadcrumb-item, .app-breadcrumb .breadcrumb-item a{ color:var(--text) !important; }
.app-title h1{
  font-weight:800; letter-spacing:.25px; margin:0;
  background:linear-gradient(90deg,var(--primary), var(--accent));
  -webkit-background-clip:text; background-clip:text; color:transparent !important;
  text-shadow:0 6px 18px rgba(124,58,237,.06);
}

/* Cards (light) */
.card{
  background: linear-gradient(180deg, var(--card), var(--card2)) !important;
  border:1px solid var(--line) !important;
  border-radius:12px !important; box-shadow:var(--shadow); color:var(--text);
}
.card .card-title{ font-size:15px; font-weight:600; }
.text-muted{ color:var(--muted) !important; }

/* Buttons */
.btn{
  border-radius:10px !important; font-weight:700 !important;
  border:1px solid rgba(15,23,36,.06) !important; color:#fff !important;
  box-shadow:0 8px 24px rgba(16,24,40,.06) !important;
}
.btn-primary{
  background:linear-gradient(180deg, var(--primary), #1d4ed8) !important;
  border-color: rgba(29,78,216,.2) !important;
}
.btn-secondary{
  background:linear-gradient(180deg, #f3f4f6, #e6eef8) !important;
  color:var(--text) !important;
  border-color: rgba(15,23,36,.06) !important;
}

/* Form controls - light */
.form-control{
  background: #ffffff !important;
  border:1px solid var(--line) !important;
  color:var(--text) !important; border-radius:8px !important;
  padding:8px 12px;
}
.form-control::placeholder{ color:rgba(15,23,36,.45) !important; }
label{ color:var(--text); font-weight:600; }

/* Switch (same shape, light) */
.switch { position:relative; display:inline-block; width:65px; height:33px; margin-bottom:0; margin-left:8px; }
.switch input{ display:none; }
.slider{
  position:absolute; cursor:pointer; inset:0; background-color:#f1f5f9; transition:.3s;
  border-radius:34px; border:1px solid rgba(15,23,36,.06);
}
.slider:before{
  position:absolute; content:""; height:22px; width:22px; left:4px; bottom:4px; background-color:#94a3b8;
  border-radius:50%; transition:.3s; box-shadow:0 6px 18px rgba(16,24,40,.06);
}
input:checked + .slider{ background:linear-gradient(180deg,var(--success), #059669); border-color:rgba(5,150,105,.25); }
input:checked + .slider:before{ transform:translateX(30px); background-color:#fff; box-shadow:0 6px 18px rgba(0,0,0,.06); }
.switch-title{ font-size:14px; font-weight:600; color:var(--text) }

/* Checkbox baseline */
[type="checkbox"]:not(:checked), [type="checkbox"]:checked{ position:unset !important; left:unset !important; }

/* QR PREVIEW WRAPPER (light) */
.qr-wrapper{
  background: linear-gradient(180deg, #ffffff, #fbfdff);
  border:1px solid var(--line);
  border-radius:12px;
  max-width: 600px; margin: 20px auto; box-shadow:var(--shadow); color:var(--text);
}
.b2bHeader{
  display:flex; justify-content:space-between; align-items:center; padding:12px 14px;
  background-color: <?= $colortheme ?>; color:#fff; border-radius:12px 12px 0 0; margin-bottom:12px;
}
.b2bHeader img{ width:50px; border-radius:10px; background:#fff; padding:4px; }
.contentContainer{ text-align:center; padding: 12px 18px 18px; }
.qr-image{ width:150px; margin:10px auto; display:block; filter: drop-shadow(0 12px 30px rgba(16,24,40,.08)); }
.payment-option{
  display:flex; justify-content:space-between; align-items:center; padding:10px 12px;
  border:1px solid var(--line); border-radius:10px; margin-bottom:10px; background: #fff;
  box-shadow: 0 4px 14px rgba(16,24,40,.04);
}
.payment-option img{ width:26px; }
.intentbox{ padding: 14px 16px; }
.footer{ text-align:center; font-size:14px; padding: 10px; color:var(--muted); }
.footer img{ width:80px; }

/* Copy toast */
.copied{
  width: 160px; opacity:0; position:fixed; bottom:24px; left:0; right:0; margin:auto; text-align:center;
  color:#0b0d12; background:#e7ecf3; padding:10px 14px; border-radius:10px; font-weight:700; transition:.35s opacity;
  box-shadow:0 16px 36px rgba(16,24,40,.08);
}
</style>

<!-- Your additional vanilla styles kept (only minimal tweaks to keep consistency) -->
<style>
.d-none{ display:none; }
.m-primary{ background:#1f6feb !important; color:#fff !important; }
.form-check{ display:block; min-height:1.3125rem; padding-left:1.8em; margin-bottom:0.125rem; }
.form-check .form-check-input{ float:left; margin-left:-1.8em; }
.form-check-input{ width:1em; height:1em; margin-top:0.1em; background:#fff; border:1px solid rgba(0,0,0,.06); }
.form-check-input:checked{ background-color:#2563eb; border-color:#2563eb; }
.form-check-input:focus{ border-color:#cbd1db; outline:0; box-shadow:none; }
.card .card-category{ font-size:14px; font-weight:600; }
.card .card-title{ line-height:1.6; }
.rl-loading-container{ display:flex; justify-content:center; align-items:center; padding:10px; }
#loading_ajax{ background:rgba(255,255,255,.8); position:fixed; inset:0; z-index:9998; }
.rl-loading-thumb{ width:10px; height:40px; background-color:#1e90ff; margin:4px; box-shadow:0 0 12px 3px rgba(30,144,255,.12); animation:rl-loading 1.5s ease-in-out infinite; }
.rl-loading-thumb-1{ animation-delay:0s; } .rl-loading-thumb-2{ animation-delay:.5s; } .rl-loading-thumb-3{ animation-delay:1s; }
@keyframes rl-loading{ 20%{background:#fff; transform:scale(1.5);} 40%{background:#1e90ff; transform:scale(1);} }
</style>

<script>
// live theme preview on color input
function updatePreview(){
  const c = document.getElementById('theme_color').value;
  document.getElementById('previewThemeColor').style.backgroundColor = c;
}
document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('theme_color');
  if(el){ el.addEventListener('input', updatePreview); }
});
</script>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-desktop"></i> Checkout Customization</h1>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
    </ul>
  </div>

  <div class="hk-pg-body pt-1">
    <div class="tab-content mt-2">
      <div class="tab-pane fade show active" id="tab_block_1">
        <div class="row">
          <div class="col-12">
            <div class="card card-border">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-8 flex-column">
                    <h3 class="mb-1 content-heading">Flexible Integration and Configuration Options Setting</h3>
                    <p class="text-muted mb-2">From branding elements like logos and color name to user interface adjustments our platform.</p>

                    <div class="row">
                      <div class="col-lg-12"><hr style="border-color:var(--line)"></div>
                      <div class="col-lg-10">

                        <form class="pgcustomize-form mb-2" method="POST" action="" enctype="multipart/form-data">
                          <div class="form-group mb-3">
                            <label class="control-label d-block" for="clogo">Company Logo</label>
                            <button class="btn btn-primary" type="button" id="clogouploadbtn">Upload Logo&nbsp;<i class="fa fa-file"></i></button>
                            <input class="form-control" id="clogo" name="clogo" type="file" style="display:none;">
                          </div>

                          <div class="form-group mb-3">
                            <label class="control-label d-block" for="theme_color">Theme Color</label>
                            <input class="form-control" id="theme_color" name="theme_color" style="height: 50px;" value="<?=$colortheme?>" type="color" required>
                          </div>

                          <div class="form-group">
                            <div class="btn-container">
                              <button class="btn btn-primary btn-block" type="submit" name="submit">Save</button>
                            </div>
                          </div>
                        </form>

                        <h3 class="mb-1 content-heading">Manage your payment methods</h3>
                        <p class="text-muted mb-3">Just manage easy for your customer to use our payment methods in our platform.</p>

                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px 18px;">
                          <div class="switch-container d-flex align-items-center">
                            <span class="switch-title">QR Code</span>
                            <label class="switch">
                              <input type="checkbox" data-service="qrcode" <?php if($userdata['pg_qrcode'] == 1){ echo 'checked'; } ?> class="updateservice_btn">
                              <span class="slider"></span>
                            </label>
                          </div>

                          <div class="switch-container d-flex align-items-center">
                            <span class="switch-title">Powered By Logo</span>
                            <label class="switch">
                              <input type="checkbox" data-service="pby" <?php if($userdata['pg_pby'] == 1){ echo 'checked'; } ?> class="updateservice_btn">
                              <span class="slider"></span>
                            </label>
                          </div>

                          <div class="switch-container d-flex align-items-center">
                            <span class="switch-title">Google Pay Intent</span>
                            <label class="switch">
                              <input type="checkbox" data-service="intent1" <?php if($userdata['pg_intent1'] == 1){ echo 'checked'; } ?> class="updateservice_btn">
                              <span class="slider"></span>
                            </label>
                          </div>

                          <div class="switch-container d-flex align-items-center">
                            <span class="switch-title">Paytm Intent</span>
                            <label class="switch">
                              <input type="checkbox" data-service="intent2" <?php if($userdata['pg_intent2'] == 1){ echo 'checked'; } ?> class="updateservice_btn">
                              <span class="slider"></span>
                            </label>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>

                  <!-- Preview -->
                  <div class="col-md-4">
                    <div class="qr-wrapper">
                      <div class="b2bHeader" id="previewThemeColor">
                        <img src="<?= $userlogo ?>" id="logo_preview1" alt="Logo">
                        <div style="text-align:right;">
                          <span style="opacity:.95;">Payable Amount</span>
                          <h4 style="margin:0;">₹100.00</h4>
                        </div>
                      </div>

                      <div class="contentContainer">
                        <?php if($userdata['pg_qrcode'] == '1'){ ?>
                          <h5 class="text-left" style="display:flex;align-items:center;gap:8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18">
                              <path fill="#0f1724" d="M4,4h6v6H4V4M20,4v6H14V4h6M14,15h2V13H14V11h2v2h2V11h2v2H18v2h2v3H18v2H16V18H13v2H11V16h3V15m2,0v3h2V15H16M4,20V14h6v6H4M6,6V8H8V6H6M16,6V8h2V6H16M6,16v2H8V16H6M4,11H6v2H4V11m5,0h4v4H11V13H9V11m2-5h2v4H11V6M2,2V6H0V2A2,2,0,0,1,2,0H6V2H2M22,0a2,2,0,0,1,2,2V6H22V2H18V0h4M2,18v4H6v2H2a2,2,0,0,1-2-2V18H2m20,4V18h2v4a2,2,0,0,1-2,2H18V22Z"></path>
                            </svg>
                            Scan QR Code to Pay
                          </h5>
                          <p class="text-left" style="margin-top:-6px;color:var(--muted)">Open UPI app and scan</p>
                          <img src="https://business.tascostudio.com/qr-code.png" alt="QR Code" class="qr-image">
                          <p style="color:var(--muted)">Checking payment status...</p>
                        <?php } ?>
                      </div>

                      <?php if($userdata['pg_intent1'] == '1' || $userdata['pg_intent2'] == '1'){ ?>
                        <div class="intentbox">
                          <h5 style="display:flex;align-items:center;gap:8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#0f1724" class="bi bi-send" viewBox="0 0 16 16">
                              <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                            </svg>
                            Pay Using UPI Apps
                          </h5>
                          <p style="color:var(--muted);margin-top:-2px;">Click UPI App and Pay</p>

                          <?php if($userdata['pg_intent2'] == '1'){ ?>
                            <div class="payment-option" onclick="openPaytmIntent('<?php echo htmlspecialchars($paytmintent,ENT_QUOTES); ?>','<?php echo htmlspecialchars($upi_link,ENT_QUOTES); ?>')">
                              <div class="d-flex align-items-center">
                                <img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/paytm.png" alt="Paytm">
                                <span class="ms-2" style="margin-left:8px;">Paytm UPI</span>
                              </div>
                              <img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/arrow.svg" alt="Arrow">
                            </div>
                          <?php } ?>

                          <?php if($userdata['pg_intent1'] == '1'){ ?>
                            <div class="payment-option" onclick="shareQRCode();">
                              <div class="d-flex align-items-center">
                                <img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/googlepay-circle.svg" alt="Google Pay">
                                <span class="ms-2" style="margin-left:8px;">Share Google Pay</span>
                              </div>
                              <img src="https://<?= $_SERVER["SERVER_NAME"] ?>/payment6/Img/arrow.svg" alt="Arrow">
                            </div>
                          <?php } ?>
                        </div>
                      <?php } ?>

                      <?php if($userdata['pg_pby'] == 1){ ?>
                        <div class="footer">
                          Powered by <img src="https://<?= $_SERVER["SERVER_NAME"] ?>/merchant/Logo/KJ.png" alt="Powered">
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                  <!-- /Preview -->

                </div>
              </div>
            </div>
          </div> <!-- /col-12 -->
        </div>
      </div>
    </div>
  </div>
</main>

<!-- JS -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="js/mainscript.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.7.8/lottie.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Optional animation container exists in your commented block; safe to keep
  const animEl = document.getElementById('pgcustomizeanim');
  if(animEl){
    lottie.loadAnimation({
      container: animEl,
      renderer: 'svg',
      loop: true,
      autoplay: true,
      path: 'assets/imbpg_img/pg_customization_anim.json'
    });
  }

  // Logo preview
  let inputlogobtn = document.getElementById("clogouploadbtn");
  let inputlogo    = document.getElementById("clogo");
  let logopreview1 = document.getElementById("logo_preview1");
  if(inputlogobtn && inputlogo){
    inputlogobtn.addEventListener('click', function(){ inputlogo.click(); });
    $('#clogo').on('change', (event) => {
      if(event.target.files && event.target.files[0]){
        let imgfile = window.URL.createObjectURL(event.target.files[0]);
        logopreview1.src = imgfile;
      }
    });
  }
});
</script>
</body>
</html>
