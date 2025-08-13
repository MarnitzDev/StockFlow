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
        $vendors = Vendor::with('image')->get()->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'image' => $vendor->image ? $vendor->image->image_path : null,
            ];
        });

        return Inertia::render('Vendors/Index', [
            'vendors' => $vendors,
        ]);
    }

    public function show(Vendor $vendor): Response
    {
        $products = $vendor->products()
            ->get()
            ->map(function ($product) {
                $inventory = Inventory::where('sku', $product->sku)->first();
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'sku' => $product->sku,
                    'stock' => $product->stock,
                    'description' => $product->description,
                    'inventory_stock' => $inventory ? $inventory->stock : 0,
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
            'vendor_id' => 'required|exists:vendors,id',
        ]);

        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::create([
                'vendor_id' => $request->vendor_id,
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
            }

            PurchaseOrderItem::insert($purchaseOrderItems);

            DB::commit();

            return redirect()->route('vendor.purchases.show', $purchaseOrder->id);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating purchase order: ' . $e->getMessage());
            return redirect()->route('vendor.purchases.history')
                ->with('error', 'An error occurred while creating the purchase order.');
        }
    }

    public function createPurchaseOrder(Vendor $vendor)
    {
        $products = $vendor->products;
        return Inertia::render('Vendors/PurchaseOrder', [
            'vendor' => $vendor,
            'products' => $products,
        ]);
    }

    public function storePurchaseOrder(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        $purchaseOrder = $vendor->purchaseOrders()->create([
            'total_amount' => $validated['total'],
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            $purchaseOrder->items()->create($item);
        }

        return redirect()->route('vendor.show', $vendor->id)->with('success', 'Purchase order created successfully.');
    }

    public function purchaseHistory()
    {
        $purchaseOrders = PurchaseOrder::with(['vendor', 'items.product'])
            ->latest()
            ->paginate(15)  // Or whatever number per page you prefer
            ->through(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'vendor' => $order->vendor->name,
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
        $purchaseOrder->load(['vendor', 'items.product']);
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

                return redirect()->route('vendor.purchases.history', $purchase->id)
                    ->with('success', 'Purchase order paid and inventory updated successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('vendor.purchases.show', $purchase->id)
                    ->with('error', 'An error occurred while processing the payment: ' . $e->getMessage());
            }
        } else {
            $purchase->update($validated);

            return redirect()->route('vendor.purchases.show', $purchase->id)
                ->with('success', 'Purchase order updated successfully.');
        }
    }
}
