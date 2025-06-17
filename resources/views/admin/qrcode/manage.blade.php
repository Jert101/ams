@extends('layouts.admin-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-red-700 mb-4 sm:mb-0">Manage User QR Codes</h1>
        <div>
            <a href="{{ route('qrcode.print-batch') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-block text-center">
                Batch Print QR Codes
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 bg-gray-50 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-semibold mb-2 sm:mb-0">All Users</h2>
            <input type="text" id="user-search" placeholder="Search users..." class="border rounded px-3 py-2 w-full sm:w-64" autocomplete="off">
        </div>
        <div id="user-list">
            @include('admin.qrcode.partials.user-list', ['users' => $users])
        </div>
    </div>
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
                fetch(`{{ route('admin.qrcode.manage') }}?search=${encodeURIComponent(query)}`, {
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

<style>
    /* Responsive pagination */
    .pagination-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    @media (max-width: 640px) {
        .pagination {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.25rem;
        }
        
        .pagination > * {
            margin: 0.125rem;
        }
    }
</style>
@endsection 