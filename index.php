<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * This file allows you to run Laravel on shared hosting by pointing
 * the web root to this file instead of the 'public' directory
 */

// Define the public path
$publicPath = __DIR__ . '/public';

// Load the autoloader
$autoloader = __DIR__ . '/vendor/autoload.php';

// Check if the autoloader exists
if (!file_exists($autoloader)) {
    die('Composer autoloader not found. Please run "composer install".');
}

// Include the composer autoloader
require $autoloader;

// Load the environment file if it exists
$env = __DIR__ . '/.env';
if (file_exists($env)) {
    // Load environment variables - this is done inside Laravel's bootstrap
}

// Run the application
if (file_exists($publicPath . '/index.php')) {
    // Change directory to ensure relative paths work as expected
    chdir($publicPath);
    
    // Define public path for Laravel
    $_SERVER['SCRIPT_FILENAME'] = $publicPath . '/index.php';
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    
    // Include the public/index.php file
    require $publicPath . '/index.php';
} else {
    die('Laravel public/index.php file not found. Please upload your Laravel application correctly.');
}