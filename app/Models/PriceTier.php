<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceTier extends Model
{
    protected $fillable = ['product_id', 'unit_id', 'min_quantity', 'price'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
