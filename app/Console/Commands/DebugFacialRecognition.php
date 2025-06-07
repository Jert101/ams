<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\FacialData;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DebugFacialRecognition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facial:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug facial recognition data in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Debugging facial recognition data...');

        // Check users table structure
        $this->info('Checking users table structure...');
        $userColumns = \DB::getSchemaBuilder()->getColumnListing('users');
        $this->info('Users table columns: ' . implode(', ', $userColumns));
        
        $hasFaceImagePath = in_array('face_image_path', $userColumns);
        $hasFaceEncoding = in_array('face_encoding', $userColumns);
        
        $this->info('face_image_path column exists: ' . ($hasFaceImagePath ? 'Yes' : 'No'));
        $this->info('face_encoding column exists: ' . ($hasFaceEncoding ? 'Yes' : 'No'));
        
        // Check facial_data table structure
        $this->info("\nChecking facial_data table structure...");
        try {
            $facialDataColumns = \DB::getSchemaBuilder()->getColumnListing('facial_data');
            $this->info('Facial data table columns: ' . implode(', ', $facialDataColumns));
            
            $facialDataCount = FacialData::count();
            $this->info("Total facial data records: $facialDataCount");
            
            // Check facial data entries
            if ($facialDataCount > 0) {
                $this->info("\nFacial data entries:");
                $facialData = FacialData::all();
                foreach ($facialData as $data) {
                    $user = User::where('user_id', $data->user_id)->first();
                    $userName = $user ? $user->name : 'Unknown';
                    
                    $this->info("- User: $userName (ID: {$data->user_id})");
                    $this->info("  Face image path: " . ($data->face_image_path ?: 'None'));
                    $this->info("  Has encoding: " . ($data->face_encoding ? 'Yes ('.strlen($data->face_encoding).' bytes)' : 'No'));
                    $this->info("  Is verified: " . ($data->is_verified ? 'Yes' : 'No'));
                    
                    // Check if the image file exists
                    if ($data->face_image_path) {
                        $exists = Storage::disk('public')->exists($data->face_image_path);
                        $this->info("  Image file exists: " . ($exists ? 'Yes' : 'No'));
                    }
                    
                    // Check encoding data
                    if ($data->face_encoding) {
                        try {
                            $encoding = json_decode($data->face_encoding, true);
                            $this->info("  Encoding valid JSON: " . ($encoding !== null ? 'Yes' : 'No'));
                            $this->info("  Encoding length: " . (is_array($encoding) ? count($encoding) : 'Not an array'));
                        } catch (\Exception $e) {
                            $this->error("  Error decoding face_encoding: " . $e->getMessage());
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error("Error accessing facial_data table: " . $e->getMessage());
        }
        
        // Check users with facial data in User model
        $this->info("\nChecking users with facial data in User model...");
        $usersWithFacialData = User::whereNotNull('face_encoding')->get();
        $this->info("Users with face_encoding in User model: " . count($usersWithFacialData));
        
        foreach ($usersWithFacialData as $user) {
            $this->info("- User: {$user->name} (ID: {$user->user_id})");
            $this->info("  Face image path: " . ($user->face_image_path ?: 'None'));
            $this->info("  Has encoding: " . ($user->face_encoding ? 'Yes ('.strlen($user->face_encoding).' bytes)' : 'No'));
            
            // Check if the image file exists
            if ($user->face_image_path) {
                $exists = Storage::disk('public')->exists($user->face_image_path);
                $this->info("  Image file exists: " . ($exists ? 'Yes' : 'No'));
            }
            
            // Check encoding data
            if ($user->face_encoding) {
                try {
                    $encoding = json_decode($user->face_encoding, true);
                    $this->info("  Encoding valid JSON: " . ($encoding !== null ? 'Yes' : 'No'));
                    $this->info("  Encoding length: " . (is_array($encoding) ? count($encoding) : 'Not an array'));
                } catch (\Exception $e) {
                    $this->error("  Error decoding face_encoding: " . $e->getMessage());
                }
            }
        }
        
        // Test image processing functionality
        $this->info("\nTesting image processing functionality...");
        try {
            $testImagePath = public_path('kofa.png');
            if (file_exists($testImagePath)) {
                $this->info("Test image exists: $testImagePath");
                
                // Test image read
                $imageData = file_get_contents($testImagePath);
                $this->info("Image read successful: " . ($imageData ? 'Yes ('.strlen($imageData).' bytes)' : 'No'));
                
                // Test image creation
                $image = @imagecreatefromstring($imageData);
                $this->info("Image creation successful: " . ($image ? 'Yes' : 'No'));
                
                if ($image) {
                    $width = imagesx($image);
                    $height = imagesy($image);
                    $this->info("Image dimensions: {$width}x{$height}");
                    
                    // Test image resize
                    $resized = imagecreatetruecolor(32, 32);
                    $result = imagecopyresampled($resized, $image, 0, 0, 0, 0, 32, 32, $width, $height);
                    $this->info("Image resize successful: " . ($result ? 'Yes' : 'No'));
                    
                    // Free memory
                    imagedestroy($image);
                    imagedestroy($resized);
                }
            } else {
                $this->error("Test image not found: $testImagePath");
            }
        } catch (\Exception $e) {
            $this->error("Error testing image processing: " . $e->getMessage());
        }
        
        $this->info("\nFacial recognition debugging complete.");
        return Command::SUCCESS;
    }
}
