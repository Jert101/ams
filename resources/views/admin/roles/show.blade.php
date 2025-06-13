@extends('layouts.admin-app')

@section('content')
<div class="w-full py-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-red-700 mb-2">Role Details: {{ $role->name }}</h1>
        <a href="{{ route('admin.roles.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
            <i class="bi bi-arrow-left mr-1"></i> Back to Roles
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Role Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm font-semibold text-red-700 mb-1">ID:</p>
                        <p class="font-medium text-gray-900">{{ $role->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-red-700 mb-1">Name:</p>
                        <p class="font-medium text-gray-900">{{ $role->name }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm font-semibold text-red-700 mb-1">Description:</p>
                        <p class="font-medium text-gray-900">{{ $role->description ?? 'No description provided' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-red-700 mb-1">Created At:</p>
                        <p class="font-medium text-gray-900">{{ $role->created_at->format('M d, Y H:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-red-700 mb-1">Updated At:</p>
                        <p class="font-medium text-gray-900">{{ $role->updated_at->format('M d, Y H:i A') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="flex space-x-2 mt-4">
                <a href="{{ route('admin.roles.edit', $role) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                    <i class="bi bi-pencil mr-1"></i> Edit
                </a>
                @if ($role->users->count() == 0)
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out" onclick="return confirm('Are you sure you want to delete this role?')">
                            <i class="bi bi-trash mr-1"></i> Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Users with this Role</h2>
            
            @if ($role->users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-red-200">
                        <thead class="bg-red-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-red-700 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-red-100">
                            @foreach ($role->users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if ($user->profile_photo_url)
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                                                @else
                                                    <div class="h-10 w-10 rounded-full bg-red-700 flex items-center justify-center text-white font-bold">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.users.show', $user) }}" class="text-red-600 hover:text-red-900 mr-3">View</a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="text-red-600 hover:text-red-900">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">No users have been assigned this role yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
