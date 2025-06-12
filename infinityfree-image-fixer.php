<?php
// InfinityFree Image Fixer - Fixes missing profile photos
// This script creates necessary directories and fixes profile photo paths

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>InfinityFree Profile Photo Fixer</h1>";
echo "<p>This tool fixes missing profile photos by creating the correct directories and updating paths.</p>";

// STEP 1: Get database credentials
$envFile = __DIR__ . '/.env';
$dbHost = '';
$dbName = '';
$dbUsername = '';
$dbPassword = '';

if (file_exists($envFile)) {
    $envContents = file_get_contents($envFile);
    
    // Extract database credentials
    preg_match('/DB_HOST=([^\n]+)/', $envContents, $hostMatches);
    preg_match('/DB_DATABASE=([^\n]+)/', $envContents, $dbMatches);
    preg_match('/DB_USERNAME=([^\n]+)/', $envContents, $userMatches);
    preg_match('/DB_PASSWORD=([^\n]+)/', $envContents, $passMatches);
    
    $dbHost = isset($hostMatches[1]) ? trim($hostMatches[1]) : '';
    $dbName = isset($dbMatches[1]) ? trim($dbMatches[1]) : '';
    $dbUsername = isset($userMatches[1]) ? trim($userMatches[1]) : '';
    $dbPassword = isset($passMatches[1]) ? trim($passMatches[1]) : '';
    
    echo "<p>Found database credentials in .env file</p>";
} else {
    echo "<p style='color:red;'>Warning: .env file not found. You will need to enter database credentials manually.</p>";
}

// STEP 2: Create form for manual credentials input
echo "<h2>Database Connection</h2>";
echo "<form method='post' action=''>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label style='display: block;'>Database Host:</label>";
echo "<input type='text' name='db_host' value='" . htmlspecialchars($dbHost) . "' style='width: 300px; padding: 5px;'>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label style='display: block;'>Database Name:</label>";
echo "<input type='text' name='db_name' value='" . htmlspecialchars($dbName) . "' style='width: 300px; padding: 5px;'>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label style='display: block;'>Database Username:</label>";
echo "<input type='text' name='db_username' value='" . htmlspecialchars($dbUsername) . "' style='width: 300px; padding: 5px;'>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label style='display: block;'>Database Password:</label>";
echo "<input type='password' name='db_password' value='" . htmlspecialchars($dbPassword) . "' style='width: 300px; padding: 5px;'>";
echo "</div>";
echo "<div style='margin-bottom: 20px;'>";
echo "<button type='submit' name='fix_photos' style='padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Fix Profile Photos</button>";
echo "</div>";
echo "</form>";

