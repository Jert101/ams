@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Manage Attendances</h1>
        <a href="{{ route('officer.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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
        <form action="{{ route('officer.attendances.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="event_id" class="block text-gray-700 text-sm font-bold mb-2">Event:</label>
                    <select name="event_id" id="event_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">All Events</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" {{ $eventId == $event->id ? 'selected' : '' }}>{{ $event->name }} ({{ $event->date }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                    <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">All Statuses</option>
                        <option value="present" {{ $status === 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ $status === 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="excused" {{ $status === 'excused' ? 'selected' : '' }}>Excused</option>
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
    
    <!-- Bulk Actions Form -->
    @if ($eventId)
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Bulk Actions</h2>
            
            <form id="bulk-absent-form" action="{{ route('officer.attendances.mark-absent') }}" method="POST" class="mb-2">
                @csrf
                <input type="hidden" name="event_id" value="{{ $eventId }}">
                <div class="flex items-center space-x-2">
                    <button type="button" id="select-all-absent" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-2 rounded text-sm">
                        Select All Members Without Records
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-sm">
                        Mark Selected as Absent
                    </button>
                </div>
                
                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach (\App\Models\User::whereHas('role', function($query) { $query->where('name', 'Member'); })->get() as $member)
                        @php
                            $hasRecord = $attendances->contains(function ($attendance) use ($member) {
                                return $attendance->user_id === $member->id;
                            });
                        @endphp
                        
                        @if (!$hasRecord)
                            <div class="flex items-center">
                                <input type="checkbox" name="user_ids[]" value="{{ $member->id }}" id="absent-user-{{ $member->id }}" class="absent-checkbox form-checkbox h-4 w-4 text-indigo-600">
                                <label for="absent-user-{{ $member->id }}" class="ml-2 text-sm text-gray-700">{{ $member->name }}</label>
                            </div>
                        @endif
                    @endforeach
                </div>
            </form>
            
            <form id="bulk-present-form" action="{{ route('officer.attendances.mark-present') }}" method="POST">
                @csrf
                <input type="hidden" name="event_id" value="{{ $eventId }}">
                <div class="flex items-center space-x-2">
                    <button type="button" id="select-all-present" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1 px-2 rounded text-sm">
                        Select All Members Without Records
                    </button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-1 px-2 rounded text-sm">
                        Mark Selected as Present
                    </button>
                </div>
                
                <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach (\App\Models\User::whereHas('role', function($query) { $query->where('name', 'Member'); })->get() as $member)
                        @php
                            $hasRecord = $attendances->contains(function ($attendance) use ($member) {
                                return $attendance->user_id === $member->id;
                            });
                        @endphp
                        
                        @if (!$hasRecord)
                            <div class="flex items-center">
                                <input type="checkbox" name="user_ids[]" value="{{ $member->id }}" id="present-user-{{ $member->id }}" class="present-checkbox form-checkbox h-4 w-4 text-indigo-600">
                                <label for="present-user-{{ $member->id }}" class="ml-2 text-sm text-gray-700">{{ $member->name }}</label>
                            </div>
                        @endif
                    @endforeach
                </div>
            </form>
        </div>
    @endif
    
    <!-- Attendance Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table id="attendance-table" class="min-w-full bg-white" data-event-id="{{ $eventId ?? '' }}">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($attendances as $attendance)
                        <tr data-attendance-id="{{ $attendance->id }}">
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $attendance->user->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->event->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->event->date }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500 attendance-status">
                                @if ($attendance->status === 'present')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 status-present">Present</span>
                                @elseif ($attendance->status === 'absent')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 status-absent">Absent</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 status-excused">Excused</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500 attendance-timestamp">{{ $attendance->approved_at ? $attendance->approved_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500 attendance-approver">{{ $attendance->approved_by ? \App\Models\User::find($attendance->approved_by)->name : 'N/A' }}</td>
                            <td class="py-4 px-6 text-sm font-medium">
                                <a href="{{ route('officer.attendances.edit', $attendance) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr data-attendance-id="{{ $attendance->id }}">
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="6">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $attendances->links() }}
    </div>
    
    <!-- Firebase sync UI -->
    <div id="firebase-sync-container" class="mt-6 bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Firebase Sync</h2>
        <button id="firebase-sync-button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-sync"></i> Sync with Firebase
        </button>
        <span id="firebase-sync-status" class="ml-2"></span>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select all checkboxes for absent form
        const selectAllAbsent = document.getElementById('select-all-absent');
        const absentCheckboxes = document.querySelectorAll('.absent-checkbox');
        
        if (selectAllAbsent) {
            selectAllAbsent.addEventListener('click', function() {
                const isChecked = absentCheckboxes[0] && absentCheckboxes[0].checked;
                absentCheckboxes.forEach(checkbox => {
                    checkbox.checked = !isChecked;
                });
            });
        }
        
        // Select all checkboxes for present form
        const selectAllPresent = document.getElementById('select-all-present');
        const presentCheckboxes = document.querySelectorAll('.present-checkbox');
        
        if (selectAllPresent) {
            selectAllPresent.addEventListener('click', function() {
                const isChecked = presentCheckboxes[0] && presentCheckboxes[0].checked;
                presentCheckboxes.forEach(checkbox => {
                    checkbox.checked = !isChecked;
                });
            });
        }
    });
</script>
@endpush
@endsection
