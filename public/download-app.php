<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Direct path to APK file - no relative paths
$filePath = __DIR__ . '/mobile/apk/ckp-kofa-app.apk';

// Debug information - only show if explicitly requested
if (isset($_GET['debug'])) {
    echo "<h1>APK Download Debug Info</h1>";
    echo "File path: " . $filePath . "<br>";
    echo "File exists: " . (file_exists($filePath) ? 'Yes' : 'No') . "<br>";
    
    if (file_exists($filePath)) {
        echo "File size: " . filesize($filePath) . " bytes<br>";
        echo "File readable: " . (is_readable($filePath) ? 'Yes' : 'No') . "<br>";
        echo "File permissions: " . substr(sprintf('%o', fileperms($filePath)), -4) . "<br>";
    }
    
    echo "<p><a href='download-app.php'>Try downloading again</a></p>";
    exit;
}

// Handle the download
if (file_exists($filePath) && is_readable($filePath)) {
    // Force download headers
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filePath));
    
    // Output file
    readfile($filePath);
    exit;
} else {
    // Show error message with troubleshooting link
    echo "<h1>Error: APK File Not Found</h1>";
    echo "<p>The application file could not be found or accessed.</p>";
    echo "<p><a href='download-app.php?debug=1'>View Debug Information</a></p>";
    echo "<p><a href='/'>Return to Homepage</a></p>";
}
?> 