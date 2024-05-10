<?php

namespace Database\Factories;

use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition()
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'name' => $this->faker->unique()->sentence(2),
            'sku_code' => $this->faker->unique()->uuid,
            'image' => json_encode([$this->faker->imageUrl()]), // Encode the array of image URLs into JSON format
            'regular_price' => $this->faker->randomFloat(2, 10, 100),
            'sale_price' => $this->faker->randomFloat(2, 5, 150),
            'stock' => $this->faker->numberBetween(0, 100),
            'is_published' => $this->faker->boolean,
        ];
    }
}
