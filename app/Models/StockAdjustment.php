<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    protected $fillable = [
        'store_id',
        'product_unit_id',
        'type',
        'quantity',
        'notes',
        'created_by'
    ];

    protected static function boot()
    {
        parent::boot();

        // After creating stock adjustment, create history
        static::created(function ($adjustment) {
            $productUnit = $adjustment->productUnit;

            // Create stock history
            StockHistory::create([
                'product_unit_id' => $adjustment->product_unit_id,
                'reference_type' => 'stock_adjustments',
                'reference_id' => $adjustment->id,
                'type' => $adjustment->type,
                'quantity' => $adjustment->quantity,
                'remaining_stock' => $productUnit->stock,
                'notes' => $adjustment->notes
            ]);
        });
    }

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function history()
    {
        return $this->morphOne(StockHistory::class, 'reference');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
