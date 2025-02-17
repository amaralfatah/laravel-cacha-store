<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number',
        'store_id',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
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

    public function markAsSuccess()
    {
        DB::transaction(function () {
            $this->update(['status' => 'success']);
        });
    }

    public function history()
    {
        return $this->morphMany(StockHistory::class, 'reference');
    }
}
