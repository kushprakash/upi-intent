<?php 
include "header.php"; 
include "config.php"; 

if($userdata["role"] != 'Admin'){
   echo '<script>
 window.location.href = "dashboard";
</script>';

    exit;
}


$mobileno =  $_REQUEST['id'];
$qyt = "SELECT * FROM pg_userKyc_details WHERE user_id='$mobileno'";
$act = mysqli_query($conn, $qyt);
$day = mysqli_fetch_assoc($act);

$aadharjson = json_decode($day["aadhar_response"],true);
$gstjson = json_decode($day["gst_response"],true);
$bpanjson = json_decode($day["bpan_response"],true);
$panjson = json_decode($day["pan_response"],true);
$bankjson = json_decode($day["bank_response"],true);


if (isset($_REQUEST['update'])) {
    
    $status =  $_REQUEST['kycstatus'];
    $remark =  $_REQUEST['remark'];
    
    $upgc = "UPDATE pg_userKyc_details SET status='$status', remark='$remark' WHERE user_id='$mobileno'";
    $resvp = mysqli_query($conn, $upgc);

    if($resvp){
       $conn->query("UPDATE `users` SET `pguser_kyc`='$status' WHERE id = '$mobileno'");
       
        // Show SweetAlert2 success message
                           echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
    Swal.fire({
        icon: "success",
        title: "User KYC Status Updated Successfull!",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "dashboard"; // Redirect to "dashboard" when the user clicks the confirm button
        }
    });
</script>';

    exit;
    
        
    } else {
        // Show SweetAlert2 error message
                           echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
    Swal.fire({
        icon: "error",
        title: "Failed To Update User KYC Status!!",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    });
</script>';
exit;
    }
}

?>

<style>
    p {
    font-size: 16px;
    letter-spacing: 0.2px;
}

   .switch {
    position: relative;
    display: inline-block;
    width: 65px;
    height: 33px;
}

.switch input {
    display: none;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #fff;
    transition: .4s;
    border:2px solid #25a6a1;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 5px;
    bottom: 4px;
    background-color: #25a6a1;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #25a6a1;
}

input:checked + .slider:before {
    transform: translateX(30px);
    background-color: #fff;
}

.switch-title {
    font-size: 16px;
    font-weight: 500;
    margin:0 10px 10px 0;
}
</style>

<main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-users"></i> KYC User Details</h1>
          <!-- <p>Start a beautiful journey here</p> -->
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
						<!-- <h4 class="page-title">UPI Settings</h4> -->
						<div class="row row-card-no-pd">
							<div class="col-md-12">

<div class="main-panel">
				<div class="content">
					<div class="container-fluid">

						<!-- <h4 class="page-title">Edit User</h4>	 -->
										
						<div class="row row-card-no-pd">							
							<div class="col-md-12">

