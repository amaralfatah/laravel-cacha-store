<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'barcode',
        'barcode_image',
        'category_id',
        'base_price',
        'tax_id',
        'discount_id',
        'default_unit_id',
        'is_active'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function defaultUnit()
    {
        return $this->belongsTo(Unit::class, 'default_unit_id');
    }

    public function priceTiers()
    {
        return $this->hasMany(PriceTier::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }



    public function getPrice($quantity, $unitId)
    {
        // 1. Cek price tier berdasarkan quantity dan unit
        $priceTier = $this->priceTiers()
            ->where('unit_id', $unitId)
            ->where('min_quantity', '<=', $quantity)
            ->orderBy('min_quantity', 'desc')
            ->first();

        if ($priceTier) {
            return $priceTier->price;
        }

        // 2. Jika tidak ada price tier, cek product unit price
        $productUnit = $this->productUnits()
            ->where('unit_id', $unitId)
            ->first();

        if ($productUnit) {
            return $productUnit->price;
        }

        // 3. Jika tidak ada product unit price, gunakan base price
        return $this->base_price;
    }

    public function getTaxAmount($subtotal)
    {
        return $this->tax ? ($subtotal * $this->tax->rate / 100) : 0;
    }

    public function getDiscountAmount($price)
    {
        if (!$this->discount) return 0;

        return $this->discount->type === 'percentage'
            ? ($price * $this->discount->value / 100)
            : $this->discount->value;
    }

    public function getCurrentStock()
    {
        return $this->inventories()
            ->where('unit_id', $this->default_unit_id)
            ->value('quantity') ?? 0;
    }
}
