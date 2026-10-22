<?php
// payment_page.php
// Full standalone page — replace your existing file with this (keeps include "header.php" dependency).
// Assumptions: header.php sets session, $conn (mysqli), and $userdata array (with id, name, user_token, aadhar_kyc, role, email, etc).
include "header.php";
if (session_status() == PHP_SESSION_NONE) session_start();

// Styles (kept same as your original, with amtbox hidden by default)
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Payment Page</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
.clipboard { position: relative; }
/* You just need to get this field */
.copy-input {
  max-width: 370px;
  width: 100%;
  cursor: pointer;
  background-color: #eaeaeb;
  border:none;
  color:#6c6c6c;
  font-size: 14px;
  border-radius: 5px;
  padding: 15px 45px 15px 15px;
  font-family: 'Montserrat', sans-serif;
}
.copy-input:focus { outline:none; }

.copy-btn {
  width:40px;
  background-color: #eaeaeb;
  font-size: 18px;
  padding: 6px 9px;
  border-radius: 5px;
  border:none;
  color:#6c6c6c;
  margin-left:-50px;
  transition: all .4s;
}
.copy-btn:hover { transform: scale(1.3); color:#1a1a1a; cursor:pointer; }
.copy-btn:focus { outline:none; }

.copied {
  font-family: 'Montserrat', sans-serif;
  width: 100px;
  opacity:0;
  position:fixed;
  bottom: 20px;
  left: 0;
  right: 0;
  margin: auto;
  color:#000;
  padding: 15px 15px;
  background-color: #fff;
  border-radius: 5px;
  transition: .4s opacity;
}

#amtbox { display:none; }
</style>
</head>
<body>

<?php
// Ensure $userdata exists
if (!isset($userdata) || !is_array($userdata)) {
    echo "<script>location.replace('dashboard');</script>";
    exit;
}

// redirect if aadhar_kyc not done (as original)
if ($userdata["aadhar_kyc"] == 1) {
    echo "<script> location.replace('dashboard?aadhar_kyc=0') </script>";
    exit;
}

// CSRF token init
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

// helper: encrypt token (kept your function but fixed IV length to 16 for AES-128-CTR)
function encrypt_token($simple_string){
    $ciphering = "AES-128-CTR";
    $options   = 0;
    // IV must be 16 bytes for AES-128-CTR
    $encryption_iv = substr('ThisIsSecretKeyForEncrytionByJisneYeBnaya!', 0, 16);
    $encryption_key = "imbbank";
    $encryption = openssl_encrypt($simple_string, $ciphering, $encryption_key, $options, $encryption_iv);
    return base64_encode($encryption);
}

// shorten_url function (kept as original)
function shorten_url($longUrl) {
    global $site_url;
    $apiUrl = $site_url.'/link/do.php';
    $data = json_encode(['long_url' => $longUrl,'server' => 2]);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);

    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['success' => false, 'error' => 'Curl error: '.$err];
    }

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $responseData = json_decode($response, true);

    if (isset($responseData['short_url'])) {
        return ['success' => true, 'short_url' => $responseData['short_url']];
    } else {
        $msg = isset($responseData['message']) ? $responseData['message'] : 'Unknown shortener error';
        return ['success' => false, 'error' => $msg, 'raw' => $response];
    }
}

// Initialize
$paymentlink = '';
$alerts = []; // collect notices/errors to show above form

