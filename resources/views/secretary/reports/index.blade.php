@extends('layouts.secretary-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-gradient-to-r from-indigo-700 to-blue-500 rounded-lg shadow-lg mb-8 p-6">
        <div class="flex flex-wrap justify-between items-center">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Attendance Reports</h1>
                <p class="text-indigo-100">Generate and download reports for member attendance and absences</p>
            </div>
            <div class="flex space-x-2 mt-4 md:mt-0">
                <a href="{{ route('secretary.reports.export-csv') }}" class="btn-secondary flex items-center">
                    <i class="bi bi-file-earmark-excel mr-2"></i> Export to Excel
                </a>
                <a href="{{ route('secretary.dashboard') }}" class="btn-white flex items-center">
                    <i class="bi bi-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
    
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="bi bi-check-circle text-green-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="bi bi-exclamation-triangle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-indigo-500 hover:shadow-lg transition duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-500">
                    <i class="bi bi-calendar-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase">Total Events</p>
                    <p class="text-xl font-semibold">{{ \App\Models\Event::count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-green-500 hover:shadow-lg transition duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i class="bi bi-people text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase">Average Attendance</p>
                    @php
                        $totalEvents = \App\Models\Event::count();
                        $presentAttendances = \App\Models\Attendance::where('status', 'present')->count();
                        $totalAttendances = \App\Models\Attendance::count();
                        $avgAttendance = $totalAttendances > 0 ? round(($presentAttendances / $totalAttendances) * 100, 1) : 0;
                    @endphp
                    <p class="text-xl font-semibold">{{ $avgAttendance }}%</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-yellow-500 hover:shadow-lg transition duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <i class="bi bi-star text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase">Perfect Attendance</p>
                    @php
                        // Members with no absences in the last 5 events
                        $recentEvents = \App\Models\Event::latest('date')->take(5)->pluck('id');
                        
                        $memberRoleId = \App\Models\Role::where('name', 'Member')->first()->id ?? null;
                        $totalMembers = $memberRoleId ? \App\Models\User::where('role_id', $memberRoleId)->count() : 0;
                        
                        $membersWithPerfectAttendance = 0;
                        
                        if ($memberRoleId && count($recentEvents) > 0) {
                            $membersWithPerfectAttendance = \App\Models\User::where('role_id', $memberRoleId)
                                ->whereDoesntHave('attendances', function($query) use ($recentEvents) {
                                    $query->whereIn('event_id', $recentEvents)
                                          ->where('status', 'absent');
                                })
                                ->count();
                        }
                    @endphp
                    <p class="text-xl font-semibold">{{ $membersWithPerfectAttendance }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-4 border-t-4 border-red-500 hover:shadow-lg transition duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <i class="bi bi-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 uppercase">Absent Members</p>
                    @php
                        // Count members with 3+ absences
                        $absentMembers = \App\Models\User::whereHas('attendances', function($query) {
                            $query->where('status', 'absent');
                        }, '>=', 3)->count();
                    @endphp
                    <p class="text-xl font-semibold">{{ $absentMembers }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
        <div class="flex border-b">
            <button class="tab-btn tab-active px-6 py-3 font-medium text-sm focus:outline-none" data-tab="filter">
                <i class="bi bi-funnel mr-2"></i> Filter Reports
            </button>
            <button class="tab-btn px-6 py-3 font-medium text-sm focus:outline-none" data-tab="events">
                <i class="bi bi-calendar-event mr-2"></i> Event Reports
            </button>
            <button class="tab-btn px-6 py-3 font-medium text-sm focus:outline-none" data-tab="members">
                <i class="bi bi-person mr-2"></i> Member Reports
            </button>
            <button class="tab-btn px-6 py-3 font-medium text-sm focus:outline-none" data-tab="absences">
                <i class="bi bi-exclamation-circle mr-2"></i> Absence Reports
            </button>
        </div>
        
        <!-- Filter Section -->
        <div id="filter-tab" class="tab-content p-6">
            <form action="{{ route('secretary.reports.by-date-range') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-calendar text-gray-400"></i>
                        </div>
                        <input type="date" id="start_date" name="start_date" class="form-input pl-10" value="{{ $startDate ?? \Carbon\Carbon::now()->subMonth()->format('Y-m-d') }}">
                    </div>
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-calendar text-gray-400"></i>
                        </div>
                        <input type="date" id="end_date" name="end_date" class="form-input pl-10" value="{{ $endDate ?? \Carbon\Carbon::now()->format('Y-m-d') }}">
                    </div>
                </div>
                <div>
                    <label for="event_type" class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-tag text-gray-400"></i>
                        </div>
                        <select id="event_type" name="event_type" class="form-select pl-10">
                            <option value="">All Events</option>
                            @foreach (\App\Models\Event::select('type')->distinct()->get()->pluck('type') as $type)
                                <option value="{{ $type }}" {{ request('event_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="md:col-span-3 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-search mr-2"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Event Reports Tab -->
        <div id="events-tab" class="tab-content p-6 hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Present</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Absent</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @php
                            $recentEvents = \App\Models\Event::with(['attendances'])
                                ->latest('date')
                                ->take(10)
                                ->get();
                        @endphp
                        
                        @forelse ($recentEvents as $event)
                            @php
                                $present = $event->attendances->where('status', 'present')->count();
                                $absent = $event->attendances->where('status', 'absent')->count();
                                $excused = $event->attendances->where('status', 'excused')->count();
                                $total = $present + $absent + $excused;
                                $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $event->name }}</td>
                                <td class="py-4 px-6 text-sm text-gray-500">{{ \Carbon\Carbon::parse($event->date)->format('M j, Y') }}</td>
                                <td class="py-4 px-6 text-sm text-gray-500">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $event->type == 'sunday' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ ucfirst($event->type ?? 'Unknown') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm text-green-600 font-medium">{{ $present }}</td>
                                <td class="py-4 px-6 text-sm text-red-600 font-medium">{{ $absent }}</td>
                                <td class="py-4 px-6 text-sm font-medium">
                                    <div class="flex items-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2.5">
                                            <div class="bg-{{ $rate > 80 ? 'green' : ($rate > 60 ? 'yellow' : 'red') }}-500 h-2.5 rounded-full" style="width: {{ $rate }}%"></div>
                                        </div>
                                        <span class="ml-2 {{ $rate > 80 ? 'text-green-600' : ($rate > 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $rate }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('secretary.reports.by-date-range', ['start_date' => $event->date, 'end_date' => $event->date]) }}" class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('secretary.reports.export-csv', ['start_date' => $event->date, 'end_date' => $event->date]) }}" class="text-green-600 hover:text-green-900" title="Export Report">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="7">No events found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Member Reports Tab -->
        <div id="members-tab" class="tab-content p-6 hidden">
            <form action="{{ route('secretary.reports.by-member') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Select Member</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-person text-gray-400"></i>
                        </div>
                        <select id="user_id" name="user_id" required class="form-select pl-10">
                            <option value="" disabled selected>Select a member</option>
                            @foreach ($members ?? [] as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-calendar text-gray-400"></i>
                        </div>
                        <input type="date" id="start_date" name="start_date" class="form-input pl-10" value="{{ \Carbon\Carbon::now()->subMonth()->format('Y-m-d') }}">
                    </div>
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="bi bi-calendar text-gray-400"></i>
                        </div>
                        <input type="date" id="end_date" name="end_date" class="form-input pl-10" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                    </div>
                </div>
                <div class="md:col-span-3 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-search mr-2"></i> Generate Member Report
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Absence Reports Tab -->
        <div id="absences-tab" class="tab-content p-6 hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-lg border border-yellow-200 shadow-md hover:shadow-lg transition duration-300">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-yellow-200 text-yellow-700">
                            <i class="bi bi-exclamation-triangle text-xl"></i>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-yellow-700">3 Consecutive Sunday Absences</h3>
                    </div>
                    
                    <p class="text-gray-600 mb-4">
                        These members need to undergo counseling at the next meeting. The list includes all members who have exactly 3 consecutive Sunday absences.
                    </p>
                    
                    <div class="bg-white bg-opacity-60 p-4 rounded-lg mb-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Report Includes:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li class="flex items-center"><i class="bi bi-check-circle text-green-500 mr-2"></i> Member contact information</li>
                            <li class="flex items-center"><i class="bi bi-check-circle text-green-500 mr-2"></i> Dates of missed Sunday masses</li>
                            <li class="flex items-center"><i class="bi bi-check-circle text-green-500 mr-2"></i> Last notification sent date</li>
                        </ul>
                    </div>
                    
                    <div class="flex justify-end">
                        <a href="{{ route('secretary.reports.export-three-consecutive-absences') }}" class="btn-warning">
                            <i class="bi bi-download mr-2"></i> Download Report
                        </a>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-lg border border-red-200 shadow-md hover:shadow-lg transition duration-300">
                    <div class="flex items-center mb-4">
                        <div class="p-3 rounded-full bg-red-200 text-red-700">
                            <i class="bi bi-exclamation-circle text-xl"></i>
                        </div>
                        <h3 class="ml-4 text-lg font-semibold text-red-700">4+ Consecutive Sunday Absences</h3>
                    </div>
                    
                    <p class="text-gray-600 mb-4">
                        These members require serious counseling and possible home visits. The list includes all members who have 4 or more consecutive Sunday absences.
                    </p>
                    
                    <div class="bg-white bg-opacity-60 p-4 rounded-lg mb-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Report Includes:</h4>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li class="flex items-center"><i class="bi bi-check-circle text-green-500 mr-2"></i> Member contact information</li>
                            <li class="flex items-center"><i class="bi bi-check-circle text-green-500 mr-2"></i> Dates of missed Sunday masses</li>
                            <li class="flex items-center"><i class="bi bi-check-circle text-green-500 mr-2"></i> Last notification sent date</li>
                            <li class="flex items-center"><i class="bi bi-check-circle text-green-500 mr-2"></i> Home address for visits</li>
                        </ul>
                    </div>
                    
                    <div class="flex justify-end">
                        <a href="{{ route('secretary.reports.export-four-plus-consecutive-absences') }}" class="btn-danger">
                            <i class="bi bi-download mr-2"></i> Download Report
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="bi bi-info-circle-fill text-blue-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Attendance Rule</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>A member is only marked as absent for a Sunday if they miss ALL 4 masses on that day. Attending at least one mass on Sunday will count as present for that Sunday.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-primary {
        @apply inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150;
    }
    
    .btn-white {
        @apply inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:border-indigo-300 focus:ring ring-indigo-200 active:bg-gray-100 disabled:opacity-25 transition ease-in-out duration-150;
    }
    
    .btn-warning {
        @apply inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    
    .btn-danger {
        @apply inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    
    .form-input, .form-select {
        @apply mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50;
    }
    
    .tab-btn {
        @apply text-gray-600 hover:text-indigo-600 hover:border-indigo-600;
    }
    
    .tab-active {
        @apply text-indigo-600 border-b-2 border-indigo-600;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                tabButtons.forEach(btn => {
                    btn.classList.remove('tab-active');
                });
                
                // Add active class to clicked button
                button.classList.add('tab-active');
                
                // Hide all tab contents
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Show the selected tab content
                const tabName = button.getAttribute('data-tab');
                document.getElementById(`${tabName}-tab`).classList.remove('hidden');
            });
        });
    });
</script>
@endsection
