<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- React Dashboard -->
            <div 
                data-react-root 
                data-component="AdminDashboard"
                data-props='{
                    "totalUsers": {{ $totalUsers }},
                    "totalEvents": {{ $totalEvents }},
                    "totalAttendances": {{ $totalAttendances }},
                    "totalNotifications": {{ $totalNotifications }},
                    "recentUsers": @json($recentUsers),
                    "recentAttendances": @json($recentAttendances)
                }'
            ></div>

            <!-- Fallback HTML Content (displayed if React fails) -->
            <div id="admindashboard-fallback-content" style="display: none;" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Users</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalUsers }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Events</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalEvents }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Attendances</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalAttendances }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Notifications</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $totalNotifications }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
                            <div class="grid grid-cols-1 gap-3">
                                <a href="/admin/users/create" class="flex items-center justify-center px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                    Add New User
                                </a>
                                
                                <a href="/admin/events/create" class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Create New Event
                                </a>
                                
                                <a href="/admin/reports" class="flex items-center justify-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    View Reports
                                </a>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow p-6 mt-6">
                            <h3 class="text-lg font-bold mb-4">Attendance Statistics</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <p class="text-sm font-medium text-green-600">Present</p>
                                    <p class="mt-1 text-3xl font-semibold text-green-800">{{ $recentAttendances->where("status", "present")->count() }}</p>
                                </div>
                                <div class="text-center p-4 bg-red-50 rounded-lg">
                                    <p class="text-sm font-medium text-red-600">Absent</p>
                                    <p class="mt-1 text-3xl font-semibold text-red-800">{{ $recentAttendances->where("status", "absent")->count() }}</p>
                                </div>
                                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                    <p class="text-sm font-medium text-yellow-600">Excused</p>
                                    <p class="mt-1 text-3xl font-semibold text-yellow-800">{{ $recentAttendances->where("status", "excused")->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold">Recent Users</h3>
                                <a href="/admin/users" class="text-sm text-blue-600 hover:text-blue-500">
                                    View All
                                </a>
                            </div>
                            
                            @if(count($recentUsers) > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 table-responsive">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Name
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Email
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Role
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($recentUsers as $user)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap" data-label="Name">
                                                        <div class="flex items-center">
                                                            <div class="flex-shrink-0 h-10 w-10">
                                                                <img class="h-10 w-10 rounded-full object-cover" 
                                                                    src="{{ $user['profile_photo_url'] }}" 
                                                                    alt="{{ $user['name'] }}"
                                                                    onerror="this.onerror=null; this.src='{{ asset('img/kofa.png') }}';">
                                                            </div>
                                                            <div class="ml-4">
                                                                <div class="text-sm font-medium text-gray-900">{{ $user['name'] }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap" data-label="Email">
                                                        <div class="text-sm text-gray-900">{{ $user['email'] }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap" data-label="Role">
                                                        <div class="text-sm text-gray-900">{{ $user['role']['name'] ?? 'No Role' }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap" data-label="Status">
                                                        @if($user['approval_status'] === 'approved')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                Approved
                                                            </span>
                                                        @else
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                                Pending
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <p class="text-gray-500">No users found</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile-photo-fix.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/profile-photo-fix.js') }}"></script>
<script src="{{ asset('js/admin-dashboard-fix.js') }}"></script>
@endpush
