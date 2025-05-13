<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\QrCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateMemberQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrcodes:generate {--force : Force regeneration of all QR codes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR codes for all members who do not have one';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting QR code generation...');
        
        // Get members who either don't have a QR code or we're forcing regeneration
        $query = User::whereHas('role', function ($query) {
            $query->where('name', 'Member');
        });
        
        if (!$this->option('force')) {
            $query->whereDoesntHave('qrCode');
            $this->info('Generating QR codes only for members without one');
        } else {
            $this->info('Regenerating QR codes for all members');
        }
        
        $members = $query->get();
        
        if ($members->isEmpty()) {
            $this->info('All members already have QR codes.');
            return 0;
        }
        
        $bar = $this->output->createProgressBar(count($members));
        $bar->start();
        $generatedCount = 0;
        
        foreach ($members as $member) {
            $this->generateQrCodeForMember($member);
            $generatedCount++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated {$generatedCount} QR codes");
        
        return 0;
    }
    
    /**
     * Generate a QR code for a specific member.
     *
     * @param User $member
     * @return void
     */
    private function generateQrCodeForMember(User $member)
    {
        // Check if member already has a QR code and we're not forcing regeneration
        $existingQrCode = $member->qrCode;
        if ($existingQrCode && !$this->option('force')) {
            return;
        }
        
        // Generate a unique code
        $code = QrCode::generateUniqueCode();
        
        // The QR code generation would happen client-side with JavaScript
        // Here we're just creating the QrCode record
        if ($existingQrCode) {
            // Update the existing record
            $existingQrCode->update([
                'code' => $code,
                'is_active' => true,
            ]);
        } else {
            // Create a new record
            QrCode::create([
                'user_id' => $member->id,
                'code' => $code,
                'is_active' => true,
            ]);
        }
        
        $this->line("Generated QR code for {$member->name}: {$code}");
    }
} 