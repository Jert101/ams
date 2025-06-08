<?php
// Web-based script with UI to update election_candidates user_ids

// Process form submission
$message = '';
$candidates = [];
$users = [];

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

// Try to load Laravel environment variables
$envPath = __DIR__ . '/.env';
$env = parseEnvFile($envPath);

// Default connection parameters for InfinityFree
$host = $env['DB_HOST'] ?? 'sql309.infinityfree.com'; // Default InfinityFree host
$port = $env['DB_PORT'] ?? '3306';
$db = $env['DB_DATABASE'] ?? 'if0_36177017_ams'; // Replace with your database name
$user = $env['DB_USERNAME'] ?? 'if0_36177017'; // Replace with your username
$password = $env['DB_PASSWORD'] ?? ''; // You'll need to enter your password in the form

// Override with form values if provided
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['connection'])) {
        $host = $_POST['host'] ?? $host;
        $port = $_POST['port'] ?? $port;
        $db = $_POST['database'] ?? $db;
        $user = $_POST['username'] ?? $user;
        $password = $_POST['password'] ?? $password;
    }
    
    // Process update if requested
    if (isset($_POST['update']) && !empty($_POST['mappings'])) {
        try {
            // Connect to database
            $dsn = "mysql:host=$host;port=$port;dbname=$db";
            $pdo = new PDO($dsn, $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $mappings = $_POST['mappings'];
            $updateCount = 0;
            
            foreach ($mappings as $candidateId => $newUserId) {
                if (!empty($newUserId)) {
                    $stmt = $pdo->prepare("UPDATE election_candidates SET user_id = ? WHERE id = ?");
                    $result = $stmt->execute([$newUserId, $candidateId]);
                    
                    if ($result) {
                        $updateCount++;
                    }
                }
            }
            
            $message = "Successfully updated $updateCount candidate(s)!";
            
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}

// Connect to database and fetch data
$dbConnected = false;
if (!empty($host) && !empty($db) && !empty($user)) {
    try {
        // Connect to database
        $dsn = "mysql:host=$host;port=$port;dbname=$db";
        $pdo = new PDO($dsn, $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbConnected = true;
        
        // Get current candidates
        $stmt = $pdo->query("SELECT id, user_id, position_id, status FROM election_candidates");
        $candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get users
        $stmt = $pdo->query("SELECT user_id, name, email FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $message = "Database connection error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMS Election Candidate Fixer</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        .card {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .error {
            background-color: #ffebee;
            color: #c62828;
        }
        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        input[type="text"], input[type="password"] {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
    </style>
</head>
<body>
    <h1>AMS Election Candidate Fixer</h1>
    
    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'error') !== false || strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <h2>Database Connection</h2>
        <form method="post">
            <div class="grid">
                <div>
                    <label for="host">Host:</label>
                    <input type="text" id="host" name="host" value="<?php echo htmlspecialchars($host); ?>" required>
                </div>
                <div>
                    <label for="port">Port:</label>
                    <input type="text" id="port" name="port" value="<?php echo htmlspecialchars($port); ?>">
                </div>
                <div>
                    <label for="database">Database Name:</label>
                    <input type="text" id="database" name="database" value="<?php echo htmlspecialchars($db); ?>" required>
                </div>
                <div>
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user); ?>" required>
                </div>
                <div>
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>">
                </div>
            </div>
            <br>
            <button type="submit" name="connection">Connect to Database</button>
        </form>
    </div>
    
    <?php if ($dbConnected): ?>
        <div class="card">
            <h2>Update Candidate User IDs</h2>
            <p>Below are all election candidates with their current user IDs. Select the correct user ID for each candidate from the dropdown menu.</p>
            
            <form method="post">
                <!-- Hidden fields to maintain connection info -->
                <input type="hidden" name="host" value="<?php echo htmlspecialchars($host); ?>">
                <input type="hidden" name="port" value="<?php echo htmlspecialchars($port); ?>">
                <input type="hidden" name="database" value="<?php echo htmlspecialchars($db); ?>">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($user); ?>">
                <input type="hidden" name="password" value="<?php echo htmlspecialchars($password); ?>">
                
                <table>
                    <thead>
                        <tr>
                            <th>Candidate ID</th>
                            <th>Current User ID</th>
                            <th>Position ID</th>
                            <th>Status</th>
                            <th>New User ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($candidates as $candidate): ?>
                            <tr>
                                <td><?php echo $candidate['id']; ?></td>
                                <td><?php echo $candidate['user_id']; ?></td>
                                <td><?php echo $candidate['position_id']; ?></td>
                                <td><?php echo $candidate['status']; ?></td>
                                <td>
                                    <select name="mappings[<?php echo $candidate['id']; ?>]">
                                        <option value="">Select User ID</option>
                                        <?php foreach ($users as $user): ?>
                                            <option value="<?php echo $user['user_id']; ?>">
                                                <?php echo $user['user_id']; ?> - <?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars($user['email']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
                <button type="submit" name="update">Update Candidates</button>
            </form>
        </div>
        
        <div class="card">
            <h2>Available Users</h2>
            <p>Reference list of all users in the system:</p>
            
            <table>
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</body>
</html> 