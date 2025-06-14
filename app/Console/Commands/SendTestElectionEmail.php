<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\ElectionWinnerNotification;
use Illuminate\Support\Facades\Mail;

class SendTestElectionEmail extends Command
{
    protected $signature = 'election:test-email {email}';
    protected $description = 'Send a test election results email';

    public function handle()
    {
        // Create test election results data
        $positions = [
            [
                'title' => 'President',
                'winners' => [
                    [
                        'name' => 'John Doe',
                        'votes' => 150,
                        'percentage' => 65.5
                    ]
                ]
            ],
            [
                'title' => 'Board Members',
                'winners' => [
                    [
                        'name' => 'Jane Smith',
                        'votes' => 120,
                        'percentage' => 52.4
                    ],
                    [
                        'name' => 'Mike Johnson',
                        'votes' => 115,
                        'percentage' => 50.2
                    ]
                ]
            ],
            [
                'title' => 'Secretary',
                'winners' => [
                    [
                        'name' => 'Sarah Wilson',
                        'votes' => 140,
                        'percentage' => 61.1
                    ]
                ]
            ],
            [
                'title' => 'Treasurer',
                'winners' => [
                    [
                        'name' => 'Robert Brown',
                        'votes' => 130,
                        'percentage' => 56.8
                    ]
                ]
            ]
        ];

        // Send the email
        Mail::to($this->argument('email'))
            ->send(new ElectionWinnerNotification($positions));

        $this->info('Test election results email sent to ' . $this->argument('email'));
    }
} 