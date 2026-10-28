@props(['title', 'crumbs' => []])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-sidebar="closed">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · {{ config('app.name') }}</title>

    {{-- Set the sidebar state before first paint to avoid a flash --}}
    <script>
        try {
            var s = localStorage.getItem('habitude.sidebar');
            document.documentElement.dataset.sidebar =
                window.matchMedia('(min-width: 1024px)').matches && s !== 'closed' ? 'open' : 'closed';
        } catch (e) {}
    </script>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-svh antialiased">
    @php
        $user = auth()->user();
        $initials = collect(explode(' ', $user->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');

        $nav = [
            ['label' => 'Dashboard', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
            ['label' => 'Track', 'children' => [
                ['label' => 'Habits', 'icon' => 'target', 'url' => route('habits.index'), 'active' => request()->routeIs('habits.*')],
                ['label' => 'Mood', 'icon' => 'smile', 'url' => route('mood.index'), 'active' => request()->routeIs('mood.*')],
                ['label' => 'Journal', 'icon' => 'book-open', 'url' => route('journal.index'), 'active' => request()->routeIs('journal.*')],
                ['label' => 'Achievements', 'icon' => 'trophy', 'url' => route('achievements.index'), 'active' => request()->routeIs('achievements.*')],
            ]],
            ['label' => 'Master Data', 'children' => [
                ['label' => 'Categories', 'icon' => 'folder', 'url' => route('categories.index'), 'active' => request()->routeIs('categories.*')],
                ['label' => 'Tags', 'icon' => 'tag', 'url' => route('tags.index'), 'active' => request()->routeIs('tags.*')],
            ]],
        ];

        $dock = [
            ['label' => 'Dashboard', 'icon' => 'home', 'url' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
            ['label' => 'Habits', 'icon' => 'target', 'url' => route('habits.index'), 'active' => request()->routeIs('habits.*')],
            ['label' => 'Mood', 'icon' => 'smile', 'url' => route('mood.index'), 'active' => request()->routeIs('mood.*')],
            ['label' => 'Journal', 'icon' => 'book-open', 'url' => route('journal.index'), 'active' => request()->routeIs('journal.*')],
            ['label' => 'Menu', 'icon' => 'menu', 'toggle' => true],
        ];
    @endphp

    {{-- Mobile overlay --}}
    <div data-sidebar-overlay class="fixed inset-0 z-30 hidden bg-ink/40 in-data-[sidebar=open]:block lg:hidden!"></div>

    {{-- Sidebar (shadcn sidebar-08, inset variant) --}}
    <aside aria-label="Sidebar"
        class="fixed inset-y-0 left-0 z-40 flex w-(--sidebar-width) -translate-x-full flex-col bg-fog transition-transform duration-200 in-data-[sidebar=open]:translate-x-0 max-lg:border-r max-lg:border-mist/60 max-lg:bg-paper">
        <div class="p-4 pb-2">
            <a href="{{ route('dashboard') }}" class="flex items-center rounded-xl px-1 py-1" aria-label="Habitude Admin home">
                <x-logo />
            </a>
        </div>

        <div class="flex-1 overflow-y-auto px-4 py-4">
            <x-nav-tree :items="$nav" />
        </div>

        <div class="px-3 pb-2">
            <a href="https://github.com/dzakwannajmi/PBO2_LARAVEL_5C" target="_blank" rel="noopener" class="menu-item">
                <x-icon name="external-link" />
                Repository
            </a>
        </div>

        {{-- User card (nav-user) --}}
        <div class="relative border-t border-mist/60 p-3">
            <details class="group" data-menu>
                <summary class="flex cursor-pointer list-none items-center gap-2 rounded-xl p-2 hover:bg-paper [&::-webkit-details-marker]:hidden">
                    <span class="flex size-8 shrink-0 items-center justify-center rounded-md bg-ink text-caption font-semibold text-paper">{{ $initials }}</span>
                    <span class="grid flex-1 text-left leading-tight">
                        <span class="truncate text-body-sm font-semibold">{{ $user->name }}</span>
                        <span class="truncate text-caption text-pewter">{{ $user->email }}</span>
                    </span>
                    <x-icon name="chevrons-up-down" class="size-4 text-steel" />
                </summary>
                <div class="absolute inset-x-3 bottom-full mb-2 rounded-xl border border-mist/60 bg-paper p-1 shadow-[0_8px_24px_-8px_rgb(0_0_0/0.18)]">
                    <a href="{{ route('profile.edit') }}" class="menu-item"><x-icon name="user" /> Profile</a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="menu-item"><x-icon name="log-out" /> Log out</button>
                    </form>
                </div>
            </details>
        </div>
    </aside>

    {{-- Inset content --}}
    <div class="p-0 transition-[padding] duration-200 lg:p-2 lg:in-data-[sidebar=open]:pl-(--sidebar-width)">
        <div class="flex min-h-svh flex-col bg-paper lg:min-h-[calc(100svh-1rem)] lg:rounded-xl lg:border lg:border-mist/60">
            <header class="flex h-14 shrink-0 items-center gap-2 border-b border-mist/60 px-4">
                <button type="button" data-sidebar-toggle class="btn btn-ghost btn-icon" aria-label="Toggle sidebar">
                    <x-icon name="panel-left" />
                </button>
                <span class="mx-1 h-4 w-px bg-mist/60" aria-hidden="true"></span>
                <nav aria-label="Breadcrumb">
                    <ol class="flex items-center gap-1.5 text-body-sm text-pewter">
                        @foreach ($crumbs as $crumb)
                            @php [$label, $url] = array_pad((array) $crumb, 2, null); @endphp
                            @if (! $loop->first)
                                <li aria-hidden="true"><x-icon name="chevron-right" class="size-3.5" /></li>
                            @endif
                            <li @if ($loop->last) aria-current="page" class="font-medium text-ink" @endif>
                                @if ($url && ! $loop->last)
                                    <a href="{{ $url }}" class="hover:text-ink">{{ $label }}</a>
                                @else
                                    {{ $label }}
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </header>

            <main class="mx-auto w-full max-w-(--page-max-width) flex-1 p-4 pb-28 sm:p-6 lg:pb-6">
                <x-flash />
                {{ $slot }}
            </main>
        </div>
    </div>

    <x-dock :items="$dock" />
    <div id="toasts" class="toasts" aria-live="polite"></div>
</body>
</html>
