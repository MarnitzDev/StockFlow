<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('inventoryItems')->get();
        return Inertia::render('App/Inventory/Categories/Index', ['categories' => $categories]);
    }

    public function create()
    {
        return Inertia::render('App/Inventory/Categories/Create');
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
        $category->load('inventoryItems');
        $allItems = Inventory::all();
        $allCategories = Category::all();

        return Inertia::render('App/Inventory/Categories/Edit', [
            'category' => $category,
            'allItems' => $allItems,
            'allCategories' => $allCategories
        ]);
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

    public function addItems(Request $request, Category $category)
    {
        $validated = $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:inventories,id'
        ]);

        $category->inventoryItems()->attach($validated['item_ids']);

        return back()->with('success', 'Items added to category successfully.');
    }

    public function removeItem(Request $request, Category $category)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:inventories,id'
        ]);

        $category->inventoryItems()->detach($validated['item_id']);

        return back()->with('success', 'Item removed from category successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('inventory.categories.index')->with('success', 'Category deleted successfully.');
    }
}
