@extends('layouts.officer-app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-cyan-700">Event Details</h1>
        <div class="flex space-x-3">
            <a href="{{ route('officer.events.edit', $event->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 px-5 rounded-lg shadow-md transition duration-150 ease-in-out flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span class="text-base">Edit Event</span>
            </a>
            <a href="{{ route('officer.events.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-5 rounded-lg shadow-md transition duration-150 ease-in-out flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span class="text-base">Back to Events</span>
            </a>
        </div>
    </div>
    
    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-cyan-100 mb-6">
        <div class="bg-cyan-50 px-6 py-4 border-b border-cyan-100">
            <h2 class="text-xl font-semibold text-cyan-700">Event Information</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Event Name</h3>
                    <p class="mt-1 text-lg font-bold text-gray-900">{{ $event->name }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <p class="mt-1">
                        @if($event->is_active)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">Inactive</span>
                        @endif
                    </p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Date</h3>
                    <p class="mt-1 text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($event->date)->format('F j, Y') }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Time</h3>
                    <p class="mt-1 text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($event->time)->format('g:i A') }}</p>
                </div>
                
                <div class="md:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500">Description</h3>
                    <p class="mt-1 text-base font-medium text-gray-900">{{ $event->description ?? 'No description available' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-cyan-100">
        <div class="bg-cyan-50 px-6 py-4 border-b border-cyan-100 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-cyan-700">Attendance Records</h2>
            <span class="bg-cyan-100 text-cyan-800 text-xs font-bold px-3 py-1 rounded">
                {{ $event->attendances->count() }} {{ Str::plural('Record', $event->attendances->count()) }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-cyan-50 text-left text-xs font-bold text-cyan-800 uppercase tracking-wider">Member</th>
                        <th class="py-3 px-6 bg-cyan-50 text-left text-xs font-bold text-cyan-800 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-6 bg-cyan-50 text-left text-xs font-bold text-cyan-800 uppercase tracking-wider">Recorded At</th>
                        <th class="py-3 px-6 bg-cyan-50 text-left text-xs font-bold text-cyan-800 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($event->attendances as $attendance)
                        <tr class="hover:bg-cyan-50">
                            <td class="py-4 px-6 text-sm">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $attendance->user->profile_photo_url ?? asset('img/defaults/user.svg') }}" alt="{{ $attendance->user->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-bold text-gray-900">{{ $attendance->user->name }}</div>
                                        <div class="text-gray-500 font-medium">{{ $attendance->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-sm">
                                @if($attendance->status == 'present')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">Present</span>
                                @elseif($attendance->status == 'absent')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">Absent</span>
                                @elseif($attendance->status == 'excused')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800">Excused</span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-gray-100 text-gray-800">{{ ucfirst($attendance->status) }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                {{ $attendance->created_at->format('M d, Y g:i A') }}
                            </td>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">
                                {{ $attendance->notes ?? 'No notes' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 px-6 text-sm text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-cyan-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="font-medium">No attendance records found</p>
                                    <p class="text-xs text-gray-400 mt-1">No one has attended this event yet</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <style>
        /* Improved text rendering for better readability */
        body {
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Improved status badges */
        .rounded-full {
            letter-spacing: 0.03em;
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        /* Button text improvements */
        .bg-yellow-500, .bg-cyan-600 {
            font-weight: 700;
            text-shadow: 0 1px 1px rgba(0,0,0,0.2);
            letter-spacing: 0.01em;
            box-shadow: 0 2px 4px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        }
        
        .bg-yellow-500:hover, .bg-cyan-600:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
            transform: translateY(-1px);
        }
        
        /* Table header improvements */
        th.text-xs {
            letter-spacing: 0.05em;
        }
    </style>
</div>
@endsection 