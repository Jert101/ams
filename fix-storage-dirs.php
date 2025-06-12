<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Storage Directory Fix Tool</h1>";

// Define directories to create
$directories = [
    'storage/app/public/profile-photos',
    'public/storage/profile-photos'
];

// Create directories with proper permissions
foreach ($directories as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    echo "<h3>Processing: $fullPath</h3>";
    
    if (file_exists($fullPath)) {
        echo "<p>Directory already exists.</p>";
        
        // Check permissions
        $perms = substr(sprintf('%o', fileperms($fullPath)), -4);
        echo "<p>Current permissions: $perms</p>";
        
        // Try to update permissions
        if (chmod($fullPath, 0777)) {
            echo "<p style='color:green;'>Successfully updated permissions to 0777.</p>";
        } else {
            echo "<p style='color:red;'>Failed to update permissions.</p>";
        }
    } else {
        echo "<p>Directory does not exist. Attempting to create...</p>";
        
        // Create directory with full permissions
        if (mkdir($fullPath, 0777, true)) {
            echo "<p style='color:green;'>Successfully created directory with 0777 permissions.</p>";
        } else {
            $error = error_get_last();
            echo "<p style='color:red;'>Failed to create directory: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    }
    
    // Verify directory is writable
    if (is_writable($fullPath)) {
        echo "<p style='color:green;'>Directory is writable.</p>";
    } else {
        echo "<p style='color:red;'>Directory is NOT writable!</p>";
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
    
    if (file_put_contents($testFile, $content)) {
        echo "<p style='color:green;'>Successfully created test file: $testFile</p>";
        
        // Verify file exists and is readable
        if (file_exists($testFile)) {
            echo "<p style='color:green;'>File exists after creation.</p>";
            
            // Check file permissions
            $perms = substr(sprintf('%o', fileperms($testFile)), -4);
            echo "<p>File permissions: $perms</p>";
            
            // Try to read the file
            $readContent = file_get_contents($testFile);
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
    $files = scandir($dir);
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
            echo " (" . filesize($path) . " bytes)\n";
        }
    }
}

try {
    listDir(__DIR__, 0);
} catch (Exception $e) {
    echo "Error listing directory: " . $e->getMessage();
}
echo "</pre>";
?> 