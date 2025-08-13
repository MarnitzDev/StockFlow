<?php

namespace Database\Seeders;

use App\Models\Vendors\VendorProduct;
use App\Models\Vendors\VendorImage;
use App\Models\Vendors\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
            $productsCount = rand(5, 10);

            for ($i = 0; $i < $productsCount; $i++) {
                VendorProduct::create([
                    'vendor_id' => $vendor->id,
                    'name' => $this->getRandomProductName(),
                    'sku' => $this->generateUniqueSku($vendor->name),
                    'price' => rand(10, 1000) / 10,
                    'stock' => rand(0, 100),
                    'description' => 'This is a sample product description for ' . $vendor->name . '.',
                ]);
            }
        }
    }

    private function generateUniqueSku($vendorName)
    {
        do {
            $sku = 'SKU-' . strtoupper(substr($vendorName, 0, 3)) . '-' . rand(1000, 9999) . '-' . Str::random(4);
        } while (VendorProduct::where('sku', $sku)->exists());

        return $sku;
    }

    private function getRandomProductName()
    {
        $adjectives = ['Premium', 'Deluxe', 'Eco-friendly', 'Organic', 'Handcrafted', 'Innovative', 'Classic', 'Modern'];
        $nouns = ['Widget', 'Gadget', 'Tool', 'Appliance', 'Device', 'Accessory', 'Component', 'Solution'];

        return $adjectives[array_rand($adjectives)] . ' ' . $nouns[array_rand($nouns)];
    }
}
