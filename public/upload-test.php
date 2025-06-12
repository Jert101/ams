<?php
// Simple file upload test for InfinityFree
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<h2>Upload Result</h2>';
    if (isset($_FILES['test_file']) && $_FILES['test_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!file_exists($uploadDir)) {
            if (mkdir($uploadDir, 0777, true)) {
                echo '<p>Created upload directory: ' . htmlspecialchars($uploadDir) . '</p>';
            } else {
                echo '<p style="color:red;">Failed to create upload directory!</p>';
            }
        }
        if (is_writable($uploadDir)) {
            $filename = time() . '-' . basename($_FILES['test_file']['name']);
            $destination = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['test_file']['tmp_name'], $destination)) {
                echo '<p style="color:green;">File uploaded successfully!</p>';
                echo '<p>Saved as: ' . htmlspecialchars($destination) . '</p>';
                echo '<img src="uploads/' . htmlspecialchars($filename) . '" style="max-width:200px; border:1px solid #ccc;">';
            } else {
                echo '<p style="color:red;">Failed to move uploaded file!</p>';
            }
        } else {
            echo '<p style="color:red;">Upload directory is not writable: ' . htmlspecialchars($uploadDir) . '</p>';
        }
    } else {
        echo '<p style="color:red;">';
        if (isset($_FILES['test_file'])) {
            echo 'Upload error code: ' . $_FILES['test_file']['error'];
        } else {
            echo 'No file uploaded.';
        }
        echo '</p>';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>File Upload Test</title>
    <style>body{font-family:sans-serif;max-width:500px;margin:40px auto;}input[type=file]{margin-bottom:10px;}</style>
</head>
<body>
<h1>File Upload Test (InfinityFree)</h1>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="test_file" required><br>
    <button type="submit">Upload</button>
</form>
<p>After uploading, check if the file appears below. If not, your host may block uploads or directory permissions are wrong.</p>
</body>
</html> 