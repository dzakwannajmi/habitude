@php
    $habit ??= null;
    $selectedTags = collect(old('tags', $habit?->tags->pluck('id')->all() ?? []))->map(fn ($id) => (int) $id);
@endphp

<div class="space-y-5">
    <div>
        <label for="name" class="label">Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $habit?->name) }}"
            placeholder="e.g. Drink water" class="input @error('name') input-error @enderror" required>
        @error('name') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="category_id" class="label">Category</label>
        <select id="category_id" name="category_id" class="input @error('category_id') input-error @enderror" required>
            <option value="">Choose a category</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $habit?->category_id) == $category->id)>
                    {{ $category->icon }} {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="description" class="label">Description <span class="font-normal text-pewter">(optional)</span></label>
        <textarea id="description" name="description" rows="3" maxlength="500"
            class="input @error('description') input-error @enderror">{{ old('description', $habit?->description) }}</textarea>
        @error('description') <p class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="target_count" class="label">Daily target</label>
            <input type="number" id="target_count" name="target_count" min="1" max="1000"
                value="{{ old('target_count', $habit?->target_count ?? 1) }}"
                class="input @error('target_count') input-error @enderror" required>
            @error('target_count') <p class="field-error">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="unit" class="label">Unit</label>
            <input type="text" id="unit" name="unit" value="{{ old('unit', $habit?->unit ?? 'times') }}"
                placeholder="glasses, pages, minutes" class="input @error('unit') input-error @enderror" required>
            @error('unit') <p class="field-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <fieldset>
        <legend class="label">Tags <span class="font-normal text-pewter">(optional)</span></legend>
        @if ($tags->isEmpty())
            <p class="help">No tags yet. <a href="{{ route('tags.create') }}" class="underline text-ink">Create one</a>.</p>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="peer sr-only"
                            @checked($selectedTags->contains($tag->id))>
                        <span class="inline-flex items-center rounded-md border border-mist bg-paper px-3 py-1.5 text-body-sm text-graphite transition-colors peer-checked:border-ink peer-checked:bg-ink peer-checked:text-paper peer-focus-visible:outline-2 peer-focus-visible:outline-ember">
                            {{ $tag->name }}
                        </span>
                    </label>
                @endforeach
            </div>
        @endif
        @error('tags.*') <p class="field-error">{{ $message }}</p> @enderror
    </fieldset>

    <div class="flex items-center justify-between rounded-xl border border-mist/60 px-4 py-3">
        <div>
            <label for="is_active" class="text-body-sm font-medium text-ink">Active</label>
            <p class="text-caption text-pewter">Paused habits stay in your list but are not tracked.</p>
        </div>
        <input type="checkbox" id="is_active" name="is_active" value="1" role="switch"
            class="size-5 accent-ember" @checked(old('is_active', $habit?->is_active ?? true))>
    </div>
</div>
