<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix User Profile Photo</h1>";

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

// Get user ID from URL parameter or use default
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 110001;

// Process actions
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Check if we need to upload a new photo
if ($action === 'upload' && isset($_FILES['profile_photo'])) {
    $file = $_FILES['profile_photo'];
    
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
                
                // Update database
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
                        
                        // Clear cache files
                        $cacheFiles = [
                            $basePath . '/bootstrap/cache/config.php',
                            $basePath . '/bootstrap/cache/routes.php'
                        ];
                        
                        foreach ($cacheFiles as $file) {
                            if (file_exists($file) && @unlink($file)) {
                                echo "<p style='color:green;'>✅ Cleared cache file: $file</p>";
                            }
                        }
                    } else {
                        echo "<p style='color:red;'>❌ Failed to update database</p>";
                    }
                } catch(PDOException $e) {
                    echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
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
    } else {
        echo "<p style='color:red;'>❌ File upload error: " . $file['error'] . "</p>";
    }
}

// Get user info
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<h2>User Information</h2>";
        echo "<p>Name: " . $user['name'] . "</p>";
        echo "<p>Email: " . $user['email'] . "</p>";
        echo "<p>User ID: " . $user['user_id'] . "</p>";
        echo "<p>Profile Photo Path: " . ($user['profile_photo_path'] ?? '<em>None</em>') . "</p>";
        
        // Check profile photo
        if (!empty($user['profile_photo_path'])) {
            $photoPath = $user['profile_photo_path'];
            
            // Check if file exists in storage
            $storageFilePath = $profilePhotosPath . '/' . $photoPath;
            $storageFileExists = file_exists($storageFilePath);
            echo "<p>Storage file exists: " . ($storageFileExists ? "✅ Yes" : "❌ No") . "</p>";
            
            // Check if file exists in public
            $publicFilePath = $publicProfilePhotosPath . '/' . $photoPath;
            $publicFileExists = file_exists($publicFilePath);
            echo "<p>Public file exists: " . ($publicFileExists ? "✅ Yes" : "❌ No") . "</p>";
            
            // Fix missing files if needed
            if (!$storageFileExists && $publicFileExists) {
                if ($action === 'copy_to_storage') {
                    if (copy($publicFilePath, $storageFilePath)) {
                        echo "<p style='color:green;'>✅ Copied file from public to storage</p>";
                        chmod($storageFilePath, 0644);
                        $storageFileExists = true;
                    } else {
                        echo "<p style='color:red;'>❌ Failed to copy file from public to storage</p>";
                    }
                } else {
                    echo "<p><a href='?user_id=$userId&action=copy_to_storage'>Copy file from public to storage</a></p>";
                }
            }
            
            if ($storageFileExists && !$publicFileExists) {
                if ($action === 'copy_to_public') {
                    if (copy($storageFilePath, $publicFilePath)) {
                        echo "<p style='color:green;'>✅ Copied file from storage to public</p>";
                        chmod($publicFilePath, 0644);
                        $publicFileExists = true;
                    } else {
                        echo "<p style='color:red;'>❌ Failed to copy file from storage to public</p>";
                    }
                } else {
                    echo "<p><a href='?user_id=$userId&action=copy_to_public'>Copy file from storage to public</a></p>";
                }
            }
            
            // Show preview
            if ($publicFileExists) {
                echo "<h3>Current Profile Photo</h3>";
                echo "<img src='/storage/$photoPath' style='max-width: 200px; border: 1px solid #ddd;'>";
            } else {
                echo "<p style='color:red;'>❌ Profile photo file is missing</p>";
            }
        } else {
            echo "<p style='color:orange;'>⚠️ User has no profile photo set</p>";
        }
        
        // Upload form
        echo "<h2>Upload New Profile Photo</h2>";
        echo "<form action='?user_id=$userId&action=upload' method='post' enctype='multipart/form-data'>";
        echo "<div style='margin-bottom: 10px;'>";
        echo "<input type='file' name='profile_photo' accept='image/*'>";
        echo "</div>";
        echo "<button type='submit' style='background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;'>Upload Photo</button>";
        echo "</form>";
        
        // Reset to default
        echo "<h2>Reset to Default Photo</h2>";
        echo "<p><a href='?user_id=$userId&action=reset' onclick='return confirm(\"Are you sure you want to reset the profile photo?\");'>Reset to Default Photo</a></p>";
        
        // Process reset action
        if ($action === 'reset') {
            $defaultPhoto = 'kofa.png';
            $stmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :user_id");
            $stmt->bindParam(':path', $defaultPhoto);
            $stmt->bindParam(':user_id', $userId);
            
            if ($stmt->execute()) {
                echo "<p style='color:green;'>✅ Reset profile photo to default</p>";
                echo "<p>Please <a href='?user_id=$userId'>refresh</a> to see the changes</p>";
            } else {
                echo "<p style='color:red;'>❌ Failed to reset profile photo</p>";
            }
        }
    } else {
        echo "<p style='color:red;'>❌ User not found</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    echo "<p>Please update the database credentials in this file.</p>";
}

