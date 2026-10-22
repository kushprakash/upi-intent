

function popup(status , title , msg){
    Swal.fire({
      icon: status,
      title: title,
      text: msg,
    });
}


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
    


$("#login_form").submit(function(e){
    
    e.preventDefault();
    $("#loading_ajax").show();
   let mobile = $('#mobile').val();
   let password = $('#passwordlogin').val();
        $.post("backend/login.php",
            {
                mobile: mobile,
                password: password
            }, 
    function(data, status){
         $("#loading_ajax").hide();   
        // console.log("Data: " + data + "\nStatus: " + status);
        let rslt = JSON.parse(data);
        if(rslt.status == 1){
            
            $('#useridmodal').val(rslt.userid);
            $('#loginformbox').hide();
            $('#otpformbox').show();
            startTimer(30, timerElement);
        }else if(rslt.status == 11){
             Swal.fire({
                icon: "success",
                title: "Hurray!",
                button: "Okay",
                text: 'Welcome. You are logged in.',
                }) .then(function(){ 
                location.replace("dashboard?login=access&user=mrcnt");
                }
                );
        }else if(rslt.status == 10){
             Swal.fire({
                icon: "success",
                title: "Hurray!",
                button: "Okay",
                text: 'Welcome. You are logged in.',
                }) .then(function(){ 
                location.replace("dashboard?login=access&user=mrcnt");
                }
                );
        }else{
           Swal.fire({
            icon: "error",
            title: "Oops!",
            button: "Close",
            text: rslt.msg,
            });
        }
       
                
    });
    
    
    
 });
 
 
 $("#loginotpform").submit(function(e){
    
    e.preventDefault();
   $("#loading_ajax").show();
   let id = $('#useridmodal').val();
   let otp = '';
        inputs.forEach(input => {
            otp += input.value;
        });
   
     $.post("backend/loginotp.php",
        {
            id: id,
            otp: otp,
        }, 
    function(data, status){
           $("#loading_ajax").hide(); 
        console.log("Data: " + data + "\nStatus: " + status);
        
        if(data == 1){
            
      
             $('#otperr').val('');
            
                Swal.fire({
                icon: "success",
                title: "Hurray!",
                button: "Okay",
                text: 'Welcome. You are logged in.',
                }) .then(function(){ 
                location.replace("dashboard?login=access&user=mrcnt");
                }
                );
            
        }else if(data == 2){
            Swal.fire({
                icon: "success",
                title: "Hurray!",
                button: "Okay",
                text: 'Welcome. You are logged in.',
                }) .then(function(){ 
                location.replace("../imbpro/dashboard");
                }
                );
        }else if(data == 3){
            Swal.fire({
                icon: "success",
                title: "Hurray!",
                button: "Okay",
                text: 'Welcome. You are logged in.',
                }) .then(function(){ 
                location.replace("../imbpg/dashboard?login=access&user=mrcnt");
                }
                );
        }else{
         Swal.fire({
                icon: "error",
                title: "Oops!",
                button: "Close",
                text: 'OTP Is Worong. Try again Later',
                });
            
            
        }
        
    });
    
    
    
 });
 
function verify(msg){
     $("#login_form").hide();
    $("#otp_area").show();
    $("#otp_msg").text(msg);
}

