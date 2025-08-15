<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\VendorProduct;
use Database\Factories\ProductFactory;
use Illuminate\Database\Seeder;

class VendorProductSeeder extends Seeder
{
    public function run()
    {
        // Create 5 vendors if they don't exist
        if (Vendor::count() == 0) {
            Vendor::factory(5)->create();
        }

        $vendors = Vendor::all();

        foreach ($vendors as $vendor) {
            $productsCount = rand(5, 10);

            for ($i = 0; $i < $productsCount; $i++) {
                $productData = (new ProductFactory())->definition();

                VendorProduct::create(array_merge($productData, [
                    'vendor_id' => $vendor->id,
                ]));
            }
        }
    }
}
