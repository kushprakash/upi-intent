<?php
include "config.php";
// List of URLs to access
$urls = [
    $site_url."/merchant/cron_sbimerchant_sessions.php?cron_token=0682b9-5ac595-c47ca5-2a048e-67b5c9"
];

// Function to access a URL
function accessUrl($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false || $http_code != 200) {
        echo "<div style='margin-bottom: 20px;'>";
        echo "<strong>Failed to access $url.</strong><br>";
        echo "HTTP code: $http_code.<br>";
        echo "Error: " . curl_error($ch) . "<br>";
        echo "</div>";
    } else {
        echo "<div style='margin-bottom: 20px;'>";
        echo "<strong>Successfully accessed $url.</strong><br>";
        echo "HTTP code: $http_code.<br>";
        echo "Response:<br><pre>" . json_encode(json_decode($response), JSON_PRETTY_PRINT) . "</pre><br>";
        echo "</div>";
    }

    curl_close($ch);
}

// Access each URL
foreach ($urls as $url) {
    accessUrl($url);
}
?>
