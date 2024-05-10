<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeOption;

class ProductStoreController extends Controller
{
    public function csvToDbProductStore(Request $request)
    {

        // Validate the uploaded file
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        // Read CSV file
        $csvData = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));
        print_r($csvData);
        // Remove header row
        $header = array_shift($csvData);

        foreach ($csvData as $row) {
            $product = Null;
            if ($row['Type'] === 'variant') {
                $product = Product::where('id', $row['Root Id'])->first();
            } else {
                // Check if product with the same name already exists
                $product = Product::where('name', $row['Name'])->first();
            }
            // If type is 'variant', create a product variant
            if ($row['Type'] === 'variant') {
                if (!$product) {
                    return response()->json(['message' => 'Your given data format is wrong.Your provided product variant preant data dit not exisits!.', 'code' => 403], 403);
                }
                // Check if product with the same name already exists
                $productVariant = ProductVariant::where('name', $row['Name'])->first();
                if (!$productVariant) {
                    $productVariant = new ProductVariant();
                }
                $productVariant->product_id = $row['Root Id'];
                $productVariant->name = $row['Name'];
                $productVariant->sku_code = $row['SKU'];
                $productVariant->is_published = $row['Published'];
                $productVariant->stock = $row['Total Stock'];
                $productVariant->sale_price = $row['Sale Price'];
                $productVariant->regular_price = $row['Regular Price'];
                $productVariant->image = $row['Image'];
                // Set other variant properties...
                $productVariant->save();
            } else if ($row['Type'] === 'variable') {
                // If product doesn't exist, create a new one
                $totalStockCount = Product::leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
                    ->selectRaw('SUM(product_variants.stock) AS total_stock_count')
                    ->groupBy('products.id')
                    ->get()
                    ->sum('total_stock_count');

                if (!$product) {
                    $product = new Product();
                }
                $product->name = $row['Name'];
                $product->is_published = $row['Published'] ?? 0;
                $product->base_image = $row['Image'];
                $product->base_stock = $totalStockCount;
                $product->save();
            } else if ($row['Type'] === 'simple') {
                if (!$product) {
                    $product = new Product();
                }
                $product->name = $row['Name'];
                $product->is_published = $row['Published'] ?? 0;
                $product->base_image = $row['Image'];
                $product->base_stock = $row['Stock'];
                $product->base_price = $row['Regular price'];
                $product->save();
            }


            $attributeList = [];
            $attributeOptionList = [];
            // Handle attribute columns
            for ($i = 1; $i <= 3; $i++) {
                $attributeName = $row["Attribute {$i} name"];
                $attributeValues = explode(',', $row["Attribute {$i} value"]);

                // Check if attribute exists
                $attribute = Attribute::where('name', $attributeName)->first();
                if (!in_array($attribute->id, $attributeList)) {
                    $attributeList[] = $attribute->id;
                }
                // If attribute doesn't exist, create a new one
                if (!$attribute) {
                    $attribute = new Attribute();
                    $attribute->name = $attributeName;
                    $attribute->save();
                }

                // Check if attribute option exists
                $options = AttributeOption::where('attribute_id', $attribute->id)
                    ->pluck('value')
                    ->toArray();

                if (empty($attributeOptionList)) {
                    $attributeOptionList[] = [
                        'attribute_id' => $attribute->id,
                        'values' => $attributeValues,
                    ];
                } else {
                    $found = false;
                    foreach ($attributeOptionList as &$list) {
                        if ($list['attribute_id'] === $attribute->id) {
                            // Merge the new values into the existing values
                            $list['values'] = array_merge($list['values'], $attributeValues);
                            $found = true;
                        }
                    }

                    // If the attribute ID is not found, insert a new entry
                    if (!$found) {
                        $attributeOptionList[] = [
                            'attribute_id' => $attribute->id,
                            'values' => $attributeValues,
                        ];
                    }
                }
                // If attribute option doesn't exist, create a new one
                if (!$options) {
                    foreach ($attributeValues as $attributeValue) {
                        if (!in_array($attributeValue, $options)) {
                            $option = new AttributeOption();
                            $option->attribute_id = $attribute->id;
                            $option->value = $attributeValue;
                            $option->save();
                        }
                    }
                }
                $product->attributes = $attributeList;
                $product->attributes_options = $attributeOptionList;
                $product->save();
            }
        }

        return response()->json(['message' => 'CSV file imported and Data store successfully']);
    }
}
