@extends('layouts.admin-app')

@section('content')
<div class="w-full py-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-red-700 mb-2">Role Management</h1>
        <a href="{{ route('admin.roles.create') }}" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
            <i class="bi bi-plus-circle mr-1"></i> Add New Role
        </a>
    </div>
    
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-responsive">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users Count</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($roles as $role)
                    <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="ID">{{ $role->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap" data-label="Name">
                            <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                        </td>
                            <td class="px-6 py-4" data-label="Description">
                            <div class="text-sm text-gray-500">{{ $role->description ?? 'No description' }}</div>
                        </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" data-label="Users Count">
                            {{ $role->users_count }}
                        </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" data-label="Actions">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                    <a href="{{ route('admin.roles.show', $role) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            @if ($role->users_count == 0)
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this role?')">
                                        Delete
                                    </button>
                                </form>
                            @endif
                                </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No roles found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $roles->links() }}
    </div>
</div>
@endsection
