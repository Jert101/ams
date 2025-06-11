<?php
// Display all errors for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the file path
$file = 'downloads/ckp-kofa-app.apk';
$filePath = __DIR__ . '/' . $file;

// Debug information
if (isset($_GET['debug'])) {
    echo "<h2>Download Debug Information</h2>";
    echo "File path: " . $filePath . "<br>";
    echo "File exists: " . (file_exists($filePath) ? 'Yes' : 'No') . "<br>";
    
    if (file_exists($filePath)) {
        echo "File size: " . filesize($filePath) . " bytes<br>";
        echo "Is readable: " . (is_readable($filePath) ? 'Yes' : 'No') . "<br>";
        echo "File permissions: " . substr(sprintf('%o', fileperms($filePath)), -4) . "<br>";
    }
    
    echo "<br><a href='basic-download.php'>Try download</a>";
    exit;
}

// Check if file exists
if (!file_exists($filePath)) {
    die("Error: File not found");
}

// Set appropriate headers
header('Content-Type: application/vnd.android.package-archive');
header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache');

// Read file and output
$fp = fopen($filePath, 'rb');

// Check if file was opened successfully
if (!$fp) {
    die("Error: Cannot open file");
}

// Output the file in chunks to handle large files
fpassthru($fp);
fclose($fp);
exit;
?> 