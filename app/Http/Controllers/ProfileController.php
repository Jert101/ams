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

        // Check if non-admin user is trying to upload a profile photo
        if ($request->hasFile('profile_photo') && !$user->isAdmin()) {
            return redirect()->route('profile.show')->with('error', 'Only administrators can update profile pictures.');
        }

        // Handle profile photo upload - only for admin users
        if ($request->hasFile('profile_photo') && $user->isAdmin()) {
            try {
                Log::info('Admin is uploading a profile photo');
                
                // Add debugging information for paths and permissions
                Log::info('Path check', [
                    'public_path' => public_path('storage/profile-photos'),
                    'storage_path' => storage_path('app/public/profile-photos'),
                    'directory_exists_public' => file_exists(public_path('storage/profile-photos')),
                    'directory_exists_storage' => file_exists(storage_path('app/public/profile-photos')),
                    'is_writable_public' => is_writable(public_path('storage/profile-photos')),
                    'is_writable_storage' => is_writable(storage_path('app/public/profile-photos'))
                ]);
                
                // Create a simplified upload process that uses direct PHP functions
                $uploadedFile = $request->file('profile_photo');
                $extension = $uploadedFile->getClientOriginalExtension();
                $filename = time() . '-' . uniqid() . '.' . $extension;
                
                // Define paths
                $publicPath = public_path('storage/profile-photos');
                $storagePath = storage_path('app/public/profile-photos');
                
                // Ensure directories exist
                if (!file_exists($storagePath)) {
                    Log::info('Creating storage directory: ' . $storagePath);
                    if (!mkdir($storagePath, 0777, true)) {
                        $error = error_get_last();
                        Log::error('Failed to create profile photos directory in storage: ' . ($error ? $error['message'] : 'Unknown error'));
                        return redirect()->route('profile.show')->with('error', 'Failed to create upload directory in storage.');
                    }
                    // Set permissions explicitly after creation
                    chmod($storagePath, 0777);
                    Log::info('Storage directory created with permissions: ' . substr(sprintf('%o', fileperms($storagePath)), -4));
                }
                
                if (!file_exists($publicPath)) {
                    Log::info('Creating public directory: ' . $publicPath);
                    if (!mkdir($publicPath, 0777, true)) {
                        $error = error_get_last();
                        Log::error('Failed to create profile photos directory in public: ' . ($error ? $error['message'] : 'Unknown error'));
                        return redirect()->route('profile.show')->with('error', 'Failed to create upload directory in public.');
                    }
                    // Set permissions explicitly after creation
                    chmod($publicPath, 0777);
                    Log::info('Public directory created with permissions: ' . substr(sprintf('%o', fileperms($publicPath)), -4));
                }
                
                // Copy to both locations to ensure it works in both environments
                if ($uploadedFile->move($storagePath, $filename)) {
                    Log::info('File successfully moved to storage path: ' . $storagePath . '/' . $filename);
                    
                    // Update user's profile photo path
                    $relativePath = 'profile-photos/' . $filename;
                    $user->profile_photo_path = $relativePath;
                    
                    Log::info('User profile_photo_path updated to: ' . $relativePath);
                    
                    // Always make sure the file is in the public directory
                    if (!file_exists($publicPath)) {
                        if (!mkdir($publicPath, 0777, true)) {
                            Log::error('Failed to create public storage directory: ' . $publicPath);
                            $error = error_get_last();
                            Log::error('Error: ' . ($error ? $error['message'] : 'Unknown error'));
                        } else {
                            chmod($publicPath, 0777); // Ensure permissions are set
                            Log::info('Created public storage directory: ' . $publicPath);
                        }
                    }
                    
                    // Copy the file to the public directory
                    $sourceFile = $storagePath . '/' . $filename;
                    $destFile = $publicPath . '/' . $filename;
                    if (copy($sourceFile, $destFile)) {
                        Log::info('File copied to public directory: ' . $destFile);
                        // Set permissions on the copied file
                        chmod($destFile, 0644);
                        Log::info('Set permissions on public file: ' . $destFile);
                    } else {
                        $error = error_get_last();
                        Log::error('Failed to copy file to public directory: ' . $destFile);
                        Log::error('Error: ' . ($error ? $error['message'] : 'Unknown error'));
                    }
                } else {
                    throw new Exception('Failed to move uploaded file');
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
