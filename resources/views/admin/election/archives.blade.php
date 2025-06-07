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
    
    .btn-info {
        background-color: #0ea5e9;
        border-color: #0ea5e9;
        color: white;
    }
    
    .btn-info:hover {
        background-color: #0284c7;
        border-color: #0284c7;
        color: white;
    }
    
    .btn-sm {
        padding: 5px 10px;
        font-size: 0.85rem;
    }
    
    /* Badge styling */
    .badge {
        padding: 6px 10px;
        font-weight: 500;
        border-radius: 6px;
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
    
    /* Archive card styling */
    .archive-card {
        position: relative;
        overflow: hidden;
        height: 100%;
        transition: all 0.3s ease;
        border: 1px solid #f3f4f6;
    }
    
    .archive-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, #b91c1c 0%, #991b1b 100%);
        z-index: 1;
    }
    
    .archive-card .card-header {
        position: relative;
        z-index: 2;
        background: white;
        color: #1f2937;
        border-bottom: 1px solid #f3f4f6;
        padding: 16px 20px;
    }
    
    .archive-card .card-header .badge {
        background-color: #f3f4f6;
        color: #4b5563;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 5px 8px;
        border-radius: 6px;
    }
    
    .archive-title {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
        font-weight: 600;
        font-size: 1rem;
        color: #1f2937;
    }
    
    .archive-stats {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    
    .stat-item {
        background-color: #f9fafb;
        border-radius: 8px;
        padding: 10px 8px;
        flex: 1;
        text-align: center;
        border: 1px solid #f3f4f6;
    }
    
    .stat-value {
        font-size: 1.125rem;
        font-weight: 600;
        color: #b91c1c;
        line-height: 1.2;
    }
    
    .stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 2px;
    }
    
    hr {
        margin: 15px 0;
        border-color: #f3f4f6;
        opacity: 0.6;
    }

    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 50px 30px;
        background-color: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px rgba(0,0,0,0.03);
        max-width: 550px;
        margin: 30px auto;
    }
    
    .empty-state .icon-container {
        background-color: #f9fafb;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px auto;
        border: 1px solid #f3f4f6;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .empty-state svg {
        width: 40px;
        height: 40px;
        color: #b91c1c;
    }
    
    .empty-state h3 {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 12px;
        font-size: 1.5rem;
    }
    
    .empty-state p {
        color: #6b7280;
        max-width: 400px;
        margin: 0 auto 24px auto;
        line-height: 1.6;
    }
    
    /* Page header styling */
    .page-header {
        margin-bottom: 30px;
    }
    
    .page-title {
        color: #b91c1c;
        font-weight: 700;
        font-size: 1.75rem;
    }
    
    @media (max-width: 767px) {
        .archive-title {
            max-width: 120px;
        }
        
        .stat-value {
            font-size: 1rem;
        }
        
        .stat-label {
            font-size: 0.7rem;
        }
    }
</style>
@endsection

@section('content')
<div class="container animated-fade">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <h1 class="mb-0 page-title">Election Archives</h1>
        <a href="{{ route('admin.election.index') }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 me-1 inline-block" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Back to Management
        </a>
    </div>

    @if($archives->isEmpty())
        <div class="empty-state animated-fade delay-100">
            <div class="icon-container">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
            </div>
            <h3>No Archives Found</h3>
            <p>There are no archived elections available. Once an election is completed, it will appear here.</p>
            <a href="{{ route('admin.election.index') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Manage Elections
            </a>
        </div>
    @else
        <div class="row g-4">
            @foreach($archives as $archive)
                <div class="col-md-6 col-lg-4 mb-0 animated-fade delay-{{ $loop->index % 3 }}00">
                    <div class="card archive-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 archive-title" title="{{ $archive->title }}">{{ $archive->title }}</h5>
                            <span class="badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $archive->created_at->format('Y') }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <small class="text-muted d-block mb-1">Start Date</small>
                                    <div class="font-semibold">{{ $archive->start_date->format('M d, Y') }}</div>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block mb-1">End Date</small>
                                    <div class="font-semibold">{{ $archive->end_date->format('M d, Y') }}</div>
                                </div>
                            </div>
                            
                            <div class="archive-stats">
                                <div class="stat-item">
                                    <div class="stat-value">{{ count($archive->results ?? []) }}</div>
                                    <div class="stat-label">Positions</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">
                                        {{ collect($archive->results ?? [])->sum(function($position) {
                                            return count($position['candidates'] ?? []);
                                        }) }}
                                    </div>
                                    <div class="stat-label">Candidates</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value">
                                        {{ collect($archive->results ?? [])->sum(function($position) {
                                            return collect($position['candidates'] ?? [])->sum('votes_count');
                                        }) }}
                                    </div>
                                    <div class="stat-label">Votes</div>
                                </div>
                            </div>
                            
                            <hr class="my-3">
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">{{ $archive->created_at->diffForHumans() }}</small>
                                <a href="{{ route('admin.election.archive', $archive->id) }}" class="btn btn-sm btn-info">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-4 mb-5 animated-fade delay-300">
            <div class="text-muted">
                Showing {{ $archives->count() }} archived election{{ $archives->count() != 1 ? 's' : '' }}
            </div>
            <a href="{{ route('admin.election.index') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class="me-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Manage Active Election
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add any JavaScript animations or interactions here
    });
</script>
@endsection 