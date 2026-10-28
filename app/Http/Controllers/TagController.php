<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::withCount('habits')->orderBy('name')->get();

        return view('tags.index', compact('tags'));
    }

    public function create()
    {
        return view('tags.create');
    }

    public function store(Request $request)
    {
        Tag::create($this->validated($request));

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag created.');
    }

    public function edit(Tag $tag)
    {
        return view('tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $tag->update($this->validated($request, $tag));

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag updated.');
    }

    public function destroy(Tag $tag)
    {
        // The habit_tag pivot rows are removed by the cascade rule.
        $tag->delete();

        return redirect()->route('admin.tags.index')
            ->with('success', 'Tag deleted.');
    }

    private function validated(Request $request, ?Tag $tag = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:30', Rule::unique('tags', 'name')->ignore($tag)],
        ], [
            'name.required' => 'Tag name is required.',
            'name.unique' => 'This tag already exists.',
        ]);
    }
}
