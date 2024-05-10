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
    public function show(AttributeOption $option)
    {
        return response()->json($option);
    }

    // Update an attribute option
    public function update(Request $request, AttributeOption $option)
    {
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string',
        ]);

        $option->update($request->all());
        return response()->json($option, 200);
    }

    // Delete an attribute option
    public function destroy(AttributeOption $option)
    {
        $option->delete();
        return response()->json(null, 204);
    }
}
