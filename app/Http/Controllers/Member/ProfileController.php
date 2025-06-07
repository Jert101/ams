<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the member's profile.
     */
    public function show()
    {
        $user = Auth::user();
        return view('member.profile.show', compact('user'));
    }
    
    /**
     * Show the form for editing the member's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('member.profile.edit', compact('user'));
    }
    
    /**
     * Update the member's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->user_id, 'user_id'),
            ],
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        // Check current password if trying to change password
        if ($request->filled('current_password')) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors([
                    'current_password' => 'The current password is incorrect.',
                ]);
            }
        }
        
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];
        
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validated['password']);
        }
        
        $user->update($userData);
        
        return redirect()->route('member.profile.show')
            ->with('success', 'Profile updated successfully.');
    }
    
    /**
     * Show the member's QR code.
     */
    public function showQrCode()
    {
        $user = Auth::user();
        $qrCode = $user->qrCode;
        
        if (!$qrCode) {
            return redirect()->route('member.profile.show')
                ->with('error', 'No QR code found for your account. Please contact an administrator.');
        }
        
        return view('member.profile.qrcode', compact('qrCode'));
    }
}
