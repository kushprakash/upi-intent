<?php

include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";
include "../merchant/config.php";

// ini_set('display_errors', 1);
// error_reporting(E_ALL);


function shorten_url($longUrl) {
    $apiUrl = $site_url.'/link/do.php';
    $data = json_encode(['long_url' => $longUrl]);

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        curl_close($ch);
        return false;
    }

    curl_close($ch);
    $responseData = json_decode($response, true);

    if (isset($responseData['short_url'])) {
        return $responseData['short_url'];
    } else {
        return false;
    }
}

function RandomNumber($length) {
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= mt_rand(0, 9);
    }
    return $str;
}

function GenRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateUniqueToken() {
    $token = time() . bin2hex(random_bytes(16)) . rand(1, 50);
    return hash('sha256', $token);
}

function generateNumericOTP($n) { 
   
    $generator = "1357902468"; 
    $result = ""; 
  
    for ($i = 1; $i <= $n; $i++) { 
        $result .= substr($generator, (rand()%(strlen($generator))), 1); 
    } 
  
    return $result; 
} 

function order_txn_id($fs=''){
 return $fs.date("ymdHis").generateNumericOTP(8);   
}


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    header('Content-Type: application/json');
    $json = array("status" => "Access Denied", "msg" => "Unauthorized Access");
    print_r(json_encode($json, TRUE));
    exit(); // Stop further script execution
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json');

    $customer_mobile = $_POST['customer_mobile'];
    $user_token = $_POST["user_token"];
    $amount = $_POST["amount"];
    $order_id = $_POST["order_id"];
    $redirect_url = $_POST["redirect_url"];
    $remark1 = $_POST["remark1"];
    $remark2 = $_POST["remark2"];

    $byteorderid = "PG" . rand(111, 999) . time();

    if ($amount == '') {
        echo json_encode(array("status" => "FAILURE", "msg" => "Please Enter Amount Data"));
        exit;
    } else {
        if ($order_id == '') {
            echo json_encode(array("status" => "false", "message" => "Please Enter Order_id Data"));
            exit;
        } else {
            if ($user_token == '') {
                echo json_encode(array("status" => "false", "message" => "Please Enter User_token Data"));
                exit;
            }

            // Source Verification (IP or Domain) using centralized function
            $source_check = validate_merchant_source($user_token);
            if (!$source_check['status']) {
                echo json_encode(['status' => false, 'message' => $source_check['message']]);
                exit;
            }
            
            // New validation for order_id
        $check_order_id_query = "SELECT * FROM orders WHERE order_id='$order_id'";
        $existing_order_result = getXbyY($check_order_id_query);

        if (!empty($existing_order_result)) {
            echo json_encode(array("status" => "false", "message" => "Order ID already exists for this user"));
            exit;
        }

            else {

                $slq_pbbytgetmode = "SELECT * FROM users where user_token='$user_token'";
                $res_pslq_pbbytmode = getXbyY($slq_pbbytgetmode);
                
                
                $twenty_minutes_ago = date('Y-m-d H:i:s', strtotime('-8 minutes'));
                $current_time = date('Y-m-d H:i:s');
                $slq_getuserp_order = "SELECT COUNT(id) as count FROM orders WHERE (create_date BETWEEN '$twenty_minutes_ago' AND '$current_time') AND user_token='$user_token' AND status = 'PENDING'";
                $res_getuserp_order = getXbyY($slq_getuserp_order);
                
                // Limit Removed as per user request
                // if($res_getuserp_order[0]["count"] >= 25){ ... }
                
                $slq_pbbyt = "SELECT * FROM users where user_token='$user_token'";
                $res_pslq_pbbyt = getXbyY($slq_pbbyt);
                $rmode = 1;
               
                
                $bydb_unq_route=$res_pslq_pbbyt[0]['route'];
                $bydb_unq_user_id=$res_pslq_pbbyt[0]['id'];
                $bydb_order_hdfc_conn = $res_pslq_pbbyt[0]['hdfc_connected'];
                $bydb_order_phonepe_conn = $res_pslq_pbbyt[0]['phonepe_connected'];
                $bydb_order_paytm_conn=$res_pslq_pbbyt[0]['paytm_connected'];
                $bydb_order_freecharge_conn=$res_pslq_pbbyt[0]['freecharge_connected'];
                $bydb_order_mobikwik_conn=$res_pslq_pbbyt[0]['mobikwik_connected'];
                $bydb_order_sbi_conn=$res_pslq_pbbyt[0]['sbi_connected'];
                $bydb_order_bharatpe_conn=$res_pslq_pbbyt[0]['bharatpe_connected'];
                $bydb_order_googlepay_conn=$res_pslq_pbbyt[0]['googlepay_connected'];
                $bydb_order_quintuspay_conn=$res_pslq_pbbyt[0]['quintuspay_connected'];
                $bydb_order_amazonpay_conn=$res_pslq_pbbyt[0]['amazonpay_connected'];
                
                
                // PRIMARY MERCHANT LOGIC
                $primary_type = $res_pslq_pbbyt[0]['primary_merchant_type'] ?? null;
                $primary_id = $res_pslq_pbbyt[0]['primary_merchant_id'] ?? null;
                
                if($bydb_unq_route == 0){
                    
                    // If Primary Merchant is Set, Override the Default Order attempt
                    if($primary_type && $primary_id) {
                         // Logic: If Primary is set, we forcefully check THAT type first.
                         // But we must respect the "Connected=Yes" status.
                         
                         $override_key = strtolower($primary_type) . "_connected";
                         // Map some types if names differ (e.g. SBI Merchant -> sbi_connected)
                         if($primary_type == 'SBI' || $primary_type == 'SBI Merchant') $override_key = 'sbi_connected';
                         if($primary_type == 'Googlepay') $override_key = 'googlepay_connected';
                         if($primary_type == 'Amazonpay') $override_key = 'amazonpay_connected';
                         if($primary_type == 'QuintusPay') $override_key = 'quintuspay_connected';
                         if($primary_type == 'PhonePe') $override_key = 'phonepe_connected';
                         if($primary_type == 'Paytm') $override_key = 'paytm_connected';
                         if($primary_type == 'HDFC') $override_key = 'hdfc_connected';
                         if($primary_type == 'Freecharge') $override_key = 'freecharge_connected';
                         if($primary_type == 'MOBIKWIK') $override_key = 'mobikwik_connected';
                         if($primary_type == 'Bharatpe') $override_key = 'bharatpe_connected';


                         // If that primary type is connected for this user
                         if (isset($res_pslq_pbbyt[0][$override_key]) && $res_pslq_pbbyt[0][$override_key] == 'Yes') {
                             // We set variables to FORCE this block to run or just run it directly.
                             // Strategy: We can re-order the checks, OR we can just set a flag to skip others?
                             // A Better Strategy for this specific code structure (which is a cascade of if...elseif):
                             // We can't easy re-order cascade without major refactor.
                             // BUT we can use a "GOTO" style or just execute it if we extract logic.
                             // Since we can't extract logic easily here, we will use a Trick:
                             // We will Modify the Checks below to prioritize this.
                             
                             // Actually, the cleanest way in this legacy code is:
                             // Check if Primary is Valid. If so, EXECUTE it and EXIT.
                             
                             if ($override_key == 'hdfc_connected') {
                                 // Execute HDFC Logic specific for Primary ID
                                 $slq_hdfc = "SELECT id FROM hdfc where user_token='$user_token' AND id='$primary_id'";
                                 $res_hdfc = getXbyY($slq_hdfc);
                                 if(!empty($res_hdfc)) {
                                     // COPY HDFC BLOCK LOGIC HERE
                                     // ... (This is duplication, but safer than refactoring the whole file)
                                      $today = date("Y-m-d");
                                      $expire_date = $res_pslq_pbbyt[0]["expiry"];
                                      if ($expire_date >= $today) {
                                          $link_token = generateUniqueToken();
                                          $cxrtoday = date("Y-m-d H:i:s");
                                          $mid = $res_hdfc[0]['id'];
                                          $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                                          setXbyY($sql_insert_link);
                                          $payment_link = $site_url."/payment/instant-pay/" . $link_token;
                                          $gateway_txn1 = rand(1000000000, 9999999999);
                                          $method = "HDFC";
                                          $currentTimestamp = date("Y-m-d H:i:s");
                                          $hdfc_txnid = '';
                                          $diss = RandomNumber(18);
                                          $sql = "INSERT INTO orders (gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, method, HDFC_TXNID, upiLink, description, create_date, remark1, remark2, user_id, merchant_id,user_mode, byteTransactionId) VALUES ('$gateway_txn1', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', '$method', '$hdfc_txnid', '$upiLink', '$diss', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode', '$byteorderid')";
                                          setXbyY($sql);
                                          echo json_encode(["status" => true,"message" => "Order Created Successfully","result" => ["orderId" => $order_id,"payment_url" => $payment_link]]);
                                          exit();
                                      }
                                 }
                             }
                             // Repeat for other gateways... or simpler:
                             // Just Re-assign $bydb_order_X_conn to "No" for all EXCEPT the primary!
                             // This is the GENIUS Hack.
                             // If HDFC is primary, set all others to "No". Then the existing Cascade will pick HDFC Only.
                             
                             $bydb_order_hdfc_conn = ($override_key == 'hdfc_connected') ? 'Yes' : 'No';
                             $bydb_order_phonepe_conn = ($override_key == 'phonepe_connected') ? 'Yes' : 'No';
                             $bydb_order_paytm_conn = ($override_key == 'paytm_connected') ? 'Yes' : 'No';
                             $bydb_order_freecharge_conn = ($override_key == 'freecharge_connected') ? 'Yes' : 'No';
                             $bydb_order_mobikwik_conn = ($override_key == 'mobikwik_connected') ? 'Yes' : 'No';
                             $bydb_order_sbi_conn = ($override_key == 'sbi_connected') ? 'Yes' : 'No';
                             $bydb_order_bharatpe_conn = ($override_key == 'bharatpe_connected') ? 'Yes' : 'No';
                             $bydb_order_googlepay_conn = ($override_key == 'googlepay_connected') ? 'Yes' : 'No';
                             $bydb_order_quintuspay_conn = ($override_key == 'quintuspay_connected') ? 'Yes' : 'No';
                             $bydb_order_amazonpay_conn = ($override_key == 'amazonpay_connected') ? 'Yes' : 'No';

                         }
                         
                    } // End Primary Override

                if ($bydb_order_hdfc_conn == "Yes") {
                    
                    // if ($amount > 2000) {
                    //     echo json_encode([
                    //         "status" => false,
                    //         "message" => "In HDFC MAxiumum 2000 Allowed",
                    //     ]);
                    //     exit();
                    // }

                    $today = date("Y-m-d");
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]["expiry"];

                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");
                        
                    $slq_hdfc = "SELECT id FROM hdfc where user_token='$user_token'";
                    $res_hdfc = getXbyY($slq_hdfc);
                    $mid = $res_hdfc[0]['id'];

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment/instant-pay/" .
                            $link_token;
                        $gateway_txn1 = rand(1000000000, 9999999999);

                        $method = "HDFC";
                        $currentTimestamp = date("Y-m-d H:i:s");
                        $mTxnid = "";
                        $hdfc_txnid = '';
                        $diss = RandomNumber(18);
                        $sql = "INSERT INTO orders (gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, method, HDFC_TXNID, upiLink, description, create_date, remark1, remark2, user_id, merchant_id,user_mode, byteTransactionId)
    VALUES ('$gateway_txn1', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', '$method', '$hdfc_txnid', '$upiLink', '$diss', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode', '$byteorderid')";

                        setXbyY($sql);

                        echo json_encode([
                            "status" => true,
                            "message" => "Order Created Successfully",
                            "result" => [
                                "orderId" => $order_id,
                                "payment_url" => $payment_link,
                            ],
                        ]);
                        exit();
                    } else {
                        echo json_encode([
                            "status" => false,
                            "message" => "Your Plan Expired Please Renew",
                        ]);
                        exit();
                    }
                }// <-- Close the HDFC block here

                
                //phonepe else if logic start
                elseif ($bydb_order_phonepe_conn == "Yes") {
                    $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];
                    
                    $slq_phonepe = "SELECT sl FROM phonepe_tokens where user_token='$user_token'";
                    $res_phonepe = getXbyY($slq_phonepe); 
                    
                     $mid = $res_phonepe[0]['sl'];

                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment2/instant-pay/" . $link_token;

                        $order_id2 = base64_encode($order_id);
                        $gateway_txn = uniqid();
                        $currentTimestamp = date('Y-m-d H:i:s');

                        $sql = "INSERT INTO orders (gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, merchant_id,user_mode)
VALUES ('$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'PhonePe', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode')";

setXbyY($sql);


            $upi_id = $res_p[0]['upi_id'];
             $unitId = $res_p[0]['company'];
            $asdasd23="TXN".rand(111,999).time().rand(1,100);
            $bhimupiintent="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tr=$byteorderid";
            
            $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$byteorderid&tr=$byteorderid&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
                    } else {
                        echo json_encode(array("status" => "false", "message" => "Your Plan Expired Please Renew"));
                        exit;
                    }
                } // <-- Close the phonepe block here
                
                //paytm else if logic start
                elseif ($bydb_order_paytm_conn == "Yes") {
                    $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];
                    
      $slq_paytm = "SELECT id,Upiid FROM paytm_tokens where user_token='$user_token'";
        $res_paytm = getXbyY($slq_paytm); 
        
        $mid = $res_paytm[0]['id'];
        
                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment3/instant-pay/" . $link_token;

                        $order_id2 = base64_encode($order_id);
                        $gateway_txn = uniqid();
                        $currentTimestamp = date('Y-m-d H:i:s');
                        $bytetxn_ref_id = GenRandomString().time();	
                       $sql = "INSERT INTO orders (paytm_txn_ref, gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, merchant_id,user_mode)
