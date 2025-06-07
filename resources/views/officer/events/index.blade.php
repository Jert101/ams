@extends('layouts.officer-app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-cyan-700">Event Management</h1>
        <a href="{{ route('officer.events.create') }}" class="bg-red-600 hover:bg-cyan-700 text-white font-bold py-3 px-6 rounded-lg shadow-md transition duration-150 ease-in-out flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            <span class="text-base">Create Event</span>
        </a>
    </div>
    
    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-cyan-100">
        <div class="bg-cyan-50 px-6 py-4 border-b border-cyan-100">
            <h2 class="text-xl font-semibold text-cyan-700">Event List</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-cyan-50 text-left text-xs font-bold text-cyan-800 uppercase tracking-wider">Name</th>
                        <th class="py-3 px-6 bg-cyan-50 text-left text-xs font-bold text-cyan-800 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-6 bg-cyan-50 text-left text-xs font-bold text-cyan-800 uppercase tracking-wider">Time</th>
                        <th class="py-3 px-6 bg-cyan-50 text-left text-xs font-bold text-cyan-800 uppercase tracking-wider">Location</th>
                        <th class="py-3 px-6 bg-cyan-50 text-left text-xs font-bold text-cyan-800 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-6 bg-cyan-50 text-left text-xs font-bold text-cyan-800 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($events as $event)
                        <tr class="hover:bg-cyan-50">
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $event->name }}</td>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $event->date }}</td>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $event->time }}</td>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $event->location }}</td>
                            <td class="py-4 px-6 text-sm">
                                @if($event->is_active)
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('officer.events.edit', $event->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded-md text-sm font-bold flex items-center shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <a href="{{ route('officer.events.show', $event->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md text-sm font-bold flex items-center shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-sm text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-cyan-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="font-medium">No events found</p>
                                    <p class="text-xs text-gray-400 mt-1">No events are currently available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-6">
        <div class="pagination-wrapper">
            {{ $events->links() }}
        </div>
    </div>
    
    <style>
        /* Improved font styles for better readability */
        body {
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Table cell styles */
        .text-sm {
            font-size: 0.9rem;
            letter-spacing: 0.01em;
        }
        
        /* Button text improvement */
        .bg-yellow-500, .bg-cyan-500, .bg-cyan-600 {
            font-weight: 700;
            text-shadow: 0 1px 1px rgba(0,0,0,0.2);
            letter-spacing: 0.01em;
        }
        
        /* Make action buttons more visible */
        .bg-yellow-500, .bg-cyan-500 {
            box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
            transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        }
        
        .bg-yellow-500:hover, .bg-cyan-500:hover, .bg-cyan-600:hover {
            box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
        }
        
        /* Status badge improvements */
        .rounded-full {
            letter-spacing: 0.03em;
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        /* KofA Pagination Styling */
        .pagination-wrapper nav div:first-child { display: none; } /* Hide the pagination text */
        .pagination-wrapper nav span.relative.inline-flex.items-center.px-4.py-2.text-sm.font-medium.text-gray-700.bg-white.border.border-gray-300 {
            background-color: #e0f2fe;
            border-color: #bae6fd;
            color: #0891b2;
            font-weight: 600;
        }
        .pagination-wrapper nav button.relative.inline-flex.items-center.px-4.py-2.text-sm.font-medium.text-gray-700.bg-white.border.border-gray-300:hover {
            background-color: #e0f2fe;
            border-color: #bae6fd;
            color: #0891b2;
            font-weight: 600;
        }
        .pagination-wrapper nav button.relative.inline-flex.items-center.px-4.py-2.border.text-sm.font-medium.rounded-md.text-white.bg-indigo-600 {
            background-color: #06b6d4;
            border-color: #0891b2;
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .pagination-wrapper nav button.relative.inline-flex.items-center.px-4.py-2.border.text-sm.font-medium.rounded-md.text-white.bg-indigo-600:hover {
            background-color: #0891b2;
        }
    </style>
</div>
@endsection 