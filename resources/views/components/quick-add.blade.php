{{-- Quick add: a name and a category are enough, the target and unit follow from the category. --}}
@php
    $cats = \App\Models\Category::orderBy('name')->get();
    $options = $cats->map(fn ($c) => ['value' => $c->id, 'label' => trim($c->icon.' '.$c->name), 'dot' => $c->color])->values()->all();
    $examples = $cats->mapWithKeys(fn ($c) => [$c->id => config('habitude.defaults.'.$c->slug.'.example', 'Name your habit')]);
    $summary = $cats->mapWithKeys(fn ($c) => [$c->id => ($d = config('habitude.defaults.'.$c->slug)) ? $d['target'].' '.$d['unit'].' a day' : '']);
@endphp

<dialog id="quick-add" aria-labelledby="quick-add-title"
    class="m-auto w-full max-w-md rounded-xl border border-mist/60 bg-paper p-0 text-left text-ink backdrop:bg-ink/40">
    <form action="{{ route('habits.store') }}" method="POST" data-quick-add data-examples="{{ json_encode($examples) }}" data-summary="{{ json_encode($summary) }}">
        @csrf
        <input type="hidden" name="quick" value="1">
        <input type="hidden" name="back" value="{{ request()->fullUrl() }}">
        <div class="space-y-4 p-6">
            <div>
                <h2 id="quick-add-title" class="text-subheading">Add a habit</h2>
                <p class="mt-1 text-body-sm text-pewter">Just a name and a category. You can fine-tune it later.</p>
            </div>
            <div>
                <label class="label">Category</label>
                <x-select name="category_id" :options="$options" placeholder="Choose a category" label="Category" />
                <p class="help" data-quick-summary>We pick a sensible daily target for you.</p>
            </div>
            <div>
                <label for="quick-name" class="label">Name</label>
                <input type="text" id="quick-name" name="name" maxlength="100" placeholder="e.g. Drink water" class="input" required>
            </div>
        </div>
        <div class="flex items-center justify-between gap-2 border-t border-mist/60 px-6 py-4">
            <a href="{{ route('habits.create') }}" class="text-body-sm text-pewter underline hover:text-ink">More options</a>
            <div class="flex gap-2">
                <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
                <button type="submit" class="btn btn-primary"><x-icon name="plus" /> Add habit</button>
            </div>
        </div>
    </form>
</dialog>
