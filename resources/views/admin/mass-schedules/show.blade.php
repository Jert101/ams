<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mass Schedule Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $massSchedule->event->name }}</h3>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.mass-schedules.edit', $massSchedule) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.mass-schedules.destroy', $massSchedule) }}" onsubmit="return confirm('Are you sure you want to delete this mass schedule?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete</button>
                            </form>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Type</p>
                                <p class="font-medium">{{ $massSchedule->type === 'sunday_mass' ? 'Sunday Mass' : 'Special Mass' }}</p>
                            </div>
                            
                            @if($massSchedule->type === 'sunday_mass')
                            <div>
                                <p class="text-sm text-gray-600">Mass Order</p>
                                <p class="font-medium">{{ ucfirst($massSchedule->mass_order) }} Mass</p>
                            </div>
                            @endif
                            
                            <div>
                                <p class="text-sm text-gray-600">Date</p>
                                <p class="font-medium">{{ $massSchedule->event->date->format('F j, Y') }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Location</p>
                                <p class="font-medium">{{ $massSchedule->event->location }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Time</p>
                                <p class="font-medium">{{ $massSchedule->formatted_start_time }} - {{ $massSchedule->formatted_end_time }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Attendance Window</p>
                                <p class="font-medium">{{ $massSchedule->formatted_attendance_start_time }} - {{ $massSchedule->formatted_attendance_end_time }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <p class="font-medium">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $massSchedule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $massSchedule->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Current Attendance Status</p>
                                <p class="font-medium">
                                    <span id="attendance-status" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Checking...
                                    </span>
                                </p>
                            </div>
                            
                            @if($massSchedule->event->description)
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-600">Description</p>
                                <p class="font-medium">{{ $massSchedule->event->description }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <a href="{{ route('admin.mass-schedules.index') }}" class="text-blue-600 hover:text-blue-900">
                        &larr; Back to Mass Schedules
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if attendance is currently allowed
            function checkAttendanceStatus() {
                const now = new Date();
                const currentHour = now.getHours();
                const currentMinute = now.getMinutes();
                const currentTime = currentHour * 60 + currentMinute;
                
                // Get attendance window times from PHP variables
                const attendanceStartTime = "{{ $massSchedule->attendance_start_time->format('H:i') }}";
                const attendanceEndTime = "{{ $massSchedule->attendance_end_time->format('H:i') }}";
                
                // Convert to minutes from midnight for comparison
                const [startHour, startMinute] = attendanceStartTime.split(':').map(Number);
                const [endHour, endMinute] = attendanceEndTime.split(':').map(Number);
                
                const startTimeMinutes = startHour * 60 + startMinute;
                const endTimeMinutes = endHour * 60 + endMinute;
                
                const statusElement = document.getElementById('attendance-status');
                
                // Check if current time is within attendance window
                if (currentTime >= startTimeMinutes && currentTime <= endTimeMinutes) {
                    statusElement.textContent = 'Open for Attendance';
                    statusElement.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800';
                } else {
                    statusElement.textContent = 'Closed for Attendance';
                    statusElement.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800';
                }
            }
            
            // Check initially and then every minute
            checkAttendanceStatus();
            setInterval(checkAttendanceStatus, 60000);
        });
    </script>
</x-app-layout> 