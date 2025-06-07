<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixAdminElectionViewCommand extends Command
{
    protected $signature = 'fix:admin-election-view';
    protected $description = 'Fix null property access issues in admin election view';

    public function handle()
    {
        $viewPath = resource_path('views/admin/election/index.blade.php');
        
        if (!File::exists($viewPath)) {
            $this->error("View file not found: {$viewPath}");
            return 1;
        }
        
        $content = File::get($viewPath);
        
        // Fix candidate->user->name and email access
        $content = str_replace(
            'alt="{{ $candidate->user->name }}"',
            'alt="{{ $candidate->user->name ?? \'User\' }}"',
            $content
        );
        
        $content = str_replace(
            '<div class="font-semibold text-gray-800">{{ $candidate->user->name }}</div>',
            '<div class="font-semibold text-gray-800">{{ $candidate->user->name ?? \'Unknown User\' }}</div>',
            $content
        );
        
        $content = str_replace(
            '<div class="text-gray-500 text-sm">{{ $candidate->user->email }}</div>',
            '<div class="text-gray-500 text-sm">{{ $candidate->user->email ?? \'No email available\' }}</div>',
            $content
        );
        
        // Fix any other direct access to candidate->user properties
        $content = preg_replace(
            '/\{\{\s*\$candidate->user->([\w]+)\s*\}\}/i',
            '{{ $candidate->user->$1 ?? \'\' }}',
            $content
        );
        
        // Fix any other potential null references in diffForHumans calls
        $content = preg_replace(
            '/now\(\)->diffForHumans\(\$electionSetting->([^,]+)/',
            'now()->diffForHumans($electionSetting->$1 ?? now()',
            $content
        );
        
        // Add null checks for position object properties if needed
        $content = preg_replace(
            '/\{\{\s*\$position->([\w]+)\s*\}\}/i',
            '{{ $position->$1 ?? \'\' }}',
            $content
        );
        
        // Add null checks for any potential format() calls on nullable dates
        $content = preg_replace(
            '/\$electionSetting->([^->\s]+)->format\(/',
            '$electionSetting->$1 ? $electionSetting->$1->format(',
            $content
        );
        
        File::put($viewPath, $content);
        
        $this->info('Successfully fixed admin election view!');
        return 0;
    }
} 