<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'sku_code', 'product_id', 'image', 'regular_price', 'sale_price', 'stock', 'variants', 'is_published'];

    protected $casts = [
        'image' => 'json',
        'variants' => 'json',
        'is_published' => 'boolean'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
