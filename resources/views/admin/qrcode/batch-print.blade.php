@extends('layouts.admin-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-red-700">Batch Print QR Codes</h1>
        <div>
            <a href="{{ route('admin.qrcode.manage') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to QR Management
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 bg-gray-50 border-b">
            <h2 class="text-lg font-semibold">Select Users to Print QR Codes</h2>
        </div>
        
        <form action="{{ route('qrcode.print-batch') }}" method="post">
            @csrf
            <div class="p-4">
                <div class="mb-4">
                    <p class="text-gray-600 mb-2">Select users whose QR codes you want to print in batch. You can select multiple users.</p>
                    <div class="flex justify-end mb-2">
                        <button type="button" id="select-all" class="text-sm text-blue-600 hover:text-blue-800">Select All</button>
                        <span class="mx-2 text-gray-400">|</span>
                        <button type="button" id="deselect-all" class="text-sm text-blue-600 hover:text-blue-800">Deselect All</button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($users as $user)
                        <div class="border rounded p-3 flex items-center space-x-3">
                            <input type="checkbox" name="user_ids[]" value="{{ $user->user_id }}" id="user-{{ $user->user_id }}" class="user-checkbox h-5 w-5 text-red-600">
                            <label for="user-{{ $user->user_id }}" class="flex-grow cursor-pointer">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="{{ $user->profile_photo_url ?? asset('img/defaults/user.svg') }}" alt="{{ $user->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->role->name ?? 'No Role' }}</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="p-4 bg-gray-50 border-t flex justify-end">
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Print Selected QR Codes
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllBtn = document.getElementById('select-all');
        const deselectAllBtn = document.getElementById('deselect-all');
        const checkboxes = document.querySelectorAll('.user-checkbox');
        
        selectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        });
        
        deselectAllBtn.addEventListener('click', function() {
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        });
        
        // Make the whole card clickable for the checkbox
        const cards = document.querySelectorAll('.border.rounded');
        cards.forEach(card => {
            card.addEventListener('click', function(e) {
                // Don't toggle if clicking on the checkbox itself
                if (e.target.type !== 'checkbox') {
                    const checkbox = this.querySelector('input[type="checkbox"]');
                    checkbox.checked = !checkbox.checked;
                }
            });
        });
    });
</script>
@endpush
@endsection
 