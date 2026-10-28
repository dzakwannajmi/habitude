@php
    $roleOptions = [['value' => '', 'label' => 'All roles'], ['value' => 'user', 'label' => 'Users'], ['value' => 'admin', 'label' => 'Admins']];
@endphp

<x-app-layout title="Users" :crumbs="[['Admin', route('admin.overview')], ['Users']]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-heading-sm">Users <span class="font-normal text-pewter">({{ $users->total() }})</span></h1>
            <p class="text-body-sm text-pewter">Everyone who signed up. Deleting a user removes all of their data.</p>
        </div>
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
            <label class="relative">
                <span class="sr-only">Search users</span>
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-steel" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Search name or email" class="input w-64 pl-9">
            </label>
            <div class="w-36"><x-select name="role" :options="$roleOptions" :value="$role" label="Filter by role" :compact="true" :autosubmit="true" /></div>
            <button type="submit" class="btn btn-outline">Search</button>
        </form>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th><th>Role</th><th>Habits</th><th>Moods</th><th>Journals</th><th>Joined</th><th class="w-28 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $u)
                        <tr>
                            <td>
                                <span class="flex items-center gap-3">
                                    <x-avatar :user="$u" size="size-9" />
                                    <span class="min-w-0"><span class="block truncate font-medium text-ink">{{ $u->name }} @if ($u->is(auth()->user())) <span class="font-normal text-pewter">(you)</span> @endif</span><span class="block truncate text-caption text-pewter">{{ $u->email }}</span></span>
                                </span>
                            </td>
                            <td><span class="badge {{ $u->isAdmin() ? 'badge-active' : '' }}">{{ ucfirst($u->role) }}</span></td>
                            <td>{{ $u->habits_count }}</td>
                            <td>{{ $u->mood_entries_count }}</td>
                            <td>{{ $u->journal_entries_count }}</td>
                            <td class="whitespace-nowrap">{{ $u->created_at->format('j M Y') }}</td>
                            <td>
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-outline btn-icon" aria-label="Edit {{ $u->name }}"><x-icon name="pencil" /></a>
                                    @unless ($u->is(auth()->user()))
                                        <button type="button" class="btn btn-outline btn-icon" data-modal-open="delete-user-{{ $u->id }}" aria-label="Delete {{ $u->name }}"><x-icon name="trash" /></button>
                                    @endunless
                                </div>
                                @unless ($u->is(auth()->user()))
                                    <x-delete-modal id="delete-user-{{ $u->id }}" :action="route('admin.users.destroy', $u)" title="Delete user"
                                        :message="'Delete '.$u->name.' and all of their habits, check-ins, moods and journal entries?'" />
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-10 text-center text-pewter">No users match your search.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="border-t border-mist/60 px-6 py-3">{{ $users->links() }}</div>
        @endif
    </div>
</x-app-layout>
