<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>EMERGENCY Profile Photo Fix</h1>";
echo "<p style='color:red;'><strong>IMPORTANT:</strong> This script makes direct changes to files and database. Use with caution.</p>";

// Define paths
$basePath = __DIR__;
$publicPath = $basePath . '/public';

// Database connection parameters - update these with your actual credentials
$servername = "localhost";
$username = "if0_38972693"; // Your InfinityFree username
$password = ""; // You need to fill this with your actual database password
$dbname = "if0_38972693_ams"; // Your database name

// Target user ID - admin user
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 110001;

// Create a simple profile photo directly in the public directory
$photoContent = <<<'EOT'
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <rect width="200" height="200" fill="#e74c3c"/>
  <circle cx="100" cy="70" r="50" fill="#ffffff"/>
  <circle cx="100" cy="180" r="80" fill="#ffffff"/>
  <text x="100" y="100" font-family="Arial" font-size="24" text-anchor="middle" fill="#ffffff">ADMIN</text>
</svg>
EOT;

// Create the public directory if it doesn't exist
if (!file_exists($publicPath)) {
    if (mkdir($publicPath, 0777, true)) {
        echo "<p style='color:green;'>✅ Created public directory</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create public directory</p>";
        exit;
    }
}

// Create a new profile photo directly in the public directory
$filename = 'admin-' . time() . '.svg';
$filePath = $publicPath . '/' . $filename;

if (file_put_contents($filePath, $photoContent)) {
    echo "<p style='color:green;'>✅ Created profile photo at: $filePath</p>";
    chmod($filePath, 0644);
    
    // Update database to use this file directly
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $stmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :user_id");
        $stmt->bindParam(':path', $filename); // Just the filename, not a path
        $stmt->bindParam(':user_id', $userId);
        
        if ($stmt->execute()) {
            echo "<p style='color:green;'>✅ Database updated successfully</p>";
            echo "<p>Updated profile_photo_path to: $filename</p>";
            
            // Clear all cache files
            $cacheFiles = glob($basePath . '/bootstrap/cache/*.php');
            foreach ($cacheFiles as $file) {
                if (@unlink($file)) {
                    echo "<p style='color:green;'>✅ Cleared cache file: " . basename($file) . "</p>";
                }
            }
            
            // Show preview
            echo "<h3>New Profile Photo</h3>";
            echo "<p>URL: /$filename</p>";
            echo "<img src='/$filename' style='max-width: 200px; border: 1px solid #ddd;'>";
            echo "<p>If the image doesn't appear, try <a href='/$filename' target='_blank'>this direct link</a>.</p>";
            
            // Fix User model to handle this case
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
        // Direct override for emergency fix
        $photoPath = $this->profile_photo_path;
        
        // Handle empty, null, or invalid profile photo paths
        if (empty($photoPath) || $photoPath === \'0\' || $photoPath === 0 || $photoPath === \'null\' || $photoPath === \'NULL\') {
            return asset(\'kofa.png\');
        }
        
        // Check if it\'s the default photo
        if ($photoPath === \'kofa.png\') {
            return asset(\'kofa.png\');
        }
        
        // Check if the file exists directly in public directory first
        if (file_exists(public_path($photoPath))) {
            return asset($photoPath);
        }
        
        // Then check if it exists in storage/public
        if (file_exists(public_path(\'storage/\' . $photoPath))) {
            return asset(\'storage/\' . $photoPath);
        }
        
        // Default fallback
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
                        echo "<p style='color:red;'>❌ Could not find getProfilePhotoUrlAttribute method in User model</p>";
                    }
                } else {
                    echo "<p style='color:red;'>❌ Failed to create backup of User model</p>";
                }
            } else {
                echo "<p style='color:red;'>❌ User model not found at $userModelPath</p>";
            }
            
            // Fix the view
            $viewPath = $basePath . '/resources/views/admin/users/edit.blade.php';
            if (file_exists($viewPath)) {
                // Create a backup
                $backupPath = $viewPath . '.backup.' . time();
                if (copy($viewPath, $backupPath)) {
                    echo "<p style='color:green;'>✅ Created backup of view at $backupPath</p>";
                    
                    $viewContent = file_get_contents($viewPath);
                    
                    // Replace the image tag
                    $pattern = '/<img src="\{\{ \$user->profile_photo_url.*?\}\}" alt="\{\{ \$user->name \}\}\'s profile photo" class="h-full w-full object-cover">/';
                    $replacement = '<img src="{{ asset($user->profile_photo_path) }}?v={{ time() }}" alt="{{ $user->name }}\'s profile photo" class="h-full w-full object-cover">';
                    
                    $updatedContent = preg_replace($pattern, $replacement, $viewContent);
                    if ($updatedContent !== $viewContent) {
                        if (file_put_contents($viewPath, $updatedContent)) {
                            echo "<p style='color:green;'>✅ Updated view to use direct asset path</p>";
                        } else {
                            echo "<p style='color:red;'>❌ Failed to update view</p>";
                        }
                    } else {
                        echo "<p style='color:orange;'>⚠️ No changes made to view (pattern not found)</p>";
                    }
                } else {
                    echo "<p style='color:red;'>❌ Failed to create backup of view</p>";
                }
            } else {
                echo "<p style='color:red;'>❌ View not found at $viewPath</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Failed to update database</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
        echo "<p>Please update the database credentials in this file.</p>";
    }
} else {
    echo "<p style='color:red;'>❌ Failed to create profile photo</p>";
    $error = error_get_last();
    echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Update the database password in this script</li>";
echo "<li>Run the script to create a new profile photo directly in the public directory</li>";
echo "<li>Clear your browser cache</li>";
echo "<li>Try accessing the admin page with cache busting: <a href='/admin/users/110001/edit?nocache=" . time() . "' target='_blank'>Open admin edit page</a></li>";
echo "</ol>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 