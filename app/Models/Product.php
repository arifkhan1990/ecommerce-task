<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'description', 'base_price', 'base_image', 'attributes', 'attributes_options', 'is_published', 'base_stock'
    ];

    protected $casts = [
        'attributes' => 'json',
        'attributes_options' => 'json',
        'is_published' => 'boolean'
    ];
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
