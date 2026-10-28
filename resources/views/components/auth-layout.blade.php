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
    @if (config('habitude.stage') !== 'production')
        <div class="fixed inset-x-0 top-0 z-50 flex h-7 items-center justify-center gap-2 bg-carbon px-3 text-caption font-medium text-paper" role="note">
            <span>Beta preview, not production.</span>
            <a href="{{ config('habitude.feedback_url') }}" target="_blank" rel="noopener" class="font-semibold text-ember underline-offset-2 hover:underline">Send feedback</a>
        </div>
    @endif

    <main class="flex min-h-svh items-center justify-center px-4 py-12">
        {{-- Card follows the login-3 layout: big radius, centred logo, centred heading --}}
        <div class="w-full {{ $wide ? 'max-w-2xl' : 'max-w-sm' }} rounded-[32px] border border-mist/60 bg-paper px-6 py-10 pt-14">
            <div class="flex flex-col items-center space-y-8">
                <a href="{{ url('/') }}" aria-label="Habitude home"><x-logo :large="true" /></a>
                {{ $slot }}
            </div>
        </div>
    </main>

    <div id="toasts" class="toasts" aria-live="polite"></div>
</body>
</html>
