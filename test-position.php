<?php

// Bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Get the ElectionSetting model and create a position
$electionSetting = App\Models\ElectionSetting::getActiveOrCreate();
echo "Using election settings ID: " . $electionSetting->id . "\n";

try {
    $position = App\Models\ElectionPosition::create([
        'title' => 'Test Position',
        'description' => 'Test Description',
        'eligible_roles' => ['Member'],
        'max_votes_per_voter' => 1,
        'election_settings_id' => $electionSetting->id,
    ]);
    
    echo "Position created with ID: " . $position->id . "\n";
} catch (Exception $e) {
    echo "Error creating position: " . $e->getMessage() . "\n";
}

// Show all positions
$positions = App\Models\ElectionPosition::all();
echo "Total positions: " . $positions->count() . "\n";
foreach ($positions as $pos) {
    echo "ID: " . $pos->id . ", Title: " . $pos->title . "\n";
} 