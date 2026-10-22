<?php
function trackUsage($license_key) {
    $trackingData = [
        'license_key' => $license_key,
        'server_ip' => $_SERVER['SERVER_ADDR'],
        'hostname' => gethostname(),
        'domain' => $_SERVER['HTTP_HOST'],
        'php_version' => phpversion(),
        'os' => php_uname()
    ];

    $ch = curl_init("https://business.phnepe.com/apis/pg-sandbox");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($trackingData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// Call Function When Script Runs
trackUsage('YOUR_LICENSE_KEY');
?>