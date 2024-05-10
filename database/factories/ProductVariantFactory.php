<?php

namespace Database\Factories;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    public function definition()
    {
        // Retrieve random attribute IDs
        $attributeIds = Attribute::inRandomOrder()->pluck('id')->toArray();

        // Initialize an array to store attribute options
        $attributesOptions = [];

        // Iterate through each attribute
        foreach ($attributeIds as $attribute) {
            // Retrieve random attribute option for the current attribute
            $options = AttributeOption::where('attribute_id', $attribute)->inRandomOrder()->pluck('value')->unique()->toArray();

            // Add the attribute ID and options to the attributesOptions array
            $attributesOptions[] = [
                'attribute_id' => $attribute,
                'values' => $options,
            ];
        }
        return [
            'product_id' => \App\Models\Product::factory(),
            'name' => $this->faker->unique()->sentence(2),
            'sku_code' => $this->faker->unique()->uuid,
            'image' => json_encode([$this->faker->imageUrl()]), // Encode the array of image URLs into JSON format
            'regular_price' => $this->faker->randomFloat(2, 10, 100),
            'sale_price' => $this->faker->randomFloat(2, 5, 150),
            'stock' => $this->faker->numberBetween(0, 100),
            'is_published' => $this->faker->boolean,
            'variants' => $attributesOptions, // Assign attribute options
        ];
    }
}
