<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return Inertia::render('Products/Index', ['products' => $products]);
    }

    public function create()
    {
        return Inertia::render('Products/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'low_stock_threshold' => 'required|integer|min:0',
            'unit_of_measurement' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($validated) {
            $product = Product::create($validated);

            StockMovement::create([
                'product_id' => $product->id,
                'quantity' => $validated['stock'],
                'type' => 'in',
                'reason' => 'Initial stock',
            ]);
        });

        return redirect()->route('products.index')->with('message', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Products/Edit', ['product' => $product]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'low_stock_threshold' => 'required|integer|min:0',
            'unit_of_measurement' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($product, $validated) {
            $oldStock = $product->stock;
            $product->update($validated);

            if ($oldStock != $validated['stock']) {
                $difference = $validated['stock'] - $oldStock;
                StockMovement::create([
                    'product_id' => $product->id,
                    'quantity' => abs($difference),
                    'type' => $difference > 0 ? 'in' : 'out',
                    'reason' => 'Stock adjustment',
                ]);
            }
        });

        return redirect()->route('products.index')->with('message', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('message', 'Product deleted successfully');
    }

    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out',
            'reason' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($product, $validated) {
            StockMovement::create([
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'type' => $validated['type'],
                'reason' => $validated['reason'] ?? 'Stock update',
            ]);

            if ($validated['type'] === 'in') {
                $product->increment('stock', $validated['quantity']);
            } else {
                $product->decrement('stock', $validated['quantity']);
            }
        });

        return redirect()->route('products.index')->with('message', 'Stock updated successfully');
    }

    public function lowStockAlert()
    {
        $lowStockProducts = Product::where('stock', '<=', DB::raw('low_stock_threshold'))->get();
        return Inertia::render('Products/LowStock', ['products' => $lowStockProducts]);
    }
}
