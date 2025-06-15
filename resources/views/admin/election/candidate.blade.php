@extends('layouts.admin-app')

@section('content')
<div class="container py-4">
    @php
        // Get user information from either the relationship or direct query
        $userName = 'Unknown User';
        $userEmail = '';
        $profilePhotoUrl = null;
        
        if ($candidate->user) {
            $userName = $candidate->user->name;
            $userEmail = $candidate->user->email;
            $profilePhotoUrl = $candidate->user->profile_photo_url;
        } elseif (isset($candidate->direct_user)) {
            $userName = $candidate->direct_user->name;
            $userEmail = $candidate->direct_user->email;
            
            // For direct user, we need to build the URL manually
            if ($candidate->direct_user->profile_photo_path) {
                if (filter_var($candidate->direct_user->profile_photo_path, FILTER_VALIDATE_URL)) {
                    $profilePhotoUrl = $candidate->direct_user->profile_photo_path;
                } else {
                    $profilePhotoUrl = asset('storage/' . $candidate->direct_user->profile_photo_path);
                }
            }
        }
        
        // Fallback to default image if no profile photo was found
        if (!$profilePhotoUrl) {
            $profilePhotoUrl = asset('img/defaults/user.svg');
            
            // Check if kofa.png exists as a fallback
            if (file_exists(public_path('kofa.png'))) {
                $profilePhotoUrl = asset('kofa.png');
            }
        }
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800">Candidate Details: {{ $userName }}</h1>
        <a href="{{ route('admin.election.index') }}" class="btn btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Election Management
        </a>
    </div>

    <div class="card shadow-lg border-0 mb-5">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 font-semibold">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Candidate Information
                </h5>
                <span class="badge rounded-pill 
                    {{ $candidate->status === 'approved' ? 'bg-success' : 
                       ($candidate->status === 'pending' ? 'bg-warning' : 'bg-danger') }} px-3 py-2">
                    {{ ucfirst($candidate->status) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="text-center">
                        <div class="mx-auto mb-3 w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            <img src="{{ $candidate->user ? $candidate->user->profile_photo_url : asset('img/kofa.png') }}" 
                                 alt="{{ $userName }}" 
                                 class="w-full h-full object-cover">
                        </div>
                        <h3 class="text-xl font-semibold mt-2">{{ $userName }}</h3>
                        @if($userEmail)
                            <p class="text-gray-600">{{ $userEmail }}</p>
                        @endif
                        
                        <div class="mt-4 py-3 px-4 bg-blue-50 rounded-lg">
                            <p class="text-sm text-gray-700">
                                <strong>Application Date:</strong> {{ $candidate->created_at->format('F j, Y - g:i A') }}
                            </p>
                            <p class="text-sm text-gray-700 mt-1">
                                <strong>Position:</strong> {{ $candidate->position ? $candidate->position->title : 'Unknown Position' }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="mb-4">
                        <h4 class="text-lg font-semibold mb-2 bg-gray-100 p-2 rounded">Platform</h4>
                        <div class="p-3 bg-white border rounded-lg">
                            @if(is_array($candidate->platform) && count($candidate->platform) > 0)
                                <ul class="list-disc list-inside space-y-2">
                                    @foreach($candidate->platform as $point)
                                        <li class="text-gray-700">{{ $point }}</li>
                                    @endforeach
                                </ul>
                            @elseif(is_string($candidate->platform) && !empty(trim($candidate->platform)))
                                <p class="text-gray-700">{{ $candidate->platform }}</p>
                            @else
                                <p class="text-gray-500 italic">No platform provided</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h4 class="text-lg font-semibold mb-2 bg-gray-100 p-2 rounded">Qualifications</h4>
                        <div class="p-3 bg-white border rounded-lg">
                            @if(is_array($candidate->qualifications) && count($candidate->qualifications) > 0)
                                <ul class="list-disc list-inside space-y-2">
                                    @foreach($candidate->qualifications as $qualification)
                                        <li class="text-gray-700">{{ $qualification }}</li>
                                    @endforeach
                                </ul>
                            @elseif(is_string($candidate->qualifications) && !empty(trim($candidate->qualifications)))
                                <p class="text-gray-700">{{ $candidate->qualifications }}</p>
                            @else
                                <p class="text-gray-500 italic">No qualifications provided</p>
                            @endif
                        </div>
                    </div>
                    
                    @if($candidate->rejection_reason)
                        <div class="mb-4">
                            <h4 class="text-lg font-semibold mb-2 bg-red-100 text-red-800 p-2 rounded">Rejection Reason</h4>
                            <div class="p-3 bg-white border border-red-200 rounded-lg">
                                {{ $candidate->rejection_reason }}
                            </div>
                        </div>
                    @endif
                    
                    @if($candidate->status === 'approved')
                        <div class="alert alert-success mt-4">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p>This candidate has been approved and will appear on the ballot during the voting period.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-lg border-0 mb-5">
        <div class="card-header bg-gray-200 py-3">
            <h5 class="card-title mb-0 font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Position Details
            </h5>
        </div>
        <div class="card-body">
            @if($candidate->position)
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="text-lg font-semibold mb-2">{{ $candidate->position->title }}</h4>
                        <p class="text-gray-700 mb-3">{{ $candidate->position->description }}</p>
                        
                        <div class="bg-gray-50 p-3 rounded-lg mt-3">
                            <h5 class="font-medium mb-2">Eligible Roles:</h5>
                            <div>
                                @foreach($candidate->position->eligible_roles as $role)
                                    <span class="badge bg-blue-100 text-blue-800 mr-1 mb-1 px-2 py-1">{{ $role }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <h5 class="font-medium mb-2">Voting Details:</h5>
                            <p><strong>Maximum votes per voter:</strong> {{ $candidate->position->max_votes_per_voter }}</p>
                            
                            @php
                                $totalCandidates = \App\Models\ElectionCandidate::where('position_id', $candidate->position_id)->count();
                                $approvedCandidates = \App\Models\ElectionCandidate::where('position_id', $candidate->position_id)
                                    ->where('status', 'approved')
                                    ->count();
                            @endphp
                            
                            <p class="mt-2"><strong>Total candidates for this position:</strong> {{ $totalCandidates }}</p>
                            <p><strong>Approved candidates:</strong> {{ $approvedCandidates }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <p>Position information is not available or has been deleted.</p>
                </div>
            @endif
        </div>
    </div>
    
    @if($candidate->votes && $candidate->votes->count() > 0)
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gray-200 py-3">
            <h5 class="card-title mb-0 font-semibold">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Voting Information
            </h5>
        </div>
        <div class="card-body">
            <p class="mb-3"><strong>Total votes received:</strong> {{ $candidate->votes->count() }}</p>
            
            @php
                $electionSetting = \App\Models\ElectionSetting::getActiveOrCreate();
                $totalPossibleVoters = \App\Models\User::whereHas('role', function($query) use ($candidate) {
                    $query->whereIn('name', $candidate->position->eligible_roles ?? []);
                })->count();
                
                $votePercentage = $totalPossibleVoters > 0 ? 
                    round(($candidate->votes->count() / $totalPossibleVoters) * 100, 1) : 0;
            @endphp
            
            <div class="mb-3">
                <div class="flex justify-between mb-1">
                    <span>Vote percentage:</span>
                    <span class="text-right">{{ $votePercentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $votePercentage }}%"></div>
                </div>
            </div>
            
            @if($electionSetting->isCompleted())
                @php
                    $winnersByPosition = \App\Models\ElectionCandidate::getResultsByPosition();
                    $isWinner = false;
                    
                    if(isset($winnersByPosition[$candidate->position_id])) {
                        $positionWinners = $winnersByPosition[$candidate->position_id];
                        $maxVotes = $positionWinners->max('votes_count');
                        $isWinner = $candidate->votes->count() == $maxVotes && $maxVotes > 0;
                    }
                @endphp
                
                @if($isWinner)
                    <div class="alert alert-success mt-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-green-800 font-medium">Winner!</h4>
                                <p class="text-green-700">
                                    This candidate won the election for the position of {{ $candidate->position->title }}.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
    @endif
</div>
@endsection 