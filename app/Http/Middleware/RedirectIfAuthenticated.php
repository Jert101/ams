<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Redirect based on user role
                $user = Auth::guard($guard)->user();
                
                if ($user->hasRole('Admin')) {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->hasRole('Officer')) {
                    return redirect()->route('officer.dashboard');
                } elseif ($user->hasRole('Secretary')) {
                    return redirect()->route('secretary.dashboard');
                } elseif ($user->hasRole('Member')) {
                    return redirect()->route('member.dashboard');
                }
                
                return redirect('/');
            }
        }

        return $next($request);
    }
}
