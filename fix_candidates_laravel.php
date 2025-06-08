<?php
// Web-based script to update election_candidates user_ids that uses Laravel .env file

// Prevent timeouts
set_time_limit(300);

// Function to parse Laravel .env file
function parseEnvFile($path) {
    if (!file_exists($path)) {
        return [];
    }
    
    $env = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0 || empty(trim($line))) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Strip quotes if present
        if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
            $value = substr($value, 1, -1);
        }
        
        $env[$name] = $value;
    }
    
    return $env;
}

// Load Laravel environment variables
$envPath = __DIR__ . '/.env';
$env = parseEnvFile($envPath);

// Set database connection parameters from Laravel .env
$host = $env['DB_HOST'] ?? 'localhost';
$port = $env['DB_PORT'] ?? '3306';
$db = $env['DB_DATABASE'] ?? 'ams';
$user = $env['DB_USERNAME'] ?? 'root';
$password = $env['DB_PASSWORD'] ?? 'YOUR_PASSWORD_HERE'; // IMPORTANT: Replace with your actual InfinityFree password!

// Output as plain text for better readability
header('Content-Type: text/plain');

// Only run if explicitly requested
$action = $_GET['action'] ?? '';

echo "AMS Election Candidate Database Fixer (Laravel Edition)\n";
echo "====================================================\n\n";

if ($action != 'run' && $action != 'view') {
    echo "USAGE INSTRUCTIONS:\n";
    echo "1. First EDIT THIS FILE to add your correct database password on line 43\n";
    echo "2. Add '?action=view' to the URL to view current candidate and user data\n";
    echo "3. Add '?action=run' to the URL to perform the updates\n";
    echo "4. Make sure to update the user ID mappings in the code before running\n";
    exit;
}

echo "Using database connection:\n";
echo "Host: $host\n";
echo "Database: $db\n";
echo "Username: $user\n";
echo "Password: " . (empty($password) ? "(empty)" : "(set)") . "\n\n";

try {
    // Connect to database
    $dsn = "mysql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Database connection successful\n\n";
    
    // Define the user ID mapping from wrong IDs to correct IDs
    // Format: old_user_id => users.id (NOT users.user_id)
    // IMPORTANT: Use the ID values from the "ID" column, not the "User ID" column!
    $userIdMapping = [
        5 => 1,  // Replace with actual users.id values
        6 => 2,  // Replace with actual users.id values
        7 => 3,  // Replace with actual users.id values
        28 => 4  // Replace with actual users.id values
    ];
    
    // VIEW MODE - Just show the data
    if ($action == 'view') {
        // Get current candidates
        $stmt = $pdo->query("SELECT id, user_id, position_id, status FROM election_candidates");
        $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "ELECTION CANDIDATES:\n";
        echo "--------------------\n";
        foreach ($candidates as $candidate) {
            echo "Candidate ID: {$candidate['id']}, User ID: {$candidate['user_id']}, ";
            echo "Position ID: {$candidate['position_id']}, Status: {$candidate['status']}\n";
        }
        
        echo "\n\n";
        
        // Get users
        $stmt = $pdo->query("SELECT id, user_id, name, email FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "USERS LIST:\n";
        echo "----------\n";
        foreach ($users as $user) {
            echo "ID: {$user['id']}, User ID: {$user['user_id']}, Name: {$user['name']}, Email: {$user['email']}\n";
        }
        
        echo "\n\n";
        echo "IMPORTANT NOTE:\n";
        echo "The election_candidates.user_id column must reference the users.id column (not users.user_id).\n";
        echo "Make sure your mappings use the correct ID values from the 'ID' column above, not the 'User ID' column.\n\n";
        echo "STEPS TO FIX:\n";
        echo "1. Look at the candidate list and user list above\n";
        echo "2. Edit this PHP file and update the \$userIdMapping array with the correct mappings\n";
        echo "3. Add '?action=run' to the URL to perform the updates\n";
    }
    
    // RUN MODE - Perform the updates
    if ($action == 'run') {
        // Get current values before updating
        $stmt = $pdo->query("SELECT id, user_id FROM election_candidates");
        $currentCandidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Current candidates before update:\n";
        foreach ($currentCandidates as $candidate) {
            echo "ID: {$candidate['id']}, User ID: {$candidate['user_id']}\n";
        }
        
        // Update each candidate user_id
        echo "\nUpdating candidates...\n";
        foreach ($userIdMapping as $oldUserId => $newUserId) {
            $stmt = $pdo->prepare("UPDATE election_candidates SET user_id = ? WHERE user_id = ?");
            $result = $stmt->execute([$newUserId, $oldUserId]);
            
            if ($result) {
                echo "Updated user_id $oldUserId to $newUserId successfully\n";
            } else {
                echo "Failed to update user_id $oldUserId\n";
            }
        }
        
        // Verify updates
        $stmt = $pdo->query("SELECT id, user_id FROM election_candidates");
        $updatedCandidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nUpdated candidates:\n";
        foreach ($updatedCandidates as $candidate) {
            echo "ID: {$candidate['id']}, User ID: {$candidate['user_id']}\n";
        }
    }

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} 