<?php
include "../merchant/config.php";
// Function to generate a unique short code
function generateShortCode($length = 6) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

// Function to save URL to a text file (for simplicity)
function saveUrl($shortCode, $longUrl) {
    $file = 'urls.txt';
    $data = "$shortCode|$longUrl\n";
    file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
}

// Process API requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['long_url'])) {
        $longUrl = $input['long_url'];
        $shortCode = generateShortCode();
        saveUrl($shortCode, $longUrl);
        echo json_encode(["short_url" => $site_url."/link/$shortCode"]);
    } else {
        echo json_encode(["error" => "No URL provided"]);
    }
    exit();
}
?>
