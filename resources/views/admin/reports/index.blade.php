@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Attendance Reports</h1>
    </div>
    
    <!-- Filter Form -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form action="{{ route('admin.reports.index') }}" method="GET">
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
                    <label for="event_id" class="block text-gray-700 text-sm font-bold mb-2">Event:</label>
                    <select name="event_id" id="event_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">All Events</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" {{ $eventId == $event->id ? 'selected' : '' }}>{{ $event->name }} ({{ $event->date }})</option>
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
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700">Total Events</h2>
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_events'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700">Total Users</h2>
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_users'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700">Total Attendances</h2>
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_attendances'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700">Attendance Rate</h2>
            <p class="text-3xl font-bold text-indigo-600">
                @php
                    $presentCount = $stats['attendance_by_status']['present'] ?? 0;
                    $totalCount = $stats['total_attendances'];
                    $attendanceRate = $totalCount > 0 ? round(($presentCount / $totalCount) * 100, 2) : 0;
                @endphp
                {{ $attendanceRate }}%
            </p>
        </div>
    </div>
    
    <!-- Attendance Chart -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Attendance Statistics</h2>
        <div class="h-64">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
    
    <!-- Attendance Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($attendances as $attendance)
                        <tr>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $attendance->user->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->event->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->event->date }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                @if ($attendance->status === 'present')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                                @elseif ($attendance->status === 'absent')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absent</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Excused</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->approved_by ? \App\Models\User::find($attendance->approved_by)->name : 'N/A' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->remarks ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
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
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(attendanceCtx, {
        type: 'pie',
        data: {
            labels: ['Present', 'Absent', 'Excused'],
            datasets: [{
                data: [
                    {{ $stats['attendance_by_status']['present'] ?? 0 }},
                    {{ $stats['attendance_by_status']['absent'] ?? 0 }},
                    {{ $stats['attendance_by_status']['excused'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(255, 206, 86, 0.2)'
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endpush
@endsection
