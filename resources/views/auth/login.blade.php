<x-guest-layout title="Log in">
    <h1 class="text-heading-sm">Welcome back</h1>
    <p class="mt-1 text-body-sm text-pewter">Log in to keep your streak going.</p>

    <form action="{{ route('login') }}" method="POST" class="mt-6 space-y-5">
        @csrf
        <div>
            <label for="email" class="label">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" autocomplete="email" autofocus
                class="input @error('email') input-error @enderror" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="label">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password"
                class="input @error('password') input-error @enderror" required>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <x-spring-check name="remember" label="Remember me" :checked="old('remember')" />
        <button type="submit" class="btn btn-primary w-full">Log in</button>
    </form>

    <x-slot:footer>
        New here? <a href="{{ route('register') }}" class="font-semibold text-ink underline">Create an account</a>
    </x-slot:footer>
</x-guest-layout>
