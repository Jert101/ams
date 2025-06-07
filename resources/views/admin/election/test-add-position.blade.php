@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Test Add Position</h1>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card">
        <div class="card-header">Add Position Form</div>
        <div class="card-body">
            <form action="{{ route('admin.election.position.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">Position Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Eligible Roles</label>
                    @foreach($roles as $role)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="eligible_roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}">
                            <label class="form-check-label" for="role_{{ $role->id }}">
                                {{ $role->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                
                <div class="mb-3">
                    <label for="max_votes_per_voter" class="form-label">Maximum Votes Per Voter</label>
                    <input type="number" class="form-control" id="max_votes_per_voter" name="max_votes_per_voter" min="1" value="1" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection 