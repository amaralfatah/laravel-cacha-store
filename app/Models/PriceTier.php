<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceTier extends Model
{
    protected $fillable = ['product_unit_id', 'min_quantity', 'price'];

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class);
    }
}
