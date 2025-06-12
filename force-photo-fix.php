<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>FORCE Profile Photo Fix</h1>";
echo "<p style='color:red;'><strong>IMPORTANT:</strong> This script makes direct changes to files and database. Use with caution.</p>";

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

// Target user ID - admin user
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 110001;

// Create necessary directories with aggressive permissions
foreach ([$storageAppPublicPath, $profilePhotosPath, $publicStoragePath, $publicProfilePhotosPath] as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "<p style='color:green;'>✅ Created directory: $dir</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to create directory: $dir</p>";
            $error = error_get_last();
            echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color:green;'>✅ Directory exists: $dir</p>";
    }
    
    // Force permissions
    chmod($dir, 0777);
    echo "<p>Set permissions to 0777 for: $dir</p>";
}

// Check if we're processing a file upload
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['profile_photo'];
    
    // Generate filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'admin-' . time() . '.' . $extension;
    $relativePath = 'profile-photos/' . $filename;
    
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
            
            // Update database
            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :user_id");
                $stmt->bindParam(':path', $relativePath);
                $stmt->bindParam(':user_id', $userId);
                
                if ($stmt->execute()) {
                    echo "<p style='color:green;'>✅ Database updated successfully</p>";
                    echo "<p>Updated profile_photo_path to: $relativePath</p>";
                    
                    // Clear cache files
                    $cacheFiles = glob($basePath . '/bootstrap/cache/*.php');
                    foreach ($cacheFiles as $file) {
                        if (@unlink($file)) {
                            echo "<p style='color:green;'>✅ Cleared cache file: $file</p>";
                        }
                    }
                } else {
                    echo "<p style='color:red;'>❌ Failed to update database</p>";
                }
            } catch(PDOException $e) {
                echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
                echo "<p>Please update the database credentials in this file.</p>";
            }
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
} else if (isset($_POST['action']) && $_POST['action'] === 'generate') {
    // Generate a test profile photo
    $testPhotoContent = <<<'EOT'
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <rect width="200" height="200" fill="#e74c3c"/>
  <circle cx="100" cy="70" r="50" fill="#ffffff"/>
  <circle cx="100" cy="180" r="80" fill="#ffffff"/>
  <text x="100" y="100" font-family="Arial" font-size="24" text-anchor="middle" fill="#ffffff">ADMIN</text>
</svg>
EOT;

    // Generate a unique filename
    $filename = 'admin-' . time() . '.svg';
    $relativePath = 'profile-photos/' . $filename;
    
    // Save to storage
    $storageFilePath = $profilePhotosPath . '/' . $filename;
    if (file_put_contents($storageFilePath, $testPhotoContent)) {
        echo "<p style='color:green;'>✅ Created profile photo in storage: $storageFilePath</p>";
        chmod($storageFilePath, 0644);
        
        // Copy to public
        $publicFilePath = $publicProfilePhotosPath . '/' . $filename;
        if (copy($storageFilePath, $publicFilePath)) {
            echo "<p style='color:green;'>✅ Copied profile photo to public: $publicFilePath</p>";
            chmod($publicFilePath, 0644);
            
            // Update database
            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :user_id");
                $stmt->bindParam(':path', $relativePath);
                $stmt->bindParam(':user_id', $userId);
                
                if ($stmt->execute()) {
                    echo "<p style='color:green;'>✅ Database updated successfully</p>";
                    echo "<p>Updated profile_photo_path to: $relativePath</p>";
                    
                    // Clear cache files
                    $cacheFiles = glob($basePath . '/bootstrap/cache/*.php');
                    foreach ($cacheFiles as $file) {
                        if (@unlink($file)) {
                            echo "<p style='color:green;'>✅ Cleared cache file: $file</p>";
                        }
                    }
                    
                    // Show preview
                    echo "<h3>New Profile Photo</h3>";
                    echo "<p>URL: /storage/$relativePath</p>";
                    echo "<img src='/storage/$relativePath' style='max-width: 200px; border: 1px solid #ddd;'>";
                    echo "<p>If the image doesn't appear, try <a href='/storage/$relativePath' target='_blank'>this direct link</a>.</p>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to update database</p>";
                }
            } catch(PDOException $e) {
                echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
                echo "<p>Please update the database credentials in this file.</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Failed to copy profile photo to public</p>";
            $error = error_get_last();
            echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Failed to create profile photo in storage</p>";
        $error = error_get_last();
        echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
    }
} else if (isset($_POST['action']) && $_POST['action'] === 'reset') {
    // Reset to default photo
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $defaultPhoto = 'kofa.png';
        $stmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :user_id");
        $stmt->bindParam(':path', $defaultPhoto);
        $stmt->bindParam(':user_id', $userId);
        
        if ($stmt->execute()) {
            echo "<p style='color:green;'>✅ Reset profile photo to default</p>";
            
            // Clear cache files
            $cacheFiles = glob($basePath . '/bootstrap/cache/*.php');
            foreach ($cacheFiles as $file) {
                if (@unlink($file)) {
                    echo "<p style='color:green;'>✅ Cleared cache file: $file</p>";
                }
            }
        } else {
            echo "<p style='color:red;'>❌ Failed to reset profile photo</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
        echo "<p>Please update the database credentials in this file.</p>";
    }
}

// Fix User model
$userModelPath = $basePath . '/app/Models/User.php';
if (file_exists($userModelPath)) {
    // Create a backup
    $backupPath = $userModelPath . '.backup.' . time();
    if (copy($userModelPath, $backupPath)) {
        echo "<p style='color:green;'>✅ Created backup of User model at $backupPath</p>";
        
        $userModelContent = file_get_contents($userModelPath);
        
        // Fix the getProfilePhotoUrlAttribute method
        $pattern = '/public\s+function\s+getProfilePhotoUrlAttribute\s*\(\)\s*\{.*?\}/s';
        $replacement = 'public function getProfilePhotoUrlAttribute()
    {
        // Direct override - always check the path first
        $photoPath = $this->profile_photo_path;
        
        // Handle empty, null, or invalid profile photo paths
        if (empty($photoPath) || $photoPath === \'0\' || $photoPath === 0 || $photoPath === \'null\' || $photoPath === \'NULL\') {
            return asset(\'kofa.png\');
        }
        
        // Check if it\'s the default photo
        if ($photoPath === \'kofa.png\') {
            return asset(\'kofa.png\');
        }
        
        // Always return the storage path for non-default photos
        return asset(\'storage/\' . $photoPath);
    }';
        
        if (preg_match($pattern, $userModelContent)) {
            $updatedContent = preg_replace($pattern, $replacement, $userModelContent);
            if (file_put_contents($userModelPath, $updatedContent)) {
                echo "<p style='color:green;'>✅ Updated User model getProfilePhotoUrlAttribute method</p>";
            } else {
                echo "<p style='color:red;'>❌ Failed to update User model</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Could not find getProfilePhotoUrlAttribute method in User model</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Failed to create backup of User model</p>";
    }
} else {
    echo "<p style='color:red;'>❌ User model not found at $userModelPath</p>";
}

// Get current user info
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->prepare("SELECT id, name, email, profile_photo_path FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<h2>Current User Information</h2>";
        echo "<p>ID: " . $user['id'] . "</p>";
        echo "<p>Name: " . $user['name'] . "</p>";
        echo "<p>Email: " . $user['email'] . "</p>";
        echo "<p>Profile Photo Path: " . ($user['profile_photo_path'] ?? '<em>None</em>') . "</p>";
        
        // Check profile photo
        if (!empty($user['profile_photo_path'])) {
            $photoPath = $user['profile_photo_path'];
            
            // Check if file exists in storage
            $storageFilePath = $storageAppPublicPath . '/' . $photoPath;
            $storageFileExists = file_exists($storageFilePath);
            echo "<p>Storage file exists: " . ($storageFileExists ? "✅ Yes" : "❌ No") . "</p>";
            echo "<p>Storage path: $storageFilePath</p>";
            
            // Check if file exists in public
            $publicFilePath = $publicStoragePath . '/' . $photoPath;
            $publicFileExists = file_exists($publicFilePath);
            echo "<p>Public file exists: " . ($publicFileExists ? "✅ Yes" : "❌ No") . "</p>";
            echo "<p>Public path: $publicFilePath</p>";
            
            // Fix missing files if needed
            if (!$storageFileExists && $publicFileExists) {
                if (copy($publicFilePath, $storageFilePath)) {
                    echo "<p style='color:green;'>✅ Copied file from public to storage</p>";
                    chmod($storageFilePath, 0644);
                } else {
                    echo "<p style='color:red;'>❌ Failed to copy file from public to storage</p>";
                }
            } elseif ($storageFileExists && !$publicFileExists) {
                if (copy($storageFilePath, $publicFilePath)) {
                    echo "<p style='color:green;'>✅ Copied file from storage to public</p>";
                    chmod($publicFilePath, 0644);
                } else {
                    echo "<p style='color:red;'>❌ Failed to copy file from storage to public</p>";
                }
            } elseif (!$storageFileExists && !$publicFileExists && $photoPath !== 'kofa.png') {
                echo "<p style='color:red;'>❌ Profile photo file is missing in both locations</p>";
                echo "<p>Consider resetting to default or uploading a new photo</p>";
            }
            
            // Show preview
            echo "<h3>Current Profile Photo</h3>";
            echo "<p>URL: /storage/$photoPath</p>";
            echo "<img src='/storage/$photoPath' style='max-width: 200px; border: 1px solid #ddd;'>";
            echo "<p>If the image doesn't appear, try <a href='/storage/$photoPath' target='_blank'>this direct link</a>.</p>";
        } else {
            echo "<p style='color:orange;'>⚠️ User has no profile photo set</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ User not found</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    echo "<p>Please update the database credentials in this file.</p>";
}

// Form for actions
echo "<h2>Actions</h2>";

// Upload form
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>1. Upload New Profile Photo</h3>";
echo "<form action='' method='post' enctype='multipart/form-data'>";
echo "<div style='margin-bottom: 10px;'>";
echo "<input type='file' name='profile_photo' accept='image/*'>";
echo "</div>";
echo "<button type='submit' style='background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;'>Upload Photo</button>";
echo "</form>";
echo "</div>";

// Generate form
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>2. Generate Test Profile Photo</h3>";
echo "<form action='' method='post'>";
echo "<input type='hidden' name='action' value='generate'>";
echo "<button type='submit' style='background-color: #2196F3; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;'>Generate Test Photo</button>";
echo "</form>";
echo "</div>";

// Reset form
echo "<div style='margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
echo "<h3>3. Reset to Default Photo</h3>";
echo "<form action='' method='post'>";
echo "<input type='hidden' name='action' value='reset'>";
echo "<button type='submit' style='background-color: #f44336; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;'>Reset to Default</button>";
echo "</form>";
echo "</div>";

echo "<h2>Browser Cache</h2>";
echo "<p>Your browser might be caching the old profile photo. Try clearing your browser cache or opening the site in a private/incognito window.</p>";
echo "<p>You can also try adding a random query parameter to force a refresh:</p>";
echo "<p><a href='/admin/users/110001/edit?nocache=" . time() . "' target='_blank'>Open admin edit page with cache busting</a></p>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Update the database password in this script</li>";
echo "<li>Choose one of the actions above to fix the profile photo</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Try accessing the admin page with the cache busting parameter</li>";
echo "</ol>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 