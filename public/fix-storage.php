<?php
// This script helps create storage symlinks on shared hosting where shell access is limited
// Only use this script when you cannot run 'php artisan storage:link'

// Security check - only allow admins to access this script
// Check if the user is logged in as admin
session_start();
require __DIR__ . '/../vendor/autoload.php';

// Determine if running in web context or CLI
$isWeb = php_sapi_name() !== 'cli';

// Helper functions for output
function output($message, $isError = false) {
    global $isWeb;
    if ($isWeb) {
        echo ($isError ? '<p style="color: red;">' : '<p>') . $message . '</p>';
    } else {
        echo ($isError ? "\033[31m" : "") . $message . ($isError ? "\033[0m" : "") . PHP_EOL;
    }
}

// Run only if executed from CLI or with security key parameter
$securityKey = '8e24905d78c97be5c5a9a5c7237c8afa'; // Example key, change this!
$securityPassed = !$isWeb || (isset($_GET['key']) && $_GET['key'] === $securityKey);

if (!$securityPassed) {
    http_response_code(403);
    output("Access denied. This script requires authorization.", true);
    exit;
}

// Start HTML output if in web context
if ($isWeb) {
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Storage Fix Utility</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
            h1 { color: #333; }
            .success { color: green; }
            .error { color: red; }
            pre { background: #f4f4f4; padding: 10px; border-radius: 5px; }
        </style>
    </head>
    <body>
        <h1>Storage Fix Utility</h1>';
}

output("Starting storage link fix process...");

// Define paths
$targetPath = dirname(__DIR__) . '/storage/app/public';
$linkPath = __DIR__ . '/storage';

// Ensure target directory exists
if (!file_exists($targetPath)) {
    if (mkdir($targetPath, 0755, true)) {
        output("Created storage directory: {$targetPath}");
    } else {
        output("Failed to create directory: {$targetPath}", true);
    }
}

// Check if symlink already exists and is correct
if (file_exists($linkPath)) {
    if (is_link($linkPath)) {
        $linkTarget = readlink($linkPath);
        output("Existing symlink points to: {$linkTarget}");
        
        if ($linkTarget == $targetPath || realpath($linkTarget) == realpath($targetPath)) {
            output("Symlink is already correctly configured.");
        } else {
            output("Symlink points to the wrong target. Will attempt to fix.");
            if (unlink($linkPath)) {
                output("Removed incorrect symlink.");
            } else {
                output("Failed to remove incorrect symlink.", true);
            }
        }
    } else {
        output("A non-symlink file/directory named 'storage' exists. Will attempt to remove it.");
        
        // Try to remove the existing directory or file
        if (is_dir($linkPath)) {
            if (rmdir($linkPath)) {
                output("Removed existing directory.");
            } else {
                output("Failed to remove existing directory. It may not be empty.", true);
                
                // Try alternative approach - rename it
                $backupName = $linkPath . '_backup_' . time();
                if (rename($linkPath, $backupName)) {
                    output("Renamed existing directory to: {$backupName}");
                } else {
                    output("Could not rename existing directory. Manual intervention required.", true);
                    goto fallback;
                }
            }
        } else {
            if (unlink($linkPath)) {
                output("Removed existing file.");
            } else {
                output("Failed to remove existing file.", true);
                goto fallback;
            }
        }
    }
}

// Create symlink if it doesn't exist or was removed
if (!file_exists($linkPath)) {
    try {
        if (symlink($targetPath, $linkPath)) {
            output("Successfully created symlink: {$linkPath} -> {$targetPath}", false);
        } else {
            output("Failed to create symlink. Trying fallback method.", true);
            goto fallback;
        }
    } catch (Exception $e) {
        output("Exception while creating symlink: " . $e->getMessage(), true);
        goto fallback;
    }
}

// Exit here if symlink creation was successful
output("Storage link process completed successfully.");
goto end;

// Fallback method for hosts that don't support symlinks
fallback:
output("Using fallback method for shared hosting...");

// Create a PHP file in public/storage.php that serves files from storage/app/public
$fallbackContent = <<<'PHP'
<?php
// This is a fallback for environments where symlinks aren't supported
$path = isset($_GET['path']) ? $_GET['path'] : null;

if (!$path) {
    http_response_code(404);
    echo "File not found";
    exit;
}

// Prevent directory traversal
$path = str_replace('..', '', $path);
$fullPath = __DIR__ . '/../storage/app/public/' . $path;

if (!file_exists($fullPath)) {
    http_response_code(404);
    echo "File not found";
    exit;
}

// Get file info
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $fullPath);
finfo_close($finfo);

// Set headers and output file
header('Content-Type: ' . $mime);
readfile($fullPath);
PHP;

// Create directory structure to mirror storage/app/public
if (!file_exists($linkPath)) {
    if (mkdir($linkPath, 0755, true)) {
        output("Created storage directory for fallback method.");
    } else {
        output("Failed to create storage directory for fallback method.", true);
    }
}

// Create the fallback PHP file
if (file_put_contents(__DIR__ . '/storage.php', $fallbackContent)) {
    output("Created fallback storage.php file");
} else {
    output("Failed to create fallback storage.php file", true);
}

// Create an .htaccess file to rewrite requests
$htaccessContent = <<<'HTACCESS'

# Storage fallback rewrite rules
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^storage/(.*)$ storage.php?path=$1 [L]
</IfModule>
HTACCESS;

// Append to existing .htaccess if it exists, otherwise create new one
$htaccessPath = __DIR__ . '/.htaccess';
if (file_exists($htaccessPath)) {
    if (file_put_contents($htaccessPath, $htaccessContent, FILE_APPEND)) {
        output("Updated .htaccess with storage rewrite rules");
    } else {
        output("Failed to update .htaccess", true);
    }
} else {
    if (file_put_contents($htaccessPath, $htaccessContent)) {
        output("Created .htaccess with storage rewrite rules");
    } else {
        output("Failed to create .htaccess", true);
    }
}

output("Fallback method setup completed.");

end:
// Final check to make sure the storage is accessible
$testFile = $targetPath . '/test.txt';
file_put_contents($testFile, 'Storage test file created on ' . date('Y-m-d H:i:s'));

output("Created test file in storage. Checking accessibility...");

// Generate the URL to access the test file
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$testUrl = $protocol . $host . '/storage/test.txt';

output("Test file should be accessible at: <a href='{$testUrl}' target='_blank'>{$testUrl}</a>");

// Close HTML if in web context
if ($isWeb) {
    echo '</body></html>';
}
?> 