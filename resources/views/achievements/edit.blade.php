<x-app-layout title="Edit achievement" :crumbs="[['Track'], ['Achievements', route('achievements.index')], ['Edit']]">
    <div class="card max-w-2xl">
        <div class="card-header">
            <h1 class="text-subheading">Edit achievement</h1>
            <a href="{{ route('achievements.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" class="size-3.5" /> Back</a>
        </div>
        <form action="{{ route('achievements.update', $achievement) }}" method="POST" class="card-body">
            @csrf
            @method('PUT')
            @include('achievements._form', ['achievement' => $achievement, 'type' => $type, 'threshold' => $threshold])
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('achievements.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary"><x-icon name="save" /> Save changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
