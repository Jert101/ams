<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // First check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }
        
        // Check if user has the Admin role
        if ($request->user()->isAdmin()) {
            return $next($request);
        }
        
        // If not admin, redirect with error
        return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
    }
} 