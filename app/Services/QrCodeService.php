<?php

namespace App\Services;

use App\Models\QrCode;
use App\Models\User;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    /**
     * Generate a QR code for a user
     *
     * @param User $user
     * @return QrCode
     */
    public function generateForUser(User $user)
    {
        // Check if user already has a QR code
        $qrCode = $user->qrCode;
        
        if (!$qrCode) {
            // Create a new QR code record
            $qrCode = new QrCode([
                'user_id' => $user->id,
                'code' => QrCode::generateUniqueCode(),
                'is_active' => true,
            ]);
            $qrCode->save();
        }
        
        return $qrCode;
    }
    
    /**
     * Generate the QR code SVG for a user
     *
     * @param User $user
     * @return string
     */
    public function generateSvg(User $user)
    {
        // Get or create QR code
        $qrCode = $this->generateForUser($user);
        
        // Generate QR code data
        $qrData = json_encode([
            'code' => $qrCode->code,
            'user_id' => $user->id,
            'timestamp' => now()->timestamp,
            'signature' => hash('sha256', $qrCode->code . $user->id . env('APP_KEY'))
        ]);
        
        // Generate SVG
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        
        return $writer->writeString($qrData);
    }
}
