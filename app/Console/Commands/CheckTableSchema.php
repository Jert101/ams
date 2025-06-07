<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CheckTableSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-schema {--detailed : Show more detailed output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for mismatches between models and database tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking schema consistency...');
        $detailed = $this->option('detailed');
        
        // Get all model files
        $modelFiles = File::glob(app_path('Models/*.php'));
        $modelCount = count($modelFiles);
        
        $this->info("Found {$modelCount} models to check.");
        
        $errors = [];
        $errorCount = 0;
        
        foreach ($modelFiles as $modelFile) {
            $className = pathinfo($modelFile, PATHINFO_FILENAME);
            $modelClass = "\\App\\Models\\{$className}";
            
            if (!class_exists($modelClass)) {
                continue;
            }
            
            try {
                $model = new $modelClass();
                $table = $model->getTable();
                
                $this->info("Checking table: {$table}");
                
                // Check if table exists
                if (!Schema::hasTable($table)) {
                    $message = "Table '{$table}' does not exist for model '{$className}'";
                    $this->error($message);
                    $errors[] = $message;
                    $errorCount++;
                    continue;
                }
                
                // Get fillable attributes from model
                $fillable = $model->getFillable();
                
                if ($detailed) {
                    $this->line("  Fillable attributes: " . implode(', ', $fillable));
                }
                
                // Get columns from database
                $columns = Schema::getColumnListing($table);
                
                if ($detailed) {
                    $this->line("  Database columns: " . implode(', ', $columns));
                }
                
                // Check for missing columns
                $missingColumns = [];
                foreach ($fillable as $attribute) {
                    if (!in_array($attribute, $columns)) {
                        $message = "Column '{$attribute}' is missing from table '{$table}' but defined in model '{$className}'";
                        $this->error($message);
                        $errors[] = $message;
                        $missingColumns[] = $attribute;
                        $errorCount++;
                    }
                }
                
                if (count($missingColumns) > 0) {
                    $this->warn("  Missing columns in '{$table}': " . implode(', ', $missingColumns));
                } else {
                    $this->info("  Table '{$table}' schema is consistent with model.");
                }
                
            } catch (\Exception $e) {
                $message = "Error checking model {$className}: " . $e->getMessage();
                $this->error($message);
                $errors[] = $message;
                $errorCount++;
            }
        }
        
        if ($errorCount === 0) {
            $this->info('All tables and models are consistent!');
            return 0;
        } else {
            $this->error("{$errorCount} issues found. Please fix these issues by creating or updating migrations.");
            
            if ($this->confirm('Do you want to see the detailed error list?')) {
                $this->line("\nDetailed errors:");
                foreach ($errors as $index => $error) {
                    $this->line(($index + 1) . ". {$error}");
                }
            }
            
            return 1;
        }
    }
} 