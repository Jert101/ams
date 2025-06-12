<?php
// Fix profile photo paths to ensure they're in public/profile-photos/
// This script ensures profile photos are accessible on InfinityFree

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Profile Photo Path Fix</h1>";

// Step 1: Create public/profile-photos directory if it doesn't exist
$profileDir = __DIR__ . '/profile-photos';
if (!file_exists($profileDir)) {
    if (mkdir($profileDir, 0755, true)) {
        echo "<p style='color:green;'>✅ Created directory: public/profile-photos</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create directory: public/profile-photos</p>";
    }
} else {
    echo "<p>✓ Directory already exists: public/profile-photos</p>";
}

// Step 2: Check for storage/app/public/profile-photos directory
$storageDir = __DIR__ . '/../storage/app/public/profile-photos';
if (file_exists($storageDir)) {
    echo "<p>Found storage directory: storage/app/public/profile-photos</p>";
    
    // Copy files from storage to public
    $files = scandir($storageDir);
    $copiedCount = 0;
    
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && is_file($storageDir . '/' . $file)) {
            if (copy($storageDir . '/' . $file, $profileDir . '/' . $file)) {
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

// Step 3: Create JavaScript fix for edit page
$jsDir = __DIR__ . '/js';
if (!file_exists($jsDir)) {
    mkdir($jsDir, 0755, true);
}

$jsContent = <<<'JS'
// Fix for profile photos on edit page
(function() {
    console.log("Running profile photo fix");
    
    // Function to fix profile photos
    function fixProfilePhotos() {
        // Find the profile photo section
        var profileSections = Array.from(document.querySelectorAll("label, div"))
            .filter(function(el) {
                return el.textContent.includes("Profile Photo");
            });
        
        console.log("Found profile sections:", profileSections.length);
        
        profileSections.forEach(function(section) {
            // Find the container
            var parent = section.parentElement;
            var container = parent.querySelector(".profile-photo-container");
            
            if (container) {
                console.log("Found container:", container);
                
                // Check for existing images
                var img = container.querySelector("img");
                if (img) {
                    console.log("Found image:", img.src);
                    
                    // Fix the image path if needed
                    if (img.src.includes("storage/app/public/profile-photos")) {
                        // Convert storage path to public path
                        var filename = img.src.split("/").pop();
                        img.src = "/profile-photos/" + filename;
                        console.log("Fixed image path:", img.src);
                    }
                    
                    // Make sure image is visible
                    img.style.display = "block";
                    img.style.visibility = "visible";
                    img.style.width = "100%";
                    img.style.height = "100%";
                    img.style.objectFit = "cover";
                    
                    // Add error handler
                    img.onerror = function() {
                        this.src = "/img/kofa.png";
                        console.log("Image error, using default");
                    };
                } else {
                    console.log("No image found, adding default");
                    
                    // Create default image
                    var newImg = document.createElement("img");
                    newImg.src = "/img/kofa.png";
                    newImg.alt = "Profile Photo";
                    newImg.style.width = "100%";
                    newImg.style.height = "100%";
                    newImg.style.objectFit = "cover";
                    
                    // Add to container
                    container.appendChild(newImg);
                }
                
                // Make container visible
                container.style.display = "block";
                container.style.width = "100px";
                container.style.height = "100px";
                container.style.borderRadius = "50%";
                container.style.overflow = "hidden";
            }
        });
    }
    
    // Run the fix
    fixProfilePhotos();
    
    // Also run after DOM loaded and with a delay
    document.addEventListener("DOMContentLoaded", fixProfilePhotos);
    setTimeout(fixProfilePhotos, 500);
})();
JS;

file_put_contents($jsDir . '/profile-path-fix.js', $jsContent);
echo "<p style='color:green;'>✅ Created JavaScript fix: js/profile-path-fix.js</p>";

// Step 4: Create CSS fix
$cssDir = __DIR__ . '/css';
if (!file_exists($cssDir)) {
    mkdir($cssDir, 0755, true);
}

$cssContent = <<<'CSS'
/* Fix for profile photos */
.profile-photo-container {
    display: block !important;
    width: 100px !important;
    height: 100px !important;
    border-radius: 50% !important;
    overflow: hidden !important;
    margin: 10px 0 !important;
}

.profile-photo-container img {
    display: block !important;
    visibility: visible !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}
CSS;

file_put_contents($cssDir . '/profile-path-fix.css', $cssContent);
echo "<p style='color:green;'>✅ Created CSS fix: css/profile-path-fix.css</p>";

// Step 5: Create bookmarklet
$bookmarklet = "javascript:(function(){var s=document.createElement('script');s.src='/js/profile-path-fix.js?v='+new Date().getTime();document.head.appendChild(s);var c=document.createElement('link');c.rel='stylesheet';c.href='/css/profile-path-fix.css?v='+new Date().getTime();document.head.appendChild(c);})();";

// Step 6: Check database for profile photo paths
try {
    // Try to connect to the database using environment variables
    require_once __DIR__ . '/../vendor/autoload.php';
    
    // Load .env file if it exists
    if (file_exists(__DIR__ . '/../.env')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
    }
    
    // Get database connection info from .env
    $dbConnection = new PDO(
        'mysql:host=' . ($_ENV['DB_HOST'] ?? 'localhost') . 
        ';dbname=' . ($_ENV['DB_DATABASE'] ?? 'ams'), 
        $_ENV['DB_USERNAME'] ?? 'root', 
        $_ENV['DB_PASSWORD'] ?? ''
    );
    
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check users table for profile_photo_path column
    $query = $dbConnection->query("SHOW COLUMNS FROM users LIKE 'profile_photo_path'");
    if ($query->rowCount() > 0) {
        echo "<p>Found profile_photo_path column in users table</p>";
        
        // Get users with profile photos
        $users = $dbConnection->query("SELECT id, name, profile_photo_path FROM users WHERE profile_photo_path IS NOT NULL AND profile_photo_path != ''")->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p>Found " . count($users) . " users with profile photos</p>";
        
        // Update paths in database to use public/profile-photos
        $updatedCount = 0;
        foreach ($users as $user) {
            $oldPath = $user['profile_photo_path'];
            
            // Skip if already using correct path
            if (strpos($oldPath, 'public/profile-photos/') === 0 || strpos($oldPath, 'profile-photos/') === 0) {
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
    } else {
        echo "<p style='color:orange;'>⚠️ Could not find profile_photo_path column in users table</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    echo "<p>Could not update database paths automatically. You may need to update them manually.</p>";
}

// Step 7: Update .htaccess to ensure profile photos are accessible
$htaccessPath = __DIR__ . '/.htaccess';
$htaccessContent = '';

if (file_exists($htaccessPath)) {
    $htaccessContent = file_get_contents($htaccessPath);
    
    // Check if we need to update .htaccess
    if (strpos($htaccessContent, 'profile-photos') === false) {
        // Add rule to allow access to profile photos
        $htaccessContent .= "\n\n# Allow access to profile photos\n";
        $htaccessContent .= "<Directory \"" . realpath($profileDir) . "\">\n";
        $htaccessContent .= "    Options Indexes FollowSymLinks\n";
        $htaccessContent .= "    AllowOverride All\n";
        $htaccessContent .= "    Require all granted\n";
        $htaccessContent .= "</Directory>\n";
        
        // Create backup
        file_put_contents($htaccessPath . '.backup', file_get_contents($htaccessPath));
        
        // Write updated .htaccess
        if (file_put_contents($htaccessPath, $htaccessContent)) {
            echo "<p style='color:green;'>✅ Updated .htaccess to allow access to profile photos</p>";
        } else {
            echo "<p style='color:red;'>❌ Failed to update .htaccess</p>";
        }
    } else {
        echo "<p>✓ .htaccess already has rules for profile photos</p>";
    }
} else {
    // Create new .htaccess
    $htaccessContent = "# Allow access to profile photos\n";
    $htaccessContent .= "<Directory \"" . realpath($profileDir) . "\">\n";
    $htaccessContent .= "    Options Indexes FollowSymLinks\n";
    $htaccessContent .= "    AllowOverride All\n";
    $htaccessContent .= "    Require all granted\n";
    $htaccessContent .= "</Directory>\n";
    
    if (file_put_contents($htaccessPath, $htaccessContent)) {
        echo "<p style='color:green;'>✅ Created .htaccess to allow access to profile photos</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create .htaccess</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile Photo Path Fix</title>
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
        h1 {
            color: #333;
        }
    </style>
</head>
<body>
    <h2>How to Use the Fix</h2>
    
    <h3>Option 1: Bookmarklet (Easiest)</h3>
    <p>Drag this link to your bookmarks bar, then click it when you're on the edit page:</p>
    <p><a href="<?php echo htmlspecialchars($bookmarklet); ?>" class="button">Fix Profile Photos</a></p>
    
    <h3>Option 2: Add to Your Layout</h3>
    <p>Add this code to your layout files just before the <code>&lt;/head&gt;</code> tag:</p>
    <div class="code">&lt;link rel="stylesheet" href="{{ asset('css/profile-path-fix.css') }}?v={{ time() }}"&gt;
&lt;script src="{{ asset('js/profile-path-fix.js') }}?v={{ time() }}"&gt;&lt;/script&gt;</div>
    
    <h3>Option 3: Test Now</h3>
    <p><a href="/admin/users" class="button">Go to Users Page</a></p>
    
    <h2>Summary of Changes</h2>
    <ol>
        <li>Created/verified public/profile-photos directory</li>
        <li>Copied profile photos from storage to public directory</li>
        <li>Created JavaScript and CSS fixes</li>
        <li>Updated database paths to use public/profile-photos/</li>
        <li>Updated .htaccess to ensure profile photos are accessible</li>
    </ol>
    
    <h2>Next Steps</h2>
    <ol>
        <li>Clear your browser cache</li>
        <li>Visit the users page to verify photos are displaying correctly</li>
        <li>If needed, use the bookmarklet on the edit page</li>
    </ol>
</body>
</html> 