// Handle form submission (both User and Admin allowed)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $alerts[] = ['type' => 'danger', 'text' => 'Invalid CSRF token. Refresh the page and try again.'];
    } else {
        // fetch & sanitize
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
        $remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';
        $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '0';
        $amountType = isset($_POST['amount_type']) ? trim($_POST['amount_type']) : '';
        $contact_form = isset($_POST['contact_form']) ? trim($_POST['contact_form']) : '1';

        // basic validation
        if ($name === '') $alerts[] = ['type' => 'danger', 'text' => 'Customer name required.'];
        if ($mobile === '' || !preg_match('/^[0-9]{6,15}$/', $mobile)) $alerts[] = ['type' => 'danger', 'text' => 'Valid mobile required.'];
        if ($amountType === '') $alerts[] = ['type' => 'danger', 'text' => 'Select amount type (Fixed/Non Fixed).'];
        if ($amountType == '1') {
            // fixed required and positive
            if (!is_numeric($amount) || floatval($amount) <= 0) $alerts[] = ['type' => 'danger', 'text' => 'Enter valid amount for Fixed type.'];
        } else {
            // non-fixed: set amount 0
            $amount = '0';
        }
        if ($remark === '') $remark = 'Payment For '.$userdata["name"];

        // KYC check (as original)
        if ($userdata["aadhar_kyc"] == '1') {
            // redirect with SweetAlert by showing message and stopping processing
            echo '
            <script src="js/jquery-3.2.1.min.js"></script>
            <script src="js/sweetalert2.min.js"></script>
            <script>
            Swal.fire({
                title: "Oops!",
                text: "Your KYC is Not Completed | Complete your KYC First For Settlement!",
                icon: "error",
                confirmButtonText: "Ok"
            }).then((r)=>{ window.location.href="kyc_process"; });
            </script>';
            exit;
        }

        // If no validation errors proceed
        $hasError = false;
        foreach ($alerts as $a) if ($a['type'] == 'danger') $hasError = true;

        if (!$hasError) {
            // prepare payload — use user's email if exists
            $email = isset($userdata['email']) ? $userdata['email'] : '';

            $data = array(
                'name' => $name,
                'email' => $email,
                'mobile' => $mobile,
                'user_token' => $userdata["user_token"],
                'amount' => $amount,
                'amount_type' => $amountType,
                'contact_form' => $contact_form,
                'remark' => $remark,
            );

            $json = json_encode($data);
            $encryptdata = encrypt_token($json);

            // prepare link
            $paymentlink_original = $site_url.'/merchant/paymentDetails?token='.$encryptdata;

            // try to shorten
            $short = shorten_url($paymentlink_original);
            if ($short['success']) {
                $paymentlink = $short['short_url'];
            } else {
                // if shortener fails, use original link but show warning
                $paymentlink = $paymentlink_original;
                $alerts[] = ['type' => 'warning', 'text' => 'Shortener warning: '.$short['error']];
            }

            // Optionally: Insert entry to local orders table (safe attempt; won't break if table differs)
            if (isset($conn) && $conn instanceof mysqli) {
                $order_id = 'PG'.time().rand(100,999);
                $stmt = @$conn->prepare("INSERT INTO orders (user_id, user_mode, customer_mobile, amount, order_id, payment_url, remark1, status, create_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $uid = $userdata['id'];
                    $user_mode = 1;
                    $status = 'PENDING';
                    $create_date = date('Y-m-d H:i:s');
                    // bind types accordingly: iisssssss (if DB accepts string types). If your DB differs change accordingly.
                    @$stmt->bind_param("iisssssss", $uid, $user_mode, $mobile, $amount, $order_id, $paymentlink, $remark, $status, $create_date);
                    @$stmt->execute();
                    @$stmt->close();
                }
            }
        }
    }
}
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-user-plus"></i> Payment Page</h1>
    </div>
    <ul class="app-breadcrumb breadcrumb">
      <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
      <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
    </ul>
  </div>

  <div class="tile mb-4">
    <div class="page-header">
      <div class="row">
        <div class="col-lg-12">
          <div class="row row-card-no-pd">
            <div class="col-md-12">

