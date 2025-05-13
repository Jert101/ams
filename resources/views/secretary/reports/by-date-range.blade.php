@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Date Range Report</h1>
        <div class="flex space-x-2">
            <a href="{{ route('secretary.reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Reports
            </a>
            <a href="{{ route('secretary.reports.export-csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Export CSV
            </a>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex flex-wrap items-center mb-4">
            <div class="mr-8">
                <h2 class="text-xl font-semibold">Date Range:</h2>
                <p class="text-gray-600">{{ date('M d, Y', strtotime($startDate)) }} - {{ date('M d, Y', strtotime($endDate)) }}</p>
            </div>
            <div>
                <form action="{{ route('secretary.reports.by-date-range') }}" method="GET" class="flex items-end space-x-2">
                    <div>
                        <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Start Date:</label>
                        <input type="date" id="start_date" name="start_date" required value="{{ $startDate }}" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div>
                        <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">End Date:</label>
                        <input type="date" id="end_date" name="end_date" required value="{{ $endDate }}" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Update
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Attendance Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Present</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $attendanceStats['present'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Absent</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $attendanceStats['absent'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Excused</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $attendanceStats['excused'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Events Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Events in Date Range</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Name</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Attendances</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Present</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($events as $event)
                        <tr>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $event->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $event->date }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $eventAttendanceRates[$event->id]['total'] }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $eventAttendanceRates[$event->id]['present'] }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $eventAttendanceRates[$event->id]['rate'] }}%"></div>
                                    </div>
                                    <span class="ml-2">{{ $eventAttendanceRates[$event->id]['rate'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="5">No events found in this date range.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Attendance Chart -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Attendance Distribution</h2>
        <div class="h-64">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'pie',
            data: {
                labels: ['Present', 'Absent', 'Excused'],
                datasets: [{
                    data: [
                        {{ $attendanceStats['present'] ?? 0 }},
                        {{ $attendanceStats['absent'] ?? 0 }},
                        {{ $attendanceStats['excused'] ?? 0 }}
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
    });
</script>
@endpush
@endsection 