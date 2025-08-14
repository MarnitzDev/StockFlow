<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Inventory;
use Faker\Factory as Faker;

class SalesOrderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Ensure we have customers
        if (Customer::count() == 0) {
            Customer::factory()->count(10)->create();
        }

        // Get all customer IDs
        $customerIds = Customer::pluck('id')->toArray();

        // Get all inventory items with stock > 0, selecting only necessary fields
        $inventoryItems = Inventory::where('stock', '>', 0)
            ->select('id', 'stock', 'price')
            ->get();

        if ($inventoryItems->isEmpty()) {
            $this->command->info('No inventory items with stock available. Skipping sales order creation.');
            return;
        }

        // Create 20 sample sales orders
        for ($i = 0; $i < 20; $i++) {
            $order = SalesOrder::create([
                'order_number' => 'SO-' . $faker->unique()->numberBetween(1000, 9999),
                'customer_id' => $faker->randomElement($customerIds),
                'total_amount' => 0, // We'll calculate this based on order items
                'status' => $faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
                'notes' => $faker->optional()->sentence,
            ]);

            // Create 1 to 5 order items for each sales order
            $itemCount = $faker->numberBetween(1, 5);
            $totalAmount = 0;

            for ($j = 0; $j < $itemCount; $j++) {
                $inventoryItem = $inventoryItems->random();
                $quantity = $faker->numberBetween(1, min($inventoryItem->stock, 10));
                $unitPrice = $inventoryItem->price;
                $subtotal = $quantity * $unitPrice;

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'inventory_id' => $inventoryItem->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;

                // Update inventory stock
                $inventoryItem->decrement('stock', $quantity);
            }

            // Update the total amount of the sales order
            $order->update(['total_amount' => $totalAmount]);

            $this->command->info("Created sales order: {$order->order_number}");
        }

        $this->command->info('Sales orders created successfully.');
    }
}
