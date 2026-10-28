<x-app-layout title="Habits" :crumbs="[['Habits']]">
    <div class="card">
        <div class="card-header">
            <div>
                <h1 class="text-subheading">Habits <span class="text-pewter font-normal">({{ $habits->count() }})</span></h1>
                <p class="text-body-sm text-pewter">Everything you are tracking, with its category and tags.</p>
            </div>
            <a href="{{ route('habits.create') }}" class="btn btn-primary">
                <x-icon name="plus" /> Add habit
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-12">#</th>
                        <th>Habit</th>
                        <th>Category</th>
                        <th>Daily target</th>
                        <th>Tags</th>
                        <th>Status</th>
                        <th class="w-28 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($habits as $habit)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <p class="font-medium text-ink">{{ $habit->name }}</p>
                                @if ($habit->description)
                                    <p class="max-w-xs truncate text-caption text-pewter">{{ $habit->description }}</p>
                                @endif
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-2">
                                    <span class="size-3 rounded-md" style="background: {{ $habit->category->color }}" aria-hidden="true"></span>
                                    {{ $habit->category->name }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap">{{ $habit->target_count }} {{ $habit->unit }}</td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($habit->tags as $tag)
                                        <span class="badge">{{ $tag->name }}</span>
                                    @empty
                                        <span class="text-steel">-</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                @if ($habit->is_active)
                                    <span class="badge badge-active">Active</span>
                                @else
                                    <span class="badge">Paused</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('habits.edit', $habit) }}" class="btn btn-outline btn-icon" aria-label="Edit {{ $habit->name }}">
                                        <x-icon name="pencil" />
                                    </a>
                                    <button type="button" class="btn btn-outline btn-icon" data-modal-open="delete-habit-{{ $habit->id }}" aria-label="Delete {{ $habit->name }}">
                                        <x-icon name="trash" />
                                    </button>
                                </div>
                                <x-delete-modal id="delete-habit-{{ $habit->id }}"
                                    :action="route('habits.destroy', $habit)"
                                    title="Delete habit"
                                    :message="'Delete &quot;'.$habit->name.'&quot; and all of its logs and reminders?'" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-pewter">No habits yet. Add your first one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
