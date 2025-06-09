<?php
// Simple direct download script that uses GitHub as the primary source
$localFile = __DIR__ . '/downloads/ckp-kofa-app.apk';
$githubUrl = 'https://github.com/Jert101/ams/raw/main/public/downloads/ckp-kofa-app.apk';

// Set headers for APK download
header('Content-Type: application/vnd.android.package-archive');
header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');

// Try to use local file if it exists
if (file_exists($localFile)) {
    header('Content-Length: ' . filesize($localFile));
    readfile($localFile);
    exit;
}

// Otherwise redirect to GitHub
header('Location: ' . $githubUrl);
exit;
?> 