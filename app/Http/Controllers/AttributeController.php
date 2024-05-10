<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    // Retrieve all attributes
    public function index()
    {
        $attributes = Attribute::all();
        return response()->json($attributes);
    }

    // Create a new attribute
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:attributes',
        ]);

        $attribute = Attribute::create($request->all());
        return response()->json($attribute, 201);
    }

    // Retrieve a single attribute
    public function show(Attribute $attribute)
    {
        return response()->json($attribute);
    }

    // Update an attribute
    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'name' => 'required|string|unique:attributes,name,' . $attribute->id,
        ]);

        $attribute->update($request->all());
        return response()->json($attribute, 200);
    }

    // Delete an attribute
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return response()->json(null, 204);
    }
}