VALUES ('$bytetxn_ref_id', '$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'Paytm', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode')";

setXbyY($sql);

        
         $upi_id = $res_paytm[0]['Upiid'];
             $unitId = $res_p[0]['company'];
            $asdasd23="ARC".rand(111,999).time().rand(1,100);
            
            $bhimupiintent="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tr=$bytetxn_ref_id";
            
            $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$bytetxn_ref_id&tr=$bytetxn_ref_id&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
                    } else {
                        echo json_encode(array("status" => "false", "message" => "Your Plan Expired Please Renew"));
                        exit;
                    }
                }
                elseif ($bydb_order_freecharge_conn == "Yes") {
                    $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];
                    
        $freecharge = "SELECT id,upi_id FROM freecharge where user_token='$user_token'";
        $freechargedata = getXbyY($freecharge); 
        $mid = $freechargedata[0]['id'];

                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment7/instant-pay/" . $link_token;
                        $googletxnnote = "ATC" . substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 5) . time();

                        $order_id2 = base64_encode($order_id);
                        $gateway_txn = uniqid();
                        $currentTimestamp = date('Y-m-d H:i:s');
                        $bytetxn_ref_id = GenRandomString().time();	
                       $sql = "INSERT INTO orders (gateway_txn, paytm_txn_ref, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, merchant_id,user_mode)
