^<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Emergency Admin Photo Fix</h1>";
echo "<p>This script will create a profile photo for the admin user and directly update the database.</p>";

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

// Target user ID - admin user
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 110001;

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

// Try to connect to database and fix the issue
if (isset($_POST['fix']) && $dbUsername && $dbName) {
    try {
        echo "<h2>Connecting to Database</h2>";
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p style='color:green;'>✅ Connected to database: $dbName</p>";
        
        // Create a profile photo
        echo "<h2>Creating Profile Photo</h2>";
        $photoContent = <<<'EOT'
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <rect width="200" height="200" fill="#e74c3c"/>
  <circle cx="100" cy="70" r="50" fill="#ffffff"/>
  <circle cx="100" cy="180" r="80" fill="#ffffff"/>
  <text x="100" y="100" font-family="Arial" font-size="24" text-anchor="middle" fill="#ffffff">ADMIN</text>
</svg>
EOT;

        // Generate a unique filename
        $filename = 'admin-' . time() . '.svg';
        $relativePath = 'profile-photos/' . $filename;
        
        // Save to storage path
        $storageFilePath = $profilePhotosPath . '/' . $filename;
        if (file_put_contents($storageFilePath, $photoContent)) {
            echo "<p style='color:green;'>✅ Created profile photo in storage: $storageFilePath</p>";
            chmod($storageFilePath, 0644);
        } else {
            echo "<p style='color:red;'>❌ Failed to create profile photo in storage</p>";
            $error = error_get_last();
            echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
        
        // Copy to public storage path
        $publicStorageFilePath = $publicProfilePhotosPath . '/' . $filename;
        if (copy($storageFilePath, $publicStorageFilePath)) {
            echo "<p style='color:green;'>✅ Copied profile photo to public storage: $publicStorageFilePath</p>";
            chmod($publicStorageFilePath, 0644);
        } else {
            echo "<p style='color:red;'>❌ Failed to copy profile photo to public storage</p>";
            $error = error_get_last();
            echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
        
        // Update database
        echo "<h2>Updating Database</h2>";
        $stmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE user_id = :user_id");
        $stmt->bindParam(':path', $relativePath);
        $stmt->bindParam(':user_id', $userId);
        
        if ($stmt->execute()) {
            echo "<p style='color:green;'>✅ Updated database record for user #$userId</p>";
            echo "<p>New profile_photo_path: $relativePath</p>";
            
            // Verify the change
            $verifyStmt = $conn->prepare("SELECT profile_photo_path FROM users WHERE user_id = :user_id");
            $verifyStmt->bindParam(':user_id', $userId);
            $verifyStmt->execute();
            $userData = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($userData && $userData['profile_photo_path'] === $relativePath) {
                echo "<p style='color:green;'>✅ Verified database update - path correctly stored as: " . $userData['profile_photo_path'] . "</p>";
            } else {
                echo "<p style='color:red;'>❌ Database verification failed</p>";
                echo "<p>Stored value: " . ($userData ? $userData['profile_photo_path'] : 'No data found') . "</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Failed to update database</p>";
        }
        
        echo "<h2>Next Steps</h2>";
        echo "<p>If the database was updated successfully:</p>";
        echo "<ol>";
        echo "<li>Clear your browser cache</li>";
        echo "<li>Try accessing the admin page with cache busting: <a href='/admin/users/$userId/edit?nocache=" . time() . "' target='_blank'>Open admin edit page</a></li>";
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
    <title>Emergency Admin Photo Fix</title>
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
        <h2>Fix Admin Photo</h2>
        <p>This will create a new profile photo for the admin user and update the database.</p>
        <form method="post" action="">
            <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>">
            <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>">
            <input type="hidden" name="db_username" value="<?php echo htmlspecialchars($dbUsername); ?>">
            <input type="hidden" name="db_password" value="<?php echo htmlspecialchars($dbPassword); ?>">
            <button type="submit" name="fix" class="btn">Fix Admin Photo</button>
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
