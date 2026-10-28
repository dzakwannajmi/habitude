@php
    $roleOptions = [['value' => 'user', 'label' => 'User', 'tag' => 'sees only their own data'], ['value' => 'admin', 'label' => 'Admin', 'tag' => 'full system access']];
    $self = $user->is(auth()->user());
@endphp

<x-app-layout title="Edit user" :crumbs="[['Admin', route('admin.overview')], ['Users', route('admin.users.index')], ['Edit']]">
    <div class="grid max-w-5xl gap-6 lg:grid-cols-5">
        <div class="card lg:col-span-3">
            <div class="card-header">
                <h1 class="text-subheading">Edit user</h1>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" class="size-3.5" /> Back</a>
            </div>
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="card-body space-y-5">
                @csrf
                @method('PUT')
                <div>
                    <label for="name" class="label">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="input @error('name') input-error @enderror" required>
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="label">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="input @error('email') input-error @enderror" required>
                    @error('email') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">Role</label>
                    <x-select name="role" :options="$roleOptions" :value="old('role', $user->role)" label="Role" />
                    @if ($self) <p class="help">You cannot change your own role.</p> @endif
                    @error('role') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="label">Set a new password <span class="font-normal text-pewter">(optional)</span></label>
                    <input type="password" id="password" name="password" autocomplete="new-password" class="input @error('password') input-error @enderror">
                    @error('password') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary"><x-icon name="save" /> Save changes</button>
                </div>
            </form>
        </div>

        <aside class="card h-fit lg:col-span-2">
            <div class="card-header"><h2 class="text-subheading">Activity</h2></div>
            <dl class="card-body space-y-2 text-body-sm">
                <div class="flex justify-between"><dt class="text-pewter">Joined</dt><dd class="font-medium">{{ $user->created_at->format('j M Y') }}</dd></div>
                <div class="flex justify-between"><dt class="text-pewter">Habits</dt><dd class="font-medium">{{ $user->habits_count }}</dd></div>
                <div class="flex justify-between"><dt class="text-pewter">Mood entries</dt><dd class="font-medium">{{ $user->mood_entries_count }}</dd></div>
                <div class="flex justify-between"><dt class="text-pewter">Journal entries</dt><dd class="font-medium">{{ $user->journal_entries_count }}</dd></div>
                <div class="flex justify-between"><dt class="text-pewter">Achievements</dt><dd class="font-medium">{{ $user->achievements_count }}</dd></div>
            </dl>
        </aside>
    </div>
</x-app-layout>
