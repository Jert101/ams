<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixSyntaxErrorCommand extends Command
{
    protected $signature = 'fix:syntax-error';
    protected $description = 'Fix syntax errors in the admin election view';

    public function handle()
    {
        $viewPath = resource_path('views/admin/election/index.blade.php');
        
        if (!File::exists($viewPath)) {
            $this->error("View file not found: {$viewPath}");
            return 1;
        }
        
        $content = File::get($viewPath);
        
        // Fix double question mark for candidacy_start_date
        $content = str_replace(
            'value="{{ $electionSetting->candidacy_start_date ? $electionSetting->candidacy_start_date ? $electionSetting->candidacy_start_date->format(\'Y-m-d\TH:i\') : \'\' }}"',
            'value="{{ $electionSetting->candidacy_start_date ? $electionSetting->candidacy_start_date->format(\'Y-m-d\TH:i\') : \'\' }}"',
            $content
        );
        
        // Fix double question mark for candidacy_end_date
        $content = str_replace(
            'value="{{ $electionSetting->candidacy_end_date ? $electionSetting->candidacy_end_date ? $electionSetting->candidacy_end_date->format(\'Y-m-d\TH:i\') : \'\' }}"',
            'value="{{ $electionSetting->candidacy_end_date ? $electionSetting->candidacy_end_date->format(\'Y-m-d\TH:i\') : \'\' }}"',
            $content
        );
        
        // Fix double question mark for voting_start_date
        $content = str_replace(
            'value="{{ $electionSetting->voting_start_date ? $electionSetting->voting_start_date ? $electionSetting->voting_start_date->format(\'Y-m-d\TH:i\') : \'\' }}"',
            'value="{{ $electionSetting->voting_start_date ? $electionSetting->voting_start_date->format(\'Y-m-d\TH:i\') : \'\' }}"',
            $content
        );
        
        // Fix double question mark for voting_end_date
        $content = str_replace(
            'value="{{ $electionSetting->voting_end_date ? $electionSetting->voting_end_date ? $electionSetting->voting_end_date->format(\'Y-m-d\TH:i\') : \'\' }}"',
            'value="{{ $electionSetting->voting_end_date ? $electionSetting->voting_end_date->format(\'Y-m-d\TH:i\') : \'\' }}"',
            $content
        );
        
        File::put($viewPath, $content);
        
        $this->info('Successfully fixed syntax errors in the admin election view!');
        return 0;
    }
} 