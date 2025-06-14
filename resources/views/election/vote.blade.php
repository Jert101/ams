@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Cast Your Vote</h1>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <form action="{{ route('election.vote') }}" method="POST">
        @csrf
        @foreach($positions as $position)
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">{{ $position->title }}</h2>
                <p class="text-gray-600 mb-4">{{ $position->description }}</p>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <p class="text-yellow-700">
                        Please select exactly {{ $position->required_votes }} candidate(s) for this position.
                    </p>
                </div>

                <div class="space-y-4">
                    @foreach($position->candidates->where('status', 'approved') as $candidate)
                        <div class="flex items-start space-x-4 p-4 border rounded-lg hover:bg-gray-50">
                            <input type="checkbox" 
                                   name="votes[{{ $loop->index }}][candidate_id]" 
                                   value="{{ $candidate->id }}"
                                   class="position-vote mt-1"
                                   data-position-id="{{ $position->id }}"
                                   data-required-votes="{{ $position->required_votes }}">
                            <input type="hidden" 
                                   name="votes[{{ $loop->index }}][position_id]" 
                                   value="{{ $position->id }}">
                            
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <img src="{{ $candidate->user->profile_photo_url }}" 
                                         alt="{{ $candidate->user->name }}" 
                                         class="h-10 w-10 rounded-full mr-3">
                                    <div>
                                        <h3 class="font-semibold text-gray-800">{{ $candidate->user->name }}</h3>
                                        <p class="text-sm text-gray-600">Member since: {{ $candidate->user->member_since_date->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                
                                @if($candidate->platform)
                                    <div class="mt-3">
                                        <h4 class="font-medium text-gray-700 mb-2">Platform:</h4>
                                        <ul class="list-disc list-inside text-gray-600 space-y-1">
                                            @foreach($candidate->platform as $point)
                                                <li>{{ $point }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                @if($candidate->qualifications)
                                    <div class="mt-3">
                                        <h4 class="font-medium text-gray-700 mb-2">Qualifications:</h4>
                                        <ul class="list-disc list-inside text-gray-600 space-y-1">
                                            @foreach($candidate->qualifications as $qualification)
                                                <li>{{ $qualification }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="mt-6">
            <button type="submit" 
                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                    id="submit-votes">
                Submit Votes
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitButton = document.getElementById('submit-votes');

    // Group checkboxes by position
    const positions = {};
    document.querySelectorAll('.position-vote').forEach(checkbox => {
        const positionId = checkbox.dataset.positionId;
        if (!positions[positionId]) {
            positions[positionId] = {
                checkboxes: [],
                requiredVotes: parseInt(checkbox.dataset.requiredVotes)
            };
        }
        positions[positionId].checkboxes.push(checkbox);
    });

    // Add change event listeners to checkboxes
    Object.values(positions).forEach(position => {
        position.checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkedCount = position.checkboxes.filter(cb => cb.checked).length;
                if (checkedCount > position.requiredVotes) {
                    this.checked = false;
                    alert(`You can only select ${position.requiredVotes} candidate(s) for this position.`);
                }
            });
        });
    });

    // Validate form before submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        let errorMessage = '';

        Object.entries(positions).forEach(([positionId, position]) => {
            const checkedCount = position.checkboxes.filter(cb => cb.checked).length;
            if (checkedCount !== position.requiredVotes) {
                isValid = false;
                const positionTitle = document.querySelector(`[data-position-id="${positionId}"]`)
                    .closest('.bg-white').querySelector('h2').textContent;
                errorMessage += `Please select exactly ${position.requiredVotes} candidate(s) for ${positionTitle}.\n`;
            }
        });

        if (!isValid) {
            alert(errorMessage);
            return;
        }

        form.submit();
    });
});
</script>
@endsection 