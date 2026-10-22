<?php include "header.php"; ?>

<style>

.grid {
  display: grid;
  grid-gap: 1em;
  margin: 0;
  padding: 0;
 
  @media (min-width: 60em) {
    grid-template-columns: repeat(1, 1fr);
  }
  
  @media (min-width: 90em) {
    grid-template-columns: repeat(1, 1fr);
  }
}

/* Card Styles */

.card {
  background: #fff;
  border: 1px solid #e2ebf6;
  border-radius: 0.25em;
  cursor: pointer;
  display: flex;
  padding: 1em;
  box-shadow:none;
  position: relative;
  transition: all 0.2s;
  
  &:hover {
    border-color: #c4d1e1;
    box-shadow: 0 4px 12px -4px rgba(0, 0, 0, 0.15);
    transform: translate(-4px, -4px);
  }
}

.caerdh {
    display: flex;
    align-items: center;
}

.card__image {
  border-radius: 0.25em;
  height: 5em;
  min-width: 5em;
}

.card__content {
  flex: auto;
  padding: 0 1em;
}

.card h2 {
  font-weight: 700;
  margin: 0;
}

.card p {
  color: #546e7a;
  margin: 0;
}

/* Checkbox Styles */

.checkbox {
  -webkit-appearance: none;
  -moz-appearance: none;
  cursor: pointer;
  background: #e2ebf6;
  border-radius: 50%;
  height: 2em;
  margin: 0;
  margin-left: auto;
  flex: none;
  outline: none;
  position: absolute;
top: 15px;
right: 10px;
  transition: all 0.2s;
  width: 2em; 
  
  &:after {
    border: 2px solid #fff;
    border-top: 0;
    border-left: 0;
    content: '';
    display: block;
    height: 1em;
    left: 0.625em;
    position: absolute;
    top: 0.25em;
    transform: rotate(45deg);
    width: 0.5em;
  }
  
  &:focus {
      box-shadow: 0 0 0 2px rgba(100, 193, 117, 0.6);
    }
  
  &:checked {
    background: #64c175;
    border-color: #64c175;
  }
}

.checkbox-control__target {
  bottom: 0;
  cursor: pointer;
  left: 0;
  opacity: 0;
  position: absolute;
  right: 0;
  top: 0;
}

@media(max-width : 450px){
    
    .referabtn{
        display:block;
        width:100%;
        margin:10px 0;
    }
}

</style>

<?php

  $today = date("Y-m-d");
  $sponser_id = $userdata["sponser_id"];
  $refertype = $userdata["refer_type"];

  $fetchbank = $conn->query("SELECT * FROM `user_bank` WHERE userid = '$userid'")->fetch_assoc();
  $todayrefer = $conn->query("SELECT COUNT(id) as data FROM `refer_history` WHERE sponser_id = '$sponser_id' AND DATE(date) = '$today'")->fetch_assoc();
  $todayearning = $conn->query("SELECT SUM(amount) as data FROM `refer_history` WHERE sponser_id = '$sponser_id' AND DATE(date) = '$today'")->fetch_assoc();
  $todaywithdrawl = $conn->query("SELECT SUM(amount) as data FROM `fund_request` WHERE userid = '$userid' AND fund_type = 'referal' AND DATE(date) = '$today'")->fetch_assoc();
  $totalrefer = $conn->query("SELECT COUNT(id) as data FROM `refer_history` WHERE sponser_id = '$sponser_id'")->fetch_assoc();
  $totalearning = $conn->query("SELECT SUM(amount) as data FROM `refer_history` WHERE sponser_id = '$sponser_id'")->fetch_assoc();
  $totalwithdrawl = $conn->query("SELECT SUM(amount) as data FROM `fund_request` WHERE userid = '$userid' AND fund_type = 'referal'")->fetch_assoc();


  if (isset($_POST['saverpbtn'])) {
      
   $refertype =  $_POST['refertype'];
   
    $result = $conn->query("UPDATE `users` SET refer_type = '$refertype' WHERE id='{$userdata["id"]}'");
    
   if($result){
       
       echo '<script src="js/jquery-3.2.1.min.js"></script>';        
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '
    <script>
    $("#loading_ajax").hide();
        Swal.fire({
            title: "Referal Program Changed Successfully!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "success"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "reffer_friends"; // Replace with your desired redirect URL
            }
        });
    </script>
';
exit;
   }else{
       echo '<script src="js/jquery-3.2.1.min.js"></script>';        
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '
    <script>
    $("#loading_ajax").hide();
        Swal.fire({
            title: "Opps! Falid to change referal program !",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "reffer_friends"; // Replace with your desired redirect URL
            }
        });
    </script>
