<?php
// Simple script to directly fix profile photos for all users
// No dependencies, just pure PHP to maximize compatibility

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database credentials - EDIT THESE FOR YOUR SERVER
$dbHost = 'localhost';     // Usually 'localhost' or '127.0.0.1'
$dbName = 'ams';          // Your database name
$dbUsername = 'root';     // Your database username
$dbPassword = '';         // Your database password 

// Directory to save avatar files
$avatarDir = __DIR__ . '/public/profile-photos';

echo "<h1>Quick Profile Photo Fix</h1>";

try {
    // Connect to database
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create avatars directory if it doesn't exist
    if (!file_exists($avatarDir)) {
        if (mkdir($avatarDir, 0777, true)) {
            echo "<p>Created profile photos directory: $avatarDir</p>";
            chmod($avatarDir, 0777);
        } else {
            echo "<p style='color:red;'>Failed to create profile photos directory!</p>";
        }
    }
    
    // Get all users
    $stmt = $conn->query("SELECT id, name FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Found " . count($users) . " users</p>";
    
    $success = 0;
    
    // Process each user
    foreach ($users as $user) {
        // Generate initials
        $name = $user['name'];
        $initials = '';
        $words = explode(' ', $name);
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        $initials = substr($initials, 0, 2);
        
        // Generate avatar color based on user ID
        $bgColor = '#' . substr(md5($user['id']), 0, 6);
        
        // Create SVG avatar
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">'
             . '<rect width="200" height="200" fill="' . $bgColor . '"/>'
             . '<text x="100" y="115" font-family="Arial, sans-serif" font-size="80" font-weight="bold" '
             . 'text-anchor="middle" fill="#ffffff">' . $initials . '</text></svg>';
        
        // Save avatar to file
        $filename = 'user-' . $user['id'] . '.svg';
        $filepath = $avatarDir . '/' . $filename;
        
        if (file_put_contents($filepath, $svg)) {
            chmod($filepath, 0644);
            
            // Update user record in database
            $dbPath = 'profile-photos/' . $filename;
            $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = ? WHERE id = ?");
            if ($updateStmt->execute([$dbPath, $user['id']])) {
                $success++;
                echo "<p>✅ Fixed avatar for User ID: " . $user['id'] . " - " . htmlspecialchars($user['name']) . "</p>";
            }
        }
    }
    
    echo "<h2>Summary</h2>";
    echo "<p>Successfully updated $success out of " . count($users) . " users</p>";
    
    // Update User model to handle the avatars better
    $userModelPath = __DIR__ . '/app/Models/User.php';
    if (file_exists($userModelPath)) {
        // Create backup first
        copy($userModelPath, $userModelPath . '.backup.' . time());
        
        $modelCode = file_get_contents($userModelPath);
        
        // Find and replace the getProfilePhotoUrlAttribute method
        $pattern = '/public\s+function\s+getProfilePhotoUrlAttribute\s*\(\)\s*\{.*?\}/s';
        $replacement = 'public function getProfilePhotoUrlAttribute()
    {
        $path = $this->profile_photo_path;
        
        // If path is empty, zero, or file doesn\'t exist
        if (empty($path) || $path === "0" || $path === 0 || !file_exists(public_path($path))) {
            // Check if we have an SVG avatar
            $svgPath = "profile-photos/user-" . $this->id . ".svg";
            if (file_exists(public_path($svgPath))) {
                return url($svgPath) . "?v=" . time();
            }
            
            // Fallback to default
            return url("img/kofa.png");
        }
        
        // Add cache-busting parameter
        return url($path) . "?v=" . time();
    }';
        
        $updatedCode = preg_replace($pattern, $replacement, $modelCode);
        
        if (file_put_contents($userModelPath, $updatedCode)) {
            echo "<p>✅ Updated User model with improved avatar handling</p>";
        } else {
            echo "<p style='color:red;'>Failed to update User model!</p>";
        }
    }
    
    echo "<p style='color:green; font-weight:bold;'>🎉 All done! Clear your browser cache and try viewing a user profile now.</p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
}
?> 