<?php
include "header.php";
?>

<?php
// ----------------- HELPERS -----------------
function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function isValidUrl($url){
    $parsed_url = parse_url($url);
    return isset($parsed_url['host']) && preg_match("/\.\w+$/", $parsed_url['host']);
}

if(isset($_POST['update_webhook'])){
    $bytecallbackurl = mysqli_real_escape_string($conn, $_POST['webhook_url']);

    if(!isValidUrl($bytecallbackurl)){
        echo '<script src="js/jquery-3.2.1.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>$("#loading_ajax").hide();Swal.fire({icon:"error",title:"Invalid webhook url!!",showConfirmButton:true,confirmButtonText:"Ok!",allowOutsideClick:false,allowEscapeKey:false}).then(()=>{window.location.href="developers";});</script>';
        exit();
    }

    // Assuming $mobile from header.php
    $sanitizedMobile = mysqli_real_escape_string($conn, $mobile);

    // $uniqueNumber is used in your original file; keep it if set
    if(isset($uniqueNumber)) { $key = md5($uniqueNumber); }

    $keyquery = "UPDATE `users` SET callback_url='$bytecallbackurl' WHERE mobile='$sanitizedMobile'";
    $queryres = mysqli_query($conn, $keyquery);

    echo '<script src="js/jquery-3.2.1.min.js"></script>';
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';

    if($queryres){
        echo '<script>$("#loading_ajax").hide();Swal.fire({icon:"success",title:"Webhook Updated Successfully",showConfirmButton:true,confirmButtonText:"Ok!",allowOutsideClick:false,allowEscapeKey:false}).then(()=>{window.location.href="developers";});</script>';
        exit;
    }else{
        echo '<script>$("#loading_ajax").hide();Swal.fire({icon:"error",title:"Error Updating Webhook. Try again later!!",showConfirmButton:true,confirmButtonText:"Ok!",allowOutsideClick:false,allowEscapeKey:false}).then(()=>{window.location.href="developers";});</script>';
        exit;
    }
}
?>

