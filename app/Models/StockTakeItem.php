<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTakeItem extends Model
{
    protected $fillable = [
        'stock_take_id',
        'product_id',
        'unit_id',
        'system_qty',
        'actual_qty'
    ];

    public function stockTake()
    {
        return $this->belongsTo(StockTake::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
