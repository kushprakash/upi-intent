<?php 
include "header.php";
// include "../pages/dbFunctions.php";
// include "../pages/dbInfo.php";
include "../phnpe/index.php";
?>
<?php
if(isset($_POST['verifyotp'])){
    
    // Use $_POST to retrieve data

$otp = sanitizeInput($_POST["otp"]);
$numbero = sanitizeInput($_POST["no"]);
$upi = sanitizeInput($_POST["upi"]);
$otp_toekn = sanitizeInput($_POST["otp_toekn"]);
$device_data = sanitizeInput($_POST["device_data"]);



$user_token = sanitizeInput($_POST['user_token']);
        // Now $user_token contains the value from the hidden input field

     ##########otpverfy####################3
        $otpferfyy=sentotp("2",$numbero,$otp,$otp_toekn,$device_data);
      // echo $otpferfyy;
       
       
$json0=json_decode($otpferfyy,1);
              //db save value
 $message=$json0["message"];
 $messages=$json0["messages"];
 $phoneNumber=$json0["number"];
 $userId=$json0["userId"];
 $token=$json0["token"];
$refreshToken=$json0["refreshToken"];
$name=$json0["name"];
$device_datar=$json0["device_data"];
//save db end//
  $b=json_decode($otpferfyy,true);
   
    $unitId = $b['userGroupNamespace']['All']['merchant_data']['displayName'];
    $groupValue = $b['userGroupNamespace']['All']['merchant_data']['merchantId'];
    $groupId = $b['userGroupNamespace']['All']['upi_ids'][0].'@ybl';
    $roleName = 'Admin';
   
 // echo $otpferfyy;
######################################3

if($message=="success"){
$sql = "UPDATE users SET upi_id='$upi' WHERE user_token='$user_token'";
setXbyY($sql); 


$sql = "DELETE FROM phonepe_tokens WHERE user_token='$user_token'";
if ($conn->query($sql) === TRUE) {}

$bbbyteuserid=$_SESSION['user_id'];

$sql = "INSERT INTO phonepe_tokens (user_token, phoneNumber, userId, token, refreshToken, name, device_data, user_id)
VALUES ('$user_token', '$phoneNumber', '$userId', '$token', '$refreshToken', '$name', '$device_data', $bbbyteuserid)";
if ($conn->query($sql) === TRUE) {}


echo '<center style="margin-top:10%;"><h2 style="margin-bottom:3%;border-bottom:1px solid #eee;">Select Your Store</h2>';

echo"<center><br><form action='store' method='POST'>
<input  type='hidden' name='unitId' value='$unitId'>
<input  type='hidden' name='roleName' value='$roleName'>
<input  type='hidden' name='groupValue' value='$groupValue'>
<input  type='hidden' name='groupId' value='$groupId'>
<input  type='hidden' name='user_token' value='$user_token'>

<button  id='$unitId' name='$unitId' class='btn btn-primary mb-2'>$unitId</button>
</form>
</center>
<br><br>

";


}
    
    
    
    
    
    else {
    
 // Show SweetAlert2 error message
 echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
    Swal.fire({
        icon: "error",
        title: "Incorrect OTP, Please try again later",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings"; // Redirect to "upisettings" when the user clicks the confirm button
        }
    });
</script>';
exit;   
    
    }    
    
    echo "<script src='js/jquery-3.2.1.min.js'></script>
    <script>
    $('#loading_ajax').hide();
    </script>
    </body>
    </html>";
    
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        color: #343a40;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        padding: 10px 20px;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }
</style>

