{{-- Mobile Card View --}}
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

{{-- Desktop Table View --}}
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
<div class="mt-4 hidden md:flex justify-center">
    {{ $notifications->links() }}
</div> 