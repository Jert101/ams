<?php
// Move profile photos to public/profile-photos/ directory and update database
// This script ensures all profile photos are in the correct location

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Move Profile Photos to Public Directory</h1>";

// Step 1: Ensure public/profile-photos directory exists
$targetDir = __DIR__ . '/public/profile-photos';
if (!file_exists($targetDir)) {
    if (mkdir($targetDir, 0755, true)) {
        echo "<p style='color:green;'>✅ Created directory: public/profile-photos</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create directory: public/profile-photos</p>";
        exit;
    }
} else {
    echo "<p>✓ Directory exists: public/profile-photos</p>";
}

// Step 2: Connect to database
try {
    // Try to connect to the database using environment variables
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Load .env file if it exists
    if (file_exists(__DIR__ . '/.env')) {
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
    
    // Step 3: Get users with profile photos
    $users = $dbConnection->query("SELECT id, name, profile_photo_path FROM users WHERE profile_photo_path IS NOT NULL AND profile_photo_path != ''")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Found " . count($users) . " users with profile photos</p>";
    
    // Step 4: Process each user's photo
    $movedCount = 0;
    $updatedCount = 0;
    
    foreach ($users as $user) {
        $oldPath = $user['profile_photo_path'];
        echo "<hr>";
        echo "<h3>Processing user: {$user['name']}</h3>";
        echo "<p>Current path: $oldPath</p>";
        
        // Skip if already using correct path format
        if (strpos($oldPath, 'profile-photos/') === 0) {
            echo "<p>Path already in correct format</p>";
            
            // Check if file exists in public directory
            $filename = basename($oldPath);
            $publicFilePath = $targetDir . '/' . $filename;
            
            if (file_exists($publicFilePath)) {
                echo "<p style='color:green;'>✅ File already exists in public directory: $filename</p>";
            } else {
                echo "<p style='color:orange;'>⚠️ File does not exist in public directory: $filename</p>";
                
                // Look for the file in storage directory
                $storageFilePath = __DIR__ . '/storage/app/public/profile-photos/' . $filename;
                if (file_exists($storageFilePath)) {
                    if (copy($storageFilePath, $publicFilePath)) {
                        echo "<p style='color:green;'>✅ Copied file from storage to public: $filename</p>";
                        $movedCount++;
                    } else {
                        echo "<p style='color:red;'>❌ Failed to copy file from storage to public: $filename</p>";
                    }
                } else {
                    // Try to find the file in other locations
                    $possiblePaths = [
                        __DIR__ . '/storage/app/' . $oldPath,
                        __DIR__ . '/' . $oldPath,
                        __DIR__ . '/storage/' . $oldPath
                    ];
                    
                    $found = false;
                    foreach ($possiblePaths as $path) {
                        if (file_exists($path)) {
                            if (copy($path, $publicFilePath)) {
                                echo "<p style='color:green;'>✅ Copied file from $path to public: $filename</p>";
                                $movedCount++;
                                $found = true;
                                break;
                            } else {
                                echo "<p style='color:red;'>❌ Failed to copy file from $path to public: $filename</p>";
                            }
                        }
                    }
                    
                    if (!$found) {
                        echo "<p style='color:red;'>❌ Could not find file in any location: $filename</p>";
                        
                        // Generate a placeholder image with user's initials
                        $initials = generateInitials($user['name']);
                        if (generatePlaceholderImage($publicFilePath, $initials)) {
                            echo "<p style='color:green;'>✅ Generated placeholder image with initials: $initials</p>";
                            $movedCount++;
                        } else {
                            echo "<p style='color:red;'>❌ Failed to generate placeholder image</p>";
                        }
                    }
                }
            }
            
            continue;
        }
        
        // Extract filename from path
        $filename = basename($oldPath);
        $newPath = 'profile-photos/' . $filename;
        
        echo "<p>New path will be: $newPath</p>";
        
        // Look for the file in various locations
        $foundFile = false;
        $sourcePaths = [
            __DIR__ . '/storage/app/public/' . $oldPath,
            __DIR__ . '/storage/app/' . $oldPath,
            __DIR__ . '/' . $oldPath,
            __DIR__ . '/storage/' . $oldPath
        ];
        
        foreach ($sourcePaths as $sourcePath) {
            if (file_exists($sourcePath)) {
                echo "<p>Found file at: $sourcePath</p>";
                
                // Copy file to public directory
                if (copy($sourcePath, $targetDir . '/' . $filename)) {
                    echo "<p style='color:green;'>✅ Copied file to public directory</p>";
                    $movedCount++;
                    $foundFile = true;
                    break;
                } else {
                    echo "<p style='color:red;'>❌ Failed to copy file to public directory</p>";
                }
            }
        }
        
        // If file not found, generate a placeholder
        if (!$foundFile) {
            echo "<p style='color:orange;'>⚠️ Could not find original file in any location</p>";
            
            // Generate a placeholder image with user's initials
            $initials = generateInitials($user['name']);
            if (generatePlaceholderImage($targetDir . '/' . $filename, $initials)) {
                echo "<p style='color:green;'>✅ Generated placeholder image with initials: $initials</p>";
                $movedCount++;
            } else {
                echo "<p style='color:red;'>❌ Failed to generate placeholder image</p>";
            }
        }
        
        // Update database
        $stmt = $dbConnection->prepare("UPDATE users SET profile_photo_path = ? WHERE id = ?");
        if ($stmt->execute([$newPath, $user['id']])) {
            echo "<p style='color:green;'>✅ Updated database path for user {$user['name']}</p>";
            $updatedCount++;
        } else {
            echo "<p style='color:red;'>❌ Failed to update database path for user {$user['name']}</p>";
        }
    }
    
    echo "<hr>";
    echo "<h2>Summary</h2>";
    echo "<p>Moved/Generated $movedCount profile photos to public directory</p>";
    echo "<p>Updated $updatedCount database records</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Function to generate initials from a name
function generateInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    
    foreach ($words as $word) {
        if (!empty($word[0])) {
            $initials .= strtoupper($word[0]);
        }
    }
    
    return $initials;
}

// Function to generate a placeholder image with initials
function generatePlaceholderImage($path, $initials) {
    // Create a 100x100 image
    $image = imagecreatetruecolor(100, 100);
    
    // Colors
    $bgColor = imagecolorallocate($image, 240, 240, 240); // Light gray background
    $textColor = imagecolorallocate($image, 50, 50, 50);  // Dark gray text
    
    // Fill the background
    imagefill($image, 0, 0, $bgColor);
    
    // Add text
    $font = 5; // Built-in font
    $textWidth = imagefontwidth($font) * strlen($initials);
    $textHeight = imagefontheight($font);
    
    // Center the text
    $x = (100 - $textWidth) / 2;
    $y = (100 - $textHeight) / 2 + $textHeight;
    
    // Draw the text
    imagestring($image, $font, $x, $y - 10, $initials, $textColor);
    
    // Save the image
    $result = imagejpeg($image, $path);
    
    // Free memory
    imagedestroy($image);
    
    return $result;
}

// Create a simple JavaScript fix for the edit page
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
        
        profileSections.forEach(function(section) {
            // Find the container
            var parent = section.parentElement;
            var container = parent.querySelector(".profile-photo-container");
            
            if (container) {
                // Check for existing images
                var img = container.querySelector("img");
                if (img) {
                    // Make sure image is visible
                    img.style.display = "block";
                    img.style.visibility = "visible";
                    img.style.width = "100%";
                    img.style.height = "100%";
                    img.style.objectFit = "cover";
                    
                    // Add error handler
                    img.onerror = function() {
                        this.src = "/img/kofa.png";
                    };
                } else {
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

$jsDir = __DIR__ . '/public/js';
if (!file_exists($jsDir)) {
    mkdir($jsDir, 0755, true);
}

file_put_contents($jsDir . '/profile-photo-fix.js', $jsContent);

// Create bookmarklet
$bookmarklet = "javascript:(function(){var s=document.createElement('script');s.src='/js/profile-photo-fix.js?v='+new Date().getTime();document.head.appendChild(s);})();";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Move Profile Photos</title>
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
        h1, h2, h3 {
            color: #333;
        }
    </style>
</head>
<body>
    <h2>Next Steps</h2>
    
    <p>All profile photos have been moved to the public/profile-photos/ directory and the database has been updated.</p>
    
    <h3>Fix for Edit Page</h3>
    <p>Drag this link to your bookmarks bar, then click it when you're on the edit page:</p>
    <p><a href="<?php echo htmlspecialchars($bookmarklet); ?>" class="button">Fix Profile Photos</a></p>
    
    <p><a href="/admin/users" class="button">Go to Users Page</a></p>
</body>
</html> 