@extends('layouts.admin-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-red-700">User Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                Edit User
            </a>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Users
            </a>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-xl font-semibold text-red-700 mb-4">User Information</h2>
                    
                    <!-- Profile Photo -->
                    <div class="mb-6 flex justify-center md:justify-start">
                        <img src="{{ $user->profile_photo_url ?? asset('img/defaults/user.svg') }}" alt="{{ $user->name }}'s profile photo" class="h-32 w-32 object-cover rounded-full border-4 border-red-200">
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700">Name</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700">User ID</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->user_id }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700">Email</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->email }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700">Role</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->role->name }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700">Address</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->address ?? 'Not provided' }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700">Mobile Number</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->mobile_number ?? 'Not provided' }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700">Date of Birth</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->date_of_birth ? $user->date_of_birth->format('F d, Y') : 'Not provided' }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700">Created At</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-xl font-semibold text-red-700 mb-4">Additional Information</h2>
                    <div class="p-6 bg-red-50 rounded-lg border border-red-200 mb-6">
                        <p class="text-red-700 font-medium mb-4">User account status: <span class="font-bold">{{ ucfirst($user->approval_status) }}</span></p>
                        <p class="text-gray-700">This user was created on {{ $user->created_at->format('F d, Y') }} and has been a member for {{ $user->created_at->diffForHumans(null, true) }}.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-8">
        <h2 class="text-2xl font-semibold mb-4">Attendance History</h2>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                            <th class="py-3 px-6 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($user->attendances as $attendance)
                            <tr>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $attendance->event->name }}</td>
                                <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->event->date }}</td>
                                <td class="py-4 px-6 text-sm text-gray-500">
                                    @if ($attendance->status === 'present')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Present</span>
                                    @elseif ($attendance->status === 'absent')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Absent</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Excused</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->approved_by ? \App\Models\User::find($attendance->approved_by)->name : 'N/A' }}</td>
                                <td class="py-4 px-6 text-sm text-gray-500">{{ $attendance->approved_at ? $attendance->approved_at->format('F d, Y h:i A') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 px-6 text-sm text-gray-500 text-center" colspan="5">No attendance records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
