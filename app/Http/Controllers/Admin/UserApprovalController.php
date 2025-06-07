<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountApproved;
use App\Notifications\AccountRejected;
use Illuminate\Http\Request;

class UserApprovalController extends Controller
{
    /**
     * Display a listing of pending user registrations.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pendingUsers = User::where('approval_status', 'pending')->get();
        return view('admin.approvals.index', compact('pendingUsers'));
    }

    /**
     * Show a specific pending user registration.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('admin.approvals.show', compact('user'));
    }

    /**
     * Approve a user registration.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function approve(User $user)
    {
        $user->update([
            'approval_status' => 'approved'
        ]);
        
        // Send notification to the user
        try {
            $user->notify(new AccountApproved());
        } catch (\Exception $e) {
            // Log the error but continue
            \Log::error("Failed to send approval notification: " . $e->getMessage());
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User registration has been approved.'
            ]);
        }

        return redirect()->route('admin.approvals.index')
            ->with('success', 'User registration has been approved.');
    }

    /**
     * Reject a user registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, User $user)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);

        $user->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);
        
        // Send notification to the user with the rejection reason
        try {
            $user->notify(new AccountRejected($validated['rejection_reason']));
        } catch (\Exception $e) {
            // Log the error but continue
            \Log::error("Failed to send rejection notification: " . $e->getMessage());
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User registration has been rejected.'
            ]);
        }

        return redirect()->route('admin.approvals.index')
            ->with('success', 'User registration has been rejected.');
    }
}
