@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Verify Attendance Submission</h5>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4>{{ $attendance->event->name }}</h4>
                            <p class="text-muted">
                                {{ $attendance->event->date->format('l, F j, Y') }} at 
                                {{ $attendance->event->time->format('g:i A') }}
                            </p>
                            
                            <div class="mb-3">
                                <strong>Member:</strong> {{ $attendance->user->name }}
                            </div>
                            
                            <div class="mb-3">
                                <strong>Location:</strong> {{ $attendance->event->location ?? 'Not specified' }}
                            </div>
                            
                            <div class="mb-3">
                                <strong>Submitted At:</strong> {{ $attendance->created_at->format('F j, Y g:i A') }}
                            </div>
                            
                            @if ($attendance->event->selfie_instruction)
                                <div class="alert alert-info">
                                    <strong>Selfie Instructions:</strong> {{ $attendance->event->selfie_instruction }}
                                </div>
                            @endif
                            
                            @if ($attendance->remarks)
                                <div class="mb-3">
                                    <strong>Member's Remarks:</strong> {{ $attendance->remarks }}
                                </div>
                            @endif
                            
                            @if ($attendance->selfie_path)
                                <div class="mb-4">
                                    <strong>Submitted Selfie:</strong>
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $attendance->selfie_path) }}" alt="Attendance Selfie" class="img-fluid rounded" style="max-width: 100%;">
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    No selfie was submitted with this attendance record.
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('officer.attendances.process-verification', $attendance) }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Verification Decision</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="verification" id="approve" value="approve" checked>
                                <label class="form-check-label" for="approve">
                                    <span class="text-success"><i class="fas fa-check-circle"></i> Approve</span> - Confirm attendance as present
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="verification" id="reject" value="reject">
                                <label class="form-check-label" for="reject">
                                    <span class="text-danger"><i class="fas fa-times-circle"></i> Reject</span> - Mark as absent
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="remarks" class="form-label fw-bold">Officer Remarks (Optional)</label>
                            <textarea name="remarks" id="remarks" class="form-control @error('remarks') is-invalid @enderror" rows="3">{{ old('remarks') }}</textarea>
                            <div class="form-text">Add any notes about this verification decision.</div>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Submit Verification
                            </button>
                            <a href="{{ route('officer.attendances.pending') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Pending List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 