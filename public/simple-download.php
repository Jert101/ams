<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set file path
$file = __DIR__ . '/downloads/ckp-kofa-app.apk';

// Debug mode
if (isset($_GET['debug'])) {
    echo "<h1>APK Download Debug Information</h1>";
    echo "<p>File path: " . $file . "</p>";
    echo "<p>File exists: " . (file_exists($file) ? 'Yes' : 'No') . "</p>";
    
    if (file_exists($file)) {
        echo "<p>File size: " . filesize($file) . " bytes</p>";
        echo "<p>File permissions: " . substr(sprintf('%o', fileperms($file)), -4) . "</p>";
        echo "<p>File readable: " . (is_readable($file) ? 'Yes' : 'No') . "</p>";
    }
    
    echo "<p><a href='simple-download.php'>Try download again</a></p>";
    exit;
}

// Check if file exists
if (file_exists($file) && is_readable($file)) {
    // Force download
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename=ckp-kofa-app.apk');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    
    // Don't use ob_clean() as it might not be available
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Output file contents
    readfile($file);
    exit;
} else {
    // Display error
    header('HTTP/1.0 404 Not Found');
    echo "<h1>Error: File not found</h1>";
    echo "<p>The APK file could not be found or is not readable.</p>";
    echo "<p><a href='simple-download.php?debug=1'>View debug information</a></p>";
}
?> 