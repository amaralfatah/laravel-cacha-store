<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    protected $fillable = [
        'store_id',
        'product_unit_id',
        'reference_type',
        'reference_id',
        'type',
        'quantity',
        'remaining_stock',
        'notes'
    ];

    public function productUnit()
    {
        return $this->belongsTo(ProductUnit::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Helper method to create history from different sources
    public static function recordHistory($productUnit, $referenceType, $referenceId, $type, $quantity, $notes = null)
    {
        // Validasi productUnit tidak null
        if (!$productUnit) {
            throw new \Exception("Product unit not found");
        }

        return self::create([
            'store_id' => $productUnit->store_id,
            'product_unit_id' => $productUnit->id,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'type' => $type,
            'quantity' => $quantity,
            'remaining_stock' => $productUnit->stock,
            'notes' => $notes
        ]);
    }
}
