@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Absences Management</h1>
        <a href="{{ route('secretary.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Dashboard
        </a>
    </div>
    
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <!-- Filter Form -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form action="{{ route('secretary.absences.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                
                <div>
                    <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">End Date:</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                
                <div>
                    <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">Member:</label>
                    <select name="user_id" id="user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">All Members</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <div class="flex mb-6 space-x-4">
        <a href="{{ route('secretary.absences.consecutive') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
            View Consecutive Absences
        </a>
        <a href="{{ route('secretary.absences.by-member') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            View Absences by Member
        </a>
    </div>
    
    <!-- Absences Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($absences as $absence)
                        <tr>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $absence->user->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $absence->event->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $absence->event->date }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                @if ($absence->status === 'absent')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absent</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Excused</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $absence->approved_by ? \App\Models\User::find($absence->approved_by)->name : 'N/A' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $absence->remarks ?? 'N/A' }}</td>
                            <td class="py-4 px-6 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <form action="{{ route('secretary.absences.update', $absence) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="excused">
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900" onclick="return confirm('Are you sure you want to mark this absence as excused?')">
                                            Mark as Excused
                                        </button>
                                    </form>
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 open-notification-modal" data-user-id="{{ $absence->user->id }}" data-user-name="{{ $absence->user->name }}">Send Notification</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="7">No absences found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $absences->links() }}
    </div>
</div>

<!-- Notification Modal -->
<div id="notification-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Send Notification</h3>
            <button id="close-modal" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="{{ route('secretary.notifications.send') }}" method="POST">
            @csrf
            <input type="hidden" name="user_ids[]" id="notification-user-id">
            
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-500">Sending to:</p>
                <p id="notification-user-name" class="text-base font-medium"></p>
            </div>
            
            <div class="mb-4">
                <label for="notification-message" class="block text-gray-700 text-sm font-bold mb-2">Message:</label>
                <textarea id="notification-message" name="message" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>We've noticed that you were absent from a recent mass service. Please let us know if you need any assistance.</textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancel-notification" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Send Notification
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Notification Modal
        const modal = document.getElementById('notification-modal');
        const openButtons = document.querySelectorAll('.open-notification-modal');
        const closeButton = document.getElementById('close-modal');
        const cancelButton = document.getElementById('cancel-notification');
        const userIdInput = document.getElementById('notification-user-id');
        const userNameDisplay = document.getElementById('notification-user-name');
        
        openButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                userIdInput.value = userId;
                userNameDisplay.textContent = userName;
                modal.classList.remove('hidden');
            });
        });
        
        function closeModal() {
            modal.classList.add('hidden');
        }
        
        closeButton.addEventListener('click', closeModal);
        cancelButton.addEventListener('click', closeModal);
        
        // Close when clicking outside the modal content
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    });
</script>
@endpush
@endsection
