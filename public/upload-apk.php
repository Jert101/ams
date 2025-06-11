<?php
// APK Upload Script
// This script allows you to manually upload the APK file directly to the server

// Set a password to protect the upload
$password = "ckpkofa2023";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify password
    if (!isset($_POST['password']) || $_POST['password'] !== $password) {
        $errorMsg = "Incorrect password. Please try again.";
    } else {
        // Check if file was uploaded
        if (isset($_FILES['apk_file']) && $_FILES['apk_file']['error'] === UPLOAD_ERR_OK) {
            $uploadedFile = $_FILES['apk_file']['tmp_name'];
            
            // Save to multiple locations to ensure it's available
            $destinations = [
                __DIR__ . '/base.apk',
                __DIR__ . '/ckp-kofa-app.apk'
            ];
            
            // Create mobile/apk directory if it doesn't exist
            if (!is_dir(__DIR__ . '/mobile/apk')) {
                mkdir(__DIR__ . '/mobile/apk', 0755, true);
            }
            
            // Add mobile/apk directory destinations
            $destinations[] = __DIR__ . '/mobile/apk/base.apk';
            $destinations[] = __DIR__ . '/mobile/apk/ckp-kofa-app.apk';
            
            $successCount = 0;
            $errors = [];
            
            // Copy to all destinations
            foreach ($destinations as $destination) {
                if (copy($uploadedFile, $destination)) {
                    $successCount++;
                } else {
                    $errors[] = "Failed to copy to: " . $destination;
                }
            }
            
            if ($successCount > 0) {
                $successMsg = "APK file uploaded successfully to $successCount locations.";
            } else {
                $errorMsg = "Failed to upload APK file to any location. Check permissions.";
            }
        } else {
            $errorMsg = "Error uploading file. Error code: " . $_FILES['apk_file']['error'];
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload APK File</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="password"], input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #218838;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Upload APK File</h1>
    
    <div class="card">
        <?php if (isset($successMsg)): ?>
            <p class="success"><?php echo $successMsg; ?></p>
            <p>You can now download the APK using one of these links:</p>
            <ul>
                <li><a href="base.apk">base.apk</a></li>
                <li><a href="mobile/apk/base.apk">mobile/apk/base.apk</a></li>
                <li><a href="infinity.php?download=1">Download via script</a></li>
            </ul>
        <?php endif; ?>
        
        <?php if (isset($errorMsg)): ?>
            <p class="error"><?php echo $errorMsg; ?></p>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="apk_file">APK File:</label>
                <input type="file" id="apk_file" name="apk_file" accept=".apk" required>
            </div>
            
            <button type="submit" class="btn">Upload APK</button>
        </form>
    </div>
    
    <div class="info-box">
        <h2>Server Information</h2>
        <p>Server Software: <?php echo $_SERVER['SERVER_SOFTWARE']; ?></p>
        <p>PHP Version: <?php echo phpversion(); ?></p>
        <p>Current Directory: <?php echo __DIR__; ?></p>
        <p>Document Root: <?php echo $_SERVER['DOCUMENT_ROOT']; ?></p>
        
        <h3>File Status Check</h3>
        <ul>
            <?php
            $checkFiles = [
                __DIR__ . '/base.apk',
                __DIR__ . '/ckp-kofa-app.apk',
                __DIR__ . '/mobile/apk/base.apk',
                __DIR__ . '/mobile/apk/ckp-kofa-app.apk'
            ];
            
            foreach ($checkFiles as $file) {
                echo '<li>' . $file . ': ' . (file_exists($file) ? 'EXISTS (' . filesize($file) . ' bytes)' : 'NOT FOUND') . '</li>';
            }
            ?>
        </ul>
    </div>
    
    <p><a href="/">Return to Home</a></p>
</body>
</html> 