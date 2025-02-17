<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'unit_id',
        'quantity',
        'unit_price',
        'subtotal',
        'discount'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class, ['product_id', 'unit_id'], ['product_id', 'unit_id']);
    }

    public function history()
    {
        return $this->morphOne(StockHistory::class, 'reference');
    }
}
