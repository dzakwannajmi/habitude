@php
    $profile = $user->profile;
    $tzOptions = collect(config('habitude.timezones'))->map(fn ($l, $v) => ['value' => $v, 'label' => $l])->values()->all();
@endphp

<x-app-layout title="Profile" :crumbs="[['Profile']]">
    <div class="card max-w-2xl">
        <div class="card-header">
            <div class="flex items-center gap-3">
                <x-avatar :user="$user" size="size-14" />
                <div><h1 class="text-subheading">Your profile</h1><p class="text-body-sm text-pewter">Your picture is generated from your account, no upload needed.</p></div>
            </div>
        </div>
        <form action="{{ route('profile.update') }}" method="POST" class="card-body space-y-5">
            @csrf
            @method('PUT')
            <div class="grid gap-5 sm:grid-cols-2">
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
            </div>
            <div>
                <label for="bio" class="label">Bio <span class="font-normal text-pewter">(optional)</span></label>
                <textarea id="bio" name="bio" rows="3" maxlength="500" class="input">{{ old('bio', $profile?->bio) }}</textarea>
                @error('bio') <p class="field-error">{{ $message }}</p> @enderror
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="label">Timezone</label>
                    <x-select name="timezone" :options="$tzOptions" :value="old('timezone', $profile?->timezone ?? 'Asia/Makassar')" label="Timezone" />
                    @error('timezone') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="daily_goal" class="label">Habits to finish per day</label>
                    <input type="number" id="daily_goal" name="daily_goal" min="1" max="20" value="{{ old('daily_goal', $profile?->daily_goal ?? 3) }}" class="input @error('daily_goal') input-error @enderror" required>
                    @error('daily_goal') <p class="field-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <fieldset class="space-y-3 border-t border-mist/60 pt-5">
                <legend class="text-body-sm font-semibold">Reminders</legend>
                <div>
                    <x-spring-check name="email_reminders" label="Email me my reminders" :checked="old('email_reminders', $profile?->email_reminders ?? true)" />
                    <p class="help">One email at the time you set on a habit, only if it is not done yet.</p>
                </div>
                <div data-notify-setting class="rounded-xl border border-mist/60 p-4">
                    <p class="text-body-sm font-medium text-ink">Browser notifications</p>
                    <p class="help" data-notify-status>Checking this browser…</p>
                    <button type="button" class="btn btn-outline btn-sm mt-3" data-notify-toggle hidden><x-icon name="bell" class="size-3.5" /> <span>Allow notifications</span></button>
                    <p class="help mt-2">They appear while Habitude is open in a tab or installed as an app. Emails work even when it is closed.</p>
                </div>
            </fieldset>
            <fieldset class="space-y-5 border-t border-mist/60 pt-5">
                <legend class="text-body-sm font-semibold">Change password <span class="font-normal text-pewter">(leave empty to keep it)</span></legend>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="password" class="label">New password</label>
                        <input type="password" id="password" name="password" autocomplete="new-password" class="input @error('password') input-error @enderror">
                        @error('password') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="label">Confirm</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" class="input">
                    </div>
                </div>
            </fieldset>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <button type="button" class="btn btn-outline" data-install hidden><x-icon name="plus" /> Install Habitude as an app</button>
                <button type="submit" class="btn btn-primary ml-auto"><x-icon name="save" /> Save changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
