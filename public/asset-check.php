<?php
header('Content-Type: text/html');
echo '<!DOCTYPE html>
<html>
<head>
    <title>Asset Path Diagnostic</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Asset Path Diagnostic</h1>';

// Server Information
echo '<h2>Server Information</h2>';
echo '<table>';
echo '<tr><th>Variable</th><th>Value</th></tr>';
echo '<tr><td>HTTP_HOST</td><td>' . ($_SERVER['HTTP_HOST'] ?? 'Not set') . '</td></tr>';
echo '<tr><td>REQUEST_URI</td><td>' . ($_SERVER['REQUEST_URI'] ?? 'Not set') . '</td></tr>';
echo '<tr><td>SCRIPT_FILENAME</td><td>' . ($_SERVER['SCRIPT_FILENAME'] ?? 'Not set') . '</td></tr>';
echo '<tr><td>DOCUMENT_ROOT</td><td>' . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . '</td></tr>';
echo '<tr><td>PHP Version</td><td>' . phpversion() . '</td></tr>';
echo '</table>';

// Check style.css file
$cssFile = __DIR__ . '/css/style.css';
$cssUrl = '/css/style.css';
echo '<h2>CSS File Check</h2>';
echo '<p>Checking for file: ' . $cssFile . '</p>';
if (file_exists($cssFile)) {
    echo '<p class="success">CSS file exists on disk!</p>';
} else {
    echo '<p class="error">CSS file does not exist on disk!</p>';
}

// Test URLs
$urls = [
    '/css/style.css',
    '/css/sidebar-enhancements.css',
    '/css/responsive.css',
    '/js/app.js'
];

echo '<h2>URL Access Check</h2>';
echo '<table>';
echo '<tr><th>URL</th><th>Status</th></tr>';

foreach ($urls as $url) {
    $fullUrl = 'http://' . $_SERVER['HTTP_HOST'] . $url;
    $headers = @get_headers($fullUrl);
    
    if ($headers && strpos($headers[0], '200') !== false) {
        echo '<tr><td>' . $url . '</td><td class="success">Accessible (200 OK)</td></tr>';
    } else {
        echo '<tr><td>' . $url . '</td><td class="error">Not accessible (' . ($headers[0] ?? 'Unknown error') . ')</td></tr>';
    }
}

echo '</table>';

// CSS Test
echo '<h2>CSS Loading Test</h2>';
echo '<div id="test-element" style="width: 200px; height: 200px; background-color: lightgray;">
    This box should have a red border if style.css is loaded correctly
</div>';

echo '<style>
    #test-element {
        border: 3px solid red;
    }
</style>';

echo '</body></html>';
?> 