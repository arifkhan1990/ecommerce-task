<?php

namespace Database\Factories;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

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
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
            'base_price' => $this->faker->randomFloat(2, 10, 100),
            'base_image' => $this->faker->imageUrl(),
            'attributes' => $attributeIds, // Assign attribute IDs directly
            'attributes_options' => $attributesOptions, // Assign attribute options
            'is_published' => $this->faker->boolean,
            'base_stock' => $this->faker->numberBetween(0, 100),
        ];
    }
}
