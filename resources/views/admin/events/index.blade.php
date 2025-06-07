@extends('layouts.admin-app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-red-700">Manage Events</h1>
        <a href="{{ route('admin.events.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add New Event
        </a>
    </div>
    
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    
    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-red-100">
        <div class="bg-red-50 px-6 py-4 border-b border-red-100">
            <h2 class="text-xl font-semibold text-red-700">Event List</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-red-50 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Name</th>
                        <th class="py-3 px-6 bg-red-50 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-6 bg-red-50 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Time</th>
                        <th class="py-3 px-6 bg-red-50 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Location</th>
                        <th class="py-3 px-6 bg-red-50 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-6 bg-red-50 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($events as $event)
                        <tr class="hover:bg-red-50">
                            <td class="py-4 px-6 text-sm text-gray-900">{{ $event->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-900">{{ $event->date }}</td>
                            <td class="py-4 px-6 text-sm text-gray-900">{{ $event->time }}</td>
                            <td class="py-4 px-6 text-sm text-gray-900">{{ $event->location }}</td>
                            <td class="py-4 px-6 text-sm">
                                @if($event->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.events.edit', $event->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded-md text-xs flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded-md text-xs flex items-center" onclick="return confirm('Are you sure you want to delete this event?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-sm text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-red-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="font-medium">No events found</p>
                                    <p class="text-xs text-gray-400 mt-1">Create your first event by clicking the "Add New Event" button</p>
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
        /* KofA Pagination Styling */
        .pagination-wrapper nav div:first-child { display: none; } /* Hide the pagination text */
        .pagination-wrapper nav span.relative.inline-flex.items-center.px-4.py-2.text-sm.font-medium.text-gray-700.bg-white.border.border-gray-300 {
            background-color: #fee2e2;
            border-color: #fecaca;
            color: #b91c1c;
        }
        .pagination-wrapper nav button.relative.inline-flex.items-center.px-4.py-2.text-sm.font-medium.text-gray-700.bg-white.border.border-gray-300:hover {
            background-color: #fee2e2;
            border-color: #fecaca;
            color: #b91c1c;
        }
        .pagination-wrapper nav button.relative.inline-flex.items-center.px-4.py-2.border.text-sm.font-medium.rounded-md.text-white.bg-indigo-600 {
            background-color: #dc2626;
            border-color: #b91c1c;
        }
        .pagination-wrapper nav button.relative.inline-flex.items-center.px-4.py-2.border.text-sm.font-medium.rounded-md.text-white.bg-indigo-600:hover {
            background-color: #b91c1c;
        }
    </style>
</div>
@endsection
