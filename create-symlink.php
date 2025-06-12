<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Storage Symlink Creation Tool</h1>";

// Show server information
echo "<h2>Server Information</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Current directory: " . __DIR__ . "\n";
echo "</pre>";

// Define paths
$publicStoragePath = __DIR__ . '/public/storage';
$storageAppPublicPath = __DIR__ . '/storage/app/public';

// Check if directories exist
echo "<h2>Directory Check</h2>";

if (file_exists($storageAppPublicPath)) {
    echo "<p style='color:green;'>storage/app/public directory exists</p>";
} else {
    echo "<p style='color:red;'>storage/app/public directory does not exist</p>";
    echo "<p>Attempting to create it...</p>";
    
    if (mkdir($storageAppPublicPath, 0777, true)) {
        echo "<p style='color:green;'>Successfully created storage/app/public directory</p>";
    } else {
        echo "<p style='color:red;'>Failed to create storage/app/public directory</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
}

// Check if symlink exists
echo "<h2>Symlink Check</h2>";

if (file_exists($publicStoragePath)) {
    if (is_link($publicStoragePath)) {
        echo "<p style='color:green;'>public/storage is already a symlink</p>";
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
        echo "<p style='color:red;'>public/storage exists but is not a symlink</p>";
        echo "<p>It's a regular directory or file</p>";
        
        // Try to remove it and create symlink
        echo "<p>Attempting to remove it and create symlink...</p>";
        
        // Backup the directory first
        $backupDir = __DIR__ . '/public/storage_backup_' . time();
        if (rename($publicStoragePath, $backupDir)) {
            echo "<p style='color:green;'>Successfully backed up public/storage to $backupDir</p>";
        } else {
            echo "<p style='color:red;'>Failed to backup public/storage</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    }
} else {
    echo "<p>public/storage does not exist</p>";
}

// Create symlink
echo "<h2>Creating Symlink</h2>";

// First make sure parent directory exists
$publicDir = __DIR__ . '/public';
if (!file_exists($publicDir)) {
    echo "<p>public directory does not exist. Creating it...</p>";
    if (mkdir($publicDir, 0777, true)) {
        echo "<p style='color:green;'>Successfully created public directory</p>";
    } else {
        echo "<p style='color:red;'>Failed to create public directory</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
}

// Remove existing symlink or directory if it exists
if (file_exists($publicStoragePath)) {
    echo "<p>Removing existing public/storage...</p>";
    
    if (is_link($publicStoragePath)) {
        if (unlink($publicStoragePath)) {
            echo "<p style='color:green;'>Successfully removed existing symlink</p>";
        } else {
            echo "<p style='color:red;'>Failed to remove existing symlink</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    } else {
        // It's a directory, try to rename it
        $backupDir = __DIR__ . '/public/storage_backup_' . time();
        if (rename($publicStoragePath, $backupDir)) {
            echo "<p style='color:green;'>Successfully moved existing directory to $backupDir</p>";
        } else {
            echo "<p style='color:red;'>Failed to move existing directory</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
            
            // Try recursive delete
            echo "<p>Attempting to recursively delete the directory...</p>";
            
            function deleteDir($dirPath) {
                if (!is_dir($dirPath)) {
                    return false;
                }
                
                $files = scandir($dirPath);
                foreach ($files as $file) {
                    if ($file != "." && $file != "..") {
                        $filePath = $dirPath . "/" . $file;
                        if (is_dir($filePath)) {
                            deleteDir($filePath);
                        } else {
                            unlink($filePath);
                        }
                    }
                }
                
                return rmdir($dirPath);
            }
            
            if (deleteDir($publicStoragePath)) {
                echo "<p style='color:green;'>Successfully deleted directory</p>";
            } else {
                echo "<p style='color:red;'>Failed to delete directory</p>";
                echo "<p>Error: " . error_get_last()['message'] . "</p>";
                echo "<p style='color:red;'>Cannot proceed with symlink creation</p>";
                exit;
            }
        }
    }
}

// Create the symlink
echo "<p>Creating symlink from public/storage to storage/app/public...</p>";

// Since symlink() might not work on shared hosting, try alternative approaches
$methods = [
    'symlink' => function() use ($storageAppPublicPath, $publicStoragePath) {
        return symlink($storageAppPublicPath, $publicStoragePath);
    },
    'junction' => function() use ($storageAppPublicPath, $publicStoragePath) {
        // Windows-specific junction point
        $command = "mklink /J \"{$publicStoragePath}\" \"{$storageAppPublicPath}\"";
        exec($command, $output, $returnVar);
        return $returnVar === 0;
    },
    'copy' => function() use ($storageAppPublicPath, $publicStoragePath) {
        // Manual copy as a fallback
        if (!file_exists($publicStoragePath)) {
            mkdir($publicStoragePath, 0777, true);
        }
        
        // Create a marker file to indicate this is a manual copy
        file_put_contents($publicStoragePath . '/.manual_copy', 'This is a manual copy of storage/app/public');
        
        // Copy all files from storage/app/public to public/storage
        if (!file_exists($storageAppPublicPath)) {
            return false;
        }
        
        $files = scandir($storageAppPublicPath);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $sourcePath = $storageAppPublicPath . "/" . $file;
                $destPath = $publicStoragePath . "/" . $file;
                
                if (is_dir($sourcePath)) {
                    if (!file_exists($destPath)) {
                        mkdir($destPath, 0777, true);
                    }
                    
                    // Recursively copy subdirectories
                    $subfiles = scandir($sourcePath);
                    foreach ($subfiles as $subfile) {
                        if ($subfile != "." && $subfile != "..") {
                            $subSourcePath = $sourcePath . "/" . $subfile;
                            $subDestPath = $destPath . "/" . $subfile;
                            
                            if (is_dir($subSourcePath)) {
                                if (!file_exists($subDestPath)) {
                                    mkdir($subDestPath, 0777, true);
                                }
                            } else {
                                copy($subSourcePath, $subDestPath);
                            }
                        }
                    }
                } else {
                    copy($sourcePath, $destPath);
                }
            }
        }
        
        return true;
    }
];

$success = false;
foreach ($methods as $method => $func) {
    echo "<p>Trying method: $method...</p>";
    
    try {
        if ($func()) {
            echo "<p style='color:green;'>Successfully created storage link using $method method</p>";
            $success = true;
            break;
        } else {
            echo "<p style='color:red;'>Failed to create storage link using $method method</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red;'>Exception while trying $method method: " . $e->getMessage() . "</p>";
    }
}

if (!$success) {
    echo "<p style='color:red;'>All methods failed. Trying direct file copy as last resort...</p>";
    
    // Create public/storage directory
    if (!file_exists($publicStoragePath)) {
        if (mkdir($publicStoragePath, 0777, true)) {
            echo "<p style='color:green;'>Created public/storage directory</p>";
        } else {
            echo "<p style='color:red;'>Failed to create public/storage directory</p>";
            exit;
        }
    }
    
    // Create profile-photos directory inside public/storage
    $publicProfilePhotosDir = $publicStoragePath . '/profile-photos';
    if (!file_exists($publicProfilePhotosDir)) {
        if (mkdir($publicProfilePhotosDir, 0777, true)) {
            echo "<p style='color:green;'>Created public/storage/profile-photos directory</p>";
        } else {
            echo "<p style='color:red;'>Failed to create public/storage/profile-photos directory</p>";
            exit;
        }
    }
    
    // Create a .htaccess file to ensure proper access
    $htaccessContent = "Options +FollowSymLinks\nAllow from all";
    if (file_put_contents($publicStoragePath . '/.htaccess', $htaccessContent)) {
        echo "<p style='color:green;'>Created .htaccess file in public/storage</p>";
    } else {
        echo "<p style='color:red;'>Failed to create .htaccess file</p>";
    }
    
    echo "<p style='color:green;'>Manual directory creation completed</p>";
}

// Verify the result
echo "<h2>Verification</h2>";

if (file_exists($publicStoragePath)) {
    echo "<p style='color:green;'>public/storage exists</p>";
    
    if (is_link($publicStoragePath)) {
        echo "<p style='color:green;'>public/storage is a symlink</p>";
        echo "<p>Target: " . readlink($publicStoragePath) . "</p>";
    } else {
        echo "<p style='color:orange;'>public/storage is a regular directory (manual copy mode)</p>";
    }
    
    // Check if profile-photos directory exists
    $publicProfilePhotosDir = $publicStoragePath . '/profile-photos';
    if (file_exists($publicProfilePhotosDir)) {
        echo "<p style='color:green;'>public/storage/profile-photos exists</p>";
    } else {
        echo "<p style='color:red;'>public/storage/profile-photos does not exist</p>";
        
        // Try to create it
        if (mkdir($publicProfilePhotosDir, 0777, true)) {
            echo "<p style='color:green;'>Created public/storage/profile-photos directory</p>";
        } else {
            echo "<p style='color:red;'>Failed to create public/storage/profile-photos directory</p>";
        }
    }
} else {
    echo "<p style='color:red;'>public/storage does not exist after all attempts</p>";
}

// Create test files
echo "<h2>Test File Creation</h2>";

$testStorageFile = $storageAppPublicPath . '/profile-photos/test-' . time() . '.txt';
$testContent = "Test file created at " . date('Y-m-d H:i:s');

if (file_put_contents($testStorageFile, $testContent)) {
    echo "<p style='color:green;'>Created test file in storage/app/public/profile-photos</p>";
    
    $testPublicFile = $publicStoragePath . '/profile-photos/test-' . time() . '.txt';
    if (file_put_contents($testPublicFile, $testContent)) {
        echo "<p style='color:green;'>Created test file in public/storage/profile-photos</p>";
    } else {
        echo "<p style='color:red;'>Failed to create test file in public/storage/profile-photos</p>";
    }
} else {
    echo "<p style='color:red;'>Failed to create test file in storage/app/public/profile-photos</p>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Try uploading a profile picture again as an admin</li>";
echo "<li>Check if the file appears in both storage/app/public/profile-photos and public/storage/profile-photos</li>";
echo "<li>If the profile picture still doesn't display, run the profile-photo-debug.php script to check the database entries</li>";
echo "</ol>";
?> 