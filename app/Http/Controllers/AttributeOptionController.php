<?php

namespace App\Http\Controllers;

use App\Models\AttributeOption;
use Illuminate\Http\Request;

class AttributeOptionController extends Controller
{
    // Retrieve all attribute options
    public function index()
    {
        $options = AttributeOption::all();
        return response()->json($options);
    }

    // Create a new attribute option
    public function store(Request $request)
    {
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string',
        ]);

        $option = AttributeOption::create($request->all());
        return response()->json($option, 201);
    }

    // Retrieve a single attribute option
    public function show(AttributeOption $attribute_option)
    {
        return response()->json($attribute_option);
    }

    // Update an attribute option
    public function update(Request $request, AttributeOption $attribute_option)
    {
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string',
        ]);

        $attribute_option->update($request->all());
        return response()->json($attribute_option, 200);
    }

    // Delete an attribute option
    public function destroy(AttributeOption $attribute_option)
    {
        $attribute_option->delete();
        return response()->json(null, 204);
    }
}
