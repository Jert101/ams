^<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix ALL User Profile Photos</h1>";
echo "<p>This script will update all users in the database with proper profile photo paths.</p>";

// Get database credentials from form submission or .env file
$envFile = __DIR__ . '/.env';
$dbHost = '';
$dbName = '';
$dbUsername = '';
$dbPassword = '';

// Try to get database credentials from .env
if (file_exists($envFile)) {
    $envContents = file_get_contents($envFile);
    
    // Extract database credentials using regex
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

// Use form-submitted credentials if provided
if (isset($_POST['db_submit'])) {
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    echo "<p style='color:green;'>✅ Database credentials updated</p>";
}

// Define paths
$basePath = __DIR__;
$publicPath = $basePath . '/public';
$storagePath = $basePath . '/storage';
$storageAppPublicPath = $storagePath . '/app/public';
$profilePhotosPath = $storageAppPublicPath . '/profile-photos';
$publicStoragePath = $publicPath . '/storage';
$publicProfilePhotosPath = $publicStoragePath . '/profile-photos';

// Create necessary directories
if (isset($_POST['fix']) || isset($_POST['create_dirs'])) {
    echo "<h2>Creating Directories</h2>";
    foreach ([$storageAppPublicPath, $profilePhotosPath, $publicStoragePath, $publicProfilePhotosPath] as $dir) {
        if (!file_exists($dir)) {
            if (@mkdir($dir, 0777, true)) {
                echo "<p style='color:green;'>✅ Created directory: $dir</p>";
            } else {
                echo "<p style='color:red;'>❌ Failed to create directory: $dir</p>";
                $error = error_get_last();
                echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
            }
        } else {
            echo "<p style='color:green;'>✅ Directory exists: $dir</p>";
        }
        
        // Force permissions
        @chmod($dir, 0777);
        echo "<p>Set permissions to 0777 for: $dir</p>";
    }
}

// Try to connect to database and fix the issue for all users
if (isset($_POST['fix']) && $dbUsername && $dbName) {
    try {
        echo "<h2>Connecting to Database</h2>";
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p style='color:green;'>✅ Connected to database: $dbName</p>";
        
        // Get all users who have profile_photo_path = 0
        $stmt = $conn->prepare("SELECT user_id, name, profile_photo_path FROM users WHERE profile_photo_path = '0' OR profile_photo_path = 0");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h2>Users With Profile Photo Issues</h2>";
        echo "<p>Found " . count($users) . " users with profile_photo_path = 0</p>";
        
        if (count($users) > 0) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>User ID</th><th>Name</th><th>Current Photo Path</th></tr>";
            
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['user_id']) . "</td>";
                echo "<td>" . htmlspecialchars($user['name']) . "</td>";
                echo "<td>" . htmlspecialchars($user['profile_photo_path']) . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            
            // Fix all users
            echo "<h2>Fixing User Photos</h2>";
            
            foreach ($users as $user) {
                // Create a personalized profile photo for each user
                $userId = $user['user_id'];
                $name = $user['name'];
                $initials = strtoupper(substr($name, 0, 1)); // Get first initial
                
                // Generate a unique filename
                $filename = 'user-' . $userId . '-' . time() . '.svg';
                $relativePath = 'profile-photos/' . $filename;
                
                // Create an SVG profile photo with the user's initial
                $photoContent = <<<EOT
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <rect width="200" height="200" fill="#3498db"/>
  <circle cx="100" cy="70" r="50" fill="#ffffff"/>
  <circle cx="100" cy="180" r="80" fill="#ffffff"/>
  <text x="100" y="100" font-family="Arial" font-size="72" text-anchor="middle" fill="#ffffff">$initials</text>
</svg>
EOT;
                
                // Save to storage path
                $storageFilePath = $profilePhotosPath . '/' . $filename;
                if (file_put_contents($storageFilePath, $photoContent)) {
                    echo "<p style='color:green;'>✅ Created profile photo for user #$userId: $storageFilePath</p>";
                    chmod($storageFilePath, 0644);
                } else {
                    echo "<p style='color:red;'>❌ Failed to create profile photo for user #$userId</p>";
                    $error = error_get_last();
                    echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
                    continue; // Skip to next user
                }
                
                // Copy to public storage path
                $publicStorageFilePath = $publicProfilePhotosPath . '/' . $filename;
                if (copy($storageFilePath, $publicStorageFilePath)) {
                    echo "<p style='color:green;'>✅ Copied profile photo to public storage for user #$userId</p>";
                    chmod($publicStorageFilePath, 0644);
                } else {
                    echo "<p style='color:red;'>❌ Failed to copy profile photo to public storage for user #$userId</p>";
                    $error = error_get_last();
                    echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
                }
                
                // Update database
                $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE user_id = :user_id");
                $updateStmt->bindParam(':path', $relativePath);
                $updateStmt->bindParam(':user_id', $userId);
                
                if ($updateStmt->execute()) {
                    echo "<p style='color:green;'>✅ Updated database record for user #$userId</p>";
                    echo "<p>New profile_photo_path: $relativePath</p>";
                    
                    // Add a separator between users
                    echo "<hr>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to update database for user #$userId</p>";
                    echo "<hr>";
                }
            }
            
            // Verify all changes
            echo "<h2>Verification</h2>";
            $verifyStmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE profile_photo_path = '0' OR profile_photo_path = 0");
            $verifyStmt->execute();
            $result = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] === 0 || $result['count'] === '0') {
                echo "<p style='color:green;'>✅ All users have been updated successfully! No more users with profile_photo_path = 0</p>";
            } else {
                echo "<p style='color:orange;'>⚠️ There are still " . $result['count'] . " users with profile_photo_path = 0</p>";
            }
        } else {
            echo "<p style='color:green;'>✅ No users found with profile_photo_path = 0. All users seem to have valid profile photos.</p>";
        }
        
        echo "<h2>Next Steps</h2>";
        echo "<ol>";
        echo "<li>Clear your browser cache</li>";
        echo "<li>Try accessing the admin page with cache busting: <a href='/admin/users?nocache=" . time() . "' target='_blank'>Open admin users page</a></li>";
        echo "</ol>";
        
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

// Display form
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix ALL User Profile Photos</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; }
        .btn { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="card">
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
            <button type="submit" name="db_submit" class="btn">Update Credentials</button>
        </form>
    </div>
    
    <div class="card">
        <h2>Fix ALL User Photos</h2>
        <p>This will create new profile photos for all users with missing or invalid profile photos and update the database.</p>
        <form method="post" action="">
            <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>">
            <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>">
            <input type="hidden" name="db_username" value="<?php echo htmlspecialchars($dbUsername); ?>">
            <input type="hidden" name="db_password" value="<?php echo htmlspecialchars($dbPassword); ?>">
            <button type="submit" name="fix" class="btn">Fix ALL User Photos</button>
        </form>
    </div>
    
    <div class="card">
        <h2>Create Directories Only</h2>
        <p>This will create the necessary directories without changing the database.</p>
        <form method="post" action="">
            <button type="submit" name="create_dirs" class="btn">Create Directories</button>
        </form>
    </div>
</body>
</html>
