<?php
// Simple direct download script - no error checking
$file = __DIR__ . '/downloads/ckp-kofa-app.apk';

// Force download regardless of file existence
header('Content-Type: application/vnd.android.package-archive');
header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
if (file_exists($file)) {
    header('Content-Length: ' . filesize($file));
    readfile($file);
} else {
    // If file doesn't exist locally, try to serve from GitHub as fallback
    $githubUrl = 'https://github.com/Jert101/ams/raw/main/public/downloads/ckp-kofa-app.apk';
    header('Location: ' . $githubUrl);
}
exit;
?> 