VALUES ('$gateway_txn', '$googletxnnote','$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'Freecharge', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode')";

setXbyY($sql);

 $upi_id = $freechargedata[0]['upi_id'];
 
             $unitId = $res_p[0]['company'];
            $asdasd23="TXN".rand(111,999).time().rand(1,100);
            $bhimupiintent = "upi://pay?pa=$upi_id&am=$amount&tid=$googletxnnote&pn=$unitId&tn=$googletxnnote&tr=$asdasd23";
            
            $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&tid=$googletxnnote&cu=INR&tn=$googletxnnote&tr=$googletxnnote&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
                    } else {
                        echo json_encode(array("status" => "false", "message" => "Your Plan Expired Please Renew"));
                        exit;
                    }
                } // <-- Close the freecharge block here
                
                 //Mobikwik Merchant
    elseif ($bydb_order_mobikwik_conn == "Yes") {
        $today = date("Y-m-d");
        $slq_p = "SELECT * FROM users where user_token='$user_token'";
        $res_p = getXbyY($slq_p);
        $expire_date = $res_p[0]["expiry"];

        if ($expire_date >= $today) {
            // Generate a unique payment link token
            $link_token = generateUniqueToken();

            $cxrtoday = date("Y-m-d H:i:s");

            // Insert the link_token into the payment_links table with the current date and time
            $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
            setXbyY($sql_insert_link);

            // Construct the payment link
            $payment_link = $site_url."/payment8/instant-pay/" . $link_token;

            $gateway_txn = uniqid();
            $googletxnnote = "ATC" . substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 5) . time();
            $currentTimestamp = date("Y-m-d H:i:s");
            $sql = "INSERT INTO orders (gateway_txn,paytm_txn_ref, amount, order_id, status, user_token, utr, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id)
VALUES ('$gateway_txn', '$googletxnnote' ,'$amount', '$order_id', 'PENDING', '$user_token', '', '$customer_mobile', '$redirect_url', 'MOBIKWIK', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id')";

            setXbyY($sql);
 http_response_code(201); // Created
            echo json_encode([
                "status" => true,
                "message" => "Order Created Successfully",
                "result" => [
                    "orderId" => $order_id,
                    "payment_url" => $payment_link,
                ],
            ]);
            exit();
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => false,
                "message" => "Your Plan Expired Please Renew",
            ]);
            exit();
        }
                } // <-- Close the freecharge block here
                
                 //Amazonpay Merchant
    elseif ($bydb_order_amazonpay_conn == "Yes") {
        $today = date("Y-m-d");
        $slq_p = "SELECT * FROM users where user_token='$user_token'";
        $res_p = getXbyY($slq_p);
        $expire_date = $res_p[0]["expiry"];
        
       $slq_amz = "SELECT id,upi_id FROM amazon_pay where user_token='$user_token'";
        $res_amz = getXbyY($slq_amz); 
        $mid = $res_amz[0]['id'];

        if ($expire_date >= $today) {
            // Generate a unique payment link token
            $link_token = generateUniqueToken();

            $cxrtoday = date("Y-m-d H:i:s");

            // Insert the link_token into the payment_links table with the current date and time
            $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
            setXbyY($sql_insert_link);

            // Construct the payment link
            $payment_link = $site_url."/payment9/instant-pay/" . $link_token;

            $gateway_txn = uniqid();
            $googletxnnote = "ARC".rand(111,999).time().rand(1,100);
            $currentTimestamp = date("Y-m-d H:i:s");
            $sql = "INSERT INTO orders (gateway_txn,paytm_txn_ref, amount, order_id, status, user_token, utr, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id,merchant_id)
VALUES ('$gateway_txn', '$googletxnnote' ,'$amount', '$order_id', 'PENDING', '$user_token', '', '$customer_mobile', '$redirect_url', 'Amazonpay', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id','$mid')";

            setXbyY($sql);
            
      $upi_id = $res_amz[0]['upi_id'];
      
             $unitId = $res_p[0]['company'];
            $asdasd23="TXN".rand(111,999).time().rand(1,100);
            $bhimupiintent="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tid=$googletxnnote";
            
    $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$googletxnnote&tid=$googletxnnote&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => false,
                "message" => "Your Plan Expired Please Renew",
            ]);
            exit();
        }
    }// <-- Close the amazon pay

                
                
                //sbi else if logic start
                  elseif ($bydb_order_sbi_conn == "Yes") {
                    $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];
                    
                $slq_sbi = "SELECT merchant_id,merchant_upi FROM merchant where user_token='$user_token'";
                $res_sbi = getXbyY($slq_sbi);  
                $mid = $res_sbi[0]['merchant_id'];

                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();
                        $bank_orderid = order_txn_id("IT");
                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment6/instant-pay/" . $link_token;

                        $order_id2 = base64_encode($order_id);
                        $gateway_txn = uniqid();
                        $currentTimestamp = date('Y-m-d H:i:s');
                        $bytetxn_ref_id = GenRandomString().time();	
                       $sql = "INSERT INTO orders (bank_orderid, gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, merchant_id,user_mode)