';
exit;
   }
   
  }
  
  
      
  if (isset($_POST['addbank'])) {
      
   $bankname =  $_POST['bankname'];
   $acc_h_name =  $_POST['acc_h_name'];
   $acc_no =  $_POST['acc_no'];
   $ifsc = $_POST['ifsc'];
  $acctype = $_POST['acctype'];
  $branch = $_POST['branch'];

    if ($bankname == '' || $acc_no == '' || $ifsc == '' || $acc_h_name == '' || $branch == '' || $acctype == '') {
        // The mobile number already exists, display an error message
        echo '
    <script src="js/jquery-3.2.1.min.js"></script>'; echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';  echo '<script>$("#loading_ajax").hide();
        Swal.fire({
            title: "Opps! All Inputs Are Requried!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "reffer_friends"; // Replace with your desired redirect URL
            }
        });
    </script>
';
    }else{
        // Proceed with user registration

        $register = "INSERT INTO `user_bank`(`userid`,`bank_name`, `acc_holder_name`, `acc_no`, `ifsc`, `branch`, `acc_type`, `status`)
        VALUES ('{$userdata["id"]}','$bankname','$acc_h_name','$acc_no','$ifsc','$branch','$acctype','1')";
        $result = mysqli_query($conn, $register);

   
   if($result){
       
        echo '
    <script src="js/jquery-3.2.1.min.js"></script>'; echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';  echo '<script>$("#loading_ajax").hide();
        Swal.fire({
            title: "Congratulations! Bank Added!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "success"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "reffer_friends"; // Replace with your desired redirect URL
            }
        });
    </script>
';
exit;
   }else{
        echo '
    <script src="js/jquery-3.2.1.min.js"></script>'; echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';  echo '<script>$("#loading_ajax").hide();
        Swal.fire({
            title: "Opps! Something Went Wrong!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "reffer_friends"; // Replace with your desired redirect URL
            }
        });
    </script>
';
exit;
   }
  }
  }
  
  if (isset($_POST['withdrawrmoney'])) {
      
   $amount =  $_POST['amount'];
   $txnid =  'IMPYWTXN'.mt_rand(100000,999999999);
   $remark = 'Your Withdraw Request Is Under Process';
      
    $updateuserbal = $userdata["wallet"]-$amount;
           
       
           if($updateuserbal < 0){
               echo '<script src="js/jquery-3.2.1.min.js"></script>';        
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
                 echo '
    <script>
    $("#loading_ajax").hide();
        Swal.fire({
            title: "Low Balance On Your Wallet ! Try Again Later",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "reffer_friends"; // Replace with your desired redirect URL
            }
        });
    </script>
';
exit;
           }
      
    if ($amount < 500) {
        
        echo '<script src="js/jquery-3.2.1.min.js"></script>';        
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '
    <script>
    $("#loading_ajax").hide();
        Swal.fire({
            title: "Minimum Fund Request is ₹500!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "reffer_friends"; // Replace with your desired redirect URL
            }
        });
    </script>
