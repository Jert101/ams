<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Laravel Cache Path Fix</h1>";

// Show server information
echo "<h2>Server Information</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Current directory: " . __DIR__ . "\n";
echo "</pre>";

// Define paths
$basePath = dirname(__DIR__);
$storagePath = $basePath . '/storage';
$cachePaths = [
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/views',
    $storagePath . '/framework/testing',
    $storagePath . '/logs',
    $storagePath . '/app/public',
    $storagePath . '/app/public/profile-photos',
];

// Check if storage directory exists
echo "<h2>Storage Directory Check</h2>";

if (file_exists($storagePath)) {
    echo "<p style='color:green;'>Storage directory exists at: $storagePath</p>";
} else {
    echo "<p style='color:red;'>Storage directory does not exist at: $storagePath</p>";
    echo "<p>Attempting to create it...</p>";
    
    if (mkdir($storagePath, 0777, true)) {
        echo "<p style='color:green;'>Successfully created storage directory</p>";
    } else {
        echo "<p style='color:red;'>Failed to create storage directory</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
}

// Create and fix permissions for all cache directories
echo "<h2>Creating Cache Directories</h2>";

foreach ($cachePaths as $path) {
    echo "<h3>Processing: $path</h3>";
    
    if (file_exists($path)) {
        echo "<p style='color:green;'>Directory exists</p>";
        
        // Check permissions
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "<p>Current permissions: $perms</p>";
        
        // Update permissions
        if (chmod($path, 0777)) {
            echo "<p style='color:green;'>Updated permissions to 0777</p>";
        } else {
            echo "<p style='color:red;'>Failed to update permissions</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    } else {
        echo "<p style='color:orange;'>Directory does not exist</p>";
        echo "<p>Attempting to create it...</p>";
        
        if (mkdir($path, 0777, true)) {
            echo "<p style='color:green;'>Successfully created directory with 0777 permissions</p>";
        } else {
            echo "<p style='color:red;'>Failed to create directory</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    }
    
    // Verify directory is writable
    if (file_exists($path)) {
        if (is_writable($path)) {
            echo "<p style='color:green;'>Directory is writable</p>";
        } else {
            echo "<p style='color:red;'>Directory is NOT writable</p>";
        }
    }
}

// Create a .gitignore file in each directory to ensure they're preserved in git
echo "<h2>Creating .gitignore Files</h2>";

foreach ($cachePaths as $path) {
    if (file_exists($path)) {
        $gitignorePath = $path . '/.gitignore';
        $gitignoreContent = "*\n!.gitignore\n";
        
        if (file_put_contents($gitignorePath, $gitignoreContent)) {
            echo "<p style='color:green;'>Created .gitignore in $path</p>";
        } else {
            echo "<p style='color:red;'>Failed to create .gitignore in $path</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    }
}

// Create a bootstrap/cache directory
$bootstrapCachePath = $basePath . '/bootstrap/cache';
echo "<h2>Bootstrap Cache Directory</h2>";

if (!file_exists($bootstrapCachePath)) {
    echo "<p>Bootstrap cache directory does not exist</p>";
    echo "<p>Attempting to create it...</p>";
    
    if (mkdir($bootstrapCachePath, 0777, true)) {
        echo "<p style='color:green;'>Successfully created bootstrap cache directory</p>";
    } else {
        echo "<p style='color:red;'>Failed to create bootstrap cache directory</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
} else {
    echo "<p style='color:green;'>Bootstrap cache directory exists</p>";
    
    // Update permissions
    if (chmod($bootstrapCachePath, 0777)) {
        echo "<p style='color:green;'>Updated bootstrap cache permissions to 0777</p>";
    } else {
        echo "<p style='color:red;'>Failed to update bootstrap cache permissions</p>";
    }
}

// Create a test file in each directory to verify write permissions
echo "<h2>Testing Write Permissions</h2>";

foreach ($cachePaths as $path) {
    if (file_exists($path)) {
        $testFile = $path . '/test-' . time() . '.txt';
        $testContent = "This is a test file created at " . date('Y-m-d H:i:s');
        
        echo "<h3>Testing write permissions in: $path</h3>";
        
        if (file_put_contents($testFile, $testContent)) {
            echo "<p style='color:green;'>Successfully created test file</p>";
            
            // Clean up
            if (unlink($testFile)) {
                echo "<p style='color:green;'>Successfully removed test file</p>";
            } else {
                echo "<p style='color:red;'>Failed to remove test file</p>";
            }
        } else {
            echo "<p style='color:red;'>Failed to create test file</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    }
}

// Check if .env file exists and has proper cache configuration
$envPath = $basePath . '/.env';
echo "<h2>Environment Configuration Check</h2>";

if (file_exists($envPath)) {
    echo "<p style='color:green;'>.env file exists</p>";
    
    // Read the .env file
    $envContent = file_get_contents($envPath);
    
    // Check if cache configuration exists
    if (strpos($envContent, 'CACHE_DRIVER=') !== false) {
        echo "<p style='color:green;'>CACHE_DRIVER configuration found in .env</p>";
    } else {
        echo "<p style='color:orange;'>CACHE_DRIVER configuration not found in .env</p>";
        echo "<p>Consider adding CACHE_DRIVER=file to your .env file</p>";
    }
} else {
    echo "<p style='color:red;'>.env file does not exist</p>";
    echo "<p>This could be causing your cache issues</p>";
}

// Clear Laravel's cache
echo "<h2>Clearing Laravel Cache</h2>";

// Create a simple script to clear the cache
$clearCacheScript = <<<'EOT'
<?php
$basePath = __DIR__;
$cachePaths = [
    $basePath . '/storage/framework/cache/data',
    $basePath . '/storage/framework/sessions',
    $basePath . '/storage/framework/views',
    $basePath . '/bootstrap/cache',
];

function clearDirectory($dir) {
    if (!is_dir($dir)) {
        return;
    }
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file != "." && $file != ".." && $file != ".gitignore") {
            $path = $dir . "/" . $file;
            if (is_dir($path)) {
                clearDirectory($path);
                @rmdir($path);
            } else {
                @unlink($path);
            }
        }
    }
}

foreach ($cachePaths as $path) {
    if (is_dir($path)) {
        clearDirectory($path);
        echo "Cleared: $path\n";
    }
}

echo "Cache cleared successfully!\n";
EOT;

$clearCacheScriptPath = $basePath . '/clear-cache.php';
if (file_put_contents($clearCacheScriptPath, $clearCacheScript)) {
    echo "<p style='color:green;'>Created cache clearing script at: $clearCacheScriptPath</p>";
    echo "<p>You can run this script to clear Laravel's cache</p>";
    
    // Try to run it
    echo "<p>Attempting to run the cache clearing script...</p>";
    $output = [];
    exec('php ' . escapeshellarg($clearCacheScriptPath), $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "<p style='color:green;'>Successfully ran cache clearing script</p>";
        echo "<pre>";
        echo implode("\n", $output);
        echo "</pre>";
    } else {
        echo "<p style='color:red;'>Failed to run cache clearing script</p>";
    }
} else {
    echo "<p style='color:red;'>Failed to create cache clearing script</p>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Try accessing your site again to see if the cache error is resolved</li>";
echo "<li>If you still see cache errors, try running the clear-cache.php script</li>";
echo "<li>Make sure your .env file has CACHE_DRIVER=file</li>";
echo "<li>Check that all the storage directories have 777 permissions</li>";
echo "</ol>";
?> 