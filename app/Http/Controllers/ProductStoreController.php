<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Models\AttributeOption;
use SplFileObject;

class ProductStoreController extends Controller
{
    public function csvToDbProductStore(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        // Open the CSV file
        $file = new SplFileObject($request->file('csv_file')->getRealPath(), 'r');

        // Read the first row as the header
        $header = $file->fgetcsv();

        $csvData = [];

        // Iterate over each row in the CSV
        while (!$file->eof()) {
            $row = $file->fgetcsv();
            if ($row !== false) {
                if (count($row) === count($header)) {
                    $csvData[] = array_combine($header, $row);
                } else {
                    continue;
                }
            }
        }

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
                $productVariant->stock = $productVariant->stock ? $productVariant->stock + $row['Total Stock'] : $row['Total Stock'];
                $productVariant->sale_price = $row['Sale Price'];
                $productVariant->regular_price = $row['Regular Price'];
                $productVariant->image = $row['Image'];
                // Set other variant properties...
                $productVariant->save();
                $totalStockCounts = ProductVariant::where('product_id', $row['Root Id'])->sum('stock');
                $product->base_stock = $totalStockCounts;
                $product->save();
            } else if ($row['Type'] === 'variable') {
                // If product doesn't exist, create a new one


                if (!$product) {
                    $product = new Product();
                }
                $product->name = $row['Name'];
                $product->is_published = $row['Published'] ?? 0;
                $product->base_image = $row['Image'];
                $product->base_stock = 0;
                $product->save();
            } else if ($row['Type'] === 'simple') {
                if (!$product) {
                    $product = new Product();
                }
                $product->name = $row['Name'];
                $product->is_published = $row['Published'] ?? 0;
                $product->base_image = $row['Image'];
                $product->base_stock = $product->base_stock ? $product->base_stock + $row['Total Stock'] : $row['Total Stock'];
                $product->base_price = $row['Regular Price'];
                $product->save();
            }


            $attributeList = [];
            $attributeOptionList = [];
            // Handle attribute columns
            for ($i = 1; $i <= 3; $i++) {
                $attributeName = $row["Attribute {$i} name"];
                $attributeValues = explode(',', $row["Attribute {$i} value"]);
                if ($attributeName and $attributeValues) {
                    // Check if attribute exists
                    $attribute = Attribute::where('name', $attributeName)->first();

                    // If attribute doesn't exist, create a new one
                    if (!$attribute) {
                        $attribute = new Attribute();
                        $attribute->name = $attributeName;
                        $attribute->save();
                    }
                    if (!in_array($attribute->id, $attributeList)) {
                        $attributeList[] = $attribute->id;
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
                }
            }

            // $product = Product::where('id',  $row['Root Id'])->first();
            if ($product) {
                // Product found, update attributes and attribute options
                if ($product->attributes) {
                    // Remove duplicates from attribute list
                    $attributeList = array_values(array_unique(array_merge($product->attributes, $attributeList)));
                }
                $product->attributes = $attributeList;

                if ($product->attributes_options) {
                    $existingOptions = collect($product->attributes_options);

                    // Merge new attribute options without duplicates
                    foreach ($attributeOptionList as $attributeOption) {
                        $existingOptionIndex = $existingOptions->search(function ($option) use ($attributeOption) {
                            return $option['attribute_id'] === $attributeOption['attribute_id'];
                        });

                        if ($existingOptionIndex !== false) {
                            $existingValues = $existingOptions[$existingOptionIndex]['values'];
                            $newValues = array_values(array_unique(array_merge($existingValues, $attributeOption['values'])));
                            $existingOptions->put($existingOptionIndex, [
                                'attribute_id' => $attributeOption['attribute_id'],
                                'values' => $newValues
                            ]);
                        } else {
                            $existingOptions->push($attributeOption); // Add new attribute option
                        }
                    }

                    $product->attributes_options = $existingOptions->toArray(); // Convert back to array
                } else {
                    // No existing attribute options, set them directly
                    $product->attributes_options = $attributeOptionList;
                }

                // print_r($product);
                $product->save();
            } else {
                return response()->json(['message' => "Product not found for ID: {$row['Root Id']}", 'code' => 404]);
            }

            if ($row['Type'] === 'variant' and $productVariant) {
                if ($productVariant->variants) {
                    $existingOptions = collect($productVariant->variants);

                    // Merge new attribute options without duplicates
                    foreach ($attributeOptionList as $attributeOption) {
                        $existingOptionIndex = $existingOptions->search(function ($option) use ($attributeOption) {
                            return $option['attribute_id'] === $attributeOption['attribute_id'];
                        });

                        if ($existingOptionIndex !== false) {
                            $existingValues = $existingOptions[$existingOptionIndex]['values'];
                            $newValues = array_values(array_unique(array_merge($existingValues, $attributeOption['values'])));
                            $existingOptions->put($existingOptionIndex, [
                                'attribute_id' => $attributeOption['attribute_id'],
                                'values' => $newValues
                            ]);
                        } else {
                            $existingOptions->push($attributeOption); // Add new attribute option
                        }
                    }

                    $productVariant->variants = $existingOptions->toArray(); // Convert back to array
                } else {
                    // No existing attribute options, set them directly
                    $productVariant->variants = $attributeOptionList;
                }

                // print_r($product);
                $productVariant->save();
            }
        }

        return response()->json(['message' => 'CSV file imported and Data store successfully']);
    }
}
