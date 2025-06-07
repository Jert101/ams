<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\QrCode;

class GenerateQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrcodes:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR codes for users who do not have them';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $users = User::whereDoesntHave('qrCode')->get();
        
        $this->info("Generating QR codes for {$users->count()} users...");
        
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();
        
        foreach ($users as $user) {
            QrCode::create([
                'user_id' => $user->user_id,
                'code' => QrCode::generateUniqueCode(),
            ]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("QR codes generated successfully!");
        
        return Command::SUCCESS;
    }
} 