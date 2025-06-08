<?php
// Profile Photo Diagnostic and Fix Tool
// This script checks for common issues with profile photos and offers fixes

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Basic styling
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Photo Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 1200px; margin: 0 auto; }
        h1, h2, h3 { color: #b22234; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { color: blue; }
        pre { background: #f4f4f4; padding: 10px; overflow: auto; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .actions { margin: 20px 0; padding: 15px; background: #f8f8f8; border-radius: 5px; }
        button, .button { 
            background: #b22234; color: white; border: none; padding: 10px 15px; 
            border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block;
            margin-right: 10px; margin-bottom: 10px;
        }
        button:hover, .button:hover { background: #931c2a; }
        .box { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Profile Photo Diagnostic and Fix Tool</h1>';

// Define paths
$publicStorage = __DIR__ . '/storage';
$storagePath = __DIR__ . '/../storage/app/public';
$profilePhotosStorage = $storagePath . '/profile-photos';
$profilePhotosPublic = $publicStorage . '/profile-photos';

// Connect to the database
require_once __DIR__ . '/../vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    $db = app('db');
    echo '<div class="success">Successfully connected to the database.</div>';
} catch (Exception $e) {
    echo '<div class="error">Failed to connect to the database: ' . $e->getMessage() . '</div>';
    echo '<p>Please make sure the application is properly set up and database credentials are correct.</p>';
    exit;
}

// Menu
echo '<div class="actions">
    <h2>Available Actions</h2>
    <a href="?action=diagnose" class="button">Run Diagnostics</a>
    <a href="?action=list_users" class="button">List Users with Issues</a>
    <a href="?action=fix_symlink" class="button">Fix Storage Symlink</a>
    <a href="?action=fix_photo_dirs" class="button">Create Missing Directories</a>
    <a href="?action=fix_invalid_paths" class="button">Fix Invalid Photo Paths</a>
    <a href="?action=test_upload" class="button">Test Photo Upload</a>
</div>';

// Get the action
$action = $_GET['action'] ?? 'diagnose';

// Handle different actions
switch ($action) {
    case 'diagnose':
        runDiagnostics();
        break;
    
    case 'list_users':
        listUsersWithIssues();
        break;
    
    case 'fix_symlink':
        fixStorageSymlink();
        break;
    
    case 'fix_photo_dirs':
        fixPhotoDirectories();
        break;
    
    case 'fix_invalid_paths':
        fixInvalidPaths();
        break;
    
    case 'test_upload':
        showUploadForm();
        break;
}

// Function to run diagnostics
function runDiagnostics() {
    global $publicStorage, $storagePath, $profilePhotosStorage, $profilePhotosPublic;
    
    echo '<h2>System Diagnostics</h2>';
    
    // Check PHP version and extensions
    echo '<h3>PHP Environment</h3>';
    echo '<p>PHP Version: <b>' . phpversion() . '</b></p>';
    echo '<p>File Uploads Enabled: <b>' . (ini_get('file_uploads') ? 'Yes' : 'No') . '</b></p>';
    echo '<p>Upload Max Filesize: <b>' . ini_get('upload_max_filesize') . '</b></p>';
    echo '<p>Post Max Size: <b>' . ini_get('post_max_size') . '</b></p>';
    echo '<p>GD Extension Loaded: <b>' . (extension_loaded('gd') ? 'Yes' : 'No') . '</b></p>';
    
    // Check directory structure and permissions
    echo '<h3>Directory Structure</h3>';
    echo '<table>
        <tr>
            <th>Path</th>
            <th>Exists</th>
            <th>Permissions</th>
            <th>Writable</th>
        </tr>';
    
    checkDirectory($storagePath, 'Storage app/public');
    checkDirectory($publicStorage, 'Public storage', true);
    checkDirectory($profilePhotosStorage, 'Profile photos in storage');
    checkDirectory($profilePhotosPublic, 'Profile photos in public');
    
    echo '</table>';
    
    // Check database configuration
    echo '<h3>Database Configuration</h3>';
    try {
        $connection = app('db')->connection()->getPdo();
        echo '<div class="success">Database connection successful</div>';
        
        // Count users with problematic profile photo paths
        $nullCount = app('db')->table('users')->whereNull('profile_photo_path')->count();
        $zeroCount = app('db')->table('users')->where('profile_photo_path', '0')->count();
        $emptyCount = app('db')->table('users')->where('profile_photo_path', '')->count();
        
        echo '<p>Users with null profile_photo_path: <b>' . $nullCount . '</b></p>';
        echo '<p>Users with "0" profile_photo_path: <b>' . $zeroCount . '</b></p>';
        echo '<p>Users with empty profile_photo_path: <b>' . $emptyCount . '</b></p>';
        
    } catch (Exception $e) {
        echo '<div class="error">Database connection error: ' . $e->getMessage() . '</div>';
    }
    
    // Check available profile photos
    echo '<h3>Available Profile Photos</h3>';
    if (file_exists($profilePhotosStorage)) {
        $files = scandir($profilePhotosStorage);
        $photoCount = count($files) - 2; // Subtract . and ..
        echo '<p>Number of photos in storage: <b>' . $photoCount . '</b></p>';
        
        // List a few photos
        echo '<p>Sample of available photos:</p>';
        echo '<ul>';
        $count = 0;
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo '<li>' . $file . ' (' . formatBytes(filesize($profilePhotosStorage . '/' . $file)) . ')</li>';
                $count++;
                if ($count >= 5) break;
            }
        }
        echo '</ul>';
    } else {
        echo '<p class="error">Profile photos directory does not exist in storage!</p>';
    }
    
    // Check default photo
    echo '<h3>Default Photo</h3>';
    if (file_exists(__DIR__ . '/kofa.png')) {
        echo '<p class="success">Default photo (kofa.png) exists in public directory.</p>';
        echo '<p>Size: ' . formatBytes(filesize(__DIR__ . '/kofa.png')) . '</p>';
    } else {
        echo '<p class="error">Default photo (kofa.png) does not exist in public directory!</p>';
    }
}

// Helper function to check directory and display info
function checkDirectory($path, $label, $checkSymlink = false) {
    $exists = file_exists($path);
    $isWritable = $exists ? is_writable($path) : false;
    $permissions = $exists ? substr(sprintf('%o', fileperms($path)), -4) : 'N/A';
    $isSymlink = $checkSymlink ? is_link($path) : false;
    
    $existsClass = $exists ? 'success' : 'error';
    $writableClass = $isWritable ? 'success' : 'error';
    
    echo "<tr>
        <td>{$path}</td>
        <td class='{$existsClass}'>" . ($exists ? 'Yes' : 'No') . "</td>
        <td>{$permissions}</td>
        <td class='{$writableClass}'>" . ($isWritable ? 'Yes' : 'No') . "</td>
    </tr>";
    
    if ($checkSymlink) {
        echo "<tr>
            <td>Is Symlink</td>
            <td colspan='3'>" . ($isSymlink ? 'Yes, points to: ' . readlink($path) : 'No') . "</td>
        </tr>";
    }
}

// Function to list users with issues
function listUsersWithIssues() {
    echo '<h2>Users with Profile Photo Issues</h2>';
    
    try {
        $users = app('db')->table('users')
            ->select('user_id', 'name', 'email', 'profile_photo_path', 'role_id')
            ->whereNull('profile_photo_path')
            ->orWhere('profile_photo_path', '0')
            ->orWhere('profile_photo_path', '')
            ->get();
        
        if (count($users) > 0) {
            echo '<table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Profile Photo Path</th>
                    <th>Issue</th>
                </tr>';
            
            foreach ($users as $user) {
                $issue = '';
                if (is_null($user->profile_photo_path)) {
                    $issue = 'NULL value';
                } else if ($user->profile_photo_path === '0') {
                    $issue = 'Value is "0"';
                } else if ($user->profile_photo_path === '') {
                    $issue = 'Empty string';
                }
                
                $role = app('db')->table('roles')->where('id', $user->role_id)->value('name') ?? 'Unknown';
                
                echo "<tr>
                    <td>{$user->user_id}</td>
                    <td>{$user->name}</td>
                    <td>{$user->email}</td>
                    <td>{$role}</td>
                    <td>" . (is_null($user->profile_photo_path) ? 'NULL' : $user->profile_photo_path) . "</td>
                    <td class='error'>{$issue}</td>
                </tr>";
            }
            
            echo '</table>';
            
            echo '<div class="actions">
                <a href="?action=fix_invalid_paths" class="button">Fix These Users</a>
            </div>';
        } else {
            echo '<p class="success">No users with invalid profile photo paths found!</p>';
        }
        
    } catch (Exception $e) {
        echo '<div class="error">Error querying users: ' . $e->getMessage() . '</div>';
    }
    
    // Now list users with valid paths but missing files
    echo '<h3>Users with Missing Profile Photos</h3>';
    
    try {
        $users = app('db')->table('users')
            ->select('user_id', 'name', 'email', 'profile_photo_path', 'role_id')
            ->whereNotNull('profile_photo_path')
            ->where('profile_photo_path', '!=', '0')
            ->where('profile_photo_path', '!=', '')
            ->where('profile_photo_path', '!=', 'kofa.png')
            ->get();
        
        $usersWithMissingFiles = [];
        
        foreach ($users as $user) {
            $photoPath = $user->profile_photo_path;
            $storageFilePath = __DIR__ . '/../storage/app/public/' . $photoPath;
            $publicFilePath = __DIR__ . '/storage/' . $photoPath;
            
            if (!file_exists($storageFilePath) && !file_exists($publicFilePath)) {
                $usersWithMissingFiles[] = $user;
            }
        }
        
        if (count($usersWithMissingFiles) > 0) {
            echo '<table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Profile Photo Path</th>
                </tr>';
            
            foreach ($usersWithMissingFiles as $user) {
                $role = app('db')->table('roles')->where('id', $user->role_id)->value('name') ?? 'Unknown';
                
                echo "<tr>
                    <td>{$user->user_id}</td>
                    <td>{$user->name}</td>
                    <td>{$user->email}</td>
                    <td>{$role}</td>
                    <td>{$user->profile_photo_path}</td>
                </tr>";
            }
            
            echo '</table>';
            
            echo '<div class="actions">
                <a href="?action=reset_missing_photos" class="button">Reset to Default</a>
            </div>';
        } else {
            echo '<p class="success">No users with missing photo files found!</p>';
        }
        
    } catch (Exception $e) {
        echo '<div class="error">Error checking for missing files: ' . $e->getMessage() . '</div>';
    }
}

// Function to fix storage symlink
function fixStorageSymlink() {
    $publicStorage = __DIR__ . '/storage';
    $actualStorage = __DIR__ . '/../storage/app/public';
    
    echo '<h2>Fix Storage Symlink</h2>';
    
    // Check if the symlink already exists
    if (is_link($publicStorage)) {
        echo '<div class="info">Storage symlink already exists and points to: ' . readlink($publicStorage) . '</div>';
        
        // Check if it points to the correct location
        if (readlink($publicStorage) === $actualStorage) {
            echo '<div class="success">Symlink is correctly configured.</div>';
        } else {
            echo '<div class="warning">Symlink exists but points to the wrong location.</div>';
            
            // Remove and recreate
            if (unlink($publicStorage)) {
                echo '<div class="info">Removed incorrect symlink.</div>';
                
                if (symlink($actualStorage, $publicStorage)) {
                    echo '<div class="success">Created new symlink pointing to: ' . $actualStorage . '</div>';
                } else {
                    echo '<div class="error">Failed to create new symlink. Check permissions.</div>';
                }
            } else {
                echo '<div class="error">Failed to remove incorrect symlink. Check permissions.</div>';
            }
        }
    } else {
        // Check if a directory exists at the symlink location
        if (file_exists($publicStorage) && is_dir($publicStorage)) {
            echo '<div class="warning">A directory exists at ' . $publicStorage . ' instead of a symlink.</div>';
            echo '<div class="info">This is fine for shared hosting environments.</div>';
            
            // Make sure the profile-photos directory exists in both locations
            $profilePhotosPublic = $publicStorage . '/profile-photos';
            $profilePhotosStorage = $actualStorage . '/profile-photos';
            
            if (!file_exists($profilePhotosPublic)) {
                if (mkdir($profilePhotosPublic, 0755, true)) {
                    echo '<div class="success">Created profile-photos directory in public/storage.</div>';
                } else {
                    echo '<div class="error">Failed to create profile-photos directory in public/storage.</div>';
                }
            }
            
            if (!file_exists($profilePhotosStorage)) {
                if (mkdir($profilePhotosStorage, 0755, true)) {
                    echo '<div class="success">Created profile-photos directory in storage/app/public.</div>';
                } else {
                    echo '<div class="error">Failed to create profile-photos directory in storage/app/public.</div>';
                }
            }
            
            // Copy any missing files between directories
            if (file_exists($profilePhotosStorage) && file_exists($profilePhotosPublic)) {
                $storageFiles = scandir($profilePhotosStorage);
                $publicFiles = scandir($profilePhotosPublic);
                
                $copiedToPublic = 0;
                $copiedToStorage = 0;
                
                // Copy from storage to public
                foreach ($storageFiles as $file) {
                    if ($file != '.' && $file != '..' && !in_array($file, $publicFiles)) {
                        if (copy($profilePhotosStorage . '/' . $file, $profilePhotosPublic . '/' . $file)) {
                            $copiedToPublic++;
                        }
                    }
                }
                
                // Copy from public to storage
                foreach ($publicFiles as $file) {
                    if ($file != '.' && $file != '..' && !in_array($file, $storageFiles)) {
                        if (copy($profilePhotosPublic . '/' . $file, $profilePhotosStorage . '/' . $file)) {
                            $copiedToStorage++;
                        }
                    }
                }
                
                echo '<div class="info">Copied ' . $copiedToPublic . ' files from storage to public.</div>';
                echo '<div class="info">Copied ' . $copiedToStorage . ' files from public to storage.</div>';
            }
        } else {
            // Try to create the symlink
            if (symlink($actualStorage, $publicStorage)) {
                echo '<div class="success">Created new symlink pointing to: ' . $actualStorage . '</div>';
            } else {
                echo '<div class="error">Failed to create symlink. This may be due to permissions or hosting limitations.</div>';
                echo '<div class="info">Creating a directory instead...</div>';
                
                if (mkdir($publicStorage, 0755, true)) {
                    echo '<div class="success">Created directory at ' . $publicStorage . ' as an alternative to symlink.</div>';
                    
                    // Create the profile-photos directory
                    $profilePhotosPublic = $publicStorage . '/profile-photos';
                    if (mkdir($profilePhotosPublic, 0755, true)) {
                        echo '<div class="success">Created profile-photos directory in public/storage.</div>';
                    } else {
                        echo '<div class="error">Failed to create profile-photos directory in public/storage.</div>';
                    }
                } else {
                    echo '<div class="error">Failed to create directory at ' . $publicStorage . '.</div>';
                }
            }
        }
    }
    
    echo '<div class="actions">
        <a href="?action=diagnose" class="button">Run Diagnostics Again</a>
    </div>';
}

// Function to fix photo directories
function fixPhotoDirectories() {
    global $publicStorage, $storagePath, $profilePhotosStorage, $profilePhotosPublic;
    
    echo '<h2>Fix Photo Directories</h2>';
    
    // Create storage directory if it doesn't exist
    if (!file_exists($storagePath)) {
        if (mkdir($storagePath, 0755, true)) {
            echo '<div class="success">Created storage/app/public directory.</div>';
        } else {
            echo '<div class="error">Failed to create storage/app/public directory.</div>';
        }
    }
    
    // Create public storage directory if it doesn't exist
    if (!file_exists($publicStorage)) {
        if (mkdir($publicStorage, 0755, true)) {
            echo '<div class="success">Created public/storage directory.</div>';
        } else {
            echo '<div class="error">Failed to create public/storage directory.</div>';
        }
    }
    
    // Create profile photos directory in storage
    if (!file_exists($profilePhotosStorage)) {
        if (mkdir($profilePhotosStorage, 0755, true)) {
            echo '<div class="success">Created profile-photos directory in storage.</div>';
        } else {
            echo '<div class="error">Failed to create profile-photos directory in storage.</div>';
        }
    }
    
    // Create profile photos directory in public
    if (!file_exists($profilePhotosPublic)) {
        if (mkdir($profilePhotosPublic, 0755, true)) {
            echo '<div class="success">Created profile-photos directory in public.</div>';
        } else {
            echo '<div class="error">Failed to create profile-photos directory in public.</div>';
        }
    }
    
    // Check for kofa.png
    $kofaPath = __DIR__ . '/kofa.png';
    if (!file_exists($kofaPath)) {
        // Create a simple placeholder image
        echo '<div class="warning">Default kofa.png is missing.</div>';
        
        // Try to create a simple placeholder image
        if (extension_loaded('gd')) {
            $img = imagecreatetruecolor(200, 200);
            $bgColor = imagecolorallocate($img, 178, 34, 52); // AMS red color
            $textColor = imagecolorallocate($img, 255, 255, 255);
            
            imagefill($img, 0, 0, $bgColor);
            imagestring($img, 5, 40, 90, 'KOFA', $textColor);
            
            if (imagepng($img, $kofaPath)) {
                echo '<div class="success">Created placeholder kofa.png image.</div>';
            } else {
                echo '<div class="error">Failed to create kofa.png image.</div>';
            }
            
            imagedestroy($img);
        } else {
            echo '<div class="error">GD extension is not available to create a placeholder image.</div>';
        }
    } else {
        echo '<div class="success">Default kofa.png exists.</div>';
    }
    
    echo '<div class="actions">
        <a href="?action=diagnose" class="button">Run Diagnostics Again</a>
    </div>';
}

// Function to fix invalid profile photo paths
function fixInvalidPaths() {
    echo '<h2>Fix Invalid Profile Photo Paths</h2>';
    
    if (isset($_POST['confirm_fix'])) {
        try {
            $updated = app('db')->table('users')
                ->whereNull('profile_photo_path')
                ->orWhere('profile_photo_path', '0')
                ->orWhere('profile_photo_path', '')
                ->update(['profile_photo_path' => 'kofa.png']);
            
            echo '<div class="success">Updated ' . $updated . ' users to use the default profile photo.</div>';
        } catch (Exception $e) {
            echo '<div class="error">Error updating users: ' . $e->getMessage() . '</div>';
        }
    } else {
        echo '<div class="box">
            <p>This will reset all users with null, "0", or empty profile photo paths to use the default "kofa.png".</p>
            <p class="warning">Are you sure you want to proceed?</p>
            <form method="post">
                <input type="hidden" name="confirm_fix" value="1">
                <button type="submit">Yes, Fix Invalid Paths</button>
                <a href="?action=diagnose" class="button" style="background-color: #6c757d;">Cancel</a>
            </form>
        </div>';
    }
}

// Function to display upload test form
function showUploadForm() {
    echo '<h2>Test Photo Upload</h2>';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_photo'])) {
        // Process the uploaded file
        $file = $_FILES['test_photo'];
        
        echo '<h3>Upload Results</h3>';
        echo '<pre>';
        print_r($file);
        echo '</pre>';
        
        if ($file['error'] === UPLOAD_ERR_OK) {
            echo '<div class="success">File was uploaded successfully.</div>';
            
            // Test saving to storage location
            $storagePath = __DIR__ . '/../storage/app/public/profile-photos';
            $publicPath = __DIR__ . '/storage/profile-photos';
            
            // Ensure directories exist
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }
            
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }
            
            // Generate a filename
            $filename = 'test-' . time() . '-' . basename($file['name']);
            $storageDestination = $storagePath . '/' . $filename;
            $publicDestination = $publicPath . '/' . $filename;
            
            // Try to save to storage
            if (move_uploaded_file($file['tmp_name'], $storageDestination)) {
                echo '<div class="success">File saved to storage at: ' . $storageDestination . '</div>';
                
                // Copy to public as well
                if (copy($storageDestination, $publicDestination)) {
                    echo '<div class="success">File copied to public at: ' . $publicDestination . '</div>';
                } else {
                    echo '<div class="error">Failed to copy file to public location.</div>';
                }
                
                // Display the image
                echo '<h3>Uploaded Image</h3>';
                echo '<img src="storage/profile-photos/' . $filename . '" style="max-width: 300px; border: 1px solid #ddd; padding: 5px;">';
                
                // Provide the path that would be stored in the database
                echo '<div class="info">
                    <p>Path to store in database: <code>profile-photos/' . $filename . '</code></p>
                </div>';
            } else {
                echo '<div class="error">Failed to save file to storage location.</div>';
                echo '<div class="error">PHP Error: ' . error_get_last()['message'] . '</div>';
            }
        } else {
            echo '<div class="error">Upload error: ' . uploadErrorMessage($file['error']) . '</div>';
        }
    }
    
    echo '<div class="box">
        <form method="post" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label for="test_photo">Select an image to upload:</label><br>
                <input type="file" name="test_photo" id="test_photo" accept="image/*">
            </div>
            <button type="submit">Upload Test Photo</button>
        </form>
    </div>';
}

// Helper function to convert bytes to human-readable format
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= (1 << (10 * $pow));
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Helper function to get upload error message
function uploadErrorMessage($code) {
    switch ($code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
        case UPLOAD_ERR_FORM_SIZE:
            return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
        case UPLOAD_ERR_PARTIAL:
            return 'The uploaded file was only partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file was uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Missing a temporary folder';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Failed to write file to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'A PHP extension stopped the file upload';
        default:
            return 'Unknown upload error';
    }
}

// Close HTML
echo '</body></html>'; 