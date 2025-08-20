<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\VendorProduct;
use App\Models\Inventory;
use App\Models\Category;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    public function run()
    {
        DB::beginTransaction();

        try {
            $this->ensureVendorsExist();
            $this->ensureVendorProductsExist();
            $this->ensureInventoryItemsExist();

            $vendorIds = $this->getVendorIds();
            $inventoryItems = $this->getInventoryItems();

            $this->createPurchaseOrders($vendorIds, $inventoryItems);

            DB::commit();
            $this->command->info('Purchase orders created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in PurchaseOrderSeeder: ' . $e->getMessage());
            $this->command->error('Failed to create purchase orders: ' . $e->getMessage());
        }
    }

    protected function ensureVendorsExist()
    {
        if (Vendor::count() == 0) {
            Vendor::factory()->count(5)->create();
            $this->command->info('Created 5 vendors.');
        }
    }

    protected function ensureVendorProductsExist()
    {
        if (VendorProduct::count() == 0) {
            $vendors = Vendor::all();
            $categories = Category::all();

            if ($categories->isEmpty()) {
                Category::factory()->count(5)->create();
                $categories = Category::all();
            }

            foreach ($vendors as $vendor) {
                VendorProduct::factory()
                    ->count(5)
                    ->create([
                        'vendor_id' => $vendor->id,
                        'category_id' => $categories->random()->id,
                    ]);
            }
            $this->command->info('Created vendor products.');
        }
    }

    protected function ensureInventoryItemsExist()
    {
        if (Inventory::count() == 0) {
            VendorProduct::all()->each(function ($vendorProduct) {
                Inventory::create([
                    'sku' => $vendorProduct->sku,
                    'name' => $vendorProduct->name,
                    'description' => $vendorProduct->description,
                    'category_id' => $vendorProduct->category_id,
                    'price' => $vendorProduct->price * 1.2,
                    'stock' => 0,
                ]);
            });
            $this->command->info('Created inventory items.');
        }
    }

    protected function getVendorIds()
    {
        $vendorIds = Vendor::pluck('id')->toArray();
        if (empty($vendorIds)) {
            throw new \Exception('No vendors found in the database.');
        }
        return $vendorIds;
    }

    protected function getInventoryItems()
    {
        $inventoryItems = Inventory::all();
        if ($inventoryItems->isEmpty()) {
            throw new \Exception('No inventory items available. Skipping purchase order creation.');
        }
        $this->command->info('Found ' . $inventoryItems->count() . ' inventory items.');
        return $inventoryItems;
    }

    protected function createPurchaseOrders($vendorIds, $inventoryItems)
    {
        $startDate = Carbon::create(Carbon::now()->year, 1, 1);
        $endDate = Carbon::now();

        for ($i = 0; $i < 15; $i++) {
            $orderDate = Carbon::parse($this->faker->dateTimeBetween($startDate, $endDate));

            $order = $this->createPurchaseOrder($vendorIds, $orderDate);
            $this->createPurchaseOrderItems($order, $inventoryItems, $orderDate);

            $this->command->info("Created purchase order: {$order->order_number} on {$orderDate->toDateString()}");
        }
    }

    protected function createPurchaseOrder($vendorIds, $orderDate)
    {
        return PurchaseOrder::create([
            'order_number' => 'PO-' . $this->faker->unique()->numberBetween(1000, 9999),
            'vendor_id' => $this->faker->randomElement($vendorIds),
            'order_date' => $orderDate,
            'total_amount' => 0,
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'created_at' => $orderDate,
            'updated_at' => $orderDate,
        ]);
    }

    protected function createPurchaseOrderItems($order, $inventoryItems, $orderDate)
    {
        $itemCount = $this->faker->numberBetween(1, 5);
        $totalAmount = 0;

        for ($j = 0; $j < $itemCount; $j++) {
            $inventoryItem = $inventoryItems->random();
            $quantity = $this->faker->numberBetween(10, 100);
            $unitPrice = $inventoryItem->price / 1.2;

            $order->items()->create([
                'product_id' => $inventoryItem->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            $totalAmount += $quantity * $unitPrice;

            $this->updateInventory($inventoryItem, $quantity);
        }

        $order->update(['total_amount' => $totalAmount]);
    }

    protected function updateInventory($inventoryItem, $quantity)
    {
        $inventoryItem->increment('stock', $quantity);
    }
}
