<?php
// PaymentLink Page - full file
// NOTE: This assumes header.php sets session, $conn (mysqli), and $userdata array.
include "header.php";
if(session_status() == PHP_SESSION_NONE) session_start();

// Basic CSS (your existing styles)
?>
<style>
.clipboard {
  position: relative;
}
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
  width: 120px;
  opacity:0;
  position:fixed;
  bottom: 20px;
  left: 0;
  right: 0;
  margin: auto;
  color:#000;
  padding: 12px 15px;
  background-color: #fff;
  border-radius: 5px;
  transition: .4s opacity;
}
</style>

<?php
// redirect if aadhar_kyc not done
if (!isset($userdata) || !isset($userdata['aadhar_kyc'])) {
    // if $userdata missing, try to handle gracefully
    echo "<script> location.replace('dashboard') </script>";
    exit;
}
if($userdata["aadhar_kyc"] == 1){
    echo "<script> location.replace('dashboard?aadhar_kyc=0') </script>";
    exit;
}

// Initialize variables
$paymentlink = "";
$errors = [];

/**
 * Helper: shorten_url using your existing service
 */
function shorten_url($longUrl) {
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

    $response = curl_exec($ch);

    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['success'=>false, 'error' => "Curl Error: $err"];
    }

    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $responseData = json_decode($response, true);
    if ($httpcode >= 200 && $httpcode < 300 && isset($responseData['short_url'])) {
        return ['success'=>true, 'short_url' => $responseData['short_url']];
    } else {
        // try to provide message if exists
        $msg = isset($responseData['message']) ? $responseData['message'] : 'Unknown error from shortener';
        return ['success'=>false, 'error' => $msg, 'raw' => $response];
    }
}

// CSRF token ensure
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}

