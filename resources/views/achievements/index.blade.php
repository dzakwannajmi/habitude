<x-app-layout title="Achievements" :crumbs="[['Track'], ['Achievements']]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-heading-sm">Achievements <span class="font-normal text-pewter">({{ $earned->count() }}/{{ $achievements->count() }})</span></h1>
            <p class="text-body-sm text-pewter">Unlocked automatically when you reach the goal. Edit the goals below.</p>
        </div>
        <a href="{{ route('achievements.create') }}" class="btn btn-primary"><x-icon name="plus" /> Add achievement</a>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($achievements as $a)
            @php
                [$type, $threshold] = $a->parsedCriteria();
                $have = min($metrics[$type] ?? 0, $threshold);
                $got = $earned->get($a->id);
            @endphp
            <article class="card flex flex-col p-5 {{ $got ? '' : 'bg-fog/50' }}">
                <div class="flex items-start gap-3">
                    <span class="flex size-11 flex-none items-center justify-center rounded-xl {{ $got ? 'bg-ink text-paper' : 'bg-fog text-steel' }}"><x-icon name="{{ $got ? 'trophy' : 'lock' }}" class="size-5" /></span>
                    <div class="min-w-0 flex-1">
                        <h2 class="truncate text-body font-semibold">{{ $a->name }}</h2>
                        <p class="text-body-sm text-graphite">{{ $a->description }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="mb-1 flex justify-between text-caption text-pewter">
                        <span>{{ config('habitude.criteria')[$type] ?? $type }}</span>
                        <span>{{ $have }} / {{ $threshold }}</span>
                    </div>
                    <div class="h-1.5 overflow-hidden rounded bg-fog"><span class="block h-full rounded bg-ink transition-[width] duration-700" style="width: {{ $threshold ? $have / $threshold * 100 : 0 }}%"></span></div>
                    <p class="mt-2 text-caption text-pewter">{{ $got ? 'Unlocked '.\Carbon\Carbon::parse($got->pivot->earned_at)->format('j M Y') : 'Locked' }}</p>
                </div>
                <div class="mt-4 flex justify-end gap-1 border-t border-mist/40 pt-3">
                    <a href="{{ route('achievements.edit', $a) }}" class="btn btn-outline btn-icon" aria-label="Edit {{ $a->name }}"><x-icon name="pencil" /></a>
                    <button type="button" class="btn btn-outline btn-icon" data-modal-open="delete-achievement-{{ $a->id }}" aria-label="Delete {{ $a->name }}"><x-icon name="trash" /></button>
                </div>
                <x-delete-modal id="delete-achievement-{{ $a->id }}" :action="route('achievements.destroy', $a)" title="Delete achievement"
                    :message="'Delete &quot;'.$a->name.'&quot;? Everyone who unlocked it loses it.'" />
            </article>
        @empty
            <div class="card col-span-full px-6 py-16 text-center text-pewter">No achievements yet.</div>
        @endforelse
    </div>
</x-app-layout>
