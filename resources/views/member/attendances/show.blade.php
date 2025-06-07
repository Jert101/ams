@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Attendance Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4>{{ $attendance->event->name }}</h4>
                            <p class="text-muted">
                                {{ $attendance->event->date->format('l, F j, Y') }} at 
                                {{ $attendance->event->time->format('g:i A') }}
                            </p>
                            
                            <div class="mb-3">
                                <strong>Location:</strong> {{ $attendance->event->location ?? 'Not specified' }}
                            </div>
                            
                            <div class="mb-3">
                                <strong>Status:</strong> 
                                @if ($attendance->status === 'present')
                                    <span class="badge bg-success">Present</span>
                                @elseif ($attendance->status === 'absent')
                                    <span class="badge bg-danger">Absent</span>
                                @elseif ($attendance->status === 'excused')
                                    <span class="badge bg-warning text-dark">Excused</span>
                                @elseif ($attendance->status === 'pending')
                                    <span class="badge bg-info">Pending Verification</span>
                                @endif
                            </div>
                            
                            @if ($attendance->approved_by)
                                <div class="mb-3">
                                    <strong>Approved By:</strong> {{ $attendance->approver->name ?? 'Unknown' }}
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Approved At:</strong> {{ $attendance->approved_at->format('F j, Y g:i A') }}
                                </div>
                            @endif
                            
                            @if ($attendance->remarks)
                                <div class="mb-3">
                                    <strong>Remarks:</strong> {{ $attendance->remarks }}
                                </div>
                            @endif
                            
                            @if ($attendance->selfie_path)
                                <div class="mb-4">
                                    <strong>Submitted Selfie:</strong>
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $attendance->selfie_path) }}" alt="Attendance Selfie" class="img-fluid rounded" style="max-height: 300px;">
                                    </div>
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                <strong>Submitted At:</strong> {{ $attendance->created_at->format('F j, Y g:i A') }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('member.attendances.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Attendances
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 