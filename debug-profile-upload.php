<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Profile Picture Upload Debug</h1>";

// Define paths
$basePath = __DIR__;
$storagePath = $basePath . '/storage';
$storageAppPublicPath = $storagePath . '/app/public';
$profilePhotosPath = $storageAppPublicPath . '/profile-photos';
$publicPath = $basePath . '/public';
$publicStoragePath = $publicPath . '/storage';
$publicProfilePhotosPath = $publicStoragePath . '/profile-photos';

// Database connection parameters - update these with your actual credentials
$servername = "localhost";
$username = "if0_38972693"; // Your InfinityFree username
$password = ""; // You need to fill this with your actual database password
$dbname = "if0_38972693_ams"; // Your database name

echo "<h2>Environment Information</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script Path: " . __FILE__ . "</p>";

// Check if we have a specific user to debug
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 110001; // Default to user 110001

// Check for actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Process test upload
if ($action === 'test_upload' && isset($_FILES['profile_photo'])) {
    echo "<h2>Processing Test Upload</h2>";
    
    $file = $_FILES['profile_photo'];
    echo "<pre>";
    print_r($file);
    echo "</pre>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        echo "<p style='color:green;'>✅ File uploaded successfully</p>";
        
        // Generate filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = time() . '-' . uniqid() . '.' . $extension;
        
        // Ensure directories exist
        foreach ([$profilePhotosPath, $publicProfilePhotosPath] as $dir) {
            if (!file_exists($dir)) {
                if (mkdir($dir, 0777, true)) {
                    echo "<p style='color:green;'>✅ Created directory: $dir</p>";
                    chmod($dir, 0777);
                } else {
                    echo "<p style='color:red;'>❌ Failed to create directory: $dir</p>";
                    $error = error_get_last();
                    echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
                }
            }
        }
        
        // Move to storage
        $storageDestination = $profilePhotosPath . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $storageDestination)) {
            echo "<p style='color:green;'>✅ File moved to storage: $storageDestination</p>";
            chmod($storageDestination, 0644);
            
            // Copy to public
            $publicDestination = $publicProfilePhotosPath . '/' . $filename;
            if (copy($storageDestination, $publicDestination)) {
                echo "<p style='color:green;'>✅ File copied to public: $publicDestination</p>";
                chmod($publicDestination, 0644);
                
                // Update database if requested
                if (isset($_POST['update_db']) && $_POST['update_db'] == '1') {
                    try {
                        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        
                        $relativePath = 'profile-photos/' . $filename;
                        $stmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :user_id");
                        $stmt->bindParam(':path', $relativePath);
                        $stmt->bindParam(':user_id', $userId);
                        
                        if ($stmt->execute()) {
                            echo "<p style='color:green;'>✅ Database updated successfully</p>";
                            echo "<p>Updated profile_photo_path to: $relativePath</p>";
                        } else {
                            echo "<p style='color:red;'>❌ Failed to update database</p>";
                        }
                    } catch(PDOException $e) {
                        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
                    }
                }
                
                // Show preview
                echo "<h3>Preview</h3>";
                echo "<p>Storage URL: /storage/profile-photos/$filename</p>";
                echo "<img src='/storage/profile-photos/$filename' style='max-width: 200px; border: 1px solid #ddd;'>";
            } else {
                echo "<p style='color:red;'>❌ Failed to copy file to public</p>";
                $error = error_get_last();
                echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Failed to move uploaded file</p>";
            $error = error_get_last();
            echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ File upload error: " . $file['error'] . "</p>";
        
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        if (isset($uploadErrors[$file['error']])) {
            echo "<p>Error description: " . $uploadErrors[$file['error']] . "</p>";
        }
    }
}

// Check directory structure
echo "<h2>Directory Structure</h2>";
$directories = [
    'storage/app/public' => $storageAppPublicPath,
    'storage/app/public/profile-photos' => $profilePhotosPath,
    'public/storage' => $publicStoragePath,
    'public/storage/profile-photos' => $publicProfilePhotosPath,
];

