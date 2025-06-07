@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Submit Attendance</h1>
        <a href="{{ route('member.attendances.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Attendances
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
            <h2 class="text-lg font-semibold">Record Your Attendance</h2>
            <p class="text-sm text-blue-100 mt-1">Submit your attendance with a selfie for verification</p>
        </div>
        
        <div class="p-6">
            @if (session('error'))
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if (count($todayEvents) === 0)
                <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd" />
                        </svg>
                        <span>There are no events scheduled for today.</span>
                    </div>
                </div>
            @else
                <form method="POST" action="{{ route('member.attendances.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-2">
                        <label for="event_id" class="block text-sm font-medium text-gray-700">Select Event</label>
                        <select name="event_id" id="event_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md @error('event_id') border-red-500 @enderror" required>
                            <option value="">-- Select an event --</option>
                            @foreach ($todayEvents as $event)
                                <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }} 
                                    {{ isset($existingAttendances[$event->id]) ? 'disabled' : '' }}
                                    class="{{ isset($existingAttendances[$event->id]) ? 'text-gray-400' : '' }}">
                                    {{ $event->name }} ({{ $event->time->format('g:i A') }})
                                    {{ isset($existingAttendances[$event->id]) ? '- Already Submitted' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('event_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="selfieInstructionContainer" class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded" style="display: none;">
                        <div class="flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium">Selfie Instructions:</h3>
                                <p id="selfieInstruction" class="text-sm"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="selfie" class="block text-sm font-medium text-gray-700">Upload Selfie</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 transition">
                            <div class="space-y-1 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="selfie" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Upload a selfie</span>
                                        <input id="selfie" name="selfie" type="file" class="sr-only" accept="image/*" capture="user" required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 10MB</p>
                            </div>
                        </div>
                        <div class="mt-1 flex items-center" id="image-preview-container" style="display: none;">
                            <img id="image-preview" class="h-32 w-auto object-cover rounded" alt="Preview">
                            <button type="button" id="remove-image" class="ml-2 bg-white rounded-md p-1 text-gray-400 hover:text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        @error('selfie')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks (Optional)</label>
                        <textarea id="remarks" name="remarks" rows="3" class="shadow-sm block w-full focus:ring-blue-500 focus:border-blue-500 sm:text-sm border border-gray-300 rounded-md @error('remarks') border-red-500 @enderror" placeholder="Any additional notes or comments">{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end mt-6 space-x-3">
                        <a href="{{ route('member.attendances.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Submit Attendance
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Event data with selfie instructions
        const eventData = {
            @foreach ($todayEvents as $event)
                {{ $event->id }}: {
                    selfieInstruction: "{{ $event->selfie_instruction ?? 'Please take a clear selfie showing your face.' }}"
                },
            @endforeach
        };

        // Update selfie instructions when event is selected
        const eventSelect = document.getElementById('event_id');
        const selfieInstructionContainer = document.getElementById('selfieInstructionContainer');
        const selfieInstructionElement = document.getElementById('selfieInstruction');

        eventSelect.addEventListener('change', function() {
            const selectedEventId = this.value;
            
            if (selectedEventId && eventData[selectedEventId]) {
                selfieInstructionElement.textContent = eventData[selectedEventId].selfieInstruction;
                selfieInstructionContainer.style.display = 'block';
            } else {
                selfieInstructionContainer.style.display = 'none';
            }
        });

        // Trigger change event if an event is already selected
        if (eventSelect.value) {
            eventSelect.dispatchEvent(new Event('change'));
        }
        
        // Image preview functionality
        const fileInput = document.getElementById('selfie');
        const previewContainer = document.getElementById('image-preview-container');
        const preview = document.getElementById('image-preview');
        const removeButton = document.getElementById('remove-image');
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'flex';
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
        
        removeButton.addEventListener('click', function() {
            fileInput.value = '';
            previewContainer.style.display = 'none';
        });
    });
</script>
@endpush
@endsection 