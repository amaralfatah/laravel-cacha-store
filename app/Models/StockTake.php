<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTake extends Model
{
    protected $fillable = ['date', 'status', 'notes', 'created_by'];

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($stockTake) {
            if ($stockTake->status === 'completed') {
                foreach ($stockTake->items as $item) {
                    $adjustment = $item->actual_qty - $item->system_qty;
                    if ($adjustment != 0) {
                        StockHistory::recordHistory(
                            $item->productUnit,
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
}