foreach ($directories as $name => $path) {
    echo "<h3>$name</h3>";
    
    if (file_exists($path)) {
        echo "<p style='color:green;'>✅ Directory exists</p>";
        
        // Check permissions
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "<p>Permissions: $perms</p>";
        
        // Check if writable
        if (is_writable($path)) {
            echo "<p style='color:green;'>✅ Directory is writable</p>";
        } else {
            echo "<p style='color:red;'>❌ Directory is NOT writable</p>";
        }
        
        // List files in profile-photos directories
        if (strpos($name, 'profile-photos') !== false) {
            $files = scandir($path);
            $fileCount = 0;
            
            echo "<p>Files in directory:</p>";
            echo "<ul>";
            foreach ($files as $file) {
                if ($file != "." && $file != ".." && is_file($path . '/' . $file)) {
                    $fileCount++;
                    $fileSize = filesize($path . '/' . $file);
                    $filePerms = substr(sprintf('%o', fileperms($path . '/' . $file)), -4);
                    echo "<li>$file - Size: $fileSize bytes, Permissions: $filePerms</li>";
                }
            }
            echo "</ul>";
            
            if ($fileCount == 0) {
                echo "<p style='color:orange;'>⚠️ No files found in this directory</p>";
            } else {
                echo "<p>Total files: $fileCount</p>";
            }
        }
    } else {
        echo "<p style='color:red;'>❌ Directory does not exist</p>";
    }
}

// Check user information in database
echo "<h2>User Information</h2>";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id OR user_id = :user_id_alt");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':user_id_alt', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p style='color:green;'>✅ User found in database</p>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        
        foreach ($user as $field => $value) {
            if ($field !== 'password' && $field !== 'remember_token') {
                echo "<tr><td>$field</td><td>" . ($value ?? '<em>NULL</em>') . "</td></tr>";
            }
        }
        
        echo "</table>";
        
        // Check profile photo path
        if (!empty($user['profile_photo_path'])) {
            $photoPath = $user['profile_photo_path'];
            echo "<h3>Profile Photo Path: $photoPath</h3>";
            
            // Check if file exists in storage
            $storageFilePath = $profilePhotosPath . '/' . $photoPath;
            $storageFileExists = file_exists($storageFilePath);
            echo "<p>Storage file exists: " . ($storageFileExists ? "✅ Yes" : "❌ No") . "</p>";
            if ($storageFileExists) {
                echo "<p>Storage file size: " . filesize($storageFilePath) . " bytes</p>";
                echo "<p>Storage file permissions: " . substr(sprintf('%o', fileperms($storageFilePath)), -4) . "</p>";
            }
            
            // Check if file exists in public
            $publicFilePath = $publicProfilePhotosPath . '/' . $photoPath;
            $publicFileExists = file_exists($publicFilePath);
            echo "<p>Public file exists: " . ($publicFileExists ? "✅ Yes" : "❌ No") . "</p>";
            if ($publicFileExists) {
                echo "<p>Public file size: " . filesize($publicFilePath) . " bytes</p>";
                echo "<p>Public file permissions: " . substr(sprintf('%o', fileperms($publicFilePath)), -4) . "</p>";
            }
            
            // Show preview
            echo "<h3>Profile Photo Preview</h3>";
            echo "<p>Expected URL: /storage/$photoPath</p>";
            echo "<img src='/storage/$photoPath' style='max-width: 200px; border: 1px solid #ddd;'>";
            
            // Fix missing files if needed
            if (!$storageFileExists && $publicFileExists) {
                echo "<p><a href='?action=copy_to_storage&user_id=$userId'>Copy file from public to storage</a></p>";
            } elseif ($storageFileExists && !$publicFileExists) {
                echo "<p><a href='?action=copy_to_public&user_id=$userId'>Copy file from storage to public</a></p>";
            } elseif (!$storageFileExists && !$publicFileExists) {
                echo "<p style='color:red;'>❌ File is missing in both locations</p>";
                echo "<p><a href='?action=reset_photo&user_id=$userId'>Reset to default photo</a></p>";
            }
        } else {
            echo "<p style='color:orange;'>⚠️ User has no profile photo path set</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ User not found in database</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    echo "<p>Please update the database credentials in this file.</p>";
}

