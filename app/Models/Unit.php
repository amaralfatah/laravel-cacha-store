<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'store_id',
        'name',
        'code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function productUnits()
    {
        return $this->hasMany(ProductUnit::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_units')
            ->withPivot(['conversion_factor', 'purchase_price', 'selling_price', 'stock', 'min_stock', 'is_default']);
    }
}
