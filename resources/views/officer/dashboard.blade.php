@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-[#B22234] mb-6">Officer Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Scan QR Code Card -->
        <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700]">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-medium text-[#B22234]">Scan QR Code</h2>
                    <p class="text-gray-700 mt-1">Mark attendance</p>
                </div>
                <div class="bg-[#FFD700] p-2 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#B22234]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 text-center">
                <a href="{{ route('officer.scan') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#B22234] hover:bg-[#8B0000] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FFD700]">
                    Open Scanner
                </a>
            </div>
        </div>

        <!-- Today's Event Card -->
        <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700]">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-medium text-[#B22234]">Today's Event</h2>
                    <p class="text-gray-700 mt-1">Current mass</p>
                </div>
                <div class="bg-[#FFD700] p-2 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-[#B22234]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                </div>
            </div>
            @if(isset($todayEvent))
                <div class="mt-4">
                    <div class="mt-2">
                        <span class="text-gray-700 font-semibold">{{ $todayEvent->title }}</span>
                    </div>
                    
                    <!-- Attendance Stats -->
                    <div class="card card-hover border-l-4 border-yellow-400">
                        <div class="flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">Attendance Stats</h2>
                        </div>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $presentCount }}</p>
                                <p class="text-green-700 dark:text-green-300 font-medium">Present</p>
                            </div>
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $absentCount }}</p>
                                <p class="text-red-700 dark:text-red-300 font-medium">Absent</p>
                            </div>
                            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $excusedCount }}</p>
                                <p class="text-yellow-700 dark:text-yellow-300 font-medium">Excused</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="card card-hover border-l-4 border-red-700">
                        <div class="flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-700 dark:text-red-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <h2 class="text-lg font-semibold text-red-700 dark:text-red-400">Quick Actions</h2>
                        </div>
                        <div class="space-y-3">
                            <a href="#" class="block w-full py-2 px-4 bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 text-white font-medium rounded-md text-center transition-colors duration-300 shadow-sm">
                                <div class="flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Record Attendance
                                </div>
                            </a>
                            <a href="#" class="block w-full py-2 px-4 bg-yellow-500 hover:bg-yellow-600 dark:bg-yellow-600 dark:hover:bg-yellow-500 text-white font-medium rounded-md text-center transition-colors duration-300 shadow-sm">
                                <div class="flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    Send Notification
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Attendance -->
                <div class="card card-hover border-t-4 border-red-700 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="section-title flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Recent Attendance
                        </h2>
                        <a href="#" class="text-sm text-red-700 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-300">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="py-3 px-4 border-b-2 border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-left text-xs font-semibold text-red-700 dark:text-red-400 uppercase tracking-wider">Member</th>
                                    <th class="py-3 px-4 border-b-2 border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-left text-xs font-semibold text-red-700 dark:text-red-400 uppercase tracking-wider">Event</th>
                                    <th class="py-3 px-4 border-b-2 border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-left text-xs font-semibold text-red-700 dark:text-red-400 uppercase tracking-wider">Date</th>
                                    <th class="py-3 px-4 border-b-2 border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-left text-xs font-semibold text-red-700 dark:text-red-400 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 border-b-2 border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-left text-xs font-semibold text-red-700 dark:text-red-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($recentAttendances as $attendance)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-150">
                                        <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-gray-200">{{ $attendance->user->name }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $attendance->event->name }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $attendance->event->date }}</td>
                                        <td class="py-3 px-4">
                                            @if ($attendance->status == 'present')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300">Present</span>
                                            @elseif ($attendance->status == 'absent')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300">Absent</span>
                                            @else
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300">Excused</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-sm">
                                            <div class="flex space-x-2">
                                                <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-4 px-4 text-center text-gray-500 dark:text-gray-400" colspan="5">No recent attendance records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Upcoming Events -->
                <div class="card card-hover border-t-4 border-yellow-400 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="section-title flex items-center text-yellow-600 dark:text-yellow-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            Upcoming Events
                        </h2>
                        <a href="#" class="text-sm text-yellow-600 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300 transition-colors duration-300">View All</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse (array_slice($upcomingEvents ?? [], 0, 3) as $event)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition-shadow duration-300">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $event->name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $event->date }} at {{ $event->time }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $event->is_active ? 'bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300' }}">
                                        {{ $event->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <a href="#" class="text-sm text-red-700 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-300">Manage Event</a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-6 text-gray-500 dark:text-gray-400">
                                <p>No upcoming events found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
                            <th class="px-4 py-2 text-left text-gray-700">Event</th>
                            <th class="px-4 py-2 text-left text-gray-700">Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingEvents as $event)
                            <tr class="border-t">
                                <td class="px-4 py-2 text-gray-700">{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ \Carbon\Carbon::parse($event->time)->format('h:i A') }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $event->title }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $event->location }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-gray-500 py-4 text-center">No upcoming events</div>
        @endif
    </div>
</div>
@endsection