// Process database connection and fix photos
if (isset($_POST['fix_photos'])) {
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    
    try {
        // Connect to the database
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p style='color:green;'>✅ Successfully connected to database</p>";
        
        // STEP 3: Create necessary directories
        $profileDir = __DIR__ . '/public/profile-photos';
        if (!file_exists($profileDir)) {
            if (mkdir($profileDir, 0755, true)) {
                echo "<p style='color:green;'>✅ Created directory: public/profile-photos</p>";
            } else {
                echo "<p style='color:red;'>❌ Failed to create directory: public/profile-photos</p>";
            }
        } else {
            echo "<p>Directory already exists: public/profile-photos</p>";
        }
        
        // STEP 4: Create placeholder images for each user
        echo "<h2>Creating Profile Photos</h2>";
        
        // Get all users with custom profile photos
        $stmt = $conn->query("SELECT id, name, email, profile_photo_path FROM users WHERE profile_photo_path IS NOT NULL AND profile_photo_path != 'img/kofa.png' AND profile_photo_path != '0' AND profile_photo_path != ''");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Found " . count($users) . " users with custom profile photos</p>";
        
        $updateCount = 0;
        
        foreach ($users as $user) {
            // Get initials for the user
            $nameParts = explode(' ', $user['name']);
            $initials = '';
            foreach ($nameParts as $part) {
                if (!empty($part)) {
                    $initials .= strtoupper(substr($part, 0, 1));
                }
            }
            if (strlen($initials) < 2 && strpos($user['email'], '@') !== false) {
                // Add first letter of email if name doesn't provide enough initials
                $initials .= strtoupper(substr($user['email'], 0, 1));
            }
            $initials = substr($initials, 0, 2); // Limit to 2 characters
            
            // Create or update profile photo
            $oldPath = $user['profile_photo_path'];
            $filename = basename($oldPath);
            
            // Check if file has valid extension, if not add .jpg
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $hasValidExtension = false;
            foreach ($validExtensions as $ext) {
                if (preg_match('/\.' . $ext . '$/i', $filename)) {
                    $hasValidExtension = true;
                    break;
                }
            }
            if (!$hasValidExtension) {
                $filename .= '.jpg';
            }
            
            $newPath = 'public/profile-photos/' . $filename;
            $fullPath = __DIR__ . '/' . $newPath;
            
            // Create image with user's initials
            $width = 200;
            $height = 200;
            $img = imagecreatetruecolor($width, $height);
            
            // Random color based on user's name
            $hash = md5($user['name']);
            $r = hexdec(substr($hash, 0, 2));
            $g = hexdec(substr($hash, 2, 2));
            $b = hexdec(substr($hash, 4, 2));
            
            // Make sure color is not too dark
            $r = max($r, 100);
            $g = max($g, 100);
            $b = max($b, 100);
            
            $bgColor = imagecolorallocate($img, $r, $g, $b);
            $textColor = imagecolorallocate($img, 255, 255, 255);
            
            // Fill background
            imagefill($img, 0, 0, $bgColor);
            
            // Add text (initials)
            $fontSize = 5; // Largest built-in font
            $textWidth = imagefontwidth($fontSize) * strlen($initials);
            $textHeight = imagefontheight($fontSize);
            $x = ($width - $textWidth) / 2;
            $y = ($height - $textHeight) / 2;
            
            imagestring($img, $fontSize, $x, $y, $initials, $textColor);
            
            // Save image
            $success = imagejpeg($img, $fullPath, 90);
            imagedestroy($img);
            
            if ($success) {
                echo "<p style='color:green;'>✅ Created profile photo for " . htmlspecialchars($user['name']) . " at: " . htmlspecialchars($newPath) . "</p>";
                
                // Update database path
                $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :newPath WHERE id = :id");
                $updateStmt->bindParam(':newPath', $newPath);
                $updateStmt->bindParam(':id', $user['id']);
                
                if ($updateStmt->execute()) {
                    $updateCount++;
                    echo "<p style='color:green;'>✓ Updated database path for: " . htmlspecialchars($user['name']) . "</p>";
                } else {
                    echo "<p style='color:red;'>✗ Failed to update database for: " . htmlspecialchars($user['name']) . "</p>";
                }
            } else {
                echo "<p style='color:red;'>✗ Failed to create profile photo for: " . htmlspecialchars($user['name']) . "</p>";
            }
        }
        
        echo "<h2>Results</h2>";
        echo "<p style='color:green;'>Updated " . $updateCount . " of " . count($users) . " users with new profile photos</p>";
        
        // STEP 5: Update User model to handle the new paths
        $userModelPath = __DIR__ . '/app/Models/User.php';
        if (file_exists($userModelPath)) {
            echo "<h2>Updating User Model</h2>";
            
            // Backup the current model
            $backupPath = $userModelPath . '.backup.' . time();
            if (copy($userModelPath, $backupPath)) {
                echo "<p>Created backup of User model at: " . $backupPath . "</p>";
                
                // Get the current model content
                $modelContent = file_get_contents($userModelPath);
                
                // Create a simplified version of the getProfilePhotoUrlAttribute method
                $pattern = '/public\s+function\s+getProfilePhotoUrlAttribute\s*\(\)\s*{.*?}/s';
                $replacement = 'public function getProfilePhotoUrlAttribute()
    {
        // Direct approach for InfinityFree hosting
        if (empty($this->profile_photo_path) || $this->profile_photo_path === "0" || $this->profile_photo_path === 0) {
            return "/img/kofa.png?v=" . time();
        }
        
        // Just return the path directly with a slash at the beginning
        return "/" . ltrim($this->profile_photo_path, "/") . "?v=" . time();
    }';
                
                // Replace the method in the file content
                $updatedContent = preg_replace($pattern, $replacement, $modelContent);
                
                // Update the defaultProfilePhotoUrl method if it exists
                $defaultPattern = '/protected\s+function\s+defaultProfilePhotoUrl\s*\(\)\s*{.*?}/s';
                if (preg_match($defaultPattern, $updatedContent)) {
                    $defaultReplacement = 'protected function defaultProfilePhotoUrl()
    {
        // Just return kofa.png directly for InfinityFree
        return "/img/kofa.png?v=" . time();
    }';
                    
                    $updatedContent = preg_replace($defaultPattern, $defaultReplacement, $updatedContent);
                }
                
                // Write the updated content back to the file
                if (file_put_contents($userModelPath, $updatedContent)) {
                    echo "<p style='color:green;'>✅ Successfully updated User model</p>";
                } else {
                    echo "<p style='color:red;'>Failed to write updated User model</p>";
                }
            } else {
                echo "<p style='color:red;'>Failed to create backup of User model</p>";
            }
        } else {
            echo "<p style='color:red;'>User model file not found at: " . htmlspecialchars($userModelPath) . "</p>";
        }
        
        // STEP 6: Clear Laravel caches
        echo "<h2>Clearing Caches</h2>";
        $cacheDirs = [
            __DIR__ . '/storage/framework/views',
            __DIR__ . '/storage/framework/cache',
            __DIR__ . '/bootstrap/cache'
        ];
        
        $filesCleared = 0;
        
        foreach ($cacheDirs as $dir) {
            if (file_exists($dir)) {
                $files = glob($dir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        if (@unlink($file)) {
                            $filesCleared++;
                        }
                    }
                }
            }
        }
        
        echo "<p style='color:green;'>✅ Cleared " . $filesCleared . " cache files</p>";
        
    } catch(PDOException $e) {
        echo "<p style='color:red;'>Database connection failed: " . $e->getMessage() . "</p>";
    }
}

// Final notes
echo "<h2>Next Steps</h2>";
echo "<p>After fixing your profile photos:</p>";
echo "<ol>";
echo "<li>Clear your browser cache or open a private/incognito window</li>";
echo "<li>Visit your user profile page to see if the profile photos now display correctly</li>";
echo "</ol>";

// Test link
echo "<p><a href='/admin/users' style='display: inline-block; padding: 10px; background-color: #4CAF50; color: white; text-decoration: none;'>Go to Users Page</a></p>";
?>
