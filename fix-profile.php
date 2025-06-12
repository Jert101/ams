<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Direct Profile Photo Fix</h1>";

// Set the user ID you want to fix - CHANGE THIS TO YOUR USER ID
$userIdToFix = 1;

// Connect to the database using credentials from .env
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
    
    // Get user info
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userIdToFix);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "<p>Found user: " . htmlspecialchars($user['name']) . " (ID: " . $user['id'] . ")</p>";
        echo "<p>Current profile_photo_path: " . var_export($user['profile_photo_path'], true) . "</p>";
        
        // Create profile photos directory
        $profilePhotoDir = __DIR__ . '/public/profile-photos';
        if (!file_exists($profilePhotoDir)) {
            if (mkdir($profilePhotoDir, 0777, true)) {
                echo "<p>Created profile photos directory</p>";
                chmod($profilePhotoDir, 0777);
            } else {
                echo "<p>Failed to create profile photos directory</p>";
            }
        }
        
        // Create a simple avatar image (blue circle with user's initials)
        $name = $user['name'];
        $initials = '';
        $words = explode(' ', $name);
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        $initials = substr($initials, 0, 2);
        
        // Generate color based on user ID
        $bgColor = '#' . substr(md5($user['id']), 0, 6);
        
        // Create SVG
        $svgContent = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="' . $bgColor . '"/><text x="100" y="115" font-family="Arial, sans-serif" font-size="80" font-weight="bold" text-anchor="middle" fill="#ffffff">' . $initials . '</text></svg>';
        
        // Save SVG file
        $filename = 'user-' . $user['id'] . '.svg';
        $filepath = $profilePhotoDir . '/' . $filename;
        
        if (file_put_contents($filepath, $svgContent)) {
            echo "<p>Created avatar file at: $filepath</p>";
            chmod($filepath, 0644);
            
            // Update user record in database
            $dbPath = 'profile-photos/' . $filename;
            $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id");
            $updateStmt->bindParam(':path', $dbPath);
            $updateStmt->bindParam(':id', $user['id']);
            
            if ($updateStmt->execute()) {
                echo "<p style='color:green; font-weight:bold;'>✅ SUCCESS: Updated user record with new profile photo path</p>";
                echo "<p>New profile_photo_path: " . $dbPath . "</p>";
                
                // Create a copy in public directory (redundancy)
                $publicProfileDir = __DIR__ . '/public/images/profiles';
                if (!file_exists($publicProfileDir)) {
                    mkdir($publicProfileDir, 0777, true);
                    chmod($publicProfileDir, 0777);
                }
                
                $publicFilepath = $publicProfileDir . '/' . $filename;
                file_put_contents($publicFilepath, $svgContent);
                chmod($publicFilepath, 0644);
                
                // Clear Laravel view cache
                $cacheDirs = [
                    __DIR__ . '/storage/framework/views',
                    __DIR__ . '/bootstrap/cache'
                ];
                
                foreach ($cacheDirs as $dir) {
                    if (file_exists($dir)) {
                        $files = glob($dir . '/*');
                        foreach ($files as $file) {
                            if (is_file($file)) {
                                unlink($file);
                            }
                        }
                        echo "<p>Cleared cache directory: $dir</p>";
                    }
                }
                
                // Show the avatar
                echo "<div style='margin:20px 0; padding:10px; border:1px solid #ccc;'>";
                echo "<p>Avatar preview:</p>";
                echo "<div style='width:100px; height:100px; margin:10px 0;'>" . $svgContent . "</div>";
                echo "<p>Avatar URL: <a href='/" . $dbPath . "' target='_blank'>/" . $dbPath . "</a></p>";
                echo "</div>";
                
                // Provide link to view the user profile
                echo "<p>View user profile with cache busting: <a href='/admin/users/" . $user['id'] . "/edit?nocache=" . time() . "' target='_blank'>Click here</a></p>";
                
                // Reminder to clear browser cache
                echo "<p><strong>Important:</strong> Also clear your browser cache or try in a private/incognito window.</p>";
            } else {
                echo "<p style='color:red;'>Failed to update user record</p>";
            }
        } else {
            echo "<p style='color:red;'>Failed to create avatar file</p>";
        }
    } else {
        echo "<p style='color:red;'>User not found with ID: $userIdToFix</p>";
        
        // Show all available users
        $stmt = $conn->prepare("SELECT id, name, email FROM users");
        $stmt->execute();
        $allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($allUsers) > 0) {
            echo "<p>Available users:</p>";
            echo "<ul>";
            foreach ($allUsers as $u) {
                echo "<li>ID: " . $u['id'] . ", Name: " . htmlspecialchars($u['name']) . ", Email: " . htmlspecialchars($u['email']) . "</li>";
            }
            echo "</ul>";
            
            echo "<p>To fix a different user, change the \$userIdToFix variable at the top of this script.</p>";
        } else {
            echo "<p>No users found in the database.</p>";
        }
    }
} catch(PDOException $e) {
    echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
}
?> 