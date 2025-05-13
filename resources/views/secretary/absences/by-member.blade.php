@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Absences by Member</h1>
        <a href="{{ route('secretary.absences.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Absences
        </a>
    </div>
    
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Member Absence Summary</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Absences</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consecutive Absences</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($members as $member)
                        <tr>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $member->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $member->email }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $member->phone ?? 'N/A' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                @if ($absenceCounts[$member->id]['total'] > 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ $absenceCounts[$member->id]['total'] }}</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">0</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                @if ($absenceCounts[$member->id]['consecutive'] > 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ $absenceCounts[$member->id]['consecutive'] }}</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">0</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('secretary.reports.member', ['user_id' => $member->id]) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">View Report</a>
                                    @if ($absenceCounts[$member->id]['total'] > 0)
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 open-notification-modal" data-user-id="{{ $member->id }}" data-user-name="{{ $member->name }}">Send Notification</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="6">No members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
                <textarea id="notification-message" name="message" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>We've noticed that you have missed some mass services. We hope everything is well with you. Please let us know if there's anything we can do to help.</textarea>
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
