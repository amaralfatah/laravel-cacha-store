<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    protected $fillable = [
        'product_id',
        'unit_id',
        'conversion_factor',
        'price',
        'is_default'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function getDiscountPercentageAttribute()
    {
        $basePrice = $this->product->base_price * $this->conversion_factor;
        $actualPrice = $this->price;
        return round((($basePrice - $actualPrice) / $basePrice) * 100, 2);
    }
}
