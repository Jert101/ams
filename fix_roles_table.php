<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

// Check if roles table exists
if (!Schema::hasTable('roles')) {
    echo "Roles table does not exist. Creating it...\n";
    
    Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('description')->nullable();
        $table->timestamps();
    });
    
    echo "Roles table created successfully.\n";
} else {
    echo "Roles table exists. Checking structure...\n";
    
    // Check if id column exists
    if (!Schema::hasColumn('roles', 'id')) {
        echo "The 'id' column is missing. Adding it...\n";
        
        Schema::table('roles', function (Blueprint $table) {
            $table->id()->first();
        });
        
        echo "Added 'id' column to roles table.\n";
    } else {
        echo "The 'id' column exists in roles table.\n";
    }
    
    // Check other required columns
    $requiredColumns = ['name', 'description', 'created_at', 'updated_at'];
    $missingColumns = [];
    
    foreach ($requiredColumns as $column) {
        if (!Schema::hasColumn('roles', $column)) {
            $missingColumns[] = $column;
        }
    }
    
    if (!empty($missingColumns)) {
        echo "Missing columns: " . implode(', ', $missingColumns) . ". Adding them...\n";
        
        Schema::table('roles', function (Blueprint $table) use ($missingColumns) {
            if (in_array('name', $missingColumns)) {
                $table->string('name');
            }
            if (in_array('description', $missingColumns)) {
                $table->string('description')->nullable();
            }
            if (in_array('created_at', $missingColumns) || in_array('updated_at', $missingColumns)) {
                $table->timestamps();
            }
        });
        
        echo "Added missing columns to roles table.\n";
    } else {
        echo "All required columns exist in roles table.\n";
    }
}

// Check if roles table has data
$roleCount = DB::table('roles')->count();

if ($roleCount === 0) {
    echo "Roles table is empty. Adding default roles...\n";
    
    $roles = [
        [
            'name' => 'Admin',
            'description' => 'System administrator with full access to all features',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Officer',
            'description' => 'Can approve attendance of members using the QR scanner',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Secretary',
            'description' => 'Can create attendance summaries and manage notifications for absent members',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'name' => 'Member',
            'description' => 'Regular member with access to personal attendance records',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ];
    
    foreach ($roles as $role) {
        DB::table('roles')->insert($role);
    }
    
    echo "Added default roles to roles table.\n";
} else {
    echo "Roles table already has {$roleCount} roles.\n";
}

echo "\nRoles table check and fix completed.\n";
