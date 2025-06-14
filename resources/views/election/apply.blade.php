@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Apply for Candidacy</h1>

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if(ElectionCandidate::hasExistingApplication(auth()->id()))
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
                <p class="font-semibold">You have already submitted a candidacy application for this election period.</p>
                <p class="mt-2">You can only apply for one position per election. Please wait for the next election period if you wish to apply for a different position.</p>
            </div>
        @else
            <form action="{{ route('election.apply.store') }}" method="POST" class="bg-white shadow-md rounded-lg p-6">
                @csrf
                <input type="hidden" name="position_id" value="{{ $position->id }}">

                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Position: {{ $position->title }}</h2>
                    <p class="text-gray-600">{{ $position->description }}</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="platform">
                        Platform and Goals
                    </label>
                    <div class="space-y-4" id="platform-fields">
                        <div class="flex items-center gap-2">
                            <input type="text" name="platform[]" class="form-input rounded-md shadow-sm mt-1 block w-full" 
                                   placeholder="Enter your platform point" required>
                            <button type="button" class="add-platform-field px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Add your key platform points and what you aim to achieve in this position.</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="qualifications">
                        Qualifications and Experience
                    </label>
                    <div class="space-y-4" id="qualification-fields">
                        <div class="flex items-center gap-2">
                            <input type="text" name="qualifications[]" class="form-input rounded-md shadow-sm mt-1 block w-full" 
                                   placeholder="Enter your qualification" required>
                            <button type="button" class="add-qualification-field px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">List your relevant qualifications and experience that make you suitable for this position.</p>
                </div>

                <div class="flex items-center justify-end mt-6">
                    <a href="{{ route('election.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">Cancel</a>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Submit Application
                    </button>
                </div>
            </form>

            <script>
                function createField(type) {
                    const div = document.createElement('div');
                    div.className = 'flex items-center gap-2';
                    div.innerHTML = `
                        <input type="text" name="${type}[]" class="form-input rounded-md shadow-sm mt-1 block w-full" 
                               placeholder="Enter your ${type === 'platform' ? 'platform point' : 'qualification'}" required>
                        <button type="button" class="remove-field px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    return div;
                }

                document.querySelectorAll('.add-platform-field').forEach(button => {
                    button.addEventListener('click', () => {
                        document.getElementById('platform-fields').appendChild(createField('platform'));
                    });
                });

                document.querySelectorAll('.add-qualification-field').forEach(button => {
                    button.addEventListener('click', () => {
                        document.getElementById('qualification-fields').appendChild(createField('qualifications'));
                    });
                });

                document.addEventListener('click', (e) => {
                    if (e.target.closest('.remove-field')) {
                        e.target.closest('.flex').remove();
                    }
                });
            </script>
        @endif
    </div>
</div>
@endsection 