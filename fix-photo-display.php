<?php
// This script checks what values are stored in the profile_photo_path column and adapts accordingly

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix Profile Photo Display</h1>";

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

try {
    // Connect to database
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get all users and analyze what's in the profile_photo_path column
    $stmt = $conn->query("SELECT id, user_id, name, email, profile_photo_path FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Analysis of User Profile Photos</h2>";
    echo "<p>Found " . count($users) . " users in the database.</p>";
    
    // Analyze patterns in profile_photo_path
    $patterns = [
        'empty' => 0,
        'zero_string' => 0,
        'zero_int' => 0,
        'null' => 0,
        'jpg' => 0,
        'jpeg' => 0,
        'png' => 0,
        'svg' => 0,
        'other' => 0
    ];
    
    $sampleValues = [];
    
    foreach ($users as $user) {
        $path = $user['profile_photo_path'];
        
        // Categorize the path
        if (empty($path) && $path !== '0' && $path !== 0) {
            $patterns['empty']++;
        } elseif ($path === '0') {
            $patterns['zero_string']++;
        } elseif ($path === 0) {
            $patterns['zero_int']++;
        } elseif ($path === null) {
            $patterns['null']++;
        } elseif (strpos($path, '.jpg') !== false) {
            $patterns['jpg']++;
            if (!in_array($path, $sampleValues)) $sampleValues[] = $path;
        } elseif (strpos($path, '.jpeg') !== false) {
            $patterns['jpeg']++;
            if (!in_array($path, $sampleValues)) $sampleValues[] = $path;
        } elseif (strpos($path, '.png') !== false) {
            $patterns['png']++;
            if (!in_array($path, $sampleValues)) $sampleValues[] = $path;
        } elseif (strpos($path, '.svg') !== false) {
            $patterns['svg']++;
            if (!in_array($path, $sampleValues)) $sampleValues[] = $path;
        } else {
            $patterns['other']++;
            if (!in_array($path, $sampleValues)) $sampleValues[] = $path;
        }
    }
    
    // Display patterns
    echo "<h3>Path Patterns:</h3>";
    echo "<ul>";
    foreach ($patterns as $type => $count) {
        if ($count > 0) {
            echo "<li>$type: $count users</li>";
        }
    }
    echo "</ul>";
    
    // Display sample values
    if (count($sampleValues) > 0) {
        echo "<h3>Sample Path Values:</h3>";
        echo "<ul>";
        foreach ($sampleValues as $value) {
            echo "<li>" . htmlspecialchars($value) . "</li>";
        }
        echo "</ul>";
    }
    
    // Display users table with photo paths
    echo "<h3>User Details:</h3>";
    echo "<table style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #f2f2f2;'>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>ID</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>User ID</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Name</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Email</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Profile Photo Path</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Type</th>";
    echo "<th style='border: 1px solid #ddd; padding: 8px;'>Action</th>";
    echo "</tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($user['id'] ?? 'N/A') . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($user['user_id'] ?? 'N/A') . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($user['name']) . "</td>";
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($user['email']) . "</td>";
        
        // Profile photo path with special formatting for empty values
        $path = $user['profile_photo_path'];
        if (empty($path) && $path !== '0' && $path !== 0) {
            echo "<td style='border: 1px solid #ddd; padding: 8px; color: #999;'><em>empty</em></td>";
        } elseif ($path === '0') {
            echo "<td style='border: 1px solid #ddd; padding: 8px; color: #999;'><em>'0' (string)</em></td>";
        } elseif ($path === 0) {
            echo "<td style='border: 1px solid #ddd; padding: 8px; color: #999;'><em>0 (integer)</em></td>";
        } elseif ($path === null) {
            echo "<td style='border: 1px solid #ddd; padding: 8px; color: #999;'><em>null</em></td>";
        } else {
            echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($path) . "</td>";
        }
        
        // Type of path
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . gettype($path) . "</td>";
        
        // Action button to fix
        echo "<td style='border: 1px solid #ddd; padding: 8px;'>";
        echo "<form method='post' style='display:inline;'>";
        echo "<input type='hidden' name='user_id' value='" . ($user['id'] ?? $user['user_id']) . "'>";
        echo "<button type='submit' name='fix_user' style='background-color: #4CAF50; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;'>Fix Path</button>";
        echo "</form>";
        echo "</td>";
        
        echo "</tr>";
    }
    echo "</table>";
    
    // Process fixing individual user
    if (isset($_POST['fix_user']) && isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];
        
        // Get user details
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id OR user_id = :user_id");
        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<h2>Fixing User: " . htmlspecialchars($user['name']) . "</h2>";
            
            // Create directories if needed
            $profilePhotoDir = __DIR__ . '/public/profile-photos';
            if (!file_exists($profilePhotoDir)) {
                mkdir($profilePhotoDir, 0777, true);
                chmod($profilePhotoDir, 0777);
            }
            
            // Generate a profile photo for this user
            $name = $user['name'];
            $userId = $user['id'] ?? $user['user_id'];
            
            // Generate initials
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
            
            // Generate background color based on user ID
            $bgColor = substr(md5($userId), 0, 6);
            $r = hexdec(substr($bgColor, 0, 2));
            $g = hexdec(substr($bgColor, 2, 2));
            $b = hexdec(substr($bgColor, 4, 2));
            
            // Fill background
            $backgroundColor = imagecolorallocate($image, $r, $g, $b);
            imagefill($image, 0, 0, $backgroundColor);
            
            // Add text
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $font = 5; // Built-in font
            
            // Calculate text position to center it
            $textWidth = imagefontwidth($font) * strlen($initials);
            $textHeight = imagefontheight($font);
            $textX = (200 - $textWidth) / 2;
            $textY = (200 - $textHeight) / 2;
            
            // Draw text
            imagestring($image, $font, (int)$textX, (int)$textY, $initials, $textColor);
            
            // Save to file
            $filename = 'user-' . $userId . '.jpg';
            $filepath = $profilePhotoDir . '/' . $filename;
            imagejpeg($image, $filepath, 90);
            chmod($filepath, 0644);
            imagedestroy($image);
            
            // Update user record
            $photoPath = 'profile-photos/' . $filename;
            $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id OR user_id = :user_id");
            $updateStmt->bindParam(':path', $photoPath);
            $updateStmt->bindParam(':id', $userId);
            $updateStmt->bindParam(':user_id', $userId);
            
            if ($updateStmt->execute()) {
                echo "<p style='color:green;'>✅ Updated profile photo path for user " . htmlspecialchars($user['name']) . "</p>";
                echo "<p>New path: " . $photoPath . "</p>";
                echo "<p><img src='/" . $photoPath . "?v=" . time() . "' style='width: 100px; height: 100px; border-radius: 50%;'></p>";
                
                // Also create copy in storage for redundancy
                $storageDir = __DIR__ . '/storage/app/public/profile-photos';
                if (!file_exists($storageDir)) {
                    mkdir($storageDir, 0777, true);
                }
                copy($filepath, $storageDir . '/' . $filename);
                
                echo "<p><a href='/admin/users/{$userId}/edit?nocache=" . time() . "' target='_blank' style='display: inline-block; padding: 8px 12px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>View User Profile</a></p>";
                echo "<p style='color:orange;'>Remember to clear cache and your browser cache!</p>";
            } else {
                echo "<p style='color:red;'>Failed to update user record</p>";
            }
        } else {
            echo "<p style='color:red;'>User not found</p>";
        }
    }
    
    // Fix User model button
    echo "<h2>Fix User Model</h2>";
    echo "<p>This will update the User model to correctly handle the profile photo paths found in your database.</p>";
    echo "<form method='post'>";
    echo "<button type='submit' name='fix_model' style='background-color: #2196F3; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer;'>Update User Model</button>";
    echo "</form>";
    
    // Fix all users button
    echo "<h2>Fix All Users</h2>";
    echo "<p>This will generate profile photos for all users and update their database records.</p>";
    echo "<form method='post'>";
    echo "<button type='submit' name='fix_all_users' style='background-color: #FF9800; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer;'>Fix All Users</button>";
    echo "</form>";
    
    // Process fixing the model
    if (isset($_POST['fix_model'])) {
        // Backup the model first
        $userModelPath = __DIR__ . '/app/Models/User.php';
        $backupPath = $userModelPath . '.backup.' . time();
        
        if (file_exists($userModelPath)) {
            if (copy($userModelPath, $backupPath)) {
                echo "<p>Created backup of User model at: $backupPath</p>";
                
                // Update the model with new logic based on what we found in the database
                $modelContents = file_get_contents($userModelPath);
                
                // Find and replace the getProfilePhotoUrlAttribute method
                $pattern = '/public\s+function\s+getProfilePhotoUrlAttribute\s*\(\).*?{.*?}/s';
                $replacement = 'public function getProfilePhotoUrlAttribute()
    {
        // Log debug info
        \Log::debug(\'Getting profile photo URL\', [
            \'user_id\' => $this->user_id,
            \'path\' => $this->profile_photo_path,
            \'type\' => gettype($this->profile_photo_path)
        ]);
        
        // The profile_photo_path column may contain:
        // 1. Empty string or null
        // 2. The string "0" or integer 0
        // 3. An actual file path
        
        // First, check if the path is valid and not empty/zero
        if (empty($this->profile_photo_path) || $this->profile_photo_path === \'0\' || $this->profile_photo_path === 0) {
            // Generate a fallback image for this user
            return $this->generateFallbackImage();
        }
        
        // Normalize path - remove any initial slash
        $path = ltrim($this->profile_photo_path, \'/\');
        
        // Check if the file exists in the public directory
        if (file_exists(public_path($path))) {
            return asset($path) . \'?v=\' . time();
        }
        
        // Check if it\'s in storage
        if (file_exists(storage_path(\'app/public/\' . $path))) {
            return asset(\'storage/\' . $path) . \'?v=\' . time();
        }
        
        // Check if it\'s an absolute path
        if (file_exists($this->profile_photo_path)) {
            return url($this->profile_photo_path) . \'?v=\' . time();
        }
        
        // Check if it\'s a full URL
        if (filter_var($this->profile_photo_path, FILTER_VALIDATE_URL)) {
            return $this->profile_photo_path;
        }
        
        // Check for specifically saved images by user ID
        $userId = $this->id ?? $this->user_id;
        $commonPaths = [
            "profile-photos/user-{$userId}.jpg",
            "profile-photos/user-{$userId}.png",
            "profile-photos/user-{$userId}.jpeg",
            "profile-photos/{$userId}.jpg",
            "profile-photos/{$userId}.png",
            "images/profiles/user-{$userId}.jpg",
            "images/profiles/user-{$userId}.png"
        ];
        
        foreach ($commonPaths as $testPath) {
            if (file_exists(public_path($testPath))) {
                return asset($testPath) . \'?v=\' . time();
            }
            
            if (file_exists(storage_path(\'app/public/\' . $testPath))) {
                return asset(\'storage/\' . $testPath) . \'?v=\' . time();
            }
        }
        
        // If we get here, we couldn\'t find the image - generate a fallback
        return $this->generateFallbackImage();
    }';
                
                // Add generateFallbackImage method if it doesn't exist
                if (strpos($modelContents, 'function generateFallbackImage') === false) {
                    // Find the end of the class
                    $endOfClass = strrpos($modelContents, '}');
                    
                    // Insert the method before the end of the class
                    if ($endOfClass !== false) {
                        $fallbackMethod = '
    /**
     * Generate a fallback image for a user
     *
     * @return string
     */
    protected function generateFallbackImage()
    {
        // Try to find an existing user image first
        $userId = $this->id ?? $this->user_id;
        $commonPaths = [
            "profile-photos/user-{$userId}.jpg",
            "profile-photos/user-{$userId}.png",
            "profile-photos/user-{$userId}.jpeg",
            "profile-photos/{$userId}.jpg",
            "profile-photos/{$userId}.png",
            "images/profiles/user-{$userId}.jpg",
            "images/profiles/user-{$userId}.png"
        ];
        
        foreach ($commonPaths as $testPath) {
            if (file_exists(public_path($testPath))) {
                return asset($testPath) . \'?v=\' . time();
            }
            
            if (file_exists(storage_path(\'app/public/\' . $testPath))) {
                return asset(\'storage/\' . $testPath) . \'?v=\' . time();
            }
        }
        
        // If we get here, we couldn\'t find an image - return the default logo
        if (file_exists(public_path(\'img/kofa.png\'))) {
            return asset(\'img/kofa.png\') . \'?v=\' . time();
        }
        
        // As absolute last resort, return a generated SVG
        return $this->generateSvgAvatar();
    }
    
    /**
     * Generate an SVG avatar with user\'s initials.
     *
     * @return string
     */
    protected function generateSvgAvatar()
    {
        // Generate initials from name
        $name = $this->name ?? \'User\';
        $initials = \'\';
        $words = explode(\' \', $name);
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        $initials = substr($initials, 0, 2); // Limit to 2 characters
        
        // Generate a consistent color based on the user ID
        $userId = $this->id ?? $this->user_id ?? 1;
        $bgColor = \'#\' . substr(md5($userId), 0, 6);
        
        // Create SVG
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <rect width="200" height="200" fill="$bgColor"/>
  <text x="100" y="115" font-family="Arial, sans-serif" font-size="80" font-weight="bold" text-anchor="middle" fill="#ffffff">$initials</text>
</svg>
SVG;
        
        // Convert to data URI
        $encoded = base64_encode($svg);
        return \'data:image/svg+xml;base64,\' . $encoded;
    }';
                        
                        $updatedContents = substr($modelContents, 0, $endOfClass) . $fallbackMethod . "\n}";
                    } else {
                        $updatedContents = $modelContents;
                    }
                } else {
                    $updatedContents = $modelContents;
                }
                
                // Replace the getProfilePhotoUrlAttribute method
                $updatedContents = preg_replace($pattern, $replacement, $updatedContents);
                
                // Write updated model back to file
                if (file_put_contents($userModelPath, $updatedContents)) {
                    echo "<p style='color:green;'>✅ Successfully updated User model with improved handling for profile photos</p>";
                    
                    // Clear Laravel caches
                    $cacheCommands = [
                        'php artisan cache:clear',
                        'php artisan view:clear',
                        'php artisan config:clear'
                    ];
                    
                    echo "<p>Clearing Laravel caches...</p>";
                    foreach ($cacheCommands as $command) {
                        @exec($command);
                    }
                    echo "<p style='color:green;'>✅ Cleared Laravel caches</p>";
                    
                    echo "<p>The User model has been updated to handle your profile photo paths better. Try viewing a user profile now:</p>";
                    echo "<p><a href='/admin/users/1/edit?nocache=" . time() . "' target='_blank' style='display: inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>View User Profile</a></p>";
                } else {
                    echo "<p style='color:red;'>Failed to write updated User model</p>";
                }
            } else {
                echo "<p style='color:red;'>Failed to create backup of User model</p>";
            }
        } else {
            echo "<p style='color:red;'>User model file not found at expected location: $userModelPath</p>";
        }
    }
    
    // Process fixing all users
    if (isset($_POST['fix_all_users'])) {
        echo "<h2>Fixing All Users</h2>";
        
        // Create directories if needed
        $profilePhotoDir = __DIR__ . '/public/profile-photos';
        if (!file_exists($profilePhotoDir)) {
            mkdir($profilePhotoDir, 0777, true);
            chmod($profilePhotoDir, 0777);
        }
        
        $storageDir = __DIR__ . '/storage/app/public/profile-photos';
        if (!file_exists($storageDir)) {
            mkdir($storageDir, 0777, true);
        }
        
        $fixedCount = 0;
        $errorCount = 0;
        
        foreach ($users as $user) {
            $userId = $user['id'] ?? $user['user_id'];
            $name = $user['name'];
            
            // Generate initials
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
            
            // Generate background color based on user ID
            $bgColor = substr(md5($userId), 0, 6);
            $r = hexdec(substr($bgColor, 0, 2));
            $g = hexdec(substr($bgColor, 2, 2));
            $b = hexdec(substr($bgColor, 4, 2));
            
            // Fill background
            $backgroundColor = imagecolorallocate($image, $r, $g, $b);
            imagefill($image, 0, 0, $backgroundColor);
            
            // Add text
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $font = 5; // Built-in font
            
            // Calculate text position to center it
            $textWidth = imagefontwidth($font) * strlen($initials);
            $textHeight = imagefontheight($font);
            $textX = (200 - $textWidth) / 2;
            $textY = (200 - $textHeight) / 2;
            
            // Draw text
            imagestring($image, $font, (int)$textX, (int)$textY, $initials, $textColor);
            
            // Save to file
            $filename = 'user-' . $userId . '.jpg';
            $filepath = $profilePhotoDir . '/' . $filename;
            
            if (imagejpeg($image, $filepath, 90)) {
                chmod($filepath, 0644);
                
                // Copy to storage
                copy($filepath, $storageDir . '/' . $filename);
                
                // Update user record
                $photoPath = 'profile-photos/' . $filename;
                $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id OR user_id = :user_id");
                $updateStmt->bindParam(':path', $photoPath);
                $updateStmt->bindParam(':id', $userId);
                $updateStmt->bindParam(':user_id', $userId);
                
                if ($updateStmt->execute()) {
                    $fixedCount++;
                } else {
                    $errorCount++;
                    echo "<p style='color:red;'>Error updating user: " . htmlspecialchars($name) . "</p>";
                }
            } else {
                $errorCount++;
                echo "<p style='color:red;'>Error creating image for user: " . htmlspecialchars($name) . "</p>";
            }
            
            // Clean up
            imagedestroy($image);
        }
        
        // Clear Laravel caches
        $cacheCommands = [
            'php artisan cache:clear',
            'php artisan view:clear',
            'php artisan config:clear'
        ];
        
        echo "<p>Clearing Laravel caches...</p>";
        foreach ($cacheCommands as $command) {
            @exec($command);
        }
        
        echo "<h3>Summary</h3>";
        echo "<p style='color:green;'>✅ Fixed $fixedCount users successfully</p>";
        if ($errorCount > 0) {
            echo "<p style='color:red;'>❌ Encountered errors with $errorCount users</p>";
        }
        
        echo "<p>Profile photos have been generated for all users and their database records have been updated.</p>";
        echo "<p><a href='/admin/users/1/edit?nocache=" . time() . "' target='_blank' style='display: inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>View User Profile</a></p>";
        echo "<p style='color:orange;'>Remember to clear your browser cache!</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
}
?> 