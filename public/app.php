<?php
// Simple standalone download script
// This script will download the APK directly or show debugging information

// Display errors for troubleshooting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// HTML output if not in download mode
if (!isset($_GET['download'])) {
    echo '<!DOCTYPE html>
<html>
<head>
    <title>CKP-KofA App Download</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .btn {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .debug {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>CKP-KofA Mobile App Download</h1>';

    // Check various possible locations for the APK file
    $possibleLocations = [
        __DIR__ . '/mobile/apk/ckp-kofa-app.apk',
        __DIR__ . '/downloads/ckp-kofa-app.apk',
        __DIR__ . '/app-direct.apk',
        __DIR__ . '/ckp-kofa-app.apk',
        dirname(__DIR__) . '/public/mobile/apk/ckp-kofa-app.apk',
        dirname(__DIR__) . '/app/apk/app-debug-androidTest.apk'
    ];
    
    $foundFile = null;
    foreach ($possibleLocations as $location) {
        if (file_exists($location)) {
            $foundFile = $location;
            break;
        }
    }
    
    if ($foundFile) {
        echo '<p>The APK file was found and is ready for download.</p>';
        echo '<p><a href="app.php?download=1" class="btn">Download APK Now</a></p>';
        echo '<p>File size: ' . round(filesize($foundFile) / 1024 / 1024, 2) . ' MB</p>';
    } else {
        echo '<p class="error">The APK file could not be found on the server.</p>';
        echo '<div class="debug">';
        echo '<h3>Debugging Information</h3>';
        echo '<p>The system checked these locations:</p>';
        echo '<ul>';
        foreach ($possibleLocations as $location) {
            echo '<li>' . $location . ' - ' . (file_exists($location) ? 'Found' : 'Not Found') . '</li>';
        }
        echo '</ul>';
        echo '<p>Server Information:</p>';
        echo '<ul>';
        echo '<li>Current directory: ' . __DIR__ . '</li>';
        echo '<li>PHP version: ' . phpversion() . '</li>';
        echo '<li>Server software: ' . $_SERVER['SERVER_SOFTWARE'] . '</li>';
        echo '</ul>';
        echo '</div>';
    }
    
    echo '</body></html>';
    exit;
}

// Download mode - try all possible locations
$possibleLocations = [
    __DIR__ . '/mobile/apk/ckp-kofa-app.apk',
    __DIR__ . '/downloads/ckp-kofa-app.apk',
    __DIR__ . '/app-direct.apk',
    __DIR__ . '/ckp-kofa-app.apk',
    dirname(__DIR__) . '/public/mobile/apk/ckp-kofa-app.apk',
    dirname(__DIR__) . '/app/apk/app-debug-androidTest.apk'
];

$foundFile = null;
foreach ($possibleLocations as $location) {
    if (file_exists($location)) {
        $foundFile = $location;
        break;
    }
}

if ($foundFile) {
    // Found the file, serve it for download
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
    header('Content-Length: ' . filesize($foundFile));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    readfile($foundFile);
    exit;
} else {
    // File not found
    header('HTTP/1.0 404 Not Found');
    echo "<h1>Error: APK file not found</h1>";
    echo "<p>The application could not be found in any of the expected locations.</p>";
    echo "<p><a href='app.php'>Return to download page</a></p>";
}
?> 