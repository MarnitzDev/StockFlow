<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\VendorProduct;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class VendorController extends Controller
{
    protected $purchaseOrderService;

    public function __construct(PurchaseOrderService $purchaseOrderService)
    {
        $this->purchaseOrderService = $purchaseOrderService;
    }

    public function index(): Response
    {
        $vendors = Vendor::all()->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'email' => $vendor->email,
                'phone' => $vendor->phone,
                'address' => $vendor->address,
            ];
        });

        return Inertia::render('Vendors/Index', [
            'vendors' => $vendors,
        ]);
    }

    public function show(Vendor $vendor): Response
    {
        $vendor->load('products');
        $products = $vendor->products->map(function ($product) {
            $inventory = Inventory::where('sku', $product->sku)->first();
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'sku' => $product->sku,
                'stock' => $product->stock ?? 0,
                'description' => $product->description,
                'inventory_stock' => $inventory ? $inventory->stock : 0,
                'image_url' => $product->image_url ?? null,
            ];
        });

        $calculatedTotals = $this->purchaseOrderService->calculateProductTotals($products);

        $tempPurchaseOrder = [
            'id' => null,
            'order_number' => 'PO-' . uniqid(),
            'vendor' => [
                'id' => $vendor->id,
                'name' => $vendor->name,
            ],
            'order_date' => now()->toDateTimeString(),
            'total_amount' => $calculatedTotals['total'] ?? 0,
            'status' => 'draft',
            'items' => [],
        ];

        return Inertia::render('Vendors/PurchaseOrderCreate', [
            'vendor' => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'email' => $vendor->email,
                'phone' => $vendor->phone,
                'address' => $vendor->address,
            ],
            'calculatedTotals' => $calculatedTotals,
            'products' => $products,
            'purchaseOrder' => $tempPurchaseOrder,
        ]);
    }

    public function storePurchaseOrder(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:vendor_products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            if (isset($validated['purchase_order_id'])) {
                $purchaseOrder = PurchaseOrder::findOrFail($validated['purchase_order_id']);

                if ($purchaseOrder->vendor_id !== $vendor->id) {
                    return response()->json(['error' => 'Invalid purchase order'], 403);
                }

                $purchaseOrder->update([
                    'total_amount' => $validated['total'],
                    'status' => 'pending',
                ]);
            } else {
                $purchaseOrder = PurchaseOrder::create([
                    'vendor_id' => $vendor->id,
                    'order_date' => now(),
                    'total_amount' => $validated['total'],
                    'status' => 'pending',
                    'order_number' => 'PO-' . uniqid(),
                ]);
            }

            if (isset($validated['purchase_order_id'])) {
                $purchaseOrder->items()->delete();
            }

            foreach ($validated['items'] as $item) {
                $purchaseOrderItem = $purchaseOrder->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                ]);

                $vendorProduct = VendorProduct::findOrFail($item['product_id']);
                $inventory = Inventory::updateOrCreate(
                    ['sku' => $vendorProduct->sku],
                    [
                        'name' => $vendorProduct->name,
                        'description' => $vendorProduct->description,
                        'price' => $vendorProduct->price,
                        'category_id' => $vendorProduct->category_id,
                    ]
                );

                $inventory->increment('stock', $item['quantity']);
            }

            DB::commit();

            return Inertia::render('Vendors/PurchaseOrderSuccess', [
                'message' => 'Purchase order finalized successfully',
                'purchaseOrder' => $purchaseOrder->fresh()->load('items'),
                'vendorId' => $vendor->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Inertia::render('Vendors/PurchaseOrderCreate', [
                'error' => 'Failed to finalize purchase order: ' . $e->getMessage(),
            ]);
        }
    }

    public function purchaseShow(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['vendor', 'items.product']);

        $items = $purchaseOrder->items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => round($item->quantity * $item->unit_price, 2),
                'sku' => $item->product->sku,
                'description' => $item->product->description,
                'image_url' => $item->product->image_url ?? null,
            ];
        });

        $subtotal = round($items->sum('total'), 2);
        $tax = round($subtotal * 0.1, 2);
        $total = round($subtotal + $tax, 2);

        $purchaseOrderData = [
            'id' => $purchaseOrder->id,
            'order_number' => $purchaseOrder->order_number,
            'vendor' => [
                'id' => $purchaseOrder->vendor->id,
                'name' => $purchaseOrder->vendor->name,
            ],
            'order_date' => $purchaseOrder->order_date,
            'total_amount' => $purchaseOrder->total_amount,
            'status' => $purchaseOrder->status,
            'items' => $items,
        ];

        $calculatedTotals = [
            'items' => $items,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ];

        return Inertia::render('Vendors/PurchaseOrderDetails', [
            'purchaseOrder' => $purchaseOrderData,
            'calculatedTotals' => $calculatedTotals,
        ]);
    }

    public function purchaseHistory()
    {
        $purchaseOrders = PurchaseOrder::with(['vendor', 'items.product'])
            ->latest()
            ->paginate(15)
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
}
