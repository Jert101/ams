<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ElectionArchive;
use App\Models\ElectionCandidate;
use App\Models\ElectionPosition;
use App\Models\ElectionSetting;
use App\Models\ElectionVote;
use App\Models\Role;
use App\Services\ElectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ElectionController extends Controller
{
    /**
     * Display the election settings and management page.
     */
    public function index()
    {
        // Check and update status based on dates before displaying
        $electionSetting = ElectionSetting::getActiveOrCreate();
        $electionSetting->updateStatusBasedOnDate();
        
        $positions = ElectionPosition::where('election_settings_id', $electionSetting->id)->get();
        $roles = Role::all();
            
        // Get all candidates with their user and position information
        // Fixed relationship using 'id' instead of 'user_id'
        $candidates = ElectionCandidate::with(['user' => function($query) {
            $query->select('id', 'user_id', 'name', 'email', 'profile_photo_path');
        }, 'position'])->get();
        
        // Process candidates to ensure they have user information
        $candidates->each(function($candidate) {
            $candidate->user_details = [];
            
            // Try to get user data through the relationship
            if ($candidate->user) {
                $candidate->user_details = [
                    'name' => $candidate->user->name,
                    'email' => $candidate->user->email,
                    'photo' => $candidate->user->profile_photo_path
                ];
            } 
            // If relationship fails, try direct database query
            else {
                try {
                    $user = DB::table('users')->where('id', $candidate->user_id)->first();
                    if ($user) {
                        $candidate->user_details = [
                            'name' => $user->name,
                            'email' => $user->email,
                            'photo' => $user->profile_photo_path
                        ];
                    }
                } catch (\Exception $e) {
                    // Log the error
                    \Log::error('Failed to get user details for candidate: ' . $e->getMessage());
                }
            }
        });
        
        // For debugging - log candidate data
        \Log::info('Candidates loaded for admin election page:', [
            'count' => $candidates->count(),
            'candidates' => $candidates->map(function($c) {
                return [
                    'id' => $c->id,
                    'user_id' => $c->user_id,
                    'candidate_name' => $c->candidate_name ?? 'Unknown',
                    'user' => $c->user ? [
                        'id' => $c->user->id,
                        'user_id' => $c->user->user_id,
                        'name' => $c->user->name,
                        'email' => $c->user->email
                    ] : null,
                    'user_details' => $c->user_details,
                    'position' => $c->position ? $c->position->title : null
                ];
            })
        ]);
            
        return view('admin.election.index', compact('electionSetting', 'positions', 'roles', 'candidates'));
    }

    /**
     * Update the election settings.
     */
    public function updateSettings(Request $request)
    {
        \Log::info('Election settings update request received:', [
            'all_input' => $request->all(),
            'has_is_enabled' => $request->has('is_enabled'),
            'is_enabled_value' => $request->input('is_enabled')
        ]);

        $request->validate([
            'is_enabled' => 'boolean',
            'candidacy_start_date' => 'nullable|date',
            'candidacy_end_date' => 'nullable|date|after_or_equal:candidacy_start_date',
            'voting_start_date' => 'nullable|date|after_or_equal:candidacy_end_date',
            'voting_end_date' => 'nullable|date|after_or_equal:voting_start_date',
        ]);

        $electionSetting = ElectionSetting::getActiveOrCreate();
        
        // Remove status from the data to be updated
        $data = $request->except('status');
        
        // Manually handle the is_enabled checkbox
        if (!$request->has('is_enabled')) {
            $data['is_enabled'] = false;
        }
        
        \Log::info('Election settings update data:', [
            'data' => $data,
        ]);
        
        $electionSetting->update($data);
        
        // Check if the dates would result in a different status
        $dateBasedStatus = $this->getStatusBasedOnDates($electionSetting);
        $currentStatus = $electionSetting->status;
        
        if ($dateBasedStatus && $dateBasedStatus !== $currentStatus && !$electionSetting->ignore_automatic_updates) {
            return redirect()->route('admin.election.index')
                ->with('warning', 'The date settings you configured suggest the status should be "' . ucfirst($dateBasedStatus) . '". The status will update automatically unless you use the Manual Status Control section to lock it.');
        }

        return redirect()->route('admin.election.index')
                ->with('success', 'Election settings updated successfully.');
    }
    
    /**
     * Directly change the election status.
     */
    public function changeStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:inactive,candidacy,voting,completed',
            'ignore_dates' => 'boolean',
        ]);
        
        $electionSetting = ElectionSetting::getActiveOrCreate();
        $previousStatus = $electionSetting->status;
        $electionSetting->status = $request->status;
        
        // Set ignore_automatic_updates flag if requested
        if ($request->has('ignore_dates') && $request->ignore_dates) {
            $electionSetting->ignore_automatic_updates = true;
        } else {
            $electionSetting->ignore_automatic_updates = false;
        }
        
        $electionSetting->save();
        
        // The broadcast event will be triggered by the model's booted method
        
        // If status changed to completed, archive the election and assign roles
        if ($request->status === 'completed' && $previousStatus !== 'completed') {
            DB::transaction(function () use ($electionSetting) {
                $archive = ElectionArchive::createFromCurrentElection($electionSetting);
                $archive->assignRolesToWinners();
            });
            
            // Send notifications to winners
            $electionService = new ElectionService();
            $notificationResults = $electionService->sendWinnerNotifications($electionSetting);
            
            $successMessage = 'Election completed! Results have been archived and roles assigned to winners.';
            
            if ($notificationResults['success'] > 0) {
                $successMessage .= ' ' . $notificationResults['success'] . ' winner notification(s) sent.';
            }
            
            if ($notificationResults['failed'] > 0) {
                $successMessage .= ' ' . $notificationResults['failed'] . ' notification(s) failed to send.';
            }
            
            return redirect()->route('admin.election.index')
                ->with('success', $successMessage);
        }
        
        return redirect()->route('admin.election.index')
            ->with('success', 'Election status changed to ' . ucfirst($request->status) . '.');
    }
    
    /**
     * Helper method to determine status based on current date and configured date ranges
     */
    private function getStatusBasedOnDates(ElectionSetting $electionSetting)
    {
        $now = now();
        
        if ($electionSetting->candidacy_start_date && 
            $electionSetting->candidacy_end_date && 
            $now->between($electionSetting->candidacy_start_date, $electionSetting->candidacy_end_date)) {
            return 'candidacy';
        }
        
        if ($electionSetting->voting_start_date && 
            $electionSetting->voting_end_date && 
            $now->between($electionSetting->voting_start_date, $electionSetting->voting_end_date)) {
            return 'voting';
        }
        
        if ($electionSetting->voting_end_date && $now->greaterThan($electionSetting->voting_end_date)) {
            return 'completed';
        }
        
        if ($electionSetting->candidacy_start_date && $now->lessThan($electionSetting->candidacy_start_date)) {
            return 'inactive';
        }
        
        return null;
    }

    /**
     * Store a new position.
     */
    public function storePosition(Request $request)
    {
        // Enhanced debugging
        $debugData = [
            'path' => $request->path(),
            'method' => $request->method(),
            'all_input' => $request->all(),
            'headers' => $request->headers->all(),
            'is_ajax' => $request->ajax(),
            'force_submit' => $request->has('force_submit'),
            'url' => $request->url(),
            'full_url' => $request->fullUrl(),
        ];
        
        \Log::info('Position creation request received:', $debugData);
        
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'eligible_roles' => 'required|array',
                'eligible_roles.*' => 'exists:roles,name',
                'max_votes_per_voter' => 'required|integer|min:1',
            ]);

            \Log::info('Validation passed:', $validatedData);

            $electionSetting = ElectionSetting::getActiveOrCreate();
            
            $position = ElectionPosition::create([
                'title' => $request->title,
                'description' => $request->description,
                'eligible_roles' => $request->eligible_roles,
                'max_votes_per_voter' => $request->max_votes_per_voter,
                'election_settings_id' => $electionSetting->id,
            ]);

            \Log::info('Position created:', $position->toArray());

            // Redirect based on the request source
            if ($request->has('force_submit') || $request->ajax()) {
                return redirect()->route('admin.election.index')
                    ->with('success', 'Position created successfully (from modal).');
            }

            return redirect()->route('admin.election.index')
                    ->with('success', 'Position created successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating position: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withErrors(['error' => 'Error creating position: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Test route for debugging
     */
    public function testAddPosition()
    {
        $electionSetting = ElectionSetting::getActiveOrCreate();
        $roles = Role::all();
        
        return view('admin.election.test-add-position', compact('electionSetting', 'roles'));
    }

    /**
     * Update a position.
     */
    public function updatePosition(Request $request, ElectionPosition $position)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'eligible_roles' => 'required|array',
            'eligible_roles.*' => 'exists:roles,name',
            'max_votes_per_voter' => 'required|integer|min:1',
        ]);

        $position->update($request->all());

        return redirect()->route('admin.election.index')->with('success', 'Position updated successfully.');
    }

    /**
     * Delete a position.
     */
    public function deletePosition(ElectionPosition $position)
    {
        $position->delete();

        return redirect()->route('admin.election.index')->with('success', 'Position deleted successfully.');
    }

    /**
     * View election results.
     */
    public function results()
    {
        $electionSetting = ElectionSetting::getActiveOrCreate();
        $positions = ElectionPosition::with(['candidates.user', 'candidates.votes'])
            ->where('election_settings_id', $electionSetting->id)
            ->get();

        return view('admin.election.results', compact('electionSetting', 'positions'));
    }

    /**
     * View archived elections.
     */
    public function archives()
    {
        $archives = ElectionArchive::orderBy('created_at', 'desc')->get();

        return view('admin.election.archives', compact('archives'));
    }

    /**
     * View specific archive details.
     */
    public function viewArchive(ElectionArchive $archive)
    {
        return view('admin.election.archive-details', compact('archive'));
    }

    /**
     * Send winner notifications manually
     */
    public function sendWinnerNotifications(ElectionSetting $election)
    {
        if (!$election->isCompleted()) {
            return redirect()->route('admin.election.index')
                ->with('error', 'Cannot send winner notifications because election is not completed.');
        }
        
        $electionService = new ElectionService();
        $results = $electionService->sendWinnerNotifications($election);
        
        $message = 'Winner notification process completed.';
        
        if ($results['success'] > 0) {
            $message .= ' ' . $results['success'] . ' notification(s) sent successfully.';
        }
        
        if ($results['failed'] > 0) {
            $message .= ' ' . $results['failed'] . ' notification(s) failed to send.';
        }
        
        if (!empty($results['errors'])) {
            $message .= ' Check the logs for more details.';
        }
        
        return redirect()->route('admin.election.index')
            ->with('success', $message);
    }

    /**
     * View candidate details
     */
    public function viewCandidate($id)
    {
        // Find the candidate by ID with proper relationship
        $candidate = ElectionCandidate::with(['position', 'votes'])->findOrFail($id);
        
        // Load the user relationship correctly
        try {
            $candidate->load(['user' => function($query) {
                $query->select('id', 'user_id', 'name', 'email', 'profile_photo_path');
            }]);
            
            // If relationship loading fails, try direct database query
            if (!$candidate->user) {
                $user = DB::table('users')->where('id', $candidate->user_id)->first();
                if ($user) {
                    // Create a custom user object to pass to the view
                    $candidate->direct_user = (object)[
                        'id' => $user->id,
                        'user_id' => $user->user_id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'profile_photo_path' => $user->profile_photo_path
                    ];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to load user for candidate ' . $id . ': ' . $e->getMessage());
        }
        
        return view('admin.election.candidate', compact('candidate'));
    }

    /**
     * List all candidate applications
     */
    public function listCandidates()
    {
        try {
            // Get all candidates with their user and position information using correct keys
            $candidates = ElectionCandidate::with(['user' => function($query) {
                $query->select('id', 'user_id', 'name', 'email', 'profile_photo_path');
            }, 'position'])->get();
            
            // Try to directly get users from database using the correct ID column
            $userIds = $candidates->pluck('user_id')->toArray();
            $users = \DB::table('users')->whereIn('id', $userIds)->get();
            
            // Add user info to each candidate
            $candidates->each(function($candidate) use ($users) {
                // User information directly from the relationship
                if ($candidate->user) {
                    $candidate->user_name = $candidate->user->name;
                    $candidate->user_email = $candidate->user->email;
                    $candidate->profile_photo = $candidate->user->profile_photo_path;
                    
                    // Add profile photo URL directly
                    if (method_exists($candidate->user, 'getProfilePhotoUrlAttribute')) {
                        $candidate->profile_photo_url = $candidate->user->profile_photo_url;
                    } else {
                        // Fallback to manual URL generation
                        if ($candidate->user->profile_photo_path) {
                            $candidate->profile_photo_url = asset('storage/' . $candidate->user->profile_photo_path);
                        } else {
                            $candidate->profile_photo_url = asset('kofa.png');
                        }
                    }
                } 
                // Fallback to direct lookup if relationship fails
                else {
                    $directUser = $users->where('id', $candidate->user_id)->first();
                    if ($directUser) {
                        $candidate->user_name = $directUser->name;
                        $candidate->user_email = $directUser->email;
                        $candidate->profile_photo = $directUser->profile_photo_path;
                        
                        // Generate URL for profile photo
                        if ($directUser->profile_photo_path) {
                            $candidate->profile_photo_url = asset('storage/' . $directUser->profile_photo_path);
                        } else {
                            $candidate->profile_photo_url = asset('kofa.png');
                        }
                    } else {
                        $candidate->user_name = 'Unknown User';
                        $candidate->user_email = 'No Email Available';
                        $candidate->profile_photo = null;
                        $candidate->profile_photo_url = asset('kofa.png');
                    }
                }
            });
            
            // Remove any debug information before returning to view
            $candidates->each(function($candidate) {
                if (isset($candidate->debug_info)) {
                    unset($candidate->debug_info);
                }
            });
            
            return view('admin.election.candidates', compact('candidates'));
        } catch (\Exception $e) {
            // Create an empty collection if there's an error
            $candidates = collect([]);
            return view('admin.election.candidates', compact('candidates'))->with('error', 'Unable to load candidates data.');
        }
    }

    /**
     * Approve a candidate application
     */
    public function approveCandidate($id)
    {
        $candidate = ElectionCandidate::findOrFail($id);
        $candidate->status = 'approved';
        $candidate->save();
        
        return redirect()->back()->with('success', 'Candidate application approved successfully.');
    }
    
    /**
     * Reject a candidate application
     */
    public function rejectCandidate(Request $request, $id)
    {
        $candidate = ElectionCandidate::findOrFail($id);
        $candidate->status = 'rejected';
        $candidate->rejection_reason = $request->rejection_reason;
        $candidate->save();
        
        return redirect()->back()->with('success', 'Candidate application rejected successfully.');
    }

    /**
     * Toggle auto-approval setting for candidate applications
     */
    public function toggleAutoApproval()
    {
        try {
            $electionSetting = ElectionSetting::getActiveOrCreate();
            
            // Get value from checkbox (will be 1 if checked, null if unchecked)
            $autoApprove = request()->has('auto_approve_candidates');
            
            // Debug logging
            \Log::info('Auto-approval toggle requested', [
                'has_auto_approve_candidates' => request()->has('auto_approve_candidates'),
                'autoApprove_value' => $autoApprove,
                'request_all' => request()->all(),
                'current_setting' => $electionSetting->auto_approve_candidates ?? 'null'
            ]);
            
            // Set the value based on the checkbox
            $electionSetting->auto_approve_candidates = $autoApprove;
            $electionSetting->save();
            
            // Log after save
            \Log::info('Auto-approval setting saved', [
                'new_value' => $electionSetting->auto_approve_candidates,
                'setting_id' => $electionSetting->id
            ]);
            
            return redirect()->back()->with('success', 'Auto-approval setting has been ' . 
                ($electionSetting->auto_approve_candidates ? 'enabled' : 'disabled'));
        } catch (\Exception $e) {
            \Log::error('Error toggling auto-approval: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'There was an error changing the auto-approval setting. Database migration may be pending.');
        }
    }

    /**
     * Set auto-approval setting directly
     */
    public function setAutoApproval($status)
    {
        try {
            $electionSetting = ElectionSetting::getActiveOrCreate();
            
            // Convert status to boolean (1, true, "true", "on", "yes", "enable" => true)
            $enable = in_array(strtolower($status), ['1', 'true', 'on', 'yes', 'enable']);
            
            // Log the request
            \Log::info('Setting auto-approval directly', [
                'status_param' => $status,
                'interpreted_as' => $enable ? 'true' : 'false'
            ]);
            
            // Direct SQL update as a fallback approach
            try {
                \DB::statement('UPDATE election_settings SET auto_approve_candidates = ? WHERE id = ?', [
                    $enable ? 1 : 0,
                    $electionSetting->id
                ]);
                \Log::info('Direct SQL update completed');
            } catch (\Exception $e) {
                \Log::error('Direct SQL update failed: ' . $e->getMessage());
            }
            
            // Also try the Eloquent way
            $electionSetting->auto_approve_candidates = $enable;
            $electionSetting->save();
            
            // Use a direct URL redirect instead of a named route
            return redirect('/admin/election')
                ->with('success', 'Auto-approval has been ' . ($enable ? 'enabled' : 'disabled'));
        } catch (\Exception $e) {
            \Log::error('Error setting auto-approval: ' . $e->getMessage());
            return redirect('/admin/election')
                ->with('error', 'Failed to update auto-approval setting: ' . $e->getMessage());
        }
    }
} 