';
    }else{
        
        $register = "INSERT INTO `fund_request`(`userid`, `bankid`, `txn_id`,`amount`, `status`, `remark`, `utr_no`, `fund_type`) VALUES ('{$userdata["id"]}','{$fetchbank["id"]}','$txnid','$amount','2','$remark','Payment Pending','referal')";
        $result = mysqli_query($conn, $register);

   if($result){
       
       $conn->query("UPDATE `users` SET wallet = '$updateuserbal' WHERE id='{$userdata["id"]}'");
       
       echo '<script src="js/jquery-3.2.1.min.js"></script>';        
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '
    <script>
    $("#loading_ajax").hide();
        Swal.fire({
            title: "Congratulations! Withdraw Request Applied!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "success"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "reffer_friends"; // Replace with your desired redirect URL
            }
        });
    </script>
';
exit;
   }else{
       echo '<script src="js/jquery-3.2.1.min.js"></script>';        
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '
    <script>
    $("#loading_ajax").hide();
        Swal.fire({
            title: "Opps! Something Went Wrong!",
            text: "Please Click Ok Button!!",
            confirmButtonText: "Ok",
            icon: "error"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "reffer_friends"; // Replace with your desired redirect URL
            }
        });
    </script>
';
exit;
   }
  }
  }


?>

