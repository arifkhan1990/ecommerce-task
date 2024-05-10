<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Retrieve all products
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    // Create a new product
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:products',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric',
            'base_image' => 'nullable|string',
            'is_published' => 'required|boolean',
            'base_stock' => 'required|integer',
        ]);

        $product = Product::create($request->all());
        return response()->json($product, 201);
    }

    // Retrieve a single product
    public function show(Product $product)
    {
        return new ProductResource($product->load('variants'));
    }

    // Update a product
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'base_price' => 'required|numeric',
            'base_image' => 'nullable|string',
            'is_published' => 'required|boolean',
            'base_stock' => 'required|integer',
        ]);

        $product->update($request->all());
        return response()->json($product, 200);
    }

    // Delete a product
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
