@extends('layouts.admin-app')

@section('content')
<div class="w-full py-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-red-700 mb-2">System Settings</h1>
        <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
            <i class="bi bi-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>
    
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Application Settings</h2>
            
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label for="app_name" class="block text-gray-700 text-sm font-bold mb-2">Application Name:</label>
                        <input type="text" name="app_name" id="app_name" value="{{ old('app_name', $settings['app_name']) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('app_name') border-red-500 @enderror" required>
                        @error('app_name')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="col-span-2">
                        <label for="app_logo" class="block text-gray-700 text-sm font-bold mb-2">Application Logo:</label>
                        @if(file_exists(public_path('kofa.png')))
                            <div class="mb-2">
                                <img src="{{ asset('kofa.png') }}" alt="Application Logo" class="h-16 w-auto">
                            </div>
                        @endif
                        <input type="file" name="app_logo" id="app_logo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('app_logo') border-red-500 @enderror" accept="image/*">
                        <p class="text-gray-500 text-xs mt-1">Leave empty to keep current logo.</p>
                        @error('app_logo')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="app_timezone" class="block text-gray-700 text-sm font-bold mb-2">Timezone:</label>
                        <select name="app_timezone" id="app_timezone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('app_timezone') border-red-500 @enderror" required>
                            @foreach(['UTC', 'Asia/Manila', 'Asia/Singapore', 'America/New_York', 'Europe/London'] as $timezone)
                                <option value="{{ $timezone }}" {{ old('app_timezone', $settings['app_timezone']) == $timezone ? 'selected' : '' }}>{{ $timezone }}</option>
                            @endforeach
                        </select>
                        @error('app_timezone')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="app_locale" class="block text-gray-700 text-sm font-bold mb-2">Language:</label>
                        <select name="app_locale" id="app_locale" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('app_locale') border-red-500 @enderror" required>
                            @foreach(['en' => 'English', 'es' => 'Spanish', 'fr' => 'French'] as $code => $language)
                                <option value="{{ $code }}" {{ old('app_locale', $settings['app_locale']) == $code ? 'selected' : '' }}>{{ $language }}</option>
                            @endforeach
                        </select>
                        @error('app_locale')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="flex items-center justify-end mt-6">
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">System Maintenance</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">Clear Application Cache</h3>
                    <p class="text-gray-600 mb-4">Clear all cached data to ensure the application is using the most recent settings and data.</p>
                    <form action="{{ route('admin.settings.clear-cache') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out">
                            Clear Cache
                        </button>
                    </form>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-2">System Information</h3>
                    <ul class="text-gray-600 space-y-2">
                        <li><span class="font-medium">PHP Version:</span> {{ phpversion() }}</li>
                        <li><span class="font-medium">Laravel Version:</span> {{ app()->version() }}</li>
                        <li><span class="font-medium">Server:</span> {{ $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' }}</li>
                        <li><span class="font-medium">Environment:</span> {{ app()->environment() }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
