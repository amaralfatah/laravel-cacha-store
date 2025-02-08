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
}
