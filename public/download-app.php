<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Direct path to APK file - no relative paths
$file = __DIR__ . '/KofA Network.apk';

// Debug information - only show if explicitly requested
if (isset($_GET['debug'])) {
    echo "<h1>APK Download Debug Info</h1>";
    echo "File path: " . $file . "<br>";
    echo "File exists: " . (file_exists($file) ? 'Yes' : 'No') . "<br>";
    
    if (file_exists($file)) {
        echo "File size: " . filesize($file) . " bytes<br>";
        echo "File readable: " . (is_readable($file) ? 'Yes' : 'No') . "<br>";
        echo "File permissions: " . substr(sprintf('%o', fileperms($file)), -4) . "<br>";
    }
    
    echo "<p><a href='download-app.php'>Try downloading again</a></p>";
    exit;
}

// Handle the download
if (file_exists($file) && is_readable($file)) {
    // Force download headers
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="KofA Network.apk"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    
    // Output file
    readfile($file);
    exit;
} else {
    // Show error message with troubleshooting link
    echo "<h1>Error: APK File Not Found</h1>";
    echo "<p>The application file could not be found or accessed.</p>";
    echo "<p><a href='download-app.php?debug=1'>View Debug Information</a></p>";
    echo "<p><a href='/'>Return to Homepage</a></p>";
}
?> 