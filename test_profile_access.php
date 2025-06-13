<?php
/**
 * Test Profile Photos Access
 * 
 * This script checks if profile photos are accessible from the root level profile-photos directory.
 * Upload this file to your InfinityFree hosting and run it in your browser.
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check if a URL is accessible
function isUrlAccessible($url) {
    $headers = @get_headers($url);
    return $headers && strpos($headers[0], '200') !== false;
}

// Get the server name and protocol
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$serverName = $_SERVER['SERVER_NAME'];
$baseUrl = $protocol . $serverName;

// Check if the profile-photos directory exists
$profilePhotosDir = __DIR__ . '/profile-photos';
if (!file_exists($profilePhotosDir)) {
    echo "<p style='color: red;'>The profile-photos directory does not exist at: {$profilePhotosDir}</p>";
    echo "<p>Please create the directory and make sure it has the correct permissions (777).</p>";
} else {
    echo "<p style='color: green;'>The profile-photos directory exists at: {$profilePhotosDir}</p>";
    
    // Check directory permissions
    $perms = substr(sprintf('%o', fileperms($profilePhotosDir)), -4);
    echo "<p>Directory permissions: {$perms}</p>";
    
    if ($perms != '0777') {
        echo "<p style='color: orange;'>Warning: Directory permissions are not set to 777. This might cause issues.</p>";
        echo "<p>You can set the permissions with: <code>chmod 777 {$profilePhotosDir}</code></p>";
    }
    
    // List files in the directory
    $files = scandir($profilePhotosDir);
    $imageFiles = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file);
    });
    
    if (count($imageFiles) > 0) {
        echo "<h3>Found " . count($imageFiles) . " image files:</h3>";
        echo "<ul>";
        
        foreach ($imageFiles as $file) {
            $fileUrl = $baseUrl . '/profile-photos/' . $file;
            $isAccessible = isUrlAccessible($fileUrl);
            $color = $isAccessible ? 'green' : 'red';
            $status = $isAccessible ? 'Accessible' : 'Not accessible';
            
            echo "<li style='margin-bottom: 10px;'>";
            echo "<strong>{$file}</strong> - <span style='color: {$color};'>{$status}</span><br>";
            echo "URL: <a href='{$fileUrl}' target='_blank'>{$fileUrl}</a><br>";
            
            if ($isAccessible) {
                echo "<img src='{$fileUrl}' alt='{$file}' style='max-width: 100px; max-height: 100px;'>";
            }
            
            echo "</li>";
        }
        
        echo "</ul>";
    } else {
        echo "<p>No image files found in the profile-photos directory.</p>";
    }
}

// Check .htaccess files
$rootHtaccess = __DIR__ . '/.htaccess';
$profileHtaccess = __DIR__ . '/profile-photos/.htaccess';

echo "<h3>Checking .htaccess files:</h3>";

if (file_exists($rootHtaccess)) {
    echo "<p style='color: green;'>Root .htaccess file exists.</p>";
} else {
    echo "<p style='color: red;'>Root .htaccess file does not exist!</p>";
}

if (file_exists($profileHtaccess)) {
    echo "<p style='color: green;'>profile-photos/.htaccess file exists.</p>";
} else {
    echo "<p style='color: red;'>profile-photos/.htaccess file does not exist!</p>";
}

// Test direct image access
echo "<h3>Testing direct image access:</h3>";

if (count($imageFiles) > 0) {
    // Get the first image file
    $testFile = reset($imageFiles);
    $testUrl = $baseUrl . '/profile-photos/' . $testFile;
    
    echo "<p>Testing URL: <a href='{$testUrl}' target='_blank'>{$testUrl}</a></p>";
    
    // Try to get the image headers
    $headers = @get_headers($testUrl);
    
    if ($headers) {
        echo "<p>Response headers:</p>";
        echo "<pre>";
        foreach ($headers as $header) {
            echo htmlspecialchars($header) . "\n";
        }
        echo "</pre>";
        
        if (strpos($headers[0], '200') !== false) {
            echo "<p style='color: green;'>Success! The image is directly accessible.</p>";
        } else {
            echo "<p style='color: red;'>Error: The image is not directly accessible. Status: " . htmlspecialchars($headers[0]) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Error: Could not get headers for the image URL.</p>";
    }
} else {
    echo "<p>No image files to test.</p>";
}

echo "<h3>Recommendations:</h3>";
echo "<ul>";
echo "<li>Make sure the profile-photos directory has permissions set to 777</li>";
echo "<li>Verify that both .htaccess files are properly uploaded</li>";
echo "<li>If images are still not accessible, contact your hosting provider</li>";
echo "</ul>";
?> 