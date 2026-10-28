<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Habit;
use App\Models\Tag;
use App\Services\AchievementService;
use App\Services\StreakService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HabitController extends Controller
{
    public function index(Request $request, StreakService $streaks)
    {
        $user = $request->user();
        $today = $user->today();

        $group = in_array($request->query('group'), ['category', 'status', 'none'], true) ? $request->query('group') : 'category';
        $view = $request->query('view') === 'grid' ? 'grid' : 'list';

        $habits = $user->habits()->with(['category', 'tags'])->orderBy('name')->get()
            ->each(fn ($h) => $h->streak = $streaks->habitStreak($h, $today));

        $groups = match ($group) {
            'category' => $habits->groupBy(fn ($h) => $h->category->name)->sortKeys(),
            'status' => $habits->groupBy(fn ($h) => $h->is_active ? 'Active' : 'Paused'),
            default => collect(['All habits' => $habits]),
        };

        return view('habits.index', compact('groups', 'group', 'view', 'habits'));
    }

    public function create()
    {
        return view('habits.create', $this->formData());
    }

    public function store(Request $request, AchievementService $achievements)
    {
        $data = $this->validated($request);

        $habit = $request->user()->habits()->create($data);
        $habit->tags()->sync($request->input('tags', []));
        $achievements->award($request->user());

        return redirect()->route('habits.edit', $habit)
            ->with('success', 'Habit created. You can add reminders below.');
    }

    public function edit(Request $request, Habit $habit)
    {
        $this->authorizeOwner($request, $habit);

        return view('habits.edit', ['habit' => $habit->load(['tags', 'reminders'])] + $this->formData());
    }

    public function update(Request $request, Habit $habit)
    {
        $this->authorizeOwner($request, $habit);

        $habit->update($this->validated($request));
        $habit->tags()->sync($request->input('tags', []));

        return redirect()->route('habits.index')
            ->with('success', 'Habit updated.');
    }

    public function destroy(Request $request, Habit $habit)
    {
        $this->authorizeOwner($request, $habit);

        $habit->delete();

        return redirect()->route('habits.index')
            ->with('success', 'Habit deleted.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'units' => config('habitude.units'),
        ];
    }

    private function authorizeOwner(Request $request, Habit $habit): void
    {
        abort_unless($habit->user_id === $request->user()->id, 404);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'target_count' => ['required', 'integer', 'min:1', 'max:100000'],
            'unit' => ['required', 'string', Rule::in(config('habitude.units'))],
            'tags' => ['array'],
            'tags.*' => ['exists:tags,id'],
        ], [
            'category_id.required' => 'Choose a category.',
            'name.required' => 'Habit name is required.',
            'target_count.required' => 'Set a daily target.',
            'target_count.min' => 'The target must be at least 1.',
            'unit.required' => 'Choose a unit.',
            'unit.in' => 'Choose one of the listed units.',
        ]);

        // An unchecked switch is not submitted, so read it as a boolean.
        $data['is_active'] = $request->boolean('is_active');
        unset($data['tags']);

        return $data;
    }
}
