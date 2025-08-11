<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class POSController extends Controller
{
    public function index()
    {
        $products = Inventory::with(['category', 'images', 'primaryImage'])
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'sku' => $product->sku,
                    'stock' => $product->stock,
                    'category' => $product->category ? $product->category->name : null,
                    'image' => $product->primaryImage ? $product->primaryImage->image_path : null,
                ];
            });

        $suppliers = Supplier::all()->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ];
        });

        return Inertia::render('Supplier/App', [
            'products' => $products,
            'suppliers' => $suppliers,
        ]);
    }

    public function checkout(Request $request)
    {

    }
}
