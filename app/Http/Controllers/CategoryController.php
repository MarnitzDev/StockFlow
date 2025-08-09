<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return Inertia::render('Inventory/Categories/Index', ['categories' => $categories]);
    }

    public function create()
    {
        return Inertia::render('Inventory/Categories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|unique:categories|max:255',
            'description' => 'nullable',
        ]);

        Category::create($validated);

        return redirect()->route('inventory.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return Inertia::render('Inventory/Categories/Edit', ['category' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|unique:categories,slug,' . $category->id . '|max:255',
            'description' => 'nullable',
        ]);

        $category->update($validated);

        return redirect()->route('inventory.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('inventory.categories.index')->with('success', 'Category deleted successfully.');
    }
}
