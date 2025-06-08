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

echo "<h1>Fix Auto-Approval Column</h1>";
echo "<p>This script will check if the auto_approve_candidates column exists in the election_settings table and add it if needed.</p>";

try {
    // Load Laravel application
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Check if the column exists
    $columnExists = \Illuminate\Support\Facades\Schema::hasColumn('election_settings', 'auto_approve_candidates');
    
    if ($columnExists) {
        echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
        echo "✅ The auto_approve_candidates column already exists in the election_settings table.";
        echo "</div>";
        
        // Check if there are any records in the table
        $settingsCount = \Illuminate\Support\Facades\DB::table('election_settings')->count();
        echo "<p>Found {$settingsCount} election settings records.</p>";
        
        if ($settingsCount > 0) {
            // Check if any settings have null value for auto_approve_candidates
            $nullCount = \Illuminate\Support\Facades\DB::table('election_settings')
                ->whereNull('auto_approve_candidates')
                ->count();
            
            if ($nullCount > 0) {
                // Update null values to true
                \Illuminate\Support\Facades\DB::table('election_settings')
                    ->whereNull('auto_approve_candidates')
                    ->update(['auto_approve_candidates' => true]);
                
                echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
                echo "✅ Updated {$nullCount} records with null auto_approve_candidates value to true.";
                echo "</div>";
            } else {
                echo "<p>All existing records have a value for auto_approve_candidates.</p>";
            }
            
            // Show current values
            $settings = \Illuminate\Support\Facades\DB::table('election_settings')->get();
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
        
        // Add the column
        \Illuminate\Support\Facades\Schema::table('election_settings', function ($table) {
            $table->boolean('auto_approve_candidates')->default(true);
        });
        
        echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
        echo "✅ Added auto_approve_candidates column to the election_settings table with default value true.";
        echo "</div>";
        
        // Check if it was added successfully
        $columnExists = \Illuminate\Support\Facades\Schema::hasColumn('election_settings', 'auto_approve_candidates');
        if ($columnExists) {
            echo "<div style='color: green; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0; margin: 10px 0;'>";
            echo "✅ Column was added successfully.";
            echo "</div>";
        } else {
            echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
            echo "❌ Failed to add the column.";
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
    
} catch (\Exception $e) {
    echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
    
    echo "<pre>";
    echo $e->getTraceAsString();
    echo "</pre>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>If the column was successfully added or already exists, the auto-approval feature should work correctly.</li>";
echo "<li>If the route was not found, make sure the web.php file contains the correct route definition.</li>";
echo "<li>After fixing the issues, try using the auto-approval toggle button again.</li>";
echo "</ol>";

echo "<p><a href='/admin/election' style='display: inline-block; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;'>Return to Election Management</a></p>";
echo "<p><a href='" . $_SERVER['PHP_SELF'] . "' style='display: inline-block; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px;'>Run Script Again</a></p>"; 