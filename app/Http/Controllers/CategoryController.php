<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('habits')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $this->validated($request, $category);

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        // habits.category_id is restricted on delete, so explain instead of failing.
        if ($category->habits()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', "\"{$category->name}\" still has habits. Move or delete them first.");
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted.');
    }

    private function validated(Request $request, ?Category $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('categories', 'name')->ignore($category)],
            'icon' => ['nullable', 'string', 'max:8'],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ], [
            'name.required' => 'Category name is required.',
            'name.unique' => 'This category name is already used.',
            'color.regex' => 'Pick a valid color.',
        ]);

        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}
