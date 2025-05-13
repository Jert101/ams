<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // No middleware here as this controller handles both authenticated and guest users
    }

    /**
     * Show the application dashboard or redirect based on user role.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Redirect based on user role
            if ($user->role && $user->role->name === 'Admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role && $user->role->name === 'Officer') {
                return redirect()->route('officer.dashboard');
            } elseif ($user->role && $user->role->name === 'Secretary') {
                return redirect()->route('secretary.dashboard');
            } elseif ($user->role && $user->role->name === 'Member') {
                return redirect()->route('member.dashboard');
            }
            
            // If no specific role or role not found, show welcome page
            return view('welcome');
        }
        
        // If not authenticated, show welcome page
        return view('welcome');
    }
}
