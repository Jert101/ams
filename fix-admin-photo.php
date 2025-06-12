<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix Admin Profile Photo</h1>";

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
$userId = 110001;

// Create necessary directories
foreach ([$storageAppPublicPath, $profilePhotosPath, $publicStoragePath, $publicProfilePhotosPath] as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "<p style='color:green;'>✅ Created directory: $dir</p>";
            chmod($dir, 0777);
        } else {
            echo "<p style='color:red;'>❌ Failed to create directory: $dir</p>";
            $error = error_get_last();
            echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color:green;'>✅ Directory exists: $dir</p>";
        
        // Ensure permissions are correct
        chmod($dir, 0777);
    }
}

// Process upload
if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['profile_photo'];
    
    // Generate filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '-' . uniqid() . '.' . $extension;
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
                    $cacheFiles = [
                        $basePath . '/bootstrap/cache/config.php',
                        $basePath . '/bootstrap/cache/routes.php'
                    ];
                    
                    foreach ($cacheFiles as $file) {
                        if (file_exists($file) && @unlink($file)) {
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
            echo "<p style='color:red;'>❌ Failed to copy file to public</p>";
            $error = error_get_last();
            echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Failed to move uploaded file</p>";
        $error = error_get_last();
        echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
    }
}

// Check current user info
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<h2>Admin User Information</h2>";
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
            
            // Fix missing files
            if (!$storageFileExists && $publicFileExists) {
                if (copy($publicFilePath, $storageFilePath)) {
                    echo "<p style='color:green;'>✅ Copied file from public to storage</p>";
                    chmod($storageFilePath, 0644);
                    $storageFileExists = true;
                } else {
                    echo "<p style='color:red;'>❌ Failed to copy file from public to storage</p>";
                }
            }
            
            if ($storageFileExists && !$publicFileExists) {
                if (copy($storageFilePath, $publicFilePath)) {
                    echo "<p style='color:green;'>✅ Copied file from storage to public</p>";
                    chmod($publicFilePath, 0644);
                    $publicFileExists = true;
                } else {
                    echo "<p style='color:red;'>❌ Failed to copy file from storage to public</p>";
                }
            }
            
            // Show preview
            if ($publicFileExists) {
                echo "<h3>Current Profile Photo</h3>";
                echo "<p>URL: /storage/$photoPath</p>";
                echo "<img src='/storage/$photoPath' style='max-width: 200px; border: 1px solid #ddd;'>";
                echo "<p>If the image doesn't appear, try <a href='/storage/$photoPath' target='_blank'>this direct link</a>.</p>";
            } else {
                echo "<p style='color:red;'>❌ Profile photo file is missing</p>";
            }
        } else {
            echo "<p style='color:orange;'>⚠️ User has no profile photo set</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Admin user not found</p>";
    }
} catch(PDOException $e) {
    echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    echo "<p>Please update the database credentials in this file.</p>";
}

// Fix User model if needed
$userModelPath = $basePath . '/app/Models/User.php';
if (file_exists($userModelPath)) {
    $userModelContent = file_get_contents($userModelPath);
    
    // Check and fix the getProfilePhotoUrlAttribute method
    if (!preg_match('/public\s+function\s+getProfilePhotoUrlAttribute\s*\(.*?\)\s*\{.*?IMPORTANT.*?public\/storage.*?\}/s', $userModelContent)) {
        // Create a backup
        $backupPath = $userModelPath . '.backup.' . time();
        if (copy($userModelPath, $backupPath)) {
            echo "<p style='color:green;'>✅ Created backup of User model at $backupPath</p>";
            
            // Replace or add the method
            $pattern = '/public function getProfilePhotoUrlAttribute\(\)\s*\{.*?\}/s';
            $replacement = 'public function getProfilePhotoUrlAttribute()
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
            
            if (preg_match($pattern, $userModelContent)) {
                $updatedContent = preg_replace($pattern, $replacement, $userModelContent);
                if (file_put_contents($userModelPath, $updatedContent)) {
                    echo "<p style='color:green;'>✅ Updated User model getProfilePhotoUrlAttribute method</p>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to update User model</p>";
                }
            } else {
                echo "<p style='color:orange;'>⚠️ Could not find getProfilePhotoUrlAttribute method to replace</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Failed to create backup of User model</p>";
        }
    } else {
        echo "<p style='color:green;'>✅ User model already has the correct getProfilePhotoUrlAttribute method</p>";
    }
} else {
    echo "<p style='color:red;'>❌ User model not found at $userModelPath</p>";
}

// Upload form
echo "<h2>Upload New Profile Photo</h2>";
echo "<form action='' method='post' enctype='multipart/form-data'>";
echo "<div style='margin-bottom: 10px;'>";
echo "<input type='file' name='profile_photo' accept='image/*'>";
echo "</div>";
echo "<button type='submit' style='background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;'>Upload Photo</button>";
echo "</form>";

// Reset to default
echo "<h2>Reset to Default Photo</h2>";
echo "<form action='' method='post'>";
echo "<input type='hidden' name='action' value='reset'>";
echo "<button type='submit' style='background-color: #f44336; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;'>Reset to Default Photo</button>";
echo "</form>";

// Process reset
if (isset($_POST['action']) && $_POST['action'] === 'reset') {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $defaultPhoto = 'kofa.png';
        $stmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :user_id");
        $stmt->bindParam(':path', $defaultPhoto);
        $stmt->bindParam(':user_id', $userId);
        
        if ($stmt->execute()) {
            echo "<p style='color:green;'>✅ Reset profile photo to default</p>";
            echo "<p>Please <a href=''>refresh</a> to see the changes</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to reset profile photo</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>Browser Cache</h2>";
echo "<p>Your browser might be caching the old profile photo. Try clearing your browser cache or opening the site in a private/incognito window.</p>";
echo "<p>You can also try adding a random query parameter to force a refresh:</p>";
echo "<p><a href='/admin/users/110001/edit?nocache=" . time() . "' target='_blank'>Open admin edit page with cache busting</a></p>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Update the database password in this script</li>";
echo "<li>Upload a new profile photo using the form above</li>";
echo "<li>Check if the photo appears correctly</li>";
echo "<li>Clear your browser cache if needed</li>";
echo "<li>Try accessing the admin page with the cache busting parameter</li>";
echo "</ol>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 