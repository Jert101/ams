<?php

// Load the .env file
require_once __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Display the database configuration
echo "Database name from .env: " . $_ENV['DB_DATABASE'] . "\n";
echo "Database connection from config: " . config('database.connections.mysql.database') . "\n";
