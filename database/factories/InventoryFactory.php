<?php

namespace Database\Factories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    protected $model = Inventory::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'sku' => $this->faker->unique()->ean13,
            'description' => $this->faker->paragraph,
            'stock' => $this->faker->numberBetween(0, 1000),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'low_stock_threshold' => $this->faker->numberBetween(5, 50),
        ];
    }
}
