<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the image filename from query string or use a default
$filename = isset($_GET['image']) ? $_GET['image'] : '1749806707-684bee7378278.png';

// Define paths to check
$paths = [
    __DIR__ . '/../profile-photos/' . $filename,
    __DIR__ . '/profile-photos/' . $filename,
    __DIR__ . '/storage/profile-photos/' . $filename,
    __DIR__ . '/img/kofa.png'
];

// HTML header
echo '<!DOCTYPE html>
<html>
<head>
    <title>Image Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-container { margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        img { max-width: 200px; max-height: 200px; border: 1px solid #ddd; }
        pre { background: #f5f5f5; padding: 10px; overflow: auto; }
    </style>
</head>
<body>
    <h1>Image Loading Test</h1>
    <p>Testing access to profile photo: <strong>' . htmlspecialchars($filename) . '</strong></p>
    
    <form method="get">
        <label for="image">Test another image:</label>
        <input type="text" name="image" id="image" value="' . htmlspecialchars($filename) . '">
        <button type="submit">Test</button>
    </form>
    <hr>';

// Check if files exist in various locations
echo '<h2>File Existence Check</h2>';

foreach ($paths as $path) {
    echo '<div class="test-container">';
    echo '<h3>Path: ' . htmlspecialchars($path) . '</h3>';
    
    if (file_exists($path)) {
        echo '<p class="success">File EXISTS at this location</p>';
        echo '<p>File size: ' . filesize($path) . ' bytes</p>';
        echo '<p>Last modified: ' . date('Y-m-d H:i:s', filemtime($path)) . '</p>';
    } else {
        echo '<p class="error">File does NOT exist at this location</p>';
    }
    
    echo '</div>';
}

// Test direct image loading
echo '<h2>Direct Image Loading Test</h2>';

// Test URLs
$urls = [
    'https://ckpkofa-network.ct.ws/profile-photos/' . $filename,
    '/profile-photos/' . $filename,
    '/public/profile-photos/' . $filename,
    '/storage/profile-photos/' . $filename,
    '/public/storage/profile-photos/' . $filename,
    '/img/kofa.png'
];

foreach ($urls as $url) {
    echo '<div class="test-container">';
    echo '<h3>URL: ' . htmlspecialchars($url) . '</h3>';
    
    // Try to get headers
    $fullUrl = strpos($url, 'http') === 0 ? $url : 'https://ckpkofa-network.ct.ws' . $url;
    
    echo '<p>Testing: <a href="' . htmlspecialchars($fullUrl) . '" target="_blank">' . htmlspecialchars($fullUrl) . '</a></p>';
    
    // Add image tag
    echo '<p>Image tag test:</p>';
    echo '<img src="' . htmlspecialchars($fullUrl) . '" alt="Test image" onerror="this.parentNode.innerHTML += \'<p class=\\\'error\\\'>Image failed to load</p>\'">';
    
    echo '</div>';
}

// End HTML
echo '</body></html>'; 