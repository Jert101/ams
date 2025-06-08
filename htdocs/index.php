<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 */

define('LARAVEL_START', microtime(true));

// Function to serve fallback page in case of errors
function serve_fallback() {
    $fallbackFile = __DIR__ . '/index-fallback.html';
    if (file_exists($fallbackFile)) {
        readfile($fallbackFile);
    } else {
        echo '<h1>Site Maintenance</h1>';
        echo '<p>The site is currently undergoing maintenance. Please check back soon.</p>';
    }
    exit;
}

// Check if this is the homepage and if we should try the direct approach
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] === '/' && 
    isset($_SERVER['HTTP_USER_AGENT']) && 
    (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false ||
     strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false ||
     strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false ||
     strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false)) {
    // For mobile devices, serve direct page immediately
    include __DIR__ . '/direct-index.php';
    exit;
}

// Check if a specific CSS/JS asset is being requested
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $requestUri)) {
    // This is a static asset request - let the web server handle it
    return false;
}

try {
    // Laravel core bootstrapping
    if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
        throw new Exception('Vendor autoload file not found. Please run "composer install".');
    }
    require __DIR__.'/../vendor/autoload.php';

    if (!file_exists(__DIR__.'/../bootstrap/app.php')) {
        throw new Exception('Bootstrap app file not found.');
    }
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    )->send();

    $kernel->terminate($request, $response);
} catch (Exception $e) {
    // Log the error
    error_log('Laravel Bootstrap Error: ' . $e->getMessage());
    
    // Serve fallback page
    serve_fallback();
} 