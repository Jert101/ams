<?php
// Detailed diagnostic script to examine database structure

// Prevent timeouts
set_time_limit(300);

// Set database connection parameters - update these with your InfinityFree credentials
$host = 'sql309.infinityfree.com'; // Update with your actual host
$db = 'if0_38972693_ams'; // Updated to match your database name from error message
$user = 'if0_38972693'; // Update with your username
$password = 'YOUR_PASSWORD_HERE'; // Replace with your actual password

// Output as plain text for better readability
header('Content-Type: text/plain');

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "===== DATABASE DIAGNOSTIC REPORT =====\n\n";
    
    // ===== Examine users table structure =====
    echo "USERS TABLE STRUCTURE:\n";
    echo "------------------------\n";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        $primaryKey = ($column['Key'] == 'PRI') ? ' (PRIMARY KEY)' : '';
        echo "{$column['Field']} - {$column['Type']}{$primaryKey}\n";
    }
    
    // ===== Examine election_candidates table structure =====
    echo "\nELECTION_CANDIDATES TABLE STRUCTURE:\n";
    echo "------------------------------------\n";
    $stmt = $pdo->query("DESCRIBE election_candidates");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        $primaryKey = ($column['Key'] == 'PRI') ? ' (PRIMARY KEY)' : '';
        echo "{$column['Field']} - {$column['Type']}{$primaryKey}\n";
    }
    
    // ===== Examine foreign keys =====
    echo "\nFOREIGN KEY CONSTRAINTS:\n";
    echo "-----------------------\n";
    $stmt = $pdo->query("
        SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE REFERENCED_TABLE_SCHEMA = '$db'
          AND TABLE_NAME = 'election_candidates'
    ");
    $foreignKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($foreignKeys as $fk) {
        echo "Table {$fk['TABLE_NAME']} column {$fk['COLUMN_NAME']} references {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}\n";
    }
    
    // ===== List sample data from users table =====
    echo "\nSAMPLE DATA FROM USERS TABLE (10 records):\n";
    echo "----------------------------------------\n";
    $stmt = $pdo->query("SELECT id, user_id, name, email FROM users LIMIT 10");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $user) {
        echo "ID: {$user['id']}, User ID: {$user['user_id']}, Name: {$user['name']}, Email: {$user['email']}\n";
    }
    
    // ===== List all data from election_candidates table =====
    echo "\nALL DATA FROM ELECTION_CANDIDATES TABLE:\n";
    echo "--------------------------------------\n";
    $stmt = $pdo->query("SELECT id, user_id, position_id, status FROM election_candidates");
    $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($candidates as $candidate) {
        echo "ID: {$candidate['id']}, User ID: {$candidate['user_id']}, Position ID: {$candidate['position_id']}, Status: {$candidate['status']}\n";
    }
    
    // ===== Check which user IDs exist =====
    echo "\nVALIDATING USER IDS:\n";
    echo "------------------\n";
    foreach ($candidates as $candidate) {
        $userId = $candidate['user_id'];
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "User ID {$userId} exists in users table ✓\n";
        } else {
            echo "User ID {$userId} DOES NOT EXIST in users table ✗\n";
            
            // Suggest possible matches
            $stmt = $pdo->query("SELECT id, user_id, name FROM users LIMIT 20");
            $potentialUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "  Possible matches in users table:\n";
            foreach ($potentialUsers as $potUser) {
                echo "  - ID: {$potUser['id']}, User ID: {$potUser['user_id']}, Name: {$potUser['name']}\n";
            }
        }
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} 