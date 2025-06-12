<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Force Avatar Display Fix</h1>";

// Get database credentials from .env file
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
    
    $dbHost = isset($hostMatches[1]) ? trim($hostMatches[1]) : 'localhost';
    $dbName = isset($dbMatches[1]) ? trim($dbMatches[1]) : '';
    $dbUsername = isset($userMatches[1]) ? trim($userMatches[1]) : '';
    $dbPassword = isset($passMatches[1]) ? trim($passMatches[1]) : '';
    
    echo "<p>Found database credentials in .env file</p>";
} else {
    echo "<p style='color:orange;'>⚠️ .env file not found. Enter credentials manually.</p>";
}

// Allow manual override of database credentials
if (isset($_POST['db_submit'])) {
    $dbHost = $_POST['db_host'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    $dbName = $_POST['db_name'];
    echo "<p style='color:green;'>✅ Database credentials updated</p>";
}

// Force modify User model
if (isset($_POST['modify_user_model'])) {
    $userModelPath = __DIR__ . '/app/Models/User.php';
    
    if (file_exists($userModelPath)) {
        // Create a backup
        $backupPath = $userModelPath . '.backup.' . time();
        if (copy($userModelPath, $backupPath)) {
            echo "<p style='color:green;'>✅ Created backup of User model at $backupPath</p>";
            
            // Read the model file
            $modelContent = file_get_contents($userModelPath);
            
            // Replace or add the avatar methods
            $pattern = '/public\s+function\s+getProfilePhotoUrlAttribute\s*\(\)\s*\{.*?\}/s';
            $replacement = 'public function getProfilePhotoUrlAttribute()
    {
        // Debug logging to help diagnose issues on production
        \Log::debug(\'Getting profile photo URL\', [
            \'user_id\' => $this->user_id,
            \'photo_path\' => $this->profile_photo_path,
            \'type\' => gettype($this->profile_photo_path)
        ]);
        
        // Directly generate SVG for all users
        return $this->generateInlineSvgAvatar();
    }
    
    /**
     * Generate an inline SVG avatar with user\'s initials.
     *
     * @return string
     */
    protected function generateInlineSvgAvatar()
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
        $bgColor = \'#\' . substr(md5($this->user_id ?? 1), 0, 6);
        
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
            
            if (preg_match($pattern, $modelContent)) {
                // Replace existing method
                $updatedContent = preg_replace($pattern, $replacement, $modelContent);
                
                // Check if we need to add the defaultProfilePhotoUrl method
                if (!preg_match('/function\s+defaultProfilePhotoUrl\s*\(\s*\)/i', $updatedContent)) {
                    $pattern = '/class\s+User\s+extends\s+Authenticatable/i';
                    $replacement = "class User extends Authenticatable\n{\n    /**\n     * Default profile photo URL\n     */\n    protected function defaultProfilePhotoUrl()\n    {\n        return \$this->generateInlineSvgAvatar();\n    }\n";
                    $updatedContent = preg_replace($pattern, $replacement, $updatedContent);
                }
                
                if (file_put_contents($userModelPath, $updatedContent)) {
                    echo "<p style='color:green;'>✅ Updated User model with forced SVG avatar method</p>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to write to User model file</p>";
                    $error = error_get_last();
                    echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
                }
            } else {
                echo "<p style='color:red;'>❌ Could not find getProfilePhotoUrlAttribute method in User model</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Failed to create backup of User model</p>";
            $error = error_get_last();
            echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ User model file not found at $userModelPath</p>";
    }
}

// Modify user record in database to force re-render
if (isset($_POST['force_update_user']) && !empty($_POST['user_id']) && !empty($dbUsername) && !empty($dbName)) {
    try {
        $userId = (int)$_POST['user_id'];
        
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get current user info
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Add a timestamp to force a change
            $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path, updated_at = NOW() WHERE id = :id");
            $updateStmt->bindParam(':path', $user['profile_photo_path']);
            $updateStmt->bindParam(':id', $userId);
            
            if ($updateStmt->execute()) {
                echo "<p style='color:green;'>✅ Updated user record to force re-render</p>";
            } else {
                echo "<p style='color:red;'>❌ Failed to update user record</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ User not found with ID: $userId</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

// Clear all caches
if (isset($_POST['clear_all_caches'])) {
    // Define cache directories
    $cacheDirs = [
        __DIR__ . '/storage/framework/views',
        __DIR__ . '/storage/framework/cache',
        __DIR__ . '/storage/framework/sessions',
        __DIR__ . '/bootstrap/cache'
    ];
    
    $totalCleared = 0;
    
    // Create and clear each directory
    foreach ($cacheDirs as $dir) {
        if (!file_exists($dir)) {
            if (mkdir($dir, 0777, true)) {
                echo "<p style='color:green;'>✅ Created directory: $dir</p>";
                chmod($dir, 0777);
            } else {
                echo "<p style='color:red;'>❌ Failed to create directory: $dir</p>";
                continue;
            }
        }
        
        $files = glob($dir . '/*');
        $count = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if (unlink($file)) {
                    $count++;
                }
            }
        }
        
        $totalCleared += $count;
        echo "<p>Cleared $count files from $dir</p>";
    }
    
    echo "<p style='color:green;'>✅ Cleared a total of $totalCleared cache files</p>";
}

// Create direct public profile photo directory
if (isset($_POST['create_avatar_dir'])) {
    $profilePhotoDir = __DIR__ . '/public/avatars';
    
    if (!file_exists($profilePhotoDir)) {
        if (mkdir($profilePhotoDir, 0777, true)) {
            echo "<p style='color:green;'>✅ Created avatars directory: $profilePhotoDir</p>";
            chmod($profilePhotoDir, 0777);
        } else {
            echo "<p style='color:red;'>❌ Failed to create avatars directory</p>";
            $error = error_get_last();
            echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color:green;'>✅ Avatars directory already exists</p>";
        chmod($profilePhotoDir, 0777);
    }
    
    // Create a test file
    $testFile = $profilePhotoDir . '/test.svg';
    $svgContent = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="#ff0000"/><text x="100" y="115" font-family="Arial, sans-serif" font-size="80" font-weight="bold" text-anchor="middle" fill="#ffffff">OK</text></svg>';
    
    if (file_put_contents($testFile, $svgContent)) {
        echo "<p style='color:green;'>✅ Created test SVG file: $testFile</p>";
        chmod($testFile, 0644);
        echo "<p>Test image URL: <a href='/avatars/test.svg' target='_blank'>/avatars/test.svg</a></p>";
        echo "<div style='width:100px; height:100px;'>" . $svgContent . "</div>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create test SVG file</p>";
    }
}

// Force create individual user avatar file
if (isset($_POST['create_user_avatar']) && !empty($_POST['user_id']) && !empty($dbUsername) && !empty($dbName)) {
    try {
        $userId = (int)$_POST['user_id'];
        
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get user info
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Generate avatar SVG
            $name = $user['name'] ?? 'User';
            $initials = '';
            $words = explode(' ', $name);
            foreach ($words as $word) {
                if (!empty($word)) {
                    $initials .= strtoupper(substr($word, 0, 1));
                }
            }
            $initials = substr($initials, 0, 2); // Limit to 2 characters
            
            // Generate a consistent color based on the user ID
            $bgColor = '#' . substr(md5($user['id']), 0, 6);
            
            // Create SVG
            $svgContent = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="' . $bgColor . '"/><text x="100" y="115" font-family="Arial, sans-serif" font-size="80" font-weight="bold" text-anchor="middle" fill="#ffffff">' . $initials . '</text></svg>';
            
            // Create directories if needed
            $avatarsDir = __DIR__ . '/public/avatars';
            if (!file_exists($avatarsDir)) {
                mkdir($avatarsDir, 0777, true);
                chmod($avatarsDir, 0777);
            }
            
            // Create avatar file
            $avatarFile = $avatarsDir . '/user-' . $userId . '.svg';
            if (file_put_contents($avatarFile, $svgContent)) {
                echo "<p style='color:green;'>✅ Created avatar file: $avatarFile</p>";
                chmod($avatarFile, 0644);
                
                // Update user record
                $avatarPath = 'avatars/user-' . $userId . '.svg';
                $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id");
                $updateStmt->bindParam(':path', $avatarPath);
                $updateStmt->bindParam(':id', $userId);
                
                if ($updateStmt->execute()) {
                    echo "<p style='color:green;'>✅ Updated user record with new avatar path</p>";
                    echo "<p>Avatar URL: <a href='/$avatarPath' target='_blank'>/$avatarPath</a></p>";
                    echo "<div style='width:100px; height:100px;'>" . $svgContent . "</div>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to update user record</p>";
                }
            } else {
                echo "<p style='color:red;'>❌ Failed to create avatar file</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ User not found with ID: $userId</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

// HTML Form
?>
<!DOCTYPE html>
<html>
<head>
    <title>Force Avatar Display Fix</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 800px; margin: 0 auto; }
        h1, h2, h3 { color: #e74c3c; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        .btn { background-color: #e74c3c; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        .btn:hover { background-color: #c0392b; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
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
    <button type="submit" name="db_submit" class="btn">Update Database Credentials</button>
</form>

<h2>Fix Avatar Display</h2>

<div class="card">
    <h3>Step 1: Modify User Model</h3>
    <p>This will modify the User model to use inline SVG avatars for all users.</p>
    <form method="post" action="">
        <button type="submit" name="modify_user_model" class="btn">Modify User Model</button>
    </form>
</div>

<div class="card">
    <h3>Step 2: Clear All Caches</h3>
    <p>Clear all Laravel caches to ensure changes take effect.</p>
    <form method="post" action="">
        <button type="submit" name="clear_all_caches" class="btn">Clear All Caches</button>
    </form>
</div>

<div class="card">
    <h3>Step 3: Create Avatar Directory</h3>
    <p>Create a public directory for avatar storage and test write access.</p>
    <form method="post" action="">
        <button type="submit" name="create_avatar_dir" class="btn">Create Avatar Directory</button>
    </form>
</div>

<div class="card">
    <h3>Step 4: Create User Avatar</h3>
    <p>Create a specific user's avatar file and update their database record.</p>
    <form method="post" action="">
        <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>">
        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>">
        <input type="hidden" name="db_username" value="<?php echo htmlspecialchars($dbUsername); ?>">
        <input type="hidden" name="db_password" value="<?php echo htmlspecialchars($dbPassword); ?>">
        <div class="form-group">
            <label for="user_id">User ID:</label>
            <input type="text" id="user_id" name="user_id" value="110007">
        </div>
        <button type="submit" name="create_user_avatar" class="btn">Create User Avatar</button>
    </form>
</div>

<div class="card">
    <h3>Step 5: Force Update User</h3>
    <p>Force update a user record to trigger a re-render of their profile.</p>
    <form method="post" action="">
        <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>">
        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>">
        <input type="hidden" name="db_username" value="<?php echo htmlspecialchars($dbUsername); ?>">
        <input type="hidden" name="db_password" value="<?php echo htmlspecialchars($dbPassword); ?>">
        <div class="form-group">
            <label for="user_id2">User ID:</label>
            <input type="text" id="user_id2" name="user_id" value="110007">
        </div>
        <button type="submit" name="force_update_user" class="btn">Force Update User</button>
    </form>
</div>

<h2>How to Test</h2>
<p>After completing all steps, try viewing the user profile with cache busting:</p>
<p><a href="/admin/users/110007/edit?nocache=<?php echo time(); ?>" target="_blank">View User (with cache busting)</a></p>

<h2>Browser Cache</h2>
<p>Also clear your browser cache or try in a private/incognito window.</p>

</body>
</html> 