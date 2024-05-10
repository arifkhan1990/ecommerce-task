<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductVariantResource;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    // Retrieve all product variants
    public function index()
    {
        $variants = ProductVariant::all();
        return response()->json($variants);
    }

    // Create a new product variant
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'attribute_option_id' => 'required|exists:attribute_options,id',
            // Add validation rules for other fields as needed
        ]);

        $variant = ProductVariant::create($request->all());
        return response()->json($variant, 201);
    }

    // Retrieve a single product variant
    public function show(ProductVariant $product_variant)
    {
        return response()->json($product_variant);
    }

    // Update a product variant
    public function update(Request $request, ProductVariant $product_variant)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'attribute_option_id' => 'required|exists:attribute_options,id',
            // Add validation rules for other fields as needed
        ]);

        $product_variant->update($request->all());
        return response()->json($product_variant, 200);
    }

    // Delete a product variant
    public function destroy(ProductVariant $product_variant)
    {
        $product_variant->delete();
        return response()->json(null, 204);
    }
}
