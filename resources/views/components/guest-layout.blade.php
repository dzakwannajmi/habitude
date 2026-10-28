@props(['title', 'wide' => false])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-svh antialiased">
    <main class="mx-auto flex min-h-svh w-full flex-col items-center justify-center px-4 py-10 {{ $wide ? 'max-w-3xl' : 'max-w-md' }}">
        <a href="{{ url('/') }}" class="mb-8" aria-label="Habitude Admin"><x-logo /></a>
        <div class="card w-full">
            <div class="card-body sm:p-8">
                {{ $slot }}
            </div>
        </div>
        @isset($footer)
            <p class="mt-6 text-center text-body-sm text-pewter">{{ $footer }}</p>
        @endisset
    </main>
    <div id="toasts" class="toasts" aria-live="polite"></div>
</body>
</html>
