@extends('layouts.admin-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-wrap items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-red-700">Candidate Applications</h1>
            <p class="text-gray-600 mt-1">Manage and review election candidate applications</p>
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

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @php
            $totalCandidates = $candidates->count();
            $approvedCandidates = $candidates->where('status', 'approved')->count();
            $pendingCandidates = $candidates->where('status', 'pending')->count();
            $rejectedCandidates = $candidates->where('status', 'rejected')->count();
        @endphp
        
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-900 text-lg font-semibold">Total</h2>
                    <p class="mt-2 text-3xl font-bold text-gray-800">{{ $totalCandidates }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-900 text-lg font-semibold">Approved</h2>
                    <p class="mt-2 text-3xl font-bold text-gray-800">{{ $approvedCandidates }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-900 text-lg font-semibold">Pending</h2>
                    <p class="mt-2 text-3xl font-bold text-gray-800">{{ $pendingCandidates }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-gray-900 text-lg font-semibold">Rejected</h2>
                    <p class="mt-2 text-3xl font-bold text-gray-800">{{ $rejectedCandidates }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Candidates List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">All Candidates</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Candidate
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Position
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Applied On
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($candidates as $candidate)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img src="{{ $candidate->user ? $candidate->user->profile_photo_url : asset('img/kofa.png') }}" 
                                             alt="{{ $candidate->user_name }}" 
                                             class="h-10 w-10 rounded-full object-cover border border-gray-200">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $candidate->user_name }}
                                        </div>
                                        @if(isset($candidate->user_email))
                                            <div class="text-sm text-gray-500">
                                                {{ $candidate->user_email }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $candidate->position ? $candidate->position->title : 'Unknown Position' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $candidate->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $candidate->created_at->format('h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $candidate->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                       ($candidate->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($candidate->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex space-x-2">
                                    <a href="{{ url('/admin/election/candidates/'.$candidate->id) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View
                                    </a>
                                    
                                    @if($candidate->status === 'pending')
                                        <form action="{{ url('/admin/election/candidates/' . $candidate->id . '/approve') }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                        
                                        <form action="{{ url('/admin/election/candidates/' . $candidate->id . '/reject') }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                Reject
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection