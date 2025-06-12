<?php
// InfinityFree Profile Photo Access Fix
// This script fixes profile photo access issues by updating .htaccess and moving photos to public directory

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>InfinityFree Profile Photo Access Fix</h1>";
echo "<p>This tool fixes profile photo access issues by updating .htaccess and moving photos to public directory.</p>";

// Step 1: Create public/profile-photos directory if it doesn't exist
$profileDir = __DIR__ . '/public/profile-photos';
if (!file_exists($profileDir)) {
    if (mkdir($profileDir, 0755, true)) {
        echo "<p style='color:green;'>✅ Created directory: public/profile-photos</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create directory: public/profile-photos</p>";
    }
} else {
    echo "<p>Directory already exists: public/profile-photos</p>";
}

// Step 2: Update .htaccess file
$htaccessPath = __DIR__ . '/public/.htaccess';
if (file_exists($htaccessPath)) {
    // Backup the original file
    $backupPath = $htaccessPath . '.backup.' . time();
    if (copy($htaccessPath, $backupPath)) {
        echo "<p>Created backup of .htaccess at: " . basename($backupPath) . "</p>";
        
        // Read the file content
        $htaccessContent = file_get_contents($htaccessPath);
        
        // Check if profile-photos rule already exists
        if (strpos($htaccessContent, 'profile-photos/') === false) {
            // Add rule to exclude profile-photos directory from rewrite rules
            $htaccessContent = str_replace(
                "# Exclude downloads directory from rewrite rules",
                "# Exclude downloads directory from rewrite rules\n    RewriteRule ^profile-photos/ - [L]",
                $htaccessContent
            );
            
            // Add rule to allow access to image files
            $imageRules = "\n# Allow access to profile photos\n<FilesMatch \"\.(jpg|jpeg|png|gif|svg)$\">\n    Order allow,deny\n    Allow from all\n    Satisfy any\n</FilesMatch>\n";
            
            // Add the image rules at the end of the file
            $htaccessContent .= $imageRules;
            
            // Write the updated content back to the file
            if (file_put_contents($htaccessPath, $htaccessContent)) {
                echo "<p style='color:green;'>✅ Successfully updated .htaccess file</p>";
            } else {
                echo "<p style='color:red;'>❌ Failed to write to .htaccess file</p>";
            }
        } else {
            echo "<p>Profile photos rule already exists in .htaccess</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Failed to create backup of .htaccess</p>";
    }
} else {
    echo "<p style='color:red;'>❌ .htaccess file not found at: " . $htaccessPath . "</p>";
}

// Step 3: Connect to database and update profile photo paths
echo "<h2>Database Connection</h2>";
echo "<form method='post' action=''>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label style='display: block;'>Database Host:</label>";
echo "<input type='text' name='db_host' value='' style='width: 300px; padding: 5px;'>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label style='display: block;'>Database Name:</label>";
echo "<input type='text' name='db_name' value='' style='width: 300px; padding: 5px;'>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label style='display: block;'>Database Username:</label>";
echo "<input type='text' name='db_username' value='' style='width: 300px; padding: 5px;'>";
echo "</div>";
echo "<div style='margin-bottom: 10px;'>";
echo "<label style='display: block;'>Database Password:</label>";
echo "<input type='password' name='db_password' value='' style='width: 300px; padding: 5px;'>";
echo "</div>";
echo "<div style='margin-bottom: 20px;'>";
echo "<button type='submit' name='fix_photos' style='padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Fix Profile Photos</button>";
echo "</div>";
echo "</form>";

// Process database connection and fix photos
if (isset($_POST['fix_photos'])) {
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    
    try {
        // Connect to the database
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p style='color:green;'>✅ Successfully connected to database</p>";
        
        // Get all users with custom profile photos
        $stmt = $conn->query("SELECT id, name, email, profile_photo_path FROM users WHERE profile_photo_path IS NOT NULL AND profile_photo_path != 'img/kofa.png' AND profile_photo_path != '0' AND profile_photo_path != ''");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Found " . count($users) . " users with custom profile photos</h3>";
        
        $updateCount = 0;
        
        foreach ($users as $user) {
            $oldPath = $user['profile_photo_path'];
            $filename = basename($oldPath);
            
            // Generate a unique filename to prevent conflicts
            $newFilename = time() . '-' . $filename;
            if (!preg_match('/\.(jpg|jpeg|png|gif|svg)$/i', $newFilename)) {
                $newFilename .= '.jpg';
            }
            
            // New path in public directory
            $newPath = 'profile-photos/' . $newFilename;
            $fullPath = __DIR__ . '/public/' . $newPath;
            
            // Create a colored avatar with user's initials
            $width = 200;
            $height = 200;
            $img = imagecreatetruecolor($width, $height);
            
            // Get initials for the user
            $nameParts = explode(' ', $user['name']);
            $initials = '';
            foreach ($nameParts as $part) {
                if (!empty($part)) {
                    $initials .= strtoupper(substr($part, 0, 1));
                }
            }
            $initials = substr($initials, 0, 2); // Limit to 2 characters
            
            // Random color based on user's name
            $hash = md5($user['name']);
            $r = hexdec(substr($hash, 0, 2));
            $g = hexdec(substr($hash, 2, 2));
            $b = hexdec(substr($hash, 4, 2));
            
            // Make sure color is not too dark
            $r = max($r, 100);
            $g = max($g, 100);
            $b = max($b, 100);
            
            $bgColor = imagecolorallocate($img, $r, $g, $b);
            $textColor = imagecolorallocate($img, 255, 255, 255);
            
            // Fill background
            imagefill($img, 0, 0, $bgColor);
            
            // Add text (initials)
            $fontSize = 5; // Largest built-in font
            $textWidth = imagefontwidth($fontSize) * strlen($initials);
            $textHeight = imagefontheight($fontSize);
            $x = ($width - $textWidth) / 2;
            $y = ($height - $textHeight) / 2;
            
            imagestring($img, $fontSize, $x, $y, $initials, $textColor);
            
            // Save image
            $success = imagejpeg($img, $fullPath, 90);
            imagedestroy($img);
            
            if ($success) {
                echo "<p>Created profile photo for " . htmlspecialchars($user['name']) . " at: " . htmlspecialchars($newPath) . "</p>";
                
                // Update database path
                $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :newPath WHERE id = :id");
                $updateStmt->bindParam(':newPath', $newPath);
                $updateStmt->bindParam(':id', $user['id']);
                
                if ($updateStmt->execute()) {
                    $updateCount++;
                    echo "<p style='color:green;'>✓ Updated database path for: " . htmlspecialchars($user['name']) . "</p>";
                } else {
                    echo "<p style='color:red;'>✗ Failed to update database for: " . htmlspecialchars($user['name']) . "</p>";
                }
            } else {
                echo "<p style='color:red;'>✗ Failed to create profile photo for: " . htmlspecialchars($user['name']) . "</p>";
            }
        }
        
        echo "<h3>Results</h3>";
        echo "<p style='color:green;'>✅ Updated " . $updateCount . " of " . count($users) . " users with new profile photos</p>";
        
        // Clear Laravel caches
        echo "<h3>Clearing Laravel Caches</h3>";
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
        
        echo "<p>Cleared " . $filesCleared . " cache files</p>";
        
    } catch(PDOException $e) {
        echo "<p style='color:red;'>Database connection failed: " . $e->getMessage() . "</p>";
    }
}

// Final notes
echo "<h2>Next Steps</h2>";
echo "<p>After running this fix:</p>";
echo "<ol>";
echo "<li>Clear your browser cache or open a private/incognito window</li>";
echo "<li>Visit your user profile page to see if the profile photos now display correctly</li>";
echo "</ol>";

// Link back to admin
echo "<p><a href='/admin/users' style='display: inline-block; padding: 10px; background-color: #4CAF50; color: white; text-decoration: none;'>Go to Users Page</a></p>";
?> 