VALUES ('$bank_orderid', '$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'SBI', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode')";

setXbyY($sql);

         $upi_id = $res_p[0]['merchant_upi'];
 
             $unitId = $res_p[0]['company'];
            $asdasd23="TXN".rand(111,999).time().rand(1,100);
            $bhimupiintent="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tr=$bank_orderid";
            
            $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$bank_orderid&tr=$bank_orderid&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
                    } else {
                        echo json_encode(array("status" => "false", "message" => "Your Plan Expired Please Renew"));
                        exit;
                    }
                } // <-- Close the paytm block here
                
                
                  //Bharatpe else if logic start
                elseif ($bydb_order_bharatpe_conn == "Yes") {
                    $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];

                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment4/instant-pay/" . $link_token;

                        
                        $gateway_txn = time().rand(11111,99999);
                        $currentTimestamp = date('Y-m-d H:i:s');
                        $sql = "INSERT INTO orders (gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, user_mode)
VALUES ('$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'Bharatpe', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$rmode')";

setXbyY($sql);



                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link)));
                        exit;
                    } else {
                        echo json_encode(array("status" => "false", "message" => "Your Plan Expired Please Renew"));
                        exit;
                    }
                } // <-- Close the Bharatpe block here
                
                 //GooglePay else if logic start
                elseif ($bydb_order_googlepay_conn == "Yes") {
                    $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];
                    
      $slq_paytm = "SELECT id,Upiid FROM gpay_tokens where user_token='$user_token'";
        $res_paytm = getXbyY($slq_paytm); 
        
        $mid = $res_paytm[0]['id'];
        
                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment5/instant-pay/" . $link_token;

                        $order_id2 = base64_encode($order_id);
                        $gateway_txn = uniqid();
                        $currentTimestamp = date('Y-m-d H:i:s');
                        $bytetxn_ref_id = GenRandomString().time();	
                       $sql = "INSERT INTO orders (paytm_txn_ref, gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, merchant_id,user_mode)
VALUES ('$bytetxn_ref_id', '$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'Googlepay', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode')";

setXbyY($sql);

        
         $upi_id = $res_paytm[0]['Upiid'];
             $unitId = $res_p[0]['company'];
            $asdasd23="ARC".rand(111,999).time().rand(1,100);
            
            $bhimupiintent="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tr=$bytetxn_ref_id";
            
            $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$bytetxn_ref_id&tr=$bytetxn_ref_id&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
                  } 
                } // <-- Close the GooglePay block here
                
                
                elseif ($bydb_order_quintuspay_conn == "Yes") {
                    $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];
                    
      $slq_paytm = "SELECT id,Upiid FROM quintus_tokens where user_token='$user_token'";
        $res_paytm = getXbyY($slq_paytm); 
        
        $mid = $res_paytm[0]['id'];
        
                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment10/instant-pay/" . $link_token;

                        $order_id2 = base64_encode($order_id);
                        $gateway_txn = uniqid();
                        $currentTimestamp = date('Y-m-d H:i:s');
                        $bytetxn_ref_id = GenRandomString().time();	
                       $sql = "INSERT INTO orders (paytm_txn_ref, gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, merchant_id,user_mode)
VALUES ('$bytetxn_ref_id', '$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'Quintuspay', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode')";

