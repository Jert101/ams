<?php
$file = __DIR__ . '/downloads/ckp-kofa-app.apk';

if (file_exists($file)) {
    // Set the appropriate headers
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: no-cache');
    
    // Read the file and output it to the browser
    readfile($file);
    exit;
} else {
    echo "File not found. Please contact the administrator.";
}
?> 