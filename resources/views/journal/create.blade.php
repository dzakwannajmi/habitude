<x-app-layout title="New journal entry" :crumbs="[['Track'], ['Journal', route('journal.index')], ['New']]">
    <div class="card max-w-2xl">
        <div class="card-header">
            <h1 class="text-subheading">New journal entry</h1>
            <a href="{{ route('journal.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" class="size-3.5" /> Back</a>
        </div>
        <form action="{{ route('journal.store') }}" method="POST" class="card-body">
            @csrf
            
            @include('journal._form', ['prompt' => $prompt ?? null, 'date' => $date ?? null])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('journal.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary"><x-icon name="save" /> Save</button>
            </div>
        </form>
    </div>
</x-app-layout>
