@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Election Results</h1>
        <a href="{{ route('admin.election.index') }}" class="btn btn-secondary">Back to Election Management</a>
    </div>

    @if($electionSetting->status !== 'completed')
        <div class="alert alert-warning">
            <h4 class="alert-heading">Election Not Completed</h4>
            <p>The election has not been completed yet. The results shown here are preliminary and may change.</p>
        </div>
    @endif

    @if($positions->isEmpty())
        <div class="alert alert-info">
            <p class="mb-0">No positions have been defined for this election.</p>
        </div>
    @else
        @foreach($positions as $position)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $position->title }}</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">{{ $position->description }}</p>
                    
                    @if($position->candidates->isEmpty())
                        <p class="text-muted">No candidates for this position.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Candidate</th>
                                        <th>Votes</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($position->candidates->sortByDesc(function($candidate) { return $candidate->votes->count(); }) as $candidate)
                                        @php
                                            $isWinner = in_array($candidate->id, $position->getWinners()->pluck('id')->toArray());
                                        @endphp
                                        <tr class="{{ $isWinner ? 'table-success' : '' }}">
                                            <td>{{ $candidate->user->name }}</td>
                                            <td>{{ $candidate->votes->count() }}</td>
                                            <td>
                                                @if($isWinner)
                                                    <span class="badge bg-success">Winner</span>
                                                @else
                                                    <span class="badge bg-secondary">Candidate</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <h6>Vote Distribution</h6>
                            <div class="progress" style="height: 30px;">
                                @foreach($position->candidates as $candidate)
                                    @php
                                        $totalVotes = $position->votes->count();
                                        $candidateVotes = $candidate->votes->count();
                                        $percentage = $totalVotes > 0 ? ($candidateVotes / $totalVotes) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar bg-{{ $loop->index % 5 == 0 ? 'primary' : ($loop->index % 5 == 1 ? 'success' : ($loop->index % 5 == 2 ? 'info' : ($loop->index % 5 == 3 ? 'warning' : 'danger'))) }}" 
                                        role="progressbar" 
                                        style="width: {{ $percentage }}%" 
                                        aria-valuenow="{{ $percentage }}" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100" 
                                        title="{{ $candidate->user->name }}: {{ $candidateVotes }} votes ({{ number_format($percentage, 1) }}%)">
                                        @if($percentage > 5)
                                            {{ $candidate->user->name }} ({{ number_format($percentage, 1) }}%)
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">Total votes: {{ $position->votes->count() }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif

    @if($electionSetting->status === 'completed')
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Role Assignment</h5>
            </div>
            <div class="card-body">
                <p>The following roles have been assigned based on the election results:</p>
                <ul class="list-group">
                    @foreach($positions as $position)
                        @foreach($position->getWinners() as $winner)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $position->title }}:</strong> {{ $winner->user->name }}
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $winner->votes->count() }} votes</span>
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>
@endsection 