setXbyY($sql);

        
         $upi_id = $res_paytm[0]['Upiid'];
             $unitId = $res_p[0]['company'];
            $asdasd23="ARC".rand(111,999).time().rand(1,100);
            
            $bhimupiintent="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tr=$bytetxn_ref_id";
            
            $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$bytetxn_ref_id&tr=$bytetxn_ref_id&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
                  }
                } // <-- Close the GooglePay block here
                
                
                elseif ($bydb_order_hdfc_conn == "No" || $bydb_order_phonepe_conn == "No" || $bydb_order_paytm_conn == "No" || $bydb_order_bharatpe_conn == "No" || $bydb_order_googlepay_conn == "No") {
                         echo json_encode(array("status" => "false", "message" => "Merchant Not Linked"));
                         exit;
                    }
                    
                }else{
                    
                       $columns = [
    "hdfc_connected" => $bydb_order_hdfc_conn,
    "phonepe_connected" => $bydb_order_phonepe_conn,
    "paytm_connected" => $bydb_order_paytm_conn,
    "bharatpe_connected" => $bydb_order_bharatpe_conn,
    "googlepay_connected" => $bydb_order_googlepay_conn,
    "quintus_connected" => $bydb_order_quintuspay_conn,
    "freecharge_connected" => $bydb_order_freecharge_conn,
    "sbi_connected" => $bydb_order_sbi_conn,
    "mobikwik_connected" => $bydb_order_mobikwik_conn,
    "amazonpay_connected" => $bydb_order_amazonpay_conn,
];

$yes_columns = array_filter($columns, function ($value) {
    return $value === "Yes";
});


if (!empty($yes_columns)) {

    // FAIR ROUTING LOGIC: Build a weighted pool based on actual account counts
    $weighted_pool = [];

    // 1. HDFC
    if (isset($yes_columns['hdfc_connected'])) {
        $check = getXbyY("SELECT count(*) as total FROM hdfc WHERE user_token='$user_token' AND status='Active'");
        $count = $check[0]['total'] ?? 0;
        for ($i = 0; $i < $count; $i++) { $weighted_pool[] = 'hdfc_connected'; }
    }
    // 2. PhonePe
    if (isset($yes_columns['phonepe_connected'])) {
        $check = getXbyY("SELECT count(*) as total FROM phonepe_tokens WHERE user_token='$user_token'"); // Assuming all tokens in this table are valid/active or add status check if exists
        $count = $check[0]['total'] ?? 0;
        for ($i = 0; $i < $count; $i++) { $weighted_pool[] = 'phonepe_connected'; }
    }
    // 3. Paytm
    if (isset($yes_columns['paytm_connected'])) {
        $check = getXbyY("SELECT count(*) as total FROM paytm_tokens WHERE user_token='$user_token'");
        $count = $check[0]['total'] ?? 0;
        for ($i = 0; $i < $count; $i++) { $weighted_pool[] = 'paytm_connected'; }
    }
    // 4. BharatPe
    if (isset($yes_columns['bharatpe_connected'])) {
        $check = getXbyY("SELECT count(*) as total FROM bharatpe_tokens WHERE user_token='$user_token' AND status='Active'");
        $count = $check[0]['total'] ?? 0;
        for ($i = 0; $i < $count; $i++) { $weighted_pool[] = 'bharatpe_connected'; }
    }
    // 5. GooglePay
    if (isset($yes_columns['googlepay_connected'])) {
        $check = getXbyY("SELECT count(*) as total FROM gpay_tokens WHERE user_token='$user_token' AND status='Active'");
        $count = $check[0]['total'] ?? 0;
        for ($i = 0; $i < $count; $i++) { $weighted_pool[] = 'googlepay_connected'; }
    }
    // 6. QuintusPay
    if (isset($yes_columns['quintus_connected'])) {
        $check = getXbyY("SELECT count(*) as total FROM quintus_tokens WHERE user_token='$user_token' AND status='Active'");
        $count = $check[0]['total'] ?? 0;
        for ($i = 0; $i < $count; $i++) { $weighted_pool[] = 'quintuspay_connected'; }
    }
    // 7. Freecharge
    if (isset($yes_columns['freecharge_connected'])) {
        $check = getXbyY("SELECT count(*) as total FROM freecharge WHERE user_token='$user_token'");
        $count = $check[0]['total'] ?? 0;
        for ($i = 0; $i < $count; $i++) { $weighted_pool[] = 'freecharge_connected'; }
    }
    // 8. SBI
    if (isset($yes_columns['sbi_connected'])) {
        $check = getXbyY("SELECT count(*) as total FROM merchant WHERE user_token='$user_token' AND status='Active'");
        $count = $check[0]['total'] ?? 0;
        for ($i = 0; $i < $count; $i++) { $weighted_pool[] = 'sbi_connected'; }
    }
    // 9. Mobikwik
    if (isset($yes_columns['mobikwik_connected'])) {
        $check = getXbyY("SELECT count(*) as total FROM mobikwik_token WHERE user_token='$user_token'");
        $count = $check[0]['total'] ?? 0;
        for ($i = 0; $i < $count; $i++) { $weighted_pool[] = 'mobikwik_connected'; }
    }
    // 10. AmazonPay
    if (isset($yes_columns['amazonpay_connected'])) {
        $check = getXbyY("SELECT count(*) as total FROM amazon_pay WHERE user_token='$user_token'");
        $count = $check[0]['total'] ?? 0;
        for ($i = 0; $i < $count; $i++) { $weighted_pool[] = 'amazonpay_connected'; }
    }

    if (!empty($weighted_pool)) {
        $random_index = mt_rand(0, count($weighted_pool) - 1);
        $random_key = $weighted_pool[$random_index];
    } else {
        // Fallback if no accounts found despite "connected" status (should rarely happen)
        $keys = array_keys($yes_columns);
        $random_index = mt_rand(0, count($keys) - 1);
        $random_key = $keys[$random_index];
    }

} else {
    http_response_code(400); // Bad Request
        echo json_encode([
            "status" => false,
            "message" => "Merchant Not Connected !try again later",
        ]);
        exit();
}


                if ($random_key == "hdfc_connected") {
                    // if ($amount > 2000) {
                    //     echo json_encode([
                    //         "status" => false,
                    //         "message" => "In HDFC MAxiumum 2000 Allowed",
                    //     ]);
                    //     exit();
                    // }

                    $today = date("Y-m-d");
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]["expiry"];

                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");
                        
                    $slq_hdfc = "SELECT id FROM hdfc where user_token='$user_token'";
                    $res_hdfc = getXbyY($slq_hdfc);
                    $mid = $res_hdfc[0]['id'];
                    

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment/instant-pay/" .
                            $link_token;
                        $gateway_txn1 = rand(1000000000, 9999999999);

                        $method = "HDFC";
                        $currentTimestamp = date("Y-m-d H:i:s");
                        $mTxnid = "";
                        $hdfc_txnid = '';
                        $diss = RandomNumber(18);
                        $sql = "INSERT INTO orders (gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, method, HDFC_TXNID, upiLink, description, create_date, remark1, remark2, user_id, merchant_id,user_mode, byteTransactionId)
    VALUES ('$gateway_txn1', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', '$method', '$hdfc_txnid', '$upiLink', '$diss', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode', '$byteorderid')";

                        setXbyY($sql);

                        echo json_encode([
                            "status" => true,
                            "message" => "Order Created Successfully",
                            "result" => [
                                "orderId" => $order_id,
                                "payment_url" => $payment_link,
                            ],
                        ]);
                        exit();
                    } else {
                        echo json_encode([
                            "status" => false,
                            "message" => "Your Plan Expired Please Renew",
                        ]);
                        exit();
                    }
                }// <-- Close the HDFC block here

                
                //phonepe else if logic start
                elseif ($random_key == "phonepe_connected") {
                    $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];
                    
                    $slq_phonepe = "SELECT sl FROM phonepe_tokens where user_token='$user_token' ORDER BY RAND() LIMIT 1";
                    $res_phonepe = getXbyY($slq_phonepe); 
                    
                     $mid = $res_phonepe[0]['sl'];

                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment2/instant-pay/" . $link_token;

                        $order_id2 = base64_encode($order_id);
                        $gateway_txn = uniqid();
                        $currentTimestamp = date('Y-m-d H:i:s');

                        $sql = "INSERT INTO orders (gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, merchant_id,user_mode)