// Check User model
echo "<h2>User Model Check</h2>";

$userModelPath = $basePath . '/app/Models/User.php';
if (file_exists($userModelPath)) {
    $userModelContent = file_get_contents($userModelPath);
    
    // Check if the User model has a profile_photo_url accessor
    if (preg_match('/public\s+function\s+getProfilePhotoUrlAttribute\s*\(.*?\)\s*\{.*?\}/s', $userModelContent, $matches)) {
        echo "<p style='color:green;'>✅ User model has profile_photo_url accessor</p>";
    } else {
        echo "<p style='color:red;'>❌ User model does not have profile_photo_url accessor</p>";
        
        // Fix the User model
        if ($action === 'fix_model') {
            $pattern = '/class User extends Authenticatable\s*\{/';
            $replacement = 'class User extends Authenticatable
{
    /**
     * Get the URL to the user\'s profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        // Handle empty, null, or invalid profile photo paths
        if (empty($this->profile_photo_path) || 
            $this->profile_photo_path === \'0\' || 
            $this->profile_photo_path === 0 || 
            $this->profile_photo_path === \'null\' ||
            $this->profile_photo_path === \'NULL\') {
            return asset(\'kofa.png\');
        }
        
        // Check if it\'s the default photo
        if ($this->profile_photo_path === \'kofa.png\') {
            return asset(\'kofa.png\');
        }
        
        // Check if it\'s a full URL
        if (filter_var($this->profile_photo_path, FILTER_VALIDATE_URL)) {
            return $this->profile_photo_path;
        }
        
        // IMPORTANT: First check if the file exists in public/storage directly
        if (file_exists(public_path(\'storage/\' . $this->profile_photo_path))) {
            return asset(\'storage/\' . $this->profile_photo_path);
        }
        
        // Then check if it exists directly in public directory
        if (file_exists(public_path($this->profile_photo_path))) {
            return asset($this->profile_photo_path);
        }
        
        // Log the issue and return the default
        \\Log::warning(\'Profile photo not found for user: \' . $this->id, [
            \'profile_photo_path\' => $this->profile_photo_path,
            \'checked_paths\' => [
                \'public/storage/\' . $this->profile_photo_path,
                \'public/\' . $this->profile_photo_path
            ]
        ]);
        
        return asset(\'kofa.png\');
    }';
            
            // Create a backup
            $backupPath = $userModelPath . '.backup.' . time();
            if (copy($userModelPath, $backupPath)) {
                // Update the file
                $updatedContent = preg_replace($pattern, $replacement, $userModelContent);
                if (file_put_contents($userModelPath, $updatedContent)) {
                    echo "<p style='color:green;'>✅ Fixed User model</p>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to update User model</p>";
                }
            } else {
                echo "<p style='color:red;'>❌ Failed to create backup of User model</p>";
            }
        } else {
            echo "<p><a href='?user_id=$userId&action=fix_model'>Fix User Model</a></p>";
        }
    }
    
    // Check if the User model appends profile_photo_url
    if (preg_match('/protected\s+\$appends\s*=\s*\[(.*?)\]/s', $userModelContent, $matches)) {
        if (strpos($matches[1], 'profile_photo_url') !== false) {
            echo "<p style='color:green;'>✅ profile_photo_url is appended to the model</p>";
        } else {
            echo "<p style='color:red;'>❌ profile_photo_url is NOT appended to the model</p>";
            
            // Fix the appends
            if ($action === 'fix_appends') {
                $pattern = '/protected\s+\$appends\s*=\s*\[(.*?)\]/s';
                $replacement = 'protected $appends = [
        \'profile_photo_url\',';
                
                // Create a backup
                $backupPath = $userModelPath . '.backup.' . time();
                if (copy($userModelPath, $backupPath)) {
                    // Update the file
                    $updatedContent = preg_replace($pattern, $replacement, $userModelContent);
                    if (file_put_contents($userModelPath, $updatedContent)) {
                        echo "<p style='color:green;'>✅ Fixed User model appends</p>";
                    } else {
                        echo "<p style='color:red;'>❌ Failed to update User model appends</p>";
                    }
                } else {
                    echo "<p style='color:red;'>❌ Failed to create backup of User model</p>";
                }
            } else {
                echo "<p><a href='?user_id=$userId&action=fix_appends'>Fix User Model Appends</a></p>";
            }
        }
    }
} else {
    echo "<p style='color:red;'>❌ User model not found at $userModelPath</p>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Update the database password in this script</li>";
echo "<li>Check if the profile photo files exist in both storage and public directories</li>";
echo "<li>Try uploading a new photo using the form above</li>";
echo "<li>Clear your browser cache to ensure you're seeing the latest version</li>";
echo "<li>Check if the User model is properly configured</li>";
echo "</ol>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 