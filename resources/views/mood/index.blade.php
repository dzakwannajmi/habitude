@php $labels = array_values(config('habitude.moods')); @endphp

<x-app-layout title="Mood" :crumbs="[['Track'], ['Mood']]">
    <div class="grid max-w-5xl gap-6 lg:grid-cols-5">
        <section class="card h-fit lg:col-span-2" aria-labelledby="checkin-title">
            <div class="card-header">
                <div>
                    <h1 id="checkin-title" class="text-subheading">Today's check-in</h1>
                    <p class="text-body-sm text-pewter">{{ $today->format('l, j F') }}</p>
                </div>
            </div>
            <form action="{{ route('mood.store') }}" method="POST" class="card-body space-y-5">
                @csrf
                <div>
                    <x-peek-rating name="mood_level" shape="heart" :value="old('mood_level', $todayMood?->mood_level ?? 0)" :labels="$labels" label="How do you feel today?" />
                    @error('mood_level') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="note" class="label">Note <span class="font-normal text-pewter">(optional)</span></label>
                    <input type="text" id="note" name="note" maxlength="255" value="{{ old('note', $todayMood?->note) }}" placeholder="What shaped your day?" class="input">
                    @error('note') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="flex flex-wrap justify-between gap-2">
                    <a href="{{ route('journal.create') }}" class="btn btn-outline"><x-icon name="book-open" /> Write a journal</a>
                    <button type="submit" class="btn btn-primary"><x-icon name="save" /> Save mood</button>
                </div>
            </form>
        </section>

        <section class="card overflow-hidden lg:col-span-3" aria-labelledby="history-title">
            <div class="card-header">
                <h2 id="history-title" class="text-subheading">History <span class="font-normal text-pewter">({{ $entries->count() }})</span></h2>
            </div>
            <div class="divide-y divide-mist/40">
                @forelse ($entries as $entry)
                    <x-swipe-row :delete-id="'delete-mood-'.$entry->id">
                        <div class="flex min-w-0 flex-1 items-center gap-3 px-6 py-3">
                            <span class="flex size-10 flex-none flex-col items-center justify-center rounded-xl bg-fog leading-none">
                                <span class="text-caption text-pewter">{{ $entry->entry_date->format('M') }}</span>
                                <span class="text-body-sm font-semibold">{{ $entry->entry_date->format('j') }}</span>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="flex items-center gap-2 font-medium text-ink">
                                    {{ config('habitude.moods')[$entry->mood_level] }}
                                    <span class="flex gap-0.5" aria-hidden="true">
                                        @foreach (range(1, 5) as $i)
                                            <span class="h-1.5 w-3 rounded-full {{ $i <= $entry->mood_level ? 'bg-ink' : 'bg-mist/60' }}"></span>
                                        @endforeach
                                    </span>
                                </span>
                                <span class="block truncate text-caption text-pewter">{{ $entry->note ?: 'No note' }}</span>
                            </span>
                            @if ($entry->journalEntry)
                                <a href="{{ route('journal.edit', $entry->journalEntry) }}" class="badge flex-none" data-no-swipe><x-icon name="book-open" class="size-3" /> Journal</a>
                            @endif
                        </div>
                    </x-swipe-row>
                    <x-delete-modal id="delete-mood-{{ $entry->id }}" :action="route('mood.destroy', $entry)" title="Delete mood entry"
                        :message="'Delete your mood for '.$entry->entry_date->format('j F').'? Its journal entry is kept.'" />
                @empty
                    <p class="px-6 py-12 text-center text-pewter">No mood entries yet. Rate how you feel today.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
