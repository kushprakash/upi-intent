<?php
// ERROR REPORTING
error_reporting(1);

// YOUR DB FILES
include "../pages/dbFunctions.php";
include "../pages/dbInfo.php";

// DB CONNECT
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

// QUERY: Get orders which are pending for more than 10 minutes
$sql = "
SELECT id, order_id, create_date 
FROM orders 
WHERE status = 'PENDING' 
AND create_date <= (NOW() - INTERVAL 10 MINUTE)
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $order_id = $row['order_id'];

        // UPDATE TO FAILURE
        $update = $conn->prepare("
            UPDATE orders 
            SET status='FAILURE', reason='PAYMENT_TIMEOUT' 
            WHERE order_id=?
        ");
        $update->bind_param("s", $order_id);
        $update->execute();
        $update->close();

        echo "Order Auto Failed: " . $order_id . "\n";
    }

} else {
    echo "No pending orders older than 10 minutes.\n";
}

$conn->close();
?>
