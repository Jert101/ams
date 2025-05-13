@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">User Attendance Report</h1>
        <a href="{{ route('admin.reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Reports
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-4">User Details</h2>
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Name</p>
                    <p class="text-lg">{{ $user->name }}</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Email</p>
                    <p class="text-lg">{{ $user->email }}</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Role</p>
                    <p class="text-lg">{{ $user->role->name }}</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Phone</p>
                    <p class="text-lg">{{ $user->phone ?? 'Not provided' }}</p>
                </div>
            </div>
            
            <div>
                <h2 class="text-xl font-semibold mb-4">Attendance Summary</h2>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-green-50 p-4 rounded text-center">
                        <p class="text-sm font-medium text-gray-500">Present</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['present'] }}</p>
                        <p class="text-sm text-gray-500">{{ $stats['present_percentage'] }}% of total</p>
                    </div>
                    
                    <div class="bg-red-50 p-4 rounded text-center">
                        <p class="text-sm font-medium text-gray-500">Absent</p>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['absent'] }}</p>
                        <p class="text-sm text-gray-500">{{ $stats['total'] > 0 ? round(($stats['absent'] / $stats['total']) * 100, 2) : 0 }}% of total</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-yellow-50 p-4 rounded text-center">
                        <p class="text-sm font-medium text-gray-500">Excused</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['excused'] }}</p>
                        <p class="text-sm text-gray-500">{{ $stats['total'] > 0 ? round(($stats['excused'] / $stats['total']) * 100, 2) : 0 }}% of total</p>
                    </div>
                    
                    <div class="bg-blue-50 p-4 rounded text-center">
                        <p class="text-sm font-medium text-gray-500">Total</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</p>
                        <p class="text-sm text-gray-500">100%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filter Form -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Filter Attendance</h2>
        <form action="{{ route('admin.reports.user') }}" method="GET">
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Attendance Chart -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Attendance Chart</h2>
        <div class="h-64">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
    
    <!-- Attendance List -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Attendance History</h2>
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
                    {{ $stats['present'] }},
                    {{ $stats['absent'] }},
                    {{ $stats['excused'] }}
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
