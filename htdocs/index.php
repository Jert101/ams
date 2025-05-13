<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 */

// Check if the request is for a specific file in the public directory
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$publicPath = __DIR__ . '/public';

// Check for direct file access (like css, js, images)
if ($uri !== '/' && file_exists($publicPath . $uri)) {
    // If it's a direct file request, return the file
    return false;
}

// Otherwise, include the Laravel framework bootstrap
require_once __DIR__ . '/public/index.php'; 