<x-auth-layout title="Log in">
    <div class="space-y-2 text-center">
        <h1 class="text-3xl font-semibold text-ink">Welcome back!</h1>
        <p class="text-body-sm text-pewter">
            First time here?
            <a href="{{ route('register') }}" class="text-ink hover:underline">Sign up for free</a>
        </p>
    </div>

    <form action="{{ route('login') }}" method="POST" class="w-full space-y-4">
        @csrf
        <div>
            <label for="email" class="sr-only">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Your email" autocomplete="email" autofocus
                class="input @error('email') input-error @enderror" required>
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="password" class="sr-only">Password</label>
            <input type="password" id="password" name="password" placeholder="Your password" autocomplete="current-password"
                class="input @error('password') input-error @enderror" required>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-center">
            <x-spring-check name="remember" label="Remember me" :checked="old('remember')" />
        </div>

        <div class="flex flex-col gap-2">
            <button type="submit" class="btn btn-primary btn-lg w-full">Log in</button>
            <button type="button" class="btn btn-link w-full" data-soon="Magic link sign-in">Send me the magic link instead</button>
        </div>

        <div class="flex items-center gap-4 py-2" role="separator">
            <span class="h-px flex-1 bg-mist/60"></span>
            <span class="text-body-sm text-pewter">OR</span>
            <span class="h-px flex-1 bg-mist/60"></span>
        </div>

        <button type="button" class="btn btn-outline btn-lg w-full" data-soon="Single sign-on">Single sign-on (SSO)</button>
    </form>

    <p class="w-11/12 text-center text-caption text-pewter">
        You acknowledge that you read, and agree, to our
        <a href="#" class="underline hover:text-ink" data-soon="Terms of Service">Terms of Service</a>
        and our
        <a href="#" class="underline hover:text-ink" data-soon="Privacy Policy">Privacy Policy</a>.
    </p>
</x-auth-layout>
