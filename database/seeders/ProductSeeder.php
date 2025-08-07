<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $categories = ['Electronics', 'Clothing', 'Books', 'Home & Garden', 'Toys'];

        foreach (range(1, 50) as $index) {
            Product::create([
                'name' => "Product $index",
                'sku' => "SKU" . str_pad($index, 5, '0', STR_PAD_LEFT),
                'stock' => rand(0, 100),
                'price' => rand(10, 1000) / 10,
                'category' => $categories[array_rand($categories)],
                'low_stock_threshold' => rand(5, 20),
            ]);
        }
    }
}
