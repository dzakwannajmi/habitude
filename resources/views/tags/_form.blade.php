@php($tag ??= null)

<div>
    <label for="name" class="label">Name</label>
    <input type="text" id="name" name="name" value="{{ old('name', $tag?->name) }}"
        placeholder="e.g. morning" class="input @error('name') input-error @enderror" required>
    @error('name') <p class="field-error">{{ $message }}</p> @enderror
</div>
