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

    protected static function boot()
    {
        parent::boot();

        // After creating transaction item with success status
        static::created(function ($item) {
            if ($item->transaction->status === 'success') {
                $productUnit = ProductUnit::where('product_id', $item->product_id)
                    ->where('unit_id', $item->unit_id)
                    ->first();

                if ($productUnit) {
                    StockHistory::create([
                        'product_unit_id' => $productUnit->id,
                        'reference_type' => 'transaction_items',
                        'reference_id' => $item->id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'remaining_stock' => $productUnit->stock,
                        'notes' => "Transaction sale: {$item->transaction->invoice_number}"
                    ]);
                }
            }
        });
    }

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
