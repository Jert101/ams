<?php
require '../vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "<h1>Auto-Approval Setting Debug Tool</h1>";
echo "<p>This script will diagnose and repair issues with the auto_approve_candidates setting.</p>";

try {
    // Load Laravel application
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
        exit;
    }
    echo "</div>";

    // 1. Check if the column exists
    echo "<h3>Step 1: Checking if auto_approve_candidates column exists</h3>";
    $hasColumn = Schema::hasColumn('election_settings', 'auto_approve_candidates');
    
    if ($hasColumn) {
        echo "<p style='color: green;'>✅ Column exists in the database schema</p>";
    } else {
        echo "<p style='color: red;'>❌ Column does not exist in the database schema</p>";
        echo "<p>Attempting to create the column...</p>";
        
        try {
            Schema::table('election_settings', function ($table) {
                $table->boolean('auto_approve_candidates')->default(false);
            });
            echo "<p style='color: green;'>✅ Column created successfully</p>";
            $hasColumn = true;
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Failed to create column: " . $e->getMessage() . "</p>";
        }
    }

    // 2. Check if there are any election_settings records
    echo "<h3>Step 2: Checking election_settings records</h3>";
    $settings = DB::table('election_settings')->get();
    echo "<p>Found " . count($settings) . " records</p>";

    if (count($settings) > 0) {
        // 3. Check the values of auto_approve_candidates in the records
        echo "<h3>Step 3: Checking auto_approve_candidates values</h3>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>auto_approve_candidates</th><th>Type</th><th>Status</th></tr>";
        
        foreach ($settings as $setting) {
            $type = gettype($setting->auto_approve_candidates ?? null);
            $status = "OK";
            
            if (!isset($setting->auto_approve_candidates)) {
                $status = "Missing";
            } elseif ($setting->auto_approve_candidates === null) {
                $status = "NULL";
            }
            
            echo "<tr>";
            echo "<td>" . $setting->id . "</td>";
            echo "<td>" . (isset($setting->auto_approve_candidates) ? ($setting->auto_approve_candidates ? 'true' : 'false') : 'N/A') . "</td>";
            echo "<td>" . $type . "</td>";
            echo "<td>" . $status . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // 4. Attempt to fix any issues
        echo "<h3>Step 4: Fixing issues</h3>";
        $needsFix = false;
        $fixApplied = false;
        
        foreach ($settings as $setting) {
            if (!isset($setting->auto_approve_candidates) || $setting->auto_approve_candidates === null) {
                $needsFix = true;
            }
        }
        
        if ($needsFix) {
            echo "<p>Issues detected. Attempting to fix...</p>";
            
            try {
                $updated = DB::table('election_settings')
                    ->whereNull('auto_approve_candidates')
                    ->update(['auto_approve_candidates' => false]);
                
                echo "<p style='color: green;'>✅ Updated " . $updated . " records</p>";
                $fixApplied = true;
            } catch (\Exception $e) {
                echo "<p style='color: red;'>❌ Failed to update records: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: green;'>✅ No issues detected</p>";
        }

        // 5. Verify the fix
        if ($fixApplied) {
            echo "<h3>Step 5: Verifying fix</h3>";
            $settings = DB::table('election_settings')->get();
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>auto_approve_candidates</th><th>Type</th></tr>";
            
            foreach ($settings as $setting) {
                echo "<tr>";
                echo "<td>" . $setting->id . "</td>";
                echo "<td>" . ($setting->auto_approve_candidates ? 'true' : 'false') . "</td>";
                echo "<td>" . gettype($setting->auto_approve_candidates) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ No election_settings records found</p>";
        
        // Create a default record
        echo "<p>Creating a default election settings record...</p>";
        try {
            $id = DB::table('election_settings')->insertGetId([
                'is_enabled' => false,
                'auto_approve_candidates' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            echo "<p style='color: green;'>✅ Created default record with ID: " . $id . "</p>";
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Failed to create default record: " . $e->getMessage() . "</p>";
        }
    }

    // 6. Add a direct update form for emergency fixes
    echo "<h3>Emergency Direct Update</h3>";
    echo "<p>Use this form only if the normal toggle in the admin interface isn't working:</p>";
    echo "<form method='post'>";
    echo "<input type='hidden' name='action' value='direct_update'>";
    echo "<label><input type='radio' name='auto_approve' value='1'> Enable Auto-Approval</label><br>";
    echo "<label><input type='radio' name='auto_approve' value='0' checked> Disable Auto-Approval</label><br>";
    echo "<button type='submit' style='margin-top: 10px; padding: 5px 10px;'>Apply Direct Update</button>";
    echo "</form>";

    // Handle direct update action
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'direct_update') {
        $value = isset($_POST['auto_approve']) && $_POST['auto_approve'] == '1';
        
        try {
            $updated = DB::table('election_settings')->update(['auto_approve_candidates' => $value]);
            
            echo "<div style='margin-top: 10px; padding: 10px; background-color: #f0fff0; border: 1px solid #a0d0a0;'>";
            echo "✅ Successfully updated " . $updated . " records. Auto-approval is now " . ($value ? 'ENABLED' : 'DISABLED');
            echo "</div>";
        } catch (\Exception $e) {
            echo "<div style='margin-top: 10px; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0;'>";
            echo "❌ Failed to update: " . $e->getMessage();
            echo "</div>";
        }
    }

} catch (\Exception $e) {
    echo "<div style='color: red; padding: 10px; background-color: #fff0f0; border: 1px solid #d0a0a0; margin: 10px 0;'>";
    echo "❌ Error: " . $e->getMessage();
    echo "</div>";
}

echo "<p><a href='/admin/election' style='display: inline-block; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;'>Return to Election Management</a></p>";
echo "<p><a href='" . $_SERVER['PHP_SELF'] . "' style='display: inline-block; padding: 10px 15px; background-color: #28a745; color: white; text-decoration: none; border-radius: 4px;'>Refresh This Page</a></p>"; 