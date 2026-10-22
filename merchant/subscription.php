<?php
error_reporting(0);
include "header.php"; 
include "pages/dbInfo.php"; // Ensure your DB connection is included here
include "pages/dbFunctions.php"; // If you use helper functions

// --- FETCH PLANS FROM DATABASE ---
// Hum sirf wahi plans layenge jo 'is_active = 1' hain
$sql_plans = "SELECT * FROM subscription_plans WHERE is_active = 1 ORDER BY plan_id ASC";
// Note: Agar aap direct mysqli use karte hain to neeche line ko adjust karein, 
// main maan ke chal raha hu aapke paas DB connection $conn variable me hai.
$query_plans = mysqli_query($conn, $sql_plans); 

?>

<style>
    .card { transition: transform 0.3s, box-shadow 0.3s; flex: 1; margin: 10px; min-width: 250px; }
    .card:hover { transform: scale(1.05); box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
    .card-header { background-color: #40446e; border-bottom: 1px solid #ddd; }
    .container-fluid { display: flex; justify-content: space-between; flex-wrap: wrap; }
    @media (max-width: 768px) { .container-fluid { flex-direction: column; } }
    
    /* Modal Styling */
    .modal-box { width: 100%; max-width: 600px; padding: 20px; font-size: 14px; }
    @media (max-width: 768px) { .modal-box { margin-left: 10px; margin-right: 10px; } }
    .modal-header1 { font-size: 20px; font-weight: bold; color: #333; margin-bottom: 10px; }
    .modal-subheader { font-size: 14px; color: #666; margin-bottom: 20px; }
    .modal-details { background:#eeeeee; border: 1px solid #e1e5ec; border-radius: 6px; padding: 15px; margin-bottom: 20px; }
    .modal-details p { margin: 0px 0; font-size: 15px; line-height: 24px; font-weight: 500; color: #333; }
    .modal-details h6 { font-size: 17px; margin: 0px; margin-bottom: 10px; }
    .modal-details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; }
    .modal-details-grid .label { font-weight: normal; }
    .modal-details .total { font-size: 16px; font-weight: bold; color: #4CAF50; margin-top: 0px; }
    .modal-buttons { display: flex; justify-content: center; }
    .modal-buttons button { padding: 10px; font-size: 14px; border: none; border-radius: 5px; cursor: pointer; transition: background 0.3s ease; font-family: 'Poppins', sans-serif; }
    .modal-buttons .cancel-button { background-color: #fff; color: #333; border: 1px solid #ccc; margin-right: 10px; }
    .modal-buttons .cancel-button:hover { background-color: #f2f2f2; }
    .modal-buttons .confirm-button { background-color: #25a6a1; color: white; }
    .modal-buttons .confirm-button:hover { background-color: #0994BB; }
    .modal-details .discountprice { color: #D1291A; margin-top: 0px; display: none; }
    #discbox, .discountpricelabel { display: none; }
    .card-header.activated { border: 2px solid #4CAF50; position: relative; } 
    /* Agar user ka plan active hai to ye class add hogi */
</style>

<main class="app-content">
    <div class="app-title">
        <div><h1><i class="fa fa-cart-plus"></i> Subscription</h1></div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
        </ul>
    </div>
    <div class="tile mb-4">
        <div class="page-header">
            <div class="row">
                <div class="col-lg-12">
                    <div class="main-panel">
                        <div class="content">
                            <div class="container-fluid">
                                <div class="row">

                                    <?php 
                                    // --- DATABASE LOOP START ---
                                    if(mysqli_num_rows($query_plans) > 0) {
                                        while($row = mysqli_fetch_assoc($query_plans)) {
                                            $db_plan_id = $row['plan_id'];
                                            $db_name = $row['name'];
                                            $db_price = $row['price'];
                                            $db_duration_text = $row['duration_text'];
                                            $db_duration_days = $row['duration_days'];
                                            $db_gst_enabled = $row['gst_enabled'];
                                            
                                            // Feature Logic based on Plan ID (Design maintain karne ke liye)
                                            // Aap features bhi DB me daal sakte hain par design ke liye switch use kar rahe hain
                                            $feature_qr_limit = "Limited";
                                            $feature_merchants = "All";
                                            $branding = true;
                                            $plugin_offer = "";

                                            switch($db_plan_id) {
                                                case 1: $feature_qr_limit = "4999"; $feature_merchants = "All"; $branding=true; break;
                                                case 2: $feature_qr_limit = "8599"; $feature_merchants = "All"; $branding=true; break;
                                                case 3: $feature_qr_limit = "11999"; $feature_merchants = "All"; $branding=true; break;
                                                case 4: $feature_qr_limit = "24999"; $feature_merchants = "All"; $branding=true; break;
                                                case 5: $feature_qr_limit = "16499"; $feature_merchants = "10"; $branding=false; $plugin_offer="Free"; break;
                                                case 6: $feature_qr_limit = "28399"; $feature_merchants = "20"; $branding=false; $plugin_offer="Free"; break;
                                                case 7: $feature_qr_limit = "39599"; $feature_merchants = "50"; $branding=false; $plugin_offer="Free"; break;
                                                case 8: $feature_qr_limit = "82499"; $feature_merchants = "Unlimited"; $branding=false; $plugin_offer="Free"; break;
                                            }
                                    ?>
                                    
                                    <div class="col-md-3">
                                        <div class="card text-center">
                                            <div class="card-header <?php if($userdata["plan_id"] == $db_plan_id){ echo "activated"; } ?>">
                                                <h4 class="card-title"><?php echo $db_name; ?> Plan</h4>
                                                <h2 class="text-center">₹<?php echo number_format($db_price, 0); ?></h2>
                                                <p class="card-category"><?php echo $db_duration_text; ?></p>
                                            </div>
                                            <div class="card-body">
                                                <table class="mx-auto">
                                                    <tbody>
                                                        <tr>
                                                            <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                            </tr>
                                                        <tr>
                                                            <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                            <td><p><i class="fas fa-qrcode"></i> Dynamic QR Code</p></td>
                                                        </tr>
                                                        <tr>
                                                            <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                            <td><p><i class="fas fa-wallet"></i> No Amount Limit</p></td>
                                                        </tr>
                                                        
                                                        <tr>
                                                            <td><i class="icon-md <?php echo ($db_plan_id > 4) ? 'text-primary' : 'text-danger'; ?> me-2" data-feather="<?php echo ($db_plan_id > 4) ? 'check' : 'x'; ?>"></i></td>
                                                            <td><p><i class="fas fa-paper-plane"></i> Intent Button System</p></td>
                                                        </tr>

                                                        <tr>
                                                            <td><i class="icon-md <?php echo ($db_plan_id > 4) ? 'text-primary' : 'text-danger'; ?> me-2" data-feather="<?php echo ($db_plan_id > 4) ? 'check' : 'x'; ?>"></i></td>
                                                            <td><p><i class="fas fa-link"></i> Payment Link Create</p></td>
                                                        </tr>

                                                        <tr>
                                                            <td><i class="icon-md <?php echo ($db_plan_id > 4) ? 'text-primary' : 'text-danger'; ?> me-2" data-feather="<?php echo ($db_plan_id > 4) ? 'check' : 'x'; ?>"></i></td>
                                                            <td><p><i class="fas fa-desktop"></i> Payment Pages Create</p></td>
                                                        </tr>

                                                        <tr>
                                                            <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                            <td><p><i class="fas fa-university"></i> Connect <?php echo $feature_merchants; ?> Merchants</p></td>
                                                        </tr>

                                                        <tr>
                                                            <td><i class="icon-md <?php echo (!$branding) ? 'text-primary' : 'text-danger'; ?> me-2" data-feather="<?php echo (!$branding) ? 'check' : 'x'; ?>"></i></td>
                                                            <td><p><i class="fas fa-copyright"></i> No Cgateway Pay Branding</p></td>
                                                        </tr>
                                                        
                                                        <?php if($plugin_offer != ""): ?>
                                                        <tr>
                                                            <td><i class="icon-md text-primary me-2" data-feather="check"></i></td>
                                                            <td><p><i class="fas fa-plug"></i> Plugin Store <span style="color: #FF5733; font-weight: bold; background-color: #f0f0f0; padding: 2px 6px; border-radius: 4px;"><?php echo $plugin_offer; ?></span></p></td>
                                                        </tr>
                                                        <?php endif; ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="card-footer">
                                                <button class="btn btn-success btn-block subscbtn" 
                                                    data-amount="<?php echo $db_price; ?>" 
                                                    data-planid="<?php echo $db_plan_id; ?>"
                                                    data-planname="<?php echo $db_name; ?>"
                                                    data-days="<?php echo $db_duration_days; ?>"
                                                    data-gst="<?php echo $db_gst_enabled; ?>">
                                                    <?php echo ($userdata["plan_id"] == $db_plan_id) ? 'Renew' : 'Upgrade Plan' ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <?php 
                                        } // End While Loop
                                    } else {
                                        echo "<h3 class='text-center'>No Active Plans Available</h3>";
                                    }
                                    ?>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="subsconfirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-box">
                    <div class="modal-header1">Confirm your payment</div>
                    <div class="modal-subheader">Quickly and secure, free transactions.</div>

                    <div class="modal-details" id="modalDetails" style="display: block;">
                        <h6>Details</h6>
                        <div class="modal-details-grid">
                            <p class="label">Name</p>
                            <p id="Name"><?= $userdata["name"] ?></p>
                            <p class="label">Mobile</p>
                            <p id="Mobile"><?= substr($userdata["mobile"],0,3) ?>XXXX<?= substr($userdata["mobile"],-3) ?></p>
                            <p class="label">Plan Pack</p>
                            <p id="Plan">Stater</p>
                            <p class="label">Plan Expiry Date</p>
                            <p id="PlanExipre"></p>
                            <p class="label">Payment Method</p>
                            <p id="paymentMethod">UPI</p>
                            <p class="label">Purchase Date</p>
                            <p id="paymentDate"><?= date("M d, Y") ?></p>

                            <hr style="border: 1px solid rgb(165, 165, 165); margin: 10px 0; grid-column: 1 / span 2;">
                            
                            <p class="label">Price : </p>
                            <p id="price" class="price">₹0.00</p>
                            
                            <p class="label" id="gstLabelRow">GST Charge (18%) : </p>
                            <p id="gstprice" class="gstcharge">₹0.00</p>
    
                            <p class="label discountpricelabel">Discount : </p>
                            <p id="discountprice" class="discountprice">-₹0.00</p>
    
                            <p class="label">Total Amount:</p>
                            <p id="paymentAmount" class="total">₹0.00</p>
                        </div>
                    </div>
        
                    <div class="modal-buttons">
                        <button class="cancel-button" data-dismiss="modal">Cancel Payment</button>
                        <form method="POST" action="lib/pay">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" class="csubsamount" name="amount" value="">
                            <input type="hidden" class="csplanid" name="planid" value="">
                            <input type="hidden" class="csdiscountamount" name="discountamount" value="0">
                            <button name="upigate" id="confirmButton" class="confirm-button">Confirm Payment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="js/plugins/pace.min.js"></script>
    
<script>
// --- Updated JS Logic ---

function formatDate(date) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + parseInt(days));
    return result;
}
    
function calculateGST(amount, isGstOn) {
    if(isGstOn == 0 || isGstOn == '0') {
        return {
            gstAmount: "0.00",
            totalAmount: parseFloat(amount).toFixed(2)
        };
    }
    const gstRate = 0.18;
    const gstAmount = amount * gstRate;
    const totalAmount = parseFloat(amount) + gstAmount;

    return {
        gstAmount: gstAmount.toFixed(2),
        totalAmount: totalAmount.toFixed(2)
    };
}

let totalprice = 0;

$(document).on('click','.subscbtn',function(){
    
    // Fetch Data from DB Attributes
    let planid = $(this).data('planid');
    let amount = $(this).data('amount');
    let planname = $(this).data('planname');
    let daysToAdd = $(this).data('days');
    let gstEnabled = $(this).data('gst'); // 1 ya 0

    // Calculate Price
    let calcamount = calculateGST(amount, gstEnabled);
    totalprice = parseFloat(calcamount.totalAmount);
    
    // Active Expiry Logic
    <?php if($userdata["expiry"] > date("Y-m-d")){ ?>
    const today = new Date('<?= $userdata["expiry"] ?>');
    <?php }else{ ?>
    const today = new Date();
    <?php } ?>
    
    const newDate = formatDate(addDays(today, daysToAdd));
    
    // UI Update
    $('#Plan').text(planname + " Plan");
    $('#price').text(`₹ ${parseFloat(amount).toFixed(2)}`);
    
    // GST Show/Hide Logic
    if(gstEnabled == 1){
        $('#gstLabelRow').show();
        $('#gstprice').show().text(`₹ ${calcamount.gstAmount}`);
    } else {
        $('#gstLabelRow').hide();
        $('#gstprice').hide();
    }

    $('#paymentAmount').text(`₹ ${totalprice}`);
    $('#PlanExipre').text(newDate);
    
    // Form Inputs Update
    $(".csplanid").val(planid);
    $(".csubsamount").val(totalprice);
    
    $("#subsconfirmModal").modal('show');
});

</script>

</body> 
</html>