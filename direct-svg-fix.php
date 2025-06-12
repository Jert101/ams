<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Direct SVG Avatar Fix</h1>";

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

// Define paths for public directory
$basePath = __DIR__;
$publicPath = $basePath . '/public';
$storagePath = $basePath . '/storage';
$storageAppPublicPath = $storagePath . '/app/public';
$profilePhotosPath = $storageAppPublicPath . '/profile-photos';
$publicStoragePath = $publicPath . '/storage';
$publicProfilePhotosPath = $publicStoragePath . '/profile-photos';
$directPublicPath = $publicPath . '/profile-photos';

// Create directories if they don't exist
if (isset($_POST['create_dirs'])) {
    foreach ([$storageAppPublicPath, $profilePhotosPath, $publicStoragePath, $publicProfilePhotosPath, $directPublicPath] as $dir) {
        if (!file_exists($dir)) {
            if (mkdir($dir, 0777, true)) {
                echo "<p style='color:green;'>✅ Created directory: $dir</p>";
                chmod($dir, 0777);
            } else {
                echo "<p style='color:red;'>❌ Failed to create directory: $dir</p>";
                $error = error_get_last();
                echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
            }
        } else {
            echo "<p style='color:green;'>✅ Directory exists: $dir</p>";
            chmod($dir, 0777);
        }
    }
}

// Function to generate SVG avatar for a user
function generateAvatarSvg($name, $userId) {
    // Generate initials from name
    $initials = '';
    $words = explode(' ', $name);
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    $initials = substr($initials, 0, 2); // Limit to 2 characters
    
    // Generate a consistent color based on the user ID
    $bgColor = '#' . substr(md5($userId), 0, 6);
    
    // Create SVG
    return '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <rect width="200" height="200" fill="' . $bgColor . '"/>
  <text x="100" y="115" font-family="Arial, sans-serif" font-size="80" font-weight="bold" text-anchor="middle" fill="#ffffff">' . $initials . '</text>
</svg>';
}

