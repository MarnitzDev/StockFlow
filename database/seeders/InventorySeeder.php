<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\InventoryImage;
use App\Models\Category;

class InventorySeeder extends Seeder
{
    public function run()
    {
        // Ensure we have categories
        $categories = Category::count() > 0 ? Category::all() : Category::factory(5)->create();

        // Create 50 inventory items
        Inventory::factory(5)->create()->each(function ($inventory) use ($categories) {
            // Assign a random category
            $inventory->category()->associate($categories->random())->save();

            // Create 1-5 images for each inventory item
            $imageCount = rand(1, 5);
            for ($i = 0; $i < $imageCount; $i++) {
                InventoryImage::create([
                    'inventory_id' => $inventory->id,
                    'image_path' => 'https://picsum.photos/seed/' . uniqid() . '/400/300',
                    'is_primary' => $i === 0, // First image is primary
                    'order' => $i,
                ]);
            }
        });
    }
}
