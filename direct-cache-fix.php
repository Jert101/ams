<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Direct Cache Path Fix</h1>";

// Define paths
$basePath = __DIR__;
$storagePath = $basePath . '/storage';
$frameworkPath = $storagePath . '/framework';
$cachePath = $frameworkPath . '/cache';
$cacheDataPath = $cachePath . '/data';
$bootstrapPath = $basePath . '/bootstrap';
$bootstrapCachePath = $bootstrapPath . '/cache';

echo "<h2>Creating Critical Cache Directories</h2>";

// Array of directories to create with 0777 permissions
$directories = [
    $storagePath,
    $frameworkPath,
    $cachePath,
    $cacheDataPath,
    $bootstrapPath,
    $bootstrapCachePath,
    $frameworkPath . '/sessions',
    $frameworkPath . '/views',
    $storagePath . '/logs',
];

foreach ($directories as $dir) {
    echo "<h3>Processing: $dir</h3>";
    
    if (file_exists($dir)) {
        echo "<p>Directory exists. Setting permissions to 0777...</p>";
        
        if (@chmod($dir, 0777)) {
            echo "<p style='color:green;'>Successfully set permissions to 0777</p>";
        } else {
            echo "<p style='color:red;'>Failed to set permissions</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    } else {
        echo "<p>Directory does not exist. Creating with 0777 permissions...</p>";
        
        if (@mkdir($dir, 0777, true)) {
            echo "<p style='color:green;'>Successfully created directory</p>";
        } else {
            echo "<p style='color:red;'>Failed to create directory</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    }
    
    // Verify directory is writable
    if (file_exists($dir)) {
        if (is_writable($dir)) {
            echo "<p style='color:green;'>Directory is writable</p>";
        } else {
            echo "<p style='color:red;'>Directory is NOT writable</p>";
        }
    }
}

// Create .gitignore files in each directory
echo "<h2>Creating .gitignore Files</h2>";

foreach ($directories as $dir) {
    if (file_exists($dir)) {
        $gitignorePath = $dir . '/.gitignore';
        $gitignoreContent = "*\n!.gitignore\n";
        
        if (file_put_contents($gitignorePath, $gitignoreContent)) {
            echo "<p style='color:green;'>Created .gitignore in $dir</p>";
        } else {
            echo "<p style='color:red;'>Failed to create .gitignore in $dir</p>";
        }
    }
}

// Check and update .env file
echo "<h2>Checking .env File</h2>";

$envPath = $basePath . '/.env';
if (file_exists($envPath)) {
    echo "<p>.env file exists. Checking cache configuration...</p>";
    
    $envContent = file_get_contents($envPath);
    
    // Check if CACHE_DRIVER is set to file
    if (strpos($envContent, 'CACHE_DRIVER=file') !== false) {
        echo "<p style='color:green;'>CACHE_DRIVER is set to file</p>";
    } else {
        echo "<p>CACHE_DRIVER is not set to file. Updating...</p>";
        
        // Replace any existing CACHE_DRIVER line
        if (preg_match('/CACHE_DRIVER=.*/', $envContent)) {
            $envContent = preg_replace('/CACHE_DRIVER=.*/', 'CACHE_DRIVER=file', $envContent);
        } else {
            // Add CACHE_DRIVER=file if it doesn't exist
            $envContent .= "\nCACHE_DRIVER=file\n";
        }
        
        if (file_put_contents($envPath, $envContent)) {
            echo "<p style='color:green;'>Successfully updated CACHE_DRIVER to file</p>";
        } else {
            echo "<p style='color:red;'>Failed to update .env file</p>";
        }
    }
    
    // Check if VIEW_COMPILED_PATH is set
    if (strpos($envContent, 'VIEW_COMPILED_PATH=') !== false) {
        echo "<p style='color:green;'>VIEW_COMPILED_PATH is set</p>";
    } else {
        echo "<p>VIEW_COMPILED_PATH is not set. Adding it...</p>";
        
        $envContent .= "\nVIEW_COMPILED_PATH=storage/framework/views\n";
        
        if (file_put_contents($envPath, $envContent)) {
            echo "<p style='color:green;'>Successfully added VIEW_COMPILED_PATH</p>";
        } else {
            echo "<p style='color:red;'>Failed to update .env file</p>";
        }
    }
} else {
    echo "<p style='color:red;'>.env file does not exist. Creating basic .env file...</p>";
    
    $basicEnvContent = <<<'EOT'
APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

CACHE_DRIVER=file
SESSION_DRIVER=file
VIEW_COMPILED_PATH=storage/framework/views
EOT;
    
    if (file_put_contents($envPath, $basicEnvContent)) {
        echo "<p style='color:green;'>Successfully created basic .env file</p>";
    } else {
        echo "<p style='color:red;'>Failed to create .env file</p>";
    }
}

// Check and update config/cache.php
echo "<h2>Checking Cache Configuration</h2>";

$cachConfigPath = $basePath . '/config/cache.php';
if (file_exists($cachConfigPath)) {
    echo "<p>config/cache.php exists. Checking configuration...</p>";
    
    $cacheConfig = file_get_contents($cachConfigPath);
    
    // Create a backup of the original file
    $backupPath = $cachConfigPath . '.backup';
    if (!file_exists($backupPath)) {
        if (copy($cachConfigPath, $backupPath)) {
            echo "<p style='color:green;'>Created backup of config/cache.php</p>";
        } else {
            echo "<p style='color:red;'>Failed to create backup of config/cache.php</p>";
        }
    }
    
    // Check if the file cache path is set correctly
    if (strpos($cacheConfig, "'path' => storage_path('framework/cache/data')") !== false) {
        echo "<p style='color:green;'>File cache path is set correctly</p>";
    } else {
        echo "<p style='color:orange;'>File cache path may not be set correctly. Attempting to fix...</p>";
        
        // This is a simple replacement that might not work in all cases
        // but it's worth trying for common Laravel configurations
        $cacheConfig = preg_replace(
            "/'path' => (.*?),/",
            "'path' => storage_path('framework/cache/data'),",
            $cacheConfig
        );
        
        if (file_put_contents($cachConfigPath, $cacheConfig)) {
            echo "<p style='color:green;'>Updated cache path in config/cache.php</p>";
        } else {
            echo "<p style='color:red;'>Failed to update config/cache.php</p>";
        }
    }
} else {
    echo "<p style='color:red;'>config/cache.php does not exist. This is a critical Laravel configuration file.</p>";
    echo "<p>Creating a basic cache.php file...</p>";
    
    $basicCacheConfig = <<<'EOT'
<?php

use Illuminate\Support\Str;

return [
    'default' => env('CACHE_DRIVER', 'file'),
    'stores' => [
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],
    ],
    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache'),
];
EOT;
    
    // Create the config directory if it doesn't exist
    $configDir = $basePath . '/config';
    if (!file_exists($configDir)) {
        if (mkdir($configDir, 0777, true)) {
            echo "<p style='color:green;'>Created config directory</p>";
        } else {
            echo "<p style='color:red;'>Failed to create config directory</p>";
        }
    }
    
    if (file_put_contents($cachConfigPath, $basicCacheConfig)) {
        echo "<p style='color:green;'>Created basic config/cache.php file</p>";
    } else {
        echo "<p style='color:red;'>Failed to create config/cache.php file</p>";
    }
}

// Create a cache test file
echo "<h2>Testing Cache</h2>";

$testFile = $cacheDataPath . '/test-' . time() . '.tmp';
$testContent = "This is a test cache file created at " . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $testContent)) {
    echo "<p style='color:green;'>Successfully created test cache file</p>";
    
    // Try to read it back
    $readContent = file_get_contents($testFile);
    if ($readContent === $testContent) {
        echo "<p style='color:green;'>Successfully read test cache file</p>";
    } else {
        echo "<p style='color:red;'>Failed to read test cache file correctly</p>";
    }
    
    // Clean up
    if (unlink($testFile)) {
        echo "<p style='color:green;'>Successfully removed test cache file</p>";
    } else {
        echo "<p style='color:red;'>Failed to remove test cache file</p>";
    }
} else {
    echo "<p style='color:red;'>Failed to create test cache file</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

// Clear any existing cache files
echo "<h2>Clearing Existing Cache</h2>";

function clearDirectory($dir) {
    if (!is_dir($dir)) {
        return;
    }
    
    $files = scandir($dir);
    $count = 0;
    
    foreach ($files as $file) {
        if ($file != "." && $file != ".." && $file != ".gitignore") {
            $path = $dir . "/" . $file;
            if (is_dir($path)) {
                clearDirectory($path);
                if (@rmdir($path)) {
                    $count++;
                }
            } else {
                if (@unlink($path)) {
                    $count++;
                }
            }
        }
    }
    
    echo "<p>Removed $count items from $dir</p>";
}

$cacheDirs = [
    $cacheDataPath,
    $frameworkPath . '/sessions',
    $frameworkPath . '/views',
    $bootstrapCachePath,
];

foreach ($cacheDirs as $dir) {
    if (file_exists($dir)) {
        clearDirectory($dir);
    }
}

echo "<h2>Next Steps</h2>";
echo "<p>All critical cache directories have been created and configured. Try accessing your site again.</p>";
echo "<p>If you still see the 'Please provide a valid cache path' error, try the following:</p>";
echo "<ol>";
echo "<li>Restart your web server if possible</li>";
echo "<li>Check Laravel logs in storage/logs for more specific error messages</li>";
echo "<li>Make sure your web server has write permissions to all the storage directories</li>";
echo "<li>Try setting permissions to 0777 on all storage directories</li>";
echo "</ol>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 