@php $days = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun']; @endphp

<x-app-layout title="Edit habit" :crumbs="[['Track'], ['Habits', route('habits.index')], ['Edit']]">
    <div class="grid max-w-5xl gap-6 lg:grid-cols-5">
        <div class="card lg:col-span-3">
            <div class="card-header">
                <h1 class="text-subheading">Edit habit</h1>
                <a href="{{ route('habits.index') }}" class="btn btn-outline btn-sm"><x-icon name="arrow-left" class="size-3.5" /> Back</a>
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

        <section class="card h-fit lg:col-span-2" aria-labelledby="reminders-title">
            <div class="card-header">
                <h2 id="reminders-title" class="text-subheading">Reminders</h2>
                <x-icon name="bell" class="size-4 text-steel" />
            </div>
            <div class="card-body space-y-4">
                @forelse ($habit->reminders->sortBy('remind_at') as $reminder)
                    <div class="flex items-center gap-3 rounded-xl border border-mist/60 p-3">
                        <div class="min-w-0 flex-1">
                            <p class="text-body-sm font-semibold">{{ \Carbon\Carbon::parse($reminder->remind_at)->format('H:i') }}</p>
                            <p class="truncate text-caption text-pewter">
                                {{ count($reminder->days_of_week) === 7 ? 'Every day' : collect($reminder->days_of_week)->map(fn ($d) => $days[$d])->implode(', ') }}
                            </p>
                        </div>
                        <span data-bell-toggle data-on="{{ $reminder->is_enabled ? 'true' : 'false' }}" data-url="{{ route('reminders.update', $reminder) }}"
                            data-off-label="Off" data-on-label="Reminder on" data-label="Toggle reminder at {{ \Carbon\Carbon::parse($reminder->remind_at)->format('H:i') }}"></span>
                        <form action="{{ route('reminders.destroy', $reminder) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-icon" aria-label="Delete reminder"><x-icon name="trash" /></button>
                        </form>
                    </div>
                @empty
                    <p class="text-body-sm text-pewter">No reminders yet.</p>
                @endforelse

                <form action="{{ route('reminders.store', $habit) }}" method="POST" class="space-y-3 border-t border-mist/60 pt-4">
                    @csrf
                    <p class="text-body-sm font-semibold">Add a reminder</p>
                    <div>
                        <label for="remind_at" class="label">Time</label>
                        <input type="time" id="remind_at" name="remind_at" value="{{ old('remind_at', '08:00') }}" class="input @error('remind_at') input-error @enderror" required>
                        @error('remind_at') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <fieldset>
                        <legend class="label">Days</legend>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($days as $n => $d)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="days_of_week[]" value="{{ $n }}" class="peer sr-only" @checked(in_array($n, old('days_of_week', [1, 2, 3, 4, 5, 6, 7])))>
                                    <span class="inline-flex w-11 justify-center rounded-md border border-mist bg-paper py-1.5 text-caption font-medium text-graphite transition-colors peer-checked:border-ink peer-checked:bg-ink peer-checked:text-paper peer-focus-visible:outline-2 peer-focus-visible:outline-ember">{{ $d }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('days_of_week') <p class="field-error">{{ $message }}</p> @enderror
                    </fieldset>
                    <button type="submit" class="btn btn-outline w-full"><x-icon name="plus" /> Add reminder</button>
                </form>
            </div>
        </section>
    </div>
</x-app-layout>
