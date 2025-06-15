<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Exception;

class ProfileController extends Controller
{
    /**
     * Display the user's profile page with KofA branding.
     */
    public function show(Request $request): View
    {
        return view('profile.show', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        // Debug information
        Log::info('Profile update started', [
            'user_id' => $request->user()->id,
            'is_admin' => $request->user()->isAdmin(),
            'has_file' => $request->hasFile('profile_photo'),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type')
        ]);
        
        if ($request->hasFile('profile_photo')) {
            Log::info('Profile photo details', [
                'original_name' => $request->file('profile_photo')->getClientOriginalName(),
                'mime_type' => $request->file('profile_photo')->getMimeType(),
                'size' => $request->file('profile_photo')->getSize(),
                'error' => $request->file('profile_photo')->getError()
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'profile_photo' => ['nullable', 'mimes:jpg,jpeg,png,gif,webp,bmp,heic', 'max:4096'],
        ]);

        $user = $request->user();
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            try {
                $file = $request->file('profile_photo');
                $filename = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path(), $filename); // Save to /public
                $user->profile_photo_path = $filename; // Store only the filename
            } catch (Exception $e) {
                Log::error('Failed to upload profile photo: ' . $e->getMessage());
                return redirect()->route('profile.show')->with('error', 'Failed to upload profile photo.');
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        Log::info('Saving user changes', [
            'dirty_attributes' => $user->getDirty()
        ]);
        
        $user->save();
        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Only admin users can delete accounts
        if (!$request->user()->isAdmin()) {
            return redirect()->route('profile.show')->with('error', 'You do not have permission to delete accounts.');
        }

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
