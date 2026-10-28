<x-app-layout title="Tags" :crumbs="[['Admin', route('admin.overview')], ['Tags']]">
    <div class="card">
        <div class="card-header">
            <div>
                <h1 class="text-subheading">Tags <span class="text-pewter font-normal">({{ $tags->count() }})</span></h1>
                <p class="text-body-sm text-pewter">Free-form labels you can attach to any habit.</p>
            </div>
            <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">
                <x-icon name="plus" /> Add tag
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th>Tag</th>
                        <th>Habits</th>
                        <th class="w-28 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tags as $tag)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><span class="badge">{{ $tag->name }}</span></td>
                            <td>{{ $tag->habits_count }}</td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-outline btn-icon" aria-label="Edit {{ $tag->name }}">
                                        <x-icon name="pencil" />
                                    </a>
                                    <button type="button" class="btn btn-outline btn-icon" data-modal-open="delete-tag-{{ $tag->id }}" aria-label="Delete {{ $tag->name }}">
                                        <x-icon name="trash" />
                                    </button>
                                </div>
                                <x-delete-modal id="delete-tag-{{ $tag->id }}"
                                    :action="route('admin.tags.destroy', $tag)"
                                    title="Delete tag"
                                    :message="'Delete the tag &quot;'.$tag->name.'&quot;? It will be removed from all habits.'" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-pewter">No tags yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
