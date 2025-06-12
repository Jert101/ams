<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Profile Photo Fix</h1>";
echo "<p>This script will fix profile photo display issues by:</p>";
echo "<ol>";
echo "<li>Creating necessary directories</li>";
echo "<li>Copying profile photos between storage and public directories</li>";
echo "<li>Updating the User model to properly handle profile photo URLs</li>";
echo "</ol>";

// Define paths
$basePath = __DIR__;
$storagePath = $basePath . '/storage';
$storageAppPublicPath = $storagePath . '/app/public';
$profilePhotosPath = $storageAppPublicPath . '/profile-photos';
$publicPath = $basePath . '/public';
$publicStoragePath = $publicPath . '/storage';
$publicProfilePhotosPath = $publicStoragePath . '/profile-photos';

echo "<h2>Step 1: Creating Directories</h2>";

// Create necessary directories with proper permissions
$directories = [
    'storage/app/public' => $storageAppPublicPath,
    'storage/app/public/profile-photos' => $profilePhotosPath,
    'public/storage' => $publicStoragePath,
    'public/storage/profile-photos' => $publicProfilePhotosPath,
];

foreach ($directories as $name => $path) {
    echo "<p>Checking $name...</p>";
    
    if (!file_exists($path)) {
        if (mkdir($path, 0777, true)) {
            echo "<p style='color:green;'>✅ Created directory: $path</p>";
            chmod($path, 0777); // Ensure permissions are set
        } else {
            echo "<p style='color:red;'>❌ Failed to create directory: $path</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
        }
    } else {
        echo "<p style='color:green;'>✅ Directory already exists: $path</p>";
        
        // Update permissions
        chmod($path, 0777);
        echo "<p>Updated permissions to 0777</p>";
    }
}

echo "<h2>Step 2: Copying Profile Photos</h2>";

// Function to copy all files from source to destination
function copyAllFiles($source, $destination) {
    if (!file_exists($source)) {
        echo "<p style='color:orange;'>⚠️ Source directory does not exist: $source</p>";
        return [0, 0];
    }
    
    if (!file_exists($destination)) {
        if (!mkdir($destination, 0777, true)) {
            echo "<p style='color:red;'>❌ Failed to create destination directory: $destination</p>";
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
                echo "<p style='color:green;'>✅ Copied: $file</p>";
                $copied++;
            } else {
                echo "<p style='color:red;'>❌ Failed to copy: $file</p>";
                $failed++;
            }
        }
    }
    
    return [$copied, $failed];
}

// Copy files from storage to public
echo "<h3>Copying files from storage to public</h3>";
list($copiedToPublic, $failedToPublic) = copyAllFiles($profilePhotosPath, $publicProfilePhotosPath);
echo "<p>Copied $copiedToPublic files to public, failed to copy $failedToPublic files</p>";

// Copy files from public to storage
echo "<h3>Copying files from public to storage</h3>";
list($copiedToStorage, $failedToStorage) = copyAllFiles($publicProfilePhotosPath, $profilePhotosPath);
echo "<p>Copied $copiedToStorage files to storage, failed to copy $failedToStorage files</p>";

echo "<h2>Step 3: Updating User Model</h2>";

$userModelPath = $basePath . '/app/Models/User.php';
if (file_exists($userModelPath)) {
    echo "<p style='color:green;'>✅ User model exists</p>";
    
    // Create a backup of the User model
    $backupPath = $userModelPath . '.backup.' . time();
    if (copy($userModelPath, $backupPath)) {
        echo "<p style='color:green;'>✅ Created backup of User model at $backupPath</p>";
        
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
                echo "<p style='color:green;'>✅ Successfully updated User model getProfilePhotoUrlAttribute method</p>";
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

echo "<h2>Step 4: Creating Test Profile Photo</h2>";

$testPhotoContent = <<<'EOT'
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <circle cx="100" cy="100" r="100" fill="#f0f0f0"/>
  <circle cx="100" cy="80" r="40" fill="#999"/>
  <circle cx="100" cy="180" r="60" fill="#999"/>
</svg>
EOT;

// Create test photo in both locations
$testPhotoName = 'test-' . time() . '.svg';
$storageTestPath = $profilePhotosPath . '/' . $testPhotoName;
$publicTestPath = $publicProfilePhotosPath . '/' . $testPhotoName;

if (file_put_contents($storageTestPath, $testPhotoContent)) {
    echo "<p style='color:green;'>✅ Created test photo in storage: $storageTestPath</p>";
    
    if (copy($storageTestPath, $publicTestPath)) {
        echo "<p style='color:green;'>✅ Copied test photo to public: $publicTestPath</p>";
        
        // Create a URL to the test photo
        $testPhotoUrl = '/storage/profile-photos/' . $testPhotoName;
        echo "<p>Test photo URL: <a href='$testPhotoUrl' target='_blank'>$testPhotoUrl</a></p>";
        echo "<p>Click the link to verify the file is accessible.</p>";
        
        // Display the test photo
        echo "<p>Test photo preview:</p>";
        echo "<img src='$testPhotoUrl' style='max-width: 200px; border: 1px solid #ddd;'>";
    } else {
        echo "<p style='color:red;'>❌ Failed to copy test photo to public</p>";
    }
} else {
    echo "<p style='color:red;'>❌ Failed to create test photo in storage</p>";
}

echo "<h2>Step 5: Clear Cache</h2>";

// Clear Laravel cache
if (file_exists($basePath . '/bootstrap/cache/config.php')) {
    if (unlink($basePath . '/bootstrap/cache/config.php')) {
        echo "<p style='color:green;'>✅ Cleared config cache</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to clear config cache</p>";
    }
}

if (file_exists($basePath . '/bootstrap/cache/routes.php')) {
    if (unlink($basePath . '/bootstrap/cache/routes.php')) {
        echo "<p style='color:green;'>✅ Cleared routes cache</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to clear routes cache</p>";
    }
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Check if the test photo is accessible via the URL above</li>";
echo "<li>If the test photo is not accessible, there may be a permissions issue with your web server</li>";
echo "<li>Try uploading a new profile photo and see if it displays correctly</li>";
echo "<li>If issues persist, check the Laravel logs for errors</li>";
echo "</ol>";

echo "<p><a href='/'>Return to your site</a></p>";
?> 