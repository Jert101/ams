<?php
// Fix for profile photos on InfinityFree hosting
// This script should be uploaded to your InfinityFree hosting

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>InfinityFree Profile Photo Fix</h1>";
echo "<p>This script fixes profile photo display issues directly on your InfinityFree hosting.</p>";

// On InfinityFree, the profile images are likely not showing because:
// 1. The path in the database might be incorrect or using "0"
// 2. The User model's getProfilePhotoUrlAttribute method might be using functions not compatible with InfinityFree
// 3. The images might not be uploaded to the correct location

// STEP 1: Get database credentials directly from your InfinityFree .env file
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
echo "<button type='submit' name='check_db' style='padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Connect to Database</button>";
echo "</div>";
echo "</form>";

// Process database connection
$db_connected = false;
$users = [];

if (isset($_POST['check_db'])) {
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    
    try {
        // Try connecting to the database
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p style='color:green;'>✅ Successfully connected to database</p>";
        $db_connected = true;
        
        // Get all users
        $stmt = $conn->query("SELECT id, user_id, name, email, profile_photo_path FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Display user list
        echo "<h2>Users in Database</h2>";
        echo "<p>Found " . count($users) . " users</p>";
        
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background-color: #f2f2f2;'>";
        echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>ID</th>";
        echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>User ID</th>";
        echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Name</th>";
        echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Email</th>";
        echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Profile Photo Path</th>";
        echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Action</th>";
        echo "</tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $user['id'] . "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $user['user_id'] . "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($user['name']) . "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($user['profile_photo_path'] ?? 'NULL') . "</td>";
            echo "<td style='border: 1px solid #ddd; padding: 8px;'>";
            echo "<form method='post' action='' style='display:inline;'>";
            echo "<input type='hidden' name='db_host' value='" . htmlspecialchars($dbHost) . "'>";
            echo "<input type='hidden' name='db_name' value='" . htmlspecialchars($dbName) . "'>";
            echo "<input type='hidden' name='db_username' value='" . htmlspecialchars($dbUsername) . "'>";
            echo "<input type='hidden' name='db_password' value='" . htmlspecialchars($dbPassword) . "'>";
            echo "<input type='hidden' name='user_id' value='" . $user['id'] . "'>";
            echo "<button type='submit' name='fix_user' style='padding: 5px 10px; background-color: #2196F3; color: white; border: none; cursor: pointer;'>Fix</button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Show Fix All button
        echo "<form method='post' action='' style='margin-top: 20px;'>";
        echo "<input type='hidden' name='db_host' value='" . htmlspecialchars($dbHost) . "'>";
        echo "<input type='hidden' name='db_name' value='" . htmlspecialchars($dbName) . "'>";
        echo "<input type='hidden' name='db_username' value='" . htmlspecialchars($dbUsername) . "'>";
        echo "<input type='hidden' name='db_password' value='" . htmlspecialchars($dbPassword) . "'>";
        echo "<button type='submit' name='fix_all_users' style='padding: 10px; background-color: #FF9800; color: white; border: none; cursor: pointer;'>Fix All Users</button>";
        echo "</form>";
        
        // Show Fix User Model button
        echo "<form method='post' action='' style='margin-top: 20px;'>";
        echo "<input type='hidden' name='db_host' value='" . htmlspecialchars($dbHost) . "'>";
        echo "<input type='hidden' name='db_name' value='" . htmlspecialchars($dbName) . "'>";
        echo "<input type='hidden' name='db_username' value='" . htmlspecialchars($dbUsername) . "'>";
        echo "<input type='hidden' name='db_password' value='" . htmlspecialchars($dbPassword) . "'>";
        echo "<button type='submit' name='fix_model' style='padding: 10px; background-color: #9C27B0; color: white; border: none; cursor: pointer;'>Update User Model</button>";
        echo "</form>";
        
    } catch(PDOException $e) {
        echo "<p style='color:red;'>Database connection failed: " . $e->getMessage() . "</p>";
    }
}

// Fix a specific user
if (isset($_POST['fix_user']) && isset($_POST['user_id'])) {
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    $userId = $_POST['user_id'];
    
    try {
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get user info
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<h2>Fixing User: " . htmlspecialchars($user['name']) . "</h2>";
            
            // Update the profile_photo_path to use the correct format
            $newPhotoPath = 'img/kofa.png'; // Default to kofa.png
            
            $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id");
            $updateStmt->bindParam(':path', $newPhotoPath);
            $updateStmt->bindParam(':id', $userId);
            
            if ($updateStmt->execute()) {
                echo "<p style='color:green;'>✅ Updated profile_photo_path for " . htmlspecialchars($user['name']) . " to: $newPhotoPath</p>";
                echo "<p>This user will now use the default kofa.png logo</p>";
            } else {
                echo "<p style='color:red;'>Failed to update user</p>";
            }
        } else {
            echo "<p style='color:red;'>User not found with ID: $userId</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
    }
}

// Fix all users
if (isset($_POST['fix_all_users'])) {
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    
    try {
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Update all users to use the default kofa.png
        $newPhotoPath = 'img/kofa.png';
        $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path");
        $updateStmt->bindParam(':path', $newPhotoPath);
        
        if ($updateStmt->execute()) {
            $count = $updateStmt->rowCount();
            echo "<p style='color:green;'>✅ Updated profile_photo_path for $count users to use the default logo</p>";
        } else {
            echo "<p style='color:red;'>Failed to update users</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
    }
}

// Fix User Model
if (isset($_POST['fix_model'])) {
    $userModelPath = __DIR__ . '/app/Models/User.php';
    
    if (file_exists($userModelPath)) {
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
                echo "<p>The User model has been simplified to work better with InfinityFree hosting.</p>";
                
                // Try to clear Laravel caches
                echo "<p>Attempting to clear Laravel caches...</p>";
                $success = true;
                
                // These commands might not work on InfinityFree, but we'll try
                $commands = [
                    'php artisan cache:clear',
                    'php artisan view:clear',
                    'php artisan config:clear'
                ];
                
                foreach ($commands as $command) {
                    @exec($command, $output, $returnVar);
                    if ($returnVar !== 0) {
                        $success = false;
                    }
                }
                
                if ($success) {
                    echo "<p style='color:green;'>✅ Laravel caches cleared</p>";
                } else {
                    echo "<p style='color:orange;'>⚠️ Could not clear Laravel caches automatically</p>";
                    echo "<p>You may need to manually clear the following directories:</p>";
                    echo "<ul>";
                    echo "<li>" . __DIR__ . "/storage/framework/views/</li>";
                    echo "<li>" . __DIR__ . "/storage/framework/cache/</li>";
                    echo "<li>" . __DIR__ . "/bootstrap/cache/</li>";
                    echo "</ul>";
                }
                
                // Provide a manual way to clear caches
                echo "<h3>Manual Cache Clearing</h3>";
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='db_host' value='" . htmlspecialchars($dbHost) . "'>";
                echo "<input type='hidden' name='db_name' value='" . htmlspecialchars($dbName) . "'>";
                echo "<input type='hidden' name='db_username' value='" . htmlspecialchars($dbUsername) . "'>";
                echo "<input type='hidden' name='db_password' value='" . htmlspecialchars($dbPassword) . "'>";
                echo "<button type='submit' name='clear_caches' style='padding: 10px; background-color: #607D8B; color: white; border: none; cursor: pointer;'>Manually Clear Caches</button>";
                echo "</form>";
            } else {
                echo "<p style='color:red;'>Failed to write updated User model</p>";
                echo "<p>This might be due to file permissions. Try changing the permissions on the file to allow writing.</p>";
            }
        } else {
            echo "<p style='color:red;'>Failed to create backup of User model</p>";
        }
    } else {
        echo "<p style='color:red;'>User model file not found at expected location: $userModelPath</p>";
    }
}

// Manually clear caches
if (isset($_POST['clear_caches'])) {
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
    
    echo "<p style='color:green;'>✅ Manually cleared $filesCleared cache files</p>";
}

// Display how to check if it's working
echo "<h2>Testing the Fix</h2>";
echo "<p>After applying fixes, you should:</p>";
echo "<ol>";
echo "<li>Clear your browser cache or open a private/incognito window</li>";
echo "<li>Visit your user profile page to see if the profile photo now displays correctly</li>";
echo "</ol>";

// Link to admin users page
echo "<p><a href='/admin/users' style='display: inline-block; padding: 10px; background-color: #4CAF50; color: white; text-decoration: none;'>Go to Users Page</a></p>";

echo "<h2>Additional Info</h2>";
echo "<p>This script has been created specifically for fixing profile photo display issues on InfinityFree hosting.</p>";
echo "<p>If you're still having issues, try making sure:</p>";
echo "<ul>";
echo "<li>The 'img/kofa.png' file exists and is accessible</li>";
echo "<li>Your PHP version is compatible with Laravel (PHP 8.0+ recommended)</li>";
echo "<li>The .env file has the correct database credentials</li>";
echo "</ul>";
?>
