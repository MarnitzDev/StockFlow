<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Shoes' => [
                'Athletic Shoes' => ['Running', 'Basketball'],
                'Boots' => [],
                'Sandals' => [],
                'Dress Shoes' => ['Oxford', 'Loafers']
            ],
            'Clothes' => [
                'Shirts' => ['T-Shirts', 'Dress Shirts'],
                'Pants' => [],
                'Dresses' => [],
                'Jackets' => [],
                'Underwear' => []
            ],
            'Electronics' => [
                'Smartphones' => [],
                'Laptops' => ['Gaming', 'Business'],
                'Tablets' => [],
                'Smartwatches' => []
            ],
            'Home & Garden' => [
                'Furniture' => ['Living Room', 'Bedroom'],
                'Kitchenware' => [],
                'Bedding' => [],
                'Garden Tools' => []
            ],
            'Sports & Outdoors' => [
                'Fitness Equipment' => ['Cardio', 'Strength'],
                'Camping Gear' => [],
                'Bicycles' => [],
                'Team Sports' => []
            ],
            'Beauty & Personal Care' => [
                'Skincare' => [],
                'Haircare' => ['Shampoo', 'Conditioner'],
                'Makeup' => [],
                'Fragrances' => []
            ]
        ];

        foreach ($categories as $parentName => $subcategories) {
            $parent = Category::create([
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'description' => "Category for {$parentName}",
                'parent_id' => null
            ]);

            foreach ($subcategories as $subcategoryName => $subsubcategories) {
                $subcategory = Category::create([
                    'name' => $subcategoryName,
                    'slug' => Str::slug($parentName . ' ' . $subcategoryName),
                    'description' => "Subcategory of {$parentName} for {$subcategoryName}",
                    'parent_id' => $parent->id
                ]);

                foreach ($subsubcategories as $subsubcategoryName) {
                    Category::create([
                        'name' => $subsubcategoryName,
                        'slug' => Str::slug($parentName . ' ' . $subcategoryName . ' ' . $subsubcategoryName),
                        'description' => "Sub-subcategory of {$subcategoryName} for {$subsubcategoryName}",
                        'parent_id' => $subcategory->id
                    ]);
                }
            }
        }
    }
}
