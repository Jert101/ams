<?php
// This is a simplified fix script focused on ensuring profile picture uploads work

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set time limit to allow for longer operations
set_time_limit(300);

echo '<h1>Quick Storage Fix for Profile Pictures</h1>';

// Function to show messages
function show_msg($message, $is_error = false) {
    echo '<p style="' . ($is_error ? 'color:red;' : 'color:green;') . '">' . $message . '</p>';
}

// Step 1: Ensure storage directories exist with correct permissions
$directories = [
    'storage' => dirname(__DIR__) . '/storage',
    'storage_app' => dirname(__DIR__) . '/storage/app',
    'storage_app_public' => dirname(__DIR__) . '/storage/app/public',
    'profile_photos' => dirname(__DIR__) . '/storage/app/public/profile-photos',
];

echo '<h2>1. Creating Required Directories</h2>';
foreach ($directories as $name => $path) {
    if (!file_exists($path)) {
        if (mkdir($path, 0755, true)) {
            show_msg("Created directory: {$path}");
        } else {
            show_msg("Failed to create directory: {$path}", true);
        }
    } else {
        // Ensure correct permissions
        chmod($path, 0755);
        show_msg("Directory already exists: {$path}");
    }
}

// Step 2: Set up storage symlink or alternative
echo '<h2>2. Setting Up Storage Access</h2>';
$storageLink = __DIR__ . '/storage';
$storageTarget = dirname(__DIR__) . '/storage/app/public';

// Remove existing symlink/directory if it exists
if (file_exists($storageLink)) {
    if (is_link($storageLink)) {
        unlink($storageLink);
        show_msg("Removed existing symlink");
    } elseif (is_dir($storageLink)) {
        // Rename instead of delete to preserve any existing files
        $backup = $storageLink . '_backup_' . time();
        rename($storageLink, $backup);
        show_msg("Renamed existing directory to: {$backup}");
    } else {
        unlink($storageLink);
        show_msg("Removed existing file");
    }
}

// Try to create symlink
$symlink_created = false;
try {
    if (symlink($storageTarget, $storageLink)) {
        show_msg("Successfully created symlink: {$storageLink} -> {$storageTarget}");
        $symlink_created = true;
    } else {
        show_msg("Failed to create symlink using symlink() function", true);
    }
} catch (Exception $e) {
    show_msg("Exception while creating symlink: " . $e->getMessage(), true);
}

// If symlink creation failed, set up alternative access
if (!$symlink_created) {
    show_msg("Using alternative method for shared hosting...");
    
    // Create a regular directory
    if (!file_exists($storageLink)) {
        mkdir($storageLink, 0755);
    }
    
    // Create a PHP proxy script
    $storage_php = <<<'PHP'
<?php
// This is a proxy script for environments where symlinks aren't supported
$path = isset($_GET['path']) ? $_GET['path'] : null;

if (!$path) {
    http_response_code(404);
    echo "File not found";
    exit;
}

// Prevent directory traversal
$path = str_replace('..', '', $path);
$fullPath = __DIR__ . '/../storage/app/public/' . $path;

if (!file_exists($fullPath)) {
    http_response_code(404);
    echo "File not found";
    exit;
}

// Get file info and serve
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $fullPath);
finfo_close($finfo);

header('Content-Type: ' . $mime);
readfile($fullPath);
PHP;
    
    file_put_contents(__DIR__ . '/storage.php', $storage_php);
    show_msg("Created storage.php proxy script");
    
    // Update .htaccess
    $htaccess_rule = "
# Storage proxy rules
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^storage/(.*)$ storage.php?path=$1 [L]
</IfModule>
";
    
    // Add to .htaccess or create new one
    $htaccess_path = __DIR__ . '/.htaccess';
    if (file_exists($htaccess_path)) {
        file_put_contents($htaccess_path, $htaccess_rule, FILE_APPEND);
    } else {
        file_put_contents($htaccess_path, $htaccess_rule);
    }
    show_msg("Updated .htaccess with storage rules");
}