<main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-gift"></i> Refer a Friend</h1>
          <!-- <p>Start a beautiful journey here</p> -->
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
        </ul>
      </div>
      <div class="tile mb-4">
        <div class="page-header">
          <div class="row">
              <?php if($refertype == 1){ ?>      
              <div class="col-md-6 mb-3">
                  <h3>Referal Dashboard</h3>
<p>Manage easy for your referal earnings from our platform.</p>
</div>
              <div class="col-md-6 mb-3">
              <button class="btn btn-primary referabtn">Wallet : ₹<?= number_format($userdata["wallet"],2) ?></button>
              <button class="btn btn-primary referabtn" data-toggle="modal" data-target="#referprogramModal">Choose Referal Program</button>
              <button class="btn btn-success referabtn" data-toggle="modal" data-target="#withdrawrmModal">Withdraw</button>
              </div>
              <div class="col-md-6 col-lg-4">
          <div class="widget-small primary coloured-icon"><i class="icon  fa-2x">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-send-fill" viewBox="0 0 16 16">
  <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z"/>
</svg>
</i>
            <div class="info">
              <h4>Today Referal</h4>
              <p><b><?php echo number_format($todayrefer["data"]) ?></b></p>
            </div>
          </div>
        </div>
              <div class="col-md-6 col-lg-4">
          <div class="widget-small primary coloured-icon"><i class="icon  fa-3x"> <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-currency-rupee" viewBox="0 0 16 16">
  <path d="M4 3.06h2.726c1.22 0 2.12.575 2.325 1.724H4v1.051h5.051C8.855 7.001 8 7.558 6.788 7.558H4v1.317L8.437 14h2.11L6.095 8.884h.855c2.316-.018 3.465-1.476 3.688-3.049H12V4.784h-1.345c-.08-.778-.357-1.335-.793-1.732H12V2H4z"/>
</svg></i>
            <div class="info">
              <h4>Today Earning</h4>
    
              <p><b><?php echo number_format($todayearning["data"],2) ?></b></p>
            </div>
          </div>
        </div>
        
              <div class="col-md-6 col-lg-4">
          <div class="widget-small primary coloured-icon"><i class="icon  fa-3x">
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-bank2" viewBox="0 0 16 16">
  <path d="M8.277.084a.5.5 0 0 0-.554 0l-7.5 5A.5.5 0 0 0 .5 6h1.875v7H1.5a.5.5 0 0 0 0 1h13a.5.5 0 1 0 0-1h-.875V6H15.5a.5.5 0 0 0 .277-.916zM12.375 6v7h-1.25V6zm-2.5 0v7h-1.25V6zm-2.5 0v7h-1.25V6zm-2.5 0v7h-1.25V6zM8 4a1 1 0 1 1 0-2 1 1 0 0 1 0 2M.5 15a.5.5 0 0 0 0 1h15a.5.5 0 1 0 0-1z"/>
</svg>
</i>
            <div class="info">
              <h4>Today Withdrawal</h4>
              <p><b><?php echo number_format($todaywithdrawl["data"],2) ?></b></p>
            </div>
          </div>
        </div>
              <div class="col-md-6 col-lg-4">
          <div class="widget-small primary coloured-icon"><i class="icon  fa-3x">
               <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-send-fill" viewBox="0 0 16 16">
  <path d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z"/>
</svg>
</i>
            <div class="info">
              <h4>Total Referal</h4>
              <p><b><?php echo number_format($totalrefer["data"]) ?></b></p>
            </div>
          </div>
        </div>
              <div class="col-md-6 col-lg-4">
          <div class="widget-small primary coloured-icon"><i class="icon  fa-3x">
             <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-currency-rupee" viewBox="0 0 16 16">
  <path d="M4 3.06h2.726c1.22 0 2.12.575 2.325 1.724H4v1.051h5.051C8.855 7.001 8 7.558 6.788 7.558H4v1.317L8.437 14h2.11L6.095 8.884h.855c2.316-.018 3.465-1.476 3.688-3.049H12V4.784h-1.345c-.08-.778-.357-1.335-.793-1.732H12V2H4z"/>
</svg>
</i>
            <div class="info">
              <h4>Total Earning</h4>
              <p><b><?php echo number_format($totalearning["data"],2) ?></b></p>
            </div>
          </div>
        </div>
              <div class="col-md-6 col-lg-4">
          <div class="widget-small primary coloured-icon"><i class="icon  fa-3x">
           <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-bank2" viewBox="0 0 16 16">
  <path d="M8.277.084a.5.5 0 0 0-.554 0l-7.5 5A.5.5 0 0 0 .5 6h1.875v7H1.5a.5.5 0 0 0 0 1h13a.5.5 0 1 0 0-1h-.875V6H15.5a.5.5 0 0 0 .277-.916zM12.375 6v7h-1.25V6zm-2.5 0v7h-1.25V6zm-2.5 0v7h-1.25V6zm-2.5 0v7h-1.25V6zM8 4a1 1 0 1 1 0-2 1 1 0 0 1 0 2M.5 15a.5.5 0 0 0 0 1h15a.5.5 0 1 0 0-1z"/>
</svg>
</i>
            <div class="info">
              <h4>Total Withdrawal</h4>
              <p><b><?php echo number_format($totalwithdrawl["data"],2) ?></b></p>
            </div>
          </div>
        </div>
              <?php } ?> 
             
            <div class="col-lg-12">
						<!-- <h4 class="page-title">UPI Settings</h4> -->
						<div class="row row-card-no-pd">
							<div class="col-md-12 text-right">
		<?php if($refertype != 1){ ?>
 <button class="btn btn-primary" data-toggle="modal" data-target="#referprogramModal">Choose Referal Program</button>
              <?php } ?> 
              <div class="main-panel">
    <div class="content">
        <div class="container">
           <div class="row text center">
    <div class="col-md-12 text-center mt-2">
        <h2 class="f_w_700" style="color: #313131;">Reffer Your Friends And Earn Rewards</h2>
        <img src="assets/img/gift-box.svg" class="boximg" style="width:130px;">
        <h1 class="f_w_800" style="color: #313131;">Get 100% Granteed Rewards</h1>
        <span style="color: #313131;font-size:12px;font-weight:600;letter-specing:1px;">Start sharing today and watch your earnings grow with every new subscriber you bring on board!</span>
        
        <div class="copy-text">
		<input type="text" class="text" style="
    padding: 10px 15px;
    width:70%;
    margin-top: 8%;
    background: rgba(245, 246, 250,0.3);
    color: #313131;
    border: 1px dashed rgba(000, 000, 000,0.4);
    border-width: 2px;
    font-size: 15px;
    font-weight: 600;
    letter-spacing: 1px;
    border-radius: 7px;
    box-shadow: -8px 6px 20px -13px #000;
