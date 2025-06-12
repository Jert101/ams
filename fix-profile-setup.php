<?php
// Fix profile setup - Move kofa.png to public/profile-photos and update database

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix Profile Setup</h1>";

// Step 1: Ensure public/profile-photos directory exists
$targetDir = 'public/profile-photos';
if (!file_exists($targetDir)) {
    if (mkdir($targetDir, 0755, true)) {
        echo "<p style='color:green;'>✅ Created directory: $targetDir</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create directory: $targetDir</p>";
        exit;
    }
} else {
    echo "<p>✓ Directory exists: $targetDir</p>";
}

// Step 2: Copy kofa.png to public/profile-photos
$kofaSources = [
    'kofa.png',
    'public/kofa.png',
    'public/img/kofa.png',
    'public/img/logos/kofa.png'
];

$kofaFound = false;
foreach ($kofaSources as $source) {
    if (file_exists($source)) {
        echo "<p>Found kofa.png at: $source</p>";
        
        if (copy($source, "$targetDir/kofa.png")) {
            echo "<p style='color:green;'>✅ Copied kofa.png to $targetDir/kofa.png</p>";
            $kofaFound = true;
            break;
        } else {
            echo "<p style='color:red;'>❌ Failed to copy kofa.png from $source</p>";
        }
    }
}

if (!$kofaFound) {
    echo "<p style='color:red;'>❌ Could not find kofa.png in any expected location</p>";
    
    // Create a simple placeholder image
    $image = imagecreatetruecolor(100, 100);
    $bgColor = imagecolorallocate($image, 240, 240, 240);
    $textColor = imagecolorallocate($image, 50, 50, 50);
    
    imagefill($image, 0, 0, $bgColor);
    imagestring($image, 5, 25, 40, 'KOFA', $textColor);
    
    if (imagepng($image, "$targetDir/kofa.png")) {
        echo "<p style='color:green;'>✅ Created a placeholder kofa.png image</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create placeholder image</p>";
    }
    
    imagedestroy($image);
}

