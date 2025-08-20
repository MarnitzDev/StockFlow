<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Category;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function items()
    {
        $inventoryItems = Inventory::with(['category'])->select([
            'id', 'name', 'sku', 'description', 'stock', 'price', 'category_id',
            'low_stock_threshold', 'vendor_id', 'image_url', 'available_on_pos'
        ])->get();
        return Inertia::render('App/Inventory/Items', ['items' => $inventoryItems]);
    }

    public function create()
    {
        return Inertia::render('App/Inventory/Create');
    }

    public function show($id)
    {
        $item = Inventory::with(['category', 'vendor'])->findOrFail($id);
        return Inertia::render('App/Inventory/Show', [
            'item' => $item
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:inventory',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'low_stock_threshold' => 'required|integer|min:0',
            'unit_of_measurement' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($validated) {
            $inventoryItem = Inventory::create($validated);

            StockMovement::create([
                'inventory_id' => $inventoryItem->id,
                'stock' => $validated['stock'],
                'type' => 'in',
                'reason' => 'Initial stock',
            ]);
        });

        return redirect()->route('inventory.items')->with('message', 'Inventory item created successfully');
    }

    public function edit($id)
    {
        $item = Inventory::findOrFail($id);
        $categories = Category::all();

        return Inertia::render('App/Inventory/Edit', [
            'item' => $item,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:inventory,sku,' . $inventory->id,
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'low_stock_threshold' => 'required|integer|min:0',
            'unit_of_measurement' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($inventory, $validated) {
            $oldStock = $inventory->stock;
            $inventory->update($validated);

            if ($oldStock != $validated['stock']) {
                $difference = $validated['stock'] - $oldStock;
                StockMovement::create([
                    'inventory_id' => $inventory->id,
                    'stock' => abs($difference),
                    'type' => $difference > 0 ? 'in' : 'out',
                    'reason' => 'Stock adjustment',
                ]);
            }
        });

        return redirect()->route('inventory.items')->with('message', 'Inventory item updated successfully');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventory.items')->with('message', 'Inventory item deleted successfully');
    }

    public function updateStock(Request $request, Inventory $inventory)
    {
        \Log::info('Update Stock Request:', $request->all());
        \Log::info('Inventory Item:', $inventory->toArray());

        $validated = $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:in,out',
            'reason' => 'required|string|max:255',
            'unit_price' => 'required|numeric|min:0',
        ]);

        \Log::info('Validated Data:', $validated);

        DB::transaction(function () use ($inventory, $validated) {
            $stockMovement = StockMovement::recordMovement(
                $inventory->id,
                $validated['quantity'],
                $validated['type'],
                $validated['reason'],
                auth()->id(),
                $validated['unit_price']
            );

            \Log::info('Stock Movement Created:', $stockMovement->toArray());
            \Log::info('Inventory Item After Update:', $inventory->fresh()->toArray());
        });

        return redirect()->back()->with('message', 'Stock updated successfully');
    }

    public function lowStockAlert()
    {
        $lowStockItems = Inventory::where('stock', '<=', DB::raw('low_stock_threshold'))->get();
        return Inertia::render('App/Inventory/LowStock', ['items' => $lowStockItems]);
    }

    public function updatePOSAvailability(Request $request, $id)
    {
        $item = Inventory::findOrFail($id);
        $item->update([
            'available_on_pos' => $request->available_on_pos
        ]);

        return back()->with('success', 'POS availability updated successfully');
    }

    public function stockMovements()
    {
        $stockMovements = StockMovement::with('inventory')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('App/Inventory/StockMovements', [
            'stockMovements' => $stockMovements
        ]);
    }
}
