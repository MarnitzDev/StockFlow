<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition()
    {
        $category = Category::inRandomOrder()->whereNotNull('parent_id')->first();
        $parentCategory = $category->parent;

        return [
            'name' => $this->getRandomProductName($parentCategory->name, $category->name),
            'sku' => $this->generateUniqueSku(),
            'description' => $this->getRandomDescription($parentCategory->name, $category->name),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'stock' => $this->faker->numberBetween(0, 100),
            'category_id' => $category->id,
            'image_url' => $this->getRandomImageUrl(),
        ];
    }

    private function generateUniqueSku()
    {
        return 'SKU-' . strtoupper(Str::random(3)) . '-' . $this->faker->unique()->numberBetween(1000, 9999) . '-' . Str::random(4);
    }

    private function getRandomProductName($parentCategory, $childCategory)
    {
        $brandName = $this->faker->company();

        $adjectives = ['Premium', 'Deluxe', 'Advanced', 'Classic', 'Modern', 'Innovative', 'Essential', 'Professional', 'Luxury', 'Compact'];
        $adjective = $this->faker->randomElement($adjectives);

        return "{$brandName} {$adjective} {$childCategory}";
    }

    private function getRandomDescription($parentCategory, $childCategory)
    {
        $features = [
            'Shoes' => ['comfortable fit', 'durable sole', 'breathable material', 'stylish design', 'arch support'],
            'Clothes' => ['high-quality fabric', 'trendy style', 'perfect fit', 'easy care', 'versatile design'],
            'Electronics' => ['high-resolution display', 'long battery life', 'fast processor', 'ample storage', 'advanced features'],
            'Home & Garden' => ['durable construction', 'easy to clean', 'space-saving design', 'multi-functional', 'eco-friendly materials'],
            'Sports & Outdoors' => ['lightweight', 'weather-resistant', 'high performance', 'ergonomic design', 'adjustable settings'],
            'Beauty & Personal Care' => ['gentle formula', 'long-lasting effect', 'natural ingredients', 'suitable for all skin types', 'dermatologist tested']
        ];

        $categoryFeatures = $features[$parentCategory] ?? $features['Shoes'];
        $selectedFeatures = $this->faker->randomElements($categoryFeatures, 3);

        return "This {$childCategory} offers " . implode(', ', $selectedFeatures) . ". Perfect for those who demand quality and style.";
    }

    private function getRandomImageUrl()
    {
        return "https://picsum.photos/400/300?random=" . Str::random(10);
    }
}
