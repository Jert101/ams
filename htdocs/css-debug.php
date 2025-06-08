<?php
// This file tests CSS file accessibility

// Function to check if a URL is accessible
function url_exists($url) {
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200') !== false;
}

// Get the base URL
$baseUrl = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 
           'https://' : 'http://';
$baseUrl .= $_SERVER['HTTP_HOST'];

// Define the CSS files to check
$cssFiles = [
    '/css/style.css',
    '/css/responsive.css',
    '/css/sidebar-enhancements.css',
    '/css/auth-fix.css',
    '/build/assets/app-CoOQjfJF.css',
];

// Define other asset files to check
$assetFiles = [
    '/js/app.js',
    '/build/assets/app-DQS8sAPH.js',
    '/build/manifest.json',
];

// Check file system access
$filesystemFiles = [
    __DIR__ . '/css/style.css',
    __DIR__ . '/css/responsive.css',
    __DIR__ . '/css/sidebar-enhancements.css',
    __DIR__ . '/css/auth-fix.css',
    __DIR__ . '/build/assets/app-CoOQjfJF.css',
    __DIR__ . '/js/app.js',
    __DIR__ . '/build/assets/app-DQS8sAPH.js',
    __DIR__ . '/build/manifest.json',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS Debug Tool</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .success {
            color: green;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .info {
            background-color: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .test-area {
            margin-top: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .feature-card {
            /* Style that should appear if style.css is loaded */
        }
    </style>
</head>
<body>
    <h1>CSS and Asset Debug Tool</h1>
    
    <div class="info">
        <p>This tool checks if CSS and other asset files are accessible from the server. It performs both HTTP and filesystem checks.</p>
        <p>Server information: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
        <p>PHP version: <?php echo phpversion(); ?></p>
        <p>Document root: <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
        <p>Current directory: <?php echo __DIR__; ?></p>
    </div>
    
    <h2>CSS File Access Check</h2>
    <table>
        <tr>
            <th>File</th>
            <th>HTTP Access</th>
            <th>Filesystem Access</th>
        </tr>
        <?php foreach($cssFiles as $index => $file): ?>
        <tr>
            <td><?php echo $file; ?></td>
            <td class="<?php echo url_exists($baseUrl . $file) ? 'success' : 'error'; ?>">
                <?php echo url_exists($baseUrl . $file) ? 'Accessible' : 'Not accessible'; ?>
            </td>
            <td class="<?php echo file_exists($filesystemFiles[$index]) ? 'success' : 'error'; ?>">
                <?php echo file_exists($filesystemFiles[$index]) ? 'Exists' : 'Does not exist'; ?>
                <?php if(file_exists($filesystemFiles[$index])): ?>
                    (<?php echo filesize($filesystemFiles[$index]); ?> bytes)
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>Other Asset Files Check</h2>
    <table>
        <tr>
            <th>File</th>
            <th>HTTP Access</th>
            <th>Filesystem Access</th>
        </tr>
        <?php foreach($assetFiles as $index => $file): ?>
        <tr>
            <td><?php echo $file; ?></td>
            <td class="<?php echo url_exists($baseUrl . $file) ? 'success' : 'error'; ?>">
                <?php echo url_exists($baseUrl . $file) ? 'Accessible' : 'Not accessible'; ?>
            </td>
            <td class="<?php echo file_exists($filesystemFiles[$index + count($cssFiles)]) ? 'success' : 'error'; ?>">
                <?php echo file_exists($filesystemFiles[$index + count($cssFiles)]) ? 'Exists' : 'Does not exist'; ?>
                <?php if(file_exists($filesystemFiles[$index + count($cssFiles)])): ?>
                    (<?php echo filesize($filesystemFiles[$index + count($cssFiles)]); ?> bytes)
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>CSS Loading Test</h2>
    <p>The following box should have styling if the CSS files are loading correctly:</p>
    
    <div class="test-area">
        <div class="feature-card">
            <h3>Test Feature Card</h3>
            <p>This card should have styling if style.css is loaded correctly.</p>
        </div>
    </div>
    
    <div class="test-area">
        <h3>Manual CSS Test</h3>
        <p>Testing loading CSS manually:</p>
        <link rel="stylesheet" href="<?php echo $baseUrl; ?>/css/style.css">
        <div class="feature-card" style="margin-top: 20px;">
            <h3>Test With Direct CSS Include</h3>
            <p>This card should have styling if the manual CSS include works.</p>
        </div>
    </div>
</body>
</html> 