// Handle form submit (Admin or User can submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $errors[] = "Invalid CSRF token. Please refresh the page and try again.";
    } else {
        // sanitize inputs
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
        $remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';
        $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';

        // basic validation
        if ($name === '') $errors[] = "Customer name is required.";
        if ($mobile === '' || !preg_match('/^[0-9]{6,15}$/', $mobile)) $errors[] = "Valid mobile number is required.";
        if ($amount === '' || !is_numeric($amount) || floatval($amount) <= 0) $errors[] = "Valid amount is required.";

        if ($remark === '') $remark = 'Your Payment Link is Created';

        if (empty($errors)) {
            $orderid = mt_rand(10000000000,9999999999999);
            // prepare data for create-order API (as your original)
            $data = array(
                'customer_mobile' => $mobile,
                'user_token' => $userdata["user_token"],
                'amount' => $amount,
                'order_id' => $orderid,
                'redirect_url' => $site_url.'/success',
                'remark1' => $remark,
                'customer_name' => $name
            );

            $curl = curl_init();
            curl_setopt_array($curl, array(
               CURLOPT_URL => $site_url.'/api/create-order',
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_ENCODING => '',
               CURLOPT_MAXREDIRS => 10,
               CURLOPT_TIMEOUT => 30,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
               CURLOPT_CUSTOMREQUEST => 'POST',
               CURLOPT_POSTFIELDS => http_build_query($data),
               CURLOPT_HTTPHEADER => array(
                  'User-Agent: Apidog/1.0.0 (https://apidog.com)'
               ),
            ));

            $response = curl_exec($curl);
            $curl_err = curl_error($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($response === false || $curl_err) {
                $errors[] = "Failed to create payment link (curl error): " . $curl_err;
            } else {
                $jsondatares = json_decode($response,true);
                if (!is_array($jsondatares) || !isset($jsondatares['result']['payment_url'])) {
                    $msg = isset($jsondatares['message']) ? $jsondatares['message'] : 'Invalid response from payment API';
                    $errors[] = "API Error: " . $msg;
                } else {
                    $paymentlink = $jsondatares["result"]["payment_url"];
                    // try shorten
                    $short = shorten_url($paymentlink);
                    if ($short['success']) {
                        $paymentlink = $short['short_url'];
                    } else {
                        // shortener failed — still allow using original link but log error
                        // you can log $short['error'] or show to admin
                        // we'll proceed with original paymentlink but notify
                        $errors[] = "Shortener warning: " . $short['error'];
                        // keep $paymentlink as original
                    }

                    // OPTIONAL: Insert into local orders table if you want immediate listing
                    // Comment/remove if your API already inserts into `orders`.
                    if (isset($conn) && $conn instanceof mysqli) {
                        $stmt = $conn->prepare("INSERT INTO orders (user_id, user_mode, customer_mobile, amount, order_id, payment_url, remark1, status, create_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        if ($stmt) {
                            $uid = $userdata['id'];
                            $user_mode = 1;
                            $status = 'PENDING';
                            $create_date = date('Y-m-d H:i:s');
                            $stmt->bind_param("iisssssss", $uid, $user_mode, $mobile, $amount, $orderid, $paymentlink, $remark, $status, $create_date);
                            // Note: if orders schema different, adjust fields accordingly. Wrap in @ to avoid fatal if mismatch.
                            @ $stmt->execute();
                            $stmt->close();
                        }
                    }
                }
            }
        }
    }
}
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-user-plus"></i> Payment Link</h1>
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
// Show form for User AND Admin (change roles as needed)
if (in_array($userdata["role"], ['User','Admin'])) {  ?>
  <div class="main-panel">
    <div class="content">
      <div class="container-fluid">
        <h4 class="page-title">Create Payment Link</h4>
        <div class="row row-card-no-pd">
          <div class="col-md-12">
            <?php
            // show errors/warnings
            if (!empty($errors)) {
                echo '<div class="alert alert-warning">';
                foreach ($errors as $e) {
                    echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8') . "<br>";
                }
                echo '</div>';
            }
            ?>
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
                <label>Amount (INR)</label>
                <input type="number" step="0.01" name="amount" placeholder="₹0.00" class="form-control" required value="<?php echo isset($amount) ? htmlspecialchars($amount, ENT_QUOTES, 'UTF-8') : ''; ?>" />
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

        <h4 class="page-title">List Of Payment Link</h4>
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
// fetch latest 5 orders for this user
$query = "SELECT * FROM `orders` WHERE user_id='{$userdata["id"]}' AND user_mode = '1' ORDER BY `id` DESC LIMIT 5";
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
    echo "<tr><td colspan='7'>No records or query error: " . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8') . "</td></tr>";
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

<?php
} else {
  // Non-user roles: show admin view (list) - keep as earlier but show all or filtered settlement table
?>
  <div class="main-panel">
    <div class="content">
      <div class="container-fluid">
        <h4 class="page-title">List Of Payment Link</h4>
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
$query = "SELECT * FROM `settlement` WHERE status='$getst'";
$query_run = mysqli_query($conn, $query);
if ($query_run) {
    while ($row = mysqli_fetch_assoc($query_run)) {
        $fetchuser = $conn->query("SELECT * FROM `users` WHERE id = '{$row["userid"]}'")->fetch_assoc();
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

      </div>
    </div>
  </div>
</div></div></div></div></main>

<!-- JS includes (same as your original) -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

<?php if(!empty($paymentlink)){ ?>
<!-- Modal to show created link -->
<div class="modal fade" id="copypaymentlinkmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-confirm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title text-primary w-100">Payment Link Created Successfully</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="clipboard">
          <input onclick="copy()" class="copy-input" value="<?php echo htmlspecialchars($paymentlink, ENT_QUOTES, 'UTF-8') ?>" id="copyClipboard" readonly>
          <button class="copy-btn" id="copyButton" onclick="copy()"><i class="fa fa-copy"></i></button>
        </div>
        <div id="copied-success" class="copied">
          <span>Link Copied !</span>
        </div>
        <p>This Payment Link is valid for only 10 min.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="copy()" id="changeusrupimodebtn">Copy</button>
      </div>
    </div>
  </div>
</div>

<script>
  $("#copypaymentlinkmodal").modal("show");
</script>
<?php } ?>

<script>
$(document).ready(function(){
  $("#frubtn").click(function(){
    $("#changefrform").slideToggle();
  });
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
      setTimeout(function(){ copySuccess.style.opacity = "0" }, 800);
    }, function(){
      // fallback
      copySuccess.style.opacity = "1";
      setTimeout(function(){ copySuccess.style.opacity = "0" }, 800);
    });
  } else {
    try {
      document.execCommand('copy');
      copySuccess.style.opacity = "1";
      setTimeout(function(){ copySuccess.style.opacity = "0" }, 800);
    } catch (e) {
      console.log('copy fallback failed', e);
    }
  }
}
</script>

</body>
</html>
