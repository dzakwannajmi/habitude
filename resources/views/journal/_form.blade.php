@php $journal ??= null; @endphp

<div class="space-y-5">
    <div class="grid gap-5 sm:grid-cols-3">
        <div>
            <label for="entry_date" class="label">Date</label>
            <input type="date" id="entry_date" name="entry_date" max="{{ $today->toDateString() }}"
                value="{{ old('entry_date', $journal?->entry_date->toDateString() ?? $today->toDateString()) }}"
                class="input @error('entry_date') input-error @enderror" required>
            @error('entry_date') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
            <label for="title" class="label">Title</label>
            <input type="text" id="title" name="title" maxlength="120" value="{{ old('title', $journal?->title) }}"
                placeholder="A good day" class="input @error('title') input-error @enderror" required>
            @error('title') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>
    <div>
        <label for="content" class="label">Entry</label>
        <textarea id="content" name="content" rows="9" maxlength="5000" class="input @error('content') input-error @enderror" required>{{ old('content', $journal?->content) }}</textarea>
        @error('content') <p class="field-error">{{ $message }}</p> @enderror
    </div>
    @if ($journal?->moodEntry)
        <p class="flex items-center gap-2 text-body-sm text-pewter"><x-icon name="smile" class="size-4" /> Linked to your mood that day: <strong class="text-ink">{{ config('habitude.moods')[$journal->moodEntry->mood_level] }}</strong></p>
    @else
        <p class="help">If you rated your mood on this date, the entry links to it automatically.</p>
    @endif
</div>
