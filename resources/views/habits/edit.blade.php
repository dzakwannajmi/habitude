<x-app-layout title="Edit habit" :crumbs="[['Habits', route('habits.index')], ['Edit']]">
    <div class="card max-w-2xl">
        <div class="card-header">
            <h1 class="text-subheading">Edit habit</h1>
            <a href="{{ route('habits.index') }}" class="btn btn-outline btn-sm">
                <x-icon name="arrow-left" class="size-3.5" /> Back
            </a>
        </div>
        <form action="{{ route('habits.update', $habit) }}" method="POST" class="card-body">
            @csrf
            @method('PUT')
            @include('habits._form', ['habit' => $habit])

            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('habits.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary"><x-icon name="save" /> Save changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
