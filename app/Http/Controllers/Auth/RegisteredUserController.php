<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'address' => ['nullable', 'string', 'max:255'],
            'mobile_number' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
        ]);

        // Get the Member role ID (default role for new registrations)
        $memberRole = \App\Models\Role::where('name', 'Member')->first();
        $roleId = $memberRole ? $memberRole->id : null;
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'mobile_number' => $request->mobile_number,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
            'role_id' => $roleId, // Assign the Member role by default
            'profile_photo_path' => 'kofa.png', // Default profile photo
            'approval_status' => 'pending', // Set approval status to pending
        ]);

        event(new Registered($user));
        
        // Store the user's email in session for later verification
        Session::put('pending_user_email', $user->email);
        Session::save(); // Explicitly save the session to ensure it persists
        Log::info('User registered and email stored in session: ' . $user->email);
        
        // Do not log in the user automatically
        // Instead, redirect to the verification pending page
        return redirect()->route('verification.pending', ['email' => $user->email]);
    }
}
