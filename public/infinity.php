<?php
// Special InfinityFree APK download script
// This script uses absolute server paths and displays detailed diagnostic information

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug mode shows information about paths and server environment
if (isset($_GET['debug'])) {
    echo '<h1>InfinityFree APK Download Debug</h1>';
    
    // Get server paths
    echo '<h2>Server Paths</h2>';
    echo '<p>Script path: ' . __FILE__ . '</p>';
    echo '<p>Directory: ' . __DIR__ . '</p>';
    echo '<p>Document root: ' . $_SERVER['DOCUMENT_ROOT'] . '</p>';
    
    // Scan directory contents
    echo '<h2>Directory Contents</h2>';
    
    // Current directory
    echo '<h3>Current Directory</h3>';
    $files = scandir(__DIR__);
    echo '<ul>';
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo '<li>' . $file . ' - ' . (is_file(__DIR__ . '/' . $file) ? 'File' : 'Directory') . '</li>';
        }
    }
    echo '</ul>';
    
    // App directory if it exists
    if (is_dir(__DIR__ . '/app')) {
        echo '<h3>App Directory</h3>';
        $files = scandir(__DIR__ . '/app');
        echo '<ul>';
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo '<li>' . $file . '</li>';
            }
        }
        echo '</ul>';
    }
    
    // Mobile directory if it exists
    if (is_dir(__DIR__ . '/mobile')) {
        echo '<h3>Mobile Directory</h3>';
        $files = scandir(__DIR__ . '/mobile');
        echo '<ul>';
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo '<li>' . $file . '</li>';
            }
        }
        echo '</ul>';
        
        // Mobile/apk directory if it exists
        if (is_dir(__DIR__ . '/mobile/apk')) {
            echo '<h3>Mobile/APK Directory</h3>';
            $files = scandir(__DIR__ . '/mobile/apk');
            echo '<ul>';
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    echo '<li>' . $file . ' - ' . (is_file(__DIR__ . '/mobile/apk/' . $file) ? filesize(__DIR__ . '/mobile/apk/' . $file) . ' bytes' : 'Directory') . '</li>';
                }
            }
            echo '</ul>';
        }
    }
    
    // Check for various APK files
    echo '<h2>APK File Check</h2>';
    $apkFiles = [
        'base.apk',
        'ckp-kofa-app.apk',
        'mobile/apk/base.apk',
        'mobile/apk/ckp-kofa-app.apk',
        'app/apk/base.apk',
    ];
    
    echo '<ul>';
    foreach ($apkFiles as $apkFile) {
        $path = __DIR__ . '/' . $apkFile;
        echo '<li>' . $path . ' - ' . (file_exists($path) ? 'EXISTS (' . filesize($path) . ' bytes)' : 'NOT FOUND') . '</li>';
    }
    echo '</ul>';
    
    // Server information
    echo '<h2>Server Information</h2>';
    echo '<ul>';
    echo '<li>PHP Version: ' . phpversion() . '</li>';
    echo '<li>Server Software: ' . $_SERVER['SERVER_SOFTWARE'] . '</li>';
    echo '<li>Host Name: ' . gethostname() . '</li>';
    echo '</ul>';
    
    // Show a direct upload form as a fallback
    echo '<h2>Upload APK File</h2>';
    echo '<form action="infinity.php?upload=1" method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="apk_file" accept=".apk" required>';
    echo '<button type="submit">Upload APK</button>';
    echo '</form>';
    
    exit;
}

// Handle file upload
if (isset($_GET['upload']) && $_GET['upload'] == 1) {
    if (isset($_FILES['apk_file']) && $_FILES['apk_file']['error'] === UPLOAD_ERR_OK) {
        $uploadedFile = $_FILES['apk_file']['tmp_name'];
        $destination = __DIR__ . '/base.apk';
        
        if (move_uploaded_file($uploadedFile, $destination)) {
            echo '<h1>Upload Successful</h1>';
            echo '<p>APK file uploaded successfully. Size: ' . filesize($destination) . ' bytes</p>';
            echo '<p><a href="infinity.php?download=1">Download the APK</a></p>';
        } else {
            echo '<h1>Upload Failed</h1>';
            echo '<p>Could not move the uploaded file. Check server permissions.</p>';
        }
    } else {
        echo '<h1>Upload Error</h1>';
        echo '<p>Error code: ' . $_FILES['apk_file']['error'] . '</p>';
    }
    exit;
}

// Create a download page if not in download mode
if (!isset($_GET['download'])) {
    echo '<!DOCTYPE html>
<html>
<head>
    <title>CKP-KofA Mobile App Download</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #218838;
        }
        .alt-btn {
            display: inline-block;
            background-color: #6c757d;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
            font-size: 14px;
        }
        .alt-btn:hover {
            background-color: #5a6268;
        }
        .alert {
            background-color: #cce5ff;
            border: 1px solid #b8daff;
            color: #004085;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>CKP-KofA Mobile App Download</h1>
    
    <div class="alert">
        <h3>Downloads Temporarily Disabled</h3>
        <p>The mobile application is currently unavailable for download.</p>
        <p>Please check back later or contact the administrator for assistance.</p>
    </div>
    
    <p style="margin-top: 40px;"><a href="/">Return to Home</a></p>
</body>
</html>';
    exit;
}

// If download parameter is set, show a message that downloads are disabled
echo '<!DOCTYPE html>
<html>
<head>
    <title>Download Unavailable</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .alert {
            background-color: #cce5ff;
            border: 1px solid #b8daff;
            color: #004085;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="alert">
        <h2>Downloads Temporarily Disabled</h2>
        <p>The mobile application is currently unavailable for download.</p>
        <p>Please check back later or contact the administrator for assistance.</p>
    </div>
    <p><a href="/">Return to Home</a></p>
</body>
</html>';
exit;

// Try to find the APK file in various locations
$possibleLocations = [
    __DIR__ . '/base.apk',
    __DIR__ . '/ckp-kofa-app.apk',
    __DIR__ . '/mobile/apk/base.apk',
    __DIR__ . '/mobile/apk/ckp-kofa-app.apk',
    __DIR__ . '/app/apk/base.apk'
];

$foundFile = null;
foreach ($possibleLocations as $location) {
    if (file_exists($location)) {
        $foundFile = $location;
        break;
    }
}

// If the file is found, serve it for download
if ($foundFile) {
    // Set appropriate headers
    header('Content-Type: application/vnd.android.package-archive');
    header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
    header('Content-Length: ' . filesize($foundFile));
    header('Pragma: public');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
    
    // Output the file
    readfile($foundFile);
    exit;
} else {
    // File not found - create one with a message
    echo '<h1>APK File Not Found</h1>';
    echo '<p>The application file could not be found on the server.</p>';
    echo '<p>Please check the server configuration or <a href="infinity.php?debug=1">view debug information</a>.</p>';
}
?> 