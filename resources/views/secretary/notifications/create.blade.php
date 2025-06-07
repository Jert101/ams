@extends('layouts.secretary-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-wrap justify-between items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-indigo-700 mb-2 md:mb-0">Create Notification</h1>
        <a href="{{ route('secretary.notifications.index') }}" class="btn-secondary">
            <i class="bi bi-arrow-left mr-1"></i> Back to Notifications
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

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('secretary.notifications.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                    <input type="text" id="title" name="title" required class="form-input rounded-md shadow-sm w-full" placeholder="Enter notification title">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select id="type" name="type" required class="form-select rounded-md shadow-sm w-full">
                        <option value="">Select notification type</option>
                        <option value="announcement">Announcement</option>
                        <option value="reminder">Reminder</option>
                        <option value="alert">Alert</option>
                    </select>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mb-6">
                <label for="recipients" class="block text-sm font-medium text-gray-700 mb-1">Recipients</label>
                <div class="flex flex-wrap gap-2 mb-2">
                    <label class="inline-flex items-center p-2 border border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer">
                        <input type="radio" name="recipient_type" value="all" class="form-radio h-4 w-4 text-indigo-600" checked>
                        <span class="ml-2 text-sm text-gray-700">All Members</span>
                    </label>
                    
                    <label class="inline-flex items-center p-2 border border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer">
                        <input type="radio" name="recipient_type" value="absent" class="form-radio h-4 w-4 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Members with Absences</span>
                    </label>
                    
                    <label class="inline-flex items-center p-2 border border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer">
                        <input type="radio" name="recipient_type" value="specific" class="form-radio h-4 w-4 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Specific Members</span>
                    </label>
                </div>
                
                <div id="specific-recipients" class="hidden mt-3">
                    <select id="user_ids" name="user_ids[]" multiple class="form-multiselect rounded-md shadow-sm w-full" size="5">
                        @for ($i = 1; $i <= 20; $i++)
                            <option value="{{ $i }}">Member {{ $i }}</option>
                        @endfor
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple members</p>
                </div>
                
                @error('user_ids')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                <textarea id="message" name="message" rows="5" required class="form-textarea rounded-md shadow-sm w-full" placeholder="Enter notification message"></textarea>
                @error('message')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="delivery_method" class="block text-sm font-medium text-gray-700 mb-1">Delivery Method</label>
                <div class="flex flex-wrap gap-2">
                    <label class="inline-flex items-center p-2 border border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox" name="delivery_methods[]" value="app" class="form-checkbox h-4 w-4 text-indigo-600" checked>
                        <span class="ml-2 text-sm text-gray-700">In-App</span>
                    </label>
                    
                    <label class="inline-flex items-center p-2 border border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox" name="delivery_methods[]" value="email" class="form-checkbox h-4 w-4 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Email</span>
                    </label>
                    
                    <label class="inline-flex items-center p-2 border border-gray-300 rounded-md bg-gray-50 hover:bg-gray-100 cursor-pointer">
                        <input type="checkbox" name="delivery_methods[]" value="sms" class="form-checkbox h-4 w-4 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">SMS</span>
                    </label>
                </div>
                @error('delivery_methods')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-6">
                <label for="schedule" class="block text-sm font-medium text-gray-700 mb-1">Delivery Schedule</label>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="schedule_type" value="immediate" class="form-radio h-4 w-4 text-indigo-600" checked>
                        <span class="ml-2 text-sm text-gray-700">Send Immediately</span>
                    </label>
                    
                    <label class="inline-flex items-center">
                        <input type="radio" name="schedule_type" value="scheduled" class="form-radio h-4 w-4 text-indigo-600">
                        <span class="ml-2 text-sm text-gray-700">Schedule for Later</span>
                    </label>
                </div>
                
                <div id="schedule-options" class="hidden mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="schedule_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" id="schedule_date" name="schedule_date" class="form-input rounded-md shadow-sm w-full" min="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div>
                        <label for="schedule_time" class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                        <input type="time" id="schedule_time" name="schedule_time" class="form-input rounded-md shadow-sm w-full">
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="window.location='{{ route('secretary.notifications.index') }}'" class="btn-secondary">
                    Cancel
                </button>
                <button type="submit" name="action" value="draft" class="btn-outline">
                    Save as Draft
                </button>
                <button type="submit" name="action" value="send" class="btn-primary">
                    Send Notification
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .btn-primary {
        @apply inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150;
    }
    
    .btn-secondary {
        @apply inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition ease-in-out duration-150;
    }
    
    .btn-outline {
        @apply inline-flex items-center px-4 py-2 bg-white border border-indigo-300 rounded-md font-semibold text-xs text-indigo-700 uppercase tracking-widest hover:bg-indigo-50 focus:outline-none focus:border-indigo-500 focus:ring ring-indigo-200 active:bg-indigo-100 disabled:opacity-25 transition ease-in-out duration-150;
    }
    
    .form-input, .form-select, .form-textarea, .form-multiselect {
        @apply mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle recipient type selection
        const recipientTypeRadios = document.querySelectorAll('input[name="recipient_type"]');
        const specificRecipientsDiv = document.getElementById('specific-recipients');
        
        recipientTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'specific') {
                    specificRecipientsDiv.classList.remove('hidden');
                } else {
                    specificRecipientsDiv.classList.add('hidden');
                }
            });
        });
        
        // Handle schedule type selection
        const scheduleTypeRadios = document.querySelectorAll('input[name="schedule_type"]');
        const scheduleOptionsDiv = document.getElementById('schedule-options');
        
        scheduleTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'scheduled') {
                    scheduleOptionsDiv.classList.remove('hidden');
                } else {
                    scheduleOptionsDiv.classList.add('hidden');
                }
            });
        });
    });
</script>
@endsection 