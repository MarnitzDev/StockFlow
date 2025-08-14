<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $parentCategories = [
            'Shoes' => [
                'Athletic Shoes', 'Boots', 'Sandals', 'Dress Shoes', 'Slippers'
            ],
            'Clothes' => [
                'Shirts', 'Pants', 'Dresses', 'Jackets', 'Underwear'
            ],
            'Electronics' => [
                'Smartphones', 'Laptops', 'Tablets', 'Smartwatches', 'Headphones'
            ],
            'Home & Garden' => [
                'Furniture', 'Kitchenware', 'Bedding', 'Garden Tools', 'Home Decor'
            ],
            'Sports & Outdoors' => [
                'Fitness Equipment', 'Camping Gear', 'Bicycles', 'Team Sports', 'Water Sports'
            ],
            'Beauty & Personal Care' => [
                'Skincare', 'Haircare', 'Makeup', 'Fragrances', 'Personal Hygiene'
            ]
        ];

        foreach ($parentCategories as $parentName => $childCategories) {
            $parent = Category::create([
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'description' => "Category for {$parentName}",
                'parent_id' => null
            ]);

            foreach ($childCategories as $childName) {
                Category::create([
                    'name' => $childName,
                    'slug' => Str::slug($parentName . ' ' . $childName),
                    'description' => "Subcategory of {$parentName} for {$childName}",
                    'parent_id' => $parent->id
                ]);
            }
        }
    }
}
