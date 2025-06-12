<?php
// Direct fix for the User model's profile photo URL method
// This script only requires basic PHP and file system access

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Direct User Model Fix</h1>";

// File path to the User model
$userModelPath = __DIR__ . '/app/Models/User.php';

if (!file_exists($userModelPath)) {
    die("<p style='color:red;'>Error: User model file not found at $userModelPath</p>");
}

// Create backup of the User model
$backupPath = $userModelPath . '.backup.' . time();
if (!copy($userModelPath, $backupPath)) {
    echo "<p style='color:orange;'>Warning: Could not create backup of User model</p>";
} else {
    echo "<p>Created backup of User model at $backupPath</p>";
}

// Read the User model file
$modelContent = file_get_contents($userModelPath);

// The replacement code for the getProfilePhotoUrlAttribute method
$replacementCode = '
    public function getProfilePhotoUrlAttribute()
    {
        // Log debug info
        \Log::debug(\'Profile photo path\', [
            \'user_id\' => $this->user_id,
            \'path\' => $this->profile_photo_path,
            \'type\' => gettype($this->profile_photo_path)
        ]);
        
        // For any problematic values, generate an SVG avatar
        if (empty($this->profile_photo_path) || $this->profile_photo_path === "0" || $this->profile_photo_path === 0) {
            return $this->generateSvgAvatar();
        }
        
        // Check if it\'s the default logo
        if ($this->profile_photo_path === \'kofa.png\') {
            return asset(\'img/kofa.png\') . \'?v=\' . time();
        }
        
        // Check if file exists in public directory
        if (file_exists(public_path($this->profile_photo_path))) {
            return asset($this->profile_photo_path) . \'?v=\' . time();
        }
        
        // Check storage paths
        if (file_exists(storage_path(\'app/public/\' . $this->profile_photo_path))) {
            return asset(\'storage/\' . $this->profile_photo_path) . \'?v=\' . time();
        }
        
        // Fallback to SVG avatar
        return $this->generateSvgAvatar();
    }
    
    /**
     * Generate an SVG avatar with user\'s initials.
     *
     * @return string
     */
    protected function generateSvgAvatar()
    {
        // Generate initials from name
        $name = $this->name ?? \'User\';
        $initials = \'\';
        $words = explode(\' \', $name);
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper(substr($word, 0, 1));
            }
        }
        $initials = substr($initials, 0, 2); // Limit to 2 characters
        
        // Generate a consistent color based on the user ID
        $bgColor = \'#\' . substr(md5($this->user_id ?? 1), 0, 6);
        
        // Create SVG
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
  <rect width="200" height="200" fill="$bgColor"/>
  <text x="100" y="115" font-family="Arial, sans-serif" font-size="80" font-weight="bold" text-anchor="middle" fill="#ffffff">$initials</text>
</svg>
SVG;
        
        // Convert to data URI
        $encoded = base64_encode($svg);
        return \'data:image/svg+xml;base64,\' . $encoded;
    }';

// Create a new User model content with fixed method
// We need to find the buggy getProfilePhotoUrlAttribute method and replace it and any following method (generateAvatarSvg)
$pattern = '/public\s+function\s+getProfilePhotoUrlAttribute\s*\(\).*?(?=\s*^\s*(?:public|protected|private)\s+function|$)/ms';
$updatedContent = preg_replace($pattern, $replacementCode, $modelContent);

// Now find and replace the defaultProfilePhotoUrl method
$defaultPhotoPattern = '/protected\s+function\s+defaultProfilePhotoUrl\s*\(\).*?(?=\s*^\s*(?:public|protected|private)\s+function|$)/ms';
$defaultPhotoReplacement = '
    protected function defaultProfilePhotoUrl()
    {
        return $this->generateSvgAvatar();
    }';
$updatedContent = preg_replace($defaultPhotoPattern, $defaultPhotoReplacement, $updatedContent);

// Make sure the problematic code is removed
// This part of code was duplicated or not properly closed
if (strpos($updatedContent, '// Check if it\'s the default photo') !== false) {
    $badCodePattern = '/\s+\/\/\s+Check if it\'s the default photo.*?(?=\s*^\s*(?:public|protected|private)\s+function|$)/ms';
    $updatedContent = preg_replace($badCodePattern, '', $updatedContent);
}

// Remove the generateAvatarSvg method if it exists (since we're adding generateSvgAvatar)
$generateAvatarPattern = '/protected\s+function\s+generateAvatarSvg\s*\(\).*?(?=\s*^\s*(?:public|protected|private)\s+function|$)/ms';
$updatedContent = preg_replace($generateAvatarPattern, '', $updatedContent);

// Write the updated content back to the file
if (file_put_contents($userModelPath, $updatedContent)) {
    echo "<p style='color:green;'>✅ Successfully updated User model with fixed avatar handling</p>";
    
    // Try to clear Laravel cache
    echo "<p>Attempting to clear Laravel cache...</p>";
    $cacheDirs = [
        __DIR__ . '/storage/framework/views',
        __DIR__ . '/bootstrap/cache'
    ];
    
    foreach ($cacheDirs as $dir) {
        if (file_exists($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            echo "<p>Cleared cache directory: $dir</p>";
        }
    }
    
    echo "<p style='color:green; font-weight:bold;'>🎉 Fix completed! Now view any user profile with cache busting:</p>";
    echo "<p><a href='/admin/users/1/edit?nocache=" . time() . "' target='_blank'>View Admin User</a></p>";
    echo "<p><strong>Important:</strong> Clear your browser cache or try in a private/incognito window.</p>";
} else {
    echo "<p style='color:red;'>Failed to update User model file. Check file permissions.</p>";
}
?> 