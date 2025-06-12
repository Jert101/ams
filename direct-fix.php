<?php
// Super direct fix for profile photo display

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Direct Profile Photo Fix</h1>";

// Check if the public profile photos directory exists
$profilePhotoDir = __DIR__ . '/public/profile-photos';
if (!file_exists($profilePhotoDir)) {
    mkdir($profilePhotoDir, 0777, true);
    chmod($profilePhotoDir, 0777);
    echo "<p>Created profile photos directory</p>";
} else {
    echo "<p>Profile photos directory exists</p>";
}

// Create a test photo directly and check if it can be seen
$testFilePath = $profilePhotoDir . '/test-photo.jpg';
$image = imagecreatetruecolor(200, 200);
$bgColor = imagecolorallocate($image, 255, 0, 0); // Red
$textColor = imagecolorallocate($image, 255, 255, 255); // White
imagefill($image, 0, 0, $bgColor);
imagestring($image, 5, 50, 90, "TEST PHOTO", $textColor);
imagejpeg($image, $testFilePath, 90);
chmod($testFilePath, 0644);
imagedestroy($image);

echo "<p>Created test photo at: $testFilePath</p>";
echo "<p>You should see a red test image below:</p>";
echo "<p><img src='/profile-photos/test-photo.jpg?v=" . time() . "' style='width: 200px; height: 200px; border: 1px solid #ccc;'></p>";

// Show where InfinityFree might be looking for the file
echo "<h2>Photo Path Information</h2>";
echo "<p>Absolute path: " . realpath($testFilePath) . "</p>";
echo "<p>Test if accessible: <a href='/profile-photos/test-photo.jpg' target='_blank'>/profile-photos/test-photo.jpg</a></p>";

// Check if the User model is using the right approach
$userModelPath = __DIR__ . '/app/Models/User.php';
if (file_exists($userModelPath)) {
    echo "<h2>User Model Check</h2>";
    
    $modelContent = file_get_contents($userModelPath);
    
    // Check how it's handling profile photos
    $usingAsset = strpos($modelContent, 'asset(') !== false;
    $usingUrl = strpos($modelContent, 'url(') !== false;
    $checkingPublicPath = strpos($modelContent, 'public_path(') !== false;
    $checkingStoragePath = strpos($modelContent, 'storage_path(') !== false;
    
    echo "<ul>";
    echo "<li>Using asset(): " . ($usingAsset ? "Yes" : "No") . "</li>";
    echo "<li>Using url(): " . ($usingUrl ? "Yes" : "No") . "</li>";
    echo "<li>Checking public_path(): " . ($checkingPublicPath ? "Yes" : "No") . "</li>";
    echo "<li>Checking storage_path(): " . ($checkingStoragePath ? "Yes" : "No") . "</li>";
    echo "</ul>";
    
    // Modify the model to use direct URL approach
    if (isset($_POST['fix_model'])) {
        // Backup the model
        $backupPath = $userModelPath . '.backup.' . time();
        copy($userModelPath, $backupPath);
        
        // Replace the getProfilePhotoUrlAttribute method
        $pattern = '/public\s+function\s+getProfilePhotoUrlAttribute\s*\(\)\s*{.*?}/s';
        $replacement = 'public function getProfilePhotoUrlAttribute()
    {
        // If the path is empty or 0, use the default
        if (empty($this->profile_photo_path) || $this->profile_photo_path === "0" || $this->profile_photo_path === 0) {
            return $this->defaultProfilePhotoUrl();
        }
        
        // Return a direct URL to the file - no checking if it exists
        return "/".$this->profile_photo_path."?v=".time();
    }';
        
        $updatedContent = preg_replace($pattern, $replacement, $modelContent);
        
        // Also update defaultProfilePhotoUrl method
        $defaultPattern = '/protected\s+function\s+defaultProfilePhotoUrl\s*\(\)\s*{.*?}/s';
        $defaultReplacement = 'protected function defaultProfilePhotoUrl()
    {
        // Just return kofa.png directly
        return "/img/kofa.png?v=".time();
    }';
        
        $updatedContent = preg_replace($defaultPattern, $defaultReplacement, $updatedContent);
        
        // Write updated content
        if (file_put_contents($userModelPath, $updatedContent)) {
            echo "<p style='color:green;'>✅ Updated User model to use direct file URLs</p>";
            
            // Clear Laravel caches
            echo "<p>Clearing Laravel caches...</p>";
            @exec('php artisan cache:clear');
            @exec('php artisan view:clear');
            @exec('php artisan config:clear');
            
            echo "<p style='color:green;'>✅ Cleared Laravel caches</p>";
        } else {
            echo "<p style='color:red;'>Failed to update User model</p>";
        }
    }
    
    echo "<form method='post'>";
    echo "<button type='submit' name='fix_model' style='background-color: #4CAF50; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer;'>Simplify User Model for InfinityFree</button>";
    echo "</form>";
}

