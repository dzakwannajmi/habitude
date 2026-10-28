<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function store(Request $request, Habit $habit)
    {
        abort_unless($habit->user_id === $request->user()->id, 404);

        $data = $request->validate([
            'remind_at' => ['required', 'date_format:H:i'],
            'days_of_week' => ['required', 'array', 'min:1'],
            'days_of_week.*' => ['integer', 'between:1,7'],
        ], [
            'remind_at.required' => 'Pick a time.',
            'days_of_week.required' => 'Pick at least one day.',
        ]);

        $habit->reminders()->create([
            'remind_at' => $data['remind_at'],
            'days_of_week' => collect($data['days_of_week'])->map(fn ($d) => (int) $d)->unique()->sort()->values()->all(),
        ]);

        return redirect()->route('habits.edit', $habit)->with('success', 'Reminder added.');
    }

    public function update(Request $request, Reminder $reminder)
    {
        abort_unless($reminder->habit->user_id === $request->user()->id, 404);

        $reminder->update(['is_enabled' => $request->boolean('is_enabled')]);

        if ($request->expectsJson()) {
            return response()->json(['is_enabled' => $reminder->is_enabled]);
        }

        return redirect()->route('habits.edit', $reminder->habit_id)->with('success', 'Reminder updated.');
    }

    public function destroy(Request $request, Reminder $reminder)
    {
        abort_unless($reminder->habit->user_id === $request->user()->id, 404);

        $habitId = $reminder->habit_id;
        $reminder->delete();

        return redirect()->route('habits.edit', $habitId)->with('success', 'Reminder deleted.');
    }
}
