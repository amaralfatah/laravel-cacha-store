<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $table = 'prices';
    protected $fillable = ['product_unit_id', 'min_quantity', 'price'];

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class);
    }
}
