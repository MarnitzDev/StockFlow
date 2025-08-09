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
            // Create some sample customers if none exist
            for ($i = 0; $i < 10; $i++) {
                Customer::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->address,
                ]);
            }
        }

        // Get all customer IDs
        $customerIds = Customer::pluck('id')->toArray();

        // Ensure we have inventory items
        if (Inventory::count() == 0) {
            // You might want to call the InventorySeeder here or create some sample inventory items
            // For now, let's create a few sample items
            for ($i = 0; $i < 5; $i++) {
                Inventory::create([
                    'name' => $faker->word,
                    'sku' => $faker->unique()->ean8,
                    'stock' => $faker->numberBetween(10, 100),
                    'price' => $faker->randomFloat(2, 10, 1000),
                    'category_id' => 1, // Assuming you have at least one category
                ]);
            }
        }

        // Get all inventory item IDs
        $inventoryIds = Inventory::pluck('id')->toArray();

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
                $inventory = Inventory::find($faker->randomElement($inventoryIds));
                $quantity = $faker->numberBetween(1, 10);
                $unitPrice = $inventory->price;
                $subtotal = $quantity * $unitPrice;

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'inventory_id' => $inventory->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                $totalAmount += $subtotal;

                // Update inventory stock
                $inventory->decrement('stock', $quantity);
            }

            // Update the total amount of the sales order
            $order->update(['total_amount' => $totalAmount]);
        }
    }
}
