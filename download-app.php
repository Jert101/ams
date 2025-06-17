<?php
$file = __DIR__ . '/KofA Network.apk';
if (!file_exists($file)) {
    http_response_code(404);
    echo 'File not found.';
    exit;
}
header('Content-Type: application/vnd.android.package-archive');
header('Content-Disposition: attachment; filename="KofA Network.apk"');
header('Content-Length: ' . filesize($file));
readfile($file);
exit; 