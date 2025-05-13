@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-[#B22234] mb-6">Member Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Attendance Summary Card -->
        <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700]">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-medium text-[#B22234]">Attendance Summary</h2>
                    <p class="text-gray-700 mt-1">Your attendance statistics</p>
                </div>
                <div class="bg-[#FFD700] p-2 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#B22234]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Present:</span>
                    <span class="text-green-600 font-semibold">{{ $presentCount ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-700">Absent:</span>
                    <span class="text-red-600 font-semibold">{{ $absentCount ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-gray-700">Excused:</span>
                    <span class="text-blue-600 font-semibold">{{ $excusedCount ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- QR Code Card -->
        <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700]">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-medium text-[#B22234]">Your QR Code</h2>
                    <p class="text-gray-700 mt-1">Show this for attendance</p>
                </div>
                <div class="bg-[#FFD700] p-2 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#B22234]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                    </svg>
                </div>
            </div>
            <div class="flex justify-center mt-4">
                <div class="border-2 border-[#FFD700] rounded-lg p-2 bg-white">
                    <div class="flex items-center justify-center w-32 h-32 bg-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-[#B22234]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('qrcode.show') }}" class="text-[#B22234] hover:text-[#8B0000]">View Full QR Code</a>
            </div>
        </div>

        <!-- Next Event Card -->
        <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700]">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-medium text-[#B22234]">Next Event</h2>
                    <p class="text-gray-700 mt-1">Upcoming mass</p>
                </div>
                <div class="bg-[#FFD700] p-2 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#B22234]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                    </svg>
                </div>
            </div>
            @if(isset($nextEvent))
                <div class="mt-4">
                    <div class="mt-2">
                        <span class="text-gray-700 font-semibold">{{ $nextEvent->title }}</span>
                    </div>
                    <div class="mt-2">
                        <span class="text-gray-700">Date:</span>
                        <span class="text-[#B22234]">{{ \Carbon\Carbon::parse($nextEvent->date)->format('M d, Y') }}</span>
                    </div>
                    <div class="mt-2">
                        <span class="text-gray-700">Time:</span>
                        <span class="text-[#B22234]">{{ \Carbon\Carbon::parse($nextEvent->time)->format('h:i A') }}</span>
                    </div>
                    <div class="mt-2">
                        <span class="text-gray-700">Location:</span>
                        <span class="text-[#B22234]">{{ $nextEvent->location }}</span>
                    </div>
                </div>
            @else
                <div class="mt-4 flex justify-center items-center h-24">
                    <span class="text-gray-500">No upcoming events</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Attendance History -->
    <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700] mb-8">
        <h2 class="text-xl font-medium text-[#B22234] mb-4">Recent Attendance History</h2>
        @if(isset($recentAttendances) && count($recentAttendances) > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-gray-700">Date</th>
                            <th class="px-4 py-2 text-left text-gray-700">Event</th>
                            <th class="px-4 py-2 text-left text-gray-700">Status</th>
                            <th class="px-4 py-2 text-left text-gray-700">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAttendances as $attendance)
                            <tr class="border-t">
                                <td class="px-4 py-2 text-gray-700">{{ \Carbon\Carbon::parse($attendance->event->date)->format('M d, Y') }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $attendance->event->title }}</td>
                                <td class="px-4 py-2">
                                    @if($attendance->status === 'present')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Present</span>
                                    @elseif($attendance->status === 'absent')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Absent</span>
                                    @else
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Excused</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-gray-700">{{ $attendance->remarks ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-gray-500 py-4 text-center">No attendance records found</div>
        @endif
    </div>

    <!-- Profile Information -->
    <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700]">
        <h2 class="text-xl font-medium text-[#B22234] mb-4">Profile Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-700"><span class="font-semibold">Name:</span> {{ auth()->user()->name }}</p>
                <p class="text-gray-700 mt-2"><span class="font-semibold">Email:</span> {{ auth()->user()->email }}</p>
                <p class="text-gray-700 mt-2"><span class="font-semibold">Phone:</span> {{ auth()->user()->phone ?? 'Not provided' }}</p>
            </div>
            <div>
                <p class="text-gray-700"><span class="font-semibold">Role:</span> {{ auth()->user()->role->name ?? 'Unknown' }}</p>
                <p class="text-gray-700 mt-2"><span class="font-semibold">Address:</span> {{ auth()->user()->address ?? 'Not provided' }}</p>
                <p class="text-gray-700 mt-2"><span class="font-semibold">Gender:</span> {{ ucfirst(auth()->user()->gender) ?? 'Not provided' }}</p>
            </div>
        </div>
        <div class="mt-4 text-center">
            <a href="#" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#B22234] hover:bg-[#8B0000] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FFD700]">
                View Full Profile
            </a>
        </div>
    </div>
</div>
@endsection 