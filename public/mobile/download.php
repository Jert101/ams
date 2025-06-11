<?php
/**
 * Mobile App Download Script
 * This script serves the CKP-KofA mobile application APK file.
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the file path - use full path, not relative
$file = __DIR__ . '/apk/ckp-kofa-app.apk';

// Debug mode
if (isset($_GET['debug'])) {
    echo "<h1>Download Debug Info</h1>";
    echo "File path: " . $file . "<br>";
    echo "File exists: " . (file_exists($file) ? 'Yes' : 'No') . "<br>";
    
    if (file_exists($file)) {
        echo "File size: " . filesize($file) . " bytes<br>";
        echo "File readable: " . (is_readable($file) ? 'Yes' : 'No') . "<br>";
    }
    
    echo "<p><a href='download.php'>Try downloading again</a></p>";
    exit;
}

// Direct path to APK - try different path if main path fails
if (!file_exists($file)) {
    // Try alternative path
    $file = dirname(__DIR__) . '/mobile/apk/ckp-kofa-app.apk';
}

// Check if file exists and handle download
if (file_exists($file)) {
    // Set proper headers
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Output the file
    readfile($file);
    exit;
} else {
    // Display error message
    echo "<h1>File Not Found</h1>";
    echo "<p>The APK file could not be found on the server.</p>";
    echo "<p><a href='download.php?debug=1'>Show Debug Info</a></p>";
    echo "<p>Try these alternative download methods:</p>";
    echo "<ul>";
    echo "<li><a href='apk/download.php'>Download Method 1</a></li>";
    echo "<li><a href='apk/ckp-kofa-app.apk'>Direct Download</a></li>";
    echo "</ul>";
}
?> 