<?php

namespace App\Http\Controllers;

use App\Models\ElectionCandidate;
use App\Models\ElectionPosition;
use App\Models\ElectionSetting;
use App\Models\ElectionVote;
use App\Services\ElectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ElectionController extends Controller
{
    /**
     * Display the election page if enabled.
     */
    public function index()
    {
        $electionSetting = ElectionSetting::getActiveOrCreate();
        
        // Check and update status based on dates
        $electionSetting->updateStatusBasedOnDate();
        
        if (!$electionSetting->is_enabled) {
            return view('election.disabled');
        }
        
        $positions = ElectionPosition::where('election_settings_id', $electionSetting->id)->get();
        $user = Auth::user();
        
        // Get user's candidacies
        $userCandidacies = ElectionCandidate::where('user_id', $user->id)->with('position')->get();
        
        // Get all candidates for voting
        $allCandidates = [];
        if ($electionSetting->isVotingPeriod()) {
            $allCandidates = ElectionCandidate::with(['user', 'position'])
                ->get()
                ->filter(function($candidate) {
                    // Filter out candidates with missing user records
                    return $candidate->user !== null;
                })
                ->groupBy('position_id');
        }
        
        // Get user's votes
        $userVotes = ElectionVote::where('user_id', $user->id)->pluck('candidate_id')->toArray();
        
        // Get results if election is completed
        $results = null;
        if ($electionSetting->isCompleted()) {
            $results = ElectionCandidate::getResultsByPosition();
        }
        
        return view('election.index', compact(
            'electionSetting',
            'positions',
            'userCandidacies',
            'allCandidates',
            'userVotes',
            'results'
        ));
    }

    /**
     * Apply for candidacy.
     */
    public function applyForCandidacy(Request $request)
    {
        $request->validate([
            'position_id' => 'required|exists:election_positions,id',
            'platform' => 'required|string',
            'qualifications' => 'required|string',
        ]);
        
        $user = Auth::user();
        $position = ElectionPosition::findOrFail($request->position_id);
        $electionSetting = ElectionSetting::getActiveOrCreate();
        
        // Check if election is in candidacy period
        if (!$electionSetting->isCandidacyPeriod()) {
            return redirect()->route('election.index')->with('error', 'The candidacy period is not active.');
        }
        
        // Check if user is eligible for this position
        if (!$position->isUserEligible($user)) {
            return redirect()->route('election.index')->with('error', 'You are not eligible to apply for this position.');
        }
        
        // Check if user has already applied for this position
        $existingApplication = ElectionCandidate::where('user_id', $user->id)
            ->where('position_id', $position->id)
            ->first();
            
        if ($existingApplication) {
            return redirect()->route('election.index')->with('error', 'You have already applied for this position.');
        }
        
        // Create the candidacy - status set by model boot method
        $candidate = ElectionCandidate::create([
            'user_id' => $user->id,
            'position_id' => $position->id,
            'platform' => $request->platform,
            'qualifications' => $request->qualifications,
        ]);
        
        $approvalMessage = isset($electionSetting->auto_approve_candidates) && $electionSetting->auto_approve_candidates
            ? 'Your candidacy application has been submitted and automatically approved.'
            : 'Your candidacy application has been submitted and is pending approval by an administrator.';
            
        return redirect()->route('election.index')->with('success', $approvalMessage);
    }

    /**
     * Cast a vote.
     */
    public function vote(Request $request)
    {
        $election = ElectionSetting::getActiveOrCreate();
        
        // Group votes by position
        $votesByPosition = collect($request->votes)->groupBy('position_id');
        
        // Validate votes for each position
        foreach ($votesByPosition as $positionId => $votes) {
            $position = ElectionPosition::findOrFail($positionId);
            
            // Check if the number of votes matches the required number
            if (count($votes) !== $position->required_votes) {
                return back()->with('error', "You must select exactly {$position->required_votes} candidate(s) for the position of {$position->title}.");
            }
            
            // Validate each vote
            foreach ($votes as $vote) {
                $candidate = ElectionCandidate::findOrFail($vote['candidate_id']);
                
                // Ensure candidate belongs to this position
                if ($candidate->position_id != $positionId) {
                    return back()->with('error', 'Invalid vote detected.');
                }
                
                // Create the vote
                ElectionVote::create([
                    'user_id' => auth()->id(),
                    'candidate_id' => $candidate->id,
                    'election_settings_id' => $election->id
                ]);
            }
        }
        
        return redirect()->route('election.index')->with('success', 'Your votes have been recorded successfully.');
    }

    /**
     * View candidate details.
     */
    public function viewCandidate(ElectionCandidate $candidate)
    {        
        // Check if the user relationship exists
        if (!$candidate->user) {
            return redirect()->route('election.index')->with('error', 'Candidate information is incomplete. Please contact an administrator.');
        }
        
        return view('election.candidate', compact('candidate'));
    }
} 