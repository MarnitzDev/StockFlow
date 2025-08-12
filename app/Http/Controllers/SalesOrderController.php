<?php

namespace App\Http\Controllers\App;

use App\Models\Customer;
use App\Models\Inventory;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;


class SalesOrderController extends Controller
{
    public function index()
    {
        $salesOrders = SalesOrder::with(['customer', 'items.inventory'])->latest()->get();
        return Inertia::render('App/Sales/Orders/Index', ['salesOrders' => $salesOrders]);
    }

    public function create()
    {
        $customers = Customer::all();
        $inventoryItems = Inventory::all();
        return Inertia::render('App/Sales/Orders/Create', [
            'customers' => $customers,
            'inventoryItems' => $inventoryItems,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:inventories,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $totalAmount = 0;
        $orderItems = [];

        DB::beginTransaction();

        try {
            foreach ($validated['items'] as $item) {
                $inventory = Inventory::findOrFail($item['inventory_id']);

                if ($inventory->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for item: " . $inventory->name);
                }

                $subtotal = number_format($inventory->price * $item['quantity'], 2, '.', '');
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'inventory_id' => $item['inventory_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => number_format($inventory->price, 2, '.', ''),
                    'subtotal' => $subtotal,
                ];

                // Decrease inventory stock
                $inventory->decrement('stock', $item['quantity']);
            }

            if ($totalAmount <= 0) {
                throw new \Exception("Total order amount must be greater than zero.");
            }

            $salesOrder = SalesOrder::create([
                'order_number' => 'SO-' . uniqid(),
                'customer_id' => $validated['customer_id'],
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            $salesOrder->items()->createMany($orderItems);

            DB::commit();

            return redirect()->route('sales.orders.index')->with('success', 'Sales order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error creating sales order: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $salesOrder = SalesOrder::with(['customer', 'items.inventory'])->findOrFail($id);

        return Inertia::render('App/Sales/Orders/Show', [
            'salesOrder' => $salesOrder
        ]);
    }
}
