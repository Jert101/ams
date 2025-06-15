@extends('layouts.admin-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-wrap items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-red-700">Election Positions</h1>
            <p class="text-gray-600 mt-1">Manage election positions and their requirements</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('admin.election.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-200 focus:outline-none focus:border-gray-300 focus:ring focus:ring-gray-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Election Management
            </a>
        </div>
    </div>

    <!-- Add Position Button -->
    <div class="mb-6">
        <button onclick="openAddPositionModal()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Position
        </button>
    </div>

    <!-- Positions List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="min-w-full leading-normal">
            <div class="w-full">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Position Title
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Eligible Roles
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Candidates
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($positions as $position)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-5 border-b border-gray-200">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $position->title }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200">
                                    <p class="text-gray-600">{{ Str::limit($position->description, 100) }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($position->eligible_roles as $role)
                                            <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                                {{ $role }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200">
                                    <span class="px-3 py-1 text-sm rounded-full {{ $position->candidates_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $position->candidates_count }} Candidate(s)
                                    </span>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="openEditPositionModal({{ $position->id }})" 
                                                class="text-blue-600 hover:text-blue-900">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.election.positions.delete', $position) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this position?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-5 border-b border-gray-200 text-center text-gray-500">
                                    No positions have been created yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Position Modal -->
<div id="addPositionModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('admin.election.positions.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Position</h3>
                    
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700">Position Title</label>
                        <input type="text" name="title" id="title" required
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" required
                                  class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Eligible Roles</label>
                        <div class="mt-2 space-y-2">
                            @foreach($roles as $role)
                                <div class="flex items-center">
                                    <input type="checkbox" name="eligible_roles[]" value="{{ $role->name }}"
                                           class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300 rounded">
                                    <label class="ml-2 text-sm text-gray-700">{{ $role->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="required_votes" class="block text-sm font-medium text-gray-700">Required Votes</label>
                        <input type="number" name="required_votes" id="required_votes" min="1" value="1" required
                               class="mt-1 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <p class="mt-1 text-xs text-gray-500">Number of candidates each voter must select for this position</p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Add Position
                    </button>
                    <button type="button" onclick="closeAddPositionModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openAddPositionModal() {
        document.getElementById('addPositionModal').classList.remove('hidden');
    }
    
    function closeAddPositionModal() {
        document.getElementById('addPositionModal').classList.add('hidden');
    }
    
    function openEditPositionModal(positionId) {
        // Implementation for editing position
        alert('Edit position ' + positionId);
    }
</script>
@endpush
@endsection 