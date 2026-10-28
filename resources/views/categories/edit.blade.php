<x-app-layout title="Edit category" :crumbs="[['Admin', route('admin.overview')], ['Categories', route('admin.categories.index')], ['Edit']]">
    <div class="card max-w-2xl">
        <div class="card-header">
            <h1 class="text-subheading">Edit category</h1>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline btn-sm">
                <x-icon name="arrow-left" class="size-3.5" /> Back
            </a>
        </div>
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="card-body">
            @csrf
            @method('PUT')
            @include('categories._form', ['category' => $category])

            <div class="mt-8 flex justify-end gap-2">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary"><x-icon name="save" /> Save changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
