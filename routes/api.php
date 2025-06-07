<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReactController;


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/firebase/sync', 'App\Http\Controllers\Api\FirebaseController@sync');
    Route::post('/firebase/attendance', 'App\Http\Controllers\Api\FirebaseController@recordAttendance');
    // Admin routes
    Route::get('/admin/users', [ReactController::class, 'getUserList']);
    Route::get('/admin/dashboard', [ReactController::class, 'getAdminDashboardData']);
    
    // Member routes
    Route::get('/member/dashboard', [ReactController::class, 'getMemberDashboardData']);
    
    // Officer routes
    Route::post('/officer/scan', [ReactController::class, 'processQRScan']);
});
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); 