<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 * 
 * Redirect to public/index.php for InfinityFree hosting
 */

// Define the path to the public directory
$publicPath = __DIR__ . '/public';

// Check if the file exists
if (file_exists($publicPath . '/index.php')) {
    // Change to the public directory
    chdir($publicPath);
    
    // Include the public/index.php file
    require_once $publicPath . '/index.php';
} else {
    // If public/index.php doesn't exist, show an error
    die('Laravel public/index.php file not found. Please upload your Laravel application correctly.');
} 