" value="<?= $site_url ?>/merchant/register?sponserid=<?= $userdata["sponser_id"] ?>" />
		<button style="
	border:1px solid rgba(000, 000, 000,0.3);	
    padding: 10px 15px;
    margin-left:5px;
    background: rgba(245, 246, 250,0.3);
    color: #313131;
    font-size: 16px;
    font-weight: 800;
    border-radius: 5px;
    box-shadow: 5px -5px 20px -15px #000;
    cursor:pointer;
"><i class="fa fa-clone"></i></button>
	</div>
	<span style="color: #313131;font-size:12px;font-weight:600;letter-specing:1px;">Copy the code and share with your friends.</span>
        
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
<?php if($refertype == 1){ ?> 
  <h4 class="page-title mt-5">Recent Withdraw History</h4>
						<div class="row row-card-no-pd">
							<div class="col-md-12">
								
							<div class="table-responsive">
							<table class="table table-sm table-hover table-bordered table-head-bg-primary" id="dataTable" width="100%">
										<thead>
											<tr>
												<th>#</th>
												<th>Txn Id</th>
												<th>Amount</th>
												<th>UTR No</th>
												<th>Status</th>
												<th>Remarks</th>
												<th>Date</th>
											
											</tr>
										</thead>
										<tbody>
<?php
$query = "SELECT * FROM `fund_request` WHERE userid='{$userdata["id"]}' AND fund_type = 'referal' ORDER BY `id` DESC LIMIT 5";
$query_run = mysqli_query($conn, $query);

if ($query_run) {
    while ($row = mysqli_fetch_assoc($query_run)) {
       
       if($row['status'] == 1){
          $st = '<span class="badge badge-success">Approved</span>';
      }else if($row['status'] == 0){
          $st = '<span class="badge badge-danger">Rejected</span>';
          
      }else{
          
          $st = '<span class="badge badge-warning">Pending</span>';
      }
        
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>" . htmlspecialchars($row['txn_id'], ENT_QUOTES, 'UTF-8') . "</td>";
echo "<td>" . htmlspecialchars($row['amount'], ENT_QUOTES, 'UTF-8') . "</td>";
echo "<td>" . htmlspecialchars($row['utr_no'], ENT_QUOTES, 'UTF-8') . "</td>";
echo "<td>" . $st . "</td>";
echo "<td>" . htmlspecialchars($row['remark'], ENT_QUOTES, 'UTF-8') . "</td>";
echo "<td>" . htmlspecialchars(date("d-M Y h:i:s A",strtotime($row['date'])), ENT_QUOTES, 'UTF-8') . "</td>";

     ?>
     
     
     
     
     <?php
        echo "</tr>";
    }
} else {
    echo "Error in query: " . mysqli_error($conn); 
}
?>
											
										</tbody>
									</table>
							</div>
							</div>
						</div>
						<?php } ?> 
</div>

						</main>
						
						<!-- The Modal -->
<div class="modal fade" id="referprogramModal" tabindex="-1" role="dialog" aria-labelledby="referprogramModal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="apikeygenrateModalLabel">Choose Your Referal Program</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="firstPage">
               <div class="container">
    <form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">               
  <h3>Choose One</h3>
  <ul class="grid">
    <!-- Start Card 1 -->
      <li class="card">
         <div class="caerdh">
        <img src="assets/imbpg_img/earnrp.svg" class="card__image" alt="earn"> 
        <div class="card__content">
          <h4>Earning Program</h4>
          <p>Invite your friends to experience our premium subscription services, and when they make their first purchase, you’ll earn a generous 30% commission! It’s simple.<p>
        </div>
         </div>
        <label class="checkbox-control">
          <input type="radio" name="refertype" value="1" class="checkbox" <?= ($refertype == '1') ? 'checked' : '' ?> >
          <span class="checkbox-control__target">Card Label</span>
        </label>
      </li>
    <!-- End Card 1 -->
    
    <!-- Start Card 2 -->
      <li class="card">
           <div class="caerdh">
       <img src="assets/imbpg_img/subsrp.svg" class="card__image" alt="earn">
        <div class="card__content">
          <h4>Subscription Program</h4>
          <p>As a valued subscriber, you can also benefit from our Subscription Program! For every subscription purchased through your referral link, you’ll earn 1/3 of the days of their subscription as a reward.<p>
         </div>
        </div>
        <label class="checkbox-control">
          <input type="radio" name="refertype" value="0" <?= ($refertype == '0') ? 'checked' : '' ?> class="checkbox">
          <span class="checkbox-control__target">Card Label</span>
        </label>
      </li>
    <!-- End Card 2 -->
    
  </ul>
