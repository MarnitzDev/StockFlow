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
                'Athletic Shoes', 'Boots', 'Sandals', 'Dress Shoes', 'Slippers', 'Loafers', 'Sneakers', 'Heels', 'Flats', 'Oxfords'
            ],
            'Clothes' => [
                'Shirts', 'Pants', 'Dresses', 'Jackets', 'Underwear', 'Sweaters', 'Skirts', 'Suits', 'Coats', 'Activewear'
            ],
            'Electronics' => [
                'Smartphones', 'Laptops', 'Tablets', 'Smartwatches', 'Headphones', 'TVs', 'Cameras', 'Gaming Consoles', 'Speakers', 'E-readers'
            ],
            'Home & Garden' => [
                'Furniture', 'Kitchenware', 'Bedding', 'Garden Tools', 'Home Decor', 'Lighting', 'Storage', 'Rugs', 'Curtains', 'Appliances'
            ],
            'Sports & Outdoors' => [
                'Fitness Equipment', 'Camping Gear', 'Bicycles', 'Team Sports', 'Water Sports', 'Hiking', 'Fishing', 'Golf', 'Tennis', 'Skiing'
            ],
            'Beauty & Personal Care' => [
                'Skincare', 'Haircare', 'Makeup', 'Fragrances', 'Personal Hygiene', 'Oral Care', 'Bath & Body', 'Nail Care', 'Men\'s Grooming', 'Sun Care'
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
