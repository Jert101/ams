<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Get the authenticated user
        $user = auth()->user();
        
        // Redirect based on user role
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        } elseif ($user->isOfficer()) {
            return redirect()->intended(route('officer.dashboard', absolute: false));
        } elseif ($user->isSecretary()) {
            return redirect()->intended(route('secretary.dashboard', absolute: false));
        } elseif ($user->isMember()) {
            return redirect()->intended(route('member.dashboard', absolute: false));
        }
        
        // Fallback to home page if no role-specific dashboard is available
        return redirect()->intended('/');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
    
    /**
     * Show the facial recognition login form.
     */
    public function showFacialLoginForm(): View
    {
        return view('auth.facial-login');
    }
}
