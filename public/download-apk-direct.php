<?php
// Most basic direct file download script
$file = __DIR__ . '/downloads/ckp-kofa-app.apk';

if (file_exists($file)) {
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
    header('Content-Length: ' . filesize($file));
    
    // Output the file content directly
    echo file_get_contents($file);
    exit;
} else {
    echo "File not found: " . $file;
}
?> 