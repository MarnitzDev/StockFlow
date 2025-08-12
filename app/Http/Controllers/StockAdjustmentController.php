<?php

namespace App\Http\Controllers\App;

use App\Models\Inventory;
use App\Models\StockAdjustment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $stockAdjustments = StockAdjustment::with(['inventory', 'user'])->latest()->get();
        return Inertia::render('App/Inventory/StockAdjustments/Index', ['stockAdjustments' => $stockAdjustments]);
    }

    public function create()
    {
        $inventoryItems = Inventory::all();
        return Inertia::render('App/Inventory/StockAdjustments/Create', ['inventoryItems' => $inventoryItems]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'stock' => 'required|integer',
            'type' => 'required|in:increase,decrease',
            'reason' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();

        $stockAdjustment = StockAdjustment::create($validated);

        $inventory = Inventory::find($validated['inventory_id']);
        if ($validated['type'] === 'increase') {
            $inventory->stock += $validated['stock'];
        } else {
            $inventory->stock -= $validated['stock'];
        }
        $inventory->save();

        return redirect()->route('inventory.stockAdjustments')->with('success', 'Stock adjustment created successfully.');
    }
}
