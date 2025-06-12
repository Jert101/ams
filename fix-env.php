<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Laravel .env Configuration Fix</h1>";

// Define paths
$basePath = dirname(__DIR__);
$envPath = $basePath . '/.env';
$envExamplePath = $basePath . '/.env.example';

echo "<h2>Environment File Check</h2>";

// Check if .env file exists
if (file_exists($envPath)) {
    echo "<p style='color:green;'>.env file exists</p>";
    
    // Read the .env file
    $envContent = file_get_contents($envPath);
    
    // Check cache configuration
    if (strpos($envContent, 'CACHE_DRIVER=') !== false) {
        echo "<p style='color:green;'>CACHE_DRIVER configuration found in .env</p>";
        
        // Update cache driver to file if it's not already
        if (!preg_match('/CACHE_DRIVER=file/', $envContent)) {
            echo "<p style='color:orange;'>CACHE_DRIVER is not set to file</p>";
            echo "<p>Updating CACHE_DRIVER to file...</p>";
            
            $envContent = preg_replace('/CACHE_DRIVER=.*/', 'CACHE_DRIVER=file', $envContent);
            
            if (file_put_contents($envPath, $envContent)) {
                echo "<p style='color:green;'>Successfully updated CACHE_DRIVER to file</p>";
            } else {
                echo "<p style='color:red;'>Failed to update CACHE_DRIVER</p>";
                echo "<p>Error: " . error_get_last()['message'] . "</p>";
            }
        } else {
            echo "<p style='color:green;'>CACHE_DRIVER is already set to file</p>";
        }
    } else {
        echo "<p style='color:orange;'>CACHE_DRIVER configuration not found in .env</p>";
        echo "<p>Adding CACHE_DRIVER=file to .env...</p>";
        
        $envContent .= "\n# Cache Configuration\nCACHE_DRIVER=file\n";
        
        if (file_put_contents($envPath, $envContent)) {
            echo "<p style='color:green;'>Successfully added CACHE_DRIVER=file to .env</p>";
        } else {
            echo "<p style='color:red;'>Failed to add CACHE_DRIVER to .env</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    }
    
    // Check session configuration
    if (strpos($envContent, 'SESSION_DRIVER=') !== false) {
        echo "<p style='color:green;'>SESSION_DRIVER configuration found in .env</p>";
        
        // Update session driver to file if it's not already
        if (!preg_match('/SESSION_DRIVER=file/', $envContent)) {
            echo "<p style='color:orange;'>SESSION_DRIVER is not set to file</p>";
            echo "<p>Updating SESSION_DRIVER to file...</p>";
            
            $envContent = preg_replace('/SESSION_DRIVER=.*/', 'SESSION_DRIVER=file', $envContent);
            
            if (file_put_contents($envPath, $envContent)) {
                echo "<p style='color:green;'>Successfully updated SESSION_DRIVER to file</p>";
            } else {
                echo "<p style='color:red;'>Failed to update SESSION_DRIVER</p>";
                echo "<p>Error: " . error_get_last()['message'] . "</p>";
            }
        } else {
            echo "<p style='color:green;'>SESSION_DRIVER is already set to file</p>";
        }
    } else {
        echo "<p style='color:orange;'>SESSION_DRIVER configuration not found in .env</p>";
        echo "<p>Adding SESSION_DRIVER=file to .env...</p>";
        
        $envContent .= "\n# Session Configuration\nSESSION_DRIVER=file\n";
        
        if (file_put_contents($envPath, $envContent)) {
            echo "<p style='color:green;'>Successfully added SESSION_DRIVER=file to .env</p>";
        } else {
            echo "<p style='color:red;'>Failed to add SESSION_DRIVER to .env</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    }
    
    // Check view configuration
    if (strpos($envContent, 'VIEW_COMPILED_PATH=') === false) {
        echo "<p style='color:orange;'>VIEW_COMPILED_PATH configuration not found in .env</p>";
        echo "<p>Adding VIEW_COMPILED_PATH to .env...</p>";
        
        $viewPath = 'storage/framework/views';
        $envContent .= "\n# View Configuration\nVIEW_COMPILED_PATH=" . $viewPath . "\n";
        
        if (file_put_contents($envPath, $envContent)) {
            echo "<p style='color:green;'>Successfully added VIEW_COMPILED_PATH to .env</p>";
        } else {
            echo "<p style='color:red;'>Failed to add VIEW_COMPILED_PATH to .env</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    } else {
        echo "<p style='color:green;'>VIEW_COMPILED_PATH configuration found in .env</p>";
    }
    
} else {
    echo "<p style='color:red;'>.env file does not exist</p>";
    
    // Check if .env.example exists
    if (file_exists($envExamplePath)) {
        echo "<p style='color:green;'>.env.example file exists</p>";
        echo "<p>Creating .env file from .env.example...</p>";
        
        $envExampleContent = file_get_contents($envExamplePath);
        
        // Update cache and session drivers to file
        $envExampleContent = preg_replace('/CACHE_DRIVER=.*/', 'CACHE_DRIVER=file', $envExampleContent);
        $envExampleContent = preg_replace('/SESSION_DRIVER=.*/', 'SESSION_DRIVER=file', $envExampleContent);
        
        // Add VIEW_COMPILED_PATH if it doesn't exist
        if (strpos($envExampleContent, 'VIEW_COMPILED_PATH=') === false) {
            $viewPath = 'storage/framework/views';
            $envExampleContent .= "\n# View Configuration\nVIEW_COMPILED_PATH=" . $viewPath . "\n";
        }
        
        if (file_put_contents($envPath, $envExampleContent)) {
            echo "<p style='color:green;'>Successfully created .env file from .env.example</p>";
        } else {
            echo "<p style='color:red;'>Failed to create .env file</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    } else {
        echo "<p style='color:red;'>.env.example file does not exist</p>";
        echo "<p>Creating a basic .env file...</p>";
        
        $basicEnvContent = <<<'EOT'
APP_NAME=Laravel
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_APP_NAME="${APP_NAME}"
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

# View Configuration
VIEW_COMPILED_PATH=storage/framework/views
EOT;
        
        if (file_put_contents($envPath, $basicEnvContent)) {
            echo "<p style='color:green;'>Successfully created basic .env file</p>";
        } else {
            echo "<p style='color:red;'>Failed to create basic .env file</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    }
}

// Generate application key if it's not set
echo "<h2>Application Key Check</h2>";

$envContent = file_get_contents($envPath);
if (preg_match('/APP_KEY=\s*$/', $envContent) || strpos($envContent, 'APP_KEY=') === false) {
    echo "<p style='color:orange;'>APP_KEY is not set</p>";
    echo "<p>Generating a new application key...</p>";
    
    // Generate a random 32-character key
    $key = 'base64:' . base64_encode(random_bytes(32));
    
    // Update the .env file with the new key
    if (strpos($envContent, 'APP_KEY=') !== false) {
        $envContent = preg_replace('/APP_KEY=.*/', 'APP_KEY=' . $key, $envContent);
    } else {
        $envContent = "APP_KEY=" . $key . "\n" . $envContent;
    }
    
    if (file_put_contents($envPath, $envContent)) {
        echo "<p style='color:green;'>Successfully generated and set APP_KEY</p>";
    } else {
        echo "<p style='color:red;'>Failed to set APP_KEY</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
} else {
    echo "<p style='color:green;'>APP_KEY is already set</p>";
}

echo "<h2>Cache Configuration in config/cache.php</h2>";

// Check if config/cache.php exists
$cachePath = $basePath . '/config/cache.php';
if (file_exists($cachePath)) {
    echo "<p style='color:green;'>config/cache.php file exists</p>";
    
    // Read the cache configuration file
    $cacheConfig = file_get_contents($cachePath);
    
    // Check if the file cache path is set correctly
    if (strpos($cacheConfig, "'path' => storage_path('framework/cache/data')") !== false) {
        echo "<p style='color:green;'>File cache path is set correctly</p>";
    } else {
        echo "<p style='color:orange;'>File cache path may not be set correctly</p>";
        echo "<p>Please check config/cache.php and ensure the file cache path is set to storage_path('framework/cache/data')</p>";
    }
} else {
    echo "<p style='color:red;'>config/cache.php file does not exist</p>";
    echo "<p>This is a critical Laravel configuration file that should be present</p>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Try accessing your site again to see if the cache error is resolved</li>";
echo "<li>Run the fix-cache-paths.php script to ensure all cache directories exist and are writable</li>";
echo "<li>If you still see errors, check the Laravel logs in storage/logs</li>";
echo "</ol>";
?> 