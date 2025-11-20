<x-guest-layout>
    <h1 class="text-lg font-semibold mb-4">Set a new password</h1>

    @if (session('status'))
        <div class="mb-3 text-sm text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        @if (! auth()->user()->force_password_reset)
            <div class="mb-4">
                <x-input-label for="current_password" value="Current password" />
                <x-text-input id="current_password" name="current_password" type="password"
                              class="mt-1 w-full" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
            </div>
        @endif

        <div class="mb-4">
            <x-input-label for="password" value="New password" />
            <x-text-input id="password" name="password" type="password"
                          class="mt-1 w-full" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-6">
            <x-input-label for="password_confirmation" value="Confirm new password" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                          class="mt-1 w-full" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button>Save new password</x-primary-button>
    </form>
</x-guest-layout>