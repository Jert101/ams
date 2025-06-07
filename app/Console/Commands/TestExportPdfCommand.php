<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Secretary\ReportController;

class TestExportPdfCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absences:test-export-pdf {type=three : Type of report to export (three or four)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test exporting PDF reports for consecutive absences';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        
        $controller = new ReportController();
        
        if ($type === 'three') {
            $this->info('Generating PDF for 3 consecutive absences...');
            $response = $controller->exportThreeConsecutiveAbsencesPdf();
            file_put_contents('export_3_consecutive.pdf', $response->getContent());
            $this->info('PDF saved to export_3_consecutive.pdf');
        } elseif ($type === 'four') {
            $this->info('Generating PDF for 4+ consecutive absences...');
            $response = $controller->exportFourPlusConsecutiveAbsencesPdf();
            file_put_contents('export_4plus_consecutive.pdf', $response->getContent());
            $this->info('PDF saved to export_4plus_consecutive.pdf');
        } else {
            $this->error('Invalid type. Use "three" or "four".');
            return 1;
        }
        
        return 0;
    }
} 