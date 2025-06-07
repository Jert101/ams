<?php
// This is a direct PHP file in the public directory that will be processed by the web server
// We'll try to load the user directly from the database

// Load Laravel app
require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Get user ID from query parameter
$userId = $_GET['id'] ?? null;

if ($userId) {
    // Get the user
    $user = \App\Models\User::with('qrCode')->find($userId);
} else {
    $user = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .card { border: 1px solid #ccc; padding: 20px; max-width: 600px; margin: 0 auto; }
        .detail { margin-bottom: 10px; }
        .label { font-weight: bold; display: inline-block; width: 100px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>QR Code Test</h1>
        
        <?php if ($user): ?>
            <h2>User Information</h2>
            <div class="detail">
                <span class="label">Name:</span> 
                <?php echo htmlspecialchars($user->name ?? 'N/A'); ?>
            </div>
            <div class="detail">
                <span class="label">ID:</span> 
                <?php echo htmlspecialchars($user->user_id ?? 'N/A'); ?>
            </div>
            <div class="detail">
                <span class="label">Email:</span> 
                <?php echo htmlspecialchars($user->email ?? 'N/A'); ?>
            </div>
            <div class="detail">
                <span class="label">Address:</span> 
                <?php echo htmlspecialchars($user->address ?? 'N/A'); ?>
            </div>
            <div class="detail">
                <span class="label">Birth Date:</span> 
                <?php echo $user->date_of_birth ? date('F j, Y', strtotime($user->date_of_birth)) : 'N/A'; ?>
            </div>
            <div class="detail">
                <span class="label">Gender:</span> 
                <?php echo htmlspecialchars($user->gender ?? 'N/A'); ?>
            </div>
            <div class="detail">
                <span class="label">Mobile:</span> 
                <?php echo htmlspecialchars($user->mobile_number ?? 'N/A'); ?>
            </div>
            
            <?php if ($user->qrCode): ?>
                <h2>QR Code Information</h2>
                <div class="detail">
                    <span class="label">Code:</span> 
                    <?php echo htmlspecialchars($user->qrCode->code ?? 'N/A'); ?>
                </div>
            <?php else: ?>
                <p>No QR code found for this user.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>No user found with the provided ID.</p>
            <p>Please use ?id=110001 in the URL to specify a user.</p>
        <?php endif; ?>
    </div>
</body>
</html> 