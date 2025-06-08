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
        $candidates = ElectionCandidate::with(['user', 'position'])->get();
            
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
        // Find the candidate by ID
        $candidate = ElectionCandidate::with(['user', 'position', 'votes'])->findOrFail($id);
        
        return view('admin.election.candidate', compact('candidate'));
    }

    // Candidate approval is now automatic via the ElectionCandidate model's booted method
} 