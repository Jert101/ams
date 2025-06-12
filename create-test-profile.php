<?php
// Create a test profile photo for a specific user (using standard image formats)

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Create Test Profile Photo</h1>";

// Configuration - CHANGE THESE AS NEEDED
$userId = 1; // User ID to create photo for
$dbHost = 'localhost';
$dbName = 'ams';
$dbUsername = 'root';
$dbPassword = '';

// Get database credentials from .env file if available
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContents = file_get_contents($envFile);
    
    // Extract database credentials
    preg_match('/DB_HOST=([^\n]+)/', $envContents, $hostMatches);
    preg_match('/DB_DATABASE=([^\n]+)/', $envContents, $dbMatches);
    preg_match('/DB_USERNAME=([^\n]+)/', $envContents, $userMatches);
    preg_match('/DB_PASSWORD=([^\n]+)/', $envContents, $passMatches);
    
    $dbHost = isset($hostMatches[1]) ? trim($hostMatches[1]) : $dbHost;
    $dbName = isset($dbMatches[1]) ? trim($dbMatches[1]) : $dbName;
    $dbUsername = isset($userMatches[1]) ? trim($userMatches[1]) : $dbUsername;
    $dbPassword = isset($passMatches[1]) ? trim($passMatches[1]) : $dbPassword;
    
    echo "<p>Found database credentials in .env file</p>";
}

// Connect to database
try {
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get user info
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        // If not found by id, try user_id
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    if ($user) {
        echo "<p>Found user: " . htmlspecialchars($user['name']) . "</p>";
        
        // Create profile-photos directory if it doesn't exist
        $profilePhotoDir = __DIR__ . '/public/profile-photos';
        if (!file_exists($profilePhotoDir)) {
            if (mkdir($profilePhotoDir, 0777, true)) {
                echo "<p>Created profile-photos directory</p>";
                chmod($profilePhotoDir, 0777);
            } else {
                echo "<p style='color:red;'>Failed to create profile-photos directory</p>";
                $error = error_get_last();
                echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
            }
        }
        
        // Get initials for text-based image
        $name = $user['name'];
        $initials = '';
        $words = explode(' ', $name);
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        $initials = substr($initials, 0, 2);
        
        // Create image with GD
        $imageWidth = 200;
        $imageHeight = 200;
        $image = imagecreatetruecolor($imageWidth, $imageHeight);
        
        // Generate background color based on user ID
        $bgColor = substr(md5($user['id'] ?? $user['user_id'] ?? 1), 0, 6);
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
        $textX = ($imageWidth - $textWidth) / 2;
        $textY = ($imageHeight - $textHeight) / 2;
        
        // Draw text
        imagestring($image, $font, $textX, $textY, $initials, $textColor);
        
        // Save as JPG file
        $userId = $user['id'] ?? $user['user_id'];
        $filename = "user-{$userId}.jpg";
        $filepath = $profilePhotoDir . '/' . $filename;
        imagejpeg($image, $filepath, 90); // 90 is quality
        chmod($filepath, 0644);
        
        // Clean up
        imagedestroy($image);
        
        echo "<p style='color:green;'>✅ Created JPEG profile photo at: $filepath</p>";
        
        // Update user's profile_photo_path in database
        $photoPath = 'profile-photos/' . $filename;
        $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id");
        $updateStmt->bindParam(':path', $photoPath);
        $userIdParam = $user['id'] ?? $user['user_id'];
        $updateStmt->bindParam(':id', $userIdParam);
        
        if ($updateStmt->execute()) {
            echo "<p style='color:green;'>✅ Updated user record with new profile photo path</p>";
            
            // Create a copy in storage directory for redundancy
            $storageDir = __DIR__ . '/storage/app/public/profile-photos';
            if (!file_exists($storageDir)) {
                mkdir($storageDir, 0777, true);
            }
            $storageFilepath = $storageDir . '/' . $filename;
            copy($filepath, $storageFilepath);
            chmod($storageFilepath, 0644);
            
            // Create a copy in public/images/profiles for maximum compatibility
            $altDir = __DIR__ . '/public/images/profiles';
            if (!file_exists($altDir)) {
                mkdir($altDir, 0777, true);
            }
            $altFilepath = $altDir . '/' . $filename;
            copy($filepath, $altFilepath);
            chmod($altFilepath, 0644);
            
            // Show the image
            echo "<div style='margin: 20px 0; padding: 10px; border: 1px solid #ccc;'>";
            echo "<p>Profile photo preview:</p>";
            echo "<img src='/" . $photoPath . "?v=" . time() . "' style='width: 100px; height: 100px; border-radius: 50%;'>";
            echo "</div>";
            
            echo "<p style='color:green; font-weight:bold;'>🎉 Done! Try viewing the user profile now:</p>";
            echo "<a href='/admin/users/{$userId}/edit?nocache=" . time() . "' target='_blank' style='display: inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>View User Profile</a>";
            
            // Provide command to clear cache
            echo "<div style='margin-top: 20px; padding: 10px; background-color: #f8f8f8; border-left: 4px solid #2196F3;'>";
            echo "<p><strong>Next Steps:</strong></p>";
            echo "<p>1. Make sure to clear Laravel cache:</p>";
            echo "<code>php artisan cache:clear</code><br>";
            echo "<code>php artisan view:clear</code><br>";
            echo "<p>2. Clear your browser cache or use incognito/private mode.</p>";
            echo "</div>";
        } else {
            echo "<p style='color:red;'>Failed to update user record</p>";
        }
    } else {
        echo "<p style='color:red;'>User not found with ID: $userId</p>";
        
        // List available users
        $stmt = $conn->query("SELECT id, user_id, name, email FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($users) > 0) {
            echo "<p>Available users:</p>";
            echo "<table style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background-color: #f2f2f2;'><th style='border: 1px solid #ddd; padding: 8px;'>ID</th><th style='border: 1px solid #ddd; padding: 8px;'>User ID</th><th style='border: 1px solid #ddd; padding: 8px;'>Name</th><th style='border: 1px solid #ddd; padding: 8px;'>Email</th><th style='border: 1px solid #ddd; padding: 8px;'>Action</th></tr>";
            
            foreach ($users as $u) {
                echo "<tr>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($u['id'] ?? 'N/A') . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($u['user_id'] ?? 'N/A') . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($u['name']) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($u['email']) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'><a href='?userId=" . ($u['id'] ?? $u['user_id']) . "' style='display: inline-block; padding: 5px 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 3px;'>Create Photo</a></td>";
                echo "</tr>";
            }
            
            echo "</table>";
        } else {
            echo "<p>No users found in the database.</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
}
?> 