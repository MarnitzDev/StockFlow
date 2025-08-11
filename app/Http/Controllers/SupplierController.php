<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index(): Response
    {
        $suppliers = Supplier::all()->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ];
        });

        return Inertia::render('Supplier/Index', [
            'suppliers' => $suppliers,
        ]);
    }

    public function show(Supplier $supplier): Response
    {
        $products = $supplier->inventories()->with(['category', 'primaryImage'])
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

        return Inertia::render('Supplier/Show', [
            'supplier' => $supplier,
            'products' => $products,
        ]);
    }

    public function purchaseCheckout(Request $request): RedirectResponse
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:inventories,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $request->supplier_id,
                'order_date' => now(),
                'total_amount' => $request->total,
                'status' => 'pending',
            ]);

            foreach ($request->items as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                ]);

                $inventory = Inventory::find($item['product_id']);
                $inventory->stock += $item['quantity'];
                $inventory->save();
            }

            DB::commit();

            return redirect()->route('supplier.purchase.index')->with('success', 'Purchase order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('supplier.purchase.index')->with('error', 'An error occurred while creating the purchase order.');
        }
    }
}
