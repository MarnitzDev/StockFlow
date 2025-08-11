<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\InventoryImage;

class SupplierWithInventorySeeder extends Seeder
{
    public function run()
    {
        // Create some categories if they don't exist
        $categories = Category::count() > 0 ? Category::all() : Category::factory(5)->create();

        // Create exactly 5 suppliers with their own inventories
        Supplier::factory(5)->create()->each(function ($supplier) use ($categories) {
            $inventoryCount = rand(5, 15);
            Inventory::factory($inventoryCount)->create([
                'supplier_id' => $supplier->id,
            ])->each(function ($inventory) use ($categories) {
                // Assign a random category
                $inventory->category()->associate($categories->random())->save();

                // Create 1-5 images for each inventory item
                $imageCount = rand(1, 5);
                for ($i = 0; $i < $imageCount; $i++) {
                    InventoryImage::create([
                        'inventory_id' => $inventory->id,
                        'image_path' => 'https://picsum.photos/seed/' . uniqid() . '/400/300',
                        'is_primary' => $i === 0,
                        'order' => $i,
                    ]);
                }
            });
        });
    }
}
