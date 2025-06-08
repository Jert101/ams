@extends('layouts.admin-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold text-[#B22234] mb-4 md:mb-6">Officer Dashboard</h1>
    
    <!-- React Dashboard -->
    <div 
        data-react-root 
        data-component="OfficerDashboard"
        data-props='{
            "totalEvents": {{ $totalEvents ?? 0 }},
            "totalAttendances": {{ $totalAttendances ?? 0 }},
            "todayAttendanceCount": {{ $todayAttendanceCount ?? 0 }},
            "pendingVerificationsCount": {{ $pendingVerificationsCount ?? 0 }},
            "upcomingEvents": @json($upcomingEvents ?? []),
            "todayEvent": @json($todayEvent ?? null),
            "recentAttendances": @json($recentAttendances ?? [])
        }'
    ></div>
    
    <!-- Fallback HTML Content (displayed if React fails) -->
    <div id="officerdashboard-fallback-content" style="display: none;" class="space-y-4 md:space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Events</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalEvents ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Attendances</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalAttendances ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Present Today</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $todayAttendanceCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-4 md:p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Verifications</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $pendingVerificationsCount ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
            <div>
                <div class="bg-white rounded-lg shadow p-4 md:p-6">
                    <h3 class="text-lg font-bold mb-3 md:mb-4">Quick Actions</h3>
                    <div class="button-container">
                        <a href="/officer/scan" class="flex items-center justify-center px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            Scan QR Code
                        </a>
                        
                        <a href="/officer/attendances/pending" class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Verify Attendances
                        </a>
                        
                        <a href="/officer/events/create" class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Create New Event
                        </a>
                    </div>
                </div>
                
                @if(isset($upcomingEvents) && count($upcomingEvents) > 0)
                <div class="bg-white rounded-lg shadow p-4 md:p-6 mt-4 md:mt-6">
                    <div class="flex justify-between items-center mb-3 md:mb-4">
                        <h3 class="text-lg font-bold">Upcoming Events</h3>
                        <a href="/officer/events" class="text-sm text-blue-600 hover:text-blue-900">
                            View All
                        </a>
                    </div>
                    <div class="space-y-3 md:space-y-4">
                        @foreach($upcomingEvents as $event)
                        <div class="border border-gray-200 rounded-lg p-3 md:p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="font-medium text-gray-900">{{ $event['name'] }}</h3>
                                    <p class="text-sm text-gray-500">{{ date('F j, Y', strtotime($event['date'])) }}</p>
                                    @if(isset($event['location']))
                                    <p class="text-sm text-gray-500 mt-1">{{ $event['location'] }}</p>
                                    @endif
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $event['is_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $event['is_active'] ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div class="mt-3 flex justify-end">
                                <a href="/officer/events/{{ $event['id'] }}" class="text-sm text-blue-600 hover:text-blue-900">
                                    View Details
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            
            <div class="lg:col-span-2">
                @if(isset($todayEvent) && $todayEvent)
                <div class="bg-white rounded-lg shadow p-4 md:p-6">
                    <h3 class="text-lg font-bold mb-3 md:mb-4">Current Event</h3>
                    <div class="space-y-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $todayEvent['name'] }}</h3>
                            <p class="text-sm text-gray-500">{{ $todayEvent['description'] ?? '' }}</p>
                        </div>
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-gray-700">{{ date('F j, Y', strtotime($todayEvent['date'])) }}</span>
                        </div>
                        @if(isset($todayEvent['location']))
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-gray-700">{{ $todayEvent['location'] }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-4 md:mt-6 grid grid-cols-3 gap-2 md:gap-4 text-center">
                        <div class="p-2 md:p-3 bg-green-50 rounded-lg">
                            <p class="text-xl md:text-3xl font-bold text-green-600">{{ $todayEvent['attendanceStats']['present'] ?? 0 }}</p>
                            <p class="text-green-700 font-medium text-sm md:text-base">Present</p>
                        </div>
                        <div class="p-2 md:p-3 bg-red-50 rounded-lg">
                            <p class="text-xl md:text-3xl font-bold text-red-600">{{ $todayEvent['attendanceStats']['absent'] ?? 0 }}</p>
                            <p class="text-red-700 font-medium text-sm md:text-base">Absent</p>
                        </div>
                        <div class="p-2 md:p-3 bg-yellow-50 rounded-lg">
                            <p class="text-xl md:text-3xl font-bold text-yellow-600">{{ $todayEvent['attendanceStats']['excused'] ?? 0 }}</p>
                            <p class="text-yellow-700 font-medium text-sm md:text-base">Excused</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-center">
                        <a href="/officer/events/{{ $todayEvent['id'] }}/attendances" class="px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">
                            View Attendances
                        </a>
                    </div>
                </div>
                @endif
                
                <div class="bg-white rounded-lg shadow p-4 md:p-6 {{ isset($todayEvent) && $todayEvent ? 'mt-4 md:mt-6' : '' }}">
                    <div class="flex justify-between items-center mb-3 md:mb-4">
                        <h3 class="text-lg font-bold">Recent Attendances</h3>
                        <a href="/officer/attendances" class="text-sm text-blue-600 hover:text-blue-900">
                            View All
                        </a>
                    </div>
                    @if(isset($recentAttendances) && count($recentAttendances) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse table-mobile-friendly">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-2 text-left text-gray-700">Member</th>
                                    <th class="px-4 py-2 text-left text-gray-700">Event</th>
                                    <th class="px-4 py-2 text-left text-gray-700">Date</th>
                                    <th class="px-4 py-2 text-left text-gray-700">Status</th>
                                    <th class="px-4 py-2 text-left text-gray-700">Verified</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttendances as $attendance)
                                <tr class="border-t">
                                    <td class="px-4 py-2 text-gray-700" data-label="Member">{{ $attendance['member_name'] }}</td>
                                    <td class="px-4 py-2 text-gray-700" data-label="Event">{{ $attendance['event_name'] }}</td>
                                    <td class="px-4 py-2 text-gray-700" data-label="Date">{{ date('M j, Y', strtotime($attendance['date'])) }}</td>
                                    <td class="px-4 py-2" data-label="Status">
                                        @if($attendance['status'] === 'present')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Present</span>
                                        @elseif($attendance['status'] === 'absent')
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Absent</span>
                                        @elseif($attendance['status'] === 'excused')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Excused</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-center" data-label="Verified">
                                        @if($attendance['verified'])
                                        <svg class="h-5 w-5 text-green-500 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                        @else
                                        <svg class="h-5 w-5 text-yellow-500 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-gray-500 py-4 text-center">No recent attendances</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript to show fallback content if React fails to load -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const reactRoot = document.querySelector('[data-react-root]');
        if (reactRoot && !reactRoot.hasChildNodes()) {
            document.getElementById('officerdashboard-fallback-content').style.display = 'block';
        }
    }, 1000); // Give React 1 second to load
});
</script>
@endsection
