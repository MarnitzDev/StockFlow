<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
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
        ]);

        Product::create($validated);

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
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('message', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('message', 'Product deleted successfully');
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:in,out',
            'reason' => 'nullable|string',
        ]);

        $movement = new StockMovement([
            'quantity' => $request->quantity,
            'type' => $request->type,
            'reason' => $request->reason,
        ]);

        $product->stockMovements()->save($movement);

        if ($request->type === 'in') {
            $product->stock += $request->quantity;
        } else {
            $product->stock -= $request->quantity;
        }

        $product->save();

        return redirect()->route('products.index')->with('message', 'Stock updated successfully');
    }

    public function lowStockAlert()
    {
        $lowStockProducts = Product::where('stock', '<=', DB::raw('low_stock_threshold'))->get();
        return Inertia::render('Products/LowStock', ['products' => $lowStockProducts]);
    }
}
