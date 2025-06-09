<?php
// Check if the file exists
$filePath = __DIR__ . '/downloads/ckp-kofa-app.apk';
if (file_exists($filePath)) {
    echo "File exists at: " . $filePath . "<br>";
    echo "File size: " . filesize($filePath) . " bytes<br>";
    echo "File is readable: " . (is_readable($filePath) ? 'Yes' : 'No') . "<br>";
    
    // Check if the directory is accessible
    $dirPath = __DIR__ . '/downloads';
    echo "Directory exists: " . (is_dir($dirPath) ? 'Yes' : 'No') . "<br>";
    echo "Directory is readable: " . (is_readable($dirPath) ? 'Yes' : 'No') . "<br>";
    
    // Try to get the file URL
    $fileUrl = 'downloads/ckp-kofa-app.apk';
    echo "File URL: <a href='{$fileUrl}'>{$fileUrl}</a>";
} else {
    echo "File not found at: " . $filePath;
}
?> 