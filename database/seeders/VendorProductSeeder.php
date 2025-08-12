<?php

namespace Database\Seeders;

use App\Models\Vendors\VendorProduct;
use App\Models\Vendors\VendorImage;
use App\Models\Vendors\Vendor;
use Illuminate\Database\Seeder;

class VendorProductSeeder extends Seeder
{
    public function run()
    {
        // Create 5 vendors if they don't exist
        if (Vendor::count() == 0) {
            Vendor::factory(5)->create()->each(function ($vendor) {
                // Create one image for each vendor
                VendorImage::create([
                    'vendor_id' => $vendor->id,
                    'image_path' => 'https://picsum.photos/seed/' . uniqid() . '/400/300',
                ]);
            });
        }

        $vendors = Vendor::all();

        foreach ($vendors as $vendor) {
            $productsCount = rand(5, 15);  // Each vendor will have 5 to 15 products

            for ($i = 0; $i < $productsCount; $i++) {
                VendorProduct::create([
                    'vendor_id' => $vendor->id,
                    'name' => $this->getRandomProductName(),
                    'sku' => 'SKU-' . strtoupper(substr($vendor->name, 0, 3)) . '-' . rand(1000, 9999),
                    'price' => rand(10, 1000) / 10,  // Random price between 1.0 and 100.0
                    'stock' => rand(0, 100),
                    'description' => 'This is a sample product description for ' . $vendor->name . '.',
                ]);
            }
        }
    }

    private function getRandomProductName()
    {
        $adjectives = ['Premium', 'Deluxe', 'Eco-friendly', 'Organic', 'Handcrafted', 'Innovative', 'Classic', 'Modern'];
        $nouns = ['Widget', 'Gadget', 'Tool', 'Appliance', 'Device', 'Accessory', 'Component', 'Solution'];

        return $adjectives[array_rand($adjectives)] . ' ' . $nouns[array_rand($nouns)];
    }
}
