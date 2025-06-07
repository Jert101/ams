<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ElectionVote;
use App\Models\ElectionPosition;
use App\Models\ElectionSetting;
use Illuminate\Support\Facades\Schema;

function checkModelSchema($modelInstance, $modelName) {
    echo "Checking {$modelName} schema...\n";
    
    $table = $modelInstance->getTable();
    echo "Table name: {$table}\n";
    
    // Get fillable attributes from model
    $fillable = $modelInstance->getFillable();
    echo "Fillable attributes: " . implode(', ', $fillable) . "\n";
    
    // Get columns from database
    $columns = Schema::getColumnListing($table);
    echo "Database columns: " . implode(', ', $columns) . "\n";
    
    // Check for missing columns
    $missingColumns = [];
    foreach ($fillable as $attribute) {
        if (!in_array($attribute, $columns)) {
            $missingColumns[] = $attribute;
        }
    }
    
    if (count($missingColumns) > 0) {
        echo "Missing columns: " . implode(', ', $missingColumns) . "\n";
        return false;
    } else {
        echo "All columns are present in the database.\n";
        return true;
    }
    
    echo "\n";
}

// Check ElectionVote model
$voteModel = new ElectionVote();
checkModelSchema($voteModel, 'ElectionVote');
echo "\n";

// Check ElectionPosition model
$positionModel = new ElectionPosition();
checkModelSchema($positionModel, 'ElectionPosition');
echo "\n";

// Check ElectionSetting model
$settingModel = new ElectionSetting();
checkModelSchema($settingModel, 'ElectionSetting'); 