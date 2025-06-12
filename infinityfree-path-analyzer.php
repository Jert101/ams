<?php
// Profile Path Analyzer for InfinityFree hosting
// This script helps analyze profile photo paths to find what's causing display issues

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>InfinityFree Profile Path Analyzer</h1>";
echo "<p>This tool analyzes profile photo paths to identify why custom profiles aren't displaying.</p>";

// STEP 1: Get database credentials
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
echo "<button type='submit' name='analyze' style='padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Analyze Paths</button>";
echo "</div>";
echo "</form>";

// Process database connection and analysis
if (isset($_POST['analyze'])) {
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    
    try {
        // Connect to the database
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p style='color:green;'>✅ Successfully connected to database</p>";
        
        // Get the User model code
        $userModelPath = __DIR__ . '/app/Models/User.php';
        if (file_exists($userModelPath)) {
            echo "<h2>User Model Analysis</h2>";
            $modelContent = file_get_contents($userModelPath);
            
            // Extract the getProfilePhotoUrlAttribute method
            preg_match('/public\s+function\s+getProfilePhotoUrlAttribute\s*\(\)\s*{(.*?)}/s', $modelContent, $methodMatches);
            
            if (!empty($methodMatches)) {
                echo "<p>Found getProfilePhotoUrlAttribute method:</p>";
                echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto;'>";
                echo htmlspecialchars("public function getProfilePhotoUrlAttribute() {" . $methodMatches[1] . "}");
                echo "</pre>";
                
                // Analyze method for potential issues
                $methodCode = $methodMatches[1];
                $issues = [];
                
                if (strpos($methodCode, 'storage_path') !== false) {
                    $issues[] = "Uses storage_path() which might not work correctly on InfinityFree hosting";
                }
                
                if (strpos($methodCode, 'public_path') !== false) {
                    $issues[] = "Uses public_path() which might not work correctly on InfinityFree hosting";
                }
                
                if (strpos($methodCode, 'asset(') !== false) {
                    $issues[] = "Uses asset() which might be using incorrect base URL on InfinityFree";
                }
                
                if (strpos($methodCode, 'url(') !== false) {
                    $issues[] = "Uses url() which might be using incorrect base URL on InfinityFree";
                }
                
                if (strpos($methodCode, 'file_exists') !== false) {
                    $issues[] = "Uses file_exists() which checks the server filesystem, not the web-accessible path";
                }
                
                if (!empty($issues)) {
                    echo "<p style='color:orange;'>⚠️ Potential issues in the getProfilePhotoUrlAttribute method:</p>";
                    echo "<ul>";
                    foreach ($issues as $issue) {
                        echo "<li style='color:orange;'>" . htmlspecialchars($issue) . "</li>";
                    }
                    echo "</ul>";
                }
            } else {
                echo "<p style='color:red;'>Could not find getProfilePhotoUrlAttribute method in User model</p>";
            }
            
            // Check for defaultProfilePhotoUrl method
            preg_match('/protected\s+function\s+defaultProfilePhotoUrl\s*\(\)\s*{(.*?)}/s', $modelContent, $defaultMethodMatches);
            
            if (!empty($defaultMethodMatches)) {
                echo "<p>Found defaultProfilePhotoUrl method:</p>";
                echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto;'>";
                echo htmlspecialchars("protected function defaultProfilePhotoUrl() {" . $defaultMethodMatches[1] . "}");
                echo "</pre>";
            }
        } else {
            echo "<p style='color:red;'>User model file not found at: " . htmlspecialchars($userModelPath) . "</p>";
        }
        
        // Get all users with custom profile photos
        $stmt = $conn->query("SELECT id, name, email, profile_photo_path FROM users WHERE profile_photo_path IS NOT NULL AND profile_photo_path != 'img/kofa.png' AND profile_photo_path != '0' AND profile_photo_path != ''");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h2>Custom Profile Photo Analysis</h2>";
        echo "<p>Found " . count($users) . " users with custom profile photos</p>";
        
        if (count($users) > 0) {
            echo "<table style='width: 100%; border-collapse: collapse;'>";
            echo "<tr style='background-color: #f2f2f2;'>";
            echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>User</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Photo Path in DB</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Path Analysis</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>File Exists Check</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Direct URL</th>";
            echo "</tr>";
            
            foreach ($users as $user) {
                $photoPath = $user['profile_photo_path'];
                
                // Analyze the path
                $pathAnalysis = [];
                if (strpos($photoPath, 'http://') === 0 || strpos($photoPath, 'https://') === 0) {
                    $pathAnalysis[] = "External URL (might work)";
                } else if (strpos($photoPath, '/storage/') === 0) {
                    $pathAnalysis[] = "Storage path (might not be accessible)";
                } else if (strpos($photoPath, 'storage/') === 0) {
                    $pathAnalysis[] = "Relative storage path (might not be accessible)";
                } else if (strpos($photoPath, '/') === 0) {
                    $pathAnalysis[] = "Absolute path from web root (likely correct)";
                }
                
                if (strpos($photoPath, '.jpg') !== false || strpos($photoPath, '.jpeg') !== false) {
                    $pathAnalysis[] = "JPG format";
                } else if (strpos($photoPath, '.png') !== false) {
                    $pathAnalysis[] = "PNG format";
                } else if (strpos($photoPath, '.svg') !== false) {
                    $pathAnalysis[] = "SVG format";
                } else if (strpos($photoPath, '.gif') !== false) {
                    $pathAnalysis[] = "GIF format";
                } else {
                    $pathAnalysis[] = "Unknown format";
                }
                
                // File exists check (server-side)
                $absolutePath = __DIR__ . '/' . ltrim($photoPath, '/');
                $fileExists = file_exists($absolutePath) ? "Yes" : "No";
                
                // Direct URL (for browser testing)
                $directUrl = '/' . ltrim($photoPath, '/');
                
                echo "<tr>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($user['name']) . " (" . htmlspecialchars($user['email']) . ")</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($photoPath) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . implode("<br>", $pathAnalysis) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($fileExists == "Yes" ? "<span style='color:green;'>Yes</span>" : "<span style='color:red;'>No</span>") . "<br>Checked: " . htmlspecialchars($absolutePath) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'><a href='" . htmlspecialchars($directUrl) . "' target='_blank'>Test Direct URL</a></td>";
                echo "</tr>";
            }
            
            echo "</table>";
            
            // Directory structure analysis
            echo "<h2>Directory Structure Analysis</h2>";
            
            $commonDirs = [
                'storage/app/public' => __DIR__ . '/storage/app/public',
                'public/storage' => __DIR__ . '/public/storage',
                'storage/app/livewire-tmp' => __DIR__ . '/storage/app/livewire-tmp',
                'public/profile-photos' => __DIR__ . '/public/profile-photos',
                'public/img' => __DIR__ . '/public/img',
            ];
            
            echo "<table style='width: 100%; border-collapse: collapse;'>";
            echo "<tr style='background-color: #f2f2f2;'>";
            echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Directory</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Exists</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Readable</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Writable</th>";
            echo "<th style='border: 1px solid #ddd; padding: 8px; text-align: left;'>Files Count</th>";
            echo "</tr>";
            
            foreach ($commonDirs as $dirName => $dirPath) {
                $exists = is_dir($dirPath);
                $readable = $exists ? is_readable($dirPath) : false;
                $writable = $exists ? is_writable($dirPath) : false;
                $filesCount = $exists ? count(glob($dirPath . '/*')) : 0;
                
                echo "<tr>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($dirName) . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($exists ? "<span style='color:green;'>Yes</span>" : "<span style='color:red;'>No</span>") . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($readable ? "<span style='color:green;'>Yes</span>" : "<span style='color:red;'>No</span>") . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . ($writable ? "<span style='color:green;'>Yes</span>" : "<span style='color:red;'>No</span>") . "</td>";
                echo "<td style='border: 1px solid #ddd; padding: 8px;'>" . $filesCount . "</td>";
                echo "</tr>";
            }
            
            echo "</table>";
            
            // Suggestions
            echo "<h2>Suggested Fixes</h2>";
            echo "<p>Based on the analysis, here are some possible solutions:</p>";
            
            echo "<h3>1. Update User Model Path Handling</h3>";
            echo "<p>Update the getProfilePhotoUrlAttribute method to handle paths correctly on InfinityFree:</p>";
            echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto;'>";
            echo htmlspecialchars("public function getProfilePhotoUrlAttribute()
{
    // Direct approach for InfinityFree hosting
    if (empty(\$this->profile_photo_path) || \$this->profile_photo_path === \"0\" || \$this->profile_photo_path === 0) {
        return \"/img/kofa.png?v=\" . time();
    }
    
    // Just return the path directly with a slash at the beginning
    return \"/\" . ltrim(\$this->profile_photo_path, \"/\") . \"?v=\" . time();
}");
            echo "</pre>";
            
            echo "<h3>2. Fix Symlink for storage directory</h3>";
            echo "<p>On InfinityFree, Laravel's typical storage:link symlink might not work. Try these solutions:</p>";
            echo "<ol>";
            echo "<li>Manually copy files from storage/app/public to public/storage</li>";
            echo "<li>Update paths in the database to point directly to accessible locations</li>";
            echo "</ol>";
            
            echo "<h3>3. Update Database Paths</h3>";
            echo "<p>Update profile_photo_path values in the database to use web-accessible paths:</p>";
            
            echo "<form method='post' action=''>";
            echo "<input type='hidden' name='db_host' value='" . htmlspecialchars($dbHost) . "'>";
            echo "<input type='hidden' name='db_name' value='" . htmlspecialchars($dbName) . "'>";
            echo "<input type='hidden' name='db_username' value='" . htmlspecialchars($dbUsername) . "'>";
            echo "<input type='hidden' name='db_password' value='" . htmlspecialchars($dbPassword) . "'>";
            
            // Add more specific fix options
            echo "<p><strong>Fix storage paths:</strong> This will convert paths like 'storage/profile-photos/abc.jpg' to 'public/storage/profile-photos/abc.jpg'</p>";
            echo "<button type='submit' name='fix_storage_paths' style='padding: 10px; background-color: #2196F3; color: white; border: none; cursor: pointer; margin-bottom: 20px;'>Fix Storage Paths</button><br>";
            
            echo "<p><strong>Reset all to default:</strong> This will set all profile photos to the default kofa.png</p>";
            echo "<button type='submit' name='reset_all_paths' style='padding: 10px; background-color: #FF5722; color: white; border: none; cursor: pointer;'>Reset All to Default</button>";
            
            echo "</form>";
            
            // Create test image functionality
            echo "<h3>4. Create Test Image</h3>";
            echo "<p>This will create a test image in various directories to see which one works with your User model:</p>";
            
            echo "<form method='post' action=''>";
            echo "<input type='hidden' name='db_host' value='" . htmlspecialchars($dbHost) . "'>";
            echo "<input type='hidden' name='db_name' value='" . htmlspecialchars($dbName) . "'>";
            echo "<input type='hidden' name='db_username' value='" . htmlspecialchars($dbUsername) . "'>";
            echo "<input type='hidden' name='db_password' value='" . htmlspecialchars($dbPassword) . "'>";
            echo "<button type='submit' name='create_test_images' style='padding: 10px; background-color: #9C27B0; color: white; border: none; cursor: pointer;'>Create Test Images</button>";
            echo "</form>";
        } else {
            echo "<p>No users with custom profile photos found.</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>Database connection failed: " . $e->getMessage() . "</p>";
    }
}

// Fix storage paths
if (isset($_POST['fix_storage_paths'])) {
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    
    try {
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get all users with custom profile photos
        $stmt = $conn->query("SELECT id, name, email, profile_photo_path FROM users WHERE profile_photo_path IS NOT NULL AND profile_photo_path != 'img/kofa.png' AND profile_photo_path != '0' AND profile_photo_path != ''");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $updateCount = 0;
        
        foreach ($users as $user) {
            $oldPath = $user['profile_photo_path'];
            $newPath = $oldPath;
            
            // Fix storage paths
            if (strpos($oldPath, 'storage/') === 0) {
                $newPath = str_replace('storage/', 'public/storage/', $oldPath);
            } else if (strpos($oldPath, '/storage/') === 0) {
                $newPath = str_replace('/storage/', '/public/storage/', $oldPath);
            }
            
            // Make sure paths start with a slash for web access
            if (strpos($newPath, '/') !== 0) {
                $newPath = '/' . $newPath;
            }
            
            // Update the database
            if ($newPath != $oldPath) {
                $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :newPath WHERE id = :id");
                $updateStmt->bindParam(':newPath', $newPath);
                $updateStmt->bindParam(':id', $user['id']);
                
                if ($updateStmt->execute()) {
                    $updateCount++;
                }
            }
        }
        
        echo "<h2>Path Update Results</h2>";
        echo "<p style='color:green;'>Updated " . $updateCount . " user profile paths</p>";
        echo "<p>Refresh the page to see the updated analysis</p>";
        
    } catch(PDOException $e) {
        echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
    }
}

// Reset all paths
if (isset($_POST['reset_all_paths'])) {
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    
    try {
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Reset all users to default
        $defaultPath = 'img/kofa.png';
        $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :defaultPath");
        $updateStmt->bindParam(':defaultPath', $defaultPath);
        
        if ($updateStmt->execute()) {
            $count = $updateStmt->rowCount();
            echo "<h2>Reset Results</h2>";
            echo "<p style='color:green;'>Reset " . $count . " users to the default profile photo</p>";
        } else {
            echo "<p style='color:red;'>Failed to reset users</p>";
        }
        
    } catch(PDOException $e) {
        echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
    }
}

// Create test images
if (isset($_POST['create_test_images'])) {
    $dbHost = $_POST['db_host'];
    $dbName = $_POST['db_name'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    
    echo "<h2>Test Image Creation</h2>";
    
    // Define directories to test
    $testDirs = [
        'img' => __DIR__ . '/img',
        'public/img' => __DIR__ . '/public/img',
        'public/storage' => __DIR__ . '/public/storage',
        'public/profile-photos' => __DIR__ . '/public/profile-photos',
        'storage/app/public' => __DIR__ . '/storage/app/public',
    ];
    
    $successDirs = [];
    
    // Create simple test image
    $imgWidth = 100;
    $imgHeight = 100;
    $testImage = @imagecreatetruecolor($imgWidth, $imgHeight);
    
    if ($testImage) {
        // Fill with a color
        $bgColor = imagecolorallocate($testImage, 255, 100, 100);
        imagefill($testImage, 0, 0, $bgColor);
        
        // Add text
        $textColor = imagecolorallocate($testImage, 255, 255, 255);
        $text = 'TEST';
        imagestring($testImage, 5, 30, 40, $text, $textColor);
        
        // Save to each directory
        echo "<p>Attempting to create test images in multiple directories:</p>";
        echo "<ul>";
        
        foreach ($testDirs as $dirName => $dirPath) {
            // Create directory if it doesn't exist
            if (!file_exists($dirPath)) {
                @mkdir($dirPath, 0755, true);
            }
            
            if (is_dir($dirPath) && is_writable($dirPath)) {
                $testFilename = 'test_profile_' . time() . '.png';
                $testFilePath = $dirPath . '/' . $testFilename;
                
                if (@imagepng($testImage, $testFilePath)) {
                    $successDirs[] = [
                        'dir' => $dirName,
                        'path' => $testFilePath,
                        'web_path' => '/' . ltrim($dirName, '/') . '/' . $testFilename
                    ];
                    echo "<li style='color:green;'>Created test image in " . htmlspecialchars($dirName) . "</li>";
                } else {
                    echo "<li style='color:red;'>Failed to create image in " . htmlspecialchars($dirName) . "</li>";
                }
            } else {
                echo "<li style='color:red;'>Directory " . htmlspecialchars($dirName) . " doesn't exist or is not writable</li>";
            }
        }
        
        echo "</ul>";
        
        // Free memory
        imagedestroy($testImage);
        
        // Update a test user with each path
        if (!empty($successDirs)) {
            try {
                $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Get first admin user
                $stmt = $conn->query("SELECT id, name, email FROM users WHERE role='admin' LIMIT 1");
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($admin) {
                    echo "<p>Using admin user: " . htmlspecialchars($admin['name']) . " for testing profile photos</p>";
                    
                    foreach ($successDirs as $dir) {
                        // Create a copy specifically for this test
                        $testUserPath = $dir['web_path'];
                        
                        // Update the admin user
                        $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id");
                        $updateStmt->bindParam(':path', $testUserPath);
                        $updateStmt->bindParam(':id', $admin['id']);
                        
                        if ($updateStmt->execute()) {
                            echo "<p style='color:green;'>Updated admin user to use test image at: " . htmlspecialchars($testUserPath) . "</p>";
                            echo "<p>Go to your profile page to see if this image displays correctly</p>";
                            
                            // Only update with first successful path
                            break;
                        }
                    }
                } else {
                    echo "<p style='color:orange;'>No admin user found for testing</p>";
                }
                
            } catch(PDOException $e) {
                echo "<p style='color:red;'>Database error: " . $e->getMessage() . "</p>";
            }
        }
    } else {
        echo "<p style='color:red;'>Failed to create test image - GD library might not be enabled</p>";
    }
}

// Final notes
echo "<h2>Next Steps</h2>";
echo "<p>After analyzing your profile photo issue:</p>";
echo "<ol>";
echo "<li>Check the file paths in your database</li>";
echo "<li>Verify the User model's getProfilePhotoUrlAttribute method</li>";
echo "<li>Make sure profile photos are in web-accessible directories</li>";
echo "<li>Try the suggested fixes above</li>";
echo "</ol>";

echo "<p><a href='infinityfree-fix.php' style='display: inline-block; padding: 10px; background-color: #4CAF50; color: white; text-decoration: none;'>Go to Fix Script</a></p>";
?>
