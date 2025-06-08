<?php
// Enable error reporting for diagnostics
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo '<!DOCTYPE html>
<html>
<head>
    <title>Profile Picture Upload Test</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
        h1, h2 { color: #333; }
        .success { color: green; }
        .error { color: red; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; }
        .test-block { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Profile Picture Upload Diagnostic Tool</h1>';

// Function to output test results
function test_result($test_name, $result, $success = true, $details = '') {
    echo '<div class="test-block">';
    echo '<h3>' . $test_name . '</h3>';
    echo '<p class="' . ($success ? 'success' : 'error') . '">' . $result . '</p>';
    if ($details) {
        echo '<pre>' . $details . '</pre>';
    }
    echo '</div>';
}

// Test 1: PHP Configuration
echo '<h2>1. PHP Configuration</h2>';
$upload_max_filesize = ini_get('upload_max_filesize');
$post_max_size = ini_get('post_max_size');
$max_file_uploads = ini_get('max_file_uploads');
$file_uploads = ini_get('file_uploads');

echo '<table>';
echo '<tr><th>Configuration</th><th>Value</th></tr>';
echo "<tr><td>PHP Version</td><td>" . PHP_VERSION . "</td></tr>";
echo "<tr><td>upload_max_filesize</td><td>{$upload_max_filesize}</td></tr>";
echo "<tr><td>post_max_size</td><td>{$post_max_size}</td></tr>";
echo "<tr><td>max_file_uploads</td><td>{$max_file_uploads}</td></tr>";
echo "<tr><td>file_uploads</td><td>{$file_uploads} (should be 1)</td></tr>";
echo '</table>';

if ($file_uploads != '1') {
    test_result("File Upload Support", "File uploads are disabled in PHP", false, 
                "The file_uploads directive is set to {$file_uploads} instead of 1. Contact your hosting provider to enable file uploads.");
}

// Test 2: Directory Structure and Permissions
echo '<h2>2. Directory Structure and Permissions</h2>';

$directories = [
    'storage_root' => dirname(__DIR__) . '/storage',
    'storage_app' => dirname(__DIR__) . '/storage/app',
    'storage_app_public' => dirname(__DIR__) . '/storage/app/public',
    'storage_app_public_profile_photos' => dirname(__DIR__) . '/storage/app/public/profile-photos',
    'public_storage' => __DIR__ . '/storage',
];

echo '<table>';
echo '<tr><th>Directory</th><th>Exists</th><th>Readable</th><th>Writable</th><th>Path</th></tr>';

$all_dirs_ok = true;
foreach ($directories as $name => $path) {
    // Create directory if it doesn't exist
    if (!file_exists($path) && strpos($name, 'public_storage') === false) {
        if (mkdir($path, 0755, true)) {
            echo "<tr><td>{$name}</td><td colspan='4'>Created directory: {$path}</td></tr>";
        } else {
            echo "<tr><td>{$name}</td><td colspan='4' class='error'>Failed to create directory: {$path}</td></tr>";
            $all_dirs_ok = false;
        }
    }
    
    $exists = file_exists($path);
    $readable = $exists ? is_readable($path) : false;
    $writable = $exists ? is_writable($path) : false;
    $realpath = $exists ? realpath($path) : 'N/A';
    
    echo "<tr>";
    echo "<td>{$name}</td>";
    echo "<td>" . ($exists ? '✅' : '❌') . "</td>";
    echo "<td>" . ($readable ? '✅' : '❌') . "</td>";
    echo "<td>" . ($writable ? '✅' : '❌') . "</td>";
    echo "<td>{$realpath}</td>";
    echo "</tr>";
    
    if (!$exists || !$readable || !$writable) {
        $all_dirs_ok = false;
    }
}
echo '</table>';

if (!$all_dirs_ok) {
    test_result("Directory Structure", "Some directories are missing or have incorrect permissions", false, 
                "Make sure all storage directories exist and have read/write permissions (chmod 755 or 775).");
} else {
    test_result("Directory Structure", "All required directories exist and have correct permissions", true);
}

// Test 3: Storage Symlink Check
echo '<h2>3. Storage Symlink Status</h2>';
$storageLink = __DIR__ . '/storage';
$storageTarget = dirname(__DIR__) . '/storage/app/public';

if (!file_exists($storageTarget)) {
    if (mkdir($storageTarget, 0755, true)) {
        echo "<p>Created missing target directory: {$storageTarget}</p>";
    } else {
        echo "<p class='error'>Failed to create target directory: {$storageTarget}</p>";
    }
}

$symlink_status = "";
if (file_exists($storageLink)) {
    if (is_link($storageLink)) {
        $linkTarget = readlink($storageLink);
        $symlink_status .= "✅ Storage symlink exists and points to: {$linkTarget}\n";
        
        if ($linkTarget == $storageTarget || realpath($linkTarget) == realpath($storageTarget)) {
            $symlink_status .= "✅ Symlink is correctly pointing to the storage/app/public directory";
            $symlink_ok = true;
        } else {
            $symlink_status .= "❌ Symlink is pointing to the wrong location. Should point to: {$storageTarget}";
            $symlink_ok = false;
        }
    } else {
        $symlink_status .= "❌ Storage exists but is not a symlink. It is a " . (is_dir($storageLink) ? "directory" : "file");
        $symlink_ok = false;
    }
} else {
    $symlink_status .= "❌ Storage symlink does not exist";
    $symlink_ok = false;
}

test_result("Storage Symlink", $symlink_ok ? "Storage symlink is properly configured" : "Storage symlink issues detected", 
            $symlink_ok, $symlink_status);

// Test 4: Manual File Creation Test
echo '<h2>4. Manual File Creation Test</h2>';

$test_file_path = dirname(__DIR__) . '/storage/app/public/test-' . time() . '.txt';
$test_content = 'Test file created on ' . date('Y-m-d H:i:s');

$file_create_ok = false;
try {
    if (file_put_contents($test_file_path, $test_content)) {
        $file_create_details = "✅ Successfully created test file: {$test_file_path}\n";
        $file_create_details .= "✅ File content: {$test_content}\n";
        
        // Check if we can read the file back
        if (file_exists($test_file_path) && is_readable($test_file_path)) {
            $read_content = file_get_contents($test_file_path);
            if ($read_content === $test_content) {
                $file_create_details .= "✅ Successfully read back the file content";
                $file_create_ok = true;
            } else {
                $file_create_details .= "❌ File content doesn't match what was written";
            }
        } else {
            $file_create_details .= "❌ Cannot read back the created file";
        }
    } else {
        $file_create_details = "❌ Failed to create test file";
    }
} catch (Exception $e) {
    $file_create_details = "❌ Exception when creating test file: " . $e->getMessage();
}

test_result("Manual File Creation", $file_create_ok ? "File creation works correctly" : "File creation failed", 
            $file_create_ok, $file_create_details);

// Test 5: Test File Access Via Web
echo '<h2>5. File Access Via Web</h2>';

$web_access_ok = false;
$web_access_details = "";

if ($file_create_ok) {
    $file_name = basename($test_file_path);
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'unknown';
    
    // Regular symlink access
    $symlink_url = "{$protocol}{$host}/storage/" . basename($test_file_path);
    $web_access_details .= "Testing symlink URL: {$symlink_url}\n";
    
    // Test using file_get_contents
    $context = stream_context_create(['http' => ['ignore_errors' => true]]);
    $result = @file_get_contents($symlink_url, false, $context);
    
    if ($result === false) {
        $web_access_details .= "❌ Cannot access file via symlink URL\n";
    } else {
        $web_access_details .= "✅ Successfully accessed file via symlink URL\n";
        if ($result === $test_content) {
            $web_access_details .= "✅ File content matches\n";
            $web_access_ok = true;
        } else {
            $web_access_details .= "❌ File content doesn't match\n";
        }
    }
    
    // Show the URL for manual testing
    $web_access_details .= "\nYou can manually check file access by clicking: <a href='{$symlink_url}' target='_blank'>{$symlink_url}</a>\n";
}

test_result("Web File Access", $web_access_ok ? "Files are accessible via web" : "Files cannot be accessed via web", 
            $web_access_ok, $web_access_details);

// Test 6: File Upload Test
echo '<h2>6. File Upload Test</h2>';
echo '<form action="" method="post" enctype="multipart/form-data">';
echo '<p><input type="file" name="test_image" accept="image/*"></p>';
echo '<p><input type="submit" name="upload_test" value="Test Upload"></p>';
echo '</form>';

if (isset($_POST['upload_test']) && isset($_FILES['test_image'])) {
    $upload_ok = false;
    $upload_details = "Upload information:\n";
    $upload_details .= print_r($_FILES['test_image'], true) . "\n";
    
    if ($_FILES['test_image']['error'] !== UPLOAD_ERR_OK) {
        $upload_details .= "❌ Upload error code: " . $_FILES['test_image']['error'] . "\n";
        
        // Translate error code
        switch ($_FILES['test_image']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $upload_details .= "❌ The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $upload_details .= "❌ The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $upload_details .= "❌ The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $upload_details .= "❌ No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $upload_details .= "❌ Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $upload_details .= "❌ Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $upload_details .= "❌ A PHP extension stopped the file upload";
                break;
            default:
                $upload_details .= "❌ Unknown upload error";
                break;
        }
    } else {
        // Upload succeeded, try to save to storage
        $upload_details .= "✅ File successfully uploaded to temporary location\n";
        
        $target_dir = dirname(__DIR__) . '/storage/app/public/profile-photos/';
        if (!file_exists($target_dir)) {
            if (mkdir($target_dir, 0755, true)) {
                $upload_details .= "✅ Created target directory: {$target_dir}\n";
            } else {
                $upload_details .= "❌ Failed to create target directory: {$target_dir}\n";
            }
        }
        
        $target_file = $target_dir . basename($_FILES["test_image"]["name"]);
        $upload_details .= "Target file path: {$target_file}\n";
        
        if (move_uploaded_file($_FILES["test_image"]["tmp_name"], $target_file)) {
            $upload_details .= "✅ File has been saved to: {$target_file}\n";
            
            // Check web access
            $access_url = "/storage/profile-photos/" . basename($_FILES["test_image"]["name"]);
            $upload_details .= "File should be accessible at: <a href='{$access_url}' target='_blank'>{$access_url}</a>\n";
            
            $upload_ok = true;
        } else {
            $upload_details .= "❌ Failed to save the uploaded file\n";
            $upload_details .= "PHP error: " . error_get_last()['message'] . "\n";
        }
    }
    
    test_result("File Upload", $upload_ok ? "Upload test successful" : "Upload test failed", 
                $upload_ok, $upload_details);
}

// Test 7: Laravel Storage Configuration
echo '<h2>7. Laravel Storage Configuration</h2>';

// Try to load Laravel configuration
$laravel_config_ok = false;
$laravel_config_details = "";

try {
    // Get filesystem config
    $filesystem_config_path = dirname(__DIR__) . '/config/filesystems.php';
    if (file_exists($filesystem_config_path)) {
        $laravel_config_details .= "✅ Filesystem config file exists: {$filesystem_config_path}\n";
        
        // Check .env file
        $env_path = dirname(__DIR__) . '/.env';
        if (file_exists($env_path)) {
            $laravel_config_details .= "✅ .env file exists: {$env_path}\n";
            
            // Read specific variables from .env
            $env_content = file_get_contents($env_path);
            preg_match('/FILESYSTEM_DISK=([^\s]+)/', $env_content, $disk_matches);
            $filesystem_disk = $disk_matches[1] ?? 'public'; // default to public
            
            preg_match('/APP_URL=([^\s]+)/', $env_content, $url_matches);
            $app_url = $url_matches[1] ?? 'Not defined';
            
            $laravel_config_details .= "FILESYSTEM_DISK: {$filesystem_disk}\n";
            $laravel_config_details .= "APP_URL: {$app_url}\n";
            
            if ($filesystem_disk !== 'public') {
                $laravel_config_details .= "⚠️ FILESYSTEM_DISK is not set to 'public'. Consider changing it in .env file.\n";
            }
            
            $laravel_config_ok = true;
        } else {
            $laravel_config_details .= "❌ .env file does not exist: {$env_path}\n";
        }
    } else {
        $laravel_config_details .= "❌ Filesystem config file does not exist: {$filesystem_config_path}\n";
    }
} catch (Exception $e) {
    $laravel_config_details .= "❌ Exception when checking Laravel config: " . $e->getMessage() . "\n";
}

test_result("Laravel Configuration", $laravel_config_ok ? "Laravel configuration looks good" : "Issues with Laravel configuration", 
            $laravel_config_ok, $laravel_config_details);

// Final summary and recommendations
echo '<h2>Summary and Recommendations</h2>';

if (!$symlink_ok) {
    echo '<div class="test-block">';
    echo '<h3>Storage Symlink Fix</h3>';
    echo '<p>The storage symlink is not properly configured. Try running the fix script:</p>';
    echo '<p><a href="/fix-storage.php?key=8e24905d78c97be5c5a9a5c7237c8afa" target="_blank">Run Storage Fix Utility</a></p>';
    echo '</div>';
}

if (!$all_dirs_ok) {
    echo '<div class="test-block">';
    echo '<h3>Directory Permissions</h3>';
    echo '<p>Fix the directory permissions with these commands on your server:</p>';
    echo '<pre>
chmod -R 755 ' . dirname(__DIR__) . '/storage
chmod -R 755 ' . dirname(__DIR__) . '/bootstrap/cache
</pre>';
    echo '<p>If using FTP, set the permissions of these directories to "readable and writable"</p>';
    echo '</div>';
}

if (!$file_create_ok || !$upload_ok) {
    echo '<div class="test-block">';
    echo '<h3>File Upload Issues</h3>';
    echo '<p>PHP may not have permission to write files. Contact your hosting provider with this diagnostic report.</p>';
    echo '</div>';
}

// Add a direct profile picture upload test for an existing user
echo '<h2>8. Direct Profile Picture Update Test</h2>';
echo '<p>This test simulates the exact process used when updating a profile picture.</p>';
echo '<form action="" method="post" enctype="multipart/form-data">';
echo '<p><input type="file" name="profile_photo" accept="image/*"></p>';
echo '<p><input type="submit" name="update_profile" value="Test Profile Picture Update"></p>';
echo '</form>';

if (isset($_POST['update_profile']) && isset($_FILES['profile_photo'])) {
    $profile_update_ok = false;
    $profile_update_details = "Profile photo update information:\n";
    $profile_update_details .= print_r($_FILES['profile_photo'], true) . "\n";
    
    if ($_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
        $profile_update_details .= "❌ Upload error code: " . $_FILES['profile_photo']['error'] . "\n";
    } else {
        // Upload succeeded, try to save to storage using Laravel's approach
        $profile_update_details .= "✅ File successfully uploaded to temporary location\n";
        
        // Create storage path if it doesn't exist
        $profile_photos_dir = dirname(__DIR__) . '/storage/app/public/profile-photos/';
        if (!file_exists($profile_photos_dir)) {
            if (mkdir($profile_photos_dir, 0755, true)) {
                $profile_update_details .= "✅ Created profile photos directory: {$profile_photos_dir}\n";
            } else {
                $profile_update_details .= "❌ Failed to create profile photos directory: {$profile_photos_dir}\n";
            }
        }
        
        // Generate a unique filename like Laravel would
        $filename = 'test-' . time() . '-' . uniqid() . '.' . pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $target_file = $profile_photos_dir . $filename;
        $relative_path = 'profile-photos/' . $filename;
        
        $profile_update_details .= "Target file path: {$target_file}\n";
        $profile_update_details .= "Relative storage path: {$relative_path}\n";
        
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
            $profile_update_details .= "✅ Profile photo has been saved to: {$target_file}\n";
            
            // Check web access
            $access_url = "/storage/{$relative_path}";
            $profile_update_details .= "File should be accessible at: <a href='{$access_url}' target='_blank'>{$access_url}</a>\n";
            
            // Check file permissions
            $file_perms = substr(sprintf('%o', fileperms($target_file)), -4);
            $profile_update_details .= "File permissions: {$file_perms}\n";
            
            $profile_update_ok = true;
        } else {
            $profile_update_details .= "❌ Failed to save the profile photo\n";
            $profile_update_details .= "PHP error: " . (error_get_last() ? error_get_last()['message'] : 'No error reported') . "\n";
        }
    }
    
    test_result("Profile Picture Update", $profile_update_ok ? "Profile picture update simulation successful" : "Profile picture update simulation failed", 
                $profile_update_ok, $profile_update_details);
}

echo '</body></html>';
?>
</rewritten_file>