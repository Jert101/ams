<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Secretary\ReportController;

class TestExportCsvCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absences:test-export {type=three : Type of report to export (three or four)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test exporting CSV reports for consecutive absences';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        
        $controller = new ReportController();
        
        if ($type === 'three') {
            $this->info('Generating CSV for 3 consecutive absences...');
            $response = $controller->exportThreeConsecutiveAbsences();
            file_put_contents('export_3_consecutive.csv', $response->getContent());
            $this->info('CSV saved to export_3_consecutive.csv');
        } elseif ($type === 'four') {
            $this->info('Generating CSV for 4+ consecutive absences...');
            $response = $controller->exportFourPlusConsecutiveAbsences();
            file_put_contents('export_4plus_consecutive.csv', $response->getContent());
            $this->info('CSV saved to export_4plus_consecutive.csv');
        } else {
            $this->error('Invalid type. Use "three" or "four".');
            return 1;
        }
        
        return 0;
    }
} 