@extends('layouts.admin-app')

@section('content')
<div class="w-full py-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-red-700 mb-2 md:mb-0">Edit User: {{ $user->name }}</h1>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Users
        </a>
    </div>
    
    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-red-100">
        <div class="bg-red-50 px-6 py-4 border-b border-red-100">
            <h2 class="text-xl font-semibold text-red-700">User Information</h2>
            <p class="text-sm text-gray-600">Update the user's personal information and account settings</p>
        </div>
        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="p-6" id="user-edit-form">
            @csrf
            @method('PUT')
            <!-- Add hidden field for user ID to ensure proper routing -->
            <input type="hidden" name="user_id" value="{{ $user->id }}">
            
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <strong class="font-bold">Please fix the following errors:</strong>
                    <ul class="list-disc ml-5 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div>
                    <label for="name" class="block text-red-700 text-sm font-semibold mb-2">Name:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="pl-10 shadow-sm border border-red-200 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('name') border-red-500 @enderror" required placeholder="Enter user's full name">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="email" class="block text-red-700 text-sm font-semibold mb-2">Email:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="pl-10 shadow-sm border border-red-200 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('email') border-red-500 @enderror" required placeholder="Enter user's email address">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="password" class="block text-red-700 text-sm font-semibold mb-2">Password (leave blank to keep current):</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" class="pl-10 shadow-sm border border-red-200 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('password') border-red-500 @enderror" placeholder="Enter new password">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-red-700 text-sm font-semibold mb-2">Confirm Password:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="pl-10 shadow-sm border border-red-200 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Confirm new password">
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="role_id" class="block text-red-700 text-sm font-semibold mb-2">Role:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <select name="role_id" id="role_id" class="pl-10 shadow-sm border border-red-200 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('role_id') border-red-500 @enderror" required>
                            <option value="">Select a role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" {{ (old('role_id', $user->role_id) == $role->id) ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="mobile_number" class="block text-red-700 text-sm font-semibold mb-2">Mobile Number:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                        </div>
                        <input type="text" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $user->mobile_number) }}" class="pl-10 shadow-sm border border-red-200 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('mobile_number') border-red-500 @enderror" placeholder="Enter mobile number">
                        @error('mobile_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label for="date_of_birth" class="block text-red-700 text-sm font-semibold mb-2">Date of Birth:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}" class="pl-10 shadow-sm border border-red-200 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('date_of_birth') border-red-500 @enderror">
                        @error('date_of_birth')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <label for="address" class="block text-red-700 text-sm font-semibold mb-2">Address:</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}" class="pl-10 shadow-sm border border-red-200 rounded-md w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('address') border-red-500 @enderror" placeholder="Enter full address">
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mb-6">
                <label for="profile_photo" class="block text-red-700 text-sm font-semibold mb-2">Profile Photo:</label>
                <div class="flex items-center">
                    <div class="mr-4">
                        <div class="h-24 w-24 rounded-full border-4 border-red-200 shadow-md overflow-hidden">
                            @php
                                $photoPath = $user->profile_photo_path;
                                $defaultImage = asset('img/kofa.png');
                                
                                if ($photoPath && $photoPath !== 'kofa.png') {
                                    // For InfinityFree, use direct public path
                                    $photoUrl = url($photoPath) . '?v=' . time();
                                } else {
                                    $photoUrl = $defaultImage;
                                }
                            @endphp
                            
                            <img src="{{ $photoUrl }}" 
                                alt="{{ $user->name }}'s profile photo" 
                                class="h-full w-full object-cover"
                                onerror="this.src='{{ $defaultImage }}'; console.log('Profile photo load failed, using default');">
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="relative flex items-center">
                            <label for="profile_photo" class="cursor-pointer bg-white border border-red-200 rounded-md px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                </svg>
                                Choose Photo
                            </label>
                            <input type="file" name="profile_photo" id="profile_photo" class="hidden" accept="image/*">
                            <span class="ml-3 text-sm text-gray-500" id="file-name">No file selected</span>
                        </div>
                        <p class="text-gray-500 text-xs mt-2">Leave empty to keep current profile photo. Maximum file size: 2MB.</p>
                        @error('profile_photo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="border-t border-red-100 pt-6 mt-6">
                <div class="flex items-center justify-end">
                    <a href="{{ route('admin.users.show', $user) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mr-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('profile_photo');
        const fileNameDisplay = document.getElementById('file-name');
        const form = document.getElementById('user-edit-form');
        
        // Handle file input change
        if (fileInput && fileNameDisplay) {
            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    fileNameDisplay.textContent = fileInput.files[0].name;
                } else {
                    fileNameDisplay.textContent = 'No file selected';
                }
            });
        }
        
        // Add form submission handler
        if (form) {
            form.addEventListener('submit', function(event) {
                // Ensure the form method is POST and the _method hidden field is set to PUT
                const methodField = form.querySelector('input[name="_method"]');
                if (!methodField || methodField.value !== 'PUT') {
                    const newMethodField = document.createElement('input');
                    newMethodField.type = 'hidden';
                    newMethodField.name = '_method';
                    newMethodField.value = 'PUT';
                    form.appendChild(newMethodField);
                }
                
                console.log('Form is being submitted with method: ' + (methodField ? methodField.value : 'POST'));
                
                // Continue with form submission
                return true;
            });
        }
    });
</script>

<!-- Immediate fix for profile photos -->
<script>
    // Function to fix profile photos
    function fixProfilePhotos() {
        console.log('Running immediate profile photo fix');
        
        // Find profile photo images
        var profileImg = document.querySelector('.profile-user-img');
        if (profileImg) {
            console.log('Found profile image:', profileImg.src);
            
            // Make sure the image is visible
            profileImg.style.display = 'block';
            profileImg.style.visibility = 'visible';
            profileImg.style.opacity = '1';
            
            // If the image is not loading, try a direct URL
            if (!profileImg.complete || profileImg.naturalWidth === 0) {
                var filename = profileImg.src.split('/').pop().split('?')[0];
                profileImg.src = 'https://ckpkofa-network.ct.ws/profile-photos/' + filename + '?v=' + new Date().getTime();
                console.log('Updated image source to:', profileImg.src);
            }
            
            // Add error handler
            profileImg.onerror = function() {
                console.log('Image failed to load, using default');
                this.src = '/img/kofa.png';
            };
        }
    }
    
    // Run immediately
    fixProfilePhotos();
    
    // Run again after a delay
    setTimeout(fixProfilePhotos, 500);
    setTimeout(fixProfilePhotos, 2000);
</script>
@endsection
