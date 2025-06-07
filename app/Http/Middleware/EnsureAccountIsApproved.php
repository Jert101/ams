<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->approval_status === 'pending') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Store the email in session for verification page
                $request->session()->put('pending_user_email', $user->email);
                
                return redirect()->route('verification.pending', ['email' => $user->email])
                    ->with('status', 'Your account is still pending approval.');
            } elseif ($user->approval_status === 'rejected') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                $reason = $user->rejection_reason ?? 'No reason provided.';
                
                return redirect()->route('login')
                    ->with('error', 'Your account has been rejected. Reason: ' . $reason);
            }
        }

        return $next($request);
    }
}
