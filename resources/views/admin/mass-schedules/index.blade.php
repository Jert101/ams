<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mass Schedules') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Sunday Mass Schedules</h3>
                    <div>
                        <a href="{{ route('admin.mass-schedules.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Special Mass
                        </a>
                    </div>
                </div>

                <!-- Sunday Mass Setup Form -->
                <div class="bg-gray-50 p-4 rounded-lg mb-8 border border-gray-200">
                    <h4 class="text-md font-medium mb-4">Configure Sunday Mass Schedules</h4>
                    <form action="{{ route('admin.mass-schedules.setup-sunday') }}" method="POST">
                        @csrf
                        
                        <!-- First Mass -->
                        <div class="mb-6 p-4 bg-white rounded-lg shadow">
                            <h5 class="text-md font-semibold mb-3">First Mass (5:30 AM - 6:30 AM)</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                    <input type="time" name="first_mass_start" value="05:30" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                    <input type="time" name="first_mass_end" value="06:30" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Attendance Start Time</label>
                                    <input type="time" name="first_mass_attendance_start" value="05:30" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Attendance End Time</label>
                                    <input type="time" name="first_mass_attendance_end" value="07:29" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Second Mass -->
                        <div class="mb-6 p-4 bg-white rounded-lg shadow">
                            <h5 class="text-md font-semibold mb-3">Second Mass (7:30 AM - 8:30 AM)</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                    <input type="time" name="second_mass_start" value="07:30" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                    <input type="time" name="second_mass_end" value="08:30" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Attendance Start Time</label>
                                    <input type="time" name="second_mass_attendance_start" value="07:30" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Attendance End Time</label>
                                    <input type="time" name="second_mass_attendance_end" value="08:59" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Third Mass -->
                        <div class="mb-6 p-4 bg-white rounded-lg shadow">
                            <h5 class="text-md font-semibold mb-3">Third Mass (9:00 AM - 10:00 AM)</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                    <input type="time" name="third_mass_start" value="09:00" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                    <input type="time" name="third_mass_end" value="10:00" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Attendance Start Time</label>
                                    <input type="time" name="third_mass_attendance_start" value="09:00" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Attendance End Time</label>
                                    <input type="time" name="third_mass_attendance_end" value="10:30" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Fourth Mass -->
                        <div class="mb-6 p-4 bg-white rounded-lg shadow">
                            <h5 class="text-md font-semibold mb-3">Fourth Mass (5:00 PM - 6:00 PM)</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                                    <input type="time" name="fourth_mass_start" value="17:00" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                    <input type="time" name="fourth_mass_end" value="18:00" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Attendance Start Time</label>
                                    <input type="time" name="fourth_mass_attendance_start" value="17:00" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Attendance End Time</label>
                                    <input type="time" name="fourth_mass_attendance_end" value="18:30" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block w-full">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Save Sunday Mass Schedule
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Current Sunday Mass Schedules -->
                <div class="mb-8">
                    <h4 class="text-md font-medium mb-4">Current Sunday Mass Schedules</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mass</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Window</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sundaySchedules as $order => $schedules)
                                    @foreach($schedules as $schedule)
                                        <tr>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ ucfirst($order) }} Mass</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $schedule->formatted_start_time }} - {{ $schedule->formatted_end_time }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">{{ $schedule->formatted_attendance_start_time }} - {{ $schedule->formatted_attendance_end_time }}</td>
                                            <td class="py-2 px-4 border-b border-gray-200">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $schedule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $schedule->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 px-4 border-b border-gray-200 text-center text-gray-500">
                                            No Sunday mass schedules configured yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Special Masses -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Special Masses</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Window</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($specialMasses as $mass)
                                    <tr>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $mass->event->name }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $mass->event->date->format('M d, Y') }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $mass->formatted_start_time }} - {{ $mass->formatted_end_time }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">{{ $mass->formatted_attendance_start_time }} - {{ $mass->formatted_attendance_end_time }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $mass->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $mass->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.mass-schedules.edit', $mass) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                                <form method="POST" action="{{ route('admin.mass-schedules.destroy', $mass) }}" onsubmit="return confirm('Are you sure you want to delete this mass schedule?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 px-4 border-b border-gray-200 text-center text-gray-500">
                                            No special masses scheduled.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $specialMasses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Attendance Time Validation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to check if the current time is within the attendance window
            function checkAttendanceWindow() {
                const now = new Date();
                const currentHour = now.getHours();
                const currentMinute = now.getMinutes();
                const currentTime = currentHour * 60 + currentMinute;
                
                // Define attendance windows (in minutes from midnight)
                const windows = {
                    first: { start: 5*60+30, end: 7*60+29 },
                    second: { start: 7*60+30, end: 8*60+59 },
                    third: { start: 9*60, end: 10*60+30 },
                    fourth: { start: 17*60, end: 18*60+30 }
                };
                
                // Check each window and update UI accordingly
                for (const [mass, window] of Object.entries(windows)) {
                    const isActive = currentTime >= window.start && currentTime <= window.end;
                    // Update UI elements if needed
                    // This is just a placeholder - you would need to add elements with these IDs
                    const element = document.getElementById(`${mass}-mass-status`);
                    if (element) {
                        element.textContent = isActive ? 'Active' : 'Inactive';
                        element.className = isActive ? 'text-green-600' : 'text-red-600';
                    }
                }
            }
            
            // Check initially and then every minute
            checkAttendanceWindow();
            setInterval(checkAttendanceWindow, 60000);
        });
    </script>
</x-app-layout> 