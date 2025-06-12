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
            'profile_photo' => ['nullable', 'image', 'max:1024'],
        ]);

        $user = $request->user();
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Allow all users to upload profile photos
        // Previously, only admins could update profile pictures, but now we're allowing all users
        
        // Handle profile photo upload - for all users
        if ($request->hasFile('profile_photo')) {
            try {
                Log::info('User is uploading a profile photo');
                $uploadedFile = $request->file('profile_photo');
                if ($uploadedFile->isValid()) {
                    $extension = $uploadedFile->getClientOriginalExtension();
                    $filename = time() . '-' . uniqid() . '.' . $extension;
                    $storagePath = storage_path('app/public/profile-photos');
                    $publicPath = public_path('storage/profile-photos');
                    // Ensure directories exist
                    foreach ([$storagePath, $publicPath] as $dir) {
                        if (!file_exists($dir)) {
                            if (!mkdir($dir, 0777, true)) {
                                $error = error_get_last();
                                Log::error('Failed to create directory: ' . $dir . ' - ' . ($error ? $error['message'] : 'Unknown error'));
                                return redirect()->route('profile.show')->with('error', 'Failed to create upload directory.');
                            }
                        }
                    }
                    // Try to move to storage
                    if ($uploadedFile->move($storagePath, $filename)) {
                        Log::info('File successfully moved to storage path: ' . $storagePath . '/' . $filename);
                        // Set permissions
                        chmod($storagePath . '/' . $filename, 0644);
                        // Copy to public directory
                        if (copy($storagePath . '/' . $filename, $publicPath . '/' . $filename)) {
                            Log::info('File copied to public directory: ' . $publicPath . '/' . $filename);
                            chmod($publicPath . '/' . $filename, 0644);
                        } else {
                            $error = error_get_last();
                            Log::warning('Could not copy to public directory: ' . ($error ? $error['message'] : 'Unknown error'));
                        }
                        // Only update if upload succeeded
                        $user->profile_photo_path = 'profile-photos/' . $filename;
                        Log::info('User profile_photo_path updated to: ' . $user->profile_photo_path);
                    } else {
                        $error = error_get_last();
                        Log::error('Failed to move uploaded file: ' . ($error ? $error['message'] : 'Unknown error'));
                        return redirect()->route('profile.show')->with('error', 'Failed to upload profile photo.');
                    }
                } else {
                    Log::error('Uploaded file is not valid');
                    return redirect()->route('profile.show')->with('error', 'Uploaded file is not valid.');
                }
            } catch (Exception $e) {
                Log::error('Profile photo upload error: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->route('profile.show')->with('error', 'Failed to upload profile photo: ' . $e->getMessage());
            }
        } else {
            Log::info('No profile photo uploaded or user is not admin', [
                'has_file' => $request->hasFile('profile_photo'),
                'is_admin' => $user->isAdmin()
            ]);
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        Log::info('Saving user changes', [
            'dirty_attributes' => $user->getDirty()
        ]);
        
        $user->save();

        return redirect()->route('profile.show')->with('status', 'profile-updated');
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
