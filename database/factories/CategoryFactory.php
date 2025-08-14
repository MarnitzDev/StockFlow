<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        $categories = [
            'Shoes' => [
                'description' => 'Footwear for various occasions',
                'subcategories' => [
                    'Business Casual' => 'Stylish and comfortable shoes suitable for office environments',
                    'Crocs' => 'Lightweight, comfortable clogs and sandals',
                    'Hiking' => 'Durable shoes designed for outdoor adventures and rough terrains',
                    'Running Shoes' => 'Lightweight, cushioned footwear optimized for running and jogging',
                    'Sneakers' => 'Casual, comfortable shoes for everyday wear',
                    'Trainers' => 'Versatile athletic shoes suitable for various sports and activities'
                ]
            ],
            'Clothes' => [
                'description' => 'Apparel for all occasions',
                'subcategories' => [
                    'Shirts' => 'Upper body garments with sleeves',
                    'Pants' => 'Lower body garments covering the legs',
                    'Dresses' => 'One-piece garments typically worn by women',
                    'Jackets' => 'Outerwear for protection against cold or rain',
                    'Underwear' => 'Garments worn under other clothes, next to the skin',
                    'Socks' => 'Garments worn on the feet and ankles'
                ]
            ],
            'Electronics' => [
                'description' => 'Devices and gadgets powered by electricity',
                'subcategories' => [
                    'Smartphones' => 'Mobile phones with advanced features',
                    'Laptops' => 'Portable personal computers',
                    'Tablets' => 'Portable touchscreen computers',
                    'Smartwatches' => 'Wearable computers in the form of a watch',
                    'Headphones' => 'Audio equipment for personal listening'
                ]
            ],
            'Home & Garden' => [
                'description' => 'Products for home improvement and gardening',
                'subcategories' => [
                    'Furniture' => 'Movable objects intended to support various human activities',
                    'Kitchenware' => 'Tools, utensils, and appliances for use in a kitchen',
                    'Bedding' => 'Materials used on a bed for sleeping or relaxing',
                    'Garden Tools' => 'Tools used for gardening and landscaping',
                    'Home Decor' => 'Decorative elements used to enhance the interior of a building'
                ]
            ],
            'Sports & Outdoors' => [
                'description' => 'Equipment and gear for sports and outdoor activities',
                'subcategories' => [
                    'Fitness Equipment' => 'Devices used for physical exercise',
                    'Camping Gear' => 'Equipment used for outdoor camping',
                    'Bicycles' => 'Human-powered vehicles with two wheels',
                    'Team Sports Equipment' => 'Gear used in team sports like football, basketball, etc.',
                    'Water Sports Gear' => 'Equipment for water-based activities like swimming, surfing, etc.'
                ]
            ],
            'Beauty & Personal Care' => [
                'description' => 'Products for personal grooming and beauty',
                'subcategories' => [
                    'Skincare' => 'Products for maintaining or improving the appearance of skin',
                    'Haircare' => 'Products for cleaning, styling, and maintaining hair',
                    'Makeup' => 'Cosmetics applied to the face to enhance appearance',
                    'Fragrances' => 'Liquids that give off a pleasant scent',
                    'Personal Hygiene' => 'Products used for personal cleanliness and grooming'
                ]
            ]
        ];

        $parentCategory = $this->faker->randomElement(array_keys($categories));
        $isParent = $this->faker->boolean(30);

        if ($isParent) {
            return [
                'name' => $parentCategory,
                'slug' => Str::slug($parentCategory),
                'description' => $categories[$parentCategory]['description'],
                'parent_id' => null,
            ];
        } else {
            $subcategories = $categories[$parentCategory]['subcategories'];
            $subcategoryName = $this->faker->randomElement(array_keys($subcategories));
            return [
                'name' => $subcategoryName,
                'slug' => Str::slug($subcategoryName),
                'description' => $subcategories[$subcategoryName],
                'parent_id' => Category::where('name', $parentCategory)->first()->id ?? null,
            ];
        }
    }
}
