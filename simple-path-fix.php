<?php
// Simple script to fix profile photo paths in the database
// This ensures profile photos are in the public/profile-photos/ directory

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Simple Profile Photo Path Fix</h1>";

// Step 1: Check if the public/profile-photos directory exists
if (file_exists('public/profile-photos')) {
    echo "<p>✓ Directory exists: public/profile-photos</p>";
} else {
    if (mkdir('public/profile-photos', 0755, true)) {
        echo "<p style='color:green;'>✅ Created directory: public/profile-photos</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create directory: public/profile-photos</p>";
    }
}

// Step 2: Check for storage/app/public/profile-photos directory
if (file_exists('storage/app/public/profile-photos')) {
    echo "<p>Found storage directory: storage/app/public/profile-photos</p>";
    
    // Copy files from storage to public
    $files = scandir('storage/app/public/profile-photos');
    $copiedCount = 0;
    
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && is_file('storage/app/public/profile-photos/' . $file)) {
            if (copy('storage/app/public/profile-photos/' . $file, 'public/profile-photos/' . $file)) {
                echo "<p style='color:green;'>✅ Copied: $file</p>";
                $copiedCount++;
            } else {
                echo "<p style='color:red;'>❌ Failed to copy: $file</p>";
            }
        }
    }
    
    echo "<p>Copied $copiedCount files from storage to public directory</p>";
} else {
    echo "<p style='color:orange;'>⚠️ Storage directory not found: storage/app/public/profile-photos</p>";
}

// Step 3: Update database paths
try {
    // Try to connect to the database using environment variables
    require_once 'vendor/autoload.php';
    
    // Load .env file if it exists
    if (file_exists('.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }
    
    // Get database connection info from .env
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $database = $_ENV['DB_DATABASE'] ?? 'ams';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    $dbConnection = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get users with profile photos
    $users = $dbConnection->query("SELECT id, name, profile_photo_path FROM users WHERE profile_photo_path IS NOT NULL AND profile_photo_path != ''")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Found " . count($users) . " users with profile photos</p>";
    
    // Update paths in database to use profile-photos/
    $updatedCount = 0;
    foreach ($users as $user) {
        $oldPath = $user['profile_photo_path'];
        
        // Skip if already using correct path
        if (strpos($oldPath, 'profile-photos/') === 0) {
            echo "<p>User {$user['name']} already has correct path: $oldPath</p>";
            continue;
        }
        
        // Extract filename from path
        $filename = basename($oldPath);
        $newPath = 'profile-photos/' . $filename;
        
        // Update database
        $stmt = $dbConnection->prepare("UPDATE users SET profile_photo_path = ? WHERE id = ?");
        if ($stmt->execute([$newPath, $user['id']])) {
            echo "<p style='color:green;'>✅ Updated path for user {$user['name']}: $oldPath → $newPath</p>";
            $updatedCount++;
        } else {
            echo "<p style='color:red;'>❌ Failed to update path for user {$user['name']}</p>";
        }
    }
    
    echo "<p>Updated paths for $updatedCount users</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<h2>Fix Complete!</h2>";
echo "<p>The profile photo paths have been updated to use the public/profile-photos/ directory.</p>";
echo "<p><a href='/admin/users' style='display: inline-block; padding: 10px 15px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>Go to Users Page</a></p>";
?> 