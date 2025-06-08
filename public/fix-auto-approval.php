<?php
/**
 * Fix Auto-Approval Column Script
 * 
 * This script checks if the auto_approve_candidates column exists in the election_settings table
 * and adds it if it doesn't exist.
 */

// Set display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h1>Fix Auto-Approval Column</h1>";
echo "<p>This script will check if the auto_approve_candidates column exists in the election_settings table and add it if needed.</p>";

try {
    // Load Laravel database configuration
    $app = require_once '../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "<div style='margin-bottom: 10px; padding: 10px; background-color: #f0f8ff; border: 1px solid #ccc;'>";
    echo "<h3>Database Connection Test</h3>";
    try {
        // Test the database connection
        DB::connection()->getPdo();
        echo "<p style='color: green;'>✅ Database connection successful: " . DB::connection()->getDatabaseName() . "</p>";
    } catch (\Exception $e) {
        echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
        echo "❌ Database connection failed: " . $e->getMessage();
        echo "</div>";
        
        echo "<h3>Troubleshooting Tips</h3>";
        echo "<ul>";
        echo "<li>Make sure your MySQL server is running</li>";
        echo "<li>Check your database credentials in .env file</li>";
        echo "<li>For InfinityFree hosting, ensure your database connection settings are correct</li>";
        echo "</ul>";
        
        exit;
    }
    echo "</div>";

    // Check if column exists
    $hasColumn = false;
    try {
        $hasColumn = Schema::hasColumn('election_settings', 'auto_approve_candidates');
        echo "<p>Column check complete.</p>";
    } catch (\Exception $e) {
        echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
        echo "❌ Error checking for column: " . $e->getMessage();
        echo "</div>";
        exit;
    }

    if ($hasColumn) {
        echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
        echo "✅ The 'auto_approve_candidates' column already exists in the election_settings table.";
        echo "</div>";
        
        // Check if there are any records in the table
        $settingsCount = DB::table('election_settings')->count();
        echo "<p>Found {$settingsCount} election settings records.</p>";
        
        if ($settingsCount > 0) {
            // Check if any settings have null value for auto_approve_candidates
            $nullCount = DB::table('election_settings')
                ->whereNull('auto_approve_candidates')
                ->count();
            
            if ($nullCount > 0) {
                // Update null values to true
                DB::table('election_settings')
                    ->whereNull('auto_approve_candidates')
                    ->update(['auto_approve_candidates' => true]);
                
                echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
                echo "✅ Updated {$nullCount} records with null auto_approve_candidates value to true.";
                echo "</div>";
            } else {
                echo "<p>All existing records have a value for auto_approve_candidates.</p>";
            }
            
            // Show current values
            $settings = DB::table('election_settings')->get();
            echo "<h2>Current Election Settings</h2>";
            echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Status</th><th>Auto-Approve Candidates</th></tr>";
            
            foreach ($settings as $setting) {
                $autoApprove = $setting->auto_approve_candidates ? 'Yes' : 'No';
                echo "<tr><td>{$setting->id}</td><td>{$setting->status}</td><td>{$autoApprove}</td></tr>";
            }
            
            echo "</table>";
        }
    } else {
        echo "<div style='color: orange; padding: 10px; background-color: #fff8f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
        echo "⚠️ The auto_approve_candidates column does not exist in the election_settings table.";
        echo "</div>";
        
        // Add the column if it doesn't exist
        try {
            Schema::table('election_settings', function ($table) {
                $table->boolean('auto_approve_candidates')->default(false);
            });
            
            echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
            echo "✅ Added 'auto_approve_candidates' column to the election_settings table successfully.";
            echo "</div>";
        } catch (\Exception $e) {
            echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
            echo "❌ Error adding column: " . $e->getMessage();
            echo "</div>";
        }
    }
    
    echo "<h2>Routes Check</h2>";
    
    // Check if the route exists
    $router = app('router');
    $routes = collect($router->getRoutes())->map(function ($route) {
        return [
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'methods' => $route->methods(),
            'action' => $route->getActionName(),
        ];
    });
    
    $toggleRoute = $routes->firstWhere('name', 'admin.election.toggle-auto-approval');
    
    if ($toggleRoute) {
        echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
        echo "✅ The route 'admin.election.toggle-auto-approval' exists.";
        echo "</div>";
        
        echo "<p>Route details:</p>";
        echo "<ul>";
        echo "<li>URI: {$toggleRoute['uri']}</li>";
        echo "<li>Methods: " . implode(', ', $toggleRoute['methods']) . "</li>";
        echo "<li>Action: {$toggleRoute['action']}</li>";
        echo "</ul>";
    } else {
        echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
        echo "❌ The route 'admin.election.toggle-auto-approval' does not exist.";
        echo "</div>";
        
        // Check for similar routes
        $similarRoutes = $routes->filter(function ($route) {
            return strpos($route['name'], 'toggle-auto-approval') !== false || 
                   strpos($route['uri'], 'toggle-auto-approval') !== false;
        });
        
        if ($similarRoutes->count() > 0) {
            echo "<p>Found similar routes:</p>";
            echo "<ul>";
            foreach ($similarRoutes as $route) {
                echo "<li>Name: {$route['name']}, URI: {$route['uri']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No similar routes found.</p>";
        }
    }
    
    echo "<p><a href='/admin/election' class='btn btn-primary'>Return to Election Management</a></p>";

} catch (\Exception $e) {
    echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
    echo "<p><a href='/admin/election'>Return to Election Management</a></p>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>If the column was successfully added or already exists, the auto-approval feature should work correctly.</li>";
echo "<li>If the route was not found, make sure the web.php file contains the correct route definition.</li>";
echo "<li>After fixing the issues, try using the auto-approval toggle button again.</li>";
echo "</ol>";

echo "<p><a href='" . $_SERVER['PHP_SELF'] . "' style='display: inline-block; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px;'>Run Script Again</a></p>"; 