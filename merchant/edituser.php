<?php 
include "header.php"; 
include "config.php"; 

// ------ Auth check ------
if (!isset($userdata["role"]) || $userdata["role"] !== 'Admin') {
    echo '<script>window.location.href = "dashboard";</script>';
    exit;
}

// ------ Get user by mobile (prepared) ------
$mobileno = $_REQUEST['mobileno'] ?? '';
$day = [];

if (!empty($mobileno)) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE mobile = ?");
    mysqli_stmt_bind_param($stmt, "s", $mobileno);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $day = mysqli_fetch_assoc($result) ?: [];
    mysqli_stmt_close($stmt);
}

// ------ Handle update ------
if (isset($_REQUEST['update'])) {

    // Optional CSRF verify (enable only if you are setting token in session)
    if (isset($_POST['csrf_token'], $_SESSION['csrf_token']) && $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
            Swal.fire({ icon:"error", title:"Invalid CSRF token" }).then(() => { window.location.href="userlist"; });
        </script>';
        exit;
    }

    $mobilex = $_REQUEST['mobile'] ?? '';
    $email   = $_REQUEST['email'] ?? '';
    $password= $_REQUEST['password'] ?? ''; // not used in update below; keep if needed later
    $name    = $_REQUEST['name'] ?? '';
    $company = $_REQUEST['company'] ?? '';
    $pin     = $_REQUEST['pin'] ?? '';
    $pan     = $_REQUEST['pan'] ?? '';
    $aadhaar = $_REQUEST['aadhaar'] ?? '';
    $location= $_REQUEST['location'] ?? '';
    $exp     = $_REQUEST['date'] ?? '';

    // If you really need to update password, uncomment below 2 lines and add to query.
    // $pass = password_hash($password, PASSWORD_BCRYPT);

    $stmt = mysqli_prepare($conn, "UPDATE users 
        SET name = ?, email = ?, company = ?, pin = ?, pan = ?, aadhaar = ?, location = ?, expiry = ?
        WHERE mobile = ?");
    mysqli_stmt_bind_param($stmt, "sssssssss", $name, $email, $company, $pin, $pan, $aadhaar, $location, $exp, $mobilex);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    if ($ok) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "User Update Successful!!",
                showConfirmButton: true,
                confirmButtonText: "Ok!",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((r) => { if (r.isConfirmed) { window.location.href = "dashboard"; }});
        </script>';
        exit;
    } else {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "User Update Failed!!",
                showConfirmButton: true,
                confirmButtonText: "Ok!",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((r) => { if (r.isConfirmed) { window.location.href = "userlist"; }});
        </script>';
        exit;
    }
}
?>

