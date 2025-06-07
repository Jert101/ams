<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class QrCodeController extends Controller
{
    /**
     * Display the user's QR code.
     */
    public function show()
    {
        $user = Auth::user();
        $qrCode = $user->qrCode;
        
        if (!$qrCode) {
            return redirect()->route('dashboard')
                ->with('error', 'No QR code found for your account. Please contact an administrator.');
        }
        
        return view('qrcode.show', compact('qrCode'));
    }
    
    /**
     * Display a printable ID card for the user.
     */
    public function printCard()
    {
        $user = Auth::user();
        $qrCode = $user->qrCode;
        
        if (!$user->qrCode) {
            return redirect()->route('dashboard')
                ->with('error', 'No QR code found for your account. Please contact an administrator.');
        }
        
        return view('qrcode.print-card', compact('user', 'qrCode'));
    }
    
    /**
     * Print multiple QR code badges for a set of users (admin only).
     */
    public function printBatch(Request $request)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this page.');
        }
        
        // Get user IDs from request
        $userIds = $request->input('user_ids', []);
        
        // If no user IDs provided, show form to select users
        if (empty($userIds)) {
            $users = User::whereHas('qrCode')->with('qrCode')->get();
            return view('admin.qrcode.batch-print', compact('users'));
        }
        
        // Get users with their QR codes
        $users = User::whereIn('user_id', $userIds)
                    ->whereHas('qrCode')
                    ->with('qrCode')
                    ->get();
        
        return view('admin.qrcode.print-multiple', compact('users'));
    }
    
    /**
     * Generate a new QR code for a user (admin only).
     */
    public function regenerate(Request $request, $userId)
    {
        // Check if user is admin
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to perform this action.');
        }
        
        $user = User::where('user_id', $userId)->first();
        
        if (!$user) {
            return back()->with('error', 'User not found.');
        }
        
        // Generate a new QR code that includes the user ID in the format
        // This helps with identification even if the QR code record is lost
        $code = QrCode::generateCodeWithUserId($user->user_id);
        
        // Update or create QR code record
        $qrCode = QrCode::updateOrCreate(
            ['user_id' => $user->user_id],
            [
                'code' => $code,
                // Storing this format helps with identifying users even if the QR code record is lost
                // The actual QR code contains the user ID for easy identification
            ]
        );
        
        return back()->with('success', 'QR code regenerated successfully.');
    }
    
    /**
     * Display page to manage all users' QR codes (admin only).
     */
    public function manageQrCodes()
    {
        // Get all users with their QR codes
        $users = User::with('qrCode', 'role')
                    ->orderBy('name')
                    ->paginate(20);
        
        return view('admin.qrcode.manage', compact('users'));
    }
    
    /**
     * View QR code for a specific user (admin only).
     */
    public function viewUserQrCode($userId)
    {
        $user = User::with('qrCode')
                    ->where('user_id', $userId)
                    ->firstOrFail();
        
        if (!$user->qrCode) {
            return redirect()->route('admin.qrcode.manage')
                ->with('error', 'No QR code found for this user.');
        }
        
        return view('admin.qrcode.view', compact('user'));
    }
    
    /**
     * Print QR code for a specific user (admin only).
     */
    public function printUserQrCode($userId)
    {
        // Load user with all relationships needed for the card
        $user = User::with(['qrCode', 'role'])
                    ->where('user_id', $userId)
                    ->firstOrFail();
        
        // If no QR code found, generate one
        if (!$user->qrCode) {
            $qrCode = QrCode::create([
                'user_id' => $user->user_id,
                'code' => QrCode::generateCodeWithUserId($user->user_id)
            ]);
            
            // Reload the user with the new QR code
            $user = User::with(['qrCode', 'role'])
                        ->where('user_id', $userId)
                        ->firstOrFail();
        } else {
            $qrCode = $user->qrCode;
        }
        
        // Force-load the user data
        $user->refresh();
        
        // Make sure all necessary fields are set
        if (empty($user->address)) {
            $user->address = 'Not Available';
        }
        
        if (empty($user->gender)) {
            $user->gender = 'Not Available';
        }
        
        if (empty($user->mobile_number)) {
            $user->mobile_number = 'Not Available';
        }
        
        // Return the view using the admin template
        return view('admin.qrcode.print-card', [
            'user' => $user,
            'qrCode' => $qrCode
        ]);
    }
    
    /**
     * Generate a QR code for a user.
     */
    public function generate(Request $request)
    {
        $user = Auth::user();
        
        // Check if user already has a QR code
        $qrCode = QrCode::where('user_id', $user->user_id)->first();
        
        if ($qrCode) {
            return redirect()->route('qrcode.view')
                ->with('info', 'You already have a QR code.');
        }
        
        // Generate a new QR code with user ID embedded
        $code = QrCode::generateCodeWithUserId($user->user_id);
        
        // Create QR code record
        QrCode::create([
            'user_id' => $user->user_id,
            'code' => $code,
        ]);
        
        return redirect()->route('qrcode.view')
            ->with('success', 'QR code generated successfully.');
    }
    
    /**
     * Debug user data (admin only).
     */
    public function debugUserData($userId)
    {
        // Load user with all relationships needed for the card
        $user = User::with(['qrCode', 'role'])
                    ->where('user_id', $userId)
                    ->firstOrFail();
        
        // Make sure all necessary fields are loaded
        $userData = [
            'name' => $user->name ?? 'N/A',
            'user_id' => $user->user_id ?? 'N/A',
            'email' => $user->email ?? 'N/A',
            'role' => $user->role,
            'date_of_birth' => $user->date_of_birth ?? null,
            'gender' => $user->gender ?? 'N/A',
            'address' => $user->address ?? 'N/A',
            'mobile_number' => $user->mobile_number ?? 'N/A',
        ];
        
        // Get the QR code
        $qrCode = $user->qrCode;
        
        // Return debug view
        return view('qrcode.print-debug', [
            'user' => $user,
            'qrCode' => $qrCode,
            'userData' => $userData
        ]);
    }
    
    /**
     * Test with a simple PHP view (no Blade).
     */
    public function testSimpleView($userId)
    {
        // Load user
        $user = User::with('qrCode')
                    ->where('user_id', $userId)
                    ->firstOrFail();
        
        // Return a simple PHP view
        return view('qrcode.simple-test', ['user' => $user]);
    }
} 