<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendors\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class VendorController extends Controller
{
    public function index(): Response
    {
        $suppliers = Vendor::with('image')->get()->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
                'image' => $supplier->image ? $supplier->image->image_path : null,
            ];
        });

        return Inertia::render('Vendors/Index', [
            'suppliers' => $suppliers,
        ]);
    }

    public function show(Vendor $vendor): Response
    {
        $products = $vendor->products()
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'sku' => $product->sku,
                    'stock' => $product->stock,
                    'description' => $product->description,
                    // Add image handling if needed
                ];
            });

        return Inertia::render('Vendors/Show', [
            'vendor' => $vendor,
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
                'order_number' => 'PO-' . uniqid(),
            ]);

            $purchaseOrderItems = [];
            foreach ($request->items as $item) {
                $purchaseOrderItems[] = [
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                ];

                $inventory = Inventory::find($item['product_id']);
                $inventory->stock += $item['quantity'];
                $inventory->save();
            }

            PurchaseOrderItem::insert($purchaseOrderItems);

            DB::commit();

            return redirect()->route('suppliers.purchases.show', $purchaseOrder->id)->with('success', 'Purchase order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('suppliers.purchases.index')->with('error', 'An error occurred while creating the purchase order.');
        }
    }

    public function createPurchaseOrder(Vendor $supplier)
    {
        $products = $supplier->products;
        return Inertia::render('Vendors/PurchaseOrder', [
            'supplier' => $supplier,
            'products' => $products,
        ]);
    }

    public function storePurchaseOrder(Request $request, Vendor $supplier)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        $purchaseOrder = $supplier->purchaseOrders()->create([
            'total_amount' => $validated['total'],
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            $purchaseOrder->items()->create($item);
        }

        return redirect()->route('suppliers.show', $supplier->id)->with('success', 'Purchase order created successfully.');
    }

    public function purchaseHistory()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'items.product'])
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'supplier' => $order->supplier->name,
                    'order_date' => $order->order_date instanceof \DateTime
                        ? $order->order_date->format('Y-m-d')
                        : $order->order_date,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status,
                    'items_count' => $order->items->count(),
                ];
            });

        return Inertia::render('Vendors/PurchaseHistory', [
            'purchaseOrders' => $purchaseOrders
        ]);
    }

    public function purchaseShow(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product']);
        return Inertia::render('Vendors/PurchaseShow', [
            'purchaseOrder' => $purchaseOrder
        ]);
    }

    public function updatePurchase(Request $request, PurchaseOrder $purchase)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,cancelled',
        ]);

        if ($validated['status'] === 'paid' && $purchase->status !== 'paid') {
            try {
                DB::beginTransaction();

                $purchase->update([
                    'status' => 'paid',
                    'payment_date' => now(),
                ]);

                // Update inventory stock
                foreach ($purchase->items as $item) {
                    $inventory = Inventory::find($item->product_id);
                    $inventory->stock += $item->quantity;
                    $inventory->save();
                }

                DB::commit();

                return redirect()->route('suppliers.index')
                    ->with('success', 'Purchase order paid and inventory updated successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('suppliers.index')
                    ->with('error', 'An error occurred while processing the payment: ' . $e->getMessage());
            }
        } else {
            $purchase->update($validated);

            return redirect()->route('suppliers.index')
                ->with('success', 'Purchase order updated successfully.');
        }
    }
}
