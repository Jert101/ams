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
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'profile_photo' => ['nullable', 'image', 'max:1024'],
        ]);

        $user = $request->user();
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Check if non-admin user is trying to upload a profile photo
        if ($request->hasFile('profile_photo') && !$user->isAdmin()) {
            return redirect()->route('profile.show')->with('error', 'Only administrators can update profile pictures.');
        }

        // Handle profile photo upload - only for admin users
        if ($request->hasFile('profile_photo') && $user->isAdmin()) {
            try {
                // Check if the storage directory exists and is writable
                $storageDirectory = storage_path('app/public/profile-photos');
                
                if (!file_exists($storageDirectory)) {
                    if (!mkdir($storageDirectory, 0755, true)) {
                        Log::error('Failed to create profile photos directory: ' . $storageDirectory);
                        return redirect()->route('profile.show')->with('error', 'Failed to create upload directory. Please contact the administrator.');
                    }
                }
                
                if (!is_writable($storageDirectory)) {
                    Log::error('Profile photos directory is not writable: ' . $storageDirectory);
                    return redirect()->route('profile.show')->with('error', 'Upload directory is not writable. Please contact the administrator.');
                }
                
                // Check if storage link exists
                $publicStorageLink = public_path('storage');
                if (!file_exists($publicStorageLink)) {
                    Log::error('Public storage link does not exist: ' . $publicStorageLink);
                    return redirect()->route('profile.show')->with('error', 'Storage link is missing. Please run "php artisan storage:link".');
                }
                
                // Delete old photo if exists and it's not the default
                if ($user->profile_photo_path && 
                    $user->profile_photo_path !== 'kofa.png' && 
                    $user->profile_photo_path !== '0' &&
                    $user->profile_photo_path !== 0 &&
                    !empty($user->profile_photo_path)) {
                    try {
                        if (Storage::disk('public')->exists($user->profile_photo_path)) {
                            Storage::disk('public')->delete($user->profile_photo_path);
                        }
                    } catch (Exception $e) {
                        Log::warning('Failed to delete old profile photo: ' . $e->getMessage());
                        // Continue anyway
                    }
                }
                
                // Store the new photo
                $path = $request->file('profile_photo')->store('profile-photos', 'public');
                
                if (!$path) {
                    throw new Exception('Failed to store profile photo');
                }
                
                $user->profile_photo_path = $path;
                
                // Log success
                Log::info('Profile photo uploaded successfully for user ID: ' . $user->id . ' to path: ' . $path);
                
            } catch (Exception $e) {
                Log::error('Profile photo upload error: ' . $e->getMessage());
                return redirect()->route('profile.show')->with('error', 'Failed to upload profile photo: ' . $e->getMessage());
            }
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

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
