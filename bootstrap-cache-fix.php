<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Bootstrap Cache Fix</h1>";

// Define paths
$basePath = __DIR__;
$bootstrapPath = $basePath . '/bootstrap';
$bootstrapCachePath = $bootstrapPath . '/cache';

echo "<h2>Checking Bootstrap Directory</h2>";

// Check if bootstrap directory exists
if (file_exists($bootstrapPath)) {
    echo "<p style='color:green;'>Bootstrap directory exists</p>";
} else {
    echo "<p style='color:red;'>Bootstrap directory does not exist. Creating it...</p>";
    
    if (mkdir($bootstrapPath, 0777, true)) {
        echo "<p style='color:green;'>Successfully created bootstrap directory</p>";
    } else {
        echo "<p style='color:red;'>Failed to create bootstrap directory</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
}

// Check if bootstrap/cache directory exists
echo "<h2>Checking Bootstrap Cache Directory</h2>";

if (file_exists($bootstrapCachePath)) {
    echo "<p style='color:green;'>Bootstrap cache directory exists</p>";
    
    // Set permissions to 0777
    if (chmod($bootstrapCachePath, 0777)) {
        echo "<p style='color:green;'>Set permissions to 0777</p>";
    } else {
        echo "<p style='color:red;'>Failed to set permissions</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
} else {
    echo "<p style='color:red;'>Bootstrap cache directory does not exist. Creating it...</p>";
    
    if (mkdir($bootstrapCachePath, 0777, true)) {
        echo "<p style='color:green;'>Successfully created bootstrap cache directory</p>";
    } else {
        echo "<p style='color:red;'>Failed to create bootstrap cache directory</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
}

// Clear all cached files in bootstrap/cache
echo "<h2>Clearing Bootstrap Cache</h2>";

if (file_exists($bootstrapCachePath)) {
    $files = scandir($bootstrapCachePath);
    $count = 0;
    
    foreach ($files as $file) {
        if ($file != "." && $file != ".." && $file != ".gitignore") {
            $path = $bootstrapCachePath . "/" . $file;
            if (is_file($path)) {
                if (unlink($path)) {
                    echo "<p>Removed cached file: $file</p>";
                    $count++;
                } else {
                    echo "<p style='color:red;'>Failed to remove cached file: $file</p>";
                }
            }
        }
    }
    
    echo "<p>Removed $count cached files from bootstrap/cache</p>";
} else {
    echo "<p style='color:red;'>Bootstrap cache directory does not exist</p>";
}

// Create a .gitignore file in bootstrap/cache
echo "<h2>Creating .gitignore File</h2>";

if (file_exists($bootstrapCachePath)) {
    $gitignorePath = $bootstrapCachePath . '/.gitignore';
    $gitignoreContent = "*\n!.gitignore\n";
    
    if (file_put_contents($gitignorePath, $gitignoreContent)) {
        echo "<p style='color:green;'>Created .gitignore file in bootstrap/cache</p>";
    } else {
        echo "<p style='color:red;'>Failed to create .gitignore file</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
}

// Create a basic config.php file in bootstrap/cache
echo "<h2>Creating Basic Configuration Files</h2>";

$configFile = $bootstrapCachePath . '/config.php';
$configContent = <<<'EOT'
<?php return array (
  'app' => 
  array (
    'name' => 'Laravel',
    'env' => 'production',
    'debug' => false,
    'url' => 'http://localhost',
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'key' => 'base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=',
    'cipher' => 'AES-256-CBC',
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/storage/framework/cache/data',
      ),
    ),
    'prefix' => 'laravel_cache',
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => '/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'laravel_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => NULL,
    'http_only' => true,
    'same_site' => 'lax',
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/resources/views',
    ),
    'compiled' => '/storage/framework/views',
  ),
);
EOT;

// Update the paths to use the correct base path
$configContent = str_replace("'/storage/", "'" . $basePath . '/storage/', $configContent);
$configContent = str_replace("'/resources/", "'" . $basePath . '/resources/', $configContent);

if (file_put_contents($configFile, $configContent)) {
    echo "<p style='color:green;'>Created config.php file in bootstrap/cache</p>";
} else {
    echo "<p style='color:red;'>Failed to create config.php file</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

// Create a services.php file
$servicesFile = $bootstrapCachePath . '/services.php';
$servicesContent = <<<'EOT'
<?php return array (
  'providers' => 
  array (
    0 => 'Illuminate\\Cache\\CacheServiceProvider',
    1 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
    2 => 'Illuminate\\View\\ViewServiceProvider',
  ),
  'eager' => 
  array (
    0 => 'Illuminate\\Cache\\CacheServiceProvider',
    1 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
    2 => 'Illuminate\\View\\ViewServiceProvider',
  ),
  'deferred' => 
  array (
  ),
);
EOT;

if (file_put_contents($servicesFile, $servicesContent)) {
    echo "<p style='color:green;'>Created services.php file in bootstrap/cache</p>";
} else {
    echo "<p style='color:red;'>Failed to create services.php file</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

// Create a packages.php file
$packagesFile = $bootstrapCachePath . '/packages.php';
$packagesContent = "<?php return array (\n);";

if (file_put_contents($packagesFile, $packagesContent)) {
    echo "<p style='color:green;'>Created packages.php file in bootstrap/cache</p>";
} else {
    echo "<p style='color:red;'>Failed to create packages.php file</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

// Test if the bootstrap/cache directory is writable
echo "<h2>Testing Write Permissions</h2>";

$testFile = $bootstrapCachePath . '/test-' . time() . '.tmp';
$testContent = "This is a test file created at " . date('Y-m-d H:i:s');

if (file_put_contents($testFile, $testContent)) {
    echo "<p style='color:green;'>Successfully created test file in bootstrap/cache</p>";
    
    // Clean up
    if (unlink($testFile)) {
        echo "<p style='color:green;'>Successfully removed test file</p>";
    } else {
        echo "<p style='color:red;'>Failed to remove test file</p>";
    }
} else {
    echo "<p style='color:red;'>Failed to create test file in bootstrap/cache</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

echo "<h2>Next Steps</h2>";
echo "<p>The bootstrap/cache directory has been fixed and basic configuration files have been created.</p>";
echo "<p>Try accessing your site again. If you still see the 'Please provide a valid cache path' error, run the direct-cache-fix.php script.</p>";

echo "<p><a href='direct-cache-fix.php'>Run Direct Cache Fix</a></p>";
echo "<p><a href='/'>Return to your site</a></p>";
?> 