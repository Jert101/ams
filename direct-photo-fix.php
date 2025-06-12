<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Direct Profile Photo Fix</h1>";

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
        chmod($dir, 0777);
    }
}

// Create a test profile photo
$testPhotoContent = <<<'EOT'
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <rect width="200" height="200" fill="#3498db"/>
  <circle cx="100" cy="70" r="50" fill="#ecf0f1"/>
  <circle cx="100" cy="180" r="80" fill="#ecf0f1"/>
  <text x="100" y="100" font-family="Arial" font-size="24" text-anchor="middle" fill="#2c3e50">Admin</text>
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
            
            // Get current user info
            $stmt = $conn->prepare("SELECT name, email, profile_photo_path FROM users WHERE id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                echo "<h2>Current User Information</h2>";
                echo "<p>Name: " . $user['name'] . "</p>";
                echo "<p>Email: " . $user['email'] . "</p>";
                echo "<p>Current Profile Photo Path: " . ($user['profile_photo_path'] ?? '<em>None</em>') . "</p>";
                
                // Update the profile photo path
                $stmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :user_id");
                $stmt->bindParam(':path', $relativePath);
                $stmt->bindParam(':user_id', $userId);
                
                if ($stmt->execute()) {
                    echo "<p style='color:green;'>✅ Database updated successfully</p>";
                    echo "<p>New Profile Photo Path: $relativePath</p>";
                    
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
            } else {
                echo "<p style='color:red;'>❌ User not found</p>";
            }
        } catch(PDOException $e) {
            echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
            echo "<p>Please update the database credentials in this file.</p>";
        }
        
        // Show preview
        echo "<h2>New Profile Photo</h2>";
        echo "<p>URL: /storage/$relativePath</p>";
        echo "<img src='/storage/$relativePath' style='max-width: 200px; border: 1px solid #ddd;'>";
        echo "<p>If the image doesn't appear, try <a href='/storage/$relativePath' target='_blank'>this direct link</a>.</p>";
        
        // Check User model
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

echo "<h2>Browser Cache</h2>";
echo "<p>Your browser might be caching the old profile photo. Try clearing your browser cache or opening the site in a private/incognito window.</p>";
echo "<p>You can also try adding a random query parameter to force a refresh:</p>";
echo "<p><a href='/admin/users/110001/edit?nocache=" . time() . "' target='_blank'>Open admin edit page with cache busting</a></p>";

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Update the database password in this script</li>";
echo "<li>Run the script to create a new profile photo and update the database</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Try accessing the admin page with the cache busting parameter</li>";
echo "</ol>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 