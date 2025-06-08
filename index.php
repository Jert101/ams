<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * This file allows you to run Laravel on shared hosting by pointing
 * the web root to this file instead of the 'public' directory
 */

// Define the public path
$publicPath = __DIR__ . '/public';

// Check if we're on production (shared hosting)
$isProduction = (strpos($_SERVER['HTTP_HOST'] ?? '', 'ckpkofa-network.ct.ws') !== false);

// For debugging
$debug = isset($_GET['debug']) && $_GET['debug'] === 'true';
if ($debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    echo "<h1>Debug Information</h1>";
    echo "<p>Current directory: " . __DIR__ . "</p>";
    echo "<p>Public path: " . $publicPath . "</p>";
    echo "<p>Is production: " . ($isProduction ? 'Yes' : 'No') . "</p>";
    echo "<p>REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</p>";
    echo "<p>SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Not set') . "</p>";
    echo "<p>DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . "</p>";
}

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

// Set proper asset URL for production
if ($isProduction) {
    putenv('APP_URL=https://ckpkofa-network.ct.ws');
    putenv('ASSET_URL=https://ckpkofa-network.ct.ws');
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