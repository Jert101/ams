@extends('layouts.admin-app')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Responsive fixes for mobile screens */
    @media (max-width: 768px) {
        .container {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
        
        h1 {
            font-size: 1.5rem !important;
            margin-bottom: 1rem !important;
        }
        
        .card-header {
            padding: 0.75rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .election-phase-indicator {
            margin-top: 0.5rem;
        }
        
        .timeline-dot {
            width: 12px;
            height: 12px;
            margin-right: 4px;
        }
        
        .input-group {
            flex-direction: column;
        }
        
        .input-group > .form-control {
            width: 100%;
            border-radius: 0.375rem !important;
            margin-bottom: 0.5rem;
        }
        
        .input-group > .input-group-text {
            width: 100%;
            border-radius: 0.375rem !important;
            justify-content: center;
        }
        
        .badge {
            font-size: 0.7rem;
            padding: 0.3rem 0.5rem;
        }
        
        .d-flex.justify-content-between {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .election-progress {
            height: 6px;
            margin: 0.75rem 0;
        }
        
        .form-check-input {
            width: 2.5em !important;
            height: 1.25em !important;
        }
    }
    
    @media (max-width: 480px) {
        .grid.grid-cols-1.md\:grid-cols-2 {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .p-4.bg-gray-50 {
            padding: 0.75rem;
        }
        
        .text-sm {
            font-size: 0.75rem !important;
        }
        
        .badge {
            padding: 0.25rem 0.5rem;
            font-size: 0.65rem;
        }
        
        .election-phase-indicator {
            width: 100%;
            margin-top: 0.5rem;
            text-align: center;
        }
        
        .card-header h5 {
            font-size: 1rem;
        }
        
        .d-flex.justify-content-between.text-sm {
            font-size: 0.7rem !important;
        }
    }
    
    /* Mobile-specific styles for forms */
    @media (max-width: 768px) {
        .custom-modal-content {
            width: 95%;
            margin: 5% auto;
            padding: 15px;
        }
        
        .custom-modal-header {
            padding: 12px 15px;
            margin: -15px -15px 15px -15px;
        }
        
        .custom-modal-title {
            font-size: 1.1rem;
        }
        
        .custom-close {
            font-size: 24px;
        }
        
        .form-control, .form-select {
            font-size: 16px; /* Prevents iOS zoom */
        }
        
        .input-group.flex-nowrap {
            flex-wrap: wrap !important;
        }
        
        .input-group.flex-nowrap > .form-control {
            border-radius: 0.375rem !important;
            margin-bottom: 0.5rem;
        }
        
        .input-group.flex-nowrap > .input-group-text {
            width: 100%;
            justify-content: center;
            border-radius: 0.375rem !important;
        }
        
        .form-text {
            font-size: 0.75rem;
        }
        
        .form-check-label {
            font-size: 0.875rem;
        }
        
        .btn {
            width: 100%;
            margin-bottom: 0.5rem;
            padding: 0.5rem 1rem;
        }
        
        .custom-modal-footer {
            display: flex;
            flex-direction: column;
        }
        
        .me-2 {
            margin-right: 0 !important;
        }
    }
    
    @media (min-width: 769px) {
        .custom-modal-footer {
            display: flex;
            justify-content: flex-end;
        }
        
        .custom-modal-footer .btn {
            width: auto;
        }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto">
    <h1 class="mb-4 text-red-700 font-bold text-3xl">Election Management</h1>

    <!-- Display Validation Errors -->
    @if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
        <div class="font-bold">Whoops! There were some problems with your input:</div>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Custom Modal CSS -->
    <style>
        /* The Modal (background) */
        .custom-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
        }

        /* Modal Content */
        .custom-modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 0;
            width: 90%;
            max-width: 800px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            animation: modalFadeIn 0.3s ease-out;
        }
        
        @keyframes modalFadeIn {
            from {opacity: 0; transform: translateY(-20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        /* The Close Button */
        .custom-close {
            color: #ffffff;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s;
        }

        .custom-close:hover,
        .custom-close:focus {
            color: #f8f9fa;
            text-decoration: none;
        }
        
        .custom-modal-header {
            background-color: #b91c1c;
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .custom-modal-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .custom-modal-footer {
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
            margin-top: 20px;
            text-align: right;
        }
        
        /* Card enhancements */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
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
        
        /* Form styling */
        .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
            transition: all 0.2s;
        }
        
        .form-control:focus {
            border-color: #b91c1c;
            box-shadow: 0 0 0 0.2rem rgba(185, 28, 28, 0.25);
        }
        
        .form-select {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
            height: auto;
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
        
        .btn-warning {
            background-color: #fbbf24;
            border-color: #fbbf24;
        }
        
        .btn-warning:hover {
            background-color: #f59e0b;
            border-color: #f59e0b;
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
        
        .table tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Badge styling */
        .badge {
            padding: 6px 10px;
            font-weight: 500;
            font-size: 0.75rem;
            border-radius: 20px;
        }
        
        /* Status indicators */
        .bg-inactive {
            background-color: #6c757d;
        }
        
        .bg-candidacy {
            background-color: #0dcaf0;
        }
        
        .bg-voting {
            background-color: #0d6efd;
        }
        
        .bg-completed {
            background-color: #198754;
        }
        
        /* Animation for status changes */
        .status-change {
            animation: statusPulse 1s ease-in-out;
        }
        
        @keyframes statusPulse {
            0% {transform: scale(1);}
            50% {transform: scale(1.05);}
            100% {transform: scale(1);}
        }
        
        /* Timeline indicators */
        .timeline-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }
        
        .timeline-dot.active {
            background-color: #198754;
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.2);
        }
        
        .timeline-dot.pending {
            background-color: #ffc107;
            box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.2);
        }
        
        .timeline-dot.inactive {
            background-color: #6c757d;
            box-shadow: 0 0 0 4px rgba(108, 117, 125, 0.2);
        }
        
        /* Progress status bar */
        .election-progress {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
            margin: 1rem 0;
            position: relative;
            overflow: hidden;
        }
        
        .election-progress-bar {
            height: 100%;
            border-radius: 4px;
            background: linear-gradient(90deg, #b91c1c 0%, #facc15 100%);
            transition: width 0.5s ease;
        }
        
        /* Mobile responsive adjustments */
        @media (max-width: 768px) {
            .d-flex.justify-content-between.align-items-center {
                flex-direction: column;
                align-items: flex-start !important;
            }
            
            .election-phase-indicator {
                margin-top: 10px;
            }
            
            .grid-cols-1.md\:grid-cols-2 {
                display: grid;
                grid-template-columns: 1fr;
            }
            
            .input-group {
                display: flex;
                flex-direction: column;
            }
            
            .input-group > * {
                margin-right: 0 !important;
                margin-bottom: 8px;
                width: 100%;
                text-align: center;
            }
            
            .d-flex.justify-content-between {
                flex-wrap: wrap;
            }
            
            .timeline-indicators {
                font-size: 0.7rem;
                justify-content: space-between;
                width: 100%;
            }
        }
    </style>

    <!-- Election Settings -->
    <div class="card shadow-lg mb-5">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-bold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Election Settings
                </h5>
                <div class="election-phase-indicator">
                    <span class="badge bg-{{ $electionSetting->status === 'inactive' ? 'inactive' : ($electionSetting->status === 'candidacy' ? 'candidacy' : ($electionSetting->status === 'voting' ? 'voting' : 'completed')) }} px-3 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $electionSetting->status === 'inactive' ? 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' : ($electionSetting->status === 'candidacy' ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : ($electionSetting->status === 'voting' ? 'M5 13l4 4L19 7' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z')) }}" />
                        </svg>
                        {{ ucfirst($electionSetting->status) }} Phase
                    </span>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Election Progress -->
            <div class="mb-4">
                <div class="election-progress">
                    @php
                        $progressPercentage = 0;
                        if ($electionSetting->status === 'candidacy') {
                            $progressPercentage = 25;
                        } elseif ($electionSetting->status === 'voting') {
                            $progressPercentage = 75;
                        } elseif ($electionSetting->status === 'completed') {
                            $progressPercentage = 100;
                        }
                    @endphp
                    <div class="election-progress-bar" style="width: {{ $progressPercentage }}%"></div>
                </div>
                <div class="d-flex justify-content-between text-sm text-gray-600 mt-1 timeline-indicators">
                    <div>
                        <span class="timeline-dot {{ $electionSetting->status !== 'inactive' ? 'active' : 'inactive' }}"></span>
                        Setup
                    </div>
                    <div>
                        <span class="timeline-dot {{ $electionSetting->status === 'candidacy' || $electionSetting->status === 'voting' || $electionSetting->status === 'completed' ? 'active' : 'pending' }}"></span>
                        Candidacy
                    </div>
                    <div>
                        <span class="timeline-dot {{ $electionSetting->status === 'voting' || $electionSetting->status === 'completed' ? 'active' : 'pending' }}"></span>
                        Voting
                    </div>
                    <div>
                        <span class="timeline-dot {{ $electionSetting->status === 'completed' ? 'active' : 'pending' }}"></span>
                        Results
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.election.update-settings') }}" method="POST" class="mt-4">
                @csrf
                <div class="mb-4 flex flex-wrap items-center gap-2 p-3 bg-gray-50 rounded-lg">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_enabled" name="is_enabled" value="1" {{ $electionSetting->is_enabled ? 'checked' : '' }} style="width: 3em; height: 1.5em;">
                    </div>
                    <div>
                        <label class="form-check-label font-semibold" for="is_enabled">
                            {{ $electionSetting->is_enabled ? 'Election System is Enabled' : 'Election System is Disabled' }}
                        </label>
                        <div class="text-sm text-gray-500">{{ $electionSetting->is_enabled ? 'The election system is currently active and accessible to users.' : 'The election system is currently disabled and not accessible to users.' }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h6 class="font-semibold mb-3 text-gray-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Candidacy Period
                        </h6>
                        <div class="mb-3">
                            <label for="candidacy_start_date" class="form-label text-sm font-medium text-gray-700">Start Date</label>
                            <div class="input-group flex-col sm:flex-row">
                                <input type="datetime-local" class="form-control" id="candidacy_start_date" name="candidacy_start_date" value="{{ $electionSetting->candidacy_start_date ? $electionSetting->candidacy_start_date->format('Y-m-d\TH:i') : '' }}">
                                @if($electionSetting->candidacy_start_date)
                                    <span class="input-group-text countdown-timer text-sm" data-target-date="{{ $electionSetting->candidacy_start_date->toISOString() }}">{{ now()->diffForHumans($electionSetting->candidacy_start_date ?? now(), ['parts' => 1]) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-1">
                            <label for="candidacy_end_date" class="form-label text-sm font-medium text-gray-700">End Date</label>
                            <div class="input-group flex-col sm:flex-row">
                                <input type="datetime-local" class="form-control" id="candidacy_end_date" name="candidacy_end_date" value="{{ $electionSetting->candidacy_end_date ? $electionSetting->candidacy_end_date->format('Y-m-d\TH:i') : '' }}">
                                @if($electionSetting->candidacy_end_date)
                                    <span class="input-group-text countdown-timer text-sm" data-target-date="{{ $electionSetting->candidacy_end_date->toISOString() }}">{{ now()->diffForHumans($electionSetting->candidacy_end_date ?? now(), ['parts' => 1]) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h6 class="font-semibold mb-3 text-gray-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Voting Period
                        </h6>
                        <div class="mb-3">
                            <label for="voting_start_date" class="form-label text-sm font-medium text-gray-700">Start Date</label>
                            <div class="input-group flex-col sm:flex-row">
                                <input type="datetime-local" class="form-control" id="voting_start_date" name="voting_start_date" value="{{ $electionSetting->voting_start_date ? $electionSetting->voting_start_date->format('Y-m-d\TH:i') : '' }}">
                                @if($electionSetting->voting_start_date)
                                    <span class="input-group-text countdown-timer text-sm" data-target-date="{{ $electionSetting->voting_start_date->toISOString() }}">{{ now()->diffForHumans($electionSetting->voting_start_date ?? now(), ['parts' => 1]) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-1">
                            <label for="voting_end_date" class="form-label text-sm font-medium text-gray-700">End Date</label>
                            <div class="input-group flex-col sm:flex-row">
                                <input type="datetime-local" class="form-control" id="voting_end_date" name="voting_end_date" value="{{ $electionSetting->voting_end_date ? $electionSetting->voting_end_date->format('Y-m-d\TH:i') : '' }}">
                                @if($electionSetting->voting_end_date)
                                    <span class="input-group-text countdown-timer text-sm" data-target-date="{{ $electionSetting->voting_end_date->toISOString() }}">{{ now()->diffForHumans($electionSetting->voting_end_date ?? now(), ['parts' => 1]) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Save Settings
                    </button>
                    <div>
                        <a href="{{ route('admin.election.results') }}" class="btn btn-info me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            View Results
                        </a>
                        <a href="{{ route('admin.election.archives') }}" class="btn btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            View Archives
                        </a>
                    </div>
                </div>
            </form>

            <!-- Direct Status Change Section -->
            <div class="mt-5 bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h5 class="mb-3 font-semibold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Election Status Control
                </h5>
                
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="p-4 mb-4 rounded-lg bg-blue-50 border-l-4 border-blue-500 text-blue-700">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="font-medium">Manual Status Override</p>
                                    <p class="text-sm">This section allows you to manually control the <strong>current status</strong> of the election, which determines what features are available to users.</p>
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('admin.election.change-status') }}" method="POST" class="row g-3 align-items-end">
                            @csrf
                            <div class="col-md-5">
                                <label for="direct_status" class="form-label font-semibold text-gray-700">Set Election Status</label>
                                <select class="form-select form-select-lg shadow-sm" id="direct_status" name="status">
                                    <option value="inactive" {{ $electionSetting->status === 'inactive' ? 'selected' : '' }}>
                                        <span class="font-semibold">Inactive</span> (Setup Phase)
                                    </option>
                                    <option value="candidacy" {{ $electionSetting->status === 'candidacy' ? 'selected' : '' }}>
                                        <span class="font-semibold">Candidacy Period</span> (Applications Open)
                                    </option>
                                    <option value="voting" {{ $electionSetting->status === 'voting' ? 'selected' : '' }}>
                                        <span class="font-semibold">Voting Period</span> (Election Active)
                                    </option>
                                    <option value="completed" {{ $electionSetting->status === 'completed' ? 'selected' : '' }}>
                                        <span class="font-semibold">Completed</span> (Results Published)
                                    </option>
                                </select>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="p-3 rounded-lg border {{ $electionSetting->ignore_automatic_updates ? 'bg-yellow-50 border-yellow-200' : 'bg-gray-50 border-gray-200' }}">
                                    <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="ignore_dates" name="ignore_dates" value="1" {{ $electionSetting->ignore_automatic_updates ? 'checked' : '' }}>
                                        <label class="form-check-label font-semibold text-gray-700" for="ignore_dates">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 {{ $electionSetting->ignore_automatic_updates ? 'text-yellow-500' : 'text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            Lock Status
                                    </label>
                                </div>
                                    <div class="form-text text-sm mt-2">
                                        Override automatic updates based on configured dates
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-warning btn-lg w-100 d-flex align-items-center justify-content-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Apply Change
                                </button>
                            </div>
                            
                            <div class="col-12 mt-4">
                                <div class="card border-0 shadow-sm rounded-lg overflow-hidden">
                                    <div class="p-4 border-l-4 {{ $electionSetting->ignore_automatic_updates ? 'border-yellow-500 bg-yellow-50' : 'border-blue-500 bg-blue-50' }}">
                                        <div class="flex items-center">
                                            <div class="mr-4 text-2xl">
                                            @if($electionSetting->ignore_automatic_updates)
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                            @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                            @endif
                                        </div>
                                        <div>
                                                <div class="flex items-center">
                                                    <h6 class="mb-1 font-semibold text-gray-800">Current status: </h6>
                                                    <span class="ml-2 badge fs-6 status-change bg-{{ $electionSetting->status === 'inactive' ? 'inactive' : ($electionSetting->status === 'candidacy' ? 'candidacy' : ($electionSetting->status === 'voting' ? 'voting' : 'completed')) }}">
                                                        {{ ucfirst($electionSetting->status) }}
                                                    </span>
                                                    
                                                    @if($electionSetting->status === 'completed')
                                                    <form action="{{ route('admin.election.send-notifications') }}" method="POST" class="d-inline ml-3">
                                                        @csrf
                                                        <input type="hidden" name="election_id" value="{{ $electionSetting->id }}">
                                                        <button type="submit" class="btn btn-success btn-sm">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                            </svg>
                                                            Send Winner Notifications
                                                        </button>
                                                    </form>
                                                    @endif
                                                </div>
                                            
                                            @if(!$electionSetting->ignore_automatic_updates)
                                                    <p class="mb-0 text-blue-700 automatic-status-text">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    Automatic updates are <strong>enabled</strong>. The system will update the status based on the configured date ranges.
                                                </p>
                                            @else
                                                    <p class="mb-0 text-yellow-700 automatic-status-text">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                        </svg>
                                                    Automatic updates are <strong>disabled</strong>. Status will remain <strong>{{ ucfirst($electionSetting->status) }}</strong> until manually changed.
                                                </p>
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Positions Management -->
    <div class="card shadow-lg mb-5 border-0">
        <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h5 class="mb-0 font-bold flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Election Positions
            </h5>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary text-sm" onclick="document.getElementById('customAddPositionModal').style.display='block'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add New Position
                </button>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle text-sm" type="button" id="quickActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                        </svg>
                        Quick Actions
                    </button>
                    <ul class="dropdown-menu shadow" aria-labelledby="quickActionsDropdown">
                        <li>
                            <a href="{{ route('admin.election.test-add-position') }}" class="dropdown-item text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Test Form
                            </a>
                        </li>
                        <li>
                            <form action="{{ route('admin.election.position.store') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="title" value="Quick Test Position">
                                <input type="hidden" name="description" value="This is a test position created with the quick button.">
                                <input type="hidden" name="eligible_roles[]" value="Member">
                                <input type="hidden" name="max_votes_per_voter" value="1">
                                <button type="submit" class="dropdown-item text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    Quick Add Test Position
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($positions->isEmpty())
                <div class="text-center py-4 sm:py-5">
                    <div class="mb-3 sm:mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg sm:text-xl font-semibold mb-2">No Positions Defined</h3>
                    <p class="text-gray-500 text-sm sm:text-base mb-3 sm:mb-4">Create a position to start the election process.</p>
                    <button type="button" class="btn btn-primary text-sm" onclick="document.getElementById('customAddPositionModal').style.display='block'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add First Position
                    </button>
                </div>
                
                <!-- Inline Position Form for Direct Access -->
                <div class="card border-0 shadow-sm mt-4 sm:mt-5 mb-4 bg-gray-50">
                    <div class="card-header bg-gray-100 border-0">
                        <h5 class="mb-0 font-semibold text-gray-700 text-sm sm:text-base">Add Position (Direct Form)</h5>
                    </div>
                    <div class="card-body p-3 sm:p-4">
                        <form action="{{ route('admin.election.position.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="direct_title" class="form-label font-medium text-sm sm:text-base">Position Title</label>
                                <input type="text" class="form-control" id="direct_title" name="title" required placeholder="E.g., President, Secretary, Treasurer">
                            </div>
                            <div class="mb-3">
                                <label for="direct_description" class="form-label font-medium text-sm sm:text-base">Description</label>
                                <textarea class="form-control" id="direct_description" name="description" rows="3" required placeholder="Describe the role and responsibilities..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label font-medium text-sm sm:text-base">Eligible Roles</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    @foreach($roles as $role)
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="eligible_roles[]" value="{{ $role->name }}" id="direct_role_{{ $role->id }}">
                                                <label class="form-check-label text-sm" for="direct_role_{{ $role->id }}">
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="direct_max_votes" class="form-label font-medium text-sm sm:text-base">Maximum Votes Per Voter</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="direct_max_votes" name="max_votes_per_voter" min="1" value="1" required>
                                    <span class="input-group-text">vote(s)</span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Add Position
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-gray-700">Title</th>
                                <th class="text-gray-700">Description</th>
                                <th class="text-gray-700">Eligible Roles</th>
                                <th class="text-gray-700">Max Votes</th>
                                <th class="text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($positions as $position)
                                <tr class="border-b">
                                    <td class="font-semibold text-gray-800">{{ $position->title ?? '' }}</td>
                                    <td class="text-gray-600">{{ Str::limit($position->description, 50) }}</td>
                                    <td>
                                        @foreach($position->eligible_roles as $role)
                                            <span class="badge rounded-pill bg-blue-100 text-blue-800 mr-1 mb-1 px-2 py-1 text-xs">{{ $role }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-center font-medium">
                                        <span class="badge rounded-pill bg-gray-200 text-gray-800 px-3 py-2">
                                            {{ $position->max_votes_per_voter ?? '' }}
                                            <span class="text-xs">vote{{ $position->max_votes_per_voter > 1 ? 's' : '' }}</span>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-warning edit-position-btn" 
                                                data-position-id="{{ $position->id ?? '' }}"
                                                data-position-title="{{ $position->title ?? '' }}"
                                                data-position-description="{{ $position->description ?? '' }}"
                                                data-position-eligible-roles="{{ json_encode($position->eligible_roles) }}"
                                                data-position-max-votes="{{ $position->max_votes_per_voter ?? '' }}"
                                                onclick="editPosition(this)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                <span class="ms-1 d-none d-md-inline">Edit</span>
                                            </button>
                                            <form action="{{ route('admin.election.position.delete', $position->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this position?')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    <span class="ms-1 d-none d-md-inline">Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Pending Candidates -->
    <div class="card shadow-lg mb-5">
        <div class="card-header bg-red-700 text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Candidate Management
                </h5>
            </div>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <div class="d-flex align-items-center p-3 bg-light rounded mb-3">
                    <div>
                        <span class="fw-bold">Auto-Approval Status:</span>
                        <span class="ms-2 badge {{ isset($electionSetting->auto_approve_candidates) && $electionSetting->auto_approve_candidates ? 'bg-success' : 'bg-secondary' }}">
                            {{ isset($electionSetting->auto_approve_candidates) && $electionSetting->auto_approve_candidates ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    <div class="text-muted small ms-3">
                        {{ isset($electionSetting->auto_approve_candidates) && $electionSetting->auto_approve_candidates ? 'Candidate applications will be automatically approved.' : 'Candidate applications require manual approval.' }}
                    </div>
                    <div class="ms-auto">
                        @if(isset($electionSetting->auto_approve_candidates) && $electionSetting->auto_approve_candidates)
                            <a href="/admin/election/set-auto-approval/disable" class="btn btn-danger">Disable Auto-Approval</a>
                        @else
                            <a href="/admin/election/set-auto-approval/enable" class="btn btn-success">Enable Auto-Approval</a>
                        @endif
                    </div>
                </div>
            </div>
            
            @if(isset($candidates) && count($candidates) > 0)
                <div class="mb-4">
                    <p class="text-gray-700">There are <strong>{{ count($candidates) }}</strong> candidate applications in the system.</p>
                    
                    <div class="mt-4">
                        <a href="{{ url('/admin/election/candidates') }}" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            View All Candidate Applications
                        </a>
                    </div>
                </div>
                
                <!-- Latest candidates preview -->
                <div class="mt-4">
                    <h6 class="font-semibold mb-3">Latest Applications</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Candidate</th>
                                    <th>Position</th>
                                    <th>Applied On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($candidates->sortByDesc('created_at')->take(3) as $candidate)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $profilePhotoUrl = null;
                                                    $candidateName = 'Unknown User';
                                                    
                                                    // Try to get photo and name from user relationship
                                                    if ($candidate->user) {
                                                        $candidateName = $candidate->user->name;
                                                        $profilePhotoUrl = $candidate->user->profile_photo_url;
                                                    } 
                                                    // Try from user_details if available
                                                    elseif (isset($candidate->user_details['name'])) {
                                                        $candidateName = $candidate->user_details['name'];
                                                        
                                                        // Try to build photo URL if we have a path
                                                        if (!empty($candidate->user_details['photo'])) {
                                                            if (filter_var($candidate->user_details['photo'], FILTER_VALIDATE_URL)) {
                                                                $profilePhotoUrl = $candidate->user_details['photo'];
                                                            } else {
                                                                $profilePhotoUrl = asset('storage/' . $candidate->user_details['photo']);
                                                            }
                                                        }
                                                    }
                                                    
                                                    // If still no photo, use default
                                                    if (!$profilePhotoUrl) {
                                                        if (file_exists(public_path('kofa.png'))) {
                                                            $profilePhotoUrl = asset('kofa.png');
                                                        } else {
                                                            $profilePhotoUrl = asset('img/defaults/user.svg');
                                                        }
                                                    }
                                                @endphp
                                                
                                                <div class="me-3">
                                                    <img src="{{ $profilePhotoUrl }}" 
                                                         alt="{{ $candidateName }}" class="rounded-circle" 
                                                         width="40" height="40">
                                                </div>
                                                <div>
                                                    {{ $candidateName }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $candidate->position ? $candidate->position->title : 'Unknown Position' }}</td>
                                        <td>{{ $candidate->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">No Candidate Applications Yet</h3>
                    <p class="text-gray-500 mb-3">There are currently no candidates who have applied for any positions.</p>
                    <p class="text-sm text-gray-600">Candidates will appear here once members apply for positions during the candidacy period.</p>
                </div>
            @endif
        </div>
    </div>
    
    <div class="text-center mt-6 mb-5">
        <p class="text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Last updated: {{ now()->format('M d, Y - h:i A') }}
        </p>
    </div>
</div>

<!-- Add Position Modal -->
<div id="customAddPositionModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header">
            <h5 class="custom-modal-title">Add New Election Position</h5>
            <span class="custom-close" onclick="document.getElementById('customAddPositionModal').style.display='none'">&times;</span>
        </div>
        
        <form action="{{ route('admin.election.position.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label font-medium">Position Title</label>
                <input type="text" class="form-control" id="title" name="title" required placeholder="E.g., President, Secretary, Treasurer">
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label font-medium">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required placeholder="Describe the role and responsibilities..."></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label font-medium">Eligible Roles</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2">
                    @foreach($roles as $role)
                        <div class="mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="eligible_roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}">
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    {{ $role->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mb-3">
                <label for="max_votes_per_voter" class="form-label font-medium">Maximum Votes Per Voter</label>
                <div class="input-group flex-nowrap">
                    <input type="number" class="form-control" id="max_votes_per_voter" name="max_votes_per_voter" min="1" value="1" required>
                    <span class="input-group-text">vote(s)</span>
                </div>
                <div class="form-text">Number of candidates a voter can select for this position</div>
            </div>
            
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-secondary me-2" onclick="document.getElementById('customAddPositionModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Position
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Custom Edit Position Modal -->
<div id="customEditPositionModal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="custom-modal-header" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
            <h5 class="custom-modal-title">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Position
            </h5>
            <span class="custom-close" onclick="document.getElementById('customEditPositionModal').style.display='none'">&times;</span>
        </div>
        <form id="editPositionForm" action="" method="POST" onsubmit="return validateEditForm()">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="edit_title" class="form-label font-semibold">Position Title</label>
                <input type="text" class="form-control" id="edit_title" name="title" required>
            </div>
            <div class="mb-4">
                <label for="edit_description" class="form-label font-semibold">Position Description</label>
                <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
            </div>
            
            <div class="card shadow-sm border-0 rounded-lg mb-4 bg-gray-50">
                <div class="card-header bg-gray-100 border-0">
                    <h6 class="mb-0 font-semibold">Position Eligibility</h6>
                </div>
                <div class="card-body">
                    <p class="text-gray-600 text-sm mb-3">Select which roles are eligible to apply for this position. These roles must be already assigned to users.</p>
                    <div class="mb-3">
                        <label class="form-label font-medium text-gray-700">Eligible Roles (Required)</label>
                        <div class="row mt-2">
                            @foreach($roles as $role)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input edit-role-checkbox" type="checkbox" name="eligible_roles[]" value="{{ $role->name }}" id="edit_role_{{ $role->id }}">
                                        <label class="form-check-label" for="edit_role_{{ $role->id }}">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-text text-gray-500 mt-2">Users must have one of these roles to be eligible to apply for this position.</div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="edit_max_votes_per_voter" class="form-label font-semibold">Maximum Votes Per Voter</label>
                <div class="input-group">
                    <input type="number" class="form-control" id="edit_max_votes_per_voter" name="max_votes_per_voter" min="1" required>
                    <span class="input-group-text">vote(s)</span>
                </div>
                <div class="form-text text-gray-500">Number of candidates each voter can select for this position.</div>
            </div>
            
            <div class="custom-modal-footer">
                <button type="button" class="btn btn-light" onclick="document.getElementById('customEditPositionModal').style.display='none'">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Cancel
                </button>
                <button type="submit" class="btn btn-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>



@endsection

@push('scripts')
<script src="https://js.pusher.com/8.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Form validation for add position
    function validateForm() {
        // Check if at least one role is selected
        const roleCheckboxes = document.querySelectorAll('#addPositionForm input[name="eligible_roles[]"]:checked');
        if (roleCheckboxes.length === 0) {
            alert('Please select at least one eligible role for this position.');
            return false;
        }
        return true;
    }
    
    // Form validation for edit position
    function validateEditForm() {
        // Check if at least one role is selected
        const roleCheckboxes = document.querySelectorAll('#editPositionForm input[name="eligible_roles[]"]:checked');
        if (roleCheckboxes.length === 0) {
            alert('Please select at least one eligible role for this position.');
            return false;
        }
        return true;
    }
    
    // Function to handle edit position button click
    function editPosition(button) {
        const positionId = button.getAttribute('data-position-id');
        const positionTitle = button.getAttribute('data-position-title');
        const positionDescription = button.getAttribute('data-position-description');
        const positionEligibleRoles = JSON.parse(button.getAttribute('data-position-eligible-roles'));
        const positionMaxVotes = button.getAttribute('data-position-max-votes');
        
        document.getElementById('edit_title').value = positionTitle;
        document.getElementById('edit_description').value = positionDescription;
        document.getElementById('edit_max_votes_per_voter').value = positionMaxVotes;
        
        // Reset checkboxes
        document.querySelectorAll('.edit-role-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        // Set checked for eligible roles
        positionEligibleRoles.forEach(role => {
            const checkbox = document.querySelector(`.edit-role-checkbox[value="${role}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
        
        // Set form action
        document.getElementById('editPositionForm').action = `/admin/election/position/${positionId}`;
        
        // Show the modal
        document.getElementById('customEditPositionModal').style.display = 'block';
    }
    

    
    // Close modal if user clicks outside of it
    window.onclick = function(event) {
        const addModal = document.getElementById('customAddPositionModal');
        const editModal = document.getElementById('customEditPositionModal');
        
        if (event.target === addModal) {
            addModal.style.display = "none";
        }
        
        if (event.target === editModal) {
            editModal.style.display = "none";
        }
    }
    
    // Format time difference for countdown
    function formatTimeDifference(targetDate) {
        const now = new Date();
        const target = new Date(targetDate);
        const diffMs = target - now;
        
        // If the date is in the past
        if (diffMs < 0) {
            const pastDiffMs = Math.abs(diffMs);
            if (pastDiffMs < 60000) return 'just now';
            if (pastDiffMs < 3600000) return Math.floor(pastDiffMs / 60000) + ' minutes ago';
            if (pastDiffMs < 86400000) return Math.floor(pastDiffMs / 3600000) + ' hours ago';
            if (pastDiffMs < 2592000000) return Math.floor(pastDiffMs / 86400000) + ' days ago';
            return Math.floor(pastDiffMs / 2592000000) + ' months ago';
        }
        
        // If the date is in the future
        if (diffMs < 60000) return 'in a few seconds';
        if (diffMs < 3600000) return 'in ' + Math.floor(diffMs / 60000) + ' minutes';
        if (diffMs < 86400000) return 'in ' + Math.floor(diffMs / 3600000) + ' hours';
        if (diffMs < 2592000000) return 'in ' + Math.floor(diffMs / 86400000) + ' days';
        return 'in ' + Math.floor(diffMs / 2592000000) + ' months';
    }
    
    // Update all countdown timers on the page
    function updateCountdowns() {
        const timers = document.querySelectorAll('.countdown-timer');
        timers.forEach(timer => {
            const targetDate = timer.getAttribute('data-target-date');
            if (targetDate) {
                // Check if this is an "Ends" countdown
                const isEndsTimer = timer.textContent.startsWith('Ends');
                const formattedTime = formatTimeDifference(targetDate);
                
                if (isEndsTimer) {
                    timer.textContent = 'Ends ' + formattedTime;
                } else {
                    timer.textContent = formattedTime;
                }
            }
        });
    }
    
    // Function to update the status badges and dropdowns
    function updateStatusDisplay(data) {
        // Update the status badges
        const statusBadges = document.querySelectorAll('.election-status-badge');
        statusBadges.forEach(badge => {
            // Remove all existing classes
            badge.classList.remove('bg-secondary', 'bg-info', 'bg-primary', 'bg-success');
            
            // Add the appropriate class based on the new status
            if (data.status === 'inactive') {
                badge.classList.add('bg-secondary');
                badge.textContent = 'Inactive';
            } else if (data.status === 'candidacy') {
                badge.classList.add('bg-info');
                badge.textContent = 'Candidacy';
            } else if (data.status === 'voting') {
                badge.classList.add('bg-primary');
                badge.textContent = 'Voting';
            } else if (data.status === 'completed') {
                badge.classList.add('bg-success');
                badge.textContent = 'Completed';
            }
        });
        
        // Update the status dropdowns
        const statusDropdowns = document.querySelectorAll('select[name="status"]');
        statusDropdowns.forEach(dropdown => {
            dropdown.value = data.status;
        });
        
        // Update the ignore automatic updates checkbox
        const ignoreCheckboxes = document.querySelectorAll('input[name="ignore_dates"]');
        ignoreCheckboxes.forEach(checkbox => {
            checkbox.checked = data.ignore_automatic_updates;
        });
        
        // Update the status text notifications
        const automaticStatusTexts = document.querySelectorAll('.automatic-status-text');
        automaticStatusTexts.forEach(text => {
            if (data.ignore_automatic_updates) {
                text.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i> Automatic updates are <strong>disabled</strong>. Status will remain <strong>' + data.status.charAt(0).toUpperCase() + data.status.slice(1) + '</strong> until manually changed.';
                text.classList.remove('text-info');
                text.classList.add('text-warning');
            } else {
                text.innerHTML = '<i class="bi bi-info-circle me-1"></i> Automatic updates are <strong>enabled</strong>. The system will update the status based on the configured date ranges.';
                text.classList.remove('text-warning');
                text.classList.add('text-info');
            }
        });
    }
    
    // Realtime updates with Pusher
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Initialize countdowns and update every second
            updateCountdowns();
            setInterval(updateCountdowns, 1000);
            
            // Initialize Pusher with explicit configuration
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY', 'localkey') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER', 'ap1') }}',
                forceTLS: true,
                enabledTransports: ['ws', 'wss'],
                disabledTransports: ['sockjs', 'xhr_streaming', 'xhr_polling'],
                authEndpoint: '/broadcasting/auth'
            });
            
            // Debug - log connection status
            pusher.connection.bind('connected', function() {
                console.log('Successfully connected to Pusher:', pusher.connection.state);
            });
            
            pusher.connection.bind('error', function(error) {
                console.error('Pusher connection error:', error);
            });
            
            // Subscribe to the election status channel
            const channel = pusher.subscribe('election-status');
            
            channel.bind('pusher:subscription_succeeded', function() {
                console.log('Successfully subscribed to election-status channel');
            });
            
            channel.bind('pusher:subscription_error', function(status) {
                console.error('Error subscribing to election-status channel:', status);
            });
            
            // Listen for status updates
            channel.bind('status.updated', function(data) {
                console.log('Election status updated event received:', data);
                
                // Update all status indicators on the page
                updateStatusDisplay(data);
                
                // Flash a notification to the user
                const notificationContainer = document.getElementById('realtime-notification');
                if (notificationContainer) {
                    const notification = document.createElement('div');
                    notification.className = 'alert alert-info alert-dismissible fade show';
                    notification.innerHTML = `
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Election status has been updated to <strong>${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    notificationContainer.appendChild(notification);
                    
                    // Auto-dismiss after 5 seconds
                    setTimeout(() => {
                        notification.classList.remove('show');
                        setTimeout(() => {
                            notification.remove();
                        }, 150);
                    }, 5000);
                }
            });
        } catch (error) {
            console.error('Error setting up Pusher:', error);
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        // Fix for iOS scrolling within modals
        const modals = document.querySelectorAll('.custom-modal');
        
        modals.forEach(modal => {
            modal.addEventListener('touchmove', function(e) {
                const modalContent = modal.querySelector('.custom-modal-content');
                const touch = e.touches[0];
                const startY = touch.clientY;
                
                // Check if we're not scrolling inside the modal content
                if (!modalContent.contains(document.elementFromPoint(touch.clientX, touch.clientY))) {
                    e.preventDefault();
                }
            });
        });
        
        // Close modal when clicking outside on mobile
        modals.forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    });
</script>

<!-- Add a container for real-time notifications at the top of the page -->
<div id="realtime-notification" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>
@endpush 