VALUES ('$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'PhonePe', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode')";

setXbyY($sql);

            $upi_id = $res_p[0]['upi_id'];
             $unitId = $res_p[0]['company'];
            $asdasd23="TXN".rand(111,999).time().rand(1,100);
            $bhimupiintent="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tr=$byteorderid";
            
            $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$byteorderid&tr=$byteorderid&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
                    } else {
                        echo json_encode(array("status" => "false", "message" => "Your Plan Expired Please Renew"));
                        exit;
                    }
                } // <-- Close the phonepe block here
                
                //paytm else if logic start
                elseif ($random_key == "paytm_connected") {
                    $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];
                    
                    $slq_paytm = "SELECT id,Upiid FROM paytm_tokens where user_token='$user_token' AND status='Active' ORDER BY RAND() LIMIT 1";
                    $res_paytm = getXbyY($slq_paytm); 
                    
                    $mid = $res_paytm[0]['id'];

                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment3/instant-pay/" . $link_token;

                        $order_id2 = base64_encode($order_id);
                        $gateway_txn = uniqid();
                        $currentTimestamp = date('Y-m-d H:i:s');
                        $bytetxn_ref_id = GenRandomString().time();	
                       $sql = "INSERT INTO orders (paytm_txn_ref, gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, merchant_id,user_mode)
VALUES ('$bytetxn_ref_id', '$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'Paytm', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode')";

setXbyY($sql);

        
         $upi_id = $res_paytm[0]['Upiid'];
             $unitId = $res_p[0]['company'];
            $asdasd23="ARC".rand(111,999).time().rand(1,100);
            
            $bhimupiintent="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tr=$bytetxn_ref_id";
            
            $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$bytetxn_ref_id&tr=$bytetxn_ref_id&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
                    } else {
                        echo json_encode(array("status" => "false", "message" => "Your Plan Expired Please Renew"));
                        exit;
                    }
                }
                elseif ($random_key == "freecharge_connected") {
                    $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];
                    
                    $freecharge = "SELECT id,upi_id FROM freecharge where user_token='$user_token' AND status='Active' ORDER BY RAND() LIMIT 1";
                    $freechargedata = getXbyY($freecharge); 
                    $mid = $freechargedata[0]['id'];

                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment7/instant-pay/" . $link_token;
                        $googletxnnote = "ATC" . substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 5) . time();

                        $order_id2 = base64_encode($order_id);
                        $gateway_txn = uniqid();
                        $currentTimestamp = date('Y-m-d H:i:s');
                        $bytetxn_ref_id = GenRandomString().time();	
                       $sql = "INSERT INTO orders (gateway_txn, paytm_txn_ref, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, merchant_id,user_mode)
VALUES ('$gateway_txn', '$googletxnnote','$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'Freecharge', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode')";