<div class="col-md-12 text-center">
<button type="submit" name="saverpbtn" id="saverpbtn" class="btn btn-success mt-4">Save Program</button>
</div>
  </form>
</div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="withdrawrmModal" tabindex="-1" role="dialog" aria-labelledby="withdrawrmModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="apikeygenrateModalLabel">Withdraw your earnings</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php if(empty($fetchbank)){ ?>
            <div class="modal-body container" id="firstPage">
               <div class="row">							
							<div class="col-md-12">

 <form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      
    <div class="col-md-6 mb-2">
        <label>Bank Name</label>
    <select name="bankname" class="form-control" required>
  <option selected="selected" value="0">--Select --</option>
  <option value="ALLAHABAD BANK">ALLAHABAD BANK </option>
  <option value="ANDHRA BANK">ANDHRA BANK</option>
  <option value="AXIS BANK">AXIS BANK</option>
  <option value="STATE BANK OF INDIA">STATE BANK OF INDIA</option>
  <option value="BANK OF BARODA">BANK OF BARODA</option>
  <option value="UCO BANK">UCO BANK</option>
  <option value="UNION BANK OF INDIA">UNION BANK OF INDIA</option>
  <option value="BANK OF INDIA">BANK OF INDIA</option>
  <option value="BANDHAN BANK LIMITED">BANDHAN BANK LIMITED</option>
  <option value="CANARA BANK">CANARA BANK</option>
  <option value="GRAMIN VIKASH BANK">GRAMIN VIKASH BANK</option>
  <option value="CORPORATION BANK">CORPORATION BANK</option>
  <option value="INDIAN BANK">INDIAN BANK</option>
  <option value="INDIAN OVERSEAS BANK">INDIAN OVERSEAS BANK</option>
  <option value="ORIENTAL BANK OF COMMERCE">ORIENTAL BANK OF COMMERCE</option>
  <option value="PUNJAB AND SIND BANK">PUNJAB AND SIND BANK</option>
  <option value="PUNJAB NATIONAL BANK">PUNJAB NATIONAL BANK</option>
  <option value="RESERVE BANK OF INDIA">RESERVE BANK OF INDIA</option>
  <option value="SOUTH INDIAN BANK">SOUTH INDIAN BANK</option>
  <option value="UNITED BANK OF INDIA">UNITED BANK OF INDIA</option>
  <option value="CENTRAL BANK OF INDIA">CENTRAL BANK OF INDIA</option>
  <option value="VIJAYA BANK">VIJAYA BANK</option>
  <option value="DENA BANK">DENA BANK</option>
  <option value="BHARATIYA MAHILA BANK LIMITED">BHARATIYA MAHILA BANK LIMITED</option>
  <option value="FEDERAL BANK LTD">FEDERAL BANK LTD </option>
  <option value="HDFC BANK LTD">HDFC BANK LTD</option>
  <option value="ICICI BANK LTD">ICICI BANK LTD</option>
  <option value="IDBI BANK LTD">IDBI BANK LTD</option>
  <option value="PAYTM BANK">PAYTM BANK</option>
  <option value="FINO PAYMENT BANK">FINO PAYMENT BANK</option>
  <option value="INDUSIND BANK LTD">INDUSIND BANK LTD</option>
  <option value="KARNATAKA BANK LTD">KARNATAKA BANK LTD</option>
  <option value="KOTAK MAHINDRA BANK">KOTAK MAHINDRA BANK</option>
  <option value="YES BANK LTD">YES BANK LTD</option>
  <option value="SYNDICATE BANK">SYNDICATE BANK</option>
  <option value="BANK OF INDIA">BANK OF INDIA</option>
  <option value="BANK OF MAHARASHTRA">BANK OF MAHARASHTRA</option>
