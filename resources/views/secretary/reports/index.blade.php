@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Attendance Reports</h1>
        <a href="{{ route('secretary.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Dashboard
        </a>
    </div>
    
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-500">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Members</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $usersByRole['Member'] ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Present</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $presentCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Absent</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $absentCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Excused</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ $excusedCount }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Report Types -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Date Range Report</h2>
            <p class="text-gray-600 mb-4">Generate attendance report for a specific date range.</p>
            <form action="{{ route('secretary.reports.by-date-range') }}" method="GET">
                <div class="mb-4">
                    <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ now()->subMonth()->format('Y-m-d') }}">
                </div>
                <div class="mb-4">
                    <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ now()->format('Y-m-d') }}">
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full">
                    Generate Report
                </button>
            </form>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Member Report</h2>
            <p class="text-gray-600 mb-4">Generate attendance report for a specific member.</p>
            <form action="{{ route('secretary.reports.by-member') }}" method="GET">
                <div class="mb-4">
                    <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">Select Member:</label>
                    <select id="user_id" name="user_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="" disabled selected>Select a member</option>
                        @foreach ($members ?? [] as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ now()->subMonth()->format('Y-m-d') }}">
                </div>
                <div class="mb-4">
                    <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">End Date:</label>
                    <input type="date" id="end_date" name="end_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ now()->format('Y-m-d') }}">
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded w-full">
                    Generate Report
                </button>
            </form>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Export Report</h2>
            <p class="text-gray-600 mb-4">Export attendance data to CSV file.</p>
            <form action="{{ route('secretary.reports.export-csv') }}" method="GET">
                <div class="mb-4">
                    <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ now()->subMonth()->format('Y-m-d') }}">
                </div>
                <div class="mb-4">
                    <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ now()->format('Y-m-d') }}">
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full">
                    Export to CSV
                </button>
            </form>
        </div>
    </div>
    
    <!-- Recent Events -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold">Recent Events</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Name</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($recentEvents as $event)
                        <tr>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $event->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $event->date }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $event->time }}</td>
                            <td class="py-4 px-6 text-sm font-medium">
                                <a href="{{ route('secretary.reports.by-date-range', ['start_date' => $event->date, 'end_date' => $event->date]) }}" class="text-indigo-600 hover:text-indigo-900">View Report</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="4">No events found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
