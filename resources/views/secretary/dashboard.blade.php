@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-[#B22234] mb-6">Secretary Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Attendance Summary Card -->
        <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700]">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-medium text-[#B22234]">Attendance Summary</h2>
                    <p class="text-gray-700 mt-1">Overall statistics</p>
                </div>
                <div class="bg-[#FFD700] p-2 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#B22234]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Total Members:</span>
                    <span class="text-[#B22234] font-semibold">{{ $totalMembers ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-700">Total Events:</span>
                    <span class="text-[#B22234] font-semibold">{{ $totalEvents ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-700">Avg. Attendance:</span>
                    <span class="text-green-600 font-semibold">{{ $averageAttendance ?? '0%' }}</span>
                </div>
            </div>
        </div>

        <!-- Consecutive Absences Card -->
        <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700]">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-medium text-[#B22234]">Consecutive Absences</h2>
                    <p class="text-gray-700 mt-1">Members needing attention</p>
                </div>
                <div class="bg-[#FFD700] p-2 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#B22234]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">3 Consecutive:</span>
                    <span class="text-yellow-600 font-semibold">{{ $threeConsecutiveAbsences ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-700">4+ Consecutive:</span>
                    <span class="text-red-600 font-semibold">{{ $fourPlusConsecutiveAbsences ?? 0 }}</span>
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('secretary.absences.index') }}" class="text-[#B22234] hover:text-[#8B0000]">View Details</a>
                </div>
            </div>
        </div>

        <!-- Recent Notifications Card -->
        <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700]">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-medium text-[#B22234]">Recent Notifications</h2>
                    <p class="text-gray-700 mt-1">Sent to members</p>
                </div>
                <div class="bg-[#FFD700] p-2 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#B22234]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Last 7 Days:</span>
                    <span class="text-[#B22234] font-semibold">{{ $recentNotifications ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-700">Total Sent:</span>
                    <span class="text-[#B22234] font-semibold">{{ $totalNotifications ?? 0 }}</span>
                </div>
                <div class="mt-4 text-center">
                    <a href="{{ route('secretary.notifications.index') }}" class="text-[#B22234] hover:text-[#8B0000]">View All</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700] mb-8">
        <h2 class="text-xl font-medium text-[#B22234] mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('secretary.reports.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#B22234] hover:bg-[#8B0000] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FFD700]">
                Generate Reports
            </a>
            <a href="{{ route('secretary.absences.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#B22234] hover:bg-[#8B0000] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FFD700]">
                Manage Absences
            </a>
            <a href="{{ route('secretary.notifications.create') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#B22234] hover:bg-[#8B0000] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FFD700]">
                Send Notifications
            </a>
            <a href="{{ route('secretary.members.index') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#B22234] hover:bg-[#8B0000] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FFD700]">
                View Members
            </a>
        </div>
    </div>
    
    <!-- Recent Attendance -->
    <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700] mb-8">
        <h2 class="text-xl font-medium text-[#B22234] mb-4">Recent Attendance</h2>
        @if(isset($recentEvents) && count($recentEvents) > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-gray-700">Date</th>
                            <th class="px-4 py-2 text-left text-gray-700">Event</th>
                            <th class="px-4 py-2 text-left text-gray-700">Present</th>
                            <th class="px-4 py-2 text-left text-gray-700">Absent</th>
                            <th class="px-4 py-2 text-left text-gray-700">Excused</th>
                            <th class="px-4 py-2 text-left text-gray-700">Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentEvents as $event)
                            <tr class="border-t">
                                <td class="px-4 py-2 text-gray-700">{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $event->title }}</td>
                                <td class="px-4 py-2 text-green-600">{{ $event->present_count ?? 0 }}</td>
                                <td class="px-4 py-2 text-red-600">{{ $event->absent_count ?? 0 }}</td>
                                <td class="px-4 py-2 text-blue-600">{{ $event->excused_count ?? 0 }}</td>
                                <td class="px-4 py-2 text-[#B22234] font-medium">{{ $event->attendance_rate ?? '0%' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-gray-500 py-4 text-center">No recent events</div>
        @endif
    </div>
    
    <!-- Members with Consecutive Absences -->
    <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700]">
        <h2 class="text-xl font-medium text-[#B22234] mb-4">Members with Consecutive Absences</h2>
        @if(isset($membersWithConsecutiveAbsences) && count($membersWithConsecutiveAbsences) > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-gray-700">Name</th>
                            <th class="px-4 py-2 text-left text-gray-700">Email</th>
                            <th class="px-4 py-2 text-left text-gray-700">Phone</th>
                            <th class="px-4 py-2 text-left text-gray-700">Consecutive Absences</th>
                            <th class="px-4 py-2 text-left text-gray-700">Last Notification</th>
                            <th class="px-4 py-2 text-left text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($membersWithConsecutiveAbsences as $member)
                            <tr class="border-t">
                                <td class="px-4 py-2 text-gray-700">{{ $member->name }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $member->email }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $member->phone ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    @if($member->consecutive_absences >= 4)
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">{{ $member->consecutive_absences }}</span>
                                    @else
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">{{ $member->consecutive_absences }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-gray-700">{{ $member->last_notification_sent_at ? \Carbon\Carbon::parse($member->last_notification_sent_at)->format('M d, Y') : 'Never' }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('secretary.notifications.create', ['user_id' => $member->id]) }}" class="text-[#B22234] hover:text-[#8B0000]">Send Notification</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-gray-500 py-4 text-center">No members with consecutive absences</div>
        @endif
    </div>
</div>
@endsection
