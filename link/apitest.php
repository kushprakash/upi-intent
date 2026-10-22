<?php
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

// Process API requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (isset($input['long_url'])) {
        $longUrl = $input['long_url'];
        $shortCode = generateShortCode();
        saveUrl($shortCode, $longUrl);
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$site_url = $protocol . "://" . $_SERVER['HTTP_HOST'];
        echo json_encode(["short_url" => "$site_url/link/$shortCode"]);
    } else {
        echo json_encode(["error" => "No URL provided"]);
    }
    exit();
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

// If no code is provided, show the form (optional)
?>
<!DOCTYPE html>
<html>
<head>
    <title>URL Shortener Test</title>
</head>
<body>
    <h1>Test URL Shortener API</h1>
    <form id="shortenForm">
        <input type="url" id="longUrl" placeholder="Enter your URL" required>
        <button type="submit">Shorten</button>
    </form>
    <p id="shortUrlResult"></p>
    
    <script>
        document.getElementById('shortenForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var longUrl = document.getElementById('longUrl').value;

            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ long_url: longUrl })
            })
            .then(response => response.json())
            .then(data => {
                if (data.short_url) {
                    document.getElementById('shortUrlResult').innerHTML = 
                        'Short URL: <a href="' + data.short_url + '">' + data.short_url + '</a>';
                } else if (data.error) {
                    document.getElementById('shortUrlResult').innerText = 'Error: ' + data.error;
                }
            })
            .catch(error => {
                document.getElementById('shortUrlResult').innerText = 'Error: ' + error;
            });
        });
    </script>
</body>
</html>
