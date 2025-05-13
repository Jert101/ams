@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Member Attendance Report</h1>
        <a href="{{ route('secretary.reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Reports
        </a>
    </div>
    
    <!-- Filter Form -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form action="{{ route('secretary.reports.member') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">Member:</label>
                    <select name="user_id" id="user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Select a member</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $user->id == $member->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Start Date:</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                
                <div>
                    <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">End Date:</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Member Information -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-4">Member Information</h2>
                <div class="grid grid-cols-2 gap-2">
                    <div class="text-gray-600 font-medium">Name:</div>
                    <div>{{ $member->name }}</div>
                    
                    <div class="text-gray-600 font-medium">Email:</div>
                    <div>{{ $member->email }}</div>
                    
                    <div class="text-gray-600 font-medium">Phone:</div>
                    <div>{{ $member->phone ?? 'N/A' }}</div>
                </div>
            </div>
            
            <div>
                <h2 class="text-xl font-semibold mb-4">Attendance Summary</h2>
                <div class="grid grid-cols-2 gap-2">
                    <div class="text-gray-600 font-medium">Total Events:</div>
                    <div>{{ $stats['total_events'] }}</div>
                    
                    <div class="text-gray-600 font-medium">Present:</div>
                    <div>{{ $stats['status_counts']['present'] ?? 0 }}</div>
                    
                    <div class="text-gray-600 font-medium">Absent:</div>
                    <div>{{ $stats['status_counts']['absent'] ?? 0 }}</div>
                    
                    <div class="text-gray-600 font-medium">Excused:</div>
                    <div>{{ $stats['status_counts']['excused'] ?? 0 }}</div>
                    
                    <div class="text-gray-600 font-medium">Attendance Rate:</div>
                    <div>{{ $stats['attendance_rate'] }}%</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Attendance Chart -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Attendance Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="h-64">
                <canvas id="attendanceChart"></canvas>
            </div>
            <div class="h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Attendance Details -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Attendance Records</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($attendances as $attendance)
                        <tr>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $attendance->event->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->event->date }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->event->time }}</td>
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
                            <td class="py-4 px-6 text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if ($attendance->status === 'absent')
                                        <form action="{{ route('secretary.absences.update', $attendance->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="excused">
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-900" onclick="return confirm('Are you sure you want to mark this absence as excused?')">
                                                Mark as Excused
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="7">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attendance Pie Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'pie',
            data: {
                labels: ['Present', 'Absent', 'Excused'],
                datasets: [{
                    data: [
                        {{ $stats['status_counts']['present'] ?? 0 }},
                        {{ $stats['status_counts']['absent'] ?? 0 }},
                        {{ $stats['status_counts']['excused'] ?? 0 }}
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
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Attendance Status Distribution'
                    }
                }
            }
        });
        
        // Monthly Attendance Line Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($stats['monthly_labels']) !!},
                datasets: [{
                    label: 'Attendance Rate (%)',
                    data: {!! json_encode($stats['monthly_rates']) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Monthly Attendance Rate'
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
