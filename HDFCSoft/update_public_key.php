<?php

// URL to fetch the public key
$url = "https://pay.garudhub.in/public_key.php";

// Path to the public.key file
$filePath = 'public.key';

// Fetch the public key from the URL
$publicKey = file_get_contents($url);

if ($publicKey === FALSE) {
    die("Failed to fetch the public key.");
}

// Write the fetched key to the public.key file
$formattedKey = "-----BEGIN PUBLIC KEY-----\n" . chunk_split($publicKey, 64, "\n") . "-----END PUBLIC KEY-----\n";
file_put_contents($filePath, $formattedKey);

echo "Public key updated successfully.";
?>