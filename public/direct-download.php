<?php
// Direct download script for the real application APK
$localFile = __DIR__ . '/downloads/ckp-kofa-app.apk';

// Set headers for APK download
header('Content-Type: application/vnd.android.package-archive');
header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');

// Use local file
if (file_exists($localFile)) {
    header('Content-Length: ' . filesize($localFile));
    readfile($localFile);
    exit;
} else {
    // If file doesn't exist, show error
    header('HTTP/1.0 404 Not Found');
    echo "APK file not found. Please contact the administrator.";
}
exit;
?> 