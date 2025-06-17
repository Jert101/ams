<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        // Update last_seen_at for the current user
        if ($request->user()) {
            $request->user()->update(['last_seen_at' => now()]);
        }
        // Get search query
        $search = $request->query('search');
        
        // Check for filters
        $filter = $request->query('filter');
        
        $query = User::with('role', 'qrCode');
        
        // Exclude rejected users
        $query->where('approval_status', '!=', 'rejected');
        
        // Apply search if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('user_id', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }
        
        // Apply filters if any
        if ($filter === 'members-with-qr') {
            $query->whereHas('role', function($q) {
                $q->where('name', 'Member');
            })->whereHas('qrCode');
        }
        
        $users = $query->paginate(10);
        
        // Keep search parameter in pagination links
        $users->appends(['search' => $search, 'filter' => $filter]);
        
        $now = now();
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.users.partials.user-list', ['users' => $users, 'now' => $now])->render()
            ]);
        }
        return view('admin.users.index', compact('users', 'filter', 'search', 'now'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'address' => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'profile_photo' => 'nullable|mimes:jpg,jpeg,png,gif,webp,bmp,heic|max:4096',
        ]);
        
        // Generate random password if not provided
        if (empty($validated['password'])) {
            $password = substr(str_shuffle('abcdefghjklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*()'), 0, 12);
        } else {
            $password = $validated['password'];
        }

        // Create the user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role_id' => $validated['role_id'],
            'address' => $validated['address'] ?? null,
            'mobile_number' => $validated['mobile_number'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'profile_photo_path' => 'kofa.png', // Default profile photo
            'approval_status' => 'approved', // Auto-approve users created by admin
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $uploadDir = base_path('uploads'); // uploads folder outside public
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $file->move($uploadDir, $filename); // Save to /uploads
            $user->profile_photo_path = $filename; // Store only the filename
            $user->save();
        }

        // Generate QR code for the user
        QrCode::create([
            'user_id' => $user->user_id,
            'code' => QrCode::generateCodeWithUserId($user->user_id),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('role', 'qrCode', 'attendances.event');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        \Log::info('Admin UserController@update called', [
            'user_id' => $user->user_id ?? null,
            'request_user_id' => $request->input('user_id'),
            'has_file' => $request->hasFile('profile_photo'),
            'all_request' => $request->all()
        ]);

        // Handle QR code generation request
        if ($request->has('generate_qr')) {
            // Check if the user already has a QR code
            if (!$user->qrCode) {
                // Create a new QR code for the user with user ID embedded for better identification
                QrCode::create([
                    'user_id' => $user->user_id,
                    'code' => QrCode::generateCodeWithUserId($user->user_id),
                ]);
                
                return redirect()->route('admin.users.show', $user)
                    ->with('success', 'QR code generated successfully.');
            }
            
            return redirect()->route('admin.users.show', $user)
                ->with('info', 'User already has a QR code.');
        }
        
        // Regular user update
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->user_id, 'user_id'),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'address' => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'profile_photo' => 'nullable|mimes:jpg,jpeg,png,gif,webp,bmp,heic|max:4096',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'address' => $validated['address'] ?? null,
            'mobile_number' => $validated['mobile_number'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }
        
        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            \Log::info('Profile photo upload detected', [
                'original_name' => $request->file('profile_photo')->getClientOriginalName(),
                'mime_type' => $request->file('profile_photo')->getMimeType(),
                'size' => $request->file('profile_photo')->getSize(),
                'error' => $request->file('profile_photo')->getError()
            ]);

            try {
                // Delete old photo if exists and it's not the default
                if ($user->profile_photo_path && $user->profile_photo_path !== 'kofa.png') {
                    \Log::info('Attempting to delete old photo', ['old_path' => $user->profile_photo_path]);
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                
                // Store the file
                $file = $request->file('profile_photo');
                $filename = uniqid() . '.' . $file->getClientOriginalExtension();
                $uploadDir = base_path('uploads'); // uploads folder outside public
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                    }
                $file->move($uploadDir, $filename); // Save to /uploads
                $userData['profile_photo_path'] = $filename; // Store only the filename
                
                \Log::info('Profile photo uploaded successfully', [
                    'filename' => $filename,
                    'full_path' => $uploadDir . '/' . $filename,
                    'web_path' => $userData['profile_photo_path'],
                    'file_exists' => file_exists($uploadDir . '/' . $filename),
                    'file_permissions' => substr(sprintf('%o', fileperms($uploadDir . '/' . $filename)), -4)
                ]);
            } catch (\Exception $e) {
                \Log::error('Profile photo upload failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return back()->with('error', 'Failed to upload profile photo. Please try again.');
            }
        }

        \Log::info('Updating user with data', ['userData' => $userData]);
        $user->update($userData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Using soft delete as defined in the User model
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