setXbyY($sql);


         $upi_id = $freechargedata[0]['upi_id'];
 
             $unitId = $res_p[0]['company'];
            $asdasd23="TXN".rand(111,999).time().rand(1,100);
            $bhimupiintent = "upi://pay?pa=$upi_id&am=$amount&tid=$googletxnnote&pn=$unitId&tn=$googletxnnote&tr=$asdasd23";
            
            $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&tid=$googletxnnote&cu=INR&tn=$googletxnnote&tr=$googletxnnote&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
                    } else {
                        echo json_encode(array("status" => "false", "message" => "Your Plan Expired Please Renew"));
                        exit;
                    }
                } // <-- Close the freecharge block here
                
                 //Mobikwik Merchant
    elseif ($random_key == "mobikwik_connected") {
        $today = date("Y-m-d");
        $slq_p = "SELECT * FROM users where user_token='$user_token'";
        $res_p = getXbyY($slq_p);
        $expire_date = $res_p[0]["expiry"];

        if ($expire_date >= $today) {
            // Generate a unique payment link token
            $link_token = generateUniqueToken();

            $cxrtoday = date("Y-m-d H:i:s");
            
     $slq_mobikwik = "SELECT id FROM mobikwik_token where user_token='$user_token' AND status='Active' ORDER BY RAND() LIMIT 1";
                    $res_mobikwik = getXbyY($slq_mobikwik);
                    $mid = $res_mobikwik[0]['id'];

            // Insert the link_token into the payment_links table with the current date and time
            $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
            setXbyY($sql_insert_link);

            // Construct the payment link
            $payment_link = $site_url."/payment8/instant-pay/" . $link_token;

            $gateway_txn = uniqid();
            $googletxnnote = "ATC" . substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 5) . time();
            $currentTimestamp = date("Y-m-d H:i:s");
            $sql = "INSERT INTO orders (gateway_txn,paytm_txn_ref, amount, order_id, status, user_token, utr, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, merchant_id,user_id)
VALUES ('$gateway_txn', '$googletxnnote' ,'$amount', '$order_id', 'PENDING', '$user_token', '', '$customer_mobile', '$redirect_url', 'MOBIKWIK', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$mid','$bydb_unq_user_id')";

            setXbyY($sql);
 http_response_code(201); // Created
            echo json_encode([
                "status" => true,
                "message" => "Order Created Successfully",
                "result" => [
                    "orderId" => $order_id,
                    "payment_url" => $payment_link,
                ],
            ]);
            exit();
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => false,
                "message" => "Your Plan Expired Please Renew",
            ]);
            exit();
        }
    }// <-- Close the mobikwik

 elseif ($random_key == "amazonpay_connected") {
        $today = date("Y-m-d");
        $slq_p = "SELECT * FROM users where user_token='$user_token'";
        $res_p = getXbyY($slq_p);
        $expire_date = $res_p[0]["expiry"];
        
         $slq_amz = "SELECT id,upi_id FROM amazon_pay where user_token='$user_token' AND status='Active' ORDER BY RAND() LIMIT 1";
        $res_amz = getXbyY($slq_amz); 
        $mid = $res_amz[0]['id'];

        if ($expire_date >= $today) {
            // Generate a unique payment link token
            $link_token = generateUniqueToken();

            $cxrtoday = date("Y-m-d H:i:s");

            // Insert the link_token into the payment_links table with the current date and time
            $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
            setXbyY($sql_insert_link);

            // Construct the payment link
            $payment_link = $site_url."/payment9/instant-pay/" . $link_token;

            $gateway_txn = uniqid();
            $googletxnnote = "ATC" . substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 5) . time();
            $currentTimestamp = date("Y-m-d H:i:s");
            $sql = "INSERT INTO orders (gateway_txn,paytm_txn_ref, amount, order_id, status, user_token, utr, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id,merchant_id)
VALUES ('$gateway_txn', '$googletxnnote' ,'$amount', '$order_id', 'PENDING', '$user_token', '', '$customer_mobile', '$redirect_url', 'Amazonpay', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id','$mid')";

            setXbyY($sql);
            
  
      $upi_id = $res_amz[0]['upi_id'];
      
             $unitId = $res_p[0]['company'];
            $asdasd23="TXN".rand(111,999).time().rand(1,100);
            $bhimupiintent="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tid=$googletxnnote";
            
    $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$googletxnnote&tid=$googletxnnote&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => false,
                "message" => "Your Plan Expired Please Renew",
            ]);
            exit();
        }
    }// <-- Close the amazon pay
            
                //sbi else if logic start
                  elseif ($random_key == "sbi_connected") {
                    $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];
                    
                     $slq_sbi = "SELECT merchant_id,merchant_upi FROM merchant where user_token='$user_token' AND status='Active' ORDER BY RAND() LIMIT 1";
                $res_sbi = getXbyY($slq_sbi);  
                $mid = $res_sbi[0]['merchant_id'];

                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();
                        $bank_orderid = order_txn_id("IT");
                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment6/instant-pay/" . $link_token;

                        $order_id2 = base64_encode($order_id);
                        $gateway_txn = uniqid();
                        $currentTimestamp = date('Y-m-d H:i:s');
                        $bytetxn_ref_id = GenRandomString().time();	
                       $sql = "INSERT INTO orders (bank_orderid, gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, merchant_id,user_mode)
VALUES ('$bank_orderid', '$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'SBI', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode')";

setXbyY($sql);

                          
         $upi_id = $res_p[0]['merchant_upi'];
 
             $unitId = $res_p[0]['company'];
            $asdasd23="TXN".rand(111,999).time().rand(1,100);
            $bhimupiintent="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tr=$bank_orderid";
            
            $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$bank_orderid&tr=$bank_orderid&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
                    } else {
                        echo json_encode(array("status" => "false", "message" => "Your Plan Expired Please Renew"));
                        exit;
                    }
                } // <-- Close the paytm block here
                
                
                  //Bharatpe else if logic start
                elseif ($random_key == "bharatpe_connected") {
                    $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];

                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");
                        
                        // ROUND ROBIN LOGIC FOR BHARATPE
                        // 1. Get all active tokens for this user, sorted by ID
                        $slq_all_tokens = "SELECT id FROM bharatpe_tokens WHERE user_token='$user_token' AND status='Active' ORDER BY id ASC";
                        $res_all_tokens = getXbyY($slq_all_tokens);
                        
                        if (empty($res_all_tokens)) {
                             echo json_encode(array("status" => "false", "message" => "No Active Merchant Found"));
                             exit;
                        }

                        // 2. Get the last used merchant_id for this user's BharatPe orders
                        $slq_last_order = "SELECT merchant_id FROM orders WHERE user_token='$user_token' AND Method='Bharatpe' ORDER BY id DESC LIMIT 1";
                        $res_last_order = getXbyY($slq_last_order);
                        $last_used_mid = $res_last_order[0]['merchant_id'] ?? 0;

                        // 3. Find the index of the last used ID and pick the next one
                        $next_mid = $res_all_tokens[0]['id']; // Default to first
                        $found_last = false;
                        
                        // Extract IDs into a simple array for easier search
                        $token_ids = array_column($res_all_tokens, 'id');
                        $count = count($token_ids);
                        
                        if ($last_used_mid != 0) {
                            $last_index = array_search($last_used_mid, $token_ids);
                            if ($last_index !== false) {
                                // Calculate next index (circular)
                                $next_index = ($last_index + 1) % $count;
                                $next_mid = $token_ids[$next_index];
                            }
                        }
                        
                        $mid = $next_mid;

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment4/instant-pay/" . $link_token;

                        
                        $gateway_txn = time().rand(11111,99999);
                        $currentTimestamp = date('Y-m-d H:i:s');
                        $sql = "INSERT INTO orders (gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, merchant_id,user_id, user_mode)
