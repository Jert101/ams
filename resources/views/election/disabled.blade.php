@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">Election System</h4>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-calendar-x text-secondary" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="mb-4">The Election System is Currently Disabled</h3>
                    <p class="lead text-muted">The KofA election system is not active at this time. Please check back later or contact an administrator for more information.</p>
                    <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="bi bi-house-door me-2"></i> Return to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 