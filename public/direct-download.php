<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Direct download script for the real application APK
$localFile = __DIR__ . '/downloads/ckp-kofa-app.apk';

// Debug information (comment this out in production)
if (isset($_GET['debug'])) {
    echo "File path: " . $localFile . "<br>";
    echo "File exists: " . (file_exists($localFile) ? 'Yes' : 'No') . "<br>";
    echo "File readable: " . (is_readable($localFile) ? 'Yes' : 'No') . "<br>";
    
    if (file_exists($localFile)) {
        echo "File size: " . filesize($localFile) . " bytes<br>";
        echo "File permissions: " . substr(sprintf('%o', fileperms($localFile)), -4) . "<br>";
    }
    
    echo "<a href='direct-download.php'>Try download again</a>";
    exit;
}

// Check if the file exists and is readable
if (file_exists($localFile) && is_readable($localFile)) {
    // Set headers for APK download
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
    header('Content-Length: ' . filesize($localFile));
    header('Pragma: public');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
    
    // Clear the output buffer to ensure no whitespace is sent
    ob_clean();
    flush();
    
    // Output the file
    readfile($localFile);
    exit;
} else {
    // If file doesn't exist or isn't readable, show error
    header('HTTP/1.0 404 Not Found');
    echo "APK file not found or not readable. Please contact the administrator.<br>";
    echo "You can <a href='direct-download.php?debug=1'>check debug information</a> to troubleshoot.";
}
exit;
?> 