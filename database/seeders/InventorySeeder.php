<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryImage;
use App\Models\Vendors\Supplier;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $categories = Category::count() > 0 ? Category::all() : Category::factory(5)->create();

        $suppliers = Supplier::all();

        $suppliers->each(function ($supplier) use ($categories) {
            $itemCount = rand(5, 15);
            Inventory::factory($itemCount)->create([
                'supplier_id' => $supplier->id,
            ])->each(function ($inventory) use ($categories) {
                // Assign a random category
                $inventory->category()->associate($categories->random())->save();

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
