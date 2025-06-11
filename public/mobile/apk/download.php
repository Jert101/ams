<?php
// Most direct approach - same directory as the APK
$file = 'ckp-kofa-app.apk';

if (file_exists($file)) {
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
    header('Content-Length: ' . filesize($file));
    readfile($file);
} else {
    echo "File not found: " . $file;
}
?> 