// Check the value in the database and update it for a specific user
echo "<h2>Check Database Values</h2>";

// Get database credentials from .env file
$envFile = __DIR__ . '/.env';
$dbHost = 'localhost';
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
    
    $dbHost = isset($hostMatches[1]) ? trim($hostMatches[1]) : 'localhost';
    $dbName = isset($dbMatches[1]) ? trim($dbMatches[1]) : '';
    $dbUsername = isset($userMatches[1]) ? trim($userMatches[1]) : '';
    $dbPassword = isset($passMatches[1]) ? trim($passMatches[1]) : '';
    
    echo "<p>Found database credentials in .env file</p>";
}

// Check user-provided credentials
if (isset($_POST['db_check'])) {
    $userId = $_POST['user_id'];
    
    try {
        // Connect to database
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check current value
        $stmt = $conn->prepare("SELECT user_id, name, profile_photo_path FROM users WHERE id = :id OR user_id = :user_id");
        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p>User: " . htmlspecialchars($user['name']) . "</p>";
            echo "<p>Current profile_photo_path: " . var_export($user['profile_photo_path'], true) . "</p>";
            
            // Create a new profile photo
            $name = $user['name'];
            $initials = '';
            $words = explode(' ', $name);
            foreach ($words as $word) {
                if (!empty($word)) {
                    $initials .= strtoupper(substr($word, 0, 1));
                }
            }
            $initials = substr($initials, 0, 2);
            
            // Create JPG image
            $image = imagecreatetruecolor(200, 200);
            $bgColor = imagecolorallocate($image, 0, 128, 255); // Blue
            $textColor = imagecolorallocate($image, 255, 255, 255); // White
            imagefill($image, 0, 0, $bgColor);
            
            // Center text
            $font = 5;
            $textWidth = imagefontwidth($font) * strlen($initials);
            $textHeight = imagefontheight($font);
            $textX = (200 - $textWidth) / 2;
            $textY = (200 - $textHeight) / 2;
            
            // Draw text
            imagestring($image, $font, (int)$textX, (int)$textY, $initials, $textColor);
            
            // Save as JPG file
            $userId = $user['user_id'];
            $filename = 'user-' . $userId . '.jpg';
            $filepath = $profilePhotoDir . '/' . $filename;
            imagejpeg($image, $filepath, 90);
            chmod($filepath, 0644);
            imagedestroy($image);
            
            echo "<p>Created new profile photo at: $filepath</p>";
            
            // Update database
            $photoPath = 'profile-photos/' . $filename;
            $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id OR user_id = :user_id");
            $updateStmt->bindParam(':path', $photoPath);
            $updateStmt->bindParam(':id', $userId);
            $updateStmt->bindParam(':user_id', $userId);
            
            if ($updateStmt->execute()) {
                echo "<p style='color:green;'>✅ Updated user record with new profile photo path</p>";
                echo "<p>New profile_photo_path: " . $photoPath . "</p>";
                echo "<p><img src='/" . $photoPath . "?v=" . time() . "' style='width: 100px; height: 100px; border-radius: 50%;'></p>";
                
                echo "<p><a href='/admin/users/{$userId}/edit?nocache=" . time() . "' target='_blank' style='display: inline-block; padding: 8px 12px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>View User Profile</a></p>";
            } else {
                echo "<p style='color:red;'>Failed to update user record</p>";
            }
        } else {
            echo "<p style='color:red;'>User not found with ID: $userId</p>";
            
            // Show available users
            $stmt = $conn->query("SELECT id, user_id, name FROM users");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($users) > 0) {
                echo "<p>Available users:</p>";
                echo "<ul>";
                foreach ($users as $u) {
                    echo "<li>" . $u['id'] . " (user_id: " . $u['user_id'] . "): " . htmlspecialchars($u['name']) . "</li>";
                }
                echo "</ul>";
            }
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
    }
}

// Form to check a specific user
echo "<form method='post'>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label for='user_id' style='display: block; margin-bottom: 5px;'>User ID:</label>";
echo "<input type='text' id='user_id' name='user_id' value='1' style='padding: 8px; width: 200px;'>";
echo "</div>";
echo "<button type='submit' name='db_check' style='background-color: #2196F3; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer;'>Check & Fix User</button>";
echo "</form>";

echo "<h2>Need to try a different solution?</h2>";
echo "<p>Try these links:</p>";
echo "<ul>";
echo "<li><a href='fix-photo-display.php'>Comprehensive Photo Fixer</a></li>";
echo "<li><a href='create-test-profile.php'>Create Test Profile Photo</a></li>";
echo "</ul>";
?> 