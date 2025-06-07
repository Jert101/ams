<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixElectionViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'election:fix-view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the election view file to handle null dates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = resource_path('views/election/index.blade.php');
        
        if (!file_exists($filePath)) {
            $this->error('Election view file not found!');
            return 1;
        }
        
        // Backup the original file
        $backupPath = $filePath . '.bak-' . date('YmdHis');
        copy($filePath, $backupPath);
        $this->info('Backup created at: ' . $backupPath);
        
        // Get the content of the file
        $content = file_get_contents($filePath);
        
        // The problematic section
        $problematicSection = <<<'EOD'
                @elseif($electionSetting->status === 'voting')
                    Voting is now open! You can vote for candidates in each position.
                    <div class="mt-3 flex items-center bg-indigo-500 bg-opacity-30 px-4 py-2 rounded-lg inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>
                            <strong>Voting period:</strong> {{ $electionSetting->voting_start_date->format('M d, Y') }} to {{ $electionSetting->voting_end_date->format('M d, Y') }}
                            <span class="ml-2 bg-white text-indigo-700 px-2 py-1 rounded-full text-xs font-medium countdown-timer" data-target-date="{{ $electionSetting->voting_end_date->toISOString() }}">Ends {{ now()->diffForHumans($electionSetting->voting_end_date, ['parts' => 2]) }}</span>
                        </span>
                    </div>
EOD;
        
        // The fixed section
        $fixedSection = <<<'EOD'
                @elseif($electionSetting->status === 'voting')
                    Voting is now open! You can vote for candidates in each position.
                    <div class="mt-3 flex items-center bg-indigo-500 bg-opacity-30 px-4 py-2 rounded-lg inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>
                            <strong>Voting period:</strong> 
                            @if($electionSetting->voting_start_date)
                                {{ $electionSetting->voting_start_date->format('M d, Y') }}
                            @else
                                Start date not set
                            @endif
                            to 
                            @if($electionSetting->voting_end_date)
                                {{ $electionSetting->voting_end_date->format('M d, Y') }}
                                <span class="ml-2 bg-white text-indigo-700 px-2 py-1 rounded-full text-xs font-medium countdown-timer" data-target-date="{{ $electionSetting->voting_end_date->toISOString() }}">Ends {{ now()->diffForHumans($electionSetting->voting_end_date, ['parts' => 2]) }}</span>
                            @else
                                End date not set
                            @endif
                        </span>
                    </div>
EOD;

        // Replace the problematic section with the fixed section
        $newContent = str_replace($problematicSection, $fixedSection, $content);
        
        // If no replacement was made, the problematic section wasn't found exactly as expected
        if ($content === $newContent) {
            $this->warn('Could not find the exact problematic section. Trying a different approach...');
            
            // More aggressive approach - find any instance of voting_start_date->format or voting_end_date->format
            // without checking if it's null first
            $pattern = '/\$electionSetting->voting_(?:start|end)_date->format/';
            
            if (preg_match($pattern, $content)) {
                $this->info('Found instances of voting date formats without null checks.');
                
                // Replace the problematic parts in the file content
                $newContent = preg_replace(
                    '/\{\{ \$electionSetting->voting_start_date->format\(\'M d, Y\'\) \}\}/',
                    '@if($electionSetting->voting_start_date)
                                {{ $electionSetting->voting_start_date->format(\'M d, Y\') }}
                            @else
                                Start date not set
                            @endif',
                    $content
                );
                
                $newContent = preg_replace(
                    '/\{\{ \$electionSetting->voting_end_date->format\(\'M d, Y\'\) \}\}/',
                    '@if($electionSetting->voting_end_date)
                                {{ $electionSetting->voting_end_date->format(\'M d, Y\') }}
                            @else
                                End date not set
                            @endif',
                    $newContent
                );
                
                // Fix any remaining references to voting_end_date that might cause errors
                $newContent = preg_replace(
                    '/data-target-date="\{\{ \$electionSetting->voting_end_date->toISOString\(\) \}\}"/',
                    'data-target-date="@if($electionSetting->voting_end_date){{ $electionSetting->voting_end_date->toISOString() }}@else{{ now()->addDays(1)->toISOString() }}@endif"',
                    $newContent
                );
                
                $newContent = preg_replace(
                    '/Ends \{\{ now\(\)->diffForHumans\(\$electionSetting->voting_end_date, \[\'parts\' => 2\]\) \}\}/',
                    '@if($electionSetting->voting_end_date)Ends {{ now()->diffForHumans($electionSetting->voting_end_date, [\'parts\' => 2]) }}@else Not scheduled @endif',
                    $newContent
                );
            } else {
                $this->error('Could not find any problematic voting date formats.');
                return 1;
            }
        }
        
        // Write the fixed content back to the file
        if (file_put_contents($filePath, $newContent)) {
            $this->info('Election view file fixed successfully!');
            return 0;
        } else {
            $this->error('Failed to write the fixed content to the file.');
            return 1;
        }
    }
}
