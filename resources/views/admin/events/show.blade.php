@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Event Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.events.edit', $event) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Edit Event
            </a>
            <a href="{{ route('admin.events.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Events
            </a>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-xl font-semibold mb-4">Event Information</h2>
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Name</p>
                        <p class="text-lg">{{ $event->name }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Date</p>
                        <p class="text-lg">{{ $event->date }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Time</p>
                        <p class="text-lg">{{ $event->time }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Status</p>
                        <p class="text-lg">
                            @if ($event->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-xl font-semibold mb-4">Description</h2>
                    <div class="mb-4 bg-gray-50 p-4 rounded">
                        <p class="text-gray-700">{{ $event->description ?? 'No description provided.' }}</p>
                    </div>
                    
                    <h2 class="text-xl font-semibold mb-4 mt-6">Attendance Summary</h2>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-green-50 p-4 rounded text-center">
                            <p class="text-sm font-medium text-gray-500">Present</p>
                            <p class="text-2xl font-bold text-green-600">{{ $event->attendances->where('status', 'present')->count() }}</p>
                        </div>
                        
                        <div class="bg-red-50 p-4 rounded text-center">
                            <p class="text-sm font-medium text-gray-500">Absent</p>
                            <p class="text-2xl font-bold text-red-600">{{ $event->attendances->where('status', 'absent')->count() }}</p>
                        </div>
                        
                        <div class="bg-yellow-50 p-4 rounded text-center">
                            <p class="text-sm font-medium text-gray-500">Excused</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ $event->attendances->where('status', 'excused')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-8">
        <h2 class="text-2xl font-semibold mb-4">Attendance List</h2>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved At</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($event->attendances as $attendance)
                            <tr>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $attendance->user->name }}</td>
                                <td class="py-4 px-6 text-sm text-gray-500">
                                    @if ($attendance->status === 'present')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                                    @elseif ($attendance->status === 'absent')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absent</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Excused</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->approved_by ? \App\Models\User::find($attendance->approved_by)->name : 'N/A' }}</td>
                                <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->approved_at ? $attendance->approved_at->format('F d, Y h:i A') : 'N/A' }}</td>
                                <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->remarks ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="5">No attendance records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
