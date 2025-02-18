<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Models\BalanceMutation;
use App\Models\StockHistory;
use App\Models\StoreBalance;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $transactions = Transaction::select([
                'transactions.id',
                'transactions.store_id',
                'transactions.invoice_number',
                'transactions.invoice_date',
                'transactions.customer_id',
                'transactions.final_amount',
                'transactions.status',
                'transactions.created_at'
            ])->with(['customer:id,name', 'store:id,name']);

            // Filter berdasarkan store untuk non-admin
            if (auth()->user()->role !== 'admin') {
                $transactions->where('transactions.store_id', auth()->user()->store_id);
            }

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
                ->addColumn('store_name', function ($transaction) {
                    return $transaction->store?->name ?? '-';
                })
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
                    if (auth()->user()->role !== 'admin' &&
                        $transaction->store_id !== auth()->user()->store_id) {
                        return '';
                    }

                    $actions = [];

                    if ($transaction->status == TransactionStatus::PENDING->value) {
                        $actions[] = sprintf(
                            '<a href="%s" class="btn btn-primary btn-sm">Lanjutkan</a>',
                            route('transactions.continue', $transaction->id)
                        );

//                        $actions[] = sprintf(
//                            '<button type="button" class="btn btn-danger btn-sm"
//                                             onclick="cancelTransaction(%d)">
//                                        Batalkan
//                                    </button>',
//                            $transaction->id
//                        );
                    } elseif ($transaction->status == TransactionStatus::SUCCESS->value) {
                        $actions[] = sprintf(
                            '<a href="%s" class="btn btn-info btn-sm" target="_blank">Detail</a>',
                            route('pos.print-invoice', $transaction->id)
                        );

                        $actions[] = sprintf(
                            '<button type="button" class="btn btn-danger btn-sm"
                     onclick="returnTransaction(%d)">
                Kembalikan
            </button>',
                            $transaction->id
                        );
                    }

                    return implode(' ', $actions);
                })
                ->filterColumn('invoice_date', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(invoice_date,'%d/%m/%Y %H:%i') like ?", ["%$keyword%"]);
                })
                ->filterColumn('customer_name', function ($query, $keyword) {
                    $query->whereHas('customer', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%$keyword%");
                    });
                })
                ->rawColumns(['status_formatted', 'action'])
                ->make(true);
        }

        $stores = [];
        if (auth()->user()->role === 'admin') {
            $stores = \App\Models\Store::select('id', 'name')->get();
        }

        return view('transactions.index', compact('stores'));
    }

    public function continue(Transaction $transaction)
    {
        if (auth()->user()->role !== 'admin' &&
            $transaction->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        try {
            // Load the transaction with all necessary relationships based on database schema
            $transaction->load([
                'items.product.productUnits' => function ($query) {
                    $query->with(['unit', 'prices']); // Include prices table
                },
                'items.product.tax',
                'items.product.discount',
                'items.unit',
                'customer',
                'store'
            ]);

            // Convert transaction data to cart format matching store function requirements
            $cartData = [
                'pending_transaction_id' => $transaction->id,
                'store_id' => $transaction->store_id,
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

    public function return(Transaction $transaction)
    {
        if (auth()->user()->role !== 'admin' &&
            $transaction->store_id !== auth()->user()->store_id) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            // Cek status transaksi
            if ($transaction->status !== TransactionStatus::SUCCESS->value) {
                throw new \Exception('Hanya transaksi selesai yang dapat dikembalikan');
            }

            // Update status transaksi
            $transaction->update([
                'status' => TransactionStatus::RETURNED->value,
                'returned_at' => now(),
                'returned_by' => auth()->id(),
                'return_reason' => request('reason'),
                'return_notes' => request('notes')
            ]);

            // Kembalikan stok
            foreach ($transaction->items as $item) {
                $productUnit = $item->product->productUnits()
                    ->where('unit_id', $item->unit_id)
                    ->first();

                if ($productUnit) {
                    // Kembalikan stok
                    $productUnit->increment('stock', $item->quantity);

                    // Catat history stok
                    StockHistory::create([
                        'store_id' => $transaction->store_id,
                        'product_unit_id' => $productUnit->id,
                        'reference_type' => Transaction::class,
                        'reference_id' => $transaction->id,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'remaining_stock' => $productUnit->stock,
                        'notes' => 'Pengembalian transaksi #' . $transaction->invoice_number
                    ]);
                }
            }

            // Jika transaksi menggunakan cash, kurangi saldo toko
            if ($transaction->payment_type === 'cash') {
                $storeBalance = StoreBalance::where('store_id', $transaction->store_id)
                    ->first();

                if ($storeBalance) {
                    $storeBalance->decrement('cash_amount', $transaction->final_amount);

                    // Catat mutasi saldo
                    BalanceMutation::create([
                        'store_id' => $transaction->store_id,
                        'type' => 'out',
                        'payment_method' => 'cash',
                        'amount' => $transaction->final_amount,
                        'previous_balance' => $storeBalance->cash_amount + $transaction->final_amount,
                        'current_balance' => $storeBalance->cash_amount,
                        'source_type' => Transaction::class,
                        'source_id' => $transaction->id,
                        'notes' => 'Pengembalian transaksi #' . $transaction->invoice_number,
                        'created_by' => auth()->id()
                    ]);
                }
            }

            Log::info('Transaction returned', [
                'transaction' => $transaction->id,
                'user' => auth()->id(),
                'status' => TransactionStatus::RETURNED->value,
                'reason' => request('reason'),
                'notes' => request('notes'),
                'previous_status' => $transaction->getOriginal('status')
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil dikembalikan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal mengembalikan transaksi: ' . $e->getMessage()
            ], 422);
        }
    }

//    public function cancel(Transaction $transaction)
//    {
//        if (auth()->user()->role !== 'admin' &&
//            $transaction->store_id !== auth()->user()->store_id) {
//            abort(403);
//        }
//
//        DB::beginTransaction();
//        try {
//            // Cek apakah transaksi masih dalam status pending
//            if ($transaction->status !== TransactionStatus::PENDING->value) {
//                throw new \Exception('Hanya transaksi draft yang dapat dibatalkan');
//            }
//
//            // Update status transaksi
//            $transaction->update([
//                'status' => TransactionStatus::CANCELLED->value,
//                'cancelled_at' => now(),
//                'cancelled_by' => auth()->id(),
//                'cancellation_reason' => request('reason')
//            ]);
//
//            // Catat ke history
////            activity()
////                ->performedOn($transaction)
////                ->causedBy(auth()->user())
////                ->withProperties([
////                    'status' => TransactionStatus::CANCELLED->value,
////                    'reason' => request('reason')
////                ])
////                ->log('cancelled_transaction');
//
//            Log::info('Transaction returned', [
//                'transaction' => $transaction->id,
//                'user' => auth()->id(),
//                'status' => TransactionStatus::CANCELLED->value,
//                'reason' => request('reason'),
//                'notes' => request('notes'),
//                'previous_status' => $transaction->getOriginal('status')
//            ]);
//
//            DB::commit();
//
//            return response()->json([
//                'message' => 'Transaksi berhasil dibatalkan'
//            ]);
//        } catch (\Exception $e) {
//            DB::rollBack();
//            return response()->json([
//                'message' => 'Gagal membatalkan transaksi: ' . $e->getMessage()
//            ], 422);
//        }
//    }
}
