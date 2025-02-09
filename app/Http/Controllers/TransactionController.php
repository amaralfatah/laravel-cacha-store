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
            // Load the transaction items with their related data
            $transaction->load([
                'items.product.productUnits.unit',
                'items.product.tax',
                'items.product.discount',
                'items.unit',
                'customer'
            ]);

            // Convert transaction data to cart format
            $cartData = [
                'pending_transaction_id' => $transaction->id, // Add this to track the pending transaction
                'invoice_number' => $transaction->invoice_number,
                'customer_id' => $transaction->customer_id,
                'items' => $transaction->items->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'unit_id' => $item->unit_id,
                        'unit_name' => $item->unit->name,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'tax_rate' => $item->product->tax ? $item->product->tax->rate : 0,
                        'discount' => $item->discount,
                        'subtotal' => $item->subtotal,
                        'available_units' => $item->product->productUnits->map(function ($pu) {
                            return [
                                'unit_id' => $pu->unit_id,
                                'unit_name' => $pu->unit->name,
                                'conversion_factor' => $pu->conversion_factor,
                                'price' => $pu->price
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

            // Redirect to POS page with cart data
            return redirect()->route('pos.index')
                ->with('cart_data', $cartData);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat melanjutkan transaksi: ' . $e->getMessage());
        }
    }
}
