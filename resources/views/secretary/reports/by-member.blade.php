@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Member Attendance Report</h1>
        <a href="{{ route('secretary.reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Reports
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form action="{{ route('secretary.reports.by-member') }}" method="GET" class="flex flex-wrap items-end space-x-2">
            <div class="mb-4 mr-4">
                <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">Select Member:</label>
                <select id="user_id" name="user_id" required class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline min-w-[200px]">
                    <option value="" disabled {{ !$userId ? 'selected' : '' }}>Select a member</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" {{ $userId == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4 mr-4">
                <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4 mr-4">
                <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            <div class="mb-4">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Generate Report
                </button>
            </div>
        </form>
    </div>
    
    @if($memberData)
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="flex flex-wrap mb-6">
                <div class="w-full md:w-1/2 mb-4 md:mb-0">
                    <h2 class="text-xl font-semibold mb-2">{{ $memberData['member']->name }}</h2>
                    <p class="text-gray-600">{{ $memberData['member']->email }}</p>
                    @if($memberData['member']->phone)
                        <p class="text-gray-600">{{ $memberData['member']->phone }}</p>
                    @endif
                </div>
                <div class="w-full md:w-1/2">
                    <div class="bg-gray-100 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Attendance Summary</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-600">Total Events:</p>
                                <p class="text-xl font-semibold">{{ $memberData['stats']['total_events'] }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Attendance Rate:</p>
                                <p class="text-xl font-semibold">{{ $memberData['stats']['attendance_rate'] }}%</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Present:</p>
                                <p class="text-green-600 font-semibold">{{ $memberData['stats']['attended_events'] }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Absent:</p>
                                <p class="text-red-600 font-semibold">{{ $memberData['stats']['absent_events'] }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Excused:</p>
                                <p class="text-yellow-600 font-semibold">{{ $memberData['stats']['excused_events'] }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Not Recorded:</p>
                                <p class="text-gray-600 font-semibold">{{ $memberData['stats']['missed_events'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Attendance Progress Bar -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Attendance Rate</h3>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-green-600 h-4 rounded-full" style="width: {{ $memberData['stats']['attendance_rate'] }}%"></div>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-sm text-gray-600">0%</span>
                    <span class="text-sm text-gray-600">50%</span>
                    <span class="text-sm text-gray-600">100%</span>
                </div>
            </div>
            
            <!-- Attendance Chart -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Attendance Distribution</h3>
                <div class="h-64">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
            
            <!-- Event Attendance Table -->
            <div>
                <h3 class="text-lg font-semibold mb-2">Event Attendance Details</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Name</th>
                                <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($memberData['events'] as $event)
                                <tr>
                                    <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $event['name'] }}</td>
                                    <td class="py-4 px-6 text-sm text-gray-500">{{ $event['date'] }}</td>
                                    <td class="py-4 px-6 text-sm">
                                        @if($event['status'] === 'Present')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                                        @elseif($event['status'] === 'Absent')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absent</span>
                                        @elseif($event['status'] === 'Excused')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Excused</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Not Recorded</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="3">No events found in this date range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif($userId)
        <div class="bg-white shadow-md rounded-lg p-6 mb-6 text-center">
            <p class="text-gray-600">No data found for the selected member in the specified date range.</p>
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg p-6 mb-6 text-center">
            <p class="text-gray-600">Please select a member to view their attendance report.</p>
        </div>
    @endif
</div>

@push('scripts')
@if($memberData)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'pie',
            data: {
                labels: ['Present', 'Absent', 'Excused', 'Not Recorded'],
                datasets: [{
                    data: [
                        {{ $memberData['stats']['attended_events'] }},
                        {{ $memberData['stats']['absent_events'] }},
                        {{ $memberData['stats']['excused_events'] }},
                        {{ $memberData['stats']['missed_events'] }}
                    ],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(201, 203, 207, 1)'
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
@endif
@endpush
@endsection 