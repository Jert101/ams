<?php
// Basic DB connection info - modify if needed
$db_host = 'localhost';
$db_name = 'epiz_33769488_ams'; // Adjust if needed for InfinityFree
$db_user = 'epiz_33769488';     // Adjust if needed for InfinityFree
$db_pass = '';                  // Fill in your password

echo "<h1>Direct Database Fix Tool</h1>";
echo "<p>This tool directly manipulates the database to fix the auto_approve_candidates setting.</p>";

// Try to get connection info from Laravel .env file
$env_file = '../.env';
if (file_exists($env_file)) {
    $env_lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($env_lines as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            if ($key === 'DB_HOST') $db_host = $value;
            if ($key === 'DB_DATABASE') $db_name = $value;
            if ($key === 'DB_USERNAME') $db_user = $value;
            if ($key === 'DB_PASSWORD') $db_pass = $value;
        }
    }
}

echo "<div style='background-color: #f8f9fa; padding: 10px; border: 1px solid #ddd; margin-bottom: 20px;'>";
echo "<h3>Connection Information</h3>";
echo "<p>Host: " . htmlspecialchars($db_host) . "</p>";
echo "<p>Database: " . htmlspecialchars($db_name) . "</p>";
echo "<p>Username: " . htmlspecialchars($db_user) . "</p>";
echo "<p>Password: " . (empty($db_pass) ? "<span style='color:red'>Empty (problem!)</span>" : "<span style='color:green'>Set</span>") . "</p>";
echo "</div>";

// Handle direct fix
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        // Connect to the database
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if ($_POST['action'] === 'check_column') {
            // Check if column exists
            $stmt = $pdo->prepare("SHOW COLUMNS FROM election_settings LIKE 'auto_approve_candidates'");
            $stmt->execute();
            $column_exists = $stmt->fetch() !== false;
            
            if ($column_exists) {
                echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
                echo "✅ Column exists in the database";
                echo "</div>";
            } else {
                echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
                echo "❌ Column does not exist";
                echo "</div>";
                
                // Add the column
                $stmt = $pdo->prepare("ALTER TABLE election_settings ADD COLUMN auto_approve_candidates TINYINT(1) NOT NULL DEFAULT 0");
                $stmt->execute();
                
                echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
                echo "✅ Column has been added successfully";
                echo "</div>";
            }
        } elseif ($_POST['action'] === 'enable_auto_approve') {
            // Enable auto approval
            $stmt = $pdo->prepare("UPDATE election_settings SET auto_approve_candidates = 1");
            $rowCount = $stmt->execute();
            
            echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
            echo "✅ Auto-approval has been ENABLED for all settings records";
            echo "</div>";
        } elseif ($_POST['action'] === 'disable_auto_approve') {
            // Disable auto approval
            $stmt = $pdo->prepare("UPDATE election_settings SET auto_approve_candidates = 0");
            $rowCount = $stmt->execute();
            
            echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
            echo "✅ Auto-approval has been DISABLED for all settings records";
            echo "</div>";
        } elseif ($_POST['action'] === 'view_settings') {
            // View current settings
            $stmt = $pdo->prepare("SELECT * FROM election_settings");
            $stmt->execute();
            $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Current Settings</h3>";
            echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
            
            if (count($settings) > 0) {
                // Table header
                echo "<tr>";
                foreach (array_keys($settings[0]) as $column) {
                    echo "<th>" . htmlspecialchars($column) . "</th>";
                }
                echo "</tr>";
                
                // Table rows
                foreach ($settings as $row) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td>No settings found</td></tr>";
            }
            
            echo "</table>";
        }
    } catch (PDOException $e) {
        echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
        echo "❌ Database error: " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }
}

// Check for infinityfree specifics
$is_infinityfree = false;
if (strpos($db_host, 'infinityfree') !== false || strpos($db_host, 'epizy') !== false || strpos($db_name, 'epiz_') !== false) {
    $is_infinityfree = true;
    echo "<div style='background-color: #fff3cd; color: #856404; padding: 10px; border: 1px solid #ffeeba; margin: 15px 0;'>";
    echo "<h3>⚠️ InfinityFree Hosting Detected</h3>";
    echo "<p>Your database appears to be on InfinityFree hosting. Note that:</p>";
    echo "<ul>";
    echo "<li>InfinityFree databases must be accessed through their control panel</li>";
    echo "<li>Direct MySQL connections may be blocked</li>";
    echo "<li>Consider using the phpMyAdmin interface provided by InfinityFree instead</li>";
    echo "</ul>";
    echo "</div>";
}
?>

<div style="margin-top: 20px;">
    <h2>Database Tools</h2>
    
    <form method="post" style="margin-bottom: 15px; padding: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
        <h3>1. Check/Create Column</h3>
        <p>First, check if the auto_approve_candidates column exists and create it if missing:</p>
        <input type="hidden" name="action" value="check_column">
        <button type="submit" style="padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Check Column</button>
    </form>
    
    <form method="post" style="margin-bottom: 15px; padding: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
        <h3>2. View Current Settings</h3>
        <p>View the current settings in the database:</p>
        <input type="hidden" name="action" value="view_settings">
        <button type="submit" style="padding: 8px 15px; background-color: #17a2b8; color: white; border: none; border-radius: 4px; cursor: pointer;">View Settings</button>
    </form>
    
    <form method="post" style="margin-bottom: 15px; padding: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px;">
        <h3>3. Set Auto-Approval</h3>
        <p>Use these buttons to directly enable or disable auto-approval for all settings:</p>
        <div style="display: flex; gap: 10px;">
            <button type="submit" name="action" value="enable_auto_approve" style="padding: 8px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer;">Enable Auto-Approval</button>
            <button type="submit" name="action" value="disable_auto_approve" style="padding: 8px 15px; background-color: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">Disable Auto-Approval</button>
        </div>
    </form>
</div>

<div style="margin-top: 20px;">
    <a href="/admin/election" style="display: inline-block; padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px;">Return to Election Management</a>
</div> 