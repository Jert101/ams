@extends('layouts.admin-app')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Card enhancements */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .card:hover {
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .card-header {
        background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
        color: white;
        font-weight: 600;
        padding: 15px 20px;
        border: none;
    }
    
    .card-header.position-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    }
    
    .card-body {
        padding: 20px;
    }
    
    /* Table styling */
    .table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    
    .table th {
        background-color: #f8f9fa;
        color: #333;
        font-weight: 600;
        padding: 12px 15px;
        border-bottom: 2px solid #e9ecef;
    }
    
    .table td {
        padding: 12px 15px;
        vertical-align: middle;
        border-top: 1px solid #e9ecef;
    }
    
    .table tr {
        transition: all 0.2s;
    }
    
    .table tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Button styling */
    .btn {
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .btn-primary {
        background-color: #b91c1c;
        border-color: #b91c1c;
    }
    
    .btn-primary:hover {
        background-color: #991b1b;
        border-color: #991b1b;
    }
    
    .btn-secondary {
        background-color: #4b5563;
        border-color: #4b5563;
    }
    
    .btn-secondary:hover {
        background-color: #374151;
        border-color: #374151;
    }
    
    /* Badge styling */
    .badge {
        padding: 6px 10px;
        font-weight: 500;
        border-radius: 6px;
    }
    
    .badge.bg-success {
        background-color: #10b981 !important;
    }
    
    .badge.bg-secondary {
        background-color: #6b7280 !important;
    }
    
    /* Animation for page load */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animated-fade {
        animation: fadeIn 0.4s ease-out forwards;
    }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    
    /* Winner card styling */
    .winner-card {
        border-left: 4px solid #10b981;
        background-color: #f8fafc;
        border-radius: 8px;
        transition: all 0.2s;
    }
    
    .winner-card:hover {
        background-color: #f1f5f9;
    }
    
    .info-item {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        width: 140px;
        color: #4b5563;
    }
    
    .info-value {
        flex: 1;
        color: #1f2937;
    }
    
    /* Progress bar enhancements */
    .progress {
        height: 30px;
        border-radius: 10px;
        background-color: #f3f4f6;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        margin-bottom: 8px;
    }
    
    .progress-bar {
        border-radius: 10px;
        text-shadow: 0 1px 1px rgba(0,0,0,0.1);
        position: relative;
        overflow: hidden;
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            135deg,
            rgba(255, 255, 255, 0.1) 0%,
            rgba(255, 255, 255, 0) 100%
        );
    }
    
    .bg-primary {
        background-color: #2563eb !important;
    }
    
    .bg-success {
        background-color: #10b981 !important;
    }
    
    .bg-info {
        background-color: #0ea5e9 !important;
    }
    
    .bg-warning {
        background-color: #f59e0b !important;
    }
    
    .bg-danger {
        background-color: #ef4444 !important;
    }
    
    /* Breadcrumb styling */
    .breadcrumb {
        padding: 10px 15px;
        background-color: #f9fafb;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .breadcrumb-item a {
        color: #4b5563;
        text-decoration: none;
    }
    
    .breadcrumb-item a:hover {
        color: #b91c1c;
    }
    
    .breadcrumb-item.active {
        color: #1f2937;
        font-weight: 500;
    }
    
    /* Section title */
    .section-title {
        position: relative;
        color: #1f2937;
        font-weight: 600;
        margin-bottom: 20px;
        padding-left: 15px;
    }
    
    .section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: #b91c1c;
        border-radius: 4px;
    }
    
    /* Stats */
    .stats-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .stat-box {
        flex: 1;
        min-width: 200px;
        background-color: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        text-align: center;
        border: 1px solid #e5e7eb;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #b91c1c;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #6b7280;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="container animated-fade">
    <nav aria-label="breadcrumb" class="animated-fade">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.election.index') }}">Election Management</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.election.archives') }}">Archives</a></li>
            <li class="breadcrumb-item active">{{ $archive->title }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 animated-fade">
        <h1 class="mb-0 text-red-700 font-bold text-3xl">{{ $archive->title }}</h1>
        <a href="{{ route('admin.election.archives') }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Back to Archives
        </a>
    </div>

    <div class="card animated-fade delay-100">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Election Overview</h5>
            <span class="badge bg-light text-dark">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $archive->created_at->format('Y') }}
            </span>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="info-item">
                        <div class="info-label">Start Date:</div>
                        <div class="info-value">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-block text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $archive->start_date->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">End Date:</div>
                        <div class="info-value">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-block text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $archive->end_date->format('M d, Y h:i A') }}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <div class="info-label">Duration:</div>
                        <div class="info-value">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-block text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $archive->start_date->diffForHumans($archive->end_date, ['parts' => 2]) }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Archived On:</div>
                        <div class="info-value">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-block text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            {{ $archive->created_at->format('M d, Y h:i A') }}
                            <small class="text-muted">({{ $archive->created_at->diffForHumans() }})</small>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $positions = count($archive->results ?? []);
                $candidates = collect($archive->results ?? [])->sum(function($position) {
                    return count($position['candidates'] ?? []);
                });
                $winners = collect($archive->results ?? [])->sum(function($position) {
                    return collect($position['candidates'] ?? [])->where('is_winner', true)->count();
                });
                $totalVotes = collect($archive->results ?? [])->sum(function($position) {
                    return collect($position['candidates'] ?? [])->sum('votes_count');
                });
            @endphp

            <div class="stats-container">
                <div class="stat-box">
                    <div class="stat-value">{{ $positions }}</div>
                    <div class="stat-label">Positions</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $candidates }}</div>
                    <div class="stat-label">Candidates</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $winners }}</div>
                    <div class="stat-label">Winners</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value">{{ $totalVotes }}</div>
                    <div class="stat-label">Total Votes</div>
                </div>
            </div>
        </div>
    </div>

    @if(empty($archive->results))
        <div class="alert alert-info animated-fade delay-200">
            <p class="mb-0">No results data available for this archive.</p>
        </div>
    @else
        <h3 class="section-title animated-fade delay-200">Election Results</h3>
        
        @foreach($archive->results as $index => $position)
            <div class="card animated-fade delay-{{ 200 + ($index * 50) }}">
                <div class="card-header position-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $position['position_title'] }}</h5>
                    <span class="badge bg-light text-dark">
                        {{ count($position['candidates'] ?? []) }} {{ Str::plural('candidate', count($position['candidates'] ?? [])) }}
                    </span>
                </div>
                <div class="card-body">
                    <p class="mb-4">{{ $position['position_description'] }}</p>
                    
                    @if(empty($position['candidates']))
                        <p class="text-muted">No candidates for this position.</p>
                    @else
                        @php
                            $totalVotes = collect($position['candidates'])->sum('votes_count');
                            $sortedCandidates = collect($position['candidates'])->sortByDesc('votes_count');
                        @endphp

                        <div class="mt-4 mb-4">
                            <h6 class="font-weight-bold mb-3">Vote Distribution</h6>
                            <div class="progress">
                                @foreach($sortedCandidates as $index => $candidate)
                                    @php
                                        $percentage = $totalVotes > 0 ? ($candidate['votes_count'] / $totalVotes) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar bg-{{ $index % 5 == 0 ? 'primary' : ($index % 5 == 1 ? 'success' : ($index % 5 == 2 ? 'info' : ($index % 5 == 3 ? 'warning' : 'danger'))) }}" 
                                        role="progressbar" 
                                        style="width: {{ $percentage }}%" 
                                        aria-valuenow="{{ $percentage }}" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100" 
                                        title="{{ $candidate['user_name'] }}: {{ $candidate['votes_count'] }} votes ({{ number_format($percentage, 1) }}%)">
                                        @if($percentage > 5)
                                            {{ $candidate['user_name'] }} ({{ number_format($percentage, 1) }}%)
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">Total votes: {{ $totalVotes }}</small>
                                <div>
                                    <span class="badge bg-light text-dark mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        {{ count($position['candidates']) }} {{ Str::plural('candidate', count($position['candidates'])) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mb-4">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px">#</th>
                                        <th>Candidate</th>
                                        <th style="width: 120px">Votes</th>
                                        <th style="width: 120px">Percentage</th>
                                        <th style="width: 120px">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sortedCandidates as $index => $candidate)
                                        @php
                                            $percentage = $totalVotes > 0 ? ($candidate['votes_count'] / $totalVotes) * 100 : 0;
                                        @endphp
                                        <tr class="{{ $candidate['is_winner'] ? 'table-success' : '' }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="font-weight-bold">{{ $candidate['user_name'] }}</div>
                                            </td>
                                            <td>{{ $candidate['votes_count'] }}</td>
                                            <td>{{ number_format($percentage, 1) }}%</td>
                                            <td>
                                                @if($candidate['is_winner'])
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

                        <div class="winners-section mt-4">
                            <h6 class="font-weight-bold mb-3">Winner Details</h6>
                            <div class="row">
                                @foreach(collect($position['candidates'])->where('is_winner', true) as $winner)
                                    <div class="col-md-6 mb-3">
                                        <div class="card winner-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div>
                                                        <h5 class="mb-1 font-weight-bold">{{ $winner['user_name'] }}</h5>
                                                        <span class="badge bg-success">Winner</span>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="font-weight-bold text-success">{{ $winner['votes_count'] }} votes</div>
                                                        <small class="text-muted">
                                                            {{ $totalVotes > 0 ? number_format(($winner['votes_count'] / $totalVotes) * 100, 1) : 0 }}% of total
                                                        </small>
                                                    </div>
                                                </div>
                                                
                                                <hr>
                                                
                                                <div class="mt-3">
                                                    <h6 class="font-weight-bold">Platform:</h6>
                                                    <p class="mb-3">{{ $winner['platform'] ?: 'No platform provided' }}</p>
                                                    
                                                    <h6 class="font-weight-bold">Qualifications:</h6>
                                                    <p class="mb-0">{{ $winner['qualifications'] ?: 'No qualifications provided' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @endif
    
    <div class="d-flex justify-content-between align-items-center mt-4 mb-5 animated-fade delay-300">
        <a href="{{ route('admin.election.archives') }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Back to Archives
        </a>
        <a href="{{ route('admin.election.index') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Manage Active Election
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript animations or interactions here
    });
</script>
@endsection 