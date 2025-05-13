@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">User Details</h1>
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
                    <h2 class="text-xl font-semibold mb-4">User Information</h2>
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Name</p>
                        <p class="text-lg">{{ $user->name }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Email</p>
                        <p class="text-lg">{{ $user->email }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Role</p>
                        <p class="text-lg">{{ $user->role->name }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Phone</p>
                        <p class="text-lg">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-500">Created At</p>
                        <p class="text-lg">{{ $user->created_at->format('F d, Y h:i A') }}</p>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-xl font-semibold mb-4">QR Code</h2>
                    @if ($user->qrCode)
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">QR Code ID</p>
                            <p class="text-lg">{{ $user->qrCode->code }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <p class="text-lg">
                                @if ($user->qrCode->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inactive</span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-500">Last Used</p>
                            <p class="text-lg">{{ $user->qrCode->last_used_at ? $user->qrCode->last_used_at->format('F d, Y h:i A') : 'Never used' }}</p>
                        </div>
                        
                        <div class="mb-4">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $user->qrCode->code }}" alt="QR Code" class="border border-gray-300 rounded">
                        </div>
                    @else
                        <p class="text-red-500">No QR code assigned to this user.</p>
                        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="mt-4">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="generate_qr" value="1">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Generate QR Code
                            </button>
                        </form>
                    @endif
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