<?php
// Show form to both User and Admin (so Admin can also create payment page)
if (in_array($userdata["role"], ['User','Admin'])) {  ?>

  <div class="main-panel">
    <div class="content">
      <div class="container-fluid">
        <h4 class="page-title">Create Payment Page</h4>

        <?php
        // show alerts if any
        if (!empty($alerts)) {
            foreach ($alerts as $a) {
                $cls = ($a['type']=='danger') ? 'alert-danger' : (($a['type']=='warning') ? 'alert-warning' : 'alert-info');
                echo '<div class="alert '.$cls.'">'.htmlspecialchars($a['text'], ENT_QUOTES, 'UTF-8').'</div>';
            }
        }
        ?>

        <div class="row row-card-no-pd">
          <div class="col-md-12">
            <form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

              <div class="col-md-6 mb-2">
                <label>Customer Name</label>
                <input type="text" name="name" placeholder="Customer Name" class="form-control" required value="<?php echo isset($name) ? htmlspecialchars($name, ENT_QUOTES, 'UTF-8') : ''; ?>" />
              </div>

              <div class="col-md-6 mb-2">
                <label>Mobile Number</label>
                <input type="text" name="mobile" placeholder="Mobile Number" class="form-control" required value="<?php echo isset($mobile) ? htmlspecialchars($mobile, ENT_QUOTES, 'UTF-8') : ''; ?>" />
              </div>

              <div class="col-md-6 mb-2">
                <label>Get Customer Details</label>
                <select id="contact_form" name="contact_form" class="form-control" required>
                  <option value="1" <?php echo (isset($contact_form) && $contact_form=='1') ? 'selected' : ''; ?>>Yes</option>
                  <option value="0" <?php echo (isset($contact_form) && $contact_form=='0') ? 'selected' : ''; ?>>No</option>
                </select>
              </div>

              <div class="col-md-6 mb-2">
                <label>Amount Type</label>
                <select id="amount_type" name="amount_type" class="form-control" required>
                  <option value="">Select Type</option>
                  <option value="1" <?php echo (isset($amountType) && $amountType=='1') ? 'selected' : ''; ?>>Fixed</option>
                  <option value="0" <?php echo (isset($amountType) && $amountType=='0') ? 'selected' : ''; ?>>Non Fixed</option>
                </select>
              </div>

              <div class="col-md-6 mb-2" id="amtbox">
                <label>Amount (INR)</label>
                <input type="number" name="amount" step="0.01" value="<?php echo isset($amount) ? htmlspecialchars($amount, ENT_QUOTES, 'UTF-8') : '0'; ?>" class="form-control" />
              </div>

              <div class="col-md-6 mb-2">
                <label>Remark</label>
                <input type="text" name="remark" placeholder="Remarks Eg. Gift, Deposit etc." class="form-control" value="<?php echo isset($remark) ? htmlspecialchars($remark, ENT_QUOTES, 'UTF-8') : ''; ?>" />
              </div>

              <div class="col-md-12 mb-2 mt-2">
                <button type="submit" name="create" class="btn btn-primary btn-sm">Submit</button>
              </div>

            </form>
          </div>
        </div>

        <h4 class="page-title">List Of Payment Page</h4>
        <div class="row row-card-no-pd">
          <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered table-head-bg-primary" id="dataTable" width="100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Customer Mobile</th>
                    <th>Amount</th>
                    <th>Order id</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
<?php
$getst = isset($_GET["getfundst"]) ? $_GET["getfundst"] : '';
$query = "SELECT * FROM `orders` WHERE user_id='".intval($userdata["id"])."' AND user_mode = '1' ORDER BY `id` DESC LIMIT 5";
$query_run = mysqli_query($conn, $query);
if ($query_run) {
    while ($row = mysqli_fetch_assoc($query_run)) {
        if($row['status'] == 'SUCCESS'){
            $st = '<span class="badge badge-success">Success</span>';
        } else if($row['status'] == 'FAILURE'){
            $st = '<span class="badge badge-danger">Failed</span>';
        } else {
            $st = '<span class="badge badge-warning">Pending</span>';
        }

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['customer_mobile'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['order_id'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . $st . "</td>";
        echo "<td>" . htmlspecialchars($row['remark1'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['create_date'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>Error in query: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . "</td></tr>";
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

<?php } else { // non-user role view (same as original admin listing) ?>

  <div class="main-panel">
    <div class="content">
      <div class="container-fluid">
        <h4 class="page-title">List Of Payment Page</h4>
        <div class="row row-card-no-pd">
          <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-sm table-hover table-bordered table-head-bg-primary" id="dataTable" width="100%">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>UTR Number</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
<?php
$getst = isset($_GET["getfundst"]) ? $_GET["getfundst"] : '';
$query = "SELECT * FROM `settlement` WHERE status='".mysqli_real_escape_string($conn,$getst)."'";
$query_run = mysqli_query($conn, $query);
if ($query_run) {
    while ($row = mysqli_fetch_assoc($query_run)) {
        $fetchuser = $conn->query("SELECT * FROM `users` WHERE id = '".intval($row["userid"])."'")->fetch_assoc();
        if($row['status'] == 1){
            $st = '<span class="badge badge-success">Success</span>';
        } else if($row['status'] == 0){
            $st = '<span class="badge badge-danger">Rejected</span>';
        } else {
            $st = '<span class="badge badge-warning">Pending</span>';
        }

        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($fetchuser['name'], ENT_QUOTES, 'UTF-8') . " Mobile -" . htmlspecialchars($fetchuser['mobile'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['utr_no'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . $st . "</td>";
        echo "<td>" . htmlspecialchars($row['remark'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7'>Error in query: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . "</td></tr>";
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

<?php } // end role check ?>

        <?php if($paymentlink != ''){ ?>
        <!--confirm Modal -->
<div class="modal fade" id="copypaymentlinkmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-confirm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-primary w-100">Payment Page Created Successfully</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="clipboard">
<input onclick="copy()" class="copy-input" value="<?php echo htmlspecialchars($paymentlink, ENT_QUOTES, 'UTF-8'); ?>" id="copyClipboard" readonly>
<button class="copy-btn" id="copyButton" onclick="copy()"><i class="fa fa-copy"></i></button>
</div>
<div id="copied-success" class="copied">
  <span>Link Copied !</span>
</div>
       <p>This Payment Page is valid for only 10 min.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="copy()" id="changeusrupimodebtn">Copy</button>
      </div>
    </div>
  </div>
</div>
        <?php } ?>

      </div>
    </div>
  </div>
</div></div></div></div></main>

<!-- Essential javascripts for application to work (make sure files exist in your project) -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

<?php if($paymentlink != ''){ ?>
<script>
 // show modal if link created
 $(document).ready(function(){ $("#copypaymentlinkmodal").modal("show"); });
</script>
<?php } ?>

<script>
$(document).ready(function(){
  // show/hide amount box depending on amount_type
  function toggleAmtBox(){
    if ($("#amount_type").val() == '1') {
      $("#amtbox").show();
    } else {
      $("#amtbox").hide();
    }
  }
  $("#amount_type").on('change', toggleAmtBox);
  toggleAmtBox(); // initial

  $("#frubtn").click(function(){ $("#changefrform").slideToggle(); });
});

function copy() {
  let copyText = document.getElementById("copyClipboard");
  let copySuccess = document.getElementById("copied-success");
  if(!copyText) return;
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(copyText.value).then(function(){
      copySuccess.style.opacity = "1";
      setTimeout(function(){ copySuccess.style.opacity = "0"; }, 800);
    }, function(){
      copySuccess.style.opacity = "1";
      setTimeout(function(){ copySuccess.style.opacity = "0"; }, 800);
    });
  } else {
    try {
      document.execCommand('copy');
      copySuccess.style.opacity = "1";
      setTimeout(function(){ copySuccess.style.opacity = "0"; }, 800);
    } catch (e) {
      console.log('copy failed', e);
    }
  }
}
</script>

</body>
</html>
