<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ElectionCandidate;
use Illuminate\Support\Facades\Schema;

echo "Checking ElectionCandidate schema...\n";

$model = new ElectionCandidate();
$table = $model->getTable();

echo "Table name: {$table}\n";

// Get fillable attributes from model
$fillable = $model->getFillable();
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
} else {
    echo "All columns are present in the database.\n";
} 