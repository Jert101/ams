<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        // First check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }
        
        // Check if user has the required role
        // Roles can be comma-separated
        $rolesArray = explode(',', $roles);
        
        foreach ($rolesArray as $role) {
            $roleToCheck = trim($role);
            // Make case-insensitive comparison
            if ($request->user()->role && strtolower($request->user()->role->name) === strtolower($roleToCheck)) {
                return $next($request);
            }
        }
        
        abort(403, 'Unauthorized action. You do not have the required role to access this page.');
        
        return $next($request);
    }
}
