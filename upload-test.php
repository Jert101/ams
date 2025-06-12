<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Upload Test</h1>";
echo "<pre>Upload directory: " . __DIR__ . "/uploads</pre>";

if (!file_exists(__DIR__ . "/uploads")) {
    if (mkdir(__DIR__ . "/uploads", 0777, true)) {
        echo "<p>Created uploads directory</p>";
    } else {
        echo "<p>Failed to create uploads directory</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>File upload details:</h2>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    if (isset($_FILES['test_file']) && $_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
        $uploadfile = __DIR__ . "/uploads/" . basename($_FILES['test_file']['name']);
        
        echo "<p>Attempting to move file from {$_FILES['test_file']['tmp_name']} to {$uploadfile}</p>";
        
        if (move_uploaded_file($_FILES['test_file']['tmp_name'], $uploadfile)) {
            echo "<p>File uploaded successfully to: $uploadfile</p>";
            echo "<p>File exists after upload: " . (file_exists($uploadfile) ? 'Yes' : 'No') . "</p>";
            echo "<p>File size: " . filesize($uploadfile) . " bytes</p>";
        } else {
            echo "<p>Failed to move uploaded file!</p>";
            echo "<p>Error: " . error_get_last()['message'] . "</p>";
            echo "<p>Temporary file exists: " . (file_exists($_FILES['test_file']['tmp_name']) ? 'Yes' : 'No') . "</p>";
        }
    }
}

// Show server information
echo "<h2>Server Information</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "file_uploads enabled: " . (ini_get('file_uploads') ? 'Yes' : 'No') . "\n";
echo "Current user: " . get_current_user() . "\n";
echo "Current script owner: " . fileowner(__FILE__) . "\n";
echo "</pre>";

// Check storage directories
echo "<h2>Storage Directories Check</h2>";
$storagePublic = __DIR__ . "/storage/profile-photos";
$storageApp = __DIR__ . "/storage/app/public/profile-photos";

echo "<pre>";
echo "Public storage path: $storagePublic\n";
echo "App storage path: $storageApp\n";
echo "Public directory exists: " . (file_exists($storagePublic) ? 'Yes' : 'No') . "\n";
echo "App directory exists: " . (file_exists($storageApp) ? 'Yes' : 'No') . "\n";
echo "Public directory writable: " . (is_writable($storagePublic) ? 'Yes' : 'No') . "\n";
echo "App directory writable: " . (is_writable($storageApp) ? 'Yes' : 'No') . "\n";
echo "</pre>";

// Check current directory structure
echo "<h2>Directory Structure</h2>";
echo "<pre>";
echo "Current directory: " . __DIR__ . "\n";
echo "Parent directory: " . dirname(__DIR__) . "\n";
echo "Files in current directory:\n";
$files = scandir(__DIR__);
print_r($files);
echo "</pre>";
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_file">
    <button type="submit">Upload</button>
</form> 