<?php
header('Content-Type: text/html');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Image Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .image-container { margin: 20px 0; border: 1px solid #ccc; padding: 10px; }
        img { max-width: 100px; height: auto; display: block; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Image Loading Test</h1>
    
    <div class="image-container">
        <h2>Direct Image (img/kofa.png)</h2>
        <img src="/img/kofa.png" alt="Direct Image">
        <p>Status: <span id="status1">Loading...</span></p>
    </div>
    
    <div class="image-container">
        <h2>Asset Image (asset function)</h2>
        <img src="<?php echo asset('img/kofa.png'); ?>" alt="Asset Image">
        <p>Status: <span id="status2">Loading...</span></p>
    </div>
    
    <div class="image-container">
        <h2>Profile Photos Directory</h2>
        <img src="/profile-photos/kofa.png" alt="Profile Photo">
        <p>Status: <span id="status3">Loading...</span></p>
    </div>
    
    <div class="image-container">
        <h2>Storage Link</h2>
        <img src="/storage/profile-photos/kofa.png" alt="Storage Image">
        <p>Status: <span id="status4">Loading...</span></p>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img');
            const statuses = [
                document.getElementById('status1'),
                document.getElementById('status2'),
                document.getElementById('status3'),
                document.getElementById('status4')
            ];
            
            images.forEach((img, index) => {
                img.onload = function() {
                    statuses[index].textContent = 'Loaded successfully';
                    statuses[index].style.color = 'green';
                };
                
                img.onerror = function() {
                    statuses[index].textContent = 'Failed to load';
                    statuses[index].style.color = 'red';
                };
                
                // Check if already loaded or failed
                if (img.complete) {
                    if (img.naturalWidth === 0) {
                        statuses[index].textContent = 'Failed to load';
                        statuses[index].style.color = 'red';
                    } else {
                        statuses[index].textContent = 'Loaded successfully';
                        statuses[index].style.color = 'green';
                    }
                }
            });
        });
    </script>
</body>
</html> 