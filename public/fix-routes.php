<?php
// This script manually clears Laravel route cache files without using the database

echo "<h1>Manual Route Cache Fix</h1>";

// Files to clear
$cacheFiles = [
    '../bootstrap/cache/routes-v7.php',
    '../bootstrap/cache/routes.php',
    '../bootstrap/cache/config.php',
    '../bootstrap/cache/services.php',
    '../bootstrap/cache/packages.php',
    '../storage/framework/cache/data/*.php'
];

// Log deleted files
$deletedFiles = [];
$failedFiles = [];

// Try to delete cache files
foreach ($cacheFiles as $pattern) {
    // Handle glob patterns
    if (strpos($pattern, '*') !== false) {
        $matchedFiles = glob($pattern);
        foreach ($matchedFiles as $file) {
            if (file_exists($file)) {
                if (@unlink($file)) {
                    $deletedFiles[] = $file;
                } else {
                    $failedFiles[] = $file;
                }
            }
        }
    } else {
        // Handle direct file paths
        if (file_exists($pattern)) {
            if (@unlink($pattern)) {
                $deletedFiles[] = $pattern;
            } else {
                $failedFiles[] = $pattern;
            }
        }
    }
}

// Display results
echo "<div style='margin: 20px 0; padding: 15px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 4px;'>";
echo "<h2>Route Cache Fix Results</h2>";

if (count($deletedFiles) > 0) {
    echo "<div style='margin-bottom: 15px; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px;'>";
    echo "<h3>Successfully Cleared:</h3>";
    echo "<ul>";
    foreach ($deletedFiles as $file) {
        echo "<li>" . htmlspecialchars($file) . "</li>";
    }
    echo "</ul>";
    echo "</div>";
}

if (count($failedFiles) > 0) {
    echo "<div style='margin-bottom: 15px; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;'>";
    echo "<h3>Failed to Clear:</h3>";
    echo "<ul>";
    foreach ($failedFiles as $file) {
        echo "<li>" . htmlspecialchars($file) . "</li>";
    }
    echo "</ul>";
    echo "</div>";
}

if (count($deletedFiles) === 0 && count($failedFiles) === 0) {
    echo "<div style='margin-bottom: 15px; padding: 10px; background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px;'>";
    echo "<h3>No cache files found to clear</h3>";
    echo "</div>";
}

echo "</div>";

// Instructions
echo "<div style='margin: 20px 0; padding: 15px; background-color: #e2f0fd; border: 1px solid #b8daff; border-radius: 4px;'>";
echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Restart your web server (Apache/Nginx) if possible</li>";
echo "<li>Return to the candidates page and try again</li>";
echo "<li>If you still encounter issues, check file permissions on the bootstrap/cache directory</li>";
echo "</ol>";
echo "</div>";

// Navigation
echo "<div style='margin-top: 20px;'>";
echo "<a href='/admin/election/candidates' style='display: inline-block; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;'>Return to Candidates</a>";
echo "</div>"; 