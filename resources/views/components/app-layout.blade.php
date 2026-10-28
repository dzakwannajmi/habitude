@props(['title', 'crumbs' => []])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-sidebar="closed">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} · {{ config("app.name") }}</title>

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
        $user = \App\Support\CurrentUser::get();
        $initials = collect(explode(' ', $user->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('');
    @endphp

    {{-- Mobile overlay --}}
    <div data-sidebar-overlay class="fixed inset-0 z-30 hidden bg-ink/40 in-data-[sidebar=open]:block lg:hidden!"></div>

    {{-- Sidebar (shadcn sidebar-08, inset variant) --}}
    <aside aria-label="Main navigation"
        class="fixed inset-y-0 left-0 z-40 flex w-(--sidebar-width) -translate-x-full flex-col bg-fog transition-transform duration-200 in-data-[sidebar=open]:translate-x-0 max-lg:bg-paper max-lg:border-r max-lg:border-mist/60">
        <div class="p-4 pb-2">
            <a href="{{ route('habits.index') }}" class="flex items-center rounded-xl px-1 py-1" aria-label="Habitude home">
                <x-logo />
            </a>
        </div>

        <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-2">
            <div>
                <p class="px-2 pb-2 text-caption font-medium text-pewter">Platform</p>
                <ul class="space-y-1">
                    @php
                        $habitsOpen = request()->routeIs('habits.*');
                        $masterOpen = request()->routeIs('categories.*', 'tags.*');
                    @endphp

                    <li>
                        <details class="group" @if ($habitsOpen) open @endif>
                            <summary class="menu-item cursor-pointer list-none [&::-webkit-details-marker]:hidden" @if ($habitsOpen) aria-current="page" @endif>
                                <x-icon name="target" />
                                <span class="flex-1">Habits</span>
                                <x-icon name="chevron-right" class="size-4 transition-transform group-open:rotate-90" />
                            </summary>
                            <ul class="ml-4 mt-1 space-y-0.5 border-l border-mist/60 pl-3">
                                <li><a href="{{ route('habits.index') }}" class="menu-sub" @if (request()->routeIs('habits.index', 'habits.edit')) aria-current="page" @endif>All habits</a></li>
                                <li><a href="{{ route('habits.create') }}" class="menu-sub" @if (request()->routeIs('habits.create')) aria-current="page" @endif>Add habit</a></li>
                            </ul>
                        </details>
                    </li>

                    <li>
                        <details class="group" @if ($masterOpen) open @endif>
                            <summary class="menu-item cursor-pointer list-none [&::-webkit-details-marker]:hidden" @if ($masterOpen) aria-current="page" @endif>
                                <x-icon name="folder" />
                                <span class="flex-1">Master Data</span>
                                <x-icon name="chevron-right" class="size-4 transition-transform group-open:rotate-90" />
                            </summary>
                            <ul class="ml-4 mt-1 space-y-0.5 border-l border-mist/60 pl-3">
                                <li><a href="{{ route('categories.index') }}" class="menu-sub" @if (request()->routeIs('categories.*')) aria-current="page" @endif>Categories</a></li>
                                <li><a href="{{ route('tags.index') }}" class="menu-sub" @if (request()->routeIs('tags.*')) aria-current="page" @endif>Tags</a></li>
                            </ul>
                        </details>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="px-3 pb-2">
            <a href="https://github.com/dzakwannajmi/PBO2_LARAVEL_5C" target="_blank" rel="noopener" class="menu-item">
                <x-icon name="external-link" />
                Repository
            </a>
        </div>

        {{-- User card (nav-user) --}}
        <div class="border-t border-mist/60 p-3">
            <div class="flex items-center gap-2 rounded-xl p-2">
                <span class="flex size-8 shrink-0 items-center justify-center rounded-md bg-ink text-caption font-semibold text-paper">{{ $initials }}</span>
                <span class="grid flex-1 text-left leading-tight">
                    <span class="truncate text-body-sm font-semibold">{{ $user->name }}</span>
                    <span class="truncate text-caption text-pewter">{{ $user->email }}</span>
                </span>
                <x-icon name="chevrons-up-down" class="size-4 text-steel" />
            </div>
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

            <main class="mx-auto w-full max-w-(--page-max-width) flex-1 p-4 sm:p-6">
                <x-flash />
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
