<?php

namespace Database\Seeders;

use App\Models\AttributeOption;
use Illuminate\Database\Seeder;

class AttributeOptionSeeder extends Seeder
{
    public function run()
    {
        // Create attribute options for each attribute
        $attributes = \App\Models\Attribute::all();
        foreach ($attributes as $attribute) {
            AttributeOption::factory()->count(5)->create();
        }
    }
}
