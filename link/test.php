<?php
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$site_url = $protocol . "://" . $_SERVER['HTTP_HOST'];

// The URL of the API endpoint
$apiUrl = $site_url.'/link/do.php';

// The long URL you want to shorten
$longUrl = $site_url.'/checkout/pay/b6eaed78e63560ac13a12bee8a0129a91888dc0c078faeb6dfaaeace13be1a5b';

// Prepare the data to be sent in the POST request
$data = json_encode([
    'long_url' => $longUrl
]);

// Initialize a cURL session
$ch = curl_init($apiUrl);

// Set the options for the cURL session
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data)
]);

// Execute the cURL session and get the response
$response = curl_exec($ch);

// Check for errors
if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    die('cURL Error: ' . $error);
}

// Close the cURL session
curl_close($ch);

// Decode the JSON response
$responseData = json_decode($response, true);

// Check if the response contains the shortened URL
if (isset($responseData['short_url'])) {
    echo 'Shortened URL: ' . $responseData['short_url'];
} else {
    echo 'Error: ' . $responseData['error'];
}
?>
