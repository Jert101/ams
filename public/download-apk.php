<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define possible file locations
$possibleLocations = [
    __DIR__ . '/downloads/ckp-kofa-app.apk',
    __DIR__ . '/../public/downloads/ckp-kofa-app.apk',
    __DIR__ . '/../downloads/ckp-kofa-app.apk',
    '/home/ckpkofan/public_html/public/downloads/ckp-kofa-app.apk', // Common cPanel path
    '/var/www/html/public/downloads/ckp-kofa-app.apk', // Common Linux path
];

$file = null;

// Check each location
foreach ($possibleLocations as $location) {
    if (file_exists($location)) {
        $file = $location;
        break;
    }
}

// Debug information
echo "Checking for APK file:<br>";
echo "Current directory: " . __DIR__ . "<br>";

foreach ($possibleLocations as $index => $location) {
    echo "Location $index: $location - " . (file_exists($location) ? "EXISTS" : "NOT FOUND") . "<br>";
}

if ($file) {
    echo "<p>File found at: $file</p>";
    echo "<p>File size: " . filesize($file) . " bytes</p>";
    
    echo "<p><a href='#' id='downloadLink' class='btn btn-success'>Click to Download APK</a></p>";
    
    echo "<script>
    document.getElementById('downloadLink').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = 'download-apk.php?download=true';
    });
    </script>";
    
    // If download parameter is set, serve the file
    if (isset($_GET['download']) && $_GET['download'] === 'true') {
        // Set the appropriate headers
        header('Content-Type: application/vnd.android.package-archive');
        header('Content-Disposition: attachment; filename="ckp-kofa-app.apk"');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: no-cache');
        
        // Read the file and output it to the browser
        readfile($file);
        exit;
    }
} else {
    echo "<p style='color: red; font-weight: bold;'>File not found in any of the checked locations.</p>";
    echo "<p>Please make sure the APK file has been uploaded to the server in the correct location.</p>";
    
    // Check if the downloads directory exists
    $downloadsDir = __DIR__ . '/downloads';
    echo "Downloads directory (" . $downloadsDir . "): " . (is_dir($downloadsDir) ? "EXISTS" : "NOT FOUND") . "<br>";
    
    // If the directory exists, list its contents
    if (is_dir($downloadsDir)) {
        echo "<p>Contents of downloads directory:</p>";
        echo "<ul>";
        $files = scandir($downloadsDir);
        foreach ($files as $f) {
            if ($f != "." && $f != "..") {
                echo "<li>$f</li>";
            }
        }
        echo "</ul>";
    }
}
?> 