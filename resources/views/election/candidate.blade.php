@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Candidate Profile</h1>
        <a href="{{ route('election.index') }}" class="btn btn-secondary">Back to Elections</a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $candidate->user ? $candidate->user->name : 'Unknown Candidate' }} - {{ $candidate->position->title }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center mb-4 mb-md-0">
                    @php
                        // Try to get user information directly from database if relationship fails
                        $userName = $candidate->user ? $candidate->user->name : 'Unknown Candidate';
                        $profilePhotoUrl = null;
                        
                        if ($candidate->user && $candidate->user->profile_photo_url) {
                            $profilePhotoUrl = $candidate->user->profile_photo_url;
                        } else {
                            try {
                                $user = DB::table('users')->where('id', $candidate->user_id)->first();
                                if ($user) {
                                    $userName = $user->name;
                                    if ($user->profile_photo_path) {
                                        $profilePhotoUrl = asset('storage/' . $user->profile_photo_path);
                                    }
                                }
                            } catch (\Exception $e) {
                                // Silently ignore errors
                            }
                        }
                        
                        // Default photo if none found
                        if (!$profilePhotoUrl) {
                            $profilePhotoUrl = asset('img/kofa.png');
                        }
                    @endphp
                    
                    <img src="{{ $candidate->user ? $candidate->user->profile_photo_url : asset('img/kofa.png') }}" 
                         alt="{{ $userName }}" 
                         class="img-fluid rounded-circle mb-3" style="max-width: 200px;">
                    <h4>{{ $userName }}</h4>
                    <p class="text-muted">Running for {{ $candidate->position->title }}</p>
                    
                    @if($candidate->isApproved() && $candidate->position->electionSetting->isVotingPeriod())
                        <div class="mt-3">
                            @if(in_array($candidate->id, \App\Models\ElectionVote::where('user_id', Auth::user()->user_id)->pluck('candidate_id')->toArray()))
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> You voted for this candidate
                                </div>
                            @else
                                @if($candidate->position->canUserVoteMore(Auth::user()))
                                    <form action="{{ route('election.vote') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
                                        <button type="submit" class="btn btn-primary">Vote for this Candidate</button>
                                    </form>
                                @else
                                    <div class="alert alert-warning">
                                        You have used all your votes for this position
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    <div class="mb-4">
                        <h5>Platform</h5>
                        <div class="card">
                            <div class="card-body">
                                {{ $candidate->platform }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Qualifications</h5>
                        <div class="card">
                            <div class="card-body">
                                {{ $candidate->qualifications }}
                            </div>
                        </div>
                    </div>
                    
                    @if($candidate->position->electionSetting->isCompleted())
                        <div class="mb-4">
                            <h5>Election Result</h5>
                            <div class="card">
                                <div class="card-body">
                                    @php
                                        $votes = $candidate->votes->count();
                                        $totalVotes = App\Models\ElectionVote::where('position_id', $candidate->position_id)->count();
                                        $percentage = $totalVotes > 0 ? ($votes / $totalVotes) * 100 : 0;
                                        $isWinner = in_array($candidate->id, $candidate->position->getWinners()->pluck('id')->toArray());
                                    @endphp
                                    
                                    <p>Total votes received: <strong>{{ $votes }}</strong> ({{ number_format($percentage, 1) }}% of total votes)</p>
                                    
                                    <div class="progress mb-3" style="height: 25px;">
                                        <div class="progress-bar bg-{{ $isWinner ? 'success' : 'primary' }}" 
                                            role="progressbar" 
                                            style="width: {{ $percentage }}%" 
                                            aria-valuenow="{{ $percentage }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            {{ number_format($percentage, 1) }}%
                                        </div>
                                    </div>
                                    
                                    @if($isWinner)
                                        <div class="alert alert-success">
                                            <i class="fas fa-trophy"></i> This candidate was elected as {{ $candidate->position->title }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 