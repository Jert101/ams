<?php
// Most direct approach - same directory as the APK
$file = 'ckp-kofa-app.apk';

// If the regular file doesn't exist, check for base.apk
if (!file_exists($file)) {
    $file = 'base.apk';
    
    // If we find base.apk, use that instead
    if (file_exists($file)) {
        header('Content-Type: application/vnd.android.package-archive');
        header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
    
    // Neither file exists
    echo "File not found. Checked for ckp-kofa-app.apk and base.apk in " . __DIR__;
    exit;
}

// Original file exists, serve it
header('Content-Type: application/vnd.android.package-archive');
header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
header('Content-Length: ' . filesize($file));
readfile($file);
?> 