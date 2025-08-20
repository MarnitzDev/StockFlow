<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Inventory;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalesOrderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        DB::beginTransaction();

        try {
            // Ensure we have customers
            if (Customer::count() == 0) {
                Customer::factory()->count(10)->create();
                $this->command->info('Created 10 customers.');
            }

            // Get all customer IDs
            $customerIds = Customer::pluck('id')->toArray();

            if (empty($customerIds)) {
                throw new \Exception('No customers found in the database.');
            }

            // Get all inventory items with stock > 0, selecting only necessary fields
            $inventoryItems = Inventory::where('stock', '>', 0)
                ->select('id', 'stock', 'price')
                ->get();

            if ($inventoryItems->isEmpty()) {
                throw new \Exception('No inventory items with stock available. Skipping sales order creation.');
            }

            $this->command->info('Found ' . $inventoryItems->count() . ' inventory items with stock.');

            // Define date range
            $startDate = Carbon::create(Carbon::now()->year, 1, 1);
            $endDate = Carbon::now();

            // Create 50 sample sales orders
            for ($i = 0; $i < 100; $i++) {
                $orderDate = Carbon::parse($faker->dateTimeBetween($startDate, $endDate));

                $order = SalesOrder::create([
                    'order_number' => 'SO-' . $faker->unique()->numberBetween(1000, 9999),
                    'customer_id' => $faker->randomElement($customerIds),
                    'total_amount' => 0,
                    'status' => $faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
                    'notes' => $faker->optional()->sentence,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
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
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                    ]);

                    $totalAmount += $subtotal;

                    // Update inventory stock
                    $inventoryItem->decrement('stock', $quantity);
                }

                // Update the total amount of the sales order
                $order->update(['total_amount' => $totalAmount]);

                $this->command->info("Created sales order: {$order->order_number} on {$orderDate->toDateString()}");
            }

            DB::commit();
            $this->command->info('Sales orders created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in SalesOrderSeeder: ' . $e->getMessage());
            $this->command->error('Failed to create sales orders: ' . $e->getMessage());
        }
    }
}
