<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix All User Avatars</h1>";

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
} else {
    echo "<p style='color:orange;'>⚠️ .env file not found. Enter credentials manually.</p>";
}

// Connect to database and fix all users
if (isset($_POST['fix_all']) || isset($_GET['fix_all'])) {
    try {
        // Connect to database
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get all users
        $stmt = $conn->prepare("SELECT id, name, email, profile_photo_path FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($users) > 0) {
            echo "<p>Found " . count($users) . " users to fix</p>";
            
            // Create profile photos directory
            $profilePhotoDir = __DIR__ . '/public/profile-photos';
            if (!file_exists($profilePhotoDir)) {
                if (mkdir($profilePhotoDir, 0777, true)) {
                    echo "<p>Created profile photos directory</p>";
                    chmod($profilePhotoDir, 0777);
                } else {
                    echo "<p style='color:red;'>Failed to create profile photos directory</p>";
                    $error = error_get_last();
                    echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
                }
            } else {
                echo "<p>Profile photos directory already exists</p>";
                chmod($profilePhotoDir, 0777);
            }
            
            // Create backup directory for the User model
            $userModelBackupDir = __DIR__ . '/app/Models/backups';
            if (!file_exists($userModelBackupDir)) {
                mkdir($userModelBackupDir, 0777, true);
            }
            
            // Backup User model
            $userModelPath = __DIR__ . '/app/Models/User.php';
            if (file_exists($userModelPath)) {
                $backupPath = $userModelBackupDir . '/User.php.' . time();
                copy($userModelPath, $backupPath);
                echo "<p>Created backup of User model at: " . $backupPath . "</p>";
            }
            
            // Process each user
            $successCount = 0;
            $errors = [];
            
            echo "<h2>Processing Users</h2>";
            echo "<div style='max-height: 400px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
            
            foreach ($users as $user) {
                echo "<div style='margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee;'>";
                echo "<h3>User: " . htmlspecialchars($user['name']) . " (ID: " . $user['id'] . ")</h3>";
                
                // Generate avatar
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
                
                try {
                    if (file_put_contents($filepath, $svgContent)) {
                        chmod($filepath, 0644);
                        
                        // Update user record
                        $dbPath = 'profile-photos/' . $filename;
                        $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id");
                        $updateStmt->bindParam(':path', $dbPath);
                        $updateStmt->bindParam(':id', $user['id']);
                        
                        if ($updateStmt->execute()) {
                            $successCount++;
                            echo "<p style='color:green;'>✅ Updated user with new avatar</p>";
                            echo "<div style='display:flex; align-items:center;'>";
                            echo "<div style='width:40px; height:40px; margin-right:10px;'>" . $svgContent . "</div>";
                            echo "<span>New path: " . $dbPath . "</span>";
                            echo "</div>";
                            
                            // Also create copies in other potential locations
                            $otherDirs = [
                                __DIR__ . '/public/images/profiles',
                                __DIR__ . '/storage/app/public/profile-photos'
                            ];
                            
                            foreach ($otherDirs as $dir) {
                                if (!file_exists($dir)) {
                                    mkdir($dir, 0777, true);
                                }
                                $otherPath = $dir . '/' . $filename;
                                file_put_contents($otherPath, $svgContent);
                                chmod($otherPath, 0644);
                            }
                        } else {
                            $errors[] = "Failed to update database record for user " . $user['id'];
                            echo "<p style='color:red;'>❌ Failed to update database record</p>";
                        }
                    } else {
                        $errors[] = "Failed to write avatar file for user " . $user['id'];
                        echo "<p style='color:red;'>❌ Failed to write avatar file</p>";
                    }
                } catch (Exception $e) {
                    $errors[] = "Error processing user " . $user['id'] . ": " . $e->getMessage();
                    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
                }
                
                echo "</div>"; // End user div
            }
            
            echo "</div>"; // End scrollable div
            
            // Update User model to always use the SVG avatars
            if (file_exists($userModelPath)) {
                $modelContent = file_get_contents($userModelPath);
                
                // Check if the getProfilePhotoUrlAttribute method exists
                $hasMethod = preg_match('/public\s+function\s+getProfilePhotoUrlAttribute\s*\(\)\s*\{/i', $modelContent);
                
                if ($hasMethod) {
                    // Replace the method with our fixed version
                    $pattern = '/public\s+function\s+getProfilePhotoUrlAttribute\s*\(\)\s*\{.*?\}/s';
                    $replacement = 'public function getProfilePhotoUrlAttribute()
    {
        $path = $this->profile_photo_path;
        
        if (empty($path) || $path === "0" || !file_exists(public_path($path))) {
            // Check if we have an SVG avatar
            $svgPath = "profile-photos/user-" . $this->id . ".svg";
            if (file_exists(public_path($svgPath))) {
                return url($svgPath) . "?v=" . time();
            }
            
            // Fallback to default
            return $this->defaultProfilePhotoUrl();
        }
        
        // Add cache-busting parameter
        return url($path) . "?v=" . time();
    }';
                    
                    $updatedContent = preg_replace($pattern, $replacement, $modelContent);
                    
                    // Also update defaultProfilePhotoUrl method if it exists
                    $hasDefaultMethod = preg_match('/protected\s+function\s+defaultProfilePhotoUrl\s*\(\)\s*\{/i', $updatedContent);
                    
                    if ($hasDefaultMethod) {
                        $pattern = '/protected\s+function\s+defaultProfilePhotoUrl\s*\(\)\s*\{.*?\}/s';
                        $replacement = 'protected function defaultProfilePhotoUrl()
    {
        $name = $this->name ?? "User";
        $initials = "";
        $words = explode(" ", $name);
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        $initials = substr($initials, 0, 2);
        
        // Generate color based on user ID
        $bgColor = "#" . substr(md5($this->id ?? 1), 0, 6);
        
        // Create SVG
        $svg = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"200\" height=\"200\" viewBox=\"0 0 200 200\"><rect width=\"200\" height=\"200\" fill=\"$bgColor\"/><text x=\"100\" y=\"115\" font-family=\"Arial, sans-serif\" font-size=\"80\" font-weight=\"bold\" text-anchor=\"middle\" fill=\"#ffffff\">$initials</text></svg>";
        
        // Convert to data URI
        return "data:image/svg+xml;base64," . base64_encode($svg);
    }';
                        
                        $updatedContent = preg_replace($pattern, $replacement, $updatedContent);
                    } else {
                        // Add the method if it doesn't exist
                        $pattern = '/(class\s+User\s+extends\s+Authenticatable.*?\{)/s';
                        $replacement = '$1

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        $name = $this->name ?? "User";
        $initials = "";
        $words = explode(" ", $name);
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        $initials = substr($initials, 0, 2);
        
        // Generate color based on user ID
        $bgColor = "#" . substr(md5($this->id ?? 1), 0, 6);
        
        // Create SVG
        $svg = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"200\" height=\"200\" viewBox=\"0 0 200 200\"><rect width=\"200\" height=\"200\" fill=\"$bgColor\"/><text x=\"100\" y=\"115\" font-family=\"Arial, sans-serif\" font-size=\"80\" font-weight=\"bold\" text-anchor=\"middle\" fill=\"#ffffff\">$initials</text></svg>";
        
        // Convert to data URI
        return "data:image/svg+xml;base64," . base64_encode($svg);
    }';
                        
                        $updatedContent = preg_replace($pattern, $replacement, $updatedContent);
                    }
                    
                    // Write the updated model file
                    if (file_put_contents($userModelPath, $updatedContent)) {
                        echo "<p style='color:green;'>✅ Updated User model with improved avatar handling</p>";
                    } else {
                        echo "<p style='color:red;'>❌ Failed to update User model</p>";
                    }
                } else {
                    echo "<p style='color:orange;'>⚠️ Could not find getProfilePhotoUrlAttribute method in User model</p>";
                }
            } else {
                echo "<p style='color:red;'>❌ User model file not found</p>";
            }
            
            // Clear Laravel caches
            $cacheDirs = [
                __DIR__ . '/storage/framework/views',
                __DIR__ . '/storage/framework/cache',
                __DIR__ . '/bootstrap/cache'
            ];
            
            foreach ($cacheDirs as $dir) {
                if (file_exists($dir)) {
                    $files = glob($dir . '/*');
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            @unlink($file);
                        }
                    }
                }
            }
            
            echo "<h2>Summary</h2>";
            echo "<p>Successfully updated $successCount out of " . count($users) . " users</p>";
            
            if (count($errors) > 0) {
                echo "<p style='color:red;'>Errors encountered:</p>";
                echo "<ul>";
                foreach ($errors as $error) {
                    echo "<li>" . htmlspecialchars($error) . "</li>";
                }
                echo "</ul>";
            }
            
            echo "<p style='color:green; font-size:1.2em; font-weight:bold;'>
                🎉 All users have been processed! Try viewing a user profile now with cache busting:
                <a href='/admin/users/1/edit?nocache=" . time() . "' target='_blank'>View Admin User</a>
            </p>";
            
            echo "<p><strong>Important:</strong> Clear your browser cache or try in a private/incognito window.</p>";
            
        } else {
            echo "<p style='color:red;'>No users found in the database</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
    }
} else {
    // Display form
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Fix All User Avatars</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 800px; margin: 0 auto; }
            h1, h2, h3 { color: #3498db; }
            .card { border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
            .btn { background-color: #3498db; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
            .btn:hover { background-color: #2980b9; }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; font-weight: bold; }
            input[type="text"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
            .warning { background-color: #fcf8e3; border: 1px solid #faebcc; color: #8a6d3b; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        </style>
    </head>
    <body>
        <h2>Database Connection</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="db_host">Database Host:</label>
                <input type="text" id="db_host" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>">
            </div>
            <div class="form-group">
                <label for="db_name">Database Name:</label>
                <input type="text" id="db_name" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>">
            </div>
            <div class="form-group">
                <label for="db_username">Database Username:</label>
                <input type="text" id="db_username" name="db_username" value="<?php echo htmlspecialchars($dbUsername); ?>">
            </div>
            <div class="form-group">
                <label for="db_password">Database Password:</label>
                <input type="password" id="db_password" name="db_password" value="<?php echo htmlspecialchars($dbPassword); ?>">
            </div>
            
            <div class="warning">
                <strong>Warning:</strong> This script will:
                <ul>
                    <li>Generate SVG avatars for all users in the database</li>
                    <li>Update all users' profile_photo_path in the database</li>
                    <li>Modify the User model to improve avatar handling</li>
                    <li>Clear Laravel caches</li>
                </ul>
                A backup of your User model will be created before any changes.
            </div>
            
            <button type="submit" name="fix_all" class="btn">Fix All User Avatars</button>
        </form>
        
        <p>You can also run this fix by adding <code>?fix_all=1</code> to the URL.</p>
    </body>
    </html>
    <?php
}
?> 