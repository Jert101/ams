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
        $request->validate([
            'candidate_id' => 'required|exists:election_candidates,id',
        ]);
        
        $user = Auth::user();
        $candidate = ElectionCandidate::findOrFail($request->candidate_id);
        $position = $candidate->position;
        $electionSetting = ElectionSetting::getActiveOrCreate();
        
        // Check if election is in voting period
        if (!$electionSetting->isVotingPeriod()) {
            return redirect()->route('election.index')->with('error', 'The voting period is not active.');
        }
        
        // Check if user can vote more for this position
        if (!$position->canUserVoteMore($user)) {
            return redirect()->route('election.index')->with('error', 'You have already cast the maximum number of votes for this position.');
        }
        
        // Check if user has already voted for this candidate
        $existingVote = ElectionVote::where('user_id', $user->id)
            ->where('candidate_id', $candidate->id)
            ->first();
            
        if ($existingVote) {
            return redirect()->route('election.index')->with('error', 'You have already voted for this candidate.');
        }
        
        // Create the vote
        ElectionVote::create([
            'user_id' => $user->id,
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
        ]);
        
        return redirect()->route('election.index')->with('success', 'Your vote has been recorded.');
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