// Process copy actions
if ($action === 'copy_to_storage' && $userId) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->prepare("SELECT profile_photo_path FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['profile_photo_path'])) {
            $photoPath = $result['profile_photo_path'];
            $sourceFile = $publicProfilePhotosPath . '/' . $photoPath;
            $destFile = $profilePhotosPath . '/' . $photoPath;
            
            // Ensure destination directory exists
            $destDir = dirname($destFile);
            if (!file_exists($destDir)) {
                mkdir($destDir, 0777, true);
            }
            
            if (copy($sourceFile, $destFile)) {
                echo "<p style='color:green;'>✅ Successfully copied file from public to storage</p>";
                chmod($destFile, 0644);
            } else {
                echo "<p style='color:red;'>❌ Failed to copy file</p>";
            }
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

if ($action === 'copy_to_public' && $userId) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->prepare("SELECT profile_photo_path FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['profile_photo_path'])) {
            $photoPath = $result['profile_photo_path'];
            $sourceFile = $profilePhotosPath . '/' . $photoPath;
            $destFile = $publicProfilePhotosPath . '/' . $photoPath;
            
            // Ensure destination directory exists
            $destDir = dirname($destFile);
            if (!file_exists($destDir)) {
                mkdir($destDir, 0777, true);
            }
            
            if (copy($sourceFile, $destFile)) {
                echo "<p style='color:green;'>✅ Successfully copied file from storage to public</p>";
                chmod($destFile, 0644);
            } else {
                echo "<p style='color:red;'>❌ Failed to copy file</p>";
            }
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

if ($action === 'reset_photo' && $userId) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $defaultPhoto = 'kofa.png';
        $stmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :user_id");
        $stmt->bindParam(':path', $defaultPhoto);
        $stmt->bindParam(':user_id', $userId);
        
        if ($stmt->execute()) {
            echo "<p style='color:green;'>✅ Reset profile photo to default</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to reset profile photo</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

// Check User model
echo "<h2>User Model</h2>";

$userModelPath = $basePath . '/app/Models/User.php';
if (file_exists($userModelPath)) {
    echo "<p style='color:green;'>✅ User model exists</p>";
    
    $userModelContent = file_get_contents($userModelPath);
    
    // Check if the User model has a profile_photo_url accessor
    if (preg_match('/public\s+function\s+getProfilePhotoUrlAttribute\s*\(.*?\)\s*\{.*?\}/s', $userModelContent, $matches)) {
        echo "<p style='color:green;'>✅ User model has profile_photo_url accessor</p>";
        echo "<pre style='background-color: #f5f5f5; padding: 10px; overflow: auto; max-height: 300px;'>";
        echo htmlspecialchars($matches[0]);
        echo "</pre>";
    } else {
        echo "<p style='color:red;'>❌ User model does not have profile_photo_url accessor</p>";
    }
    
    // Check if the User model appends profile_photo_url
    if (preg_match('/protected\s+\$appends\s*=\s*\[(.*?)\]/s', $userModelContent, $matches)) {
        echo "<p>Appended attributes: " . htmlspecialchars($matches[1]) . "</p>";
        
        if (strpos($matches[1], 'profile_photo_url') !== false) {
            echo "<p style='color:green;'>✅ profile_photo_url is appended to the model</p>";
        } else {
            echo "<p style='color:red;'>❌ profile_photo_url is NOT appended to the model</p>";
        }
    }
} else {
    echo "<p style='color:red;'>❌ User model not found at $userModelPath</p>";
}

// Check ProfileController
echo "<h2>ProfileController</h2>";

$controllerPath = $basePath . '/app/Http/Controllers/ProfileController.php';
if (file_exists($controllerPath)) {
    echo "<p style='color:green;'>✅ ProfileController exists</p>";
    
    $controllerContent = file_get_contents($controllerPath);
    
    // Check if the controller handles file uploads
    if (strpos($controllerContent, 'hasFile(\'profile_photo\')') !== false) {
        echo "<p style='color:green;'>✅ Controller checks for file uploads</p>";
    } else {
        echo "<p style='color:red;'>❌ Controller does not check for file uploads</p>";
    }
    
    // Check if the controller saves files to both locations
    if (strpos($controllerContent, 'copy($sourceFile, $destFile)') !== false) {
        echo "<p style='color:green;'>✅ Controller copies files to public directory</p>";
    } else {
        echo "<p style='color:red;'>❌ Controller does not copy files to public directory</p>";
    }
    
    // Check for admin restrictions
    if (strpos($controllerContent, '!$user->isAdmin()') !== false) {
        echo "<p style='color:orange;'>⚠️ Controller restricts profile photo uploads to admin users only</p>";
    }
} else {
    echo "<p style='color:red;'>❌ ProfileController not found at $controllerPath</p>";
}

// Test form for uploading a profile photo
echo "<h2>Test Profile Photo Upload</h2>";
echo "<p>Use this form to test uploading a profile photo:</p>";

echo "<form action='?action=test_upload&user_id=$userId' method='post' enctype='multipart/form-data'>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label for='profile_photo'>Select a profile photo:</label>";
echo "<input type='file' name='profile_photo' id='profile_photo' accept='image/*'>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label>";
echo "<input type='checkbox' name='update_db' value='1' checked> Update database with new photo path";
echo "</label>";
echo "</div>";
echo "<button type='submit' style='background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;'>Upload Photo</button>";
echo "</form>";

echo "<h2>Recommendations</h2>";
echo "<ol>";
echo "<li>Check if the user is an admin (only admins can upload profile photos)</li>";
echo "<li>Verify that the profile photo directories exist and are writable</li>";
echo "<li>Make sure files are being copied to both storage and public directories</li>";
echo "<li>Check that the User model has a proper profile_photo_url accessor</li>";
echo "<li>Clear the browser cache to ensure you're seeing the latest version of the profile photo</li>";
echo "</ol>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 