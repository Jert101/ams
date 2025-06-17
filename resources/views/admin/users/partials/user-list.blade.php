<div class="overflow-x-auto">
    <table class="min-w-full bg-white table-responsive">
        <thead class="bg-gray-50">
            <tr>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">Name</th>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">User ID</th>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">Email</th>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">Role</th>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">QR Code</th>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">Mobile</th>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">Status</th>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" scope="col">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse ($users as $user)
                <tr>
                    <td class="py-4 px-6 whitespace-nowrap" data-label="Name">
                        <div class="flex items-center">
                            <div class="user-avatar relative h-10 w-10 mr-2">
                                <img src="{{ $user->profile_photo_url ?? asset('img/kofa.png') }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover border-2 border-gray-200 profile-user-img">
                                @php
                                    $isOnline = $user->last_seen_at && \Carbon\Carbon::parse($user->last_seen_at)->gt($now->subMinutes(5));
                                @endphp
                                <span style="position:absolute;bottom:0;right:0;width:12px;height:12px;border-radius:50%;border:2px solid #fff;{{ $isOnline ? 'background:#22c55e;' : 'background:#a3a3a3;' }}" title="{{ $isOnline ? 'Online' : 'Offline' }}"></span>
                            </div>
                            <div class="ml-2">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-sm text-gray-500" data-label="User ID">{{ $user->user_id }}</td>
                    <td class="py-4 px-6 text-sm text-gray-500" data-label="Email">{{ $user->email }}</td>
                    <td class="py-4 px-6 text-sm text-gray-500" data-label="Role">{{ $user->role ? $user->role->name : 'No Role' }}</td>
                    <td class="py-4 px-6 text-sm text-gray-500" data-label="QR Code">
                        @if($user->qrCode)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="bi bi-check-circle-fill mr-1"></i> Generated
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <i class="bi bi-exclamation-circle-fill mr-1"></i> Not Generated
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-sm text-gray-500" data-label="Mobile">{{ $user->mobile_number ?? 'N/A' }}</td>
                    <td class="py-4 px-6 text-sm text-gray-500" data-label="Status">
                        @if($user->approval_status === 'approved')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Approved
                            </span>
                        @elseif($user->approval_status === 'pending')
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-sm font-medium" data-label="Actions">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this user?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="8">No users found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $users->links() }} 