@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Monthly Attendance Report</h1>
        <a href="{{ route('secretary.reports.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Reports
        </a>
    </div>
    
    <!-- Filter Form -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form action="{{ route('secretary.reports.monthly') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="year" class="block text-gray-700 text-sm font-bold mb-2">Year:</label>
                    <select name="year" id="year" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @for ($i = date('Y'); $i >= date('Y') - 5; $i--)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label for="month" class="block text-gray-700 text-sm font-bold mb-2">Month:</label>
                    <select name="month" id="month" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Summary Statistics -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Monthly Summary for {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="p-4 bg-green-50 rounded text-center">
                <p class="text-sm font-medium text-gray-500">Total Events</p>
                <p class="text-2xl font-bold text-green-600">{{ $events->count() }}</p>
            </div>
            
            <div class="p-4 bg-blue-50 rounded text-center">
                <p class="text-sm font-medium text-gray-500">Total Members</p>
                <p class="text-2xl font-bold text-blue-600">{{ $members->count() }}</p>
            </div>
            
            <div class="p-4 bg-indigo-50 rounded text-center">
                <p class="text-sm font-medium text-gray-500">Average Attendance</p>
                @php
                    $totalAttendance = 0;
                    $presentCount = 0;
                    
                    foreach ($memberStats as $stat) {
                        $totalAttendance += $stat['total'];
                        $presentCount += $stat['present'];
                    }
                    
                    $averageAttendance = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 2) : 0;
                @endphp
                <p class="text-2xl font-bold text-indigo-600">{{ $averageAttendance }}%</p>
            </div>
            
            <div class="p-4 bg-red-50 rounded text-center">
                <p class="text-sm font-medium text-gray-500">Members with Absences</p>
                @php
                    $membersWithAbsences = 0;
                    
                    foreach ($memberStats as $stat) {
                        if ($stat['absent'] > 0) {
                            $membersWithAbsences++;
                        }
                    }
                @endphp
                <p class="text-2xl font-bold text-red-600">{{ $membersWithAbsences }}</p>
            </div>
        </div>
    </div>
    
    <!-- Events List -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Events This Month</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Name</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Present</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Absent</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Excused</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($events as $event)
                        @php
                            $present = 0;
                            $absent = 0;
                            $excused = 0;
                            
                            foreach ($attendances as $userId => $userAttendances) {
                                if (isset($userAttendances[$event->id])) {
                                    $attendance = $userAttendances[$event->id][0];
                                    if ($attendance['status'] === 'present') {
                                        $present++;
                                    } elseif ($attendance['status'] === 'absent') {
                                        $absent++;
                                    } elseif ($attendance['status'] === 'excused') {
                                        $excused++;
                                    }
                                } else {
                                    $absent++;
                                }
                            }
                            
                            $total = $present + $absent + $excused;
                            $rate = $total > 0 ? round(($present / $total) * 100, 2) : 0;
                        @endphp
                        <tr>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $event->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $event->date }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $event->time }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $present }}</span>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ $absent }}</span>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $excused }}</span>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $rate }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="7">No events found for this month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Member Attendance Report -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Member Attendance Report</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Present</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Absent</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Excused</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Rate</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($members as $member)
                        <tr>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $member->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ $memberStats[$member->id]['present'] }}</span>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ $memberStats[$member->id]['absent'] }}</span>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $memberStats[$member->id]['excused'] }}</span>
                            </td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $memberStats[$member->id]['percentage'] }}%</td>
                            <td class="py-4 px-6 text-sm font-medium">
                                <a href="{{ route('secretary.reports.member', ['user_id' => $member->id]) }}" class="text-indigo-600 hover:text-indigo-900">View Details</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="6">No members found.</td>
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
        // Attendance visualization could be added here if needed
    });
</script>
@endpush
@endsection
