<x-guest-layout title="Create account">
    <h1 class="text-heading-sm">Create your account</h1>
    <p class="mt-1 text-body-sm text-pewter">A short setup follows so we can suggest your first habits.</p>

    <form action="{{ route('register') }}" method="POST" class="mt-6 space-y-5">
        @csrf
        <div>
            <label for="name" class="label">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" autocomplete="name" autofocus
                class="input @error('name') input-error @enderror" required>
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="label">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email"
                class="input @error('email') input-error @enderror" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="label">Password</label>
            <input type="password" id="password" name="password" autocomplete="new-password"
                class="input @error('password') input-error @enderror" required>
            <p class="help">At least 8 characters.</p>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="label">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="input" required>
        </div>
        <button type="submit" class="btn btn-primary w-full">Create account</button>
    </form>

    <x-slot:footer>
        Already registered? <a href="{{ route('login') }}" class="font-semibold text-ink underline">Log in</a>
    </x-slot:footer>
</x-guest-layout>
