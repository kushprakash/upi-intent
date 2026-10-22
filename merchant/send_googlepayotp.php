<?php
include "header.php";



if(isset($_POST['verifyotp'])) {

    $bbbyteuserid = $_SESSION['user_id']; 
    $userid = $userdata['user_token'];   

    // ---- DO NOT SANITIZE THESE (important!) ----
    $freqid = $_POST["freq_id"];
    $cookie = $_POST["cookie"];
    $at     = $_POST["at"];

    // ONLY SQL ESCAPE
    $freqid  = mysqli_real_escape_string($conn, $freqid);
    $cookie  = mysqli_real_escape_string($conn, $cookie);
    $at      = mysqli_real_escape_string($conn, $at);

    // Normal fields → sanitize allowed
    $upiid = sanitizeInput($_POST["upi_id"]);
    $merchant_id = sanitizeInput($_POST["merchant_id"]);

    $upiid = mysqli_real_escape_string($conn, $upiid);

    // Update user
    $sqlUpdateUser = "UPDATE users SET googlepay_connected='Yes' WHERE user_token='$userid'";
    mysqli_query($conn, $sqlUpdateUser);

    // MAIN FIXED UPDATE QUERY
    $sqlw = "
        UPDATE gpay_tokens 
        SET 
            cokkie = '$cookie',
            Upiid = '$upiid',
            `at` = '$at',
            `f-req` = '$freqid',
            status = 'Active',
            user_id = '$bbbyteuserid'
        WHERE 
            user_token = '$userid'
            AND id = '$merchant_id'
    ";
    $result = mysqli_query($conn, $sqlw);

    if ($result) {

        $fetchuser = $conn->query("SELECT route FROM `users` WHERE user_token='$userid'")->fetch_assoc();

        if($fetchuser["route"] == 0){

            /*
            $tablesarr = ["bharatpe_tokens","freecharge","paytm_tokens","merchant","hdfc","phonepe_tokens"];
            $connected_merarr = ["sbi_connected","phonepe_connected","hdfc_connected","freecharge_connected","bharatpe_connected","paytm_connected"];

            foreach($tablesarr as $tables){
                $fetchmerchant = $conn->query("SELECT user_token FROM `$tables` WHERE user_token = '$userid' AND status = 'Active'");
                if($fetchmerchant->num_rows > 0){
                    $conn->query("UPDATE $tables SET status = 'Deactive' WHERE user_token = '$userid'");
                }
            }

            foreach($connected_merarr as $connected){
                $conn->query("UPDATE users SET $connected = 'No' WHERE user_token = '$userid'");
            }
            */
        }

        // SweetAlert Success
        echo '<script src="js/jquery-3.2.1.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
                $("#loading_ajax").hide();
                Swal.fire({
                    icon: "success",
                    title: "Congratulations! Your Google Pay has been connected successfully!",
                    confirmButtonText: "Ok!",
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "upisettings";
                    }
                });
              </script>';
        exit;

    } else {

        // SweetAlert ERROR
        echo '<script src="js/jquery-3.2.1.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
                $("#loading_ajax").hide();
                Swal.fire({
                    icon: "error",
                    title: "Please Try Again Later!",
                    confirmButtonText: "Ok!",
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "upisettings";
                    }
                });
              </script>';
        exit;
    }
}


if(isset($_POST['Verify'])) {

    if ($userdata['googlepay_connected']=="Yes" && $userdata['plan_id'] < 5){

        echo '<script src="js/jquery-3.2.1.min.js"></script>';
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
                $("#loading_ajax").hide();
                Swal.fire({
                    icon: "error",
                    title: "Merchant Already Connected !!",
                    confirmButtonText: "Ok!",
                    allowOutsideClick: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "upisettings";
                    }
                });
              </script>';
        exit;
    }

    $gpay_mobile = sanitizeInput($_POST["gpay_mobile"]);
    $merchant_id = sanitizeInput($_POST["merchant_id"]);
?>

<!-- HTML START -->
<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-user"></i> Google Pay UPI Settings</h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
        </ul>
    </div>
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

                                    <h4 class="page-title">Google Pay UPI Settings</h4>

                                    <form method="POST" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="mb-2">
                                        <input type="hidden" name="merchant_id" value="<?= $merchant_id; ?>">

                                        <div class="row" id="merchant">

                                            <div class="col-md-4 mb-2">
                                                <label>Mobile Number</label>
                                                <input type="number" name="number" value="<?= $gpay_mobile; ?>" class="form-control" required>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <label>Enter UPI</label>
                                                <input type="text" name="upi_id" class="form-control" value="dummy@gpay" required>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <label>Enter Freq Id</label>
                                                <input type="text" name="freq_id" class="form-control" required>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <label>Enter Cookie</label>
                                                <input type="text" name="cookie" class="form-control" required>
                                            </div>

                                            <div class="col-md-12 mb-2">
                                                <label>Enter At</label>
                                                <input type="text" name="at" class="form-control" required>
                                            </div>

                                            <div class="col-md-4 mb-2">
                                                <button type="submit" name="verifyotp" class="btn btn-primary btn-block">
                                                    Verify Gpay
                                                </button>
                                            </div>

                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>

                        <script src="js/jquery-3.2.1.min.js"></script>
                        <script src="js/popper.min.js"></script>
                        <script src="js/bootstrap.min.js"></script>
                        <script src="js/main.js"></script>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php } ?>