<style>
p { font-size:16px; letter-spacing:0.2px; }
.switch { position:relative; display:inline-block; width:65px; height:33px; }
.switch input { display:none; }
.slider { position:absolute; cursor:pointer; top:0; left:0; right:0; bottom:0; background:#fff; transition:.4s; border:2px solid #25a6a1; border-radius:34px; }
.slider:before { position:absolute; content:""; height:22px; width:22px; left:5px; bottom:4px; background:#25a6a1; transition:.4s; border-radius:50%; }
input:checked + .slider { background:#25a6a1; }
input:checked + .slider:before { transform:translateX(30px); background:#fff; }
.switch-title { font-size:16px; font-weight:500; margin:0 10px 10px 0; }
.user-image img { max-width:120px; border-radius:6px; }
</style>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-users"></i> Edit User</h1>
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
              <div class="main-panel">
                <div class="content">
                  <div class="container-fluid">

                    <div class="row row-card-no-pd">
                      <div class="col-md-12">

<form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
  <div class="col-md-6 mb-2">
    <label>Mobile Number</label>
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
    <input type="number" name="mobile" value="<?php echo htmlspecialchars($day['mobile'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Mobile Number" class="form-control" onkeypress="if(this.value.length==10) return false;" required readonly />
  </div>

  <div class="col-md-6 mb-2">
    <label>Email Address</label>
    <input type="text" name="email" value="<?php echo htmlspecialchars($day['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Email Address" class="form-control" required />
  </div>

  <div class="col-md-6 mb-2">
    <label>Name</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($day['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Name" class="form-control" required />
  </div>

  <div class="col-md-6 mb-2">
    <label>Company</label>
    <input type="text" name="company" value="<?php echo htmlspecialchars($day['company'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Company" class="form-control" required />
  </div>

  <div class="col-md-6 mb-2">
    <label>Area Pin</label>
    <input type="text" name="pin" value="<?php echo htmlspecialchars($day['pin'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Area Pin" class="form-control" required />
  </div>

  <div class="col-md-6 mb-2">
    <label>PAN Number</label>
    <input type="text" name="pan" value="<?php echo htmlspecialchars($day['pan'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter PAN Number" class="form-control" onkeypress="if(this.value.length==10) return false;" required />
  </div>

  <div class="col-md-6 mb-2">
    <label>Aadhaar Number</label>
    <input type="number" name="aadhaar" value="<?php echo htmlspecialchars($day['aadhaar'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Aadhaar Number" class="form-control" onkeypress="if(this.value.length==12) return false;" required />
  </div>

  <div class="col-md-3 mb-2">
    <label>Expiry Date</label>
    <input type="date" id="from_date" name="date" value="<?php echo htmlspecialchars($day['expiry'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control">
  </div>

  <div class="col-md-12 mb-2">
    <label>Location</label>
    <input type="text" name="location" value="<?php echo htmlspecialchars($day['location'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter Location" class="form-control" required />
  </div>

  <div class="col-md-12 mb-2 mt-2">
    <button type="submit" name="update" class="btn btn-primary btn-sm">Update Now</button>
  </div>
</form>

                      </div>

<?php 
// ---------------- Aadhaar details block (fixed) ----------------
if (!empty($day["aadhar_kyc"]) && (int)$day["aadhar_kyc"] === 1) { 
    $aadharjson = json_decode($day["kyc_response"] ?? '', true);

    // New API structure: data -> aadhaar_data
    if (json_last_error() === JSON_ERROR_NONE && isset($aadharjson["data"]["aadhaar_data"])) {
        $ad = $aadharjson["data"]["aadhaar_data"];

        $fullName     = htmlspecialchars($ad["full_name"] ?? "", ENT_QUOTES, 'UTF-8');
        $careOf       = htmlspecialchars($ad["care_of"] ?? "", ENT_QUOTES, 'UTF-8');
        $dob          = htmlspecialchars($ad["dob"] ?? "", ENT_QUOTES, 'UTF-8');
        $genderRaw    = strtoupper(trim($ad["gender"] ?? ""));
        $gender       = $genderRaw === 'M' ? 'Male' : ($genderRaw === 'F' ? 'Female' : 'Other');
        $pincode      = htmlspecialchars($ad["zip"] ?? "", ENT_QUOTES, 'UTF-8');
        $fullAddress  = htmlspecialchars($ad["full_address"] ?? "", ENT_QUOTES, 'UTF-8');
        $maskedAadhaar= htmlspecialchars($ad["masked_aadhaar"] ?? "", ENT_QUOTES, 'UTF-8');
        $fatherName   = htmlspecialchars($ad["father_name"] ?? "", ENT_QUOTES, 'UTF-8');

        $addr         = $ad["address"] ?? [];
        $addrCountry  = htmlspecialchars($addr["country"]  ?? "", ENT_QUOTES, 'UTF-8');
        $addrState    = htmlspecialchars($addr["state"]    ?? "", ENT_QUOTES, 'UTF-8');
        $addrDist     = htmlspecialchars($addr["dist"]     ?? "", ENT_QUOTES, 'UTF-8');
        $addrSubDist  = htmlspecialchars($addr["subdist"]  ?? "", ENT_QUOTES, 'UTF-8');
        $addrPO       = htmlspecialchars($addr["po"]       ?? "", ENT_QUOTES, 'UTF-8');
        $addrVTC      = htmlspecialchars($addr["vtc"]      ?? "", ENT_QUOTES, 'UTF-8');
        $addrLoc      = htmlspecialchars($addr["loc"]      ?? "", ENT_QUOTES, 'UTF-8');
        $addrHouse    = htmlspecialchars($addr["house"]    ?? "", ENT_QUOTES, 'UTF-8');
        $addrStreet   = htmlspecialchars($addr["street"]   ?? "", ENT_QUOTES, 'UTF-8');
        $addrLandmark = htmlspecialchars($addr["landmark"] ?? "", ENT_QUOTES, 'UTF-8');

        $profileImage = $ad["profile_image"] ?? "";
        $imgSrc = !empty($profileImage) ? "data:image/jpeg;base64,".$profileImage : "";
        ?>
                        <div class="col-12 col-sm-12 py-2 text-center">
                          <h5 class="mb-2" style="border-bottom:0.6px solid #ccc9c9;padding:5px 0;">Aadhaar Card Details</h5>
                          <div class="invoice-body">
                            <div class="user-details d-flex align-items-center" id="user_aadhar_data">
                              <div class="user-image">
                                <?php if ($imgSrc) { ?>
                                  <img src="<?php echo $imgSrc; ?>" alt="User Image">
                                <?php } else { ?>
                                  <div style="width:120px;height:120px;background:#eee;border-radius:6px;display:flex;align-items:center;justify-content:center;">No Photo</div>
                                <?php } ?>
                              </div>
                              <div class="user-info" style="text-align:left;margin-left:20px;">
                                <p><strong>Name: </strong> <?php echo $fullName; ?></p>
                                <?php if ($careOf) { ?><p><?php echo $careOf; ?></p><?php } ?>
                                <p><strong>Gender: </strong> <?php echo $gender; ?></p>
                                <?php if ($dob) { ?><p><strong>DOB: </strong> <?php echo $dob; ?></p><?php } ?>
                                <p><strong>Masked Aadhaar: </strong> <?php echo $maskedAadhaar; ?></p>
                                <p><strong>Address: </strong> <?php echo $fullAddress; ?></p>
                                <p><strong>Pincode: </strong> <?php echo $pincode; ?></p>

                                <!-- Optional structured address -->
                                <div style="margin-top:8px;">
                                  <?php if ($addrHouse)   { ?><p><strong>House: </strong><?php echo $addrHouse; ?></p><?php } ?>
                                  <?php if ($addrStreet)  { ?><p><strong>Street: </strong><?php echo $addrStreet; ?></p><?php } ?>
                                  <?php if ($addrLandmark){ ?><p><strong>Landmark: </strong><?php echo $addrLandmark; ?></p><?php } ?>
                                  <?php if ($addrLoc)     { ?><p><strong>Locality: </strong><?php echo $addrLoc; ?></p><?php } ?>
                                  <?php if ($addrVTC)     { ?><p><strong>VTC: </strong><?php echo $addrVTC; ?></p><?php } ?>
                                  <?php if ($addrPO)      { ?><p><strong>PO: </strong><?php echo $addrPO; ?></p><?php } ?>
                                  <?php if ($addrSubDist) { ?><p><strong>Sub-District: </strong><?php echo $addrSubDist; ?></p><?php } ?>
                                  <?php if ($addrDist)    { ?><p><strong>District: </strong><?php echo $addrDist; ?></p><?php } ?>
                                  <?php if ($addrState)   { ?><p><strong>State: </strong><?php echo $addrState; ?></p><?php } ?>
                                  <?php if ($addrCountry) { ?><p><strong>Country: </strong><?php echo $addrCountry; ?></p><?php } ?>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
<?php 
    } // else: invalid JSON format - silently skip
} 
?>

                      <div class="col-md-12 my-4">
                        <h3 class="mb-1 content-heading">Manage 2FA Security</h3>
                        <p class="text-muted mb-3">Manage when you login if 2fa enabled we send 6 digit OTP code to your email so your account is more secure and safe.</p>
                        <div class="d-flex justify-content-between align-items-center">
                          <div class="switch-container d-flex align-items-center">
                            <span class="switch-title">Two Factor Authentication</span>
                            <label class="switch">
                              <input type="checkbox" <?php if (!empty($day['two_factor']) && (int)$day['two_factor'] === 1) { echo 'checked'; } ?> data-srno="<?php echo htmlspecialchars($day["id"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="update2fa_btn">
                              <span class="slider"></span>
                            </label>
                          </div>
                        </div>
                      </div>

<?php if (!empty($day["pg_mode"]) && (int)$day["pg_mode"] === 2) { ?>
                      <div class="col-md-12 my-4">
                        <h3 class="mb-1 content-heading">Manage your payment methods</h3>
                        <p class="text-muted mb-3">Just manage easy for your customer to use our payment methods in our platform.</p>
                        <div class="d-flex justify-content-between align-items-center">
                          <div class="switch-container d-flex align-items-center">
                            <span class="switch-title">QR Code</span>
                            <label class="switch">
                              <input type="checkbox" data-service="qrcode" data-srno="<?php echo htmlspecialchars($day["mobile"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php if (!empty($day['pg_qrcode'])) { echo 'checked'; } ?> class="updateservice_btn">
                              <span class="slider"></span>
                            </label>
                          </div>
                          <div class="switch-container d-flex align-items-center">
                            <span class="switch-title">UPI Apps</span>
                            <label class="switch">
                              <input type="checkbox" data-service="upiapps" data-srno="<?php echo htmlspecialchars($day["mobile"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php if (!empty($day['pg_upiapps'])) { echo 'checked'; } ?> class="updateservice_btn">
                              <span class="slider"></span>
                            </label>
                          </div>
                          <div class="switch-container d-flex align-items-center">
                            <span class="switch-title">UPI Request</span>
                            <label class="switch">
                              <input type="checkbox" data-service="upirequest" data-srno="<?php echo htmlspecialchars($day["mobile"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php if (!empty($day['pg_upiidreq'])) { echo 'checked'; } ?> class="updateservice_btn">
                              <span class="slider"></span>
                            </label>
                          </div>
                          <div class="switch-container d-flex align-items-center">
                            <span class="switch-title">Ads Show</span>
                            <label class="switch">
                              <input type="checkbox" data-service="ads" data-srno="<?php echo htmlspecialchars($day["mobile"] ?? '', ENT_QUOTES, 'UTF-8'); ?>" <?php if (!empty($day['pg_ads'])) { echo 'checked'; } ?> class="updateservice_btn">
                              <span class="slider"></span>
                            </label>
                          </div>
                        </div>
                      </div>
<?php } ?>

                    </div><!-- row -->
                  </div><!-- container-fluid -->
                </div><!-- content -->
              </div><!-- main-panel -->
            </div><!-- col -->
          </div><!-- row -->
        </div><!-- col -->
      </div><!-- page-header -->
    </div><!-- tile -->
</main>

</div>
</body>

<!-- Scripts -->
<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="js/plugins/pace.min.js"></script>
<script src="assets/js/ready.min.js"></script>
<script src="js/mainscript.js"></script>

<script>
$(document).ready(function() {
  if ($('#disclaimer').length) {
    $('#disclaimer').modal({backdrop: 'static', keyboard: false});
    $("#disclaimer").modal("show");
  }
});
</script>
<script>
$(document).ready(function () {
  if ($.fn.datepicker) {
    $(".datepicker").datepicker({ dateFormat: "dd-mm-yy" });
  }
});
</script>

<!-- Mirrored from upigetway.com/auth/register by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 19 Oct 2023 17:52:40 GMT -->
</html>
