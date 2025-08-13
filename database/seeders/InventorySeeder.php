<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Vendors\Vendor;
use App\Models\InventoryImage;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $initialCount = Inventory::count();
        $this->command->info("Initial inventory count: $initialCount");

        $categories = Category::count() > 0 ? Category::all() : Category::factory(5)->create();
        $this->command->info("Number of categories: " . $categories->count());

        $vendors = Vendor::all();
        $this->command->info("Number of vendors: " . $vendors->count());

        $totalCreated = 0;
        $maxItems = 15; // Set your desired maximum here

        $vendors->each(function ($vendor) use ($categories, &$totalCreated, $maxItems) {
            if ($totalCreated >= $maxItems) {
                return false;
            }

            $itemCount = min(rand(5, 15), $maxItems - $totalCreated);
            $created = Inventory::factory($itemCount)->create([
                'vendor_id' => $vendor->id,
            ])->each(function ($inventory) use ($categories) {
                // Assign a random category
                $inventory->category()->associate($categories->random())->save();

                $imageCount = rand(1, 2);
                for ($i = 0; $i < $imageCount; $i++) {
                    InventoryImage::create([
                        'inventory_id' => $inventory->id,
                        'image_path' => 'https://picsum.photos/seed/' . uniqid() . '/400/300',
                        'is_primary' => $i === 0,
                        'order' => $i,
                    ]);
                }
            })->count();

            $totalCreated += $created;
            $this->command->info("Created $created items for vendor {$vendor->name}");

            if ($totalCreated >= $maxItems) {
                return false; // Stop the loop if we've reached the maximum
            }
        });

        $finalCount = Inventory::count();
        $this->command->info("Final inventory count: $finalCount");
        $this->command->info("Total items created in this run: $totalCreated");
    }
}
