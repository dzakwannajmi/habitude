<x-app-layout title="Add achievement" :crumbs="[['Admin', route('admin.overview')], ['Achievements', route('admin.achievements.index')], ['Add']]">
    <div class="card max-w-2xl">
        <div class="card-header">
            <h1 class="text-subheading">Add achievement</h1>
            <a href="{{ route('admin.achievements.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" class="size-3.5" /> Back</a>
        </div>
        <form action="{{ route('admin.achievements.store') }}" method="POST" class="card-body">
            @csrf
            
            @include('achievements._form')
            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('admin.achievements.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary"><x-icon name="save" /> Save</button>
            </div>
        </form>
    </div>
</x-app-layout>
