<x-auth-layout title="Choose a new password">
    <div class="space-y-2 text-center">
        <h1 class="text-3xl font-semibold text-ink">Choose a new password</h1>
        <p class="text-body-sm text-pewter">At least 8 characters.</p>
    </div>

    <form action="{{ route('password.update') }}" method="POST" class="w-full space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div>
            <label for="email" class="sr-only">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $email) }}" placeholder="Your email" autocomplete="email"
                class="input @error('email') input-error @enderror" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="sr-only">New password</label>
            <input type="password" id="password" name="password" placeholder="New password" autocomplete="new-password" autofocus
                class="input @error('password') input-error @enderror" required>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="sr-only">Confirm new password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" autocomplete="new-password" class="input" required>
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-full">Change password</button>
    </form>
</x-auth-layout>
