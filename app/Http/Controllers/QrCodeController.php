<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user's QR code.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $user = auth()->user();
        $qrCode = $user->qrCode;

        // If user doesn't have a QR code yet, create one
        if (!$qrCode) {
            $qrCode = \App\Models\QrCode::create([
                'user_id' => $user->id,
                'code' => \App\Models\QrCode::generateUniqueCode(),
                'is_active' => true,
            ]);
        }

        return view('qrcode.show', compact('qrCode'));
    }
}
