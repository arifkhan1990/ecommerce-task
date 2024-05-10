<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
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
            'sku_code' => $this->sku_code,
            'image' => json_decode($this->image),
            'regular_price' => $this->regular_price,
            'sale_price' => $this->sale_price,
            'stock' => $this->stock,
            'is_published' => $this->is_published,
        ];
    }
}