// Step 3: Test file creation and access
echo '<h2>3. Testing File Access</h2>';
$test_file = $storageTarget . '/test-' . time() . '.txt';
$test_content = 'Test file created on ' . date('Y-m-d H:i:s');

try {
    if (file_put_contents($test_file, $test_content)) {
        show_msg("Created test file: " . basename($test_file));
        
        // Test access
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $test_url = $protocol . $host . '/storage/' . basename($test_file);
        
        echo "<p>Test the file access by clicking: <a href='{$test_url}' target='_blank'>{$test_url}</a></p>";
    } else {
        show_msg("Failed to create test file", true);
    }
} catch (Exception $e) {
    show_msg("Error creating test file: " . $e->getMessage(), true);
}

// Step 4: Attempt to create a test profile photo
echo '<h2>4. Testing Profile Photo Creation</h2>';

// Create a 1x1 pixel GIF image
$pixel_gif = base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==');
$test_photo_path = $storageTarget . '/profile-photos/test-pixel-' . time() . '.gif';

try {
    if (file_put_contents($test_photo_path, $pixel_gif)) {
        show_msg("Created test profile photo: " . basename($test_photo_path));
        
        // Test access
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $photo_url = $protocol . $host . '/storage/profile-photos/' . basename($test_photo_path);
        
        echo "<p>Test the photo access by clicking: <a href='{$photo_url}' target='_blank'>{$photo_url}</a></p>";
    } else {
        show_msg("Failed to create test profile photo", true);
    }
} catch (Exception $e) {
    show_msg("Error creating test profile photo: " . $e->getMessage(), true);
}

// Step 5: Create direct file upload test
echo '<h2>5. Test Upload Form</h2>';
echo '<p>Use this form to test if direct file uploads work:</p>';
echo '<form action="" method="post" enctype="multipart/form-data">';
echo '<input type="file" name="test_upload" accept="image/*"><br><br>';
echo '<input type="submit" name="upload" value="Test Upload">';
echo '</form>';

if (isset($_POST['upload']) && isset($_FILES['test_upload'])) {
    echo '<h3>Upload Results:</h3>';
    
    if ($_FILES['test_upload']['error'] !== UPLOAD_ERR_OK) {
        show_msg("Upload error: " . $_FILES['test_upload']['error'], true);
    } else {
        show_msg("File uploaded to temporary location: " . $_FILES['test_upload']['tmp_name']);
        
        // Try to save to profile-photos directory
        $target_file = $storageTarget . '/profile-photos/' . basename($_FILES['test_upload']['name']);
        
        if (move_uploaded_file($_FILES['test_upload']['tmp_name'], $target_file)) {
            show_msg("Successfully saved file to: " . $target_file);
            
            // Show URL
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $file_url = $protocol . $host . '/storage/profile-photos/' . basename($_FILES['test_upload']['name']);
            
            echo "<p>View uploaded file: <a href='{$file_url}' target='_blank'>{$file_url}</a></p>";
        } else {
            show_msg("Failed to save uploaded file", true);
            show_msg("PHP error: " . error_get_last()['message'], true);
        }
    }
}

// Final instructions
echo '<h2>What to do next</h2>';
echo '<ol>';
echo '<li>Try accessing the test files using the links above. If they work, the storage setup is working.</li>';
echo '<li>If you can upload an image using the form above but profile pictures still don\'t work in the admin panel, there may be a Laravel-specific issue.</li>';
echo '<li>Try clearing the Laravel cache with: <code>php artisan cache:clear</code></li>';
echo '<li>Restart your web server if possible</li>';
echo '<li>If issues persist, try the <a href="/profile-picture-test.php">comprehensive diagnostic tool</a> for more detailed tests.</li>';
echo '</ol>';
?> 