<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    public function continue(Transaction $transaction)
    {
        try {
            // Load the transaction with all necessary relationships based on database schema
            $transaction->load([
                'items.product.productUnits' => function($query) {
                    $query->with(['unit', 'prices']); // Include prices table
                },
                'items.product.tax',
                'items.product.discount',
                'items.unit',
                'customer'
            ]);

            // Convert transaction data to cart format matching store function requirements
            $cartData = [
                'pending_transaction_id' => $transaction->id,
                'invoice_number' => $transaction->invoice_number,
                'customer_id' => $transaction->customer_id,
                'items' => $transaction->items->map(function ($item) {
                    $productUnit = $item->product->productUnits
                        ->where('unit_id', $item->unit_id)
                        ->first();

                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'unit_id' => $item->unit_id,
                        'unit_name' => $item->unit->name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->subtotal,
                        'discount' => $item->discount,
                        'available_units' => $item->product->productUnits->map(function ($pu) {
                            return [
                                'unit_id' => $pu->unit_id,
                                'unit_name' => $pu->unit->name,
                                'conversion_factor' => $pu->conversion_factor,
                                'purchase_price' => $pu->purchase_price,
                                'selling_price' => $pu->selling_price,
                                'stock' => $pu->stock,
                                'is_default' => $pu->is_default,
                                'prices' => $pu->prices->map(function ($price) {
                                    return [
                                        'min_quantity' => $price->min_quantity,
                                        'price' => $price->price
                                    ];
                                })
                            ];
                        })
                    ];
                })->toArray(),
                'payment_type' => $transaction->payment_type,
                'reference_number' => $transaction->reference_number,
                'total_amount' => $transaction->total_amount,
                'tax_amount' => $transaction->tax_amount,
                'discount_amount' => $transaction->discount_amount,
                'final_amount' => $transaction->final_amount
            ];

            // Store cart data in session for use in store function
            session(['cart_data' => $cartData]);

            return redirect()->route('pos.index');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat melanjutkan transaksi: ' . $e->getMessage());
        }
    }
}
