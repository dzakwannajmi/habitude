@php
    $achievement ??= null;
    $criteriaOptions = collect(config('habitude.criteria'))->map(fn ($l, $v) => ['value' => $v, 'label' => $l])->values()->all();
@endphp

<div class="space-y-5">
    <div>
        <label for="name" class="label">Name</label>
        <input type="text" id="name" name="name" maxlength="60" value="{{ old('name', $achievement?->name) }}" placeholder="e.g. 7-Day Streak"
            class="input @error('name') input-error @enderror" required>
        @error('name') <p class="field-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="description" class="label">Description <span class="font-normal text-pewter">(optional)</span></label>
        <input type="text" id="description" name="description" maxlength="255" value="{{ old('description', $achievement?->description) }}" class="input">
        @error('description') <p class="field-error">{{ $message }}</p> @enderror
    </div>
    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label class="label">Unlocked by</label>
            <x-select name="criteria_type" :options="$criteriaOptions" :value="old('criteria_type', $type ?? null)" placeholder="Choose a goal type" label="Goal type" />
            @error('criteria_type') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="criteria_threshold" class="label">How many</label>
            <input type="number" id="criteria_threshold" name="criteria_threshold" min="1" value="{{ old('criteria_threshold', $threshold ?? 7) }}"
                class="input @error('criteria_threshold') input-error @enderror" required>
            @error('criteria_threshold') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>
</div>
