<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class StreakService
{
    /**
     * Consecutive days (ending today, or yesterday if today is still open)
     * on which the habit reached its daily target.
     */
    public function habitStreak(Habit $habit, CarbonImmutable $today): int
    {
        $dates = $habit->logs()
            ->where('value', '>=', $habit->target_count)
            ->pluck('logged_date')
            ->map(fn ($d) => $this->day($d));

        return $this->current($dates, $today);
    }

    /**
     * Overall streak: days with at least one completed habit.
     *
     * @return array{current: int, best: int}
     */
    public function overall(User $user, CarbonImmutable $today): array
    {
        $dates = $this->completedDates($user);

        return [
            'current' => $this->current($dates, $today),
            'best' => $this->best($dates),
        ];
    }

    /**
     * Distinct dates (Y-m-d) on which the user completed at least one habit.
     *
     * @return Collection<int, string>
     */
    public function completedDates(User $user): Collection
    {
        return HabitLog::query()
            ->join('habits', 'habits.id', '=', 'habit_logs.habit_id')
            ->where('habits.user_id', $user->id)
            ->whereNull('habits.deleted_at')
            ->whereColumn('habit_logs.value', '>=', 'habits.target_count')
            ->distinct()
            ->pluck('habit_logs.logged_date')
            ->map(fn ($d) => $this->day($d))
            ->unique()
            ->values();
    }

    /** @param Collection<int, string> $dates */
    private function current(Collection $dates, CarbonImmutable $today): int
    {
        $set = $dates->flip();
        $cursor = $set->has($today->toDateString()) ? $today : $today->subDay();
        $count = 0;

        while ($set->has($cursor->toDateString())) {
            $count++;
            $cursor = $cursor->subDay();
        }

        return $count;
    }

    /** @param Collection<int, string> $dates */
    private function best(Collection $dates): int
    {
        $sorted = $dates->sort()->values();
        $best = $run = 0;
        $previous = null;

        foreach ($sorted as $date) {
            $run = $previous && CarbonImmutable::parse($previous)->addDay()->toDateString() === $date ? $run + 1 : 1;
            $best = max($best, $run);
            $previous = $date;
        }

        return $best;
    }

    private function day(mixed $value): string
    {
        return CarbonImmutable::parse($value)->toDateString();
    }
}
