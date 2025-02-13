<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    protected $fillable = [
        'product_id',
        'unit_id',
        'conversion_factor',
        'purchase_price',
        'selling_price',
        'stock',
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

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }



    public function getDiscountPercentageAttribute()
    {
        $defaultUnitPrice = $this->product->productUnits()
            ->where('is_default', true)
            ->value('selling_price') ?? 0;

        $basePrice = $defaultUnitPrice * $this->conversion_factor;
        $actualPrice = $this->selling_price;
        return round((($basePrice - $actualPrice) / $basePrice) * 100, 2);
    }
}
