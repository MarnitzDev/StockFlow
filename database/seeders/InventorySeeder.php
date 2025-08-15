<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Vendor;
use Database\Factories\ProductFactory;
use Illuminate\Database\Seeder;

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

        foreach ($vendors as $vendor) {
            $productsCount = rand(5, 10);

            for ($i = 0; $i < $productsCount; $i++) {
                $productData = (new ProductFactory())->definition();

                $inventory = Inventory::create(array_merge($productData, [
                    'vendor_id' => $vendor->id,
                    'stock' => rand(0, 20),
                    'low_stock_threshold' => rand(1, 5),
                    'image_url' => $productData['image_url'], // Use the image_url directly
                ]));

                $totalCreated++;
                $this->command->info("Created inventory item: {$inventory->name}");
            }
        }

        $finalCount = Inventory::count();
        $this->command->info("Final inventory count: $finalCount");
        $this->command->info("Total items created in this run: $totalCreated");
    }
}
