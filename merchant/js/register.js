function popup(status , title , msg){
    Swal.fire({
      icon: status,
      title: title,
      text: msg,
    });
}


$("#signup-form").validate({
        errorPlacement: function(error, element) {
            error.appendTo(element.parent());
        },
        rules: {
            company: {
                required: true,
                minlength: 5
            },
            mobile: {
                required: true,
                minlength: 10,
                maxlength: 15
            },
            email: {
                required: true,
                email: true
            },
            pan: {
                required: true,
                minlength: 10,
                maxlength: 10
            },
            name: {
                required: true,
                minlength: 2
            },
            location: {
                required: true,
                minlength: 10
            },
            pin: {
                required: true,
                minlength: 6,
                maxlength: 6
            },
            password: {
                required: true,
                minlength: 6
            },
      term_condition: {
        required: true
      }
      
        },
        messages: {
            company: {
                required: "Please enter your company name",
                minlength: "Your company name must be at least 5 characters long"
            },
            mobile: {
                required: "Please enter your mobile number",
                minlength: "Your mobile number must be at least 10 digits long",
                maxlength: "Your mobile number must be at most 15 digits long"
            },
            email: {
                required: "Please enter your email address",
                email: "Please enter a valid email address"
            },
            pan: {
                required: "Please enter your PAN number",
                minlength: "Your PAN number must be exactly 10 characters long",
                maxlength: "Your PAN number must be exactly 10 characters long"
            },
            name: {
                required: "Please enter your name",
                minlength: "Your name must be at least 2 characters long"
            },
            location: {
                required: "Please enter your location",
                minlength: "Your location must be at least 10 characters long"
            },
            pin: {
                required: "Please enter your PIN code",
                minlength: "Your PIN code must be exactly 6 characters long",
                maxlength: "Your PIN code must be exactly 6 characters long"
            },
            password: {
                required: "Please enter your password",
                minlength: "Your password must be at least 6 characters long"
            },
            term_condition: {
        required: "You must agree to the terms and conditions before submitting."
      }
        }
    });


    const inputs = document.querySelectorAll('.otp-input');

    inputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            if (input.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && index > 0 && input.value.length === 0) {
                inputs[index - 1].focus();
            }
        });
    });
    
    const resendOtpBtn = document.getElementById('resendOtpBtn');
    const timerElement = document.getElementById('timer');
    let timer;

    function startTimer(duration, display) {
        let timer = duration, minutes, seconds;
        let countdown = setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = seconds;

            if (--timer < 0) {
                clearInterval(countdown);
                resendOtpBtn.classList.remove('disabled');
                display.textContent = "";
            }
        }, 1000);
    }

    
    resendOtpBtn.addEventListener('click', () => {
        if (!resendOtpBtn.classList.contains('disabled')) {
            console.log('Resend OTP');
            // Here you can add code to resend the OTP
            resendOtpBtn.classList.add('disabled');
            startTimer(30, timerElement);
        }
    });
    
    
    $("#rotpverify").submit(function(e) {

	e.preventDefault();
	$('#loading_ajax').fadeIn();
	
	let formdata = new FormData(this);
	let otp = '';
        inputs.forEach(input => {
            otp += input.value;
        });
        
        formdata.append('otp',otp);
	
	$.ajax({
		url: 'backend/MerchantAuthController.php',
		type: 'POST',
		data: formdata,
		processData: false,
		contentType: false,
		success: function(data, status) {
		    $('#loading_ajax').fadeOut();
		    let rslt = JSON.parse(data);
			if (rslt.rescode == 200) {
			    
			    inputs.forEach(input => {
                input.value = ''; 
                });
			    
	         if(rslt.type == 'mobile'){
	            localStorage.setItem('motpVerified','1'); 
	            $("#verifymobilebtn").html('Verified');
	            $("#verifymobilebtn").removeClass('btn-primary');
	            $("#verifymobilebtn").addClass('btn-success').attr('disabled','');
	            $("#mobileno").attr('readonly');
	         }else{
	             
	            localStorage.setItem('eotpVerified','1'); 
	            $("#verifyemailbtn").html('Verified');
	            $("#verifyemailbtn").removeClass('btn-primary');
	            $("#verifyemailbtn").addClass('btn-success').attr('disabled','');
	            $("#emailid").attr('readonly');
	         }
			   
	         $("#verifyrotpModal").modal('hide');
			} else {
				popup('error', 'OopS!', rslt.msg);
			}

		},
		error: function(err) {
		    $('#loading_ajax').fadeOut();
			Swal.fire({
				icon: "error",
				title: "OOPS..!",
				button: "Close",
				text: 'some internel error occured we are fixing it',
			});
			//  popup('error' , 'OOPS..!' , "some internel error occured we are fixing it");
		}
	});

});
    
    // verify user mobile js code here
    $('#verifymobilebtn').on('click', function() {
        let mobile = $("#mobileno").val();
        if (mobile.length === 10) {
            
     $('#loading_ajax').fadeIn();
	
	$.ajax({
		url: 'backend/MerchantAuthController.php',
		type: 'POST',
		data: {mobile,page:'Register Mobile Verification',sendmobileotp:true},
		success: function(data, status) {
		    $('#loading_ajax').fadeOut();
		    let rslt = JSON.parse(data);
			if (rslt.rescode == 200) {
			$("#rmtext").text(`We have send 6 digit OTP in your Mobile Number`);
			$("#otpdata").val(rslt.data);
			$("#otptype").val(rslt.type);
	        $("#verifyrotpModal").modal('show');
			} else {
				popup('error', 'OopS!', rslt.msg);
			}

		},
		error: function(err) {
		    $('#loading_ajax').fadeOut();
			Swal.fire({
				icon: "error",
				title: "OOPS..!",
				button: "Close",
				text: 'some internel error occured we are fixing it',
			});
			
		}
	});
            
        }else{
    	popup('error', 'OopS!', 'Enter 10 Digit Mobile Number');
        }
    });
    
    
    function validateEmail(email) {
    // Regular expression for validating an email
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailPattern.test(email);
}
    
    // verify user email js code here
    $('#verifyemailbtn').on('click', function() {
     let email = $("#emailid").val();
       
     $('#loading_ajax').fadeIn();
     
     if(validateEmail(email)){
	
	$.ajax({
		url: 'backend/MerchantAuthController.php',
		type: 'POST',
		data: {email,sendotp: true,page:'Register Email Verification'},
		success: function(data, status) {
		    $('#loading_ajax').fadeOut();
		    let rslt = JSON.parse(data);
			if (rslt.rescode == 200) {
			$("#rmtext").text(`We have send 6 digit OTP in your Email Id`);
			$("#otpdata").val(rslt.data);
			$("#otptype").val(rslt.type);
	        $("#verifyrotpModal").modal('show');
			} else {
				popup('error', 'OopS!', rslt.msg);
			}

		},
		error: function(err) {
		    $('#loading_ajax').fadeOut();
			Swal.fire({
				icon: "error",
				title: "OOPS..!",
				button: "Close",
				text: 'some internel error occured we are fixing it',
			});
			
		}
	});
	
     }else{
         $('#loading_ajax').fadeOut();
        popup('error', 'OopS!', 'Invalid Email ! Enter Correct Email id.');
     }

    });
    
    // verify kyc user pan js code here
//     $('#rpanno').on('keyup', function() {
//         let userpanno = $(this).val();
//         if (userpanno.length === 10) {
            
//      $('#loading_ajax').fadeIn();
	
// 	$.ajax({
// 		url: 'backend/MerchantAadharVerify.php',
// 		type: 'POST',
// 		data: {pan_no:userpanno,type:'panVerify'},
// 		success: function(data, status) {
// 		    $('#loading_ajax').fadeOut();
// 		    let rslt = JSON.parse(data);
// 			if (rslt.rescode == 200) {
// 	    	$("#rname").val(rslt.data.name);
// 			$(".panmsg").html(`<small class='text-success'>${rslt.msg}</small>`)
// 			} else {
// 				popup('error', 'OopS!', rslt.msg);
// 			}

// 		},
// 		error: function(err) {
// 		    $('#loading_ajax').fadeOut();
// 			Swal.fire({
// 				icon: "error",
// 				title: "OOPS..!",
// 				button: "Close",
// 				text: 'some internel error occured we are fixing it',
// 			});
			
// 		}
// 	});
            
//         }
//     });
