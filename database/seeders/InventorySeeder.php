<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Vendor;
use Database\Factories\ProductFactory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $initialCount = Inventory::count();
        $this->command->info("Initial inventory count: $initialCount");

        // Ensure categories exist
        if (Category::count() == 0) {
            Category::factory(6)->create();
        }

        $vendors = Vendor::all();
        $totalCreated = 0;

        // Create a date range for the last 3 months
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();

        foreach ($vendors as $vendor) {
            $productsCount = rand(50, 100);

            for ($i = 0; $i < $productsCount; $i++) {
                $productData = (new ProductFactory())->definition();

                // Generate a random date within the last 3 months
                $createdAt = Carbon::createFromTimestamp(rand($startDate->timestamp, $endDate->timestamp));

                $inventory = Inventory::create(array_merge($productData, [
                    'vendor_id' => $vendor->id,
                    'stock' => rand(0, 25),
                    'low_stock_threshold' => rand(0, 5),
                    'image_url' => $productData['image_url'],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]));

                $totalCreated++;
                $this->command->info("Created inventory item: {$inventory->name} on {$createdAt->toDateString()}");
            }
        }

        $finalCount = Inventory::count();
        $this->command->info("Final inventory count: $finalCount");
        $this->command->info("Total items created in this run: $totalCreated");
    }
}
