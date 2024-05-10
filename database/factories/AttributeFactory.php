<?php

// AttributeFactory.php
namespace Database\Factories;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Size', 'Color', 'Storage', 'Fabric', 'Style', 'Body'])
        ];
    }
}
