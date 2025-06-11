<?php
// Simple direct download script for the CKP-KofA app APK
$file = __DIR__ . '/downloads/ckp-kofa-app.apk';

// Check if file exists
if (file_exists($file)) {
    // Set the appropriate headers for APK download
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: no-cache');
    
    // Read the file and output it to the browser
    readfile($file);
    exit;
} else {
    // File not found error
    header('HTTP/1.0 404 Not Found');
    echo "<p style='color: red; font-weight: bold;'>APK file not found.</p>";
    echo "<p>Please contact the administrator for assistance.</p>";
}
?> 