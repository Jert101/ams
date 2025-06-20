^<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix Profile Photo for InfinityFree</h1>";

// Get database credentials from the Laravel .env file
$envFile = __DIR__ . '/.env';
$dbUsername = '';
$dbPassword = '';
$dbName = '';
$dbHost = '';

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
    echo "<p style='color:orange;'>⚠️ .env file not found. You'll need to enter database credentials manually.</p>";
}

// Allow manual override of database credentials
if (isset($_POST['db_submit'])) {
    $dbHost = $_POST['db_host'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    $dbName = $_POST['db_name'];
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

// Create necessary directories with aggressive permissions
if (isset($_POST['create_dirs'])) {
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

// Test database connection
$dbConnected = false;
$userData = null;

if ($dbUsername && $dbName) {
    try {
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbConnected = true;
        
        // Get user data
        $stmt = $conn->prepare("SELECT id, name, email, profile_photo_path FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userData) {
            echo "<p style='color:green;'>✅ Connected to database and found user #$userId</p>";
        } else {
            echo "<p style='color:orange;'>⚠️ Connected to database but user #$userId not found</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    }
}

// Fix User model
if (isset($_POST['fix_model'])) {
    $userModelPath = $basePath . '/app/Models/User.php';
    if (file_exists($userModelPath)) {
        // Create a backup
        $backupPath = $userModelPath . '.backup.' . time();
        if (copy($userModelPath, $backupPath)) {
            echo "<p style='color:green;'>✅ Created backup of User model at $backupPath</p>";
            
            $userModelContent = file_get_contents($userModelPath);
            
            // Fix the getProfilePhotoUrlAttribute method
            $pattern = '/public\s+function\s+getProfilePhotoUrlAttribute\s*\(\)\s*\{.*?\}/s';
            $replacement = 'public function getProfilePhotoUrlAttribute()
    {
        // Direct override for emergency fix
        $photoPath = $this->profile_photo_path;
        
        // Handle empty, null, or invalid profile photo paths
        if (empty($photoPath) || $photoPath === \'0\' || $photoPath === 0 || $photoPath === \'null\' || $photoPath === \'NULL\') {
            return asset(\'kofa.png\');
        }
        
        // Check if it\'s the default photo
        if ($photoPath === \'kofa.png\') {
            return asset(\'kofa.png\');
        }
        
        // Check if the file exists directly in public directory first
        if (file_exists(public_path($photoPath))) {
            return asset($photoPath);
        }
        
        // Then check if it exists in storage/public
        if (file_exists(public_path(\'storage/\' . $photoPath))) {
            return asset(\'storage/\' . $photoPath);
        }
        
        // Default fallback
        return asset(\'kofa.png\');
    }';
            
            if (preg_match($pattern, $userModelContent)) {
                $updatedContent = preg_replace($pattern, $replacement, $userModelContent);
                if (file_put_contents($userModelPath, $updatedContent)) {
                    echo "<p style='color:green;'>✅ Updated User model getProfilePhotoUrlAttribute method</p>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to update User model</p>";
                }
            } else {
                echo "<p style='color:red;'>❌ Could not find getProfilePhotoUrlAttribute method in User model</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Failed to create backup of User model</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ User model not found at $userModelPath</p>";
    }
}

// Fix view
if (isset($_POST['fix_view'])) {
    $viewPath = $basePath . '/resources/views/admin/users/edit.blade.php';
    if (file_exists($viewPath)) {
        // Create a backup
        $backupPath = $viewPath . '.backup.' . time();
        if (copy($viewPath, $backupPath)) {
            echo "<p style='color:green;'>✅ Created backup of view at $backupPath</p>";
            
            $viewContent = file_get_contents($viewPath);
            
            // Find the image tag section
            $pattern = '/<div class="h-24 w-24 rounded-full border-4 border-red-200 shadow-md overflow-hidden">.*?<\/div>/s';
            $replacement = '<div class="h-24 w-24 rounded-full border-4 border-red-200 shadow-md overflow-hidden">
                            @php
                                $photoPath = $user->profile_photo_path;
                                $photoUrl = empty($photoPath) ? asset(\'kofa.png\') : 
                                            ($photoPath === \'kofa.png\' ? asset(\'kofa.png\') : 
                                            asset(\'storage/\' . $photoPath));
                                // Add cache busting parameter
                                $photoUrl = $photoUrl . \'?v=\' . time();
                            @endphp
                            <img src="{{ $photoUrl }}" alt="{{ $user->name }}\'s profile photo" class="h-full w-full object-cover">
                        </div>';
            
            $updatedContent = preg_replace($pattern, $replacement, $viewContent);
            if ($updatedContent !== $viewContent) {
                if (file_put_contents($viewPath, $updatedContent)) {
                    echo "<p style='color:green;'>✅ Updated view to use direct asset path with cache busting</p>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to update view</p>";
                }
            } else {
                echo "<p style='color:orange;'>⚠️ No changes made to view (pattern not found)</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ Failed to create backup of view</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ View not found at $viewPath</p>";
    }
}

// Create a simple profile photo directly in the public directory
if (isset($_POST['create_photo'])) {
    $photoContent = <<<'EOT'
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <rect width="200" height="200" fill="#e74c3c"/>
  <circle cx="100" cy="70" r="50" fill="#ffffff"/>
  <circle cx="100" cy="180" r="80" fill="#ffffff"/>
  <text x="100" y="100" font-family="Arial" font-size="24" text-anchor="middle" fill="#ffffff">ADMIN</text>
</svg>
EOT;

    // Create a new profile photo directly in the public directory
    $filename = 'admin-' . time() . '.svg';
    $filePath = $publicPath . '/' . $filename;

    if (file_put_contents($filePath, $photoContent)) {
        echo "<p style='color:green;'>✅ Created profile photo at: $filePath</p>";
        chmod($filePath, 0644);
        
        // Create a copy in the storage directory
        $storageFilePath = $profilePhotosPath . '/' . $filename;
        if (copy($filePath, $storageFilePath)) {
            echo "<p style='color:green;'>✅ Copied profile photo to storage: $storageFilePath</p>";
            chmod($storageFilePath, 0644);
        }
        
        // Create a copy in the public storage directory
        $publicStorageFilePath = $publicProfilePhotosPath . '/' . $filename;
        if (copy($filePath, $publicStorageFilePath)) {
            echo "<p style='color:green;'>✅ Copied profile photo to public storage: $publicStorageFilePath</p>";
            chmod($publicStorageFilePath, 0644);
        }
        
        // Update database if connected
        if ($dbConnected) {
            try {
                // Set the path relative to storage/app/public
                $relativePath = 'profile-photos/' . $filename;
                
                $stmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :user_id");
                $stmt->bindParam(':path', $relativePath);
                $stmt->bindParam(':user_id', $userId);
                
                if ($stmt->execute()) {
                    echo "<p style='color:green;'>✅ Database updated successfully</p>";
                    echo "<p>Updated profile_photo_path to: $relativePath</p>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to update database</p>";
                }
            } catch(PDOException $e) {
                echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
            }
        }
        
        // Show preview
        echo "<h3>New Profile Photo</h3>";
        echo "<p>Direct URL: /$filename</p>";
        echo "<p>Storage URL: /storage/profile-photos/$filename</p>";
        echo "<img src='/$filename' style='max-width: 200px; border: 1px solid #ddd;'>";
        echo "<p>If the image doesn't appear, try <a href='/$filename' target='_blank'>this direct link</a>.</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create profile photo</p>";
        $error = error_get_last();
        echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
    }
}

// Clear cache
if (isset($_POST['clear_cache'])) {
    $bootstrapCachePath = $basePath . '/bootstrap/cache';
    $storageCachePath = $basePath . '/storage/framework/cache';
    $storageViewsPath = $basePath . '/storage/framework/views';
    
    // Clear bootstrap cache
    $bootstrapCacheFiles = glob($bootstrapCachePath . '/*.php');
    $count = 0;
    foreach ($bootstrapCacheFiles as $file) {
        if (@unlink($file)) {
            echo "<p style='color:green;'>✅ Deleted cache file: " . basename($file) . "</p>";
            $count++;
        }
    }
    echo "<p>Deleted $count bootstrap cache files</p>";
    
    // Clear framework cache
    $frameworkCacheFiles = glob($storageCachePath . '/data/*');
    $count = 0;
    foreach ($frameworkCacheFiles as $file) {
        if (is_file($file) && @unlink($file)) {
            echo "<p style='color:green;'>✅ Deleted cache file: " . basename($file) . "</p>";
            $count++;
        }
    }
    echo "<p>Deleted $count framework cache files</p>";
    
    // Clear compiled views
    $viewFiles = glob($storageViewsPath . '/*');
    $count = 0;
    foreach ($viewFiles as $file) {
        if (is_file($file) && @unlink($file)) {
            echo "<p style='color:green;'>✅ Deleted view file: " . basename($file) . "</p>";
            $count++;
        }
    }
    echo "<p>Deleted $count compiled view files</p>";
}

// Reset to default photo
if (isset($_POST['reset_photo']) && $dbConnected) {
    try {
        $defaultPhoto = 'kofa.png';
        $stmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :user_id");
        $stmt->bindParam(':path', $defaultPhoto);
        $stmt->bindParam(':user_id', $userId);
        
        if ($stmt->execute()) {
            echo "<p style='color:green;'>✅ Reset profile photo to default (kofa.png)</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to reset profile photo</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

// Display current user info
if ($userData) {
    echo "<h2>Current User Information</h2>";
    echo "<p>ID: " . $userData['id'] . "</p>";
    echo "<p>Name: " . $userData['name'] . "</p>";
    echo "<p>Email: " . $userData['email'] . "</p>";
    echo "<p>Profile Photo Path: " . ($userData['profile_photo_path'] ?? '<em>None</em>') . "</p>";
    
    // Check profile photo
    if (!empty($userData['profile_photo_path'])) {
        $photoPath = $userData['profile_photo_path'];
        
        // Check if file exists in storage
        $storageFilePath = $storageAppPublicPath . '/' . $photoPath;
        $storageFileExists = file_exists($storageFilePath);
        echo "<p>Storage file exists: " . ($storageFileExists ? "✅ Yes" : "❌ No") . "</p>";
        echo "<p>Storage path: $storageFilePath</p>";
        
        // Check if file exists in public storage
        $publicFilePath = $publicStoragePath . '/' . $photoPath;
        $publicFileExists = file_exists($publicFilePath);
        echo "<p>Public storage file exists: " . ($publicFileExists ? "✅ Yes" : "❌ No") . "</p>";
        echo "<p>Public storage path: $publicFilePath</p>";
        
        // Check if file exists directly in public
        $directPublicPath = $publicPath . '/' . basename($photoPath);
        $directPublicExists = file_exists($directPublicPath);
        echo "<p>Direct public file exists: " . ($directPublicExists ? "✅ Yes" : "❌ No") . "</p>";
        echo "<p>Direct public path: $directPublicPath</p>";
    }
}

// HTML Form
echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Fix Profile Photo for InfinityFree</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 800px; margin: 0 auto; }
        h1 { color: #e74c3c; }
        h2 { color: #e74c3c; margin-top: 30px; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        .btn { background-color: #e74c3c; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        .btn:hover { background-color: #c0392b; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<h2>Actions</h2>

<div class="card">
    <h3>1. Database Connection</h3>
    <form method="post" action="">
        <div class="form-group">
            <label for="db_host">Database Host:</label>
            <input type="text" id="db_host" name="db_host" value="{$dbHost}">
        </div>
        <div class="form-group">
            <label for="db_name">Database Name:</label>
            <input type="text" id="db_name" name="db_name" value="{$dbName}">
        </div>
        <div class="form-group">
            <label for="db_username">Database Username:</label>
            <input type="text" id="db_username" name="db_username" value="{$dbUsername}">
        </div>
        <div class="form-group">
            <label for="db_password">Database Password:</label>
            <input type="password" id="db_password" name="db_password" value="{$dbPassword}">
        </div>
        <button type="submit" name="db_submit" class="btn">Update Database Credentials</button>
    </form>
</div>

<div class="card">
    <h3>2. Create Directories</h3>
    <p>Create necessary directories for profile photos with proper permissions.</p>
    <form method="post" action="">
        <button type="submit" name="create_dirs" class="btn">Create Directories</button>
    </form>
</div>

<div class="card">
    <h3>3. Fix User Model</h3>
    <p>Update the User model to properly handle profile photos.</p>
    <form method="post" action="">
        <button type="submit" name="fix_model" class="btn">Fix User Model</button>
    </form>
</div>

<div class="card">
    <h3>4. Fix View</h3>
    <p>Update the view to display profile photos with cache busting.</p>
    <form method="post" action="">
        <button type="submit" name="fix_view" class="btn">Fix View</button>
    </form>
</div>

<div class="card">
    <h3>5. Create New Profile Photo</h3>
    <p>Create a new profile photo and update the database.</p>
    <form method="post" action="">
        <button type="submit" name="create_photo" class="btn">Create New Photo</button>
    </form>
</div>

<div class="card">
    <h3>6. Clear Cache</h3>
    <p>Clear Laravel cache files.</p>
    <form method="post" action="">
        <button type="submit" name="clear_cache" class="btn">Clear Cache</button>
    </form>
</div>

<div class="card">
    <h3>7. Reset to Default Photo</h3>
    <p>Reset profile photo to default (kofa.png).</p>
    <form method="post" action="">
        <button type="submit" name="reset_photo" class="btn">Reset to Default</button>
    </form>
</div>

<h2>Browser Cache</h2>
<p>Your browser might be caching the old profile photo. Try clearing your browser cache or opening the site in a private/incognito window.</p>
<p>You can also try adding a random query parameter to force a refresh:</p>
<p><a href="/admin/users/110001/edit?nocache=<?= time() ?>" target="_blank">Open admin edit page with cache busting</a></p>

<h2>Recommended Steps</h2>
<ol>
    <li>Update database credentials if needed</li>
    <li>Create directories</li>
    <li>Fix User model</li>
    <li>Fix view</li>
    <li>Create new profile photo</li>
    <li>Clear cache</li>
    <li>Try accessing the admin page with cache busting</li>
</ol>

</body>
</html>
HTML;