// Fix all users with problematic profile photos
if (isset($_POST['fix_all_users']) && !empty($dbUsername) && !empty($dbName)) {
    try {
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get all users with problematic profile photos
        $stmt = $conn->prepare("SELECT * FROM users WHERE profile_photo_path = '0' OR profile_photo_path = 0 OR profile_photo_path IS NULL OR profile_photo_path = 'null'");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Found " . count($users) . " users with invalid profile photos</p>";
        
        $fixedCount = 0;
        foreach ($users as $user) {
            // Generate SVG content
            $svgContent = generateAvatarSvg($user['name'], $user['id']);
            
            // Create filename
            $filename = 'user-' . $user['id'] . '-' . time() . '.svg';
            $relativePath = 'profile-photos/' . $filename;
            
            // Save to all locations
            $filePaths = [
                $profilePhotosPath . '/' . $filename,
                $publicProfilePhotosPath . '/' . $filename,
                $directPublicPath . '/' . $filename
            ];
            
            $success = false;
            foreach ($filePaths as $filePath) {
                $dirPath = dirname($filePath);
                if (!file_exists($dirPath)) {
                    mkdir($dirPath, 0777, true);
                    chmod($dirPath, 0777);
                }
                
                if (file_put_contents($filePath, $svgContent)) {
                    chmod($filePath, 0644);
                    $success = true;
                }
            }
            
            if ($success) {
                // Update the database
                $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id");
                $updateStmt->bindParam(':path', $relativePath);
                $updateStmt->bindParam(':id', $user['id']);
                
                if ($updateStmt->execute()) {
                    $fixedCount++;
                    echo "<p style='color:green;'>✅ Fixed user {$user['name']} (ID: {$user['id']})</p>";
                    
                    // Show the generated image
                    echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd;'>";
                    echo "<strong>User: {$user['name']}</strong><br>";
                    echo "<div style='display: inline-block; width: 50px; height: 50px; margin-right: 10px;'>";
                    echo $svgContent;
                    echo "</div>";
                    echo "Path: $relativePath";
                    echo "</div>";
                }
            }
        }
        
        echo "<p>Fixed $fixedCount users with SVG avatars</p>";
        
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

// Fix specific user
if (isset($_POST['fix_specific_user']) && !empty($_POST['user_id']) && !empty($dbUsername) && !empty($dbName)) {
    $userId = (int)$_POST['user_id'];
    
    try {
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get user info
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<p>Found user: " . $user['name'] . " (ID: " . $user['id'] . ")</p>";
            
            // Generate SVG content
            $svgContent = generateAvatarSvg($user['name'], $user['id']);
            
            // Create filename
            $filename = 'user-' . $user['id'] . '-' . time() . '.svg';
            $relativePath = 'profile-photos/' . $filename;
            
            // Save to all locations
            $filePaths = [
                $profilePhotosPath . '/' . $filename,
                $publicProfilePhotosPath . '/' . $filename,
                $directPublicPath . '/' . $filename
            ];
            
            $success = false;
            foreach ($filePaths as $filePath) {
                $dirPath = dirname($filePath);
                if (!file_exists($dirPath)) {
                    mkdir($dirPath, 0777, true);
                    chmod($dirPath, 0777);
                }
                
                if (file_put_contents($filePath, $svgContent)) {
                    echo "<p style='color:green;'>✅ Created avatar at: $filePath</p>";
                    chmod($filePath, 0644);
                    $success = true;
                } else {
                    echo "<p style='color:red;'>❌ Failed to create avatar at: $filePath</p>";
                    $error = error_get_last();
                    echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
                }
            }
            
            if ($success) {
                // Update the database
                $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id");
                $updateStmt->bindParam(':path', $relativePath);
                $updateStmt->bindParam(':id', $userId);
                
                if ($updateStmt->execute()) {
                    echo "<p style='color:green;'>✅ Updated user's profile_photo_path to: $relativePath</p>";
                    
                    // Show the generated image
                    echo "<h3>Generated Avatar</h3>";
                    echo $svgContent;
                    echo "<p>This SVG avatar will now be used for the user.</p>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to update database</p>";
                }
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
    <title>Direct SVG Avatar Fix</title>
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

<h2>Fix Avatar Issues</h2>

<div class="card">
    <h3>1. Create Directories</h3>
    <p>Create all necessary directories for profile photos with proper permissions.</p>
    <form method="post" action="">
        <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>">
        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>">
        <input type="hidden" name="db_username" value="<?php echo htmlspecialchars($dbUsername); ?>">
        <input type="hidden" name="db_password" value="<?php echo htmlspecialchars($dbPassword); ?>">
        <button type="submit" name="create_dirs" class="btn">Create Directories</button>
    </form>
</div>

<div class="card">
    <h3>2. Fix Specific User's Avatar</h3>
    <p>Create an SVG avatar for a specific user and update the database.</p>
    <form method="post" action="">
        <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>">
        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>">
        <input type="hidden" name="db_username" value="<?php echo htmlspecialchars($dbUsername); ?>">
        <input type="hidden" name="db_password" value="<?php echo htmlspecialchars($dbPassword); ?>">
        <div class="form-group">
            <label for="user_id">User ID:</label>
            <input type="text" id="user_id" name="user_id" value="110007">
        </div>
        <button type="submit" name="fix_specific_user" class="btn">Fix User's Avatar</button>
    </form>
</div>

<div class="card">
    <h3>3. Fix All Invalid Avatars</h3>
    <p>Generate SVG avatars for all users with missing or invalid profile photos.</p>
    <form method="post" action="">
        <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>">
        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>">
        <input type="hidden" name="db_username" value="<?php echo htmlspecialchars($dbUsername); ?>">
        <input type="hidden" name="db_password" value="<?php echo htmlspecialchars($dbPassword); ?>">
        <button type="submit" name="fix_all_users" class="btn">Fix All Avatars</button>
    </form>
</div>

<h2>How This Works</h2>
<p>This script generates SVG avatars with user initials directly in the code, rather than relying on external files. These avatars:</p>
<ul>
    <li>Are displayed inline (no file access needed)</li>
    <li>Show user initials in a colored circle</li>
    <li>Have a unique color based on the user ID</li>
    <li>Don't depend on filesystem or permissions</li>
</ul>

<p>After fixing users, try viewing a user profile page with cache busting:</p>
<p><a href="/admin/users/110007/edit?nocache=<?php echo time(); ?>" target="_blank">Edit User Page (with cache busting)</a></p>

</body>
</html> 