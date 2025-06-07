<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckTimezone extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'timezone:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and display the application timezone settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Current PHP default timezone: ' . date_default_timezone_get());
        $this->info('Current Laravel app timezone: ' . config('app.timezone'));
        
        $now = Carbon::now();
        $this->info('Current time: ' . $now->toDateTimeString());
        $this->info('Current time with timezone: ' . $now->toDateTimeString() . ' ' . $now->tzName);
        
        $utcNow = Carbon::now('UTC');
        $this->info('Current UTC time: ' . $utcNow->toDateTimeString() . ' UTC');
        
        $manilaTime = Carbon::now('Asia/Manila');
        $this->info('Current Manila time: ' . $manilaTime->toDateTimeString() . ' Asia/Manila');
        
        $this->info('Timezone difference from UTC: ' . $now->tzName . ' is UTC' . $now->format('P'));
        
        return 0;
    }
} 