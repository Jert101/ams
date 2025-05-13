<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    /**
     * Display a listing of the members.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get the member role ID
        $memberRoleId = Role::where('name', 'Member')->first()->id;
        
        // Get all members
        $members = User::where('role_id', $memberRoleId)
            ->orderBy('name')
            ->paginate(15);
            
        // Get attendance statistics for each member
        foreach ($members as $member) {
            $totalAttendances = Attendance::where('user_id', $member->id)->count();
            $presentCount = Attendance::where('user_id', $member->id)
                ->where('status', 'present')
                ->count();
                
            $member->attendance_rate = $totalAttendances > 0 
                ? round(($presentCount / $totalAttendances) * 100, 1) . '%' 
                : '0%';
                
            $member->consecutive_absences = $this->getConsecutiveAbsences($member->id);
        }
        
        return view('secretary.members.index', compact('members'));
    }
    
    /**
     * Get the number of consecutive absences for a member.
     *
     * @param int $userId
     * @return int
     */
    private function getConsecutiveAbsences($userId)
    {
        $recentAttendances = Attendance::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
            
        $consecutiveAbsences = 0;
        
        foreach ($recentAttendances as $attendance) {
            if ($attendance->status === 'absent') {
                $consecutiveAbsences++;
            } else {
                break;
            }
        }
        
        return $consecutiveAbsences;
    }
}
