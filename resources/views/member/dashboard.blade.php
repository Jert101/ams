<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Member Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- React Dashboard -->
            <div 
                id="member-dashboard-root"
                data-react-root 
                data-component="MemberDashboard"
                data-props='{
                    "presentCount": {{ $presentCount }},
                    "absentCount": {{ $absentCount }},
                    "excusedCount": {{ $excusedCount }},
                    "attendanceRate": {{ $attendanceRate }},
                    "nextEvent": @json($nextEvent),
                    "recentAttendances": @json($recentAttendances)
                }'
            ></div>

            <!-- Fallback HTML Content (displayed if React fails) -->
            <div id="memberdashboard-fallback-content" style="display: none;" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-bold mb-4">Attendance Summary</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <p class="text-sm font-medium text-green-600">Present</p>
                                    <p class="mt-1 text-3xl font-semibold text-green-800">{{ $presentCount }}</p>
                                </div>
                                <div class="text-center p-4 bg-red-50 rounded-lg">
                                    <p class="text-sm font-medium text-red-600">Absent</p>
                                    <p class="mt-1 text-3xl font-semibold text-red-800">{{ $absentCount }}</p>
                                </div>
                                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                    <p class="text-sm font-medium text-yellow-600">Excused</p>
                                    <p class="mt-1 text-3xl font-semibold text-yellow-800">{{ $excusedCount }}</p>
                                </div>
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <p class="text-sm font-medium text-blue-600">Attendance Rate</p>
                                    <p class="mt-1 text-3xl font-semibold text-blue-800">{{ $attendanceRate }}%</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $attendanceRate }}%"></div>
                                </div>
                                <p class="mt-2 text-sm text-gray-600">
                                    Total Events: {{ $presentCount + $absentCount + $excusedCount }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-bold mb-4">Member Status</h3>
                            <div class="text-center py-6">
                                <p class="text-2xl font-bold text-red-700">{{ auth()->user()->name }}</p>
                                <p class="text-gray-500 mt-2">{{ auth()->user()->role->name }}</p>
                                <p class="text-gray-500">Member since {{ auth()->user()->created_at->format('F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-bold mb-4">Next Event</h3>
                            @if($nextEvent)
                                <div class="space-y-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">{{ $nextEvent['name'] }}</h3>
                                        <p class="text-gray-600">{{ $nextEvent['description'] }}</p>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-gray-700">{{ date('F j, Y', strtotime($nextEvent['date'])) }}</span>
                                    </div>
                                    @if(isset($nextEvent['time']))
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span class="text-gray-700">{{ $nextEvent['time'] }}</span>
                                        </div>
                                    @endif
                                    @if(isset($nextEvent['location']))
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="text-gray-700">{{ $nextEvent['location'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <p class="text-gray-500">No upcoming events scheduled</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- QR Code Section -->
                        @if(isset($qrCode))
                        <div class="bg-white rounded-lg shadow p-6 mt-6">
                            <h3 class="text-lg font-bold mb-4">Your QR Code</h3>
                            <div class="text-center">
                                <div class="mb-4 flex justify-center">
                                    <div id="qrcode-container-dashboard"></div>
                                </div>
                                <p class="text-gray-600 mb-1 text-sm">Code: <span class="font-mono">{{ $qrCode->code }}</span></p>
                                <div class="mt-4 flex justify-center space-x-2">
                                    <a href="{{ route('qrcode.show') }}" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                        View Full QR
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                    <a href="{{ route('qrcode.print') }}" class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm">
                                        Print ID Card
                                    </a>
                                    @endif
                                </div>
                                <p class="mt-2 text-xs text-gray-500">Click "View Full QR" to see your complete QR code</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-bold mb-4">Recent Attendance</h3>
                            @if(count($recentAttendances) > 0)
                                <div class="overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Event
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Date
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Status
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($recentAttendances as $attendance)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $attendance['event']['name'] ?? 'Unknown Event' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ isset($attendance['event']['date']) ? date('M d, Y', strtotime($attendance['event']['date'])) : 'Unknown Date' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        @php
                                                            $statusClass = '';
                                                            switch($attendance['status']) {
                                                                case 'present':
                                                                    $statusClass = 'bg-green-100 text-green-800';
                                                                    break;
                                                                case 'absent':
                                                                    $statusClass = 'bg-red-100 text-red-800';
                                                                    break;
                                                                case 'excused':
                                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                                    break;
                                                                default:
                                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                            }
                                                        @endphp
                                                        <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                                                            {{ ucfirst($attendance['status']) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <a href="/member/attendances" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                        View All
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <p class="text-gray-500">No attendance records found</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
@if(isset($qrCode))
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if container exists (might not be visible in the React version)
        const container = document.getElementById('qrcode-container-dashboard');
        if (container) {
            new QRCode(container, {
                text: "{{ $qrCode->code }}",
                width: 100,
                height: 100,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    });
</script>
@endif
@endpush 