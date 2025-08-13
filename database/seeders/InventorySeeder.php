<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\Vendors\Vendor;
use App\Models\InventoryImage;
use App\Models\Vendors\VendorProduct;

class InventorySeeder extends Seeder
{
    public function run()
    {
        $initialCount = Inventory::count();
        $this->command->info("Initial inventory count: $initialCount");

        $categories = Category::count() > 0 ? Category::all() : Category::factory(5)->create();
        $this->command->info("Number of categories: " . $categories->count());

        $totalCreated = 0;
        $maxItems = 15;

        $allVendorProducts = VendorProduct::all()->unique('sku');
        $this->command->info("Total unique vendor products: " . $allVendorProducts->count());

        foreach ($allVendorProducts as $vendorProduct) {
            if ($totalCreated >= $maxItems) {
                break;
            }

            $existingInventory = Inventory::where('sku', $vendorProduct->sku)->first();

            if (!$existingInventory) {
                $inventory = Inventory::create([
                    'name' => $vendorProduct->name,
                    'sku' => $vendorProduct->sku,
                    'description' => $vendorProduct->description,
                    'price' => $vendorProduct->price,
                    'stock' => rand(0, min($vendorProduct->stock, 20)),
                    'vendor_id' => $vendorProduct->vendor_id,
                    'category_id' => $categories->random()->id,
                    'low_stock_threshold' => rand(1, 5),
                ]);

                $imageCount = rand(1, 2);
                for ($i = 0; $i < $imageCount; $i++) {
                    InventoryImage::create([
                        'inventory_id' => $inventory->id,
                        'image_path' => 'https://picsum.photos/seed/' . uniqid() . '/400/300',
                        'is_primary' => $i === 0,
                        'order' => $i,
                    ]);
                }

                $totalCreated++;
                $this->command->info("Created inventory item for SKU: {$vendorProduct->sku}");
            } else {
                $this->command->info("Skipped existing SKU: {$vendorProduct->sku}");
            }
        }

        $finalCount = Inventory::count();
        $this->command->info("Final inventory count: $finalCount");
        $this->command->info("Total items created in this run: $totalCreated");
    }
}