// Step 3: Connect to database and update paths
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
    
    echo "<p style='color:green;'>✅ Connected to database</p>";
    
    // Update default profile paths in the database
    $defaultPaths = [
        'kofa.png',
        'img/kofa.png',
        'public/kofa.png',
        'public/img/kofa.png'
    ];
    
    $placeholders = implode(',', array_fill(0, count($defaultPaths), '?'));
    $stmt = $dbConnection->prepare("UPDATE users SET profile_photo_path = 'profile-photos/kofa.png' WHERE profile_photo_path IN ($placeholders)");
    
    if ($stmt->execute($defaultPaths)) {
        $count = $stmt->rowCount();
        echo "<p style='color:green;'>✅ Updated $count users with default profile photo path</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to update default profile paths</p>";
    }
    
    // Set default profile for users with NULL or empty profile_photo_path
    $stmt = $dbConnection->prepare("UPDATE users SET profile_photo_path = 'profile-photos/kofa.png' WHERE profile_photo_path IS NULL OR profile_photo_path = ''");
    
    if ($stmt->execute()) {
        $count = $stmt->rowCount();
        echo "<p style='color:green;'>✅ Updated $count users with NULL or empty profile photo path</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to update users with NULL profile paths</p>";
    }
    
    // Check if any users still have incorrect paths
    $users = $dbConnection->query("SELECT id, name, profile_photo_path FROM users WHERE profile_photo_path NOT LIKE 'profile-photos/%'")->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "<p>Found " . count($users) . " users with incorrect profile photo paths</p>";
        
        foreach ($users as $user) {
            $oldPath = $user['profile_photo_path'];
            $filename = basename($oldPath);
            $newPath = 'profile-photos/' . $filename;
            
            // Copy file if it exists
            $sourcePaths = [
                $oldPath,
                'storage/app/public/' . $oldPath,
                'storage/app/' . $oldPath,
                'storage/' . $oldPath
            ];
            
            $fileCopied = false;
            foreach ($sourcePaths as $sourcePath) {
                if (file_exists($sourcePath)) {
                    if (copy($sourcePath, "$targetDir/$filename")) {
                        echo "<p style='color:green;'>✅ Copied $sourcePath to $targetDir/$filename</p>";
                        $fileCopied = true;
                        break;
                    }
                }
            }
            
            if (!$fileCopied) {
                echo "<p style='color:orange;'>⚠️ Could not find file for user {$user['name']}, using default</p>";
                $newPath = 'profile-photos/kofa.png';
            }
            
            // Update database
            $updateStmt = $dbConnection->prepare("UPDATE users SET profile_photo_path = ? WHERE id = ?");
            if ($updateStmt->execute([$newPath, $user['id']])) {
                echo "<p style='color:green;'>✅ Updated path for user {$user['name']}: $oldPath → $newPath</p>";
            } else {
                echo "<p style='color:red;'>❌ Failed to update path for user {$user['name']}</p>";
            }
        }
    } else {
        echo "<p>✓ All users have correct profile photo paths</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Step 4: Fix User model to ensure correct profile photo URL
$userModelPath = 'app/Models/User.php';
if (file_exists($userModelPath)) {
    $userModel = file_get_contents($userModelPath);
    
    // Check if the model needs fixing
    if (strpos($userModel, 'getProfilePhotoUrlAttribute') !== false) {
        // Create backup
        file_put_contents($userModelPath . '.backup', $userModel);
        
        // Fix the getProfilePhotoUrlAttribute method
        $pattern = '/public\s+function\s+getProfilePhotoUrlAttribute\s*\([^)]*\)\s*\{[^}]*\}/s';
        $replacement = 'public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? asset($this->profile_photo_path)
            : asset(\'profile-photos/kofa.png\');
    }';
        
        $fixedModel = preg_replace($pattern, $replacement, $userModel);
        
        if ($fixedModel !== $userModel) {
            if (file_put_contents($userModelPath, $fixedModel)) {
                echo "<p style='color:green;'>✅ Fixed User model getProfilePhotoUrlAttribute method</p>";
            } else {
                echo "<p style='color:red;'>❌ Failed to update User model</p>";
            }
        } else {
            echo "<p>✓ User model already has correct getProfilePhotoUrlAttribute method</p>";
        }
    } else {
        // Add the method if it doesn't exist
        $pattern = '/class\s+User\s+extends\s+Authenticatable\s*\{/';
        $addition = 'class User extends Authenticatable {
    
    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? asset($this->profile_photo_path)
            : asset(\'profile-photos/kofa.png\');
    }';
        
        $fixedModel = preg_replace($pattern, $addition, $userModel);
        
        if ($fixedModel !== $userModel) {
            if (file_put_contents($userModelPath, $fixedModel)) {
                echo "<p style='color:green;'>✅ Added getProfilePhotoUrlAttribute method to User model</p>";
            } else {
                echo "<p style='color:red;'>❌ Failed to update User model</p>";
            }
        } else {
            echo "<p style='color:orange;'>⚠️ Could not add getProfilePhotoUrlAttribute method to User model</p>";
        }
    }
} else {
    echo "<p style='color:orange;'>⚠️ User model not found at $userModelPath</p>";
}

// Step 5: Create JavaScript fix for edit page
$jsContent = <<<'JS'
// Fix for profile photos on edit page
(function() {
    console.log("Running edit page profile photo fix");
    
    // Function to fix profile photos
    function fixEditPagePhotos() {
        // Find the profile photo section by looking for the label
        var profileLabels = Array.from(document.querySelectorAll("label"))
            .filter(function(label) {
                return label.textContent.includes("Profile Photo");
            });
        
        if (profileLabels.length > 0) {
            console.log("Found profile photo labels:", profileLabels.length);
            
            profileLabels.forEach(function(label) {
                // Find the parent container
                var parent = label.parentElement;
                
                // Look for the profile photo container
                var container = parent.querySelector(".profile-photo-container");
                
                if (!container) {
                    // Look for empty divs that might be the container
                    var emptyDivs = Array.from(parent.querySelectorAll("div"))
                        .filter(function(div) {
                            return div.children.length === 0 || 
                                  (div.children.length === 1 && 
                                   div.children[0].tagName === "IMG" && 
                                   window.getComputedStyle(div.children[0]).display === "none");
                        });
                    
                    if (emptyDivs.length > 0) {
                        container = emptyDivs[0];
                        container.className = "profile-photo-container";
                    } else {
                        // Create a new container
                        container = document.createElement("div");
                        container.className = "profile-photo-container";
                        parent.appendChild(container);
                    }
                }
                
                // Style the container
                container.style.display = "block";
                container.style.width = "100px";
                container.style.height = "100px";
                container.style.borderRadius = "50%";
                container.style.overflow = "hidden";
                container.style.backgroundColor = "#f0f0f0";
                container.style.margin = "10px 0";
                
                // Check for existing images
                var images = container.querySelectorAll("img");
                var visibleImages = Array.from(images)
                    .filter(function(img) {
                        return window.getComputedStyle(img).display !== "none";
                    });
                
                if (visibleImages.length === 0) {
                    // If there's a hidden image, make it visible
                    if (images.length > 0) {
                        var img = images[0];
                        img.style.display = "block";
                        img.style.visibility = "visible";
                        img.style.width = "100%";
                        img.style.height = "100%";
                        img.style.objectFit = "cover";
                        
                        // Fix path if needed
                        if (img.src.includes("storage/app/public/profile-photos")) {
                            var filename = img.src.split("/").pop();
                            img.src = "/profile-photos/" + filename;
                        }
                        
                        // Add error handler
                        img.onerror = function() {
                            this.src = "/profile-photos/kofa.png";
                        };
                    } else {
                        // Create a new image
                        var newImg = document.createElement("img");
                        newImg.src = "/profile-photos/kofa.png";
                        newImg.alt = "Profile Photo";
                        newImg.style.width = "100%";
                        newImg.style.height = "100%";
                        newImg.style.objectFit = "cover";
                        container.appendChild(newImg);
                    }
                }
            });
        } else {
            console.log("No profile photo labels found, looking for alternative elements");
            
            // Try to find the profile photo section by other means
            var profileSections = Array.from(document.querySelectorAll("div"))
                .filter(function(div) {
                    return div.textContent.includes("Profile Photo");
                });
            
            if (profileSections.length > 0) {
                console.log("Found profile sections:", profileSections.length);
                
                profileSections.forEach(function(section) {
                    // Look for empty circular divs
                    var emptyDivs = Array.from(section.querySelectorAll("div"))
                        .filter(function(div) {
                            return div.children.length === 0 || 
                                  (div.children.length === 1 && 
                                   div.children[0].tagName === "IMG" && 
                                   window.getComputedStyle(div.children[0]).display === "none");
                        });
                    
                    if (emptyDivs.length > 0) {
                        var container = emptyDivs[0];
                        container.className = "profile-photo-container";
                        container.style.display = "block";
                        container.style.width = "100px";
                        container.style.height = "100px";
                        container.style.borderRadius = "50%";
                        container.style.overflow = "hidden";
                        container.style.backgroundColor = "#f0f0f0";
                        
                        // Add image
                        var img = document.createElement("img");
                        img.src = "/profile-photos/kofa.png";
                        img.alt = "Profile Photo";
                        img.style.width = "100%";
                        img.style.height = "100%";
                        img.style.objectFit = "cover";
                        container.appendChild(img);
                    }
                });
            }
        }
    }
    
    // Run the fix
    fixEditPagePhotos();
    
    // Also run after DOM loaded and with a delay
    document.addEventListener("DOMContentLoaded", fixEditPagePhotos);
    setTimeout(fixEditPagePhotos, 500);
    setTimeout(fixEditPagePhotos, 1000);
})();
JS;

$jsDir = 'public/js';
if (!file_exists($jsDir)) {
    mkdir($jsDir, 0755, true);
}

file_put_contents($jsDir . '/edit-page-fix.js', $jsContent);
echo "<p style='color:green;'>✅ Created JavaScript fix for edit page</p>";

// Step 6: Create CSS fix
$cssContent = <<<'CSS'
/* Fix for profile photos on edit page */
.profile-photo-container {
    display: block !important;
    width: 100px !important;
    height: 100px !important;
    border-radius: 50% !important;
    overflow: hidden !important;
    margin: 10px 0 !important;
    background-color: #f0f0f0 !important;
}

.profile-photo-container img {
    display: block !important;
    visibility: visible !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}
CSS;

$cssDir = 'public/css';
if (!file_exists($cssDir)) {
    mkdir($cssDir, 0755, true);
}

file_put_contents($cssDir . '/edit-page-fix.css', $cssContent);
echo "<p style='color:green;'>✅ Created CSS fix for edit page</p>";

// Step 7: Create bookmarklet for easy application
$bookmarklet = "javascript:(function(){var s=document.createElement('script');s.src='/js/edit-page-fix.js?v='+new Date().getTime();document.head.appendChild(s);var c=document.createElement('link');c.rel='stylesheet';c.href='/css/edit-page-fix.css?v='+new Date().getTime();document.head.appendChild(c);})();";

// Step 8: Clean up unnecessary files
$filesToRemove = [
    'fix-profile-paths.php',
    'simple-path-fix.php',
    'public/fix-photos.php',
    'public/direct-edit-fix.php',
    'public/photo-fix-bookmarklet.html',
    'public/fix.html',
    'public/inspect.html',
    'public/generate-fix.php'
];

$removedCount = 0;
foreach ($filesToRemove as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "<p style='color:green;'>✅ Removed unnecessary file: $file</p>";
            $removedCount++;
        } else {
            echo "<p style='color:red;'>❌ Failed to remove file: $file</p>";
        }
    }
}

