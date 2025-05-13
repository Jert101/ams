@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Consecutive Absences</h1>
        <a href="{{ route('secretary.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Dashboard
        </a>
    </div>
    
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Recent Events</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Name</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentEvents as $event)
                        <tr>
                            <td class="py-2 px-4 border-b border-gray-200">{{ $event->name }}</td>
                            <td class="py-2 px-4 border-b border-gray-200">{{ $event->date }}</td>
                            <td class="py-2 px-4 border-b border-gray-200">{{ $event->time }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Absence Filter Tabs -->
    <div class="bg-white shadow-md rounded-lg mb-6 overflow-hidden">
        <div class="flex border-b">
            <button class="absence-tab py-3 px-6 text-sm font-medium {{ !request('filter') || request('filter') == 'all' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-indigo-600' }}" data-filter="all">
                All Absences <span class="ml-1 px-2 py-1 text-xs rounded-full {{ !request('filter') || request('filter') == 'all' ? 'bg-white text-indigo-600' : 'bg-gray-200 text-gray-600' }}">{{ $users->count() }}</span>
            </button>
            <button class="absence-tab py-3 px-6 text-sm font-medium {{ request('filter') == '3' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-indigo-600' }}" data-filter="3">
                3 Consecutive <span class="ml-1 px-2 py-1 text-xs rounded-full {{ request('filter') == '3' ? 'bg-white text-indigo-600' : 'bg-gray-200 text-gray-600' }}">{{ $users->where('recent_absences', 3)->count() }}</span>
            </button>
            <button class="absence-tab py-3 px-6 text-sm font-medium {{ request('filter') == '4' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:text-indigo-600' }}" data-filter="4">
                4+ Consecutive <span class="ml-1 px-2 py-1 text-xs rounded-full {{ request('filter') == '4' ? 'bg-white text-indigo-600' : 'bg-gray-200 text-gray-600' }}">{{ $users->where('recent_absences', '>=', 4)->count() }}</span>
            </button>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-semibold">Members with Consecutive Absences</h2>
            @if($users->count() > 0)
                <button id="bulk-notification" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Send Bulk Notification
                </button>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <input type="checkbox" id="select-all" class="mr-2 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                Member
                            </div>
                        </th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consecutive Absences</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Absence Details</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($filteredUsers ?? $users as $user)
                        <tr class="absence-row" data-absences="{{ $user->recent_absences }}">
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                <div class="flex items-center">
                                    <input type="checkbox" name="selected_users[]" value="{{ $user->id }}" class="user-checkbox mr-2 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    {{ $user->name }}
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $user->email }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $user->phone ?? 'N/A' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->recent_absences >= 4 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $user->recent_absences }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                <ul class="list-disc list-inside">
                                    @foreach ($userAbsences[$user->id] as $absence)
                                        <li>{{ $absence->event->name }} ({{ $absence->event->date }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-4 px-6 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 open-notification-modal" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" data-absences="{{ $user->recent_absences }}">Send Notification</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="6">No members with consecutive absences found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Notification Modal (Individual) -->
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
                <textarea id="notification-message" name="message" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
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

<!-- Bulk Notification Modal -->
<div id="bulk-notification-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Send Bulk Notification</h3>
            <button id="close-bulk-modal" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form action="{{ route('secretary.notifications.send') }}" method="POST" id="bulk-notification-form">
            @csrf
            <div id="bulk-user-ids"></div>
            
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-500">Sending to:</p>
                <p id="bulk-user-count" class="text-base font-medium">0 selected members</p>
            </div>
            
            <div class="mb-4">
                <label for="bulk-notification-message" class="block text-gray-700 text-sm font-bold mb-2">Message:</label>
                <textarea id="bulk-notification-message" name="message" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="button" id="cancel-bulk-notification" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Cancel
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Send Notifications
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter buttons
        const filterButtons = document.querySelectorAll('.absence-tab');
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                window.location.href = '{{ route("secretary.absences.consecutive") }}' + (filter !== 'all' ? '?filter=' + filter : '');
            });
        });
        
        // Template messages based on absences
        const threeDayTemplate = "Dear member, we've noticed you've been absent for 3 consecutive Sundays. Please note that according to our organization's rules, you need to undergo counseling. Please contact us as soon as possible.";
        const fourDayTemplate = "Dear member, we've noticed you've been absent for 4 or more consecutive Sundays. This is a serious matter that requires your immediate attention. Please contact us as soon as possible to discuss this matter.";
        const defaultTemplate = "Dear member, we've noticed you've been absent from several recent mass services. Please let us know if there's anything we can do to help you attend our services regularly.";

        // Individual Notification Modal
        const modal = document.getElementById('notification-modal');
        const openButtons = document.querySelectorAll('.open-notification-modal');
        const closeButton = document.getElementById('close-modal');
        const cancelButton = document.getElementById('cancel-notification');
        const userIdInput = document.getElementById('notification-user-id');
        const userNameDisplay = document.getElementById('notification-user-name');
        const messageTextarea = document.getElementById('notification-message');
        
        openButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                const absences = parseInt(this.getAttribute('data-absences'));
                
                userIdInput.value = userId;
                userNameDisplay.textContent = userName;
                
                // Set template message based on number of absences
                if (absences === 3) {
                    messageTextarea.value = threeDayTemplate;
                } else if (absences >= 4) {
                    messageTextarea.value = fourDayTemplate;
                } else {
                    messageTextarea.value = defaultTemplate;
                }
                
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
        
        // Bulk notification functionality
        const selectAllCheckbox = document.getElementById('select-all');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const bulkNotificationButton = document.getElementById('bulk-notification');
        const bulkModal = document.getElementById('bulk-notification-modal');
        const closeBulkButton = document.getElementById('close-bulk-modal');
        const cancelBulkButton = document.getElementById('cancel-bulk-notification');
        const bulkUserCount = document.getElementById('bulk-user-count');
        const bulkUserIds = document.getElementById('bulk-user-ids');
        const bulkMessageTextarea = document.getElementById('bulk-notification-message');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
        
        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Check if all are checked
                const allChecked = Array.from(userCheckboxes).every(cb => cb.checked);
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allChecked;
                }
            });
        });
        
        if (bulkNotificationButton) {
            bulkNotificationButton.addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
                
                if (selectedCheckboxes.length === 0) {
                    alert('Please select at least one member to send notifications.');
                    return;
                }
                
                // Clear previous inputs
                bulkUserIds.innerHTML = '';
                
                // Add user IDs as hidden inputs
                selectedCheckboxes.forEach(checkbox => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = checkbox.value;
                    bulkUserIds.appendChild(input);
                });
                
                bulkUserCount.textContent = selectedCheckboxes.length + ' selected members';
                
                // Set default message
                if (selectedCheckboxes.length > 0) {
                    // Check if all selected users have the same absence count
                    const firstAbsences = parseInt(selectedCheckboxes[0].closest('tr').dataset.absences);
                    const allSameAbsences = Array.from(selectedCheckboxes).every(cb => 
                        parseInt(cb.closest('tr').dataset.absences) === firstAbsences);
                    
                    if (allSameAbsences) {
                        if (firstAbsences === 3) {
                            bulkMessageTextarea.value = threeDayTemplate;
                        } else if (firstAbsences >= 4) {
                            bulkMessageTextarea.value = fourDayTemplate;
                        } else {
                            bulkMessageTextarea.value = defaultTemplate;
                        }
                    } else {
                        bulkMessageTextarea.value = defaultTemplate;
                    }
                }
                
                bulkModal.classList.remove('hidden');
            });
        }
        
        function closeBulkModal() {
            bulkModal.classList.add('hidden');
        }
        
        if (closeBulkButton) closeBulkButton.addEventListener('click', closeBulkModal);
        if (cancelBulkButton) cancelBulkButton.addEventListener('click', closeBulkModal);
        
        // Close when clicking outside the modal content
        if (bulkModal) {
            bulkModal.addEventListener('click', function(e) {
                if (e.target === bulkModal) {
                    closeBulkModal();
                }
            });
        }
    });
</script>
@endpush
@endsection
