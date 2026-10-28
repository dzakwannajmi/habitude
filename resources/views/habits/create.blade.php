<x-app-layout title="Add habit" :crumbs="[['Habits', route('habits.index')], ['Add']]">
    <div class="card max-w-2xl">
        <div class="card-header">
            <h1 class="text-subheading">Add habit</h1>
            <a href="{{ route('habits.index') }}" class="btn btn-outline btn-sm">
                <x-icon name="arrow-left" class="size-3.5" /> Back
            </a>
        </div>
        <form action="{{ route('habits.store') }}" method="POST" class="card-body">
            @csrf
            
            @include('habits._form')

            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('habits.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary"><x-icon name="save" /> Save</button>
            </div>
        </form>
    </div>
</x-app-layout>
