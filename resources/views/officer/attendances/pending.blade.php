@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pending Attendance Verifications</h5>
                    <div>
                        <a href="{{ route('officer.attendances.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-list"></i> All Attendances
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Member</th>
                                    <th>Event</th>
                                    <th>Date & Time</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingAttendances as $attendance)
                                    <tr>
                                        <td>{{ $attendance->user->name }}</td>
                                        <td>{{ $attendance->event->name }}</td>
                                        <td>
                                            {{ $attendance->event->date->format('M d, Y') }} at 
                                            {{ $attendance->event->time->format('g:i A') }}
                                        </td>
                                        <td>{{ $attendance->created_at->format('M d, Y g:i A') }}</td>
                                        <td>
                                            <a href="{{ route('officer.attendances.verify', $attendance) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-check-circle"></i> Verify
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No pending attendance verifications.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $pendingAttendances->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 