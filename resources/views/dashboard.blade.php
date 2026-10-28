@php
    $isToday = $date->equalTo($today);
    $prev = $month->subMonth()->format('Y-m');
    $next = $month->addMonth();
    $canNext = $next->startOfMonth() <= $today->startOfMonth();
    $weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $moodLabels = array_values(config('habitude.moods'));
@endphp

<x-app-layout title="Dashboard" :crumbs="[['Dashboard']]">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-heading-sm">{{ $isToday ? 'Today' : $date->format('l') }}, {{ $date->format('j F Y') }}</h1>
            <p class="text-body-sm text-pewter">
                @if ($isToday)
                    Hi {{ explode(' ', auth()->user()->name)[0] }}, here is what is on your list.
                @else
                    You are looking at a past day. You can still log it.
                    <a href="{{ route('dashboard') }}" class="font-semibold text-ink underline">Back to today</a>
                @endif
            </p>
        </div>
        <a href="{{ route('habits.create') }}" class="btn btn-primary"><x-icon name="plus" /> Add habit</a>
    </div>

    {{-- Stats --}}
    <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="card p-5">
            <p class="flex items-center gap-2 text-caption text-pewter"><x-icon name="flame" class="size-4 text-ember" /> Current streak</p>
            <p class="mt-2 flex items-baseline gap-1.5 text-[40px] font-semibold tracking-tight"><x-counter class="text-ember" data-stat="streak" :value="$streak['current']" /><span class="text-body-sm font-normal text-pewter">days</span></p>
        </div>
        <div class="card p-5">
            <p class="flex items-center gap-2 text-caption text-pewter"><x-icon name="trophy" class="size-4" /> Best streak</p>
            <p class="mt-2 flex items-baseline gap-1.5 text-[40px] font-semibold tracking-tight"><x-counter class="text-ember" data-stat="best" :value="$streak['best']" /><span class="text-body-sm font-normal text-pewter">days</span></p>
        </div>
        <div class="card p-5">
            <p class="flex items-center gap-2 text-caption text-pewter"><x-icon name="circle-check" class="size-4" /> {{ $isToday ? 'Done today' : 'Done that day' }}</p>
            <p class="mt-2 flex items-baseline gap-1 text-[40px] font-semibold tracking-tight"><x-counter class="text-ember" data-stat="done" :value="$doneCount" /><span class="text-heading-sm font-normal text-steel">/</span><x-counter class="text-heading-sm text-steel" data-stat="total" :value="$rows->count()" /></p>
        </div>
        <div class="card p-5">
            <p class="flex items-center gap-2 text-caption text-pewter"><x-icon name="calendar" class="size-4" /> {{ $month->format('F') }} completion</p>
            <p class="mt-2 flex items-baseline gap-1 text-[40px] font-semibold tracking-tight"><x-counter class="text-ember" data-stat="rate" :value="$monthRate" /><span class="text-heading-sm font-normal text-steel">%</span></p>
        </div>
    </div>

    {{-- A year of check-ins: GitHub-style heat map that folds up into a 3D skyline --}}
    @php
        $skylineOptions = [
            'endDate' => $today->toDateString(),
            'unit' => 'check-in',
            'weekStart' => 1,
            'href' => route('dashboard'),
            'palette' => ['#ffd1b8', '#ffac80', '#ff8340', '#ff5900'], // one hue, opacity 28% / 50% / 75% / 100%
        ];
    @endphp
    <section class="mb-6" aria-label="Your year in check-ins">
        <div data-skyline="{{ json_encode($skylineOptions) }}">
            <script type="application/json">{!! json_encode($activity, JSON_HEX_TAG) !!}</script>
        </div>
    </section>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Today's habits --}}
        <section class="card lg:col-span-2" aria-labelledby="habits-title">
            <div class="card-header">
                <div>
                    <h2 id="habits-title" class="text-subheading">{{ $isToday ? "Today's habits" : 'Habits' }}</h2>
                    <p class="text-body-sm text-pewter">Tap the box to complete a habit.</p>
                </div>
                <a href="{{ route('habits.index') }}" class="btn btn-outline btn-sm">Manage</a>
            </div>

            <ul class="divide-y divide-mist/40">
                @forelse ($rows as $r)
                    @php $h = $r['habit']; $multi = $h->target_count > 1; @endphp
                    <li data-habit-row data-url="{{ route('habits.log', $h) }}" data-date="{{ $date->toDateString() }}"
                        data-target="{{ $h->target_count }}" data-unit="{{ $h->unit }}">
                        <div class="flex items-center gap-3 px-6 py-3">
                            <x-spring-check :checked="$r['done']" :aria-label="'Complete '.$h->name" />
                            <div class="min-w-0 flex-1">
                                <p class="flex items-center gap-2 truncate font-medium text-ink">
                                    <span class="size-2.5 flex-none rounded-md" style="background: {{ $h->category->color }}" aria-hidden="true"></span>
                                    {{ $h->name }}
                                </p>
                                <p class="text-caption text-pewter" data-progress-text>
                                    @if ($multi) {{ $r['value'] }} / {{ $h->target_count }} {{ $h->unit }} @else {{ $r['done'] ? 'Done' : 'Not yet' }} @endif
                                </p>
                                @if ($multi)
                                    <div class="mt-1.5 h-1 max-w-48 overflow-hidden rounded bg-fog">
                                        <span data-progress-bar class="block h-full rounded bg-ember transition-[width] duration-500" style="width: {{ min(100, $r['value'] / $h->target_count * 100) }}%"></span>
                                    </div>
                                @endif
                            </div>

                            <span data-streak-wrap class="badge" @if ($r['streak'] === 0) hidden @endif title="Habit streak">
                                <x-icon name="flame" class="size-3.5 text-ember" /><x-counter data-streak :value="$r['streak']" />
                            </span>

                            @if ($r['timed'])
                                <button type="button" class="btn btn-outline btn-sm" data-timer-start><x-icon name="timer" class="size-3.5" /> Timer</button>
                                <div data-timer-panel hidden class="flex items-center gap-2">
                                    <span class="rounded-md bg-ink px-2 py-1 text-body-sm font-semibold text-paper"><span data-timer-display class="ctr">00:00</span></span>
                                    <button type="button" class="btn btn-primary btn-sm" data-timer-stop>Stop</button>
                                    <button type="button" class="btn btn-ghost btn-icon btn-sm" data-timer-cancel aria-label="Cancel timer"><x-icon name="x" class="size-4" /></button>
                                </div>
                            @elseif ($multi)
                                <button type="button" class="btn btn-outline btn-sm" data-add aria-label="Add one {{ $h->unit }}"><x-icon name="plus" class="size-3.5" /> 1</button>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center text-pewter">
                        No active habits yet. <a href="{{ route('habits.create') }}" class="font-semibold text-ink underline">Create one</a>.
                    </li>
                @endforelse
            </ul>
        </section>

        {{-- Calendar + mood --}}
        <div class="space-y-6">
            <section class="card" aria-labelledby="cal-title">
                <div class="card-header">
                    <h2 id="cal-title" class="text-subheading">{{ $month->format('F Y') }}</h2>
                    <div class="flex gap-1">
                        <a href="{{ route('dashboard', ['month' => $prev, 'date' => $date->toDateString()]) }}" class="btn btn-outline btn-icon" aria-label="Previous month"><x-icon name="chevron-left" /></a>
                        @if ($canNext)
                            <a href="{{ route('dashboard', ['month' => $next->format('Y-m'), 'date' => $date->toDateString()]) }}" class="btn btn-outline btn-icon" aria-label="Next month"><x-icon name="chevron-right" /></a>
                        @else
                            <span class="btn btn-outline btn-icon opacity-40" aria-hidden="true"><x-icon name="chevron-right" /></span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-2 grid grid-cols-7 gap-1.5 text-center text-caption text-pewter">
                        @foreach ($weekdays as $wd) <span>{{ $wd }}</span> @endforeach
                    </div>
                    <div class="grid grid-cols-7 gap-1.5">
                        @foreach ($weeks as $week)
                            @foreach ($week as $d)
                                @php
                                    $cls = collect(['cal-day', $d['in_month'] ? '' : 'is-out', $d['is_future'] ? 'is-future' : '', $d['is_today'] ? 'is-today' : '', $d['date'] === $date->toDateString() ? 'is-selected' : ''])->filter()->implode(' ');
                                @endphp
                                @if ($d['is_future'])
                                    <span class="{{ $cls }}">{{ $d['day'] }}</span>
                                @else
                                    <a href="{{ route('dashboard', ['date' => $d['date'], 'month' => $month->format('Y-m')]) }}" class="{{ $cls }}"
                                        data-cal-date="{{ $d['date'] }}" style="--r: {{ round($d['ratio'], 2) }}" @if ($d['ratio'] > 0.55) data-high @endif
                                        aria-label="{{ \Carbon\Carbon::parse($d['date'])->format('j F') }}: {{ $d['done'] }} of {{ $d['total'] }} habits">
                                        {{ $d['day'] }}
                                    </a>
                                @endif
                            @endforeach
                        @endforeach
                    </div>
                    <div class="mt-4 flex items-center justify-between text-caption text-pewter">
                        <span>Less</span>
                        <span class="flex gap-1">
                            @foreach ([0, 0.25, 0.5, 0.75, 1] as $r)
                                <span class="size-3 rounded-md" style="background: color-mix(in srgb, var(--color-ember) {{ $r * 100 }}%, var(--color-fog))"></span>
                            @endforeach
                        </span>
                        <span>All done</span>
                    </div>
                </div>
            </section>

            <section class="card" aria-labelledby="mood-title">
                <div class="card-header">
                    <h2 id="mood-title" class="text-subheading">How do you feel?</h2>
                    <a href="{{ route('mood.index') }}" class="btn btn-outline btn-sm">History</a>
                </div>
                <div class="card-body" data-mood-quick data-url="{{ route('mood.store') }}" data-date="{{ $date->toDateString() }}">
                    <x-peek-rating shape="heart" :value="$mood?->mood_level ?? 0" :labels="$moodLabels" label="Mood" />
                    <p class="mt-1 text-caption text-pewter" data-mood-label>
                        {{ $mood ? 'Saved: '.config('habitude.moods')[$mood->mood_level] : 'Pick one, it saves right away.' }}
                    </p>
                </div>
            </section>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <section class="card" aria-labelledby="cat-title">
            <div class="card-header"><h2 id="cat-title" class="text-subheading">By category</h2><span class="text-caption text-pewter">{{ $month->format('F') }}</span></div>
            <ul class="card-body space-y-3">
                @forelse ($categoryProgress as $c)
                    <li>
                        <div class="mb-1 flex items-center justify-between text-body-sm">
                            <span class="flex items-center gap-2"><span class="size-3 rounded-md" style="background: {{ $c->color }}"></span>{{ $c->icon }} {{ $c->name }}</span>
                            <span class="font-semibold">{{ $c->completed }}</span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded bg-fog"><span class="block h-full rounded" style="width: {{ $c->completed / $categoryProgress->max('completed') * 100 }}%; background: {{ $c->color }}"></span></div>
                    </li>
                @empty
                    <li class="text-body-sm text-pewter">Complete a habit to see progress here.</li>
                @endforelse
            </ul>
        </section>

        <section class="card" aria-labelledby="rem-title">
            <div class="card-header"><h2 id="rem-title" class="text-subheading">Reminders</h2><x-icon name="bell" class="size-4 text-steel" /></div>
            <ul class="card-body space-y-3">
                @forelse ($reminders as [$habit, $reminder])
                    <li class="flex items-center justify-between text-body-sm">
                        <span class="truncate">{{ $habit->name }}</span>
                        <span class="badge flex-none"><x-icon name="clock" class="size-3" />{{ \Carbon\Carbon::parse($reminder->remind_at)->format('H:i') }}</span>
                    </li>
                @empty
                    <li class="text-body-sm text-pewter">No reminders for this day.</li>
                @endforelse
            </ul>
        </section>

        <section class="card" aria-labelledby="ach-title">
            <div class="card-header"><h2 id="ach-title" class="text-subheading">Achievements</h2><a href="{{ route('achievements.index') }}" class="btn btn-outline btn-sm">All</a></div>
            <ul class="card-body space-y-3">
                @forelse ($recentAchievements as $a)
                    <li class="flex items-center gap-3">
                        <span class="flex size-9 flex-none items-center justify-center rounded-xl bg-ember text-paper"><x-icon name="trophy" /></span>
                        <span class="min-w-0"><span class="block truncate text-body-sm font-semibold">{{ $a->name }}</span><span class="block text-caption text-pewter">{{ \Carbon\Carbon::parse($a->pivot->earned_at)->format('j M Y') }}</span></span>
                    </li>
                @empty
                    <li class="text-body-sm text-pewter">Nothing unlocked yet. Check off your first habit!</li>
                @endforelse
            </ul>
        </section>
    </div>
</x-app-layout>
