<?php

namespace App\Traits;

use App\Models\ProductUnit;
use App\Models\StockHistory;
use Illuminate\Support\Facades\DB;

trait StockMovementHandler
{
    protected function handleStockMovement(
        ProductUnit $productUnit,
        float $quantity,
        string $type,
        string $referenceType,
        int $referenceId,
        ?string $notes = null
    ) {
        DB::transaction(function () use ($productUnit, $quantity, $type, $referenceType, $referenceId, $notes) {
            // Lock the product unit row for update to prevent race conditions
            $productUnit = ProductUnit::where('id', $productUnit->id)
                ->lockForUpdate()
                ->first();

            // Calculate new stock based on movement type
            $newStock = match($type) {
                'in' => $productUnit->stock + $quantity,
                'out' => $productUnit->stock - $quantity,
                'adjustment' => $quantity,
                default => throw new \InvalidArgumentException("Invalid stock movement type: {$type}")
            };

            // Removed stock validation check to allow negative stock values
            // Original code had:
            // if ($type === 'out' && $newStock < 0) {
            //     throw new \Exception("Insufficient stock for product unit {$productUnit->id}");
            // }

            // Update the stock
            $productUnit->stock = $newStock;
            $productUnit->save();

            // Record the stock history
            StockHistory::create([
                'store_id' => $productUnit->store_id,
                'product_unit_id' => $productUnit->id,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'type' => $type,
                'quantity' => $quantity,
                'remaining_stock' => $newStock,
                'notes' => $notes,
                'created_at' => now()
            ]);
        });
    }

    // Method preserved but modified to always return true without validation
    protected function validateStockAvailability(array $items)
    {
        // Previous implementation checked stock and threw exceptions
        // Now we simply return true to allow transactions regardless of stock levels
        return true;
    }
}
