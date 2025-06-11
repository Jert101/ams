<?php
// Set the file path to the .dat file (which is actually the APK)
$file = __DIR__ . '/downloads/app.dat';

// Check if file exists
if (file_exists($file)) {
    // Set headers for APK download
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
    header('Content-Length: ' . filesize($file));
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    
    // Output the file in a way that avoids buffer issues
    $handle = fopen($file, 'rb');
    if ($handle) {
        while (!feof($handle)) {
            echo fread($handle, 8192);
            flush();
        }
        fclose($handle);
    }
    exit;
} else {
    header('HTTP/1.0 404 Not Found');
    echo "File not found.";
}
?> 