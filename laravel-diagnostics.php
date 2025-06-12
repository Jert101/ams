<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Laravel Diagnostics Tool</h1>";

// Show server information
echo "<h2>Server Information</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Current directory: " . __DIR__ . "\n";
echo "Operating System: " . PHP_OS . "\n";
echo "Max execution time: " . ini_get('max_execution_time') . " seconds\n";
echo "Memory limit: " . ini_get('memory_limit') . "\n";
echo "Upload max filesize: " . ini_get('upload_max_filesize') . "\n";
echo "Post max size: " . ini_get('post_max_size') . "\n";
echo "</pre>";

// Define paths
$basePath = dirname(__DIR__);
$storagePath = $basePath . '/storage';
$bootstrapPath = $basePath . '/bootstrap';
$publicPath = $basePath . '/public';
$envPath = $basePath . '/.env';

// Check critical directories
echo "<h2>Critical Directory Check</h2>";
$criticalDirs = [
    'storage' => $storagePath,
    'storage/framework' => $storagePath . '/framework',
    'storage/framework/cache' => $storagePath . '/framework/cache',
    'storage/framework/cache/data' => $storagePath . '/framework/cache/data',
    'storage/framework/sessions' => $storagePath . '/framework/sessions',
    'storage/framework/views' => $storagePath . '/framework/views',
    'storage/logs' => $storagePath . '/logs',
    'bootstrap' => $bootstrapPath,
    'bootstrap/cache' => $bootstrapPath . '/cache',
    'public' => $publicPath,
    'public/storage' => $publicPath . '/storage',
];

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Directory</th><th>Exists</th><th>Permissions</th><th>Writable</th></tr>";

