@extends('layouts.member-app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-[#B22234] mb-6">My Profile</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Left Column - Profile Information -->
        <div class="md:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700]">
                <div class="flex flex-col items-center">
                    <!-- Profile Photo -->
                    <div class="mb-4 relative">
                        <div class="h-32 w-32 rounded-full border-4 border-[#FFD700] overflow-hidden">
                            <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover profile-user-img" onerror="this.onerror=null;this.src='{{ asset('img/kofa.png') }}';">
                        </div>
                    </div>
                    
                    <!-- User Info -->
                    <h3 class="text-xl font-bold text-gray-800 mb-1">{{ Auth::user()->name }}</h3>
                    <p class="text-gray-600 mb-2">{{ Auth::user()->email }}</p>
                    <p class="text-sm text-gray-500 mb-2">{{ Auth::user()->role ? Auth::user()->role->name : 'No Role Assigned' }}</p>
                    <p class="text-sm font-semibold bg-gray-100 text-gray-800 px-3 py-1 rounded-full mb-3">ID: {{ Auth::user()->user_id }}</p>
                    
                    <!-- Account Status -->
                    @if(Auth::user()->approval_status === 'approved')
                        <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-1 rounded-full text-xs font-medium flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Account Approved
                        </div>
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
            <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700] mb-6">
                <h2 class="text-xl font-medium text-[#B22234] mb-4">Update Profile</h2>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-[#B22234] text-sm font-semibold mb-2">Name:</label>
                        <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" class="shadow appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-[#B22234]">
                        @error('name')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-[#B22234] text-sm font-semibold mb-2">Email:</label>
                        <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="shadow appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-[#B22234]">
                        @error('email')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Profile Photo -->
                    <div class="mb-4">
                        <label for="profile_photo" class="block text-[#B22234] text-sm font-semibold mb-2">Profile Photo:</label>
                        <div class="flex items-center">
                            <label class="flex items-center px-4 py-2 bg-white text-[#B22234] rounded-lg shadow-md border border-[#FFD700] cursor-pointer hover:bg-[#FFD700] hover:text-[#B22234] transition-colors duration-300">
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
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-[#B22234] hover:bg-[#8B0000] text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Update Password -->
            <div class="bg-white p-6 rounded-lg shadow border border-[#FFD700]">
                <h2 class="text-xl font-medium text-[#B22234] mb-4">Update Password</h2>
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Current Password -->
                    <div class="mb-4">
                        <label for="current_password" class="block text-[#B22234] text-sm font-semibold mb-2">Current Password:</label>
                        <input type="password" id="current_password" name="current_password" class="shadow appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-[#B22234]">
                        @error('current_password')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- New Password -->
                    <div class="mb-4">
                        <label for="password" class="block text-[#B22234] text-sm font-semibold mb-2">New Password:</label>
                        <input type="password" id="password" name="password" class="shadow appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-[#B22234]">
                        @error('password')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-[#B22234] text-sm font-semibold mb-2">Confirm Password:</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="shadow appearance-none border border-gray-300 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-[#B22234]">
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-[#B22234] hover:bg-[#8B0000] text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for file input display -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('profile_photo');
        const fileNameDisplay = document.getElementById('file-name');
        
        if (fileInput && fileNameDisplay) {
            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    fileNameDisplay.textContent = fileInput.files[0].name;
                } else {
                    fileNameDisplay.textContent = 'No file selected';
                }
            });
        }
    });
</script>
@endsection 