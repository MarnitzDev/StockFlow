<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryImage;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function items()
    {
        $inventoryItems = Inventory::with(['category'])->get();
        return Inertia::render('App/Inventory/Items', ['items' => $inventoryItems]);
    }

    public function create()
    {
        return Inertia::render('App/Inventory/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:inventories',
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

    public function edit(Inventory $inventory)
    {
        return Inertia::render('App/Inventory/Edit', ['item' => $inventory]);
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:inventories,sku,' . $inventory->id,
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
            'stock' => 'required|integer',
            'type' => 'required|in:in,out',
            'reason' => 'nullable|string|max:255',
        ]);

        \Log::info('Validated Data:', $validated);

        DB::transaction(function () use ($inventory, $validated) {
            $stockMovement = StockMovement::create([
                'inventory_id' => $inventory->id,
                'stock' => abs($validated['stock']),
                'type' => $validated['type'],
                'reason' => $validated['reason'] ?? 'Stock update',
            ]);

            \Log::info('Stock Movement Created:', $stockMovement->toArray());

            if ($validated['type'] === 'in') {
                $inventory->increment('stock', $validated['stock']);
            } else {
                $inventory->decrement('stock', $validated['stock']);
            }

            \Log::info('Inventory Item After Update:', $inventory->fresh()->toArray());
        });

        return redirect()->back()->with('message', 'Stock updated successfully');
    }

    public function lowStockAlert()
    {
        $lowStockItems = Inventory::where('stock', '<=', DB::raw('low_stock_threshold'))->get();
        return Inertia::render('App/Inventory/LowStock', ['items' => $lowStockItems]);
    }
}
