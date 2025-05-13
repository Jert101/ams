@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Edit Attendance</h1>
        <a href="{{ route('officer.attendances.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Attendances
        </a>
    </div>
    
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-xl font-semibold mb-4">Member Information</h2>
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Name</p>
                    <p class="text-lg">{{ $attendance->user->name }}</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Email</p>
                    <p class="text-lg">{{ $attendance->user->email }}</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Phone</p>
                    <p class="text-lg">{{ $attendance->user->phone ?? 'Not provided' }}</p>
                </div>
            </div>
            
            <div>
                <h2 class="text-xl font-semibold mb-4">Event Information</h2>
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Name</p>
                    <p class="text-lg">{{ $attendance->event->name }}</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Date</p>
                    <p class="text-lg">{{ $attendance->event->date }}</p>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm font-medium text-gray-500">Time</p>
                    <p class="text-lg">{{ $attendance->event->time }}</p>
                </div>
            </div>
        </div>
        
        <form action="{{ route('officer.attendances.update', $attendance) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                <div class="mt-2 space-y-2">
                    <div class="flex items-center">
                        <input id="status-present" name="status" type="radio" value="present" {{ $attendance->status === 'present' ? 'checked' : '' }} class="form-radio h-4 w-4 text-indigo-600">
                        <label for="status-present" class="ml-2 text-sm text-gray-700">Present</label>
                    </div>
                    <div class="flex items-center">
                        <input id="status-absent" name="status" type="radio" value="absent" {{ $attendance->status === 'absent' ? 'checked' : '' }} class="form-radio h-4 w-4 text-indigo-600">
                        <label for="status-absent" class="ml-2 text-sm text-gray-700">Absent</label>
                    </div>
                    <div class="flex items-center">
                        <input id="status-excused" name="status" type="radio" value="excused" {{ $attendance->status === 'excused' ? 'checked' : '' }} class="form-radio h-4 w-4 text-indigo-600">
                        <label for="status-excused" class="ml-2 text-sm text-gray-700">Excused</label>
                    </div>
                </div>
                @error('status')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="remarks" class="block text-gray-700 text-sm font-bold mb-2">Remarks:</label>
                <textarea name="remarks" id="remarks" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('remarks') border-red-500 @enderror">{{ old('remarks', $attendance->remarks) }}</textarea>
                @error('remarks')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex items-center justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Attendance
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
