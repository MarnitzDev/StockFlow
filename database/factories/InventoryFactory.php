<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Vendors\Vendor;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition()
    {
        return [
            'name' => $this->getRandomProductName(),
            'sku' => $this->faker->unique()->ean13,
            'description' => $this->faker->paragraph,
            'stock' => $this->faker->numberBetween(0, 15),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'low_stock_threshold' => $this->faker->numberBetween(0, 5),
            'vendor_id' => Vendor::factory(),
            'category_id' => Category::factory(),
        ];
    }

    private function getRandomProductName()
    {
        $adjectives = ['Premium', 'Deluxe', 'Eco-friendly', 'Organic', 'Handcrafted', 'Innovative', 'Classic', 'Modern'];
        $nouns = ['Widget', 'Gadget', 'Tool', 'Appliance', 'Device', 'Accessory', 'Component', 'Solution'];

        return $adjectives[array_rand($adjectives)] . ' ' . $nouns[array_rand($nouns)];
    }
}
