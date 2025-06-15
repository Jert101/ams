@extends('layouts.admin-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">Election Settings</h1>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('admin.election.updateSettings') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Enable/Disable Election -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_enabled" class="form-checkbox h-5 w-5 text-red-700" {{ $electionSetting->is_enabled ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700">Enable Election System</span>
                </label>
            </div>

            <!-- Date Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="candidacy_start_date">
                        Candidacy Start Date
                    </label>
                    <input type="datetime-local" name="candidacy_start_date" id="candidacy_start_date"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        value="{{ $electionSetting->candidacy_start_date ? date('Y-m-d\TH:i', strtotime($electionSetting->candidacy_start_date)) : '' }}">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="candidacy_end_date">
                        Candidacy End Date
                    </label>
                    <input type="datetime-local" name="candidacy_end_date" id="candidacy_end_date"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        value="{{ $electionSetting->candidacy_end_date ? date('Y-m-d\TH:i', strtotime($electionSetting->candidacy_end_date)) : '' }}">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="voting_start_date">
                        Voting Start Date
                    </label>
                    <input type="datetime-local" name="voting_start_date" id="voting_start_date"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        value="{{ $electionSetting->voting_start_date ? date('Y-m-d\TH:i', strtotime($electionSetting->voting_start_date)) : '' }}">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="voting_end_date">
                        Voting End Date
                    </label>
                    <input type="datetime-local" name="voting_end_date" id="voting_end_date"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        value="{{ $electionSetting->voting_end_date ? date('Y-m-d\TH:i', strtotime($electionSetting->voting_end_date)) : '' }}">
                </div>
            </div>

            <!-- Current Status -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Current Status</h3>
                <div class="bg-gray-100 p-4 rounded">
                    <p class="text-gray-700">
                        Status: <span class="font-semibold">{{ ucfirst($electionSetting->status) }}</span>
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        The status is automatically updated based on the dates above unless manual control is enabled.
                    </p>
                </div>
            </div>

            <!-- Manual Status Control -->
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Manual Status Control</h3>
                <div class="bg-gray-100 p-4 rounded">
                    <form action="{{ route('admin.election.changeStatus') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                                Set Status Manually
                            </label>
                            <select name="status" id="status" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="inactive" {{ $electionSetting->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="candidacy" {{ $electionSetting->status === 'candidacy' ? 'selected' : '' }}>Candidacy Period</option>
                                <option value="voting" {{ $electionSetting->status === 'voting' ? 'selected' : '' }}>Voting Period</option>
                                <option value="completed" {{ $electionSetting->status === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="ignore_dates" class="form-checkbox h-5 w-5 text-red-700" {{ $electionSetting->ignore_automatic_updates ? 'checked' : '' }}>
                                <span class="ml-2 text-gray-700">Ignore date-based automatic updates</span>
                            </label>
                        </div>
                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Update Status Manually
                        </button>
                    </form>
                </div>
            </div>

            <!-- Save Settings Button -->
            <div class="flex justify-end">
                <button type="submit" class="bg-red-700 hover:bg-red-800 text-white font-bold py-2 px-4 rounded">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 