VALUES ('$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'Bharatpe', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$mid','$bydb_unq_user_id', '$rmode')";

setXbyY($sql);

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link)));
                        exit;
                    } else {
                        echo json_encode(array("status" => "false", "message" => "Your Plan Expired Please Renew"));
                        exit;
                    }
                } // <-- Close the Bharatpe block here
                
                 //GooglePay else if logic start
                elseif ($random_key == "googlepay_connected") {
                   $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];
                    
      $slq_paytm = "SELECT id,Upiid FROM gpay_tokens where user_token='$user_token' AND status='Active' ORDER BY RAND() LIMIT 1";
        $res_paytm = getXbyY($slq_paytm); 
        
        $mid = $res_paytm[0]['id'];
        
                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment5/instant-pay/" . $link_token;

                        $order_id2 = base64_encode($order_id);
                        $gateway_txn = uniqid();
                        $currentTimestamp = date('Y-m-d H:i:s');
                        $bytetxn_ref_id = GenRandomString().time();	
                       $sql = "INSERT INTO orders (paytm_txn_ref, gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, merchant_id,user_mode)
VALUES ('$bytetxn_ref_id', '$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'Googlepay', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode')";

setXbyY($sql);

        
         $upi_id = $res_paytm[0]['Upiid'];
             $unitId = $res_p[0]['company'];
            $asdasd23="ARC".rand(111,999).time().rand(1,100);
            
            $bhimupiintent="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tr=$bytetxn_ref_id";
            
            $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$bytetxn_ref_id&tr=$bytetxn_ref_id&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
                  } 
                }
                elseif ($random_key == "quintuspay_connected") {
                   $today = date('Y-m-d');
                    $slq_p = "SELECT * FROM users where user_token='$user_token'";
                    $res_p = getXbyY($slq_p);
                    $expire_date = $res_p[0]['expiry'];
                    
      $slq_paytm = "SELECT id,Upiid FROM quintus_tokens where user_token='$user_token' AND status='Active' ORDER BY RAND() LIMIT 1";
        $res_paytm = getXbyY($slq_paytm); 
        
        $mid = $res_paytm[0]['id'];
        
                    if ($expire_date >= $today) {
                        // Generate a unique payment link token
                        $link_token = generateUniqueToken();

                        $cxrtoday = date("Y-m-d H:i:s");

                        // Insert the link_token into the payment_links table with the current date and time
                        $sql_insert_link = "INSERT INTO payment_links (link_token, order_id, created_at) VALUES ('$link_token', '$order_id', '$cxrtoday')";
                        setXbyY($sql_insert_link);

                        // Construct the payment link
                        $payment_link = $site_url."/payment10/instant-pay/" . $link_token;

                        $order_id2 = base64_encode($order_id);
                        $gateway_txn = uniqid();
                        $currentTimestamp = date('Y-m-d H:i:s');
                        $bytetxn_ref_id = GenRandomString().time();	
                       $sql = "INSERT INTO orders (paytm_txn_ref, gateway_txn, amount, order_id, status, user_token, utr, plan_id, customer_mobile, redirect_url, Method, byteTransactionId, create_date, remark1, remark2, user_id, merchant_id,user_mode)
VALUES ('$bytetxn_ref_id', '$gateway_txn', '$amount', '$order_id', 'PENDING', '$user_token', '', '', '$customer_mobile', '$redirect_url', 'Quintuspay', '$byteorderid', '$currentTimestamp', '$remark1', '$remark2', '$bydb_unq_user_id', '$mid','$rmode')";

setXbyY($sql);

        
         $upi_id = $res_paytm[0]['Upiid'];
             $unitId = $res_p[0]['company'];
            $asdasd23="ARC".rand(111,999).time().rand(1,100);
            
            $bhimupiintent="upi://pay?pa=$upi_id&am=$amount&pn=$unitId&tn=$asdasd23&tr=$bytetxn_ref_id";
            
            $paytmupiintent = "paytmmp://cash_wallet?pa=$upi_id&pn=$unitId&am=$amount&cu=INR&tn=$bytetxn_ref_id&tr=$bytetxn_ref_id&mc=4722&&sign=AAuN7izDWN5cb8A5scnUiNME+LkZqI2DWgkXlN1McoP6WZABa/KkFTiLvuPRP6/nWK8BPg/rPhb+u4QMrUEX10UsANTDbJaALcSM9b8Wk218X+55T/zOzb7xoiB+BcX8yYuYayELImXJHIgL/c7nkAnHrwUCmbM97nRbCVVRvU0ku3Tr&featuretype=money_transfer";

                        echo json_encode(array("status" => true, "message" => "Order Created Successfully", "result" => array("orderId" => $order_id, "payment_url" => $payment_link,"paytm_link" => $paytmupiintent,"bhim_link" => $bhimupiintent)));
                        exit;
                  } 
                }
                
                elseif ($bydb_order_hdfc_conn == "No" || $bydb_order_phonepe_conn == "No" || $bydb_order_paytm_conn == "No" || $bydb_order_bharatpe_conn == "No" || $bydb_order_googlepay_conn == "No") {
                         echo json_encode(array("status" => "false", "message" => "Merchant Not Linked"));
                         exit;
                    } 
                }
            }
        }
    }
}
?>