<form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    
    <div class="col-md-12 mb-4">
        <h4>Contact Info Details</h4>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Contact Person Name</label>
        <h5><?php echo htmlspecialchars($day['contact_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Contact Mobile Number</label>
        <h5><?php echo htmlspecialchars($day['contact_number'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
   
    <div class="col-md-6 mb-3">
        <label>Contact Email Address</label>
        <h5><?php echo htmlspecialchars($day['contact_email'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>AADHAAR NUMBER</label>
        <h5><?php echo htmlspecialchars($day['id_proof'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-4 mb-3">
        <label>ID Proof</label>
        <h5>AADHAAR</h5>
    </div>
    
    <div class="col-md-4 mb-3">
        <label>Agreement & Terms Condition</label>
        <?php echo ($day["agreement_status"] == '1') ? '<span class="badge badge-success">Agreed</span>' : '<span class="badge badge-danger">Not Agree</span>' ?>
    </div>
    
    <?php if(!isset($aadharjson["status"]) && $aadharjson["status"] != 'VALID'){ ?>
     <div class="col-md-4 mb-3">
        <label>Aadhaar Verification</label>
        <span class="badge badge-danger">Incomplete</span>
    </div>
    <?php }else{ ?>
     <div class="col-md-4 mb-3">
        <label>Aadhaar Verification</label>
        <span class="badge badge-success">Completed</span>
    </div>
    <?php } ?>
    
    <?php if(!isset($gstjson["valid"]) && $gstjson["valid"] != true && $gstjson["gst_in_status"] != 'Active'){ ?>
     <div class="col-md-4 mb-3">
        <label>GSTIN Verification</label>
        <span class="badge badge-danger">Incomplete</span>
    </div>
    <?php }else{ ?>
     <div class="col-md-4 mb-3">
        <label>GSTIN Verification</label>
        <span class="badge badge-success">Completed</span>
    </div>
    <?php } ?>
    
    <?php if(!isset($bpanjson["valid"]) && $bpanjson["valid"] != true && $bpanjson["pan_status"] != 'VALID'){ ?>
     <div class="col-md-4 mb-3">
        <label>Business Pan Verification</label>
        <span class="badge badge-danger">Incomplete</span>
    </div>
    <?php }else{ ?>
     <div class="col-md-4 mb-3">
        <label>Business Pan Verification</label>
        <span class="badge badge-success">Completed</span>
    </div>
    <?php } ?>
    
    <?php if(!isset($panjson["valid"]) && $panjson["valid"] != true && $panjson["pan_status"] != 'VALID'){ ?>
     <div class="col-md-4 mb-3">
        <label>Signatory PAN Verification</label>
        <span class="badge badge-danger">Incomplete</span>
    </div>
    <?php }else{ ?>
     <div class="col-md-4 mb-3">
        <label>Signatory PAN Verification</label>
        <span class="badge badge-success">Completed</span>
    </div>
    <?php } ?>
    
    <?php if($bankjson["account_status"] != 'VALID'){ ?>
     <div class="col-md-4 mb-3">
        <label>Bank Verification</label>
        <span class="badge badge-danger">Incomplete</span>
    </div>
    <?php }else{ ?>
     <div class="col-md-4 mb-3">
        <label>Bank Verification</label>
        <span class="badge badge-success">Completed</span>
    </div>
    <?php } ?>
    
</form>



              </div>
              
							<div class="col-md-12">

<form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    
    <div class="col-md-12 mb-4">
        <h4>Business Overview Details</h4>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Business Type</label>
        <h5><?php echo htmlspecialchars($day['business_type'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Business Category</label>
        <h5><?php echo htmlspecialchars($day['business_category'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-12 mb-3">
        <label>Business Description</label>
        <h6><?php echo htmlspecialchars($day['business_description'], ENT_QUOTES, 'UTF-8'); ?></h6>
    </div>
   
    <div class="col-md-6 mb-3">
        <label>Website/App</label>
        <h5><?php echo htmlspecialchars($day['website_proof'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Website URL</label>
        <h5><?php echo htmlspecialchars($day['website_url'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Platform Type</label>
        <h5><?php echo htmlspecialchars($day['platform_type'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Expected Trans./Year</label>
        <h5><?php echo htmlspecialchars($day['peryear_txns'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Average Ticket Amount</label>
        <h5><?php echo htmlspecialchars($day['avg_ticketamt'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
   
</form>



              </div>
							<div class="col-md-12">

<form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    
    <div class="col-md-12 mb-4">
        <h4>Business Details</h4>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>GSTIN Have ?</label>
        <h5><?php echo ($day['gst_proof'] == '1') ? 'Yes' : 'No'; ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>GSTIN</label>
        <h5><?php echo htmlspecialchars($day['gst_no'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
   
    <div class="col-md-6 mb-3">
        <label>Business PAN</label>
        <h5><?php echo htmlspecialchars($day['business_pan'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Authorized Signatory PAN</label>
        <h5><?php echo htmlspecialchars($day['signatory_pan'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    <div class="col-md-12 mb-3">
        <label>Address</label>
        <h5><?php echo htmlspecialchars($gstjson["principal_place_address"], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
   
</form>



              </div>
							<div class="col-md-12">

<form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    
    <div class="col-md-12 mb-4">
        <h4>Bank Details</h4>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Account Number</label>
        <h5><?php echo htmlspecialchars($day['account_no'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>IFSC Code</label>
        <h5><?php echo htmlspecialchars($day['ifsc_code'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
   
    <div class="col-md-6 mb-3">
        <label>Account Holder Name</label>
        <h5><?php echo htmlspecialchars($bankjson['name_at_bank'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Bank Name</label>
        <h5><?php echo htmlspecialchars($bankjson['bank_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Account Type</label>
        <h5><?php echo ($day['bankaccount_type'] == 1) ? 'Current' : 'Savings'; ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Branch</label>
        <h5><?php echo htmlspecialchars($bankjson['branch'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>City</label>
        <h5><?php echo htmlspecialchars($bankjson['city'], ENT_QUOTES, 'UTF-8'); ?></h5>
    </div>
   
</form>



              </div>
							<div class="col-md-12">

<form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    
    <div class="col-md-12 mb-4">
        <h4>Merchant Documents</h4>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Auth Sign PAN </label>
        <a href="<?= $site_url ?>/imbpg/userkyc_doc/<?= $day['pancard_img'] ?>" target="_blank">View Auth Sign PAN Image</a>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>STIN/GST Declaration</label>
        <a href="<?= $site_url ?>/imbpg/userkyc_doc/<?= $day['gst_img'] ?>" target="_blank">View GSTIN Image</a>
    </div>
   
    <div class="col-md-6 mb-3">
        <label>Co. Reg. Address</label>
       <a href="<?= $site_url ?>/imbpg/userkyc_doc/<?= $day['regaddress_img'] ?>" target="_blank">View Reg.Address Image</a>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Aadhar Card (Front and Back)</label>
        <a href="<?= $site_url ?>/imbpg/userkyc_doc/<?= $day['aadharcard_img'] ?>" target="_blank">View Aadhaar Card Image</a>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Cancelled Bank Cheque</label>
       <a href="<?= $site_url ?>/imbpg/userkyc_doc/<?= $day['cancelled_bankcheque'] ?>" target="_blank">View Cancelled Bank Cheque Image</a>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Bank Statement</label>
       <a href="<?= $site_url ?>/imbpg/userkyc_doc/<?= $day['bankstatement_img'] ?>" target="_blank">View Bank Statement Image</a>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Udyam Aadhaar</label>
        <a href="<?= $site_url ?>/imbpg/userkyc_doc/<?= $day['udhyamadhaar_img'] ?>" target="_blank">View Udyam Aadhaar Image</a>
    </div>
    
    <div class="col-md-6 mb-3">
        <label>Others Document</label>
        <a href="<?= $site_url ?>/imbpg/userkyc_doc/<?= $day['other_img'] ?>" target="_blank">View Others Doc Image</a>
    </div>
   
</form>



              </div>
              
              <?php 
              
              if(isset($aadharjson["status"]) && $aadharjson["status"] == 'VALID'){ 
                  
              if($aadharjson["gender"] == 'M'){
                  $gender = 'Male';
              }else{
                  $gender = 'Female';
              }
              ?>
              <div class="col-12 col-sm-12 py-2 text-center">
              
              <h5 class="mb-3" style="border-bottom: 0.6px solid #ccc9c9;padding: 5px 0;">Aadhaar Card Details.</h5>
              <div class="invoice-body">
            <div class="user-details d-flex align-items-center" id="user_aadhar_data">
                <div class="user-image">
                    <img src="data:image/jpeg;base64,<?= $aadharjson["photo_link"] ?>" alt="User Image">
                </div>
                <div class="user-info"  style="text-align: left;margin-left: 20px;">
                    <p>  <strong>Name:  </strong>  <?= $aadharjson["name"] ?></p>
                    <p> <?= $aadharjson["care_of"] ?></p>
                    <p><strong>Gender:  </strong> <?= $gender ?></p>
                    <p><strong>DOB:  </strong> <?= $aadharjson["dob"] ?></p>
                    <p><strong>Address:  </strong> <?= $aadharjson["address"] ?></p>
                    <p><strong>Pincode:  </strong> <?= $aadharjson["split_address"]["pincode"] ?></p>
                    
                </div>
            </div>
        </div>
              
            </div>
            
            <?php } ?>
            
              <?php 
              
              if(isset($panjson["valid"]) && $panjson["valid"] == true && $panjson["pan_status"] == 'VALID'){ 
                  
            
              ?>
              <div class="col-12 col-sm-12 py-2 text-center">
              
              <h5 class="mb-3" style="border-bottom: 0.6px solid #ccc9c9;padding: 5px 0;">Signatory PAN Details.</h5>
              <div class="invoice-body">
            <div class="user-details d-flex align-items-center" id="user_aadhar_data">
               
                <div class="user-info"  style="text-align: left;margin-left: 20px;">
                    <p><strong>Pan Number   :  </strong> <?= $panjson["pan"] ?></p>
                    <p><strong>Pan Type   :  </strong> <?= $panjson["type"] ?></p>
                    <p><strong>Name On Pancard   :  </strong>  <?= $panjson["registered_name"] ?></p>
                    <p><strong>Aadhaar Seeding Status   :  </strong> <?= $panjson["aadhaar_seeding_status_desc"] ?></p>
                    
                </div>
            </div>
        </div>
              
            </div>
            
            <?php } ?>
            
            
             
            
              <?php 
              
              if(isset($bpanjson["valid"]) && $bpanjson["valid"] == true && $bpanjson["pan_status"] == 'VALID'){ 
                  
            
              ?>
              <div class="col-12 col-sm-12 py-2 text-center">
              
              <h5 class="mb-3" style="border-bottom: 0.6px solid #ccc9c9;padding: 5px 0;">Business PAN Details.</h5>
              <div class="invoice-body">
            <div class="user-details d-flex align-items-center" id="user_aadhar_data">
               
                <div class="user-info"  style="text-align: left;margin-left: 20px;">
                    <p><strong>Pan Number   :  </strong> <?= $bpanjson["pan"] ?></p>
                    <p><strong>Pan Type   :  </strong> <?= $bpanjson["type"] ?></p>
                    <p><strong>Name On Pancard   :  </strong>  <?= $bpanjson["registered_name"] ?></p>
                    <p><strong>Aadhaar Seeding Status   :  </strong> <?= $bpanjson["aadhaar_seeding_status_desc"] ?></p>
                    
                </div>
            </div>
        </div>
              
            </div>
            
            <?php } ?>
            
              <?php 
              
              if(isset($gstjson["valid"]) && $gstjson["valid"] == true && $gstjson["gst_in_status"] == 'Active'){
                  
            
              ?>
              <div class="col-12 col-sm-12 py-2 text-center">
              
              <h5 class="mb-3" style="border-bottom: 0.6px solid #ccc9c9;padding: 5px 0;">GSTIN Details.</h5>
              <div class="invoice-body">
            <div class="user-details d-flex align-items-center" id="user_aadhar_data">
               
                <div class="user-info"  style="text-align: left;margin-left: 20px;">
                    <p><strong>GSTIN Number   :  </strong> <?= $gstjson["GSTIN"] ?></p>
                    <p><strong>Date Of Registration   :  </strong> <?= $gstjson["date_of_registration"] ?></p>
                    <p><strong>Cancellation Date   :  </strong> <?= $gstjson["cancellation_date"] ?></p>
                    <p><strong>Company Type   :  </strong>  <?= $gstjson["constitution_of_business"] ?></p>
                    <p><strong>Company Name   :  </strong>  <?= $gstjson["trade_name_of_business"] ?></p>
                    <p><strong>Business Name   :  </strong>  <?= $gstjson["legal_name_of_business"] ?></p>
                    <p><strong>Taxpayer Type   :  </strong>  <?= $gstjson["taxpayer_type"] ?></p>
                    <p><strong>Company Address   :  </strong>  <?= $gstjson["principal_place_address"] ?></p>
                    <p><strong>Center Jurisdiction   :  </strong>  <?= $gstjson["center_jurisdiction"] ?></p>
                    <p><strong>GST Status   :  </strong> <?= $gstjson["gst_in_status"] ?></p>
                    
                </div>
            </div>
        </div>
              
            </div>
            
            <?php } ?>
            
             
              <div class="col-12 col-sm-12 py-2 text-center">
                  
              <h5 class="mb-3" style="border-bottom: 0.6px solid #ccc9c9;padding: 5px 0;">Update User KYC Status.</h5>
              
             <form class="row mb-4" method="POST" action="">
    
        <div class="col-md-6 mb-3">
            <label>Select Status</label>
            <select name="kycstatus" class="form-control">
              <option value="">Select</option>  
              <option value="1">Approved</option>  
              <option value="0">Reject</option>  
            </select>
        </div>
        
        <div class="col-md-6 mb-3">
            <label>Remark</label>
            <textarea  name="remark" placeholder="Remark" class="form-control"></textarea>
        </div>

    
    <div class="col-md-12 mb-3 mt-2">
        <button type="submit" name="update" class="btn btn-primary btn-sm">Update KYC</button>
    </div>
</form>
              
            </div>
           
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

</div>
</body>
<!-- Essential javascripts for application to work-->
<script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <!-- The javascript plugin to display page loading on top-->
    <script src="js/plugins/pace.min.js"></script>
    <!-- Page specific javascripts-->
<script src="assets/js/ready.min.js"></script>
    <script src="js/mainscript.js"></script>
<script>
$( document ).ready(function() {
$('#disclaimer').modal({backdrop: 'static', keyboard: false})  
$("#disclaimer").modal("show");
});
</script>
<script>
$(document).ready(function () {
$( ".datepicker" ).datepicker({
  dateFormat: "dd-mm-yy"
});
});
</script>

<!-- Mirrored from upigetway.com/auth/register by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 19 Oct 2023 17:52:40 GMT -->
</html>			