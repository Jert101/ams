@extends('layouts.secretary-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-wrap justify-between items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-indigo-700 mb-2 md:mb-0">Notifications</h1>
        <a href="{{ route('secretary.notifications.create') }}" class="btn-primary">
            <i class="bi bi-bell-fill mr-1"></i> Create Notification
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

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <h2 class="text-lg font-semibold text-indigo-700 mb-4">Filter Notifications</h2>
        <form action="{{ route('secretary.notifications.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" name="search" placeholder="Search by title or content" class="form-input rounded-md shadow-sm w-full" value="{{ request('search') }}">
            </div>
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="type" name="type" class="form-select rounded-md shadow-sm w-full">
                    <option value="">All Types</option>
                    <option value="announcement" {{ request('type') == 'announcement' ? 'selected' : '' }}>Announcement</option>
                    <option value="reminder" {{ request('type') == 'reminder' ? 'selected' : '' }}>Reminder</option>
                    <option value="absence_warning" {{ request('type') == 'absence_warning' ? 'selected' : '' }}>Absence Warning</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="form-select rounded-md shadow-sm w-full">
                    <option value="">All Statuses</option>
                    <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                    <option value="not_sent" {{ request('status') == 'not_sent' ? 'selected' : '' }}>Not Sent</option>
                </select>
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button type="submit" class="btn-primary">
                    <i class="bi bi-funnel mr-1"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>
    
    <!-- Mobile Card View -->
    <div class="block md:hidden space-y-4">
        @forelse ($notifications as $notification)
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-semibold text-lg text-indigo-700">{{ $notification->type ? ucfirst(str_replace('_', ' ', $notification->type)) : 'Notification' }}</h3>
                    <span class="text-xs px-2 py-1 rounded-full {{ $notification->is_sent ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $notification->is_sent ? 'Sent' : 'Draft' }}
                    </span>
                </div>
                <div class="text-sm text-gray-600 mb-2">
                    <span class="font-medium">Recipient:</span> {{ $notification->user ? $notification->user->name : 'Unknown User' }}
                </div>
                <div class="text-sm text-gray-600 mb-2">
                    <span class="font-medium">Date:</span> {{ $notification->sent_at ? $notification->sent_at->format('Y-m-d') : ($notification->created_at ? $notification->created_at->format('Y-m-d') : 'N/A') }}
                </div>
                <div class="text-sm text-gray-600 mb-3">
                    <span class="font-medium">Time:</span> {{ $notification->sent_at ? $notification->sent_at->format('g:i A') : ($notification->created_at ? $notification->created_at->format('g:i A') : 'N/A') }}
                </div>
                <p class="text-sm text-gray-700 mb-3 line-clamp-2">
                    {{ $notification->message }}
                </p>
                <div class="flex justify-end space-x-2 mt-2">
                    <a href="#" class="text-sm px-3 py-1 bg-indigo-100 text-indigo-700 rounded-md hover:bg-indigo-200">View</a>
                    @if (!$notification->is_sent)
                        <form action="{{ route('secretary.notifications.mark-as-sent', $notification) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm px-3 py-1 bg-green-100 text-green-700 rounded-md hover:bg-green-200">
                                Send
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('secretary.notifications.destroy', $notification) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm px-3 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200" onclick="return confirm('Are you sure you want to delete this notification?')">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-500">No notifications found.</p>
            </div>
        @endforelse
        
        <div class="mt-4 flex justify-center">
            {{ $notifications->links() }}
        </div>
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden md:block bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($notifications as $notification)
                        <tr>
                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $notification->type ? ucfirst(str_replace('_', ' ', $notification->type)) : 'Notification' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ $notification->user ? $notification->user->name : 'Unknown User' }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($notification->message, 50) }}</td>
                            <td class="py-4 px-6 text-sm text-gray-500">
                                {{ $notification->sent_at ? $notification->sent_at->format('Y-m-d g:i A') : ($notification->created_at ? $notification->created_at->format('Y-m-d g:i A') : 'N/A') }}
                            </td>
                            <td class="py-4 px-6 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full font-medium {{ $notification->is_sent ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $notification->is_sent ? 'Sent' : 'Draft' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    @if (!$notification->is_sent)
                                        <form action="{{ route('secretary.notifications.mark-as-sent', $notification) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900">
                                                Send
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('secretary.notifications.destroy', $notification) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this notification?')">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="6">No notifications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Desktop Pagination -->
    <div class="mt-4 hidden md:flex justify-center">
        {{ $notifications->links() }}
    </div>
</div>

<style>
    .btn-primary {
        @apply inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    
    .form-input, .form-select {
        @apply mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50;
    }
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
