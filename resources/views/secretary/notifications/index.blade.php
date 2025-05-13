@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Notifications Management</h1>
        <a href="{{ route('secretary.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Dashboard
        </a>
    </div>
    
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    <!-- Notification Types -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6 hover:bg-gray-50 transition duration-200">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Absence Notifications</h2>
            <p class="text-gray-600 mb-4">Send notifications to members with consecutive absences</p>
            <a href="{{ route('secretary.absences.consecutive') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Manage Absences
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 hover:bg-gray-50 transition duration-200">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Mass Notifications</h2>
            <p class="text-gray-600 mb-4">Send notifications to multiple members at once</p>
            <button id="open-mass-notification" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Send Mass Notification
            </button>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 hover:bg-gray-50 transition duration-200">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Custom Notification</h2>
            <p class="text-gray-600 mb-4">Send a custom notification to a specific member</p>
            <button id="open-custom-notification" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                Send Custom Notification
            </button>
        </div>
    </div>
    
    <!-- Notification History -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Recent Notifications</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent At</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Read</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($notifications as $notification)
                        <tr>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $notification->user->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ ucfirst($notification->type) }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ Str::limit($notification->message, 50) }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $notification->sent_at ? date('M d, Y H:i', strtotime($notification->sent_at)) : 'Not sent' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                @if ($notification->read_at)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Read ({{ date('M d, Y H:i', strtotime($notification->read_at)) }})</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Unread</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="5">No notifications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
</div>

<!-- Mass Notification Modal -->
<div id="mass-notification-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Send Mass Notification</h3>
            <button id="close-mass-modal" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="{{ route('secretary.notifications.mass') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="notification-type" class="block text-gray-700 text-sm font-bold mb-2">Recipient Type:</label>
                <select id="notification-type" name="recipient_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="all_members">All Members</option>
                    <option value="absent_members">Members with Recent Absences</option>
                    <option value="consecutive_absences">Members with Consecutive Absences</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="mass-notification-message" class="block text-gray-700 text-sm font-bold mb-2">Message:</label>
                <textarea id="mass-notification-message" name="message" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>Please be reminded to attend our upcoming mass services regularly. Thank you for your continued participation.</textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancel-mass-notification" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Send Notification
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Custom Notification Modal -->
<div id="custom-notification-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Send Custom Notification</h3>
            <button id="close-custom-modal" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="{{ route('secretary.notifications.send') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">Select Member:</label>
                <select id="user_id" name="user_ids[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select a member</option>
                    @foreach (\App\Models\User::whereHas('role', function($query) { $query->where('name', 'Member'); })->orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <label for="custom-notification-message" class="block text-gray-700 text-sm font-bold mb-2">Message:</label>
                <textarea id="custom-notification-message" name="message" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancel-custom-notification" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
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
        // Mass Notification Modal
        const massModal = document.getElementById('mass-notification-modal');
        const openMassButton = document.getElementById('open-mass-notification');
        const closeMassButton = document.getElementById('close-mass-modal');
        const cancelMassButton = document.getElementById('cancel-mass-notification');
        
        openMassButton.addEventListener('click', function() {
            massModal.classList.remove('hidden');
        });
        
        function closeMassModal() {
            massModal.classList.add('hidden');
        }
        
        closeMassButton.addEventListener('click', closeMassModal);
        cancelMassButton.addEventListener('click', closeMassModal);
        
        // Custom Notification Modal
        const customModal = document.getElementById('custom-notification-modal');
        const openCustomButton = document.getElementById('open-custom-notification');
        const closeCustomButton = document.getElementById('close-custom-modal');
        const cancelCustomButton = document.getElementById('cancel-custom-notification');
        
        openCustomButton.addEventListener('click', function() {
            customModal.classList.remove('hidden');
        });
        
        function closeCustomModal() {
            customModal.classList.add('hidden');
        }
        
        closeCustomButton.addEventListener('click', closeCustomModal);
        cancelCustomButton.addEventListener('click', closeCustomModal);
        
        // Close when clicking outside the modal content
        massModal.addEventListener('click', function(e) {
            if (e.target === massModal) {
                closeMassModal();
            }
        });
        
        customModal.addEventListener('click', function(e) {
            if (e.target === customModal) {
                closeCustomModal();
            }
        });
    });
</script>
@endpush
@endsection
