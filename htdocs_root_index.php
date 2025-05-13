<?php
/**
 * Redirector to Laravel application in subfolder
 */

// The folder where your Laravel application is located
$appFolder = 'ams';

// Redirect all requests to the Laravel public/index.php file
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$publicPath = __DIR__ . '/' . $appFolder . '/public';

// Check for direct file access (like css, js, images)
if ($uri !== '/' && file_exists($publicPath . str_replace('/' . $appFolder, '', $uri))) {
    // If it's a direct file request in the public folder
    $file = $publicPath . str_replace('/' . $appFolder, '', $uri);
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    
    // Set appropriate content type
    $contentTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
    ];
    
    if (isset($contentTypes[$extension])) {
        header('Content-Type: ' . $contentTypes[$extension]);
    }
    
    readfile($file);
    exit;
}

// Otherwise, include the Laravel public/index.php
require_once __DIR__ . '/' . $appFolder . '/public/index.php'; 