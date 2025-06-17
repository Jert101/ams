@extends('layouts.admin-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-red-700">Manage Users</h1>
        <a href="{{ route('admin.users.create') }}" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded inline-flex items-center">
            <i class="bi bi-person-plus-fill mr-2"></i> Add New User
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
    
    <!-- Search and Filter Bar -->
    <div class="bg-white shadow-md rounded-lg p-4 mb-4">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-grow relative">
                <input 
                    type="text" 
                    id="user-search" 
                    name="search" 
                    value="{{ $search ?? '' }}" 
                    placeholder="Search by name, email, ID, or mobile..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                    autocomplete="off"
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="document.getElementById('user-search').value=''; document.getElementById('user-search').dispatchEvent(new Event('input'));">
                    Clear
                </button>
            </div>
        </div>
    </div>
    <div id="user-list">
        @include('admin.users.partials.user-list', ['users' => $users, 'now' => $now])
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('user-search');
            const userList = document.getElementById('user-list');
            let timeout = null;
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    const query = searchInput.value;
                    fetch(`{{ route('admin.users.index') }}?search=${encodeURIComponent(query)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        userList.innerHTML = data.html;
                    });
                }, 300);
            });
        });
    </script>
</div>
@endsection

@push('styles')
<style>
    .user-avatar {
        width: 40px;
        height: 40px;
        overflow: hidden;
        border-radius: 50%;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
</style>
@endpush
