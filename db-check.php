<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Database name from config: " . config('database.connections.mysql.database') . "\n";
echo "Environment file path: " . app()->environmentFilePath() . "\n";

if (file_exists(__DIR__ . '/.env.local')) {
    echo ".env.local exists and might be overriding your main .env file\n";
}

echo "\nAll environment variables:\n";
$envVars = getenv();
foreach ($envVars as $key => $value) {
    if (strpos($key, 'DB_') === 0) {
        echo "$key=$value\n";
    }
}
