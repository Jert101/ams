@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
        <!-- QR Code Header -->
        <div class="bg-gradient-to-r from-red-700 to-red-800 p-4 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">KofA AMS</h1>
                    <p class="text-sm">Member Identification</p>
                </div>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Member Info -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="flex-shrink-0 mr-4">
                    <div class="h-16 w-16 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center text-red-700 dark:text-red-300 text-xl font-bold">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ $user->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">Member ID: {{ $user->id }}</p>
                </div>
            </div>
        </div>
        
        <!-- QR Code Display -->
        <div class="p-6 flex flex-col items-center">
            <div class="mb-4">
                <div class="qr-code-container bg-white p-4 rounded-lg shadow-sm">
                    {!! $qrCode !!}
                </div>
            </div>
            <p class="text-sm text-center text-gray-600 dark:text-gray-400 mb-2">Scan this QR code for event attendance</p>
            <p class="text-xs text-center text-gray-500 dark:text-gray-500">Generated on {{ now()->format('M d, Y h:i A') }}</p>
        </div>
        
        <!-- Footer -->
        <div class="bg-gray-100 dark:bg-gray-900 p-4 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">This QR code is unique to you. Do not share it with others.</p>
            <a href="{{ route('member.dashboard') }}" class="inline-block mt-2 text-red-700 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </span>
            </a>
        </div>
    </div>
</div>
@endsection
