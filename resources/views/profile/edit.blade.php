@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="font-semibold text-2xl text-[#B22234] leading-tight">
                    {{ __('Profile') }}
                </h2>
            </div>
            
            <div class="space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow-md rounded-lg border-t-4 border-[#B22234]">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow-md rounded-lg border-t-4 border-[#B22234]">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow-md rounded-lg border-t-4 border-[#B22234]">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
