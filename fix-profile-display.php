<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Profile Picture Display Fix</h1>";

// Define paths
$basePath = __DIR__;
$storagePath = $basePath . '/storage';
$storageAppPublicPath = $storagePath . '/app/public';
$profilePhotosPath = $storageAppPublicPath . '/profile-photos';
$publicPath = $basePath . '/public';
$publicStoragePath = $publicPath . '/storage';
$publicProfilePhotosPath = $publicStoragePath . '/profile-photos';

// Database connection parameters - update these with your actual credentials
$servername = "localhost";
$username = "if0_38972693"; // Your InfinityFree username
$password = ""; // You need to fill this with your actual database password
$dbname = "if0_38972693_ams"; // Your database name

echo "<h2>Checking Directory Structure</h2>";

// Check if storage directories exist
$directories = [
    'storage/app/public' => $storageAppPublicPath,
    'storage/app/public/profile-photos' => $profilePhotosPath,
    'public/storage' => $publicStoragePath,
    'public/storage/profile-photos' => $publicProfilePhotosPath,
];

foreach ($directories as $name => $path) {
    echo "<h3>$name</h3>";
    
    if (file_exists($path)) {
        echo "<p style='color:green;'>Directory exists</p>";
        
        // Check permissions
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        echo "<p>Permissions: $perms</p>";
        
        // Check if writable
        if (is_writable($path)) {
            echo "<p style='color:green;'>Directory is writable</p>";
        } else {
            echo "<p style='color:red;'>Directory is NOT writable</p>";
            
            // Try to fix permissions
            if (@chmod($path, 0777)) {
                echo "<p style='color:green;'>Updated permissions to 0777</p>";
            } else {
                echo "<p style='color:red;'>Failed to update permissions</p>";
            }
        }
    } else {
        echo "<p style='color:red;'>Directory does not exist. Creating it...</p>";
        
        if (@mkdir($path, 0777, true)) {
            echo "<p style='color:green;'>Successfully created directory</p>";
        } else {
            echo "<p style='color:red;'>Failed to create directory</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    }
}

// Check database connection and profile photos
echo "<h2>Checking Database for Profile Photos</h2>";

echo "<p>Please update the database password in this script before running it.</p>";
echo "<p>Current settings:</p>";
echo "<ul>";
echo "<li>Server: $servername</li>";
echo "<li>Username: $username</li>";
echo "<li>Database: $dbname</li>";
echo "</ul>";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green;'>Connected to database successfully</p>";
    
    // Query to get users with profile photos
    $stmt = $conn->prepare("SELECT id, user_id, name, email, profile_photo_path FROM users WHERE profile_photo_path IS NOT NULL");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<p>Found " . count($users) . " users with profile photos in the database</p>";
        
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>User ID</th><th>Name</th><th>Email</th><th>Photo Path</th><th>Storage File</th><th>Public File</th><th>Actions</th></tr>";
        
        foreach ($users as $user) {
            $photoPath = $user['profile_photo_path'];
            $storageFilePath = $storageAppPublicPath . '/' . $photoPath;
            $publicFilePath = $publicStoragePath . '/' . $photoPath;
            
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['user_id'] . "</td>";
            echo "<td>" . $user['name'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $photoPath . "</td>";
            
            // Check if file exists in storage
            $storageFileExists = file_exists($storageFilePath);
            echo "<td>" . ($storageFileExists ? "✅ Exists" : "❌ Missing") . "</td>";
            
            // Check if file exists in public
            $publicFileExists = file_exists($publicFilePath);
            echo "<td>" . ($publicFileExists ? "✅ Exists" : "❌ Missing") . "</td>";
            
            // Actions
            echo "<td>";
            if ($storageFileExists && !$publicFileExists) {
                echo "<a href='?copy_file=" . urlencode($photoPath) . "'>Copy to Public</a>";
            } elseif (!$storageFileExists && $publicFileExists) {
                echo "<a href='?copy_back=" . urlencode($photoPath) . "'>Copy to Storage</a>";
            } elseif (!$storageFileExists && !$publicFileExists) {
                echo "File missing in both locations";
            } else {
                echo "Files exist in both locations";
            }
            echo "</td>";
            
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Process copy action if requested
        if (isset($_GET['copy_file'])) {
            $filePath = $_GET['copy_file'];
            $sourceFile = $storageAppPublicPath . '/' . $filePath;
            $destFile = $publicStoragePath . '/' . $filePath;
            
            echo "<h3>Copying File from Storage to Public</h3>";
            echo "<p>Source: $sourceFile</p>";
            echo "<p>Destination: $destFile</p>";
            
            // Make sure the destination directory exists
            $destDir = dirname($destFile);
            if (!file_exists($destDir)) {
                if (mkdir($destDir, 0777, true)) {
                    echo "<p style='color:green;'>Created destination directory</p>";
                } else {
                    echo "<p style='color:red;'>Failed to create destination directory</p>";
                }
            }
            
            // Copy the file
            if (copy($sourceFile, $destFile)) {
                echo "<p style='color:green;'>Successfully copied file</p>";
                echo "<p><a href='fix-profile-display.php'>Back to file list</a></p>";
            } else {
                echo "<p style='color:red;'>Failed to copy file</p>";
                echo "<p>Error: " . error_get_last()['message'] . "</p>";
            }
        }
        
        // Process copy back action if requested
        if (isset($_GET['copy_back'])) {
            $filePath = $_GET['copy_back'];
            $sourceFile = $publicStoragePath . '/' . $filePath;
            $destFile = $storageAppPublicPath . '/' . $filePath;
            
            echo "<h3>Copying File from Public to Storage</h3>";
            echo "<p>Source: $sourceFile</p>";
            echo "<p>Destination: $destFile</p>";
            
            // Make sure the destination directory exists
            $destDir = dirname($destFile);
            if (!file_exists($destDir)) {
                if (mkdir($destDir, 0777, true)) {
                    echo "<p style='color:green;'>Created destination directory</p>";
                } else {
                    echo "<p style='color:red;'>Failed to create destination directory</p>";
                }
            }
            
            // Copy the file
            if (copy($sourceFile, $destFile)) {
                echo "<p style='color:green;'>Successfully copied file</p>";
                echo "<p><a href='fix-profile-display.php'>Back to file list</a></p>";
            } else {
                echo "<p style='color:red;'>Failed to copy file</p>";
                echo "<p>Error: " . error_get_last()['message'] . "</p>";
            }
        }
    } else {
        echo "<p>No users with profile photos found in the database</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color:red;'>Connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please update the database credentials in this file.</p>";
}

// Create a fix for the User model getProfilePhotoUrlAttribute method
echo "<h2>User Model Fix</h2>";

$userModelPath = $basePath . '/app/Models/User.php';
if (file_exists($userModelPath)) {
    echo "<p style='color:green;'>User model exists</p>";
    
    // Create a backup of the User model
    $backupPath = $userModelPath . '.backup.' . time();
    if (copy($userModelPath, $backupPath)) {
        echo "<p style='color:green;'>Created backup of User model at $backupPath</p>";
        
        // Read the User model
        $userModelContent = file_get_contents($userModelPath);
        
        // Replace the getProfilePhotoUrlAttribute method
        $pattern = '/public function getProfilePhotoUrlAttribute\(\)\s*\{.*?\}/s';
        $replacement = 'public function getProfilePhotoUrlAttribute()
    {
        // Handle empty, null, or invalid profile photo paths
        if (empty($this->profile_photo_path) || 
            $this->profile_photo_path === \'0\' || 
            $this->profile_photo_path === 0 || 
            $this->profile_photo_path === \'null\' ||
            $this->profile_photo_path === \'NULL\') {
            return asset(\'kofa.png\');
        }
        
        // Check if it\'s the default photo
        if ($this->profile_photo_path === \'kofa.png\') {
            return asset(\'kofa.png\');
        }
        
        // Check if it\'s a full URL
        if (filter_var($this->profile_photo_path, FILTER_VALIDATE_URL)) {
            return $this->profile_photo_path;
        }
        
        // IMPORTANT: First check if the file exists in public/storage directly
        if (file_exists(public_path(\'storage/\' . $this->profile_photo_path))) {
            return asset(\'storage/\' . $this->profile_photo_path);
        }
        
        // Then check if it exists directly in public directory
        if (file_exists(public_path($this->profile_photo_path))) {
            return asset($this->profile_photo_path);
        }
        
        // Log the issue and return the default
        \\Log::warning(\'Profile photo not found for user: \' . $this->id, [
            \'profile_photo_path\' => $this->profile_photo_path,
            \'checked_paths\' => [
                \'public/storage/\' . $this->profile_photo_path,
                \'public/\' . $this->profile_photo_path
            ]
        ]);
        
        return asset(\'kofa.png\');
    }';
        
        if (preg_match($pattern, $userModelContent)) {
            $fixedUserModel = preg_replace($pattern, $replacement, $userModelContent);
            
            // Write the fixed User model
            if (file_put_contents($userModelPath, $fixedUserModel)) {
                echo "<p style='color:green;'>Successfully updated User model getProfilePhotoUrlAttribute method</p>";
            } else {
                echo "<p style='color:red;'>Failed to update User model</p>";
            }
        } else {
            echo "<p style='color:red;'>Could not find getProfilePhotoUrlAttribute method in User model</p>";
        }
    } else {
        echo "<p style='color:red;'>Failed to create backup of User model</p>";
    }
} else {
    echo "<p style='color:red;'>User model not found at $userModelPath</p>";
}

// Create a quick fix script for copying all profile photos
echo "<h2>Creating Quick Fix Script</h2>";

$quickFixPath = $basePath . '/copy-profile-photos.php';
$quickFixContent = <<<'EOT'
<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Copy All Profile Photos</h1>";

// Define paths
$basePath = __DIR__;
$storagePath = $basePath . '/storage';
$storageAppPublicPath = $storagePath . '/app/public';
$profilePhotosPath = $storageAppPublicPath . '/profile-photos';
$publicPath = $basePath . '/public';
$publicStoragePath = $publicPath . '/storage';
$publicProfilePhotosPath = $publicStoragePath . '/profile-photos';

// Ensure directories exist
foreach ([$storageAppPublicPath, $profilePhotosPath, $publicStoragePath, $publicProfilePhotosPath] as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "<p>Created directory: $dir</p>";
        } else {
            echo "<p style='color:red;'>Failed to create directory: $dir</p>";
        }
    }
}

// Function to copy all files from source to destination
function copyAllFiles($source, $destination) {
    if (!file_exists($source)) {
        echo "<p style='color:red;'>Source directory does not exist: $source</p>";
        return [0, 0];
    }
    
    if (!file_exists($destination)) {
        if (!mkdir($destination, 0777, true)) {
            echo "<p style='color:red;'>Failed to create destination directory: $destination</p>";
            return [0, 0];
        }
    }
    
    $files = scandir($source);
    $copied = 0;
    $failed = 0;
    
    foreach ($files as $file) {
        if ($file != "." && $file != ".." && is_file($source . '/' . $file)) {
            $sourceFile = $source . '/' . $file;
            $destFile = $destination . '/' . $file;
            
            if (copy($sourceFile, $destFile)) {
                echo "<p>Copied: $file</p>";
                $copied++;
            } else {
                echo "<p style='color:red;'>Failed to copy: $file</p>";
                $failed++;
            }
        }
    }
    
    return [$copied, $failed];
}

// Copy files from storage to public
echo "<h2>Copying files from storage to public</h2>";
list($copiedToPublic, $failedToPublic) = copyAllFiles($profilePhotosPath, $publicProfilePhotosPath);
echo "<p>Copied $copiedToPublic files to public, failed to copy $failedToPublic files</p>";

// Copy files from public to storage
echo "<h2>Copying files from public to storage</h2>";
list($copiedToStorage, $failedToStorage) = copyAllFiles($publicProfilePhotosPath, $profilePhotosPath);
echo "<p>Copied $copiedToStorage files to storage, failed to copy $failedToStorage files</p>";

echo "<p><a href='/'>Return to your site</a></p>";
EOT;

if (file_put_contents($quickFixPath, $quickFixContent)) {
    echo "<p style='color:green;'>Created quick fix script at $quickFixPath</p>";
    echo "<p>You can run this script to copy all profile photos between storage and public directories.</p>";
    echo "<p><a href='copy-profile-photos.php' target='_blank'>Run Quick Fix Script</a></p>";
} else {
    echo "<p style='color:red;'>Failed to create quick fix script</p>";
}

// Create a test profile photo
echo "<h2>Creating Test Profile Photo</h2>";

$testPhotoContent = <<<'EOT'
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <circle cx="100" cy="100" r="100" fill="#f0f0f0"/>
  <circle cx="100" cy="80" r="40" fill="#999"/>
  <circle cx="100" cy="180" r="60" fill="#999"/>
</svg>
EOT;

// Create test photo in storage
if (!file_exists($profilePhotosPath)) {
    mkdir($profilePhotosPath, 0777, true);
}

$testPhotoName = 'test-' . time() . '.svg';
$storageTestPath = $profilePhotosPath . '/' . $testPhotoName;

if (file_put_contents($storageTestPath, $testPhotoContent)) {
    echo "<p style='color:green;'>Created test photo in storage: $storageTestPath</p>";
    
    // Copy to public
    if (!file_exists($publicProfilePhotosPath)) {
        mkdir($publicProfilePhotosPath, 0777, true);
    }
    
    $publicTestPath = $publicProfilePhotosPath . '/' . $testPhotoName;
    if (copy($storageTestPath, $publicTestPath)) {
        echo "<p style='color:green;'>Copied test photo to public: $publicTestPath</p>";
        
        // Create a URL to the test photo
        $testPhotoUrl = '/storage/profile-photos/' . $testPhotoName;
        echo "<p>Test photo URL: <a href='$testPhotoUrl' target='_blank'>$testPhotoUrl</a></p>";
        echo "<p>Click the link to verify the file is accessible.</p>";
        
        // Display the test photo
        echo "<p>Test photo preview:</p>";
        echo "<img src='$testPhotoUrl' style='max-width: 200px; border: 1px solid #ddd;'>";
    } else {
        echo "<p style='color:red;'>Failed to copy test photo to public</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
} else {
    echo "<p style='color:red;'>Failed to create test photo in storage</p>";
    echo "<p>Error: " . error_get_last()['message'] . "</p>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Update the database password in this script and run it again to see all user profile photos</li>";
echo "<li>Run the copy-profile-photos.php script to ensure all photos are in both locations</li>";
echo "<li>Check if the test photo is accessible via the URL</li>";
echo "<li>If the test photo is not accessible, there may be a permissions issue with your web server</li>";
echo "</ol>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 