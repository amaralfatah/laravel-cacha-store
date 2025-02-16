<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTake extends Model
{
    protected $table = 'stock_takes';

    protected $fillable = ['store_id', 'date', 'status', 'notes', 'created_by'];

    protected $casts = [
        'date' => 'date'
    ];

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($stockTake) {
            if ($stockTake->status === 'completed') {
                foreach ($stockTake->items as $item) {
                    $adjustment = $item->actual_qty - $item->system_qty;
                    if ($adjustment != 0) {
                        // Cari atau buat ProductUnit jika belum ada
                        $productUnit = ProductUnit::firstOrCreate(
                            [
                                'product_id' => $item->product_id,
                                'unit_id' => $item->unit_id
                            ],
                            [
                                'stock' => 0,
                                'is_default' => false
                            ]
                        );

                        // Update stock di ProductUnit
                        $productUnit->update([
                            'stock' => $item->actual_qty
                        ]);

                        // Catat history
                        StockHistory::recordHistory(
                            $productUnit,
                            'stock_takes',
                            $stockTake->id,
                            'adjustment',
                            abs($adjustment),
                            'Stock take adjustment: ' . $stockTake->id
                        );
                    }
                }
            }
        });
    }

    public function items()
    {
        return $this->hasMany(StockTakeItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
