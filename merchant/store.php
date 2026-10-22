


<?php 



include "header.php";

// include "../pages/dbFunctions.php";
// include "../pages/dbInfo.php";
include "../phnpe/index.php";
?>
<?php

$bbbyteuserid=$_SESSION['user_id'];

$user_token = $_POST["user_token"];
$unitId = $_POST["unitId"];
$roleName = $_POST["roleName"];
$groupValue = $_POST["groupValue"];
$groupId = $_POST["groupId"];


$slq_p = "SELECT * FROM phonepe_tokens where user_token='$user_token'";
        $res_p = getXbyY($slq_p);    
 $device_data = $res_p[0]['device_data'];
$name = $res_p[0]['name']; 
 $refreshToken = $res_p[0]['refreshToken'];
 $phoneNumber = $res_p[0]['phoneNumber']; 
$token = $res_p[0]['token']; 
$userId = $res_p['userId'];
$user_token = $res_p[0]['user_token']; 

 
 if($res_p){
     
$sql = "UPDATE users SET phonepe_connected='Yes',upi_id='$groupId' WHERE user_token='$user_token'";
setXbyY($sql);
$sql = "UPDATE phonepe_tokens SET status='Active' WHERE user_token='$user_token'";
setXbyY($sql);


      $sql = "DELETE FROM store_id WHERE user_token='$user_token'";
if ($conn->query($sql) === TRUE) {}


$sql = "INSERT INTO store_id (user_token, unitId, roleName, groupValue, groupId, user_id)
VALUES ('$user_token', '$unitId', '$roleName', '$groupValue', '$groupId', $bbbyteuserid)";
   
if ($conn->query($sql) === TRUE) {
    
    $fetchuser = $conn->query("SELECT route FROM `users` WHERE user_token='$user_token'")->fetch_assoc();

if($fetchuser["route"] == 0){
    // inactive other merchant
    // $tablesarr = ["bharatpe_tokens","freecharge","googlepay_tokens","merchant","paytm_tokens","hdfc"];
    // $connected_merarr = ["sbi_connected","hdfc_connected","paytm_connected","freecharge_connected","bharatpe_connected","googlepay_connected"];

    // foreach($tablesarr as $tables){
    //     $fetchmerchant = $conn->query("SELECT user_token FROM `$tables` WHERE user_token = '$user_token' AND status = 'Active'");
    //     if($fetchmerchant->num_rows > 0){
    //         $conn->query("UPDATE $tables SET status = 'Deactive' WHERE user_token = '$user_token'");
    //     }
    // }

    // foreach($connected_merarr as $connected){
    //    $conn->query("UPDATE users SET $connected = 'No' WHERE user_token = '$user_token'");
    // }
  
}
    
    // Show SweetAlert2 success message
    echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
    Swal.fire({
        icon: "success",
        title: "Your Store Data & Phonepe Connected Successfully",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings"; // Redirect to "dashboard" when the user clicks the confirm button
        }
    });
</script>';

    exit;
    

}
     
 }
 
 else{
     
       // Show SweetAlert2 error message
       echo '<script src="js/jquery-3.2.1.min.js"></script>';
                            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
echo '<script>
$("#loading_ajax").hide();
    Swal.fire({
        icon: "error",
        title: " Please try again later",
        showConfirmButton: true, // Show the confirm button
        confirmButtonText: "Ok!", // Set text for the confirm button
        allowOutsideClick: false, // Prevent the user from closing the popup by clicking outside
        allowEscapeKey: false // Prevent the user from closing the popup by pressing Escape key
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "upisettings"; // Redirect to "dashboard" when the user clicks the confirm button
        }
    });
</script>';
exit;
     
 }