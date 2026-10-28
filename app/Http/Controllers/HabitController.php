<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Habit;
use App\Models\Tag;
use App\Support\CurrentUser;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    public function index()
    {
        $habits = CurrentUser::get()->habits()
            ->with(['category', 'tags'])
            ->latest()
            ->get();

        return view('habits.index', compact('habits'));
    }

    public function create()
    {
        return view('habits.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $habit = CurrentUser::get()->habits()->create($data);
        $habit->tags()->sync($request->input('tags', []));

        return redirect()->route('habits.index')
            ->with('success', 'Habit created.');
    }

    public function edit(Habit $habit)
    {
        $this->authorizeOwner($habit);

        return view('habits.edit', ['habit' => $habit->load('tags')] + $this->formData());
    }

    public function update(Request $request, Habit $habit)
    {
        $this->authorizeOwner($habit);

        $habit->update($this->validated($request));
        $habit->tags()->sync($request->input('tags', []));

        return redirect()->route('habits.index')
            ->with('success', 'Habit updated.');
    }

    public function destroy(Habit $habit)
    {
        $this->authorizeOwner($habit);

        $habit->delete();

        return redirect()->route('habits.index')
            ->with('success', 'Habit deleted.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ];
    }

    private function authorizeOwner(Habit $habit): void
    {
        abort_unless($habit->user_id === CurrentUser::get()->id, 404);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
            'target_count' => ['required', 'integer', 'min:1', 'max:1000'],
            'unit' => ['required', 'string', 'max:20'],
            'tags' => ['array'],
            'tags.*' => ['exists:tags,id'],
        ], [
            'category_id.required' => 'Choose a category.',
            'name.required' => 'Habit name is required.',
            'target_count.required' => 'Set a daily target.',
            'target_count.min' => 'The target must be at least 1.',
            'unit.required' => 'Unit is required, for example glasses or pages.',
        ]);

        // An unchecked switch is not submitted, so read it as a boolean.
        $data['is_active'] = $request->boolean('is_active');
        unset($data['tags']);

        return $data;
    }
}
