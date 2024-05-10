<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    public function run()
    {
        // Create product variants for each product
        $products = \App\Models\Product::all();
        foreach ($products as $product) {
            ProductVariant::factory()->count(3)->create(['product_id' => $product->id]);
        }
    }
}
