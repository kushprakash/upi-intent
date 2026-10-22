<?php
// Function to get the long URL from the text file
function getLongUrl($shortCode) {
    $file = 'urls.txt';
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($code, $url) = explode('|', $line);
        if ($code == $shortCode) {
            return $url;
        }
    }
    return false;
}

// Process redirection
if (isset($_GET['code'])) {
    $shortCode = $_GET['code'];
    $longUrl = getLongUrl($shortCode);
    if ($longUrl) {
        header("Location: $longUrl");
        exit();
    } else {
        echo json_encode(["error" => "URL not found"]);
        exit();
    }
}

// If no code is provided, show an error (or you can show a form for testing)
echo json_encode(["error" => "No code provided"]);
exit();
?>
