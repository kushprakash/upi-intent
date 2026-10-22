<?php include "header.php"; ?>
<?php
// KYC Redirect Check
if(isset($userdata["aadhar_kyc"]) && $userdata["aadhar_kyc"] == 1){
    echo "<script> location.replace('dashboard?aadhar_kyc=0') </script>";
    exit;
}  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        /* Modal box Styles */
        .modal-box { width: 100%; max-width: 600px; padding: 20px; font-size: 14px; }
        @media (max-width: 768px) { .modal-box { margin-left: 10px; margin-right: 10px; } }
        .modal-header1 { font-size: 20px; font-weight: bold; color: #333; margin-bottom: 10px; }
        .modal-subheader { font-size: 14px; color: #666; margin-bottom: 20px; }
        .modal-details { background: #eeeeee; border: 1px solid #e1e5ec; border-radius: 6px; padding: 15px; margin-bottom: 20px; }
        p.value { font-size: 18px !important; font-weight: 600 !important; letter-spacing: 0.4px; }
        p#successtxn { color: #42db4a; }
        p#failedtxn { color: #eb5353; }
        .modal-details p { margin: 0px 0; font-size: 15px; line-height: 24px; font-weight: 500; color: #333; }
        .modal-details h6 { font-size: 17px; margin: 0px; margin-bottom: 10px; }
        .modal-details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px; }
        .modal-details-grid .label { font-weight: 500; font-size: 16px; line-height: 22px; }
        .modal-buttons { display: flex; justify-content: center; }
        .modal-buttons button { width: 100%; padding: 10px; font-size: 14px; border: none; border-radius: 5px; cursor: pointer; margin: 0 10px; transition: background 0.3s ease; font-family: 'Poppins', sans-serif; }
        .modal-buttons .cancel-button { background-color: #fff; color: #333; border: 1px solid #ccc; margin-right: 10px; }
        .modal-buttons .cancel-button:hover { background-color: #f2f2f2; }
        .modal-buttons .confirm-button { background-color: #25a6a1; color: white; }
        .modal-buttons .confirm-button:hover { background-color: #0994BB; }
        
        /* Widget Style */
        #emoji-widget { position: fixed; bottom: 20px; right: 20px; z-index: 9999; cursor: pointer; width: 60px; }
        #emoji-widget img { width: 100%; }
    </style>
</head>
<body>

<div id="emoji-widget">
    <img src="https://cdnl.iconscout.com/lottie/premium/thumb/customer-care-8147491-6529814.gif" alt="GIF Widget">
</div>
<audio id="widget-audio" src="https://meraqr.in/Voice/transaction.mp3"></audio>
<script>
    document.getElementById('emoji-widget').onclick = () => document.getElementById('widget-audio').play();
</script>

<main class="app-content">
    <div class="app-title">
        <div>
            <h1><i class="fa fa-pie-chart"></i> Transactions</h1>
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
                    <div class="main-panel">
                        <div class="content">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-12">
                                        <form method="POST" action="<?= $_SERVER["PHP_SELF"]; ?>" class="row mb-4">
                                            <div class="col-md-3 mb-2">
                                                <label>From Date</label>
                                                <input type="text" name="from_date" id="fdate" value="<?= isset($_POST['from_date']) ? $_POST['from_date'] : date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control datepicker" readonly>
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <label>To Date</label>
                                                <input type="text" name="to_date" id="edate" value="<?= isset($_POST['to_date']) ? $_POST['to_date'] : date("Y-m-d") ?>" placeholder="YYYY-MM-DD" class="form-control datepicker" readonly>
                                            </div>

                                            <div class="col-md-2 mb-2">
                                                <label>&nbsp;</label>
                                                <button type="button" id="viewtxndetailsBtn" class="btn btn-primary btn-block">View Details</button>
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <label>&nbsp;</label>
                                                <button type="submit" name="search" class="btn btn-primary btn-block">Search</button>
                                            </div>
                                        </form>

                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover table-bordered table-head-bg-primary" id="dataTable" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Customer Mobile</th>
                                                        <th>TransactionId</th>
                                                        <th>Date Time</th>
                                                        <th>Payee App</th>
                                                        <th>Payee VPA</th>
                                                        <th>Order ID</th>
                                                        <th>Merchant</th> <th>Gateway Txn</th>
                                                        <th>Bank RRN</th>
                                                        <th>Remark</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Ensure DB connection is available
                                                    global $conn;

                                                    if ($userdata['role'] == 'User' || $userdata['role'] == 'Admin') {
                                                        $token = $userdata['user_token'];

                                                        // Get Date Filters
                                                        if (isset($_POST["search"])) {
                                                            $from_date = $_POST["from_date"];
                                                            $to_date = $_POST["to_date"];
                                                        } else {
                                                            $from_date = date("Y-m-d");
                                                            $to_date = date("Y-m-d");
                                                        }

                                                        // Safe Query: Select * allows us to get data even if columns changed recently
                                                        // Filter by Token and Date Range
                                                        $query = "SELECT * FROM `orders` 
                                                                  WHERE user_token = '$token' 
                                                                  AND (DATE(create_date) BETWEEN '$from_date' AND '$to_date') 
                                                                  AND user_mode = '1' 
                                                                  ORDER BY `id` DESC";

                                                        $query_run = mysqli_query($conn, $query);
                                                        
                                                        // Debugging: Uncomment if still empty
                                                        // if(!$query_run) { echo "<tr><td colspan='13'>Error: " . mysqli_error($conn) . "</td></tr>"; }

                                                        $i = 1;
                                                        if ($query_run && mysqli_num_rows($query_run) > 0) {
                                                            while ($row = mysqli_fetch_assoc($query_run)) {

                                                                // Fetch Extra Report Details
                                                                $reportQuery = "SELECT vpa, paymentApp FROM `reports` WHERE order_id = '{$row["order_id"]}'";
                                                                $reportRes = $conn->query($reportQuery);
                                                                $fetchreport = ($reportRes) ? $reportRes->fetch_assoc() : [];

                                                                // Handle Merchant Name (Gateway Provider)
                                                                $merchantName = isset($row['method']) && !empty($row['method']) ? $row['method'] : 'N/A';

                                                                echo "<tr>";
                                                                echo "<td>" . $i++ . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['customer_mobile'] ?? '') . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['byteTransactionId'] ?? '') . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['create_date'] ?? '') . "</td>";

                                                                echo "<td>" . htmlspecialchars($fetchreport['paymentApp'] ?? '') . "</td>";
                                                                echo "<td>" . htmlspecialchars($fetchreport['vpa'] ?? '') . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['order_id'] ?? '') . "</td>";
                                                                
                                                                // Show Merchant Column
                                                                echo "<td><span class='badge badge-info'>" . htmlspecialchars($merchantName) . "</span></td>";

                                                                echo "<td>" . htmlspecialchars($row['gateway_txn'] ?? '') . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['utr'] ?? '') . "</td>";
                                                                echo "<td>" . htmlspecialchars($row['remark1'] ?? '') . "</td>";
                                                                echo "<td>₹" . htmlspecialchars($row['amount'] ?? '0') . "</td>";

                                                                // Status Badge Logic
                                                                $status = $row['status'] ?? 'PENDING';
                                                                if ($status == "SUCCESS") {
                                                                    $sts = 'Success';
                                                                    $cls = "badge badge-success";
                                                                } else if ($status == "FAILURE") {
                                                                    $sts = 'Failed';
                                                                    $cls = "badge badge-danger";
                                                                } else {
                                                                    $sts = 'Scanning';
                                                                    $cls = "badge badge-warning";
                                                                }

                                                                echo "<td style='width:80px; display:flex; align-items:center;'>
                                                                        <button class='$cls'>$sts</button>";
                                                                
                                                                // Edit Button for Manual Approval
                                                                if ($sts == 'Scanning') {
                                                                    echo '<button class="btn btn-sm btn-success ml-2 updatetxnstbtn" data-txnid="' . $row['order_id'] . '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                                                                }
                                                                echo "</td>";
                                                                echo "</tr>";
                                                            }
                                                        }
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
            </div>
        </div>
    </div>

    <div class="modal fade" id="txnupdatemodal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Transaction Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="updatetxnorderform" class="mb-2">
                        <input type="hidden" name="orderid" id="orderid">
                        <input type="hidden" name="type" value="updateTxnOrder">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label>Select Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="" selected disabled>--Select--</option>
                                    <option value="1">Approved</option>
                                    <option value="0">Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>UTR No</label>
                                <input type="number" name="utrno" id="utrno" class="form-control" placeholder="Enter UTR Number">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Remark</label>
                                <input type="text" name="remark" id="remark" class="form-control" placeholder="Enter Remarks">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>&nbsp;</label>
                                <button type="submit" name="updatetxnbtn" class="btn btn-primary btn-block">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="txndetailsModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-box">
                        <div class="modal-header1">Transaction Report</div>
                        <div class="modal-subheader">Quickly check & analyse your transactions.</div>
                        <div class="modal-details" id="modalDetails" style="display: block;">
                            <h6>Details</h6>
                            <div class="modal-details-grid">
                                <p class="label">Total Transactions</p>
                                <p id="totaltxn" class="value">₹ 0.00</p>
                                <p class="label">Success Transactions</p>
                                <p id="successtxn" class="value">₹ +0.00</p>
                                <p class="label">Failed Transactions</p>
                                <p id="failedtxn" class="value">₹ -0.00</p>
                                <hr style="border: 1px solidrgb(165, 165, 165); margin: 10px 0;">
                                <h6 class="">Count of txn</h6>
                                <p class="label">Total Transactions</p>
                                <p id="totaltxnc" class="value">0</p>
                                <p class="label">Success Transactions</p>
                                <p id="successtxnc" class="value">0</p>
                                <p class="label">Failed Transactions</p>
                                <p id="failedtxnc" class="value">0</p>
                            </div>
                        </div>
                        <div class="modal-buttons">
                            <button class="cancel-button" data-dismiss="modal">Close</button>
                            <button id="printbtn" class="confirm-button">Print</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
<script src="js/plugins/pace.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" />
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<link href="https://code.jquery.com/ui/1.12.1/themes/cupertino/jquery-ui.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<script>
    // Initialize DataTables
    $(document).ready(function() {
        $("#dataTable").DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: 'Export to Excel',
                titleAttr: 'Export to Excel',
                className: 'btn btn-sm btn-primary'
            }],
            order: [[3, "desc"]] // Sort by Date Time Descending
        });

        // Initialize Datepicker
        $(".datepicker").datepicker({ dateFormat: "yy-mm-dd" });
    });

    // Handle Manual Update Button Click
    $(document).on('click', '.updatetxnstbtn', function() {
        let orderid = $(this).data("txnid");
        $("#orderid").val(orderid);
        $("#txnupdatemodal").modal("show");
    });

    // Handle Update Form Submission
    $("#updatetxnorderform").submit(function(e) {
        e.preventDefault();
        $('#loading_ajax').fadeIn();
        
        $.ajax({
            url: 'backend/MerchantAuthController.php',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(data) {
                $('#loading_ajax').fadeOut();
                try {
                    let rslt = JSON.parse(data);
                    if (rslt.status == 1) {
                        Swal.fire({ icon: "success", title: "Success", text: rslt.msg, button: "Close" })
                        .then(() => { location.reload(); });
                    } else {
                        Swal.fire({ icon: "error", title: "OOPS..!", text: rslt.msg, button: "Close" });
                    }
                } catch(e) {
                    Swal.fire({ icon: "error", title: "Error", text: "Invalid response from server", button: "Close" });
                }
            },
            error: function() {
                $('#loading_ajax').fadeOut();
                Swal.fire({ icon: "error", title: "Error", text: 'Internal Server Error', button: "Close" });
            }
        });
    });

    // Handle View Details Button
    $("#viewtxndetailsBtn").click(function() {
        $('#loading_ajax').fadeIn();
        let fromdate = $("#fdate").val();
        let todate = $("#edate").val();

        $.ajax({
            url: 'backend/user_settings',
            type: 'POST',
            data: { fromdate: fromdate, todate: todate, type: 'txnrdetails' },
            success: function(data) {
                $('#loading_ajax').fadeOut();
                try {
                    let rslt = JSON.parse(data);
                    if (rslt.res_code == 200) {
                        $(".modal-subheader").html(rslt.msg);
                        $("#totaltxn").html(rslt.txndata.totaltxn);
                        $("#totaltxnc").html(rslt.txndata.totaltxnc);
                        $("#successtxn").html(rslt.txndata.totalstxn);
                        $("#successtxnc").html(rslt.txndata.totalstxnc);
                        $("#failedtxn").html(rslt.txndata.totalftxn);
                        $("#failedtxnc").html(rslt.txndata.totalftxnc);
                        $("#txndetailsModal").modal("show");
                    } else {
                        Swal.fire({ icon: "error", title: "OopS!", text: rslt.msg });
                    }
                } catch(e) {
                    console.log("JSON Parse Error");
                }
            },
            error: function() {
                $('#loading_ajax').fadeOut();
            }
        })
    });

    // Print Logic
    var printbtn = document.getElementById("printbtn");
    if(printbtn) {
        printbtn.addEventListener("click", (e) => {
            var section = document.getElementById("modalDetails").innerHTML;
            var body = document.querySelector('body').innerHTML;
            document.body.innerHTML = section;
            window.print();
            document.body.innerHTML = body;
            location.reload(); 
        });
    }
</script>

</body>
</html>