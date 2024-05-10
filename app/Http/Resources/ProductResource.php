<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'base_price' => $this->base_price,
            'base_image' => $this->base_image,
            'is_published' => $this->is_published,
            'base_stock' => $this->base_stock,
            'attributes' => $this->attributes,
            'attribute_options' => $this->attributes_options,
            'variants' => ProductVariantResource::collection($this->variants),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
