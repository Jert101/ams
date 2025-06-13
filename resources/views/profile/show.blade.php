@php
    if (Auth::user()->isAdmin()) {
        $layout = 'layouts.admin-app';
    } elseif (Auth::user()->isOfficer()) {
        $layout = 'layouts.officer-app';
    } elseif (Auth::user()->isSecretary()) {
        $layout = 'layouts.secretary-app';
    } elseif (Auth::user()->isMember()) {
        $layout = 'layouts.member-app';
    } else {
        $layout = 'layouts.app';
    }
@endphp

@extends($layout)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-red-700">{{ __('My Profile') }}</h1>
    </div>
    
    <!-- Status Messages -->
    @if (session('status') === 'profile-updated')
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p class="font-semibold">Profile updated successfully!</p>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p class="font-semibold">Password updated successfully!</p>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-semibold">Error:</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif
    
    <!-- Profile Information Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column - Profile Photo and Basic Info -->
        <div class="md:col-span-1">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-red-100 h-full">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                    <h2 class="text-xl font-semibold text-red-700">Profile Information</h2>
                </div>
                <div class="p-6 flex flex-col items-center">
                    <!-- Profile Photo -->
                    <div class="mb-4 relative">
                        <div class="h-32 w-32 rounded-full border-4 border-red-200 overflow-hidden">
                            @php
                                $photoPath = Auth::user()->profile_photo_path;
                                $photoUrl = empty($photoPath) ? asset('img/kofa.png') : 
                                            ($photoPath === 'kofa.png' ? asset('img/kofa.png') : 
                                            asset('profile-photos/' . basename($photoPath)));
                            @endphp
                            <img src="{{ $photoUrl }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover profile-user-img">
                        </div>
                    </div>
                    
                    <!-- User Info -->
                    <h3 class="text-xl font-bold text-gray-800 mb-1">{{ Auth::user()->name }}</h3>
                    <p class="text-gray-600 mb-2">{{ Auth::user()->email }}</p>
                    <p class="text-sm text-gray-500 mb-4">{{ Auth::user()->role ? Auth::user()->role->name : 'No Role Assigned' }}</p>
                    
                    <!-- Account Status -->
                    @if(Auth::user()->approval_status === 'approved')
                        <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-1 rounded-full text-xs font-medium flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Account Approved
                        </div>
                    @elseif(Auth::user()->approval_status === 'pending')
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-3 py-1 rounded-full text-xs font-medium flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Pending Approval
                        </div>
                    @elseif(Auth::user()->approval_status === 'rejected')
                        <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-1 rounded-full text-xs font-medium flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Account Rejected
                        </div>
                        @if(Auth::user()->rejection_reason)
                            <div class="mt-2 bg-red-50 border border-red-200 text-red-600 p-3 rounded-md text-sm">
                                <p class="font-semibold mb-1">Reason for rejection:</p>
                                <p>{{ Auth::user()->rejection_reason }}</p>
                            </div>
                        @endif
                    @endif
                    
                    <!-- KofA Logo -->
                    <div class="mt-6 mb-2">
                        @if(file_exists(public_path('kofa.png')))
                            <img src="{{ asset('kofa.png') }}" alt="KofA Logo" class="h-12 w-auto mx-auto">
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 text-center">CKP Knights of the Altar</p>
                </div>
            </div>
        </div>
        
        <!-- Right Column - Account Settings -->
        <div class="md:col-span-2">
            <!-- Update Profile Information -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-red-100 mb-6">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                    <h2 class="text-xl font-semibold text-red-700">Update Profile</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-red-700 text-sm font-semibold mb-2">Name:</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" class="pl-10 shadow appearance-none border border-red-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-red-500">
                            </div>
                            @error('name')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block text-red-700 text-sm font-semibold mb-2">Email:</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="pl-10 shadow appearance-none border border-red-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-red-500">
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Profile Photo - Only visible to admin users -->
                        @if(Auth::user()->isAdmin())
                        <div class="mb-4">
                            <label for="profile_photo" class="block text-red-700 text-sm font-semibold mb-2">Profile Photo:</label>
                            <div class="flex items-center">
                                <label class="flex items-center px-4 py-2 bg-white text-red-700 rounded-lg shadow-md border border-red-300 cursor-pointer hover:bg-red-50 transition-colors duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Choose Photo</span>
                                    <input type="file" id="profile_photo" name="profile_photo" class="hidden" accept="image/*">
                                </label>
                                <span id="file-name" class="ml-3 text-sm text-gray-500">No file selected</span>
                            </div>
                            @error('profile_photo')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                            
                            <!-- Added photo upload info for debugging -->
                            <div class="mt-2 text-xs text-gray-500">
                                <p>Note: Photo uploads require proper server configuration. If you're experiencing issues:</p>
                                <ul class="list-disc pl-5 mt-1">
                                    <li>Maximum upload size: {{ ini_get('upload_max_filesize') }}</li>
                                    <li>Storage symlink: {{ file_exists(public_path('storage')) ? '✓ Exists' : '✗ Missing' }}</li>
                                    <li>For troubleshooting, visit: <a href="{{ url('/storage-test.php') }}" class="text-blue-500 hover:underline" target="_blank">Storage Test</a></li>
                                </ul>
                            </div>
                        </div>
                        @endif
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Update Password -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-red-100 mb-6">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                    <h2 class="text-xl font-semibold text-red-700">Update Password</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Current Password -->
                        <div class="mb-4">
                            <label for="current_password" class="block text-red-700 text-sm font-semibold mb-2">Current Password:</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input type="password" id="current_password" name="current_password" class="pl-10 shadow appearance-none border border-red-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-red-500">
                            </div>
                            @error('current_password')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- New Password -->
                        <div class="mb-4">
                            <label for="password" class="block text-red-700 text-sm font-semibold mb-2">New Password:</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <input type="password" id="password" name="password" class="pl-10 shadow appearance-none border border-red-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-red-500">
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-red-700 text-sm font-semibold mb-2">Confirm Password:</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="pl-10 shadow appearance-none border border-red-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-red-500">
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Delete Account - Only visible to admin users -->
            @if(Auth::user()->isAdmin())
            <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-red-100">
                <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                    <h2 class="text-xl font-semibold text-red-700">Delete Account</h2>
                </div>
                <div class="p-6">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out flex items-center" onclick="document.getElementById('delete-account-modal').classList.remove('hidden')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Account
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Delete Account Modal - Only for admin users -->
    @if(Auth::user()->isAdmin())
    <div id="delete-account-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-lg w-full">
            <div class="bg-red-50 px-6 py-4 border-b border-red-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-red-700">Delete Account</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('delete-account-modal').classList.add('hidden')">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <form action="{{ route('profile.destroy') }}" method="POST">
                @csrf
                @method('DELETE')
                
                <div class="p-6">
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    <strong>Warning:</strong> This action cannot be undone. All your data will be permanently deleted.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-red-700 text-sm font-semibold mb-2">Password:</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" class="pl-10 shadow appearance-none border border-red-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-red-500" placeholder="Enter your password to confirm">
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete Account
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('delete-account-modal').classList.add('hidden')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

<!-- JavaScript for file input display -->
@if(Auth::user()->isAdmin())
<script>
    document.getElementById('profile_photo').addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : 'No file selected';
        document.getElementById('file-name').textContent = fileName;
    });
</script>
@endif
@endsection
