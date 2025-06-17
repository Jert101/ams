{{-- Mobile View (Card Layout) --}}
<div class="block sm:hidden">
    @foreach($users as $user)
    <div class="border-b border-gray-200 p-4">
        <div class="flex items-center mb-3">
            <div class="flex-shrink-0 h-10 w-10 mr-3">
                <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200" 
                     src="{{ $user->profile_photo_url }}" 
                     alt="{{ $user->name }}">
            </div>
            <div>
                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                <div class="text-xs text-gray-500 truncate max-w-[200px]">{{ $user->email }}</div>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs mb-3">
            <div>
                <span class="font-semibold block">User ID:</span>
                <span>{{ $user->user_id }}</span>
            </div>
            <div>
                <span class="font-semibold block">Role:</span>
                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    {{ $user->role->name ?? 'No Role' }}
                </span>
            </div>
            <div>
                <span class="font-semibold block">QR Code:</span>
                @if($user->qrCode)
                    <span class="font-mono">{{ substr($user->qrCode->code, 0, 8) }}...</span>
                @else
                    <span class="text-red-500">No QR Code</span>
                @endif
            </div>
            <div>
                <span class="font-semibold block">Last Used:</span>
                @if($user->qrCode && $user->qrCode->last_used_at)
                    {{ $user->qrCode->last_used_at->format('M d, Y') }}
                @else
                    Never used
                @endif
            </div>
        </div>
        <div class="flex justify-end space-x-2 border-t pt-3">
            @if($user->qrCode)
                <a href="{{ route('admin.qrcode.view', $user->user_id) }}" class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded text-xs font-medium">
                    View
                </a>
                <a href="{{ route('admin.qrcode.print', $user->user_id) }}" class="bg-blue-100 text-blue-700 px-3 py-1 rounded text-xs font-medium">
                    Print
                </a>
            @else
                <span class="text-gray-400 italic text-xs">User must generate their own QR code</span>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- Desktop View (Table Layout) --}}
<div class="hidden sm:block overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 table-responsive">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    User ID
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Name
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Role
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    QR Code
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Last Used
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($users as $user)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    {{ $user->user_id }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200" 
                                 src="{{ $user->profile_photo_url }}" 
                                 alt="{{ $user->name }}">
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        {{ $user->role->name ?? 'No Role' }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($user->qrCode)
                        <span class="font-mono">{{ substr($user->qrCode->code, 0, 8) }}...</span>
                    @else
                        <span class="text-red-500">No QR Code</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($user->qrCode && $user->qrCode->last_used_at)
                        {{ $user->qrCode->last_used_at->format('M d, Y H:i') }}
                    @else
                        Never used
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex space-x-2">
                        @if($user->qrCode)
                            <a href="{{ route('admin.qrcode.view', $user->user_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                View
                            </a>
                            <a href="{{ route('admin.qrcode.print', $user->user_id) }}" class="text-blue-600 hover:text-blue-900">
                                Print
                            </a>
                        @else
                            <span class="text-gray-400 italic">User must generate their own QR code</span>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="p-4 border-t">
    <div class="pagination-container overflow-x-auto py-2">
        {{ $users->links() }}
    </div>
</div> 