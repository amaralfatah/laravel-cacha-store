<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $transactions = Transaction::select([
                'transactions.id',
                'transactions.invoice_number',
                'transactions.invoice_date',
                'transactions.customer_id',
                'transactions.final_amount',
                'transactions.status',
                'transactions.created_at'
            ])->with(['customer:id,name']);

            // Filter Status
            if ($request->filled('status')) {
                $transactions->where('status', $request->status);
            }

            // Filter Periode
            if ($request->filled('period')) {
                switch ($request->period) {
                    case 'today':
                        $transactions->whereDate('invoice_date', today());
                        break;

                    case 'yesterday':
                        $transactions->whereDate('invoice_date', today()->subDay());
                        break;

                    case 'this_week':
                        $transactions->whereBetween('invoice_date', [
                            now()->startOfWeek(),
                            now()->endOfWeek()
                        ]);
                        break;

                    case 'this_month':
                        $transactions->whereYear('invoice_date', now()->year)
                            ->whereMonth('invoice_date', now()->month);
                        break;

                    case 'last_month':
                        $transactions->whereYear('invoice_date', now()->subMonth()->year)
                            ->whereMonth('invoice_date', now()->subMonth()->month);
                        break;
                }
            }

            return DataTables::of($transactions)
                ->addColumn('invoice_date_formatted', function ($transaction) {
                    return $transaction->invoice_date ?
                        Carbon::parse($transaction->invoice_date)->format('d/m/Y H:i') : '-';
                })
                ->addColumn('customer_name', function ($transaction) {
                    return $transaction->customer?->name ?? '-';
                })
                ->addColumn('final_amount_formatted', function ($transaction) {
                    return 'Rp ' . number_format($transaction->final_amount, 0, ',', '.');
                })
                ->addColumn('status_formatted', function ($transaction) {
                    $badges = [
                        'pending' => '<span class="badge bg-warning">Draft</span>',
                        'success' => '<span class="badge bg-success">Selesai</span>',
                        'failed' => '<span class="badge bg-danger">Gagal</span>'
                    ];
                    return $badges[$transaction->status] ?? '-';
                })
                ->addColumn('action', function ($transaction) {
                    if ($transaction->status == 'pending') {
                        return sprintf(
                            '<a href="%s" class="btn btn-primary btn-sm">Lanjutkan</a>',
                            route('transactions.continue', $transaction->id)
                        );
                    }
                    return sprintf(
                        '<a href="%s" class="btn btn-info btn-sm" target="_blank">Detail</a>',
                        route('pos.print-invoice', $transaction->id)
                    );
                })
                ->filterColumn('invoice_date', function($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(invoice_date,'%d/%m/%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->filterColumn('customer_name', function($query, $keyword) {
                    $query->whereHas('customer', function($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    });
                })
                ->rawColumns(['status_formatted', 'action'])
                ->make(true);
        }

        return view('transactions.index');
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