foreach ($criticalDirs as $name => $path) {
    echo "<tr>";
    echo "<td>$name</td>";
    
    if (file_exists($path)) {
        echo "<td style='color:green;'>Yes</td>";
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "<td>$perms</td>";
        
        if (is_writable($path)) {
            echo "<td style='color:green;'>Yes</td>";
        } else {
            echo "<td style='color:red;'>No</td>";
        }
    } else {
        echo "<td style='color:red;'>No</td>";
        echo "<td>-</td>";
        echo "<td>-</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

// Check .env file
echo "<h2>Environment Configuration</h2>";

if (file_exists($envPath)) {
    echo "<p style='color:green;'>.env file exists</p>";
    
    // Read the .env file
    $envContent = file_get_contents($envPath);
    
    // Check key configurations
    $configs = [
        'APP_KEY' => 'Application key',
        'APP_DEBUG' => 'Debug mode',
        'CACHE_DRIVER' => 'Cache driver',
        'SESSION_DRIVER' => 'Session driver',
        'DB_CONNECTION' => 'Database connection',
        'DB_HOST' => 'Database host',
        'DB_DATABASE' => 'Database name',
        'DB_USERNAME' => 'Database username',
        'VIEW_COMPILED_PATH' => 'View compiled path',
    ];
    
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Configuration</th><th>Status</th><th>Value</th></tr>";
    
    foreach ($configs as $key => $description) {
        echo "<tr>";
        echo "<td>$description</td>";
        
        if (preg_match("/$key=(.*?)(\n|$)/", $envContent, $matches)) {
            echo "<td style='color:green;'>Found</td>";
            $value = $matches[1];
            
            // Hide database password
            if ($key == 'DB_PASSWORD') {
                $value = '********';
            }
            
            echo "<td>$value</td>";
        } else {
            echo "<td style='color:red;'>Not found</td>";
            echo "<td>-</td>";
        }
        
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p style='color:red;'>.env file does not exist</p>";
}

// Check if Laravel is installed
echo "<h2>Laravel Installation Check</h2>";

$laravelFiles = [
    'artisan' => $basePath . '/artisan',
    'composer.json' => $basePath . '/composer.json',
    'app/Http/Kernel.php' => $basePath . '/app/Http/Kernel.php',
    'config/app.php' => $basePath . '/config/app.php',
];

$laravelInstalled = true;

foreach ($laravelFiles as $name => $path) {
    if (!file_exists($path)) {
        echo "<p style='color:red;'>Missing Laravel file: $name</p>";
        $laravelInstalled = false;
    }
}

if ($laravelInstalled) {
    echo "<p style='color:green;'>Laravel appears to be correctly installed</p>";
    
    // Check Laravel version
    if (file_exists($basePath . '/vendor/laravel/framework/src/Illuminate/Foundation/Application.php')) {
        $appContent = file_get_contents($basePath . '/vendor/laravel/framework/src/Illuminate/Foundation/Application.php');
        if (preg_match("/const VERSION = '(.*?)';/", $appContent, $matches)) {
            echo "<p>Laravel Version: " . $matches[1] . "</p>";
        } else {
            echo "<p>Could not determine Laravel version</p>";
        }
    } else {
        echo "<p>Could not determine Laravel version</p>";
    }
}

// Check for common Laravel issues
echo "<h2>Common Issues Check</h2>";

// Check storage symlink
$publicStoragePath = $publicPath . '/storage';
$storageAppPublicPath = $storagePath . '/app/public';

echo "<h3>Storage Symlink</h3>";

if (file_exists($publicStoragePath)) {
    if (is_link($publicStoragePath)) {
        echo "<p style='color:green;'>public/storage is a symlink</p>";
        echo "<p>Target: " . readlink($publicStoragePath) . "</p>";
        
        // Check if it's pointing to the correct location
        if (readlink($publicStoragePath) == $storageAppPublicPath) {
            echo "<p style='color:green;'>Symlink is pointing to the correct location</p>";
        } else {
            echo "<p style='color:red;'>Symlink is pointing to the wrong location</p>";
            echo "<p>Current target: " . readlink($publicStoragePath) . "</p>";
            echo "<p>Expected target: " . $storageAppPublicPath . "</p>";
        }
    } else {
        echo "<p style='color:orange;'>public/storage exists but is not a symlink</p>";
        echo "<p>It's a regular directory - this is okay if you're manually copying files</p>";
    }
} else {
    echo "<p style='color:red;'>public/storage does not exist</p>";
    echo "<p>This could cause issues with accessing uploaded files</p>";
}

// Check for common cache issues
echo "<h3>Cache Issues</h3>";

$cacheDataPath = $storagePath . '/framework/cache/data';
if (!file_exists($cacheDataPath)) {
    echo "<p style='color:red;'>Cache data directory does not exist: $cacheDataPath</p>";
    echo "<p>This could cause the 'Please provide a valid cache path' error</p>";
} else {
    if (!is_writable($cacheDataPath)) {
        echo "<p style='color:red;'>Cache data directory is not writable: $cacheDataPath</p>";
        echo "<p>This could cause cache-related errors</p>";
    } else {
        echo "<p style='color:green;'>Cache data directory exists and is writable</p>";
    }
}

// Check for bootstrap/cache issues
$bootstrapCachePath = $bootstrapPath . '/cache';
if (!file_exists($bootstrapCachePath)) {
    echo "<p style='color:red;'>Bootstrap cache directory does not exist: $bootstrapCachePath</p>";
    echo "<p>This could cause issues with configuration caching</p>";
} else {
    if (!is_writable($bootstrapCachePath)) {
        echo "<p style='color:red;'>Bootstrap cache directory is not writable: $bootstrapCachePath</p>";
        echo "<p>This could cause issues with configuration caching</p>";
    } else {
        echo "<p style='color:green;'>Bootstrap cache directory exists and is writable</p>";
    }
}

// Check for session issues
$sessionsPath = $storagePath . '/framework/sessions';
if (!file_exists($sessionsPath)) {
    echo "<p style='color:red;'>Sessions directory does not exist: $sessionsPath</p>";
    echo "<p>This could cause session-related errors</p>";
} else {
    if (!is_writable($sessionsPath)) {
        echo "<p style='color:red;'>Sessions directory is not writable: $sessionsPath</p>";
        echo "<p>This could cause session-related errors</p>";
    } else {
        echo "<p style='color:green;'>Sessions directory exists and is writable</p>";
    }
}

// Check for views issues
$viewsPath = $storagePath . '/framework/views';
if (!file_exists($viewsPath)) {
    echo "<p style='color:red;'>Views directory does not exist: $viewsPath</p>";
    echo "<p>This could cause view-related errors</p>";
} else {
    if (!is_writable($viewsPath)) {
        echo "<p style='color:red;'>Views directory is not writable: $viewsPath</p>";
        echo "<p>This could cause view-related errors</p>";
    } else {
        echo "<p style='color:green;'>Views directory exists and is writable</p>";
    }
}

// Check for logs issues
$logsPath = $storagePath . '/logs';
if (!file_exists($logsPath)) {
    echo "<p style='color:red;'>Logs directory does not exist: $logsPath</p>";
    echo "<p>This could cause logging-related errors</p>";
} else {
    if (!is_writable($logsPath)) {
        echo "<p style='color:red;'>Logs directory is not writable: $logsPath</p>";
        echo "<p>This could cause logging-related errors</p>";
    } else {
        echo "<p style='color:green;'>Logs directory exists and is writable</p>";
    }
}

// Check for profile photo issues
$profilePhotosPath = $storageAppPublicPath . '/profile-photos';
$publicProfilePhotosPath = $publicPath . '/storage/profile-photos';

echo "<h3>Profile Photos</h3>";

if (!file_exists($profilePhotosPath)) {
    echo "<p style='color:red;'>Profile photos directory does not exist: $profilePhotosPath</p>";
    echo "<p>This could cause issues with profile photo uploads</p>";
} else {
    if (!is_writable($profilePhotosPath)) {
        echo "<p style='color:red;'>Profile photos directory is not writable: $profilePhotosPath</p>";
        echo "<p>This could cause issues with profile photo uploads</p>";
    } else {
        echo "<p style='color:green;'>Profile photos directory exists and is writable</p>";
    }
}

if (!file_exists($publicProfilePhotosPath)) {
    echo "<p style='color:red;'>Public profile photos directory does not exist: $publicProfilePhotosPath</p>";
    echo "<p>This could cause issues with displaying profile photos</p>";
} else {
    if (!is_writable($publicProfilePhotosPath)) {
        echo "<p style='color:red;'>Public profile photos directory is not writable: $publicProfilePhotosPath</p>";
        echo "<p>This could cause issues with displaying profile photos</p>";
    } else {
        echo "<p style='color:green;'>Public profile photos directory exists and is writable</p>";
    }
}

// Provide fix buttons
echo "<h2>Fix Options</h2>";

echo "<div style='margin-bottom: 10px;'>";
echo "<a href='fix-cache-paths.php' style='display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Fix Cache Paths</a>";
echo "<a href='fix-env.php' style='display: inline-block; padding: 10px 20px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Fix .env Configuration</a>";
echo "<a href='simple-cache-clear.php' style='display: inline-block; padding: 10px 20px; background-color: #f44336; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Clear Cache</a>";
echo "<a href='public-symlink.php' style='display: inline-block; padding: 10px 20px; background-color: #ff9800; color: white; text-decoration: none; border-radius: 5px;'>Fix Storage Symlink</a>";
echo "</div>";

echo "<h2>Recommendations</h2>";
echo "<ol>";
echo "<li>Run the 'Fix Cache Paths' script to ensure all required directories exist with proper permissions</li>";
echo "<li>Run the 'Fix .env Configuration' script to ensure your environment configuration is correct</li>";
echo "<li>Run the 'Clear Cache' script to clear Laravel's cache</li>";
echo "<li>If you're having issues with profile photos, run the 'Fix Storage Symlink' script</li>";
echo "<li>After running these scripts, try accessing your site again</li>";
echo "</ol>";
?> 