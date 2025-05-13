<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeController extends Controller
{
    /**
     * Generate a unique QR code for the authenticated member
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = Auth::user();
        
        // Generate a unique identifier for the QR code
        $qrCodeData = json_encode([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'timestamp' => now()->timestamp,
            'signature' => hash('sha256', $user->id . $user->email . env('APP_KEY'))
        ]);
        
        // Generate QR code
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($qrCodeData);
        
        return view('member.qrcode', [
            'qrCode' => $qrCode,
            'user' => $user
        ]);
    }
}
