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

<!-- Sidebar -->
<div class="h-full bg-white border-r border-gray-200">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 px-4 bg-red-700">
        <img src="{{ asset('img/kofa.png') }}" alt="KofA Logo" class="h-10">
    </div>

    <!-- User Profile -->
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0">
                @if(auth()->user()->profile_photo_url)
                    <img src="{{ auth()->user()->profile_photo_url }}" alt="Profile photo" class="w-10 h-10 rounded-full object-cover">
                @else
                    <img src="{{ asset('img/default-profile.png') }}" alt="Default profile" class="w-10 h-10 rounded-full object-cover">
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-gray-500 truncate">
                    {{ auth()->user()->email }}
                </p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-4 px-2 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-red-700 text-white' : 'text-gray-600 hover:bg-red-50 hover:text-red-700' }}">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-red-700' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>

        <!-- Election Management -->
        <div x-data="{ open: {{ request()->routeIs('admin.election.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.election.*') ? 'bg-red-700 text-white' : 'text-gray-600 hover:bg-red-50 hover:text-red-700' }}">
                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.election.*') ? 'text-white' : 'text-gray-400 group-hover:text-red-700' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                </svg>
                <span class="flex-1">Election</span>
                <svg class="ml-3 h-5 w-5 transform transition-transform duration-150" :class="{'rotate-90': open}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            <div x-show="open" class="mt-1 space-y-1" style="display: none;">
                <a href="{{ route('admin.election.settings') }}" class="group flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.election.settings') ? 'bg-red-700 text-white' : 'text-gray-600 hover:bg-red-50 hover:text-red-700' }}">
                    Settings
                </a>
                <a href="{{ route('admin.election.positions') }}" class="group flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.election.positions') ? 'bg-red-700 text-white' : 'text-gray-600 hover:bg-red-50 hover:text-red-700' }}">
                    Positions
                </a>
                <a href="{{ route('admin.election.candidates') }}" class="group flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.election.candidates') ? 'bg-red-700 text-white' : 'text-gray-600 hover:bg-red-50 hover:text-red-700' }}">
                    Candidates
                </a>
                <a href="{{ route('admin.election.results') }}" class="group flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.election.results') ? 'bg-red-700 text-white' : 'text-gray-600 hover:bg-red-50 hover:text-red-700' }}">
                    Results
                </a>
                <a href="{{ route('admin.election.archives') }}" class="group flex items-center pl-10 pr-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.election.archives') ? 'bg-red-700 text-white' : 'text-gray-600 hover:bg-red-50 hover:text-red-700' }}">
                    Archives
                </a>
            </div>
        </div>

        <!-- User Management -->
        <a href="{{ route('admin.users.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.users.*') ? 'bg-red-700 text-white' : 'text-gray-600 hover:bg-red-50 hover:text-red-700' }}">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-400 group-hover:text-red-700' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Users
        </a>

        <!-- Settings -->
        <a href="{{ route('admin.settings.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('admin.settings.*') ? 'bg-red-700 text-white' : 'text-gray-600 hover:bg-red-50 hover:text-red-700' }}">
            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.settings.*') ? 'text-white' : 'text-gray-400 group-hover:text-red-700' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Settings
        </a>
    </nav>

    <!-- Logout -->
    <div class="mt-auto p-4 border-t border-gray-200">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full group flex items-center px-2 py-2 text-sm font-medium text-red-700 rounded-md hover:bg-red-50">
                <svg class="mr-3 h-5 w-5 text-red-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush