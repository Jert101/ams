@extends('layouts.secretary-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-wrap justify-between items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-indigo-700 mb-2 md:mb-0">Member Management</h1>
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

    <!-- Mobile Card View -->
    <div class="block md:hidden">
        @forelse ($members as $member)
            <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-lg text-indigo-700">{{ $member->name }}</h3>
                    <span class="text-xs px-2 py-1 rounded-full {{ $member->consecutive_absences > 2 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                        {{ $member->attendance_rate }}
                    </span>
                </div>
                <div class="text-sm text-gray-600 mb-1">
                    <span class="font-medium">User ID:</span> {{ $member->user_id }}
                </div>
                <div class="text-sm text-gray-600 mb-1">
                    <span class="font-medium">Email:</span> {{ $member->email }}
                </div>
                <div class="text-sm text-gray-600 mb-1">
                    <span class="font-medium">Phone:</span> {{ $member->phone ?? 'N/A' }}
                </div>
                <div class="text-sm text-gray-600 mb-3">
                    <span class="font-medium">Consecutive Absences:</span> 
                    <span class="{{ $member->consecutive_absences > 2 ? 'text-red-600 font-bold' : '' }}">
                        {{ $member->consecutive_absences }}
                    </span>
                </div>
                <div class="flex justify-end space-x-2 mt-2">
                    <a href="#" class="text-sm px-3 py-1 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200">View</a>
                    <a href="#" class="text-sm px-3 py-1 bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200">History</a>
                    <a href="#" class="text-sm px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200">Flag</a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-500">No members found.</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $members->links() }}
        </div>
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden md:block bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Rate</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Consecutive Absences</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($members as $member)
                        <tr>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $member->name }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $member->user_id }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $member->email }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $member->phone ?? 'N/A' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $member->attendance_rate }}</td>
                            <td class="py-4 px-6 text-sm {{ $member->consecutive_absences > 2 ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                {{ $member->consecutive_absences }}
                            </td>
                            <td class="py-4 px-6 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    <a href="#" class="text-yellow-600 hover:text-yellow-900">History</a>
                                    <a href="#" class="text-red-600 hover:text-red-900">Flag</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="7">No members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4 hidden md:block">
        {{ $members->links() }}
    </div>
</div>
@endsection 