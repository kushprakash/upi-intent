<?php
session_start();
include("../config.php");

// ini_set("display_errors" , 1);
// error_reporting(E_ALL);

if(isset($_POST['id'])){

        $id = mysqli_real_escape_string($conn,$_POST['id']);
        $otp = mysqli_real_escape_string($conn,$_POST['otp']);

    
        $query = $conn->query("select * FROM users WHERE id='$id' AND otp ='$otp'");
        
        $row = $query->num_rows;
        if($row!=0){
            
        $conn->query("UPDATE users SET otp_attempts = '3', blocked_until = '' WHERE id = '$id'");
            
            $fetchuser = $query->fetch_assoc();
                // Display a success message to the user
                
            $_SESSION['username'] = $fetchuser["mobile"]; // Set the username in the session
            $_SESSION['user_id']=$fetchuser["id"];
            $_SESSION['login_time'] = time();
            
            if($row['pg_mode'] == 2){
                
    	       echo '2';
            }else if($row['pg_mode'] == 3){
                
    	       echo '3';
            }else{
                
    	       echo '1';
            }
    	       // echo json_encode(["rs_code" => 200 ,  "User_Exist"=>"Yes" , "Status"=> true]);
        }else{
            $msg = "Login Failed";
            // echo json_encode(array("rs_code"=> "404" , "User_Exist"=>"No" , "Status"=> false));
            echo '0';
    	}
}



?> 