<?php
// Direct User Model Fix for InfinityFree
// This script directly fixes the syntax error in User.php

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Direct User.php Fix</h1>";

// Path to User model
$userModelPath = __DIR__ . '/app/Models/User.php';

if (file_exists($userModelPath)) {
    echo "<p>Found User.php file.</p>";
    
    // Create backup
    $backupPath = $userModelPath . '.backup.' . time();
    if (copy($userModelPath, $backupPath)) {
        echo "<p>Created backup at: " . basename($backupPath) . "</p>";
        
        // Read file content
        $content = file_get_contents($userModelPath);
        
        // Replace the problematic methods with correct ones
        // First, find the class closing brace
        $lastBrace = strrpos($content, '}');
        
        if ($lastBrace !== false) {
            // Remove any existing problematic methods
            $pattern = '/public\s+function\s+getProfilePhotoUrlAttribute.*?protected\s+function\s+defaultProfilePhotoUrl.*?}/s';
            $content = preg_replace($pattern, '', $content);
            
            // Insert the correct methods before the class closing brace
            $correctMethods = '
    /**
     * Get the URL for the user\'s profile photo.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute()
    {
        // If the path is empty or 0, use the default
        if (empty($this->profile_photo_path) || $this->profile_photo_path === "0" || $this->profile_photo_path === 0) {
            return $this->defaultProfilePhotoUrl();
        }
        
        // Return a direct URL to the file - no checking if it exists
        return "/".$this->profile_photo_path."?v=".time();
    }
    
    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        // Just return kofa.png directly
        return "/img/kofa.png?v=".time();
    }
';
            
            // Insert the correct methods before the closing brace
            $newContent = substr($content, 0, $lastBrace) . $correctMethods . "\n}";
            
            // Write the updated content back to the file
            if (file_put_contents($userModelPath, $newContent)) {
                echo "<p style='color:green;'>✅ Successfully fixed User.php!</p>";
                
                // Clear Laravel caches
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
                
                echo "<p>Cleared " . $filesCleared . " cache files.</p>";
                
                echo "<p style='color:green;'>✅ Fix completed! You should now be able to access your site without the syntax error.</p>";
                echo "<p><a href='/admin/users' style='display: inline-block; padding: 10px; background-color: #4CAF50; color: white; text-decoration: none;'>Go to Users Page</a></p>";
            } else {
                echo "<p style='color:red;'>Failed to write to User.php. Check file permissions.</p>";
            }
        } else {
            echo "<p style='color:red;'>Could not find the end of the User class.</p>";
        }
    } else {
        echo "<p style='color:red;'>Could not create backup file.</p>";
    }
} else {
    echo "<p style='color:red;'>User.php not found at: " . $userModelPath . "</p>";
}
?> 