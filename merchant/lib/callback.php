<?php
session_start();
require_once('../config.php');
require_once('config.php');

// Agar cookie set nahi hai to error avoid karne ke liye check
if (!isset($_COOKIE['order_id_cookie'])) {
    die("Order ID Cookie missing. Please try again.");
}

$orderid = $_COOKIE['order_id_cookie'];

// API endpoint URL
$url = "https://" . $_SERVER["SERVER_NAME"] . "/api/check-order-status";

// POST data
$postData = array(
    "user_token" => $token,
    "order_id" => $orderid
);

// Initialize cURL session
$ch = curl_init($url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

// Execute cURL session and get the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
    exit;
}

// Close cURL session
curl_close($ch);

// Decode the JSON response
$responseData = json_decode($response, true);

// Check if the API call was successful
if ($responseData["status"] === "COMPLETED") {
    
    // Cookie se Plan ID nikalo
    $planid = isset($_COOKIE['planid_cookie']) ? $_COOKIE['planid_cookie'] : 1;
    $email = $_SESSION['username'];

    // --- IMPORTANT CHANGE: Get Days from Database ---
    // Hum seedha DB se pooch rahe hain ki is plan ke kitne din hain
    
    $daysToAdd = 28; // Default value (agar DB me kuch gadbad ho)

    $plan_query = mysqli_query($conn, "SELECT duration_days FROM subscription_plans WHERE plan_id = '$planid'");
    
    if($plan_query && mysqli_num_rows($plan_query) > 0){
        $plan_data = mysqli_fetch_assoc($plan_query);
        // Jo bhi SQL me likha hoga (1, 30, 365) wahi yahan aayega
        $daysToAdd = (int)$plan_data['duration_days']; 
    }
    // ------------------------------------------------

    // Fetch current expiry date from the database
    $query = "SELECT expiry, sponser_by FROM users WHERE mobile = '$email'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $currentExpiry = $row['expiry'];
    $sponser_id = $row['sponser_by'];
    
    // Fetch current expiry date from the database for Sponsor
    $sponserquery = "SELECT expiry FROM users WHERE sponser_id = '$sponser_id'";
    $sponserresult = mysqli_query($conn, $sponserquery);
    $sponserdata = mysqli_fetch_assoc($sponserresult);
    $sponserExpiry = $sponserdata['expiry'];
    
    // Convert expiry date and current date to timestamps
    $currentExpiryTimestamp = strtotime($currentExpiry);
    $currentDateTimestamp = strtotime(date('Y-m-d'));

    // Calculate new expiry date for User
    if ($currentExpiryTimestamp < $currentDateTimestamp) {
        // If expiry date is in the past, add days from current date
        $newExpiry = date('Y-m-d', strtotime("+$daysToAdd days", $currentDateTimestamp));
    } else {
        // If expiry date is in the future, add days to existing expiry date
        $newExpiry = date('Y-m-d', strtotime("+$daysToAdd days", $currentExpiryTimestamp));
    }

    // Update the expiry date in the database
    $sql = "UPDATE users SET expiry = '$newExpiry', plan_id='$planid' WHERE mobile = '$email'";
    $updateResult = mysqli_query($conn, $sql);

    if ($updateResult) {
        
        // Sponsor Logic (Commission Days)
        if(mysqli_num_rows($sponserresult) > 0){
            
            // Sponsor ko plan ka 1/3 hissa milta hai
            $sponserDateadd = floor($daysToAdd / 3);
            
            // Sponsor ko tabhi update karo agar days 0 se jyada hon
            if ($sponserDateadd > 0) {
                // Convert expiry date and current date to timestamps
                $currentExpirySponser = strtotime($sponserExpiry);
            
                // Calculate new expiry date for Sponsor
                if ($currentExpirySponser < $currentDateTimestamp) {
                    $newsponserExpiry = date('Y-m-d', strtotime("+$sponserDateadd days", $currentDateTimestamp));
                } else {
                    $newsponserExpiry = date('Y-m-d', strtotime("+$sponserDateadd days", $currentExpirySponser));
                }

                // Update the expiry date in the database
                $conn->query("UPDATE users SET expiry = '$newsponserExpiry' WHERE sponser_id = '$sponser_id'");
            }
        }
        
        header("Location: https://" . $_SERVER["SERVER_NAME"] . "/merchant/dashboard");
        exit;
    } else {
        // Redirect to subscription page on error
        header("Location: https://" . $_SERVER["SERVER_NAME"] . "/merchant/subscription");
        exit;
    }
} else {
    // API call failed
    $errorMessage = isset($responseData["message"]) ? $responseData["message"] : "Unknown Error";
    echo "API Error: $errorMessage";
}
?>