</select>
    </div>
    <div class="col-md-6 mb-2"><label>Account Holder Name</label> <input type="text" name="acc_h_name" class="form-control" required="" /></div>
    <div class="col-md-6 mb-2"><label>Account Number</label> <input type="number" name="acc_no" class="form-control" required="" /></div>
    <div class="col-md-6 mb-2"><label>IFSC Code</label> <input type="text" name="ifsc" class="form-control" required="" /></div>
    <div class="col-md-6 mb-2">
        <label>Account Type</label>
    <select name="acctype" class="form-control" required>
  <option selected="selected" value="0">--Select --</option>
  <option value="1">Savings</option>
  <option value="2">Current</option>
  </select>
  </div>
    <div class="col-md-6 mb-2"><label>Branch</label> <input type="text" name="branch" class="form-control" required="" /></div>
   
    <div class="col-md-12 mb-2 mt-2 text-center"><button type="submit" name="addbank" class="btn btn-primary btn-sm">Add Bank</button>
    </div>

</form>


              </div>
            </div>
            </div>
            <?php }else{ ?>
            <div class="modal-body" id="secondPage">
               <div class="row row-card-no-pd">							
							<div class="col-md-12">

 <form class="row mb-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
     
    <div class="col-md-12 mb-2">
        <div class="card">
         <div class="caerdh">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-bank2" viewBox="0 0 16 16">
  <path d="M8.277.084a.5.5 0 0 0-.554 0l-7.5 5A.5.5 0 0 0 .5 6h1.875v7H1.5a.5.5 0 0 0 0 1h13a.5.5 0 1 0 0-1h-.875V6H15.5a.5.5 0 0 0 .277-.916zM12.375 6v7h-1.25V6zm-2.5 0v7h-1.25V6zm-2.5 0v7h-1.25V6zm-2.5 0v7h-1.25V6zM8 4a1 1 0 1 1 0-2 1 1 0 0 1 0 2M.5 15a.5.5 0 0 0 0 1h15a.5.5 0 1 0 0-1z"/>
</svg>
        <div class="card__content">
          <h5><?= $fetchbank["bank_name"] ?></h5>
          <p>Account - XXXXXX<?= substr($fetchbank["acc_no"],-4) ?>   IFSC - <?= strtoupper($fetchbank["ifsc"]) ?>   Account Type  - <?= ($fetchbank["acc_type"] == '1') ? 'Savings' : 'Current' ?><p>
        </div>
         </div>
        <label class="checkbox-control">
          <input type="radio" name="refertype" value="1" checked class="checkbox">
          <span class="checkbox-control__target">Card Label</span>
        </label>
      </div>
 </div>
    <div class="col-md-12 mb-2"><label>Amount</label> <input type="number" name="amount" class="form-control" required="" /></div>
   
    <div class="col-md-12 mb-2 mt-2 text-center"><button type="submit" name="withdrawrmoney" class="btn btn-primary btn-sm">Withdraw</button>
    </div>
    
    <div class="col-md-12 mb-2 mt-2">
       <small>
* Minimum Withdrawal Amount: ₹500 <br>
* Withdrawal Processing Time: 1-2 Working Days.</small>
    </div>

</form>


              </div>
            </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>


<!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/mainscript.js"></script>

<script>
        //  reffer user copy to clipboard code js
    
    let copyText = document.querySelector(".copy-text");
copyText.querySelector("button").addEventListener("click", function () {
	let input = copyText.querySelector("input.text");
	input.select();
	document.execCommand("copy");
	copyText.classList.add("active");
	window.getSelection().removeAllRanges();
	setTimeout(function () {
		copyText.classList.remove("active");
	}, 2500);
});
</script>


 </body>
</html>
