<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductUnit extends Model
{
    protected $fillable = [
        'store_id',
        'product_id',
        'unit_id',
        'conversion_factor',
        'purchase_price',
        'selling_price',
        'stock',
        'min_stock',
        'is_default' // gunakan is_default untuk menandai unit default
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

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function setAsDefault()
    {
        // Pastikan hanya ada satu unit default per produk
        $this->product->productUnits()
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        $this->update(['is_default' => true]);
    }

    protected static function booted()
    {
        static::saving(function ($productUnit) {
            if ($productUnit->is_default) {
                // Set semua unit lain untuk produk ini menjadi non-default
                static::where('product_id', $productUnit->product_id)
                    ->where('id', '!=', $productUnit->id)
                    ->update(['is_default' => false]);
            }
        });

        static::saved(function ($productUnit) {
            // Pastikan produk memiliki minimal satu unit default
            $hasDefault = static::where('product_id', $productUnit->product_id)
                ->where('is_default', true)
                ->exists();

            if (!$hasDefault) {
                // Set unit pertama sebagai default jika tidak ada default
                static::where('product_id', $productUnit->product_id)
                    ->first()
                    ->update(['is_default' => true]);
            }
        });
    }
}
