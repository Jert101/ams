@extends('layouts.admin-app')

@section('content')
<div class="container">
    <h1 class="mb-4 text-red-700 font-bold text-3xl">Candidate Applications</h1>

    <div class="mb-4">
        <a href="{{ route('admin.election.index') }}" class="btn btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Election Management
        </a>
    </div>

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">All Candidate Applications</h5>
        </div>
        <div class="card-body">
            @if(isset($candidates) && count($candidates) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Candidate</th>
                                <th>Position</th>
                                <th>Applied On</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($candidates as $candidate)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @php
                                                    $profilePhotoUrl = null;
                                                    
                                                    // First try direct URL from controller
                                                    if (isset($candidate->profile_photo_url)) {
                                                        $profilePhotoUrl = $candidate->profile_photo_url;
                                                    }
                                                    // Try to get photo from pre-processed data
                                                    elseif(isset($candidate->profile_photo) && $candidate->profile_photo) {
                                                        if (filter_var($candidate->profile_photo, FILTER_VALIDATE_URL)) {
                                                            $profilePhotoUrl = $candidate->profile_photo;
                                                        } else {
                                                            $profilePhotoUrl = asset('storage/' . $candidate->profile_photo);
                                                        }
                                                    }
                                                    // Try to get it from user relationship
                                                    elseif($candidate->user && $candidate->user->profile_photo_url) {
                                                        $profilePhotoUrl = $candidate->user->profile_photo_url;
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
                                                <img src="{{ $profilePhotoUrl }}" 
                                                     alt="{{ $candidate->user_name }}" class="rounded-circle" 
                                                     width="40" height="40">
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $candidate->user_name }}</div>
                                                @if(isset($candidate->user_email) && $candidate->user_email != 'No Email Available')
                                                    <small>{{ $candidate->user_email }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($candidate->position)
                                            {{ $candidate->position->title }}
                                        @else
                                            <span class="text-danger">Position not found</span>
                                        @endif
                                    </td>
                                    <td>{{ $candidate->created_at->format('M d, Y - h:i A') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $candidate->status === 'approved' ? 'success' : 
                                            ($candidate->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($candidate->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ url('/admin/election/candidates/'.$candidate->id) }}" class="btn btn-info btn-sm">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                                </svg>
                                                View
                                            </a>
                                            
                                            @if($candidate->status === 'pending')
                                                <form action="{{ route('admin.election.candidate.approve', $candidate->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                                                            <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>
                                                
                                                <button type="button" class="btn btn-danger btn-sm" onclick="openRejectModal({{ $candidate->id }}, '{{ $candidate->user_name }}', '{{ $candidate->position->title ?? 'Unknown Position' }}')">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                                                    </svg>
                                                    Reject
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <p>There are currently no candidate applications.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Global Reject Modal -->
<div class="modal fade" id="globalRejectModal" tabindex="-1" aria-labelledby="globalRejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="globalRejectModalLabel">Reject Candidate Application</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rejectForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p id="rejectMessage">Are you sure you want to reject this candidacy application?</p>
                    <div class="mb-3">
                        <p class="text-muted small" id="candidateIds"></p>
                        <label for="rejection_reason" class="form-label">Rejection Reason:</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Fix for modal z-index and visibility issues */
.modal {
    z-index: 1050 !important;
}

.modal-backdrop {
    z-index: 1040 !important;
}

.modal-dialog {
    z-index: 1060 !important;
}

.modal-content {
    box-shadow: 0 5px 15px rgba(0,0,0,.5);
}

/* Make sure modal form elements are clickable */
.modal-body, .modal-footer {
    position: relative;
    z-index: 1061 !important;
}

/* Fix textarea in modal */
.modal textarea {
    position: relative;
    z-index: 1062 !important;
    background-color: #fff;
}

/* Ensure buttons are clickable */
.modal-footer button {
    position: relative;
    z-index: 1063 !important;
}
</style>

<script>
    // Function to open the reject modal
    function openRejectModal(candidateId, candidateName, positionTitle) {
        // Set the form action
        document.getElementById('rejectForm').action = "{{ route('admin.election.candidate.reject', '') }}/" + candidateId;
        
        // Set the rejection message
        document.getElementById('rejectMessage').innerHTML = 
            "Are you sure you want to reject the candidacy application of <strong>" + 
            candidateName + "</strong> for <strong>" + positionTitle + "</strong>?";
        
        // Set candidate IDs info
        document.getElementById('candidateIds').textContent = "Candidate ID: " + candidateId;
        
        // Clear any previous rejection reason
        document.getElementById('rejection_reason').value = '';
        
        // Open the modal
        var rejectModal = new bootstrap.Modal(document.getElementById('globalRejectModal'));
        rejectModal.show();
    }

    // Initialize Bootstrap modals
    document.addEventListener('DOMContentLoaded', function() {
        // Make sure Bootstrap is properly loaded
        if (typeof bootstrap !== 'undefined') {
            console.log('Bootstrap is loaded');
        } else {
            console.error('Bootstrap is not loaded');
        }
    });
</script>
@endsection 