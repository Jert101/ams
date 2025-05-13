<section>
    <header>
        <h2 class="text-lg font-medium text-[#B22234] dark:text-[#FFD700]">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="mb-4">
            <label for="update_password_current_password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#B22234] focus:ring focus:ring-[#B22234] focus:ring-opacity-50" autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="update_password_password" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#B22234] focus:ring focus:ring-[#B22234] focus:ring-opacity-50" autocomplete="new-password">
            @error('password', 'updatePassword')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="update_password_password_confirmation" class="block font-medium text-sm text-gray-700 dark:text-gray-300">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-[#B22234] focus:ring focus:ring-[#B22234] focus:ring-opacity-50" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="px-4 py-2 bg-[#B22234] dark:bg-[#B22234] border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-red-800 dark:hover:bg-red-800 focus:bg-red-800 dark:focus:bg-red-800 active:bg-red-900 dark:active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
