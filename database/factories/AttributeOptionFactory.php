<?php

namespace Database\Factories;

use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeOptionFactory extends Factory
{
    protected $model = AttributeOption::class;

    public function definition()
    {
        $attributesOptions = [
            'Size' => ['sm', 'md', 'lg', 'xl', 'xxl'],
            'Color' => ['red', 'green', 'blue'],
            'Storage' => ['8GB', '16GB', '32GB', '64GB', '128GB'],
            'Fabric' => ['cotton', 'wool', 'silk', 'polyester'],
            'Style' => ['casual', 'formal', 'sporty'],
            'Body' => ['slim', 'regular', 'athletic']
        ];

        // Create the attribute if it doesn't exist
        $attributes = Attribute::inRandomOrder()->first();
        $values = $attributesOptions[$attributes->name];

        return [
            'attribute_id' => $attributes->id,
            'value' => $this->faker->randomElement($values),
        ];
    }
}
