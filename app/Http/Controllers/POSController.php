<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\SalesOrder;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class POSController extends Controller
{
    public function index()
    {
        $products = Inventory::with('category')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'sku' => $product->sku,
                    'stock' => $product->stock,
                    'category' => $product->category ? $product->category->name : null,
                    'image' => $product->image_url,
                    'available_on_pos' => $product->available_on_pos,
                ];
            });

        $suppliers = Vendor::all()->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ];
        });

        return Inertia::render('POS/Index', [
            'products' => $products,
            'suppliers' => $suppliers,
        ]);
    }


    public function checkoutPage()
    {
        return Inertia::render('POS/Index');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:inventory,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email',
            'total' => 'required|numeric|min:0',
        ]);

        $customerName = $request->customer_name ?: 'Walk-in Customer';
        $customerEmail = $request->customer_email ?: 'walkin_' . time() . '@example.com';

        $customer = Customer::firstOrCreate(
            ['email' => $customerEmail],
            [
                'name' => $customerName,
                'email' => $customerEmail,
            ]
        );

        $salesOrder = SalesOrder::create([
            'order_number' => 'POS-' . Str::random(8),
            'customer_id' => $customer->id,
            'total_amount' => $request->total,
            'status' => 'pending',
        ]);

        foreach ($request->items as $item) {
            $salesOrder->items()->create([
                'inventory_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal' => $item['price'] * $item['quantity'],
            ]);
        }

        return redirect()->route('pos.order.details', ['id' => $salesOrder->id]);

    }

    public function confirmOrder(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card',
        ]);

        $salesOrder = SalesOrder::findOrFail($id);

        if ($salesOrder->status !== 'pending') {
            return Inertia::render('POS/OrderError', [
                'message' => 'This order has already been processed.',
            ]);
        }

        \DB::beginTransaction();

        try {
            foreach ($salesOrder->items as $item) {
                $inventory = $item->inventory;
                if ($inventory->stock < $item->quantity) {
                    throw new \Exception("Insufficient stock for {$inventory->name}");
                }
                $inventory->decrement('stock', $item->quantity);
            }

            $salesOrder->update([
                'status' => 'completed',
                'notes' => 'POS Sale - ' . $request->payment_method,
            ]);

            \DB::commit();

            return Inertia::render('POS/OrderSuccess', [
                'order' => $salesOrder->fresh()->load('items.inventory', 'customer'),
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return Inertia::render('POS/OrderError', [
                'message' => 'Error processing order: ' . $e->getMessage(),
            ]);
        }
    }

    public function orderHistory()
    {
        $orders = SalesOrder::with('customer')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('POS/OrderHistory', [
            'orders' => $orders,
        ]);
    }

    public function show($id)
    {
        $order = SalesOrder::with(['items.inventory', 'customer'])->findOrFail($id);
        return Inertia::render('POS/OrderDetails', [
            'order' => $order
        ]);
    }

    public function getInventoryForPOS()
    {
        $items = Inventory::where('available_on_pos', true)->get();
        return response()->json($items);
    }
}