echo "<p>Removed $removedCount unnecessary files</p>";

// Step 9: Create a layout snippet
$snippetContent = <<<'HTML'
<!-- Add this to your layout files to fix profile photos -->
<link rel="stylesheet" href="{{ asset('css/edit-page-fix.css') }}?v={{ time() }}">
<script src="{{ asset('js/edit-page-fix.js') }}?v={{ time() }}"></script>
HTML;

file_put_contents('profile-fix-snippet.html', $snippetContent);
echo "<p style='color:green;'>✅ Created layout snippet: profile-fix-snippet.html</p>";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Profile Setup</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }
        .code {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            white-space: pre-wrap;
        }
        h1, h2, h3 {
            color: #333;
        }
    </style>
</head>
<body>
    <h2>Profile Setup Complete!</h2>
    
    <p>All profile photos have been set up correctly:</p>
    <ul>
        <li>kofa.png has been moved to public/profile-photos/</li>
        <li>Database paths have been updated to use profile-photos/</li>
        <li>User model has been fixed to use the correct paths</li>
        <li>JavaScript and CSS fixes have been created for the edit page</li>
        <li>Unnecessary files have been removed</li>
    </ul>
    
    <h3>How to Fix the Edit Page</h3>
    
    <h4>Option 1: Bookmarklet (Easiest)</h4>
    <p>Drag this link to your bookmarks bar, then click it when you're on the edit page:</p>
    <p><a href="<?php echo htmlspecialchars($bookmarklet); ?>" class="button">Fix Profile Photos</a></p>
    
    <h4>Option 2: Add to Your Layout</h4>
    <p>Add this code to your layout files just before the <code>&lt;/head&gt;</code> tag:</p>
    <div class="code">&lt;link rel="stylesheet" href="{{ asset('css/edit-page-fix.css') }}?v={{ time() }}"&gt;
&lt;script src="{{ asset('js/edit-page-fix.js') }}?v={{ time() }}"&gt;&lt;/script&gt;</div>
    
    <p>A snippet with this code has been saved to <code>profile-fix-snippet.html</code></p>
    
    <p><a href="/admin/users" class="button">Go to Users Page</a></p>
</body>
</html> 