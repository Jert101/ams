<?php
// Direct fix script to handle foreign key constraints

// Prevent timeouts
set_time_limit(300);

// Set database connection parameters - update these with your InfinityFree credentials
$host = 'sql309.infinityfree.com'; // Update with your actual host
$db = 'if0_38972693_ams'; // Updated to match your database name from error message
$user = 'if0_38972693'; // Update with your username
$password = 'YOUR_PASSWORD_HERE'; // Replace with your actual password

// Output as plain text for better readability
header('Content-Type: text/plain');

// Action parameter
$action = $_GET['action'] ?? '';

if (!in_array($action, ['view', 'fix', 'delete'])) {
    echo "DIRECT FIX SCRIPT\n";
    echo "================\n\n";
    echo "This script will help resolve the foreign key constraint issue.\n\n";
    echo "OPTIONS:\n";
    echo "1. ?action=view - View the current data and issues\n";
    echo "2. ?action=fix - Fix the issue by assigning valid user IDs\n";
    echo "3. ?action=delete - Delete problematic candidate entries\n";
    exit;
}

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully.\n\n";
    
    // VIEW MODE - Identify issues
    if ($action == 'view') {
        // Get all candidates
        $stmt = $pdo->query("SELECT id, user_id, position_id, status FROM election_candidates");
        $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "ELECTION CANDIDATES:\n";
        echo "-------------------\n";
        foreach ($candidates as $candidate) {
            echo "ID: {$candidate['id']}, User ID: {$candidate['user_id']}, Position: {$candidate['position_id']}, Status: {$candidate['status']}\n";
        }
        
        echo "\n";
        
        // Check foreign key validity
        echo "FOREIGN KEY VALIDATION:\n";
        echo "----------------------\n";
        
        $invalidCandidates = [];
        
        foreach ($candidates as $candidate) {
            $userId = $candidate['user_id'];
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                echo "Candidate {$candidate['id']} with user_id {$userId} is valid ✓\n";
            } else {
                echo "Candidate {$candidate['id']} with user_id {$userId} is INVALID ✗\n";
                $invalidCandidates[] = $candidate;
            }
        }
        
        // If we have invalid candidates, show valid users
        if (!empty($invalidCandidates)) {
            echo "\n";
            echo "AVAILABLE VALID USERS:\n";
            echo "--------------------\n";
            
            $stmt = $pdo->query("SELECT id, user_id, name, email FROM users LIMIT 30");
            $validUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($validUsers as $user) {
                echo "ID: {$user['id']}, User ID: {$user['user_id']}, Name: {$user['name']}, Email: {$user['email']}\n";
            }
            
            echo "\n";
            echo "TO FIX THE ISSUE:\n";
            echo "1. Update the 'directFixMapping' array in this script\n";
            echo "2. Access this script with ?action=fix\n";
        }
    }
    
    // FIX MODE - Update candidates with valid user IDs
    if ($action == 'fix') {
        // Define direct mappings from candidate IDs to valid user IDs
        // Format: candidate_id => valid_user_id
        $directFixMapping = [
            // EXAMPLE: 1 => 5,  // This will set candidate with ID 1 to have user_id 5
            // Add your actual mappings here based on the data you see in view mode
        ];
        
        if (empty($directFixMapping)) {
            echo "ERROR: No mappings defined. Please edit the script and add mappings to the \$directFixMapping array.\n";
            exit;
        }
        
        echo "UPDATING CANDIDATES:\n";
        echo "-------------------\n";
        
        foreach ($directFixMapping as $candidateId => $newUserId) {
            // Verify user ID exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
            $stmt->execute([$newUserId]);
            $userExists = $stmt->fetchColumn() > 0;
            
            if (!$userExists) {
                echo "ERROR: User ID {$newUserId} does not exist in the users table.\n";
                continue;
            }
            
            // Update the candidate
            $stmt = $pdo->prepare("UPDATE election_candidates SET user_id = ? WHERE id = ?");
            $result = $stmt->execute([$newUserId, $candidateId]);
            
            if ($result) {
                echo "Successfully updated candidate ID {$candidateId} to use user_id {$newUserId} ✓\n";
            } else {
                echo "Failed to update candidate ID {$candidateId} ✗\n";
            }
        }
        
        echo "\nDONE.\n";
    }
    
    // DELETE MODE - Remove problematic entries
    if ($action == 'delete') {
        // Define candidate IDs to delete
        $candidatesToDelete = [
            // Add candidate IDs to delete
            // Example: 1, 2, 3
        ];
        
        if (empty($candidatesToDelete)) {
            echo "ERROR: No candidates specified for deletion. Please edit the script.\n";
            exit;
        }
        
        echo "DELETING CANDIDATES:\n";
        echo "-------------------\n";
        
        foreach ($candidatesToDelete as $candidateId) {
            $stmt = $pdo->prepare("DELETE FROM election_candidates WHERE id = ?");
            $result = $stmt->execute([$candidateId]);
            
            if ($result) {
                echo "Successfully deleted candidate ID {$candidateId} ✓\n";
            } else {
                echo "Failed to delete candidate ID {$candidateId} ✗\n";
            }
        }
        
        echo "\nDONE.\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
} 