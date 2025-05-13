@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Dashboard Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-red-700 dark:text-red-500 mb-2">Admin Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400">Welcome back, {{ Auth::user()->name }}!</p>
        </div>
        <div class="mt-4 md:mt-0">
            <span class="bg-yellow-400 text-gray-800 text-sm font-medium px-3 py-1.5 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ now()->format('F d, Y') }}
            </span>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card card-hover border-t-4 border-red-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Users</h2>
                <div class="p-2 bg-red-100 dark:bg-red-900 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-700 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-red-700 dark:text-red-500">{{ $totalUsers }}</p>
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">Registered members</div>
        </div>
        
        <div class="card card-hover border-t-4 border-yellow-400">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Events</h2>
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-yellow-500 dark:text-yellow-400">{{ $totalEvents }}</p>
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">Scheduled events</div>
        </div>
        
        <div class="card card-hover border-t-4 border-red-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Attendances</h2>
                <div class="p-2 bg-red-100 dark:bg-red-900 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-700 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-red-700 dark:text-red-500">{{ $totalAttendances }}</p>
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">Recorded attendances</div>
        </div>
        
        <div class="card card-hover border-t-4 border-yellow-400">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Notifications</h2>
                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-yellow-500 dark:text-yellow-400">{{ $totalNotifications }}</p>
            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">Sent notifications</div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Users -->
        <div class="card card-hover border-l-4 border-red-700">
            <div class="flex items-center justify-between mb-6">
                <h2 class="section-title flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Recent Users
                </h2>
                <a href="#" class="text-sm text-red-700 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-300">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 border-b-2 border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-left text-xs font-semibold text-red-700 dark:text-red-400 uppercase tracking-wider">Name</th>
                            <th class="py-3 px-4 border-b-2 border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-left text-xs font-semibold text-red-700 dark:text-red-400 uppercase tracking-wider">Email</th>
                            <th class="py-3 px-4 border-b-2 border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-left text-xs font-semibold text-red-700 dark:text-red-400 uppercase tracking-wider">Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($recentUsers as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-150">
                                <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-gray-200">{{ $user->name }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                                <td class="py-3 px-4">
                                    @if ($user->role->name == 'Admin')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300">Admin</span>
                                    @elseif ($user->role->name == 'Officer')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300">Officer</span>
                                    @elseif ($user->role->name == 'Secretary')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300">Secretary</span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300">Member</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 px-4 text-center text-gray-500 dark:text-gray-400" colspan="3">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Upcoming Events -->
        <div class="card card-hover border-l-4 border-yellow-400">
            <div class="flex items-center justify-between mb-6">
                <h2 class="section-title flex items-center text-yellow-600 dark:text-yellow-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Upcoming Events
                </h2>
                <a href="#" class="text-sm text-yellow-600 hover:text-yellow-700 dark:text-yellow-400 dark:hover:text-yellow-300 transition-colors duration-300">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 border-b-2 border-yellow-100 dark:border-yellow-900 bg-yellow-50 dark:bg-yellow-900/20 text-left text-xs font-semibold text-yellow-700 dark:text-yellow-400 uppercase tracking-wider">Name</th>
                            <th class="py-3 px-4 border-b-2 border-yellow-100 dark:border-yellow-900 bg-yellow-50 dark:bg-yellow-900/20 text-left text-xs font-semibold text-yellow-700 dark:text-yellow-400 uppercase tracking-wider">Date</th>
                            <th class="py-3 px-4 border-b-2 border-yellow-100 dark:border-yellow-900 bg-yellow-50 dark:bg-yellow-900/20 text-left text-xs font-semibold text-yellow-700 dark:text-yellow-400 uppercase tracking-wider">Time</th>
                            <th class="py-3 px-4 border-b-2 border-yellow-100 dark:border-yellow-900 bg-yellow-50 dark:bg-yellow-900/20 text-left text-xs font-semibold text-yellow-700 dark:text-yellow-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($upcomingEvents as $event)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-150">
                                <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-gray-200">{{ $event->name }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $event->date }}</td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $event->time }}</td>
                                <td class="py-3 px-4">
                                    @if ($event->is_active)
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300">Active</span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-300">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 px-4 text-center text-gray-500 dark:text-gray-400" colspan="4">No upcoming events found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Recent Attendances -->
    <div class="card card-hover border-t-4 border-red-700 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="section-title flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Recent Attendances
            </h2>
            <a href="#" class="text-sm text-red-700 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-300">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr>
                        <th class="py-3 px-4 border-b-2 border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-left text-xs font-semibold text-red-700 dark:text-red-400 uppercase tracking-wider">User</th>
                        <th class="py-3 px-4 border-b-2 border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-left text-xs font-semibold text-red-700 dark:text-red-400 uppercase tracking-wider">Event</th>
                        <th class="py-3 px-4 border-b-2 border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-left text-xs font-semibold text-red-700 dark:text-red-400 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-4 border-b-2 border-red-100 dark:border-red-900 bg-red-50 dark:bg-red-900/20 text-left text-xs font-semibold text-red-700 dark:text-red-400 uppercase tracking-wider">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($recentAttendances as $attendance)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors duration-150">
                            <td class="py-3 px-4 text-sm font-medium text-gray-900 dark:text-gray-200">{{ $attendance->user->name }}</td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $attendance->event->name }}</td>
                            <td class="py-3 px-4">
                                @if ($attendance->status == 'present')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300">Present</span>
                                @elseif ($attendance->status == 'absent')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-300">Absent</span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-300">Excused</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">{{ $attendance->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-4 text-center text-gray-500 dark:text-gray-400" colspan="4">No attendance records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card card-hover border-t-4 border-yellow-400 mb-8">
        <h2 class="section-title flex items-center text-yellow-600 dark:text-yellow-400 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Quick Actions
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="#" class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors duration-300">
                <div class="p-3 bg-red-100 dark:bg-red-900/50 rounded-full mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-700 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-red-700 dark:text-red-400">Create Event</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Schedule a new event</p>
                </div>
            </a>
            
            <a href="#" class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg hover:bg-yellow-100 dark:hover:bg-yellow-900/40 transition-colors duration-300">
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/50 rounded-full mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-yellow-600 dark:text-yellow-400">Send Notification</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Notify all members</p>
                </div>
            </a>
            
            <a href="#" class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors duration-300">
                <div class="p-3 bg-red-100 dark:bg-red-900/50 rounded-full mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-700 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-medium text-red-700 dark:text-red-400">Generate Report</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">View attendance stats</p>
                </div>
            </a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Attendance by Status Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Attendance by Status</h2>
            <div class="h-64">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
        
        <!-- Users by Role Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Users by Role</h2>
            <div class="h-64">
                <canvas id="usersChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Attendance by Status Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(attendanceCtx, {
        type: 'pie',
        data: {
            labels: ['Present', 'Absent', 'Excused'],
            datasets: [{
                data: [
                    {{ $attendanceByStatus['present'] ?? 0 }},
                    {{ $attendanceByStatus['absent'] ?? 0 }},
                    {{ $attendanceByStatus['excused'] ?? 0 }}
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
    
    // Users by Role Chart
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    const usersChart = new Chart(usersCtx, {
        type: 'bar',
        data: {
            labels: ['Admin', 'Officer', 'Secretary', 'Member'],
            datasets: [{
                label: 'Users by Role',
                data: [
                    {{ $usersByRole['Admin'] ?? 0 }},
                    {{ $usersByRole['Officer'] ?? 0 }},
                    {{ $usersByRole['Secretary'] ?? 0 }},
                    {{ $usersByRole['Member'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)',
                    'rgba(75, 192, 192, 0.2)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endpush
@endsection
