@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-3xl font-bold mb-4 md:mb-0">Event Attendance Report</h1>
        <a href="{{ route('admin.reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Reports
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-4 md:p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-xl font-semibold mb-4">Event Details</h2>
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Name</p>
                    <p class="text-lg">{{ $event->name }}</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Date</p>
                    <p class="text-lg">{{ $event->date }}</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Time</p>
                    <p class="text-lg">{{ $event->time }}</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Description</p>
                    <p class="text-lg">{{ $event->description ?? 'No description provided.' }}</p>
                </div>
            </div>
            
            <div>
                <h2 class="text-xl font-semibold mb-4">Attendance Summary</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
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
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
    
    <!-- Attendance Chart -->
    <div class="bg-white shadow-md rounded-lg p-4 md:p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Attendance Chart</h2>
        <div class="h-64">
            <canvas id="attendanceChart"></canvas>
        </div>
    </div>
    
    <!-- Attendance List -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 md:p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Attendance List</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white table-responsive">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved At</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($attendances as $attendance)
                        <tr>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900" data-label="Member">{{ $attendance->user->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500" data-label="Status">
                                @if ($attendance->status === 'present')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                                @elseif ($attendance->status === 'absent')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absent</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Excused</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500" data-label="Approved By">{{ $attendance->approved_by ? \App\Models\User::find($attendance->approved_by)->name : 'N/A' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500" data-label="Approved At">{{ $attendance->approved_at ? $attendance->approved_at->format('F d, Y h:i A') : 'N/A' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500" data-label="Remarks">{{ $attendance->remarks ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="5">No attendance records found.</td>
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
