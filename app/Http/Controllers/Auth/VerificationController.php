<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    /**
     * Display the verification pending page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPendingPage(Request $request)
    {
        // First priority: Check if email is in request
        $requestEmail = $request->input('email');
        $sessionEmail = Session::get('pending_user_email');
        $email = $requestEmail ?: $sessionEmail;
        
        Log::info('Verification pending check - Request email: ' . ($requestEmail ?? 'null') . ', Session email: ' . ($sessionEmail ?? 'null'));
        
        if ($email) {
            // Find the user by email
            $user = User::where('email', $email)->first();
            
            if ($user) {
                Log::info('User found with email: ' . $email . ', approval status: ' . $user->approval_status);
                
                // Store the email in session for future checks if it came from request
                if ($requestEmail && !$sessionEmail) {
                    Session::put('pending_user_email', $requestEmail);
                    Log::info('Storing email in session: ' . $requestEmail);
                }
                
                // Check if the user is approved
                if ($user->isApproved()) {
                    // Clear the pending user session
                    Session::forget('pending_user_email');
                    Log::info('User is approved, redirecting to login page');
                    
                    // Redirect to login page with success message
                    return redirect()->route('login')
                        ->with('status', 'Your account has been approved! You can now log in.');
                } elseif ($user->isRejected()) {
                    // Clear the pending user session
                    Session::forget('pending_user_email');
                    Log::info('User is rejected, redirecting to login page with rejection message');
                    
                    // Redirect to login page with rejection message
                    return redirect()->route('login')
                        ->with('error', 'Your account registration has been rejected. Reason: ' . ($user->rejection_reason ?? 'Not specified.'));
                }
                
                // If we get here, the user is pending approval
                return view('auth.verification-pending', [
                    'email' => $email,
                    'status' => 'pending'
                ]);
            } else {
                Log::warning('User not found with email: ' . $email);
                // Clear the invalid email from session
                if ($sessionEmail) {
                    Session::forget('pending_user_email');
                }
            }
        }
        
        // If we get here, there's no valid email or user found
        // Show generic pending page or redirect to login
        return view('auth.verification-pending', [
            'email' => null,
            'status' => 'unknown'
        ]);
    }
    
    /**
     * Check the approval status of a user via AJAX
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStatus(Request $request)
    {
        try {
            $email = $request->input('email') ?? Session::get('pending_user_email');
            
            \Log::info('Check status request received with email: ' . ($email ?? 'null'));
            
            if (!$email) {
                \Log::warning('No email provided for status check');
                return response()->json([
                    'status' => 'error',
                    'message' => 'No email provided'
                ], 200, ['Content-Type' => 'application/json']);
            }
            
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                \Log::warning('User not found with email: ' . $email);
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 200, ['Content-Type' => 'application/json']);
            }
            
            // Log the user status for debugging
            \Log::info('Found user with ID: ' . $user->user_id . ', Status: ' . $user->approval_status);
            
            // Force refresh user data from the database
            $user = User::where('email', $email)->first();
            
            $status = $user->approval_status;
            
            return response()->json([
                'status' => 'success',
                'approval_status' => $status,
                'redirect' => $status === 'approved' ? route('login') : null,
                'debug_info' => [
                    'user_id' => $user->user_id,
                    'timestamp' => now()->toIso8601String()
                ]
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            \Log::error('Error checking approval status: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Server error while checking status',
                'debug_info' => app()->environment('local') ? [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 200, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Diagnostic endpoint to check if a user exists and has the approval_status field set
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUserExists(Request $request)
    {
        try {
            $email = $request->input('email');
            
            if (!$email) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No email provided'
                ], 200, ['Content-Type' => 'application/json']);
            }
            
            // Check if the user exists
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User does not exist',
                    'exists' => false
                ], 200, ['Content-Type' => 'application/json']);
            }
            
            // Check if approval_status field exists and is set
            $hasApprovalStatus = isset($user->approval_status);
            
            return response()->json([
                'status' => 'success',
                'exists' => true,
                'has_approval_status' => $hasApprovalStatus,
                'approval_status' => $user->approval_status,
                'user_id' => $user->user_id,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error checking user: ' . $e->getMessage()
            ], 200, ['Content-Type' => 'application/json']);
        }
    }
}
