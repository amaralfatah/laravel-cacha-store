<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'cashier_id',
        'total_amount',
        'tax_amount',
        'discount_amount',
        'final_amount',
        'payment_type',
        'reference_number',
        'status',
        'invoice_date'
    ];

    protected $casts = [
        'invoice_date' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($transaction) {
            if ($transaction->status === 'success') {
                foreach ($transaction->items as $item) {
                    StockHistory::recordHistory(
                        $item->productUnit,
                        'transactions',
                        $transaction->id,
                        'out',
                        $item->quantity,
                        'Transaction sale: ' . $transaction->invoice_number
                    );
                }
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function calculateTotals()
    {
        $this->total_amount = $this->items->sum('subtotal');
        $this->tax_amount = $this->items->sum(function ($item) {
            return $item->subtotal * ($item->product->tax->rate ?? 0) / 100;
        });
        $this->discount_amount = $this->items->sum('discount');
        $this->final_amount = $this->total_amount + $this->tax_amount - $this->discount_amount;
        return $this;
    }
}
