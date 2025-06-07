@extends('layouts.secretary-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-wrap justify-between items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-indigo-700 mb-2 md:mb-0">Sunday Mass Absences</h1>
        <div class="flex space-x-2">
            <div class="relative inline-block text-left">
                <button id="export-dropdown-button" class="btn-secondary flex items-center">
                    <i class="bi bi-file-earmark-text mr-1"></i> Export Report
                    <i class="bi bi-chevron-down ml-1"></i>
                </button>
                <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg z-10">
                    <div class="py-1">
                        <a href="{{ route('secretary.reports.export-csv') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="bi bi-file-earmark-spreadsheet mr-1"></i> Export Attendance Report
                        </a>
                        <div class="border-t border-gray-200 my-1"></div>
                        <span class="block px-4 py-1 text-xs font-medium text-gray-500 bg-gray-50">3 CONSECUTIVE ABSENCES</span>
                        <a href="{{ route('secretary.reports.export-three-consecutive-absences') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="bi bi-file-earmark-excel mr-1"></i> Export as CSV
                        </a>
                        <a href="{{ route('secretary.reports.export-three-consecutive-absences-pdf') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="bi bi-file-earmark-pdf mr-1"></i> Export as PDF
                        </a>
                        <div class="border-t border-gray-200 my-1"></div>
                        <span class="block px-4 py-1 text-xs font-medium text-gray-500 bg-gray-50">4+ CONSECUTIVE ABSENCES</span>
                        <a href="{{ route('secretary.reports.export-four-plus-consecutive-absences') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="bi bi-file-earmark-excel mr-1"></i> Export as CSV
                        </a>
                        <a href="{{ route('secretary.reports.export-four-plus-consecutive-absences-pdf') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="bi bi-file-earmark-pdf mr-1"></i> Export as PDF
                        </a>
                    </div>
                </div>
            </div>
            <button id="send-reminder" class="btn-primary">
                <i class="bi bi-bell mr-1"></i> Send Reminders
            </button>
        </div>
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

    <!-- Information Alert -->
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline font-medium">Attendance Rule:</span>
        <span class="block sm:inline"> A member is only marked as absent for a Sunday if they miss ALL 4 masses on that day. Attending at least one mass on Sunday will count as present for that Sunday.</span>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <h2 class="text-lg font-semibold text-indigo-700 mb-4">Filter Absences</h2>
        <form action="{{ route('secretary.absences.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Search Member</label>
                <select id="user_id" name="user_id" class="form-select rounded-md shadow-sm w-full">
                    <option value="">All Members</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="start_date" name="start_date" class="form-input rounded-md shadow-sm w-full" 
                       value="{{ $startDate ?? '' }}">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" id="end_date" name="end_date" class="form-input rounded-md shadow-sm w-full"
                       value="{{ $endDate ?? '' }}">
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-funnel mr-1"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-indigo-50 p-4 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-indigo-700 mb-1">Total Absences</div>
                    <div class="text-2xl font-bold">{{ $absences->total() }}</div>
                </div>
                <div class="bg-indigo-100 p-3 rounded-full">
                    <i class="bi bi-calendar-x text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-red-50 p-4 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-red-700 mb-1">Members with 3+ Sunday Absences</div>
                    @php
                        $usersWithConsecutiveAbsences = \App\Models\User::whereHas('attendances', function($query) {
                            $query->where('status', 'absent');
                        }, '>=', 3)->count();
                    @endphp
                    <div class="text-2xl font-bold">{{ $usersWithConsecutiveAbsences }}</div>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="bi bi-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-yellow-700 mb-1">Notifications Sent</div>
                    @php
                        $notificationsSent = \App\Models\Notification::where('type', 'absence_warning')
                            ->where('is_sent', true)
                            ->count();
                    @endphp
                    <div class="text-2xl font-bold">{{ $notificationsSent }}</div>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="bi bi-bell text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Card View -->
    <div class="block md:hidden space-y-4">
        @forelse ($absences as $absence)
            @php
                $user = $absence->user;
                $event = $absence->event;
                $consecutiveAbsences = 0;
                if ($user) {
                    $consecutiveAbsences = \App\Models\User::find($user->id)
                        ->attendances()
                        ->where('status', 'absent')
                        ->orderBy('created_at', 'desc')
                        ->take(10)
                        ->get()
                        ->takeWhile(function($attendance) {
                            return $attendance->status === 'absent';
                        })
                        ->count();
                }
                $lastAbsenceDate = $absence->created_at ? $absence->created_at->diffInDays(now()) : null;
            @endphp
            <div class="bg-white rounded-lg shadow-md p-4 {{ $consecutiveAbsences >= 5 ? 'border-l-4 border-red-500' : '' }}">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-semibold text-lg text-indigo-700">{{ $user ? $user->name : 'Unknown User' }}</h3>
                    <span class="text-xs px-2 py-1 rounded-full {{ $consecutiveAbsences >= 5 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $consecutiveAbsences }} Sunday absences
                    </span>
                </div>
                <div class="text-sm text-gray-600 mb-1">
                    <span class="font-medium">Email:</span> {{ $user ? $user->email : 'N/A' }}
                </div>
                <div class="text-sm text-gray-600 mb-1">
                    <span class="font-medium">Phone:</span> {{ $user && $user->phone ? $user->phone : 'N/A' }}
                </div>
                <div class="text-sm text-gray-600 mb-1">
                    <span class="font-medium">Event:</span> {{ $event ? $event->name : 'Unknown Event' }}
                </div>
                <div class="text-sm text-gray-600 mb-1">
                    <span class="font-medium">Date:</span> {{ $event ? $event->date : 'Unknown Date' }}
                </div>
                <div class="text-sm text-gray-600 mb-1">
                    <span class="font-medium">Last Absence:</span> {{ $lastAbsenceDate ? $lastAbsenceDate . ' days ago' : 'N/A' }}
                </div>
                <div class="flex justify-end space-x-2 mt-2">
                    <a href="#" class="text-sm px-3 py-1 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200">
                        <i class="bi bi-eye mr-1"></i> View
                    </a>
                    <a href="#" class="text-sm px-3 py-1 bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200 open-notification-modal" data-user-id="{{ $user ? $user->id : '' }}" data-user-name="{{ $user ? $user->name : 'Unknown User' }}">
                        <i class="bi bi-bell mr-1"></i> Notify
                    </a>
                    <form action="{{ route('secretary.absences.update', $absence) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="excused">
                        <button type="submit" class="text-sm px-3 py-1 bg-green-100 text-green-700 rounded-md hover:bg-green-200" onclick="return confirm('Are you sure you want to mark this absence as excused?')">
                            <i class="bi bi-check-circle mr-1"></i> Excuse
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-500">No absences found.</p>
            </div>
        @endforelse
        
        <div class="mt-4 flex justify-center">
            {{ $absences->links() }}
        </div>
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden md:block bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consecutive Sunday Absences</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($absences as $absence)
                        @php
                            $user = $absence->user;
                            $event = $absence->event;
                            $consecutiveAbsences = 0;
                            if ($user) {
                                $consecutiveAbsences = \App\Models\User::find($user->id)
                                    ->attendances()
                                    ->where('status', 'absent')
                                    ->orderBy('created_at', 'desc')
                                    ->take(10)
                                    ->get()
                                    ->takeWhile(function($attendance) {
                                        return $attendance->status === 'absent';
                                    })
                                    ->count();
                            }
                        @endphp
                        <tr class="{{ $consecutiveAbsences >= 5 ? 'bg-red-50' : '' }}">
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $user ? $user->name : 'Unknown User' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $user ? $user->email : 'N/A' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $event ? $event->name : 'Unknown Event' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $event ? $event->date : 'Unknown Date' }}</td>
                            <td class="py-4 px-6 text-sm {{ $consecutiveAbsences >= 5 ? 'font-bold text-red-600' : 'text-yellow-600' }}">
                                {{ $consecutiveAbsences }}
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $absence->remarks ?? 'No remarks' }}</td>
                            <td class="py-4 px-6 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="#" class="text-yellow-600 hover:text-yellow-900 open-notification-modal" data-user-id="{{ $user ? $user->id : '' }}" data-user-name="{{ $user ? $user->name : 'Unknown User' }}" title="Send Reminder">
                                        <i class="bi bi-bell"></i>
                                    </a>
                                    <form action="{{ route('secretary.absences.update', $absence) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="excused">
                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Mark as Excused" onclick="return confirm('Are you sure you want to mark this absence as excused?')">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
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
    
    <!-- Desktop Pagination -->
    <div class="mt-4 hidden md:flex justify-center">
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
                <textarea id="notification-message" name="message" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>We've noticed you've been absent from Sunday masses for consecutive weeks. Each Sunday has 4 masses (please attend at least one of them to be marked as present). Please let us know if you need any assistance or have circumstances preventing your attendance.</textarea>
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

<style>
    .btn-primary {
        @apply inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150;
    }
    
    .form-input, .form-select {
        @apply mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50;
    }
</style>

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

        // Export Report Dropdown
        const exportDropdownButton = document.getElementById('export-dropdown-button');
        const exportDropdown = document.getElementById('export-dropdown');
        
        exportDropdownButton.addEventListener('click', function() {
            exportDropdown.classList.toggle('hidden');
        });
        
        // Close the dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!exportDropdownButton.contains(e.target) && !exportDropdown.contains(e.target)) {
                exportDropdown.classList.add('hidden');
            }
        });
    });
</script>
@endsection

