<x-app-layout title="Categories" :crumbs="[['Master Data'], ['Categories']]">
    <div class="card">
        <div class="card-header">
            <div>
                <h1 class="text-subheading">Categories <span class="text-pewter font-normal">({{ $categories->count() }})</span></h1>
                <p class="text-body-sm text-pewter">The themes your habits are grouped into.</p>
            </div>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <x-icon name="plus" /> Add category
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th>Category</th>
                        <th>Slug</th>
                        <th>Habits</th>
                        <th class="w-28 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="inline-flex items-center gap-2 font-medium text-ink">
                                    <span class="size-3 rounded-md" style="background: {{ $category->color }}" aria-hidden="true"></span>
                                    {{ $category->icon }} {{ $category->name }}
                                </span>
                            </td>
                            <td><span class="badge">{{ $category->slug }}</span></td>
                            <td>{{ $category->habits_count }}</td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline btn-icon" aria-label="Edit {{ $category->name }}">
                                        <x-icon name="pencil" />
                                    </a>
                                    <button type="button" class="btn btn-outline btn-icon" data-modal-open="delete-category-{{ $category->id }}" aria-label="Delete {{ $category->name }}">
                                        <x-icon name="trash" />
                                    </button>
                                </div>
                                <x-delete-modal id="delete-category-{{ $category->id }}"
                                    :action="route('categories.destroy', $category)"
                                    title="Delete category"
                                    :message="'Delete the category &quot;'.$category->name.'&quot;?'" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-pewter">No categories yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
