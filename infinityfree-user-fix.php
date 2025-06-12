<?php
// InfinityFree User Model Fixer
// This script fixes syntax errors in the User model and updates profile photo handling

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>InfinityFree User Model Fixer</h1>";
echo "<p>This tool fixes syntax errors in the User model and updates profile photo handling.</p>";

// Path to User model
$userModelPath = __DIR__ . '/app/Models/User.php';

if (file_exists($userModelPath)) {
    echo "<h2>Found User Model</h2>";
    
    // Backup the original file
    $backupPath = $userModelPath . '.backup.' . time();
    if (copy($userModelPath, $backupPath)) {
        echo "<p style='color:green;'>✅ Created backup of User model at: " . htmlspecialchars(basename($backupPath)) . "</p>";
        
        // Read the file contents
        $modelContent = file_get_contents($userModelPath);
        
        // Display the current content with line numbers
        echo "<h3>Current Model Content:</h3>";
        echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;'>";
        $lines = explode("\n", $modelContent);
        foreach ($lines as $i => $line) {
            echo "<span style='color: #888;'>" . ($i + 1) . ":</span> " . htmlspecialchars($line) . "\n";
        }
        echo "</pre>";
        
        // Replace the problematic methods
        $updatedContent = preg_replace(
            '/public function getProfilePhotoUrlAttribute\(\).*?}.*?protected function defaultProfilePhotoUrl\(\).*?}/s',
            'public function getProfilePhotoUrlAttribute()
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
    }',
            $modelContent
        );
        
        // If no change was made, try a different approach - completely rewrite the file
        if ($updatedContent === $modelContent) {
            echo "<p style='color:orange;'>⚠️ Could not replace the methods using regex. Trying a different approach...</p>";
            
            // Read the file line by line and rebuild it
            $lines = file($userModelPath);
            $newContent = '';
            $skipUntil = -1;
            $methodsAdded = false;
            
            for ($i = 0; $i < count($lines); $i++) {
                // Skip lines that are part of the problematic methods
                if ($i === $skipUntil) {
                    $skipUntil = -1;
                }
                
                if ($skipUntil > 0 && $i < $skipUntil) {
                    continue;
                }
                
                // Check if this is the start of getProfilePhotoUrlAttribute method
                if (strpos($lines[$i], 'public function getProfilePhotoUrlAttribute') !== false) {
                    // Find the end of this method and the next method
                    $methodStart = $i;
                    $bracketCount = 0;
                    $j = $i;
                    
                    while ($j < count($lines)) {
                        $bracketCount += substr_count($lines[$j], '{') - substr_count($lines[$j], '}');
                        if ($bracketCount === 0 && strpos($lines[$j], '}') !== false) {
                            break;
                        }
                        $j++;
                    }
                    
                    // Also skip the defaultProfilePhotoUrl method if it follows
                    $nextMethod = $j + 1;
                    while ($nextMethod < count($lines) && trim($lines[$nextMethod]) === '') {
                        $nextMethod++;
                    }
                    
                    if ($nextMethod < count($lines) && strpos($lines[$nextMethod], 'protected function defaultProfilePhotoUrl') !== false) {
                        $bracketCount = 0;
                        $k = $nextMethod;
                        
                        while ($k < count($lines)) {
                            $bracketCount += substr_count($lines[$k], '{') - substr_count($lines[$k], '}');
                            if ($bracketCount === 0 && strpos($lines[$k], '}') !== false) {
                                break;
                            }
                            $k++;
                        }
                        
                        $skipUntil = $k + 1;
                    } else {
                        $skipUntil = $j + 1;
                    }
                    
                    // Add the fixed methods
                    $newContent .= '    /**
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
                    $methodsAdded = true;
                    continue;
                }
                
                // Add the line to the new content
                $newContent .= $lines[$i];
            }
            
            // If we couldn't find the methods, add them at the end
            if (!$methodsAdded) {
                // Find the closing brace of the class
                $lastBrace = strrpos($newContent, '}');
                if ($lastBrace !== false) {
                    $newContent = substr($newContent, 0, $lastBrace) . '
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
}';
                }
            }
            
            $updatedContent = $newContent;
        }
        
        // Write the updated content back to the file
        if (file_put_contents($userModelPath, $updatedContent)) {
            echo "<p style='color:green;'>✅ Successfully updated User model!</p>";
            
            echo "<h3>Updated Model Content:</h3>";
            echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;'>";
            $lines = explode("\n", $updatedContent);
            foreach ($lines as $i => $line) {
                echo "<span style='color: #888;'>" . ($i + 1) . ":</span> " . htmlspecialchars($line) . "\n";
            }
            echo "</pre>";
            
            // Clear Laravel caches
            echo "<h2>Clearing Laravel Caches</h2>";
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
            
            echo "<p style='color:green;'>✅ Cleared " . $filesCleared . " cache files</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to write updated User model</p>";
            echo "<p>This might be due to file permissions. Try changing the permissions on the file to allow writing.</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Failed to create backup of User model</p>";
    }
} else {
    echo "<p style='color:red;'>❌ User model file not found at expected location: " . htmlspecialchars($userModelPath) . "</p>";
}

// Provide option to fix profile photos after fixing the model
echo "<h2>Next Steps</h2>";
echo "<p>After fixing the User model, you can now fix the profile photos:</p>";
echo "<p><a href='infinityfree-image-fixer.php' style='display: inline-block; padding: 10px; background-color: #4CAF50; color: white; text-decoration: none;'>Go to Profile Photo Fixer</a></p>";

// Link back to admin
echo "<p><a href='/admin/users' style='display: inline-block; padding: 10px; background-color: #2196F3; color: white; text-decoration: none;'>Go to Users Page</a></p>";
?>
