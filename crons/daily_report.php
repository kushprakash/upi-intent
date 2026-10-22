<?php
// Daily Transaction Report Cron
include "../pages/dbInfo.php";
include "../pages/emailFunctions.php";

$conn = connect_database();

// Get all users who have transactions today or just active users
// For scalability, we should loop through users.

$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime("-1 days")); 
// If running at midnight 00:00, we typically want yesterday's report.
// Assuming this runs at 23:55 or 00:05. Let's strictly pull "Today's" date assuming it runs at 23:55.
// OR allow passing date param.

$start_date = $today . " 00:00:00";
$end_date = $today . " 23:59:59";

// Fetch all users
$users_query = mysqli_query($conn, "SELECT id, name, email, user_token FROM users WHERE active=1");

while ($user = mysqli_fetch_assoc($users_query)) {
    $user_token = $user['user_token'];
    
    // Fetch user's transactions for the day
    $sql = "SELECT * FROM orders WHERE user_token = '$user_token' AND create_date BETWEEN '$start_date' AND '$end_date' AND status='SUCCESS'";
    $txns_result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($txns_result) > 0) {
        $total_amount = 0;
        $txn_html = '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; border-collapse: collapse;">
            <tr style="background:#eee;"><th>Order ID</th><th>Amount</th><th>Method</th><th>Date</th></tr>';
        
        while ($txn = mysqli_fetch_assoc($txns_result)) {
            $total_amount += $txn['amount'];
            $txn_html .= "<tr>
                <td>{$txn['order_id']}</td>
                <td>₹{$txn['amount']}</td>
                <td>{$txn['method']}</td>
                <td>{$txn['create_date']}</td>
            </tr>";
        }
        $txn_html .= "</table>";
        
        $email_subject = "Daily Transaction Report - " . $today;
        $email_body = "<h3>Hello {$user['name']},</h3>
        <p>Here is your transaction summary for today ($today).</p>
        <p><b>Total Successful Transactions:</b> " . mysqli_num_rows($txns_result) . "</p>
        <p><b>Total Volume:</b> ₹" . number_format($total_amount, 2) . "</p>
        <br>
        $txn_html
        <br>
        <p>Regards,<br>UPIGateways Team</p>";
        
        systemSendEmail($user['email'], $email_subject, $email_body, $conn);
        echo "Report sent to {$user['email']}<br>";
    }
}
?>
