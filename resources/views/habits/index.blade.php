@php
    $groupOptions = [['value' => 'category', 'label' => 'Group by category'], ['value' => 'status', 'label' => 'Group by status'], ['value' => 'none', 'label' => 'No grouping']];
    $toggle = fn ($v) => route('habits.index', ['group' => $group, 'view' => $v]);
@endphp

<x-app-layout title="Habits" :crumbs="[['Track'], ['Habits']]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-heading-sm">Habits <span class="font-normal text-pewter">({{ $habits->count() }})</span></h1>
            <p class="text-body-sm text-pewter">Swipe a row, or press the dots, to edit or delete. Switch to grid for a Finder-style view.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form action="{{ route('habits.index') }}" method="GET" class="w-48">
                <input type="hidden" name="view" value="{{ $view }}">
                <x-select name="group" :options="$groupOptions" :value="$group" label="Group habits" :compact="true" :autosubmit="true" />
            </form>
            <div class="inline-flex rounded-xl bg-fog p-1" role="group" aria-label="View">
                <a href="{{ $toggle('list') }}" class="btn btn-sm {{ $view === 'list' ? 'bg-paper text-ink' : 'btn-ghost text-pewter' }}" aria-pressed="{{ $view === 'list' ? 'true' : 'false' }}"><x-icon name="list" class="size-3.5" /> List</a>
                <a href="{{ $toggle('grid') }}" class="btn btn-sm {{ $view === 'grid' ? 'bg-paper text-ink' : 'btn-ghost text-pewter' }}" aria-pressed="{{ $view === 'grid' ? 'true' : 'false' }}"><x-icon name="layout-grid" class="size-3.5" /> Grid</a>
            </div>
            <a href="{{ route('habits.create') }}" class="btn btn-primary"><x-icon name="plus" /> Add habit</a>
        </div>
    </div>

    @forelse ($groups as $name => $items)
        <details class="group mb-4" open>
            <summary class="mb-2 flex cursor-pointer list-none items-center gap-2 text-body-sm font-semibold text-ink [&::-webkit-details-marker]:hidden">
                <x-icon name="chevron-right" class="size-4 text-steel transition-transform group-open:rotate-90" />
                @if ($group === 'category' && $items->first()->category->color)
                    <span class="size-3 rounded-md" style="background: {{ $items->first()->category->color }}"></span>
                    {{ $items->first()->category->icon }}
                @endif
                {{ $name }}
                <span class="font-normal text-pewter">{{ $items->count() }}</span>
            </summary>

            @if ($view === 'list')
                <div class="card divide-y divide-mist/40 overflow-hidden">
                    @foreach ($items as $habit)
                        <x-swipe-row :edit-url="route('habits.edit', $habit)" :delete-id="'delete-habit-'.$habit->id">
                            <a href="{{ route('habits.edit', $habit) }}" class="flex min-w-0 flex-1 items-center gap-3 px-6 py-3">
                                <span class="flex size-10 flex-none items-center justify-center rounded-xl text-[18px]" style="background: color-mix(in srgb, {{ $habit->category->color }} 18%, white)">{{ $habit->category->icon }}</span>
                                <span class="min-w-0 flex-1">
                                    <span class="flex items-center gap-2">
                                        <span class="truncate font-medium text-ink">{{ $habit->name }}</span>
                                        @unless ($habit->is_active) <span class="badge">Paused</span> @endunless
                                    </span>
                                    <span class="block truncate text-caption text-pewter">
                                        {{ $habit->target_label }} a day
                                        @if ($group !== 'category') · {{ $habit->category->name }} @endif
                                        @foreach ($habit->tags as $tag) · #{{ $tag->name }} @endforeach
                                    </span>
                                </span>
                                @if ($habit->streak > 0)
                                    <span class="badge flex-none" title="Current streak"><x-icon name="flame" class="size-3.5" />{{ $habit->streak }}</span>
                                @endif
                            </a>
                        </x-swipe-row>
                    @endforeach
                </div>
            @else
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-6">
                    @foreach ($items as $habit)
                        <div class="tile group/tile relative rounded-xl p-2 text-center hover:bg-fog">
                            <a href="{{ route('habits.edit', $habit) }}" class="block">
                                <span class="mx-auto flex aspect-square w-full max-w-24 items-center justify-center rounded-[20px] text-[34px]"
                                    style="background: color-mix(in srgb, {{ $habit->category->color }} 18%, white); box-shadow: inset 0 0 0 1px color-mix(in srgb, {{ $habit->category->color }} 35%, white)">
                                    {{ $habit->category->icon }}
                                </span>
                                <span class="mt-2 block truncate text-body-sm font-medium text-ink">{{ $habit->name }}</span>
                                <span class="block truncate text-caption text-pewter">{{ $habit->target_label }}@unless ($habit->is_active) · Paused @endunless</span>
                            </a>
                            @if ($habit->streak > 0)
                                <span class="badge absolute left-2 top-2"><x-icon name="flame" class="size-3" />{{ $habit->streak }}</span>
                            @endif
                            <button type="button" class="btn btn-ghost btn-icon btn-sm absolute right-1 top-1 opacity-0 transition-opacity focus-visible:opacity-100 group-hover/tile:opacity-100"
                                data-modal-open="delete-habit-{{ $habit->id }}" aria-label="Delete {{ $habit->name }}"><x-icon name="trash" class="size-4" /></button>
                        </div>
                    @endforeach
                </div>
            @endif

            @foreach ($items as $habit)
                <x-delete-modal id="delete-habit-{{ $habit->id }}" :action="route('habits.destroy', $habit)" title="Delete habit"
                    :message="'Delete &quot;'.$habit->name.'&quot; and all of its logs and reminders?'" />
            @endforeach
        </details>
    @empty
        <div class="card px-6 py-16 text-center text-pewter">
            No habits yet. <a href="{{ route('habits.create') }}" class="font-semibold text-ink underline">Add your first one</a>.
        </div>
    @endforelse
</x-app-layout>