<style>
  :root {
    --primary: #4e73df;
    --primary-dark: #2e59d9;
    --bg: #f8f9fc;
    --card-bg: #ffffff;
    --text: #5a5c69;
    --text-head: #2c3e50;
    --border: #e3e6f0;
    --shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.10);
    --input-bg: #fff;
    --input-border: #d1d3e2;
  }

  body {
    background-color: var(--bg) !important;
    color: var(--text);
    font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  }

  /* ===== Header ===== */
  .app-title {
    background-color: var(--card-bg);
    border-radius: 10px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow);
    border-left: 5px solid var(--primary);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .pg-title {
    font-weight: 700;
    font-size: 24px;
    margin: 0;
    color: var(--text-head);
  }

  .subtext {
    font-size: 14px;
    color: #858796;
    margin-top: 4px;
  }

  /* ===== Clean White Cards ===== */
  .neo-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
  }

  .card-title {
    font-size: 14px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--primary);
    margin-bottom: 15px;
  }

  /* ===== Buttons ===== */
  .btn-neo {
    background-color: var(--primary);
    color: #fff;
    border: 1px solid var(--primary);
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
    box-shadow: 0 4px 6px rgba(78, 115, 223, 0.2);
  }

  .btn-neo:hover {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 6px 10px rgba(78, 115, 223, 0.3);
  }

  /* ===== Inputs & Textareas ===== */
  .form-control, 
  textarea.form-control,
  .form-control[readonly] {
    background-color: var(--input-bg) !important;
    border: 1px solid var(--input-border) !important;
    color: #495057 !important;
    border-radius: 8px;
    padding: 12px;
    font-size: 14px;
    box-shadow: none !important;
  }

  .form-control:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.15) !important;
  }
  
  /* Textarea specific for code */
  textarea.form-control {
    font-family: 'Consolas', 'Monaco', monospace;
    background-color: #f8f9fa !important; /* Light grey for code blocks */
    color: #2c3e50 !important;
    border: 1px solid #ced4da !important;
  }

  .label {
    font-size: 12px;
    font-weight: 700;
    color: #5a5c69;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
    display: block;
  }

  /* Icon circle (left rail) */
  .apidiconbox {
    width: 50px;
    height: 50px;
    background: rgba(78, 115, 223, 0.1);
    color: var(--primary);
    font-size: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Breadcrumb */
  .breadcrumb { background: transparent; margin: 0; padding: 0; }
  .breadcrumb-item a { color: var(--primary); text-decoration: none; }
  .breadcrumb-item.active { color: #858796; }

  /* Utilities */
  .tile { background: transparent !important; box-shadow: none; }
  .modal-content { border-radius: 12px; border: none; box-shadow: var(--shadow); }
  .modal-header { border-bottom: 1px solid var(--border); background-color: #f8f9fc; border-radius: 12px 12px 0 0; }
  .modal-footer { border-top: 1px solid var(--border); }
  .text-primary { color: var(--primary) !important; }
  
  /* Remove old override quirks */
  .bg-white, .bg-light { background-color: transparent !important; }
</style>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1 class="pg-title">Developers • API & Webhook</h1>
      <div class="subtext">Manage tokens, webhook URL & view API schemas</div>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
    </ul>
  </div>

  <div class="tile mb-4 neo-card p-3">
    <div class="row align-items-center">
      <div class="col-md-1 d-flex justify-content-center">
        <div class="apidiconbox"><i class="fa fa-key"></i></div>
      </div>
      <div class="col-md-11 pt-2">
        <div class="card-title">API Credentials</div>
        <p class="subtext mb-3">Generate and manage API credentials for secure & seamless integration.</p>
        <form class="row mb-2" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          <div class="col-md-8 mb-2">
            <label class="label">API Token</label>
            <div class="input-group">
              <input type="text" id="apiToken" placeholder="Click Generate Button for API Token" value="<?php echo safe($userdata['user_token']); ?>" class="form-control" readonly style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
              <div class="input-group-append">
                <button type="button" class="btn-neo" onclick="copyToken()" style="border-top-left-radius: 0; border-bottom-left-radius: 0;"><i class="fa fa-copy"></i> Copy</button>
              </div>
            </div>
            <small id="copyMessage" style="color: #28a745; display:none; font-weight:bold; margin-top:5px;">Copied!</small>
          </div>
          <div class="col-md-4 mb-2 d-flex align-items-end">
            <button type="button" data-toggle="modal" data-target="#apikeygenrateModal" class="btn-neo w-100">Generate API Token</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="tile mb-4 neo-card p-3">
    <div class="row align-items-center">
      <div class="col-md-1 d-flex justify-content-center">
        <div class="apidiconbox"><i class="fa fa-link"></i></div>
      </div>
      <div class="col-md-11 pt-2">
        <div class="card-title">Webhook URL</div>
        <p class="subtext mb-3">Receive real-time updates about transaction statuses.</p>
        <form class="row mb-2" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
          <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          <div class="col-md-8 mb-2">
            <label class="label">URL</label>
            <input type="url" name="webhook_url" placeholder="Enter your webhook URL" value="<?php echo safe($userdata['callback_url']); ?>" class="form-control" required pattern="https?://[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/?.*" title="Enter a valid URL (with http/https)">
          </div>
          <div class="col-md-4 mb-2 d-flex align-items-end">
            <button type="submit" name="update_webhook" class="btn-neo w-100">Update URL</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="row">
      <div class="col-md-12">
        <div class="tile mb-4 neo-card p-3">
            <div class="card-title" style="border-bottom: 1px solid #e3e6f0; padding-bottom: 10px; margin-bottom: 20px;">Create Order API</div>
            <form class="row mb-2" method="POST" action="#">
            <div class="col-md-12 mb-3">
                <label class="label">Endpoint URL</label>
                <input type="text" class="form-control" readonly value="https://<?php echo safe($server); ?>/api/create-order">
                <small class="text-danger mt-1 d-block"><i class="fa fa-info-circle"></i> Order Timeout 30 Minutes. Order will be automatically failed after 30 minutes.</small>
            </div>
            <div class="col-md-12 mb-3">
                <label class="label">Payload (application/x-www-form-urlencoded)</label>
                <textarea class="form-control" style="height: 190px;" readonly>{
  "customer_mobile": "8145344963",
  "user_token": "<?php echo safe($userdata['user_token']); ?>",
  "amount": "1",
  "order_id": "8787772321800",
  "redirect_url": "your website url",
  "remark1": "testremark",
  "remark2": "testremark2"
}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="label text-success">Success Response</label>
                <textarea class="form-control" style="height: 230px;" readonly>{
  "status": true,
  "message": "Order Created Successfully",
  "result": {
    "orderId": "1234561705047510",
    "payment_url": "https://yourwebsite.com/payment/pay.php?data=MTIzNDU2MTcwNTA0NzUxMkyNTIy"
  }
}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="label text-danger">Failed Response</label>
                <textarea class="form-control" style="height: 140px;" readonly>{
  "status": false,
  "message": "order_id already exists"
}</textarea>
            </div>
            </form>
        </div>
      </div>
  </div>

  <div class="tile mb-4 neo-card p-3">
    <div class="card-title" style="border-bottom: 1px solid #e3e6f0; padding-bottom: 10px; margin-bottom: 20px;">Check Order Status API</div>
    <form class="row mb-2" method="POST" action="#">
      <div class="col-md-12 mb-3">
        <label class="label">Endpoint URL</label>
        <input type="text" class="form-control" readonly value="https://<?php echo safe($server); ?>/api/check-order-status">
      </div>
      <div class="col-md-12 mb-3">
        <label class="label">Payload (application/x-www-form-urlencoded)</label>
        <textarea class="form-control" style="height: 120px;" readonly>{
  "user_token": "<?php echo safe($userdata['user_token']); ?>",
  "order_id": "8052313697"
}</textarea>
      </div>
      <div class="col-md-6 mb-3">
        <label class="label text-success">Success Response</label>
        <textarea class="form-control" style="height: 200px;" readonly>{
  "status": "COMPLETED",
  "message": "Transaction Successfully",
  "result": {
    "txnStatus": "COMPLETED",
    "resultInfo": "Transaction Success",
    "orderId": "784525sdD",
    "status": "SUCCESS",
    "amount": "1",
    "date": "2024-01-12 13:22:08",
    "utr": "454525454245"
  }
}</textarea>
      </div>
      <div class="col-md-6 mb-3">
        <label class="label text-danger">Failed Response</label>
        <textarea class="form-control" style="height: 140px;" readonly>{
  "status": "ERROR",
  "message": "Error Message"
}</textarea>
      </div>
    </form>
  </div>
</main>

<?php include "whatsapp.php"; ?>

<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="js/mainscript.js"></script>
<script src="js/plugins/pace.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>

<script>
  // Copy API token and show feedback
  function copyToken(){
    var token = document.getElementById('apiToken');
    token.select(); token.setSelectionRange(0, 99999);
    document.execCommand('copy');
    var copyMessage = document.getElementById('copyMessage');
    copyMessage.style.display = 'inline';
    setTimeout(function(){ copyMessage.style.display = 'none'; }, 1800);
  }
</script>

<div class="modal fade" id="apikeygenrateModal" tabindex="-1" role="dialog" aria-labelledby="apikeygenrateModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="apikeygenrateModalLabel" style="color: #333;">Confirm API Token Generation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="firstPage">
        <div class="text-center mb-3">
            <i class="fa fa-question-circle fa-3x text-primary"></i>
        </div>
        <p style="color: #555;">Do you want to generate a new API Token? Your old token will be immediately deactivated and replaced.</p>
        <div class="d-flex justify-content-center gap-2 mt-4">
            <button id="confirmGenerateButton" class="btn-neo mr-2">Generate New Token</button> 
            <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
      </div>
  </div>
</div>

<script>
$(document).ready(function(){
  // New handler for direct API token generation (without OTP)
  $('#confirmGenerateButton').click(function(){
    $("#loading_ajax").show();
    $.ajax({
      // Call user_settings to generate the token directly
      url:'backend/user_settings',
      type:'POST',
      // Send the flag to indicate token generation request
      data:{ get_api_token:true }, 
      success:function(response){
        $("#loading_ajax").hide();
        $('#apikeygenrateModal').modal('hide');
        let rslt = {};
        try{ rslt = JSON.parse(response); }catch(e){ rslt = { rescode:500, msg:'Unexpected response' }; }
        if(rslt.rescode == 200){
          Swal.fire({ 
            icon:'success', 
            title:'API Key Generated Successfully', 
            showConfirmButton:true, 
            confirmButtonText:'Ok!', 
            allowOutsideClick:false, 
            allowEscapeKey:false 
          }).then(()=>{ window.location.href='developers'; });
        }else{
          Swal.fire({ 
            icon:'error', 
            title: rslt.msg || 'Failed to generate API Token', 
            showConfirmButton:true, 
            confirmButtonText:'Ok!', 
            allowOutsideClick:false, 
            allowEscapeKey:false 
          });
        }
      },
      error:function(xhr,status,error){
        $("#loading_ajax").hide();
        Swal.fire({ icon:'error', title:'Error: '+error, showConfirmButton:true });
      }
    });
  });
});
</script>

<script src="./assets/vendors/jquery/dist/jquery.min.js" type="text/javascript"></script>
<script src="./assets/js/scripts/dashboard_1_demo.js" type="text/javascript"></script>
<script src="assets/js/app.min.js" type="text/javascript"></script>