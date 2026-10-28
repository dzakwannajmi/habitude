<x-auth-layout title="Create account">
    <div class="space-y-2 text-center">
        <h1 class="text-3xl font-semibold text-ink">Create your account!</h1>
        <p class="text-body-sm text-pewter">
            Already registered?
            <a href="{{ route('login') }}" class="text-ink hover:underline">Log in</a>
        </p>
    </div>

    <form action="{{ route('register') }}" method="POST" class="w-full space-y-4">
        @csrf
        <div>
            <label for="name" class="sr-only">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Your name" autocomplete="name" autofocus
                class="input @error('name') input-error @enderror" required>
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="email" class="sr-only">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Your email" autocomplete="email"
                class="input @error('email') input-error @enderror" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="sr-only">Password</label>
            <input type="password" id="password" name="password" placeholder="Password (at least 8 characters)" autocomplete="new-password"
                class="input @error('password') input-error @enderror" required>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password_confirmation" class="sr-only">Confirm password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" autocomplete="new-password" class="input" required>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-full">Create account</button>

    </form>

    <p class="w-11/12 text-center text-caption text-pewter">
        You acknowledge that you read, and agree, to our
        <a href="{{ route('terms') }}" class="underline hover:text-ink">Terms of Service</a>
        and our
        <a href="{{ route('privacy') }}" class="underline hover:text-ink">Privacy Policy</a>.
    </p>
</x-auth-layout>
