<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Special Mass Schedule') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form action="{{ route('admin.mass-schedules.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Mass Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Mass Type -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Mass Type</label>
                                <select id="type" name="type" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full" onchange="toggleMassOrder()">
                                    <option value="special_mass">Special Mass</option>
                                    <option value="sunday_mass">Sunday Mass</option>
                                </select>
                                @error('type')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Mass Order (for Sunday Masses) -->
                            <div id="mass_order_container" style="display: none;">
                                <label for="mass_order" class="block text-sm font-medium text-gray-700 mb-1">Mass Order</label>
                                <select id="mass_order" name="mass_order" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                    <option value="first">First Mass</option>
                                    <option value="second">Second Mass</option>
                                    <option value="third">Third Mass</option>
                                    <option value="fourth">Fourth Mass</option>
                                </select>
                                @error('mass_order')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Event Name -->
                            <div>
                                <label for="event_name" class="block text-sm font-medium text-gray-700 mb-1">Event Name</label>
                                <input type="text" id="event_name" name="event_name" value="{{ old('event_name') }}" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                @error('event_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Date -->
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                @error('date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Location -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <input type="text" id="location" name="location" value="{{ old('location', 'Church') }}" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                @error('location')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="description" name="description" rows="3" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Time Settings</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Start Time -->
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                @error('start_time')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- End Time -->
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                @error('end_time')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Attendance Start Time -->
                            <div>
                                <label for="attendance_start_time" class="block text-sm font-medium text-gray-700 mb-1">Attendance Start Time</label>
                                <input type="time" id="attendance_start_time" name="attendance_start_time" value="{{ old('attendance_start_time') }}" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                @error('attendance_start_time')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Attendance End Time -->
                            <div>
                                <label for="attendance_end_time" class="block text-sm font-medium text-gray-700 mb-1">Attendance End Time</label>
                                <input type="time" id="attendance_end_time" name="attendance_end_time" value="{{ old('attendance_end_time') }}" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                @error('attendance_end_time')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('admin.mass-schedules.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancel</a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create Mass Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function toggleMassOrder() {
            const massType = document.getElementById('type').value;
            const massOrderContainer = document.getElementById('mass_order_container');
            
            if (massType === 'sunday_mass') {
                massOrderContainer.style.display = 'block';
            } else {
                massOrderContainer.style.display = 'none';
            }
        }
        
        // Run on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleMassOrder();
            
            // Set default times based on selected mass order
            document.getElementById('mass_order').addEventListener('change', function() {
                const massOrder = this.value;
                const startTimeInput = document.getElementById('start_time');
                const endTimeInput = document.getElementById('end_time');
                const attendanceStartInput = document.getElementById('attendance_start_time');
                const attendanceEndInput = document.getElementById('attendance_end_time');
                
                switch(massOrder) {
                    case 'first':
                        startTimeInput.value = '05:30';
                        endTimeInput.value = '06:30';
                        attendanceStartInput.value = '05:30';
                        attendanceEndInput.value = '07:29';
                        break;
                    case 'second':
                        startTimeInput.value = '07:30';
                        endTimeInput.value = '08:30';
                        attendanceStartInput.value = '07:30';
                        attendanceEndInput.value = '08:59';
                        break;
                    case 'third':
                        startTimeInput.value = '09:00';
                        endTimeInput.value = '10:00';
                        attendanceStartInput.value = '09:00';
                        attendanceEndInput.value = '10:30';
                        break;
                    case 'fourth':
                        startTimeInput.value = '17:00';
                        endTimeInput.value = '18:00';
                        attendanceStartInput.value = '17:00';
                        attendanceEndInput.value = '18:30';
                        break;
                }
            });
        });
    </script>
</x-app-layout> 