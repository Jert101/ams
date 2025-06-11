<?php
/**
 * Mobile App Download Script
 * This script serves the CKP-KofA mobile application APK file.
 */

// Set file path - using relative path for compatibility
$file = __DIR__ . '/apk/ckp-kofa-app.apk';

// Check if file exists
if (file_exists($file)) {
    // Set proper headers
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Read file and output it
    readfile($file);
    exit;
} else {
    // Display error message
    http_response_code(404);
    echo '<h1>Error: APK File Not Found</h1>';
    echo '<p>The CKP-KofA mobile app file could not be found on the server.</p>';
    echo '<p><a href="/">Return to Homepage</a></p>';
}
?> 