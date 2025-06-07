<?php

namespace App\Console\Commands;

use App\Models\QrCode;
use Illuminate\Console\Command;

class ListQrCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qrcode:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all QR codes in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $qrCodes = QrCode::with('user')->get();
        
        if ($qrCodes->isEmpty()) {
            $this->error('No QR codes found in the system.');
            return 1;
        }
        
        $this->info('Listing all QR codes:');
        $this->newLine();
        
        $headers = ['User ID', 'User Name', 'QR Code', 'Created At'];
        $rows = [];
        
        foreach ($qrCodes as $qrCode) {
            $rows[] = [
                $qrCode->user->user_id,
                $qrCode->user->name,
                $qrCode->code,
                $qrCode->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        $this->table($headers, $rows);
        
        return 0;
    }
}
