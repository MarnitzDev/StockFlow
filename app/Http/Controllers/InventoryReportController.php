<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class InventoryReportController extends Controller
{
    public function index()
    {
        $products = Product::all();

        $currentStock = $products->map(function ($product) {
            return [
                'name' => $product->name,
                'sku' => $product->sku,
                'stock' => $product->stock,
                'category' => $product->category,
            ];
        });

        $outOfStock = $products->filter(function ($product) {
            return $product->stock == 0;
        })->values();

        $inventoryValuation = $products->sum(function ($product) {
            return $product->stock * $product->price;
        });

        return Inertia::render('InventoryReport/Index', [
            'currentStock' => $currentStock,
            'outOfStock' => $outOfStock,
            'inventoryValuation' => $inventoryValuation,
        ]);
    }
}
