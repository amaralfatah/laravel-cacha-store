<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'barcode',
        'barcode_image',
        'category_id',
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

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }









    public function getCurrentStock()
    {
        return $this->inventories()
            ->where('unit_id', $this->default_unit_id)
            ->value('quantity') ?? 0;
    }







    public function getPrice($quantity, $unitId)
    {
        \Log::info('Getting price for product', [
            'product_id' => $this->id,
            'quantity' => $quantity,
            'unit_id' => $unitId
        ]);

        // Get product unit
        $productUnit = $this->productUnits()
            ->where('unit_id', $unitId)
            ->first();

        if (!$productUnit) {
            \Log::error('Product unit not found', [
                'product_id' => $this->id,
                'unit_id' => $unitId
            ]);
            throw new \Exception("Unit tidak ditemukan untuk produk ini");
        }

        \Log::info('Found product unit', [
            'product_unit_id' => $productUnit->id,
            'selling_price' => $productUnit->selling_price
        ]);

        // Check for tiered pricing
        $price = $productUnit->prices()
            ->where('min_quantity', '<=', $quantity)
            ->orderBy('min_quantity', 'desc')
            ->first();

        if ($price) {
            \Log::info('Found tiered price', [
                'min_quantity' => $price->min_quantity,
                'price' => $price->price
            ]);
            return $price->price;
        }

        \Log::info('Using default selling price', [
            'price' => $productUnit->selling_price
        ]);
        return $productUnit->selling_price;
    }

    public function getDiscountAmount($price)
    {
        \Log::info('Calculating discount', [
            'product_id' => $this->id,
            'base_price' => $price,
            'has_discount' => $this->discount ? true : false
        ]);

        if (!$this->discount) {
            return 0;
        }

        $discountAmount = $this->discount->type === 'percentage'
            ? ($price * $this->discount->value / 100)
            : $this->discount->value;

        \Log::info('Discount calculated', [
            'type' => $this->discount->type,
            'value' => $this->discount->value,
            'amount' => $discountAmount
        ]);

        return $discountAmount;
    }

    public function getTaxAmount($price)
    {
        \Log::info('Calculating tax', [
            'product_id' => $this->id,
            'base_price' => $price,
            'has_tax' => $this->tax ? true : false
        ]);

        if (!$this->tax) {
            return 0;
        }

        $taxAmount = $price * $this->tax->rate / 100;

        \Log::info('Tax calculated', [
            'rate' => $this->tax->rate,
            'amount' => $taxAmount
        ]);

        return $taxAmount;
    }
}
