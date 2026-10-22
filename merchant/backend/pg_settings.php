<?php
session_start();
require_once '../config.php';
 
//  ini_set("display_errors",true);
//  error_reporting(E_ALL);
if(isset($_POST["srno"]) && $_POST["srno"] != ''){
 $mobile = $_POST["srno"];
}else{
$mobile = $_SESSION['username'];
}
   
    
    if(isset($_POST["type"]) && $_POST["type"] == 'updatepgservice'){
        
        $service = mysqli_real_escape_string($conn,$_POST['service']);
        $status = mysqli_real_escape_string($conn,$_POST['status']);
        
        if($service == 'qrcode'){
            $column = "`pg_qrcode` = '$status'";
            $service_name = "QR Code";
        }else if($service == 'intent1'){
            $column = "`pg_intent1` = '$status'";
            $service_name = "Google Pay Intent";
        }else if($service == 'intent2'){
            $column = "`pg_intent2` = '$status'";
            $service_name = "Paytm Intent";
        }else if($service == 'pby'){
            $column = "`pg_pby` = '$status'";
            $service_name = "Powered By";
        }else{
            $column = "`pg_upiidreq` = '$status'";
            $service_name = "UPI Request";
        }
    
$sql = "UPDATE `users` SET $column WHERE `mobile` = '$mobile'";


$run = mysqli_query($conn, $sql);
        
        if($run){
            echo json_encode(["status" => 1 ,  "msg"=>"$service_name method updated successfully !" , "Status"=> true]);
          exit;
        }else{
            echo json_encode(["status" => 3 ,  "msg"=>"Faild to update $service_name method !" , "Status"=> false]);
          exit;
            
        }
    
    }
    
    
    ?>
