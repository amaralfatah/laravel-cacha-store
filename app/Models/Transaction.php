<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

        // When status updated to success
        static::updated(function ($transaction) {
            if ($transaction->status === 'success' && $transaction->getOriginal('status') !== 'success') {
                foreach ($transaction->items as $item) {
                    $productUnit = ProductUnit::where('product_id', $item->product_id)
                        ->where('unit_id', $item->unit_id)
                        ->first();

                    if ($productUnit) {
                        if ($productUnit->stock < $item->quantity) {
                            throw new \Exception('Stok tidak mencukupi untuk produk ' . $item->product->name);
                        }

                        $productUnit->decrement('stock', $item->quantity);

                        StockHistory::create([
                            'product_unit_id' => $productUnit->id,
                            'reference_type' => 'transaction_items',
                            'reference_id' => $item->id,
                            'type' => 'out',
                            'quantity' => $item->quantity,
                            'remaining_stock' => $productUnit->stock,
                            'notes' => "Transaction sale: {$transaction->invoice_number}"
                        ]);
                    }
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
