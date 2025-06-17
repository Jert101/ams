@php
    $role = auth()->user()->role->name ?? 'guest';
    $roleLower = strtolower($role);
    
    // Define role-specific gradients
    $roleGradients = [
        'admin' => 'from-red-700 to-red-800',
        'officer' => 'from-cyan-600 to-cyan-800',
        'secretary' => 'from-indigo-600 to-indigo-800',
        'member' => 'from-green-600 to-green-800',
        'guest' => 'from-gray-600 to-gray-800'
    ];
    
    $gradient = $roleGradients[$roleLower] ?? $roleGradients['guest'];
@endphp

<!-- Sidebar Navigation -->
<div class="h-full bg-white border-r border-gray-200 role-{{ $roleLower }} flex flex-col">

    <!-- Sidebar Header -->
    <div class="sidebar-header p-2 bg-gradient-to-r {{ $gradient }} text-white border-b border-yellow-400 role-{{ $roleLower }}">
        <div class="flex items-center justify-between">
            <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : (Auth::user()->isOfficer() ? route('officer.dashboard') : (Auth::user()->isSecretary() ? route('secretary.dashboard') : route('member.dashboard'))) }}" class="flex items-center">
                @if(file_exists(public_path('kofa.png')))
                    <img src="{{ asset('kofa.png') }}" alt="KofA Logo" class="h-5 w-5">
                @else
                    <div class="bg-yellow-400 p-1 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                @endif
                <span class="ml-1 text-sm font-bold text-yellow-400">
                    AMS
                </span>
            </a>
            
            <!-- Close button for mobile -->
            <button class="sidebar-close text-white p-0.5 rounded-full hover:bg-opacity-20 hover:bg-white lg:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Sidebar User Info -->
    <div class="flex items-center p-1 border-b border-gray-200">
        @php
            $photoPath = Auth::user()->profile_photo_path;
            $defaultImage = asset('img/kofa.png');
            $photoUrl = $photoPath && $photoPath !== 'kofa.png'
                ? asset('/uploads/' . $photoPath) . '?v=' . time()
                : $defaultImage;
        @endphp
        <img src="{{ $photoUrl }}" alt="{{ Auth::user()->name }}'s profile photo" class="h-6 w-6 object-cover rounded-full border border-yellow-400" onerror="this.src='{{ $defaultImage }}';">
        <div class="ml-1">
            <div class="text-[10px] font-medium text-gray-900">{{ Auth::user()->name }}</div>
            <div class="text-[8px] text-gray-600">ID: {{ Auth::user()->user_id }}</div>
        </div>
    </div>

    <!-- Sidebar Content -->
    <div class="flex-1 flex flex-col overflow-y-auto">
        <nav class="flex-1 px-4 py-4 space-y-1">
            <!-- Improved Font Styles -->
            <style>
                /* Sidebar font improvements */
                .sidebar-link {
                    font-weight: 500;
                    letter-spacing: 0.01em;
                    text-rendering: optimizeLegibility;
                    -webkit-font-smoothing: antialiased;
                    -moz-osx-font-smoothing: grayscale;
                }
                
                .sidebar-link svg {
                    stroke-width: 2px;
                }
                
                .sidebar-link.active {
                    font-weight: 600;
                }
                
                .sidebar-header span {
                    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                }
                
                /* Mobile enhancements */
                @media (max-width: 768px) {
                    .sidebar-link {
                        padding: 0.75rem 0.5rem;
                        margin-bottom: 0.25rem;
                    }
                    
                    nav.space-y-1 > div {
                        margin-bottom: 1rem;
                    }
                }
            </style>
            
            <!-- Common Links -->
            <div class="space-y-1">
                @if(Auth::user()->isAdmin())
                    <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        {{ __('Dashboard') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        {{ __('User Management') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('admin.approvals.index')" :active="request()->routeIs('admin.approvals.*')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <div class="flex items-center justify-between w-full">
                            <span>{{ __('Pending Registrations') }}</span>
                            @php
                                $pendingCount = \App\Models\User::where('approval_status', 'pending')->count();
                            @endphp
                            @if($pendingCount > 0)
                                <span class="bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full ml-2">{{ $pendingCount }}</span>
                            @endif
                        </div>
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        {{ __('Role Management') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('admin.events.index')" :active="request()->routeIs('admin.events.*')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ __('Event Management') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ __('System Settings') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('admin.election.index')" :active="request()->routeIs('admin.election.*')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        {{ __('Election Management') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('admin.qrcode.manage')" :active="request()->routeIs('admin.qrcode.*')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        {{ __('QR Code Management') }}
                    </x-sidebar-link>
                @elseif(Auth::user()->isOfficer())
                    <x-sidebar-link :href="route('officer.dashboard')" :active="request()->routeIs('officer.dashboard')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        {{ __('Dashboard') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('officer.scan')" :active="request()->routeIs('officer.scan')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        {{ __('Scan QR') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('officer.events.index')" :active="request()->routeIs('officer.events.*')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ __('Events') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('election.index')" :active="request()->routeIs('election.*')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        {{ __('Elections') }}
                    </x-sidebar-link>
                @elseif(Auth::user()->isSecretary())
                    <x-sidebar-link :href="route('secretary.dashboard')" :active="request()->routeIs('secretary.dashboard')" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 sidebar-link">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        {{ __('Dashboard') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('secretary.members.index')" :active="request()->routeIs('secretary.members.*')" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 sidebar-link">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        {{ __('Members') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('secretary.reports.index')" :active="request()->routeIs('secretary.reports.*')" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 sidebar-link">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ __('Reports') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('secretary.notifications.index')" :active="request()->routeIs('secretary.notifications.*')" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 sidebar-link">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        {{ __('Notifications') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('secretary.absences.index')" :active="request()->routeIs('secretary.absences.*')" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 sidebar-link">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('Absences') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('election.index')" :active="request()->routeIs('election.*')" class="flex items-center px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200 sidebar-link">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        {{ __('Elections') }}
                    </x-sidebar-link>
                @else
                    <x-sidebar-link :href="route('member.dashboard')" :active="request()->routeIs('member.dashboard')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        {{ __('Dashboard') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('member.attendances.index')" :active="request()->routeIs('member.attendances.index')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        {{ __('My Attendances') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('member.attendances.create')" :active="request()->routeIs('member.attendances.create')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ __('Submit Attendance') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('member.attendances.monthly')" :active="request()->routeIs('member.attendances.monthly')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        {{ __('Monthly Report') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('member.profile')" :active="request()->routeIs('member.profile')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ __('Profile') }}
                    </x-sidebar-link>
                    
                    <x-sidebar-link :href="route('election.index')" :active="request()->routeIs('election.*')">
                        <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        {{ __('Elections') }}
                    </x-sidebar-link>
                @endif
            </div>
            
            <!-- User Settings Links -->
            <div class="mt-6">
                <a href="{{ route('profile.show') }}" class="flex items-center px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-red-700 rounded-md transition duration-150 ease-in-out sidebar-link">
                    <svg class="mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Profile Settings
                </a>
            </div>
        </nav>
    </div>
</div>