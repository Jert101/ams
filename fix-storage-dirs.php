<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Storage Directory Fix Tool</h1>";

// Show server information
echo "<h2>Server Information</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Current directory: " . __DIR__ . "\n";
echo "</pre>";

// Define directories to create
$directories = [
    'storage/app/public/profile-photos',
    'public/storage/profile-photos'
];

// Create directories with proper permissions
foreach ($directories as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    echo "<h3>Processing: $fullPath</h3>";
    
    // Create parent directories if needed
    $parentDir = dirname($fullPath);
    if (!file_exists($parentDir)) {
        echo "<p>Parent directory doesn't exist. Creating parent directories first...</p>";
        if (mkdir($parentDir, 0777, true)) {
            echo "<p style='color:green;'>Successfully created parent directories.</p>";
        } else {
            $error = error_get_last();
            echo "<p style='color:red;'>Failed to create parent directories: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    }
    
    if (file_exists($fullPath)) {
        echo "<p>Directory already exists.</p>";
        
        // Check permissions
        $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
        echo "<p>Current permissions: $perms</p>";
        
        // Try to update permissions
        if (@chmod($fullPath, 0777)) {
            echo "<p style='color:green;'>Successfully updated permissions to 0777.</p>";
        } else {
            echo "<p style='color:red;'>Failed to update permissions.</p>";
        }
    } else {
        echo "<p>Directory does not exist. Attempting to create...</p>";
        
        // Create directory with full permissions
        if (@mkdir($fullPath, 0777, true)) {
            echo "<p style='color:green;'>Successfully created directory with 0777 permissions.</p>";
        } else {
            $error = error_get_last();
            echo "<p style='color:red;'>Failed to create directory: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    }
    
    // Verify directory is writable
    if (file_exists($fullPath) && is_writable($fullPath)) {
        echo "<p style='color:green;'>Directory is writable.</p>";
    } else {
        echo "<p style='color:red;'>Directory is NOT writable or doesn't exist!</p>";
    }
    
    echo "<hr>";
}

// Test if we can create a file in each directory
echo "<h2>Testing File Creation</h2>";

foreach ($directories as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    $testFile = $fullPath . '/test-' . time() . '.txt';
    
    echo "<h3>Testing file creation in: $fullPath</h3>";
    
    if (!file_exists($fullPath)) {
        echo "<p style='color:red;'>Directory does not exist, cannot test file creation.</p>";
        continue;
    }
    
    // Try to create a test file
    $content = "This is a test file created at " . date('Y-m-d H:i:s');
    
    if (@file_put_contents($testFile, $content)) {
        echo "<p style='color:green;'>Successfully created test file: $testFile</p>";
        
        // Verify file exists and is readable
        if (file_exists($testFile)) {
            echo "<p style='color:green;'>File exists after creation.</p>";
            
            // Check file permissions
            $perms = substr(sprintf('%o', fileperms($testFile)), -4);
            echo "<p>File permissions: $perms</p>";
            
            // Try to read the file
            $readContent = @file_get_contents($testFile);
            if ($readContent === $content) {
                echo "<p style='color:green;'>Successfully read the file content.</p>";
            } else {
                echo "<p style='color:red;'>Failed to read the file content correctly.</p>";
            }
        } else {
            echo "<p style='color:red;'>File does not exist after creation!</p>";
        }
    } else {
        $error = error_get_last();
        echo "<p style='color:red;'>Failed to create test file: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
    }
    
    echo "<hr>";
}

// Show current directory structure
echo "<h2>Current Directory Structure</h2>";
echo "<pre>";
function listDir($dir, $indent = 0) {
    if (!file_exists($dir) || !is_dir($dir)) {
        echo str_repeat('  ', $indent) . "Directory does not exist: $dir\n";
        return;
    }
    
    try {
        $files = @scandir($dir);
        if ($files === false) {
            echo str_repeat('  ', $indent) . "Cannot read directory: $dir\n";
            return;
        }
        
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;
            
            $path = $dir . '/' . $file;
            echo str_repeat('  ', $indent) . ($indent > 0 ? '└─ ' : '') . $file;
            
            if (is_dir($path)) {
                echo " (dir)";
                if ($indent < 3) { // Limit depth to avoid too much output
                    echo "\n";
                    listDir($path, $indent + 1);
                } else {
                    echo " ...\n";
                }
            } else {
                echo " (" . @filesize($path) . " bytes)\n";
            }
        }
    } catch (Exception $e) {
        echo str_repeat('  ', $indent) . "Error: " . $e->getMessage() . "\n";
    }
}

try {
    listDir(__DIR__, 0);
} catch (Exception $e) {
    echo "Error listing directory: " . $e->getMessage();
}
echo "</pre>";

// Create a .htaccess file to ensure proper access to the storage directory
echo "<h2>Creating .htaccess files</h2>";

$htaccessContent = "Options +FollowSymLinks\nAllow from all";
$htaccessPaths = [
    __DIR__ . '/storage',
    __DIR__ . '/storage/app/public',
    __DIR__ . '/storage/app/public/profile-photos',
    __DIR__ . '/public/storage',
    __DIR__ . '/public/storage/profile-photos'
];

foreach ($htaccessPaths as $path) {
    if (!file_exists($path)) {
        if (@mkdir($path, 0777, true)) {
            echo "<p style='color:green;'>Created directory: $path</p>";
        } else {
            echo "<p style='color:red;'>Failed to create directory: $path</p>";
            continue;
        }
    }
    
    $htaccessFile = $path . '/.htaccess';
    if (@file_put_contents($htaccessFile, $htaccessContent)) {
        echo "<p style='color:green;'>Created .htaccess file in: $path</p>";
    } else {
        echo "<p style='color:red;'>Failed to create .htaccess file in: $path</p>";
    }
}

echo "<h2>Next Steps</h2>";
echo "<p>After running this script:</p>";
echo "<ol>";
echo "<li>Try uploading a profile picture as an admin</li>";
echo "<li>If it still doesn't work, check Laravel logs for errors</li>";
echo "<li>Make sure your form has enctype='multipart/form-data' attribute</li>";
echo "</ol>";
?> 