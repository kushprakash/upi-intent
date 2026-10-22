<?php
include "header.php";

$merchant_id = $_POST["merchant_id"];
$paytm_mobile = $_POST["paytm_mobile"];

?>

<main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-user"></i> Paytm Merchant Verify</h1>
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
                <h4 class="page-title">Paytm Merchant Verify</h4>
                <div class="row row-card-no-pd">
                    <div class="col-md-12">
                        <form method="POST" id="paytmotpform" class="mb-2">
                            <input type="hidden" id="merchant_id" name="merchant_id" value="<?php echo $merchant_id; ?>">
                            <div class="row" id="merchant">
                                <div class="col-md-4 mb-2"> 
                                    <label>Enter Mobile Number</label> 
                                    <input type="number" id="username" name="username" placeholder="Enter Mobile Number" value="<?= $paytm_mobile ?>" class="form-control" required=""> 
                                </div>
                                <div class="col-md-4 mb-2"> 
                                    <label>Enter Password</label> 
                                    <input type="text" id="password" name="password" placeholder="Enter Password" class="form-control" required=""> 
                                </div>
                               
                                <div class="col-md-4 mb-2"> 
                                    <label>&nbsp;</label> 
                                    <button type="submit" name="verifyotp" class="btn btn-primary btn-block">Verify Paytm</button> 
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>
    <script src="js/main.js"></script>
    
    <script>
    
    function loader(value){
if(value=="show"){
swal.fire({html: '<br><img src="assets/img/loading.gif"><br><br><h5>Loading...</h5>',showConfirmButton: false});   
}else if(value=="hide"){
$(".swal2-container").css('display','none');
$("body").removeClass("swal2-shown swal2-height-auto");
$("body").css('padding-right','0');
}    
}
    
        $("#paytmotpform").submit(function(e){
        
        e.preventDefault();    
        let merchant_id = $("#merchant_id").val();   
        let username = $("#username").val();   
        let password = $("#password").val();   
            
	Swal.fire({
		title: 'Did you want to verify this account?',
		showDenyButton: true,
		showCancelButton: false,
		confirmButtonText: 'Yes',
		denyButtonText: 'No',
	}).then((result) => {
		if(result.isConfirmed) {
			if(merchant_id.length > 0) {
				loader("show");
				$.ajax({
					url: 'paytm_backend',
					type: 'POST',
					data: {
						get_merchant_otp: true,
						merchant_id,
						username,
						password,
					},
					success: function(response, textStatus, jQxhr) {
						loader("hide");
						if(response.status == true) {
							swal.fire({
								html: response.html,
								showConfirmButton: false,
								showCancelButton: true,
								cancelButtonText: "Cancel",
								allowOutsideClick: false,
								allowEscapeKey: false
							}).then(function() {
								//location.reload();
							});
						} else {
							Swal.fire(response.message, '', 'error');
						}
					},
					error: function(jqXhr, textStatus, errorThrown) {
						loader("hide");
						console.log(errorThrown);
					}
				});
			} else {
				Swal.fire('merchant is Not Valid!', '', 'info');
			}
		} else if(result.isDenied) {
			//Swal.fire('Good your account is safe', '', 'info');
		}
	});
});

function get_verify_otp(merchant_id, ip, otp,merchant_csrftoken,merchant_session) {
	if(merchant_id.length > 0) {
		if(otp.length > 0) {
			loader("show");
			$.ajax({
				url: 'paytm_backend',
				type: 'POST',
				data: {
					get_verify_otp: true,
					merchant_id: merchant_id,
					ip: ip,
					otp: otp,
					merchant_csrftoken:merchant_csrftoken,
					merchant_session:merchant_session
				},
				success: function(response, textStatus, jQxhr) {
					loader("hide");
					if(response.status == true) {
						swal.fire({
							html: response.html,
							showConfirmButton: false,
							showCancelButton: true,
							cancelButtonText: "Cancel",
							allowOutsideClick: false,
							allowEscapeKey: false
						}).then(() => {
						    window.location.href = 'upisettings'
						});
					} else {
						Swal.fire(response.message, '', 'error');
					}
				},
				error: function(jqXhr, textStatus, errorThrown) {
					loader("hide");
					console.log(errorThrown);
				}
			});
		}
	} else {
		Swal.fire('merchant is Not Valid!', '', 'info');
	}
}

    </script>
    </body>
    </html>

