<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\BalanceMutation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected function getStoreId()
    {
        $user = Auth::user();
        return $user->role === 'admin' ? null : $user->store_id;
    }

    public function index()
    {
        return view('reports.index');
    }

    public function storeSales(Request $request)
    {
        $storeId = $this->getStoreId();

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        if ($request->ajax()) {
            $query = Transaction::with(['customer', 'user', 'items.product', 'items.unit'])
                ->when($storeId, function($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                })
                ->whereBetween('invoice_date', [$startDate, $endDate])
                ->where('status', 'success');

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('total_items', function($sale) {
                    return $sale->items->sum('quantity');
                })
                ->editColumn('invoice_date', function($sale) {
                    return $sale->invoice_date->format('d/m/Y H:i');
                })
                ->editColumn('customer.name', function($sale) {
                    return $sale->customer->name;
                })
                ->editColumn('user.name', function($sale) {
                    return $sale->user->name;
                })
                ->editColumn('total_amount', function($sale) {
                    return 'Rp ' . number_format($sale->total_amount, 0, ',', '.');
                })
                ->editColumn('discount_amount', function($sale) {
                    return 'Rp ' . number_format($sale->discount_amount, 0, ',', '.');
                })
                ->editColumn('tax_amount', function($sale) {
                    return 'Rp ' . number_format($sale->tax_amount, 0, ',', '.');
                })
                ->editColumn('final_amount', function($sale) {
                    return 'Rp ' . number_format($sale->final_amount, 0, ',', '.');
                })
                ->editColumn('payment_type', function($sale) {
                    return $sale->payment_type === 'cash' ? 'Tunai' : 'Transfer';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        // Get summary data
        // Get summary data
        $salesQuery = Transaction::when($storeId, function($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'success');

        $summary = [
            'total_sales' => $salesQuery->sum('final_amount'),
            'total_transactions' => $salesQuery->count(),
            'average_transaction' => $salesQuery->count() > 0 ? $salesQuery->sum('final_amount') / $salesQuery->count() : 0,
            'total_items' => Transaction::whereBetween('invoice_date', [$startDate, $endDate])
                ->where('status', 'success')
                ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
                ->sum('transaction_items.quantity'),
        ];

        return view('reports.sales', compact('summary', 'startDate', 'endDate'));
    }

    public function storeInventory(Request $request)
    {
        $storeId = $this->getStoreId();

        if ($request->ajax()) {
            $query = Product::with(['category', 'productUnits.unit'])
                ->when($storeId, function($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                })
                ->where('is_active', true);


            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('total_stock', function ($product) {
                    return $product->productUnits->sum('stock');
                })
                ->addColumn('low_stock', function ($product) {
                    return $product->productUnits->filter(function ($unit) {
                            return $unit->stock <= $unit->min_stock;
                        })->count() > 0;
                })
                ->addColumn('units', function ($product) {
                    return view('reports.partials.units-column', [
                        'units' => $product->productUnits
                    ])->render();
                })
                ->addColumn('status', function ($product) {
                    $totalStock = $product->productUnits->sum('stock');
                    $lowStock = $product->productUnits->filter(function ($unit) {
                            return $unit->stock <= $unit->min_stock;
                        })->count() > 0;

                    if ($totalStock <= 0) {
                        return '<span class="badge bg-danger">Stok Habis</span>';
                    } elseif ($lowStock) {
                        return '<span class="badge bg-warning">Stok Menipis</span>';
                    } else {
                        return '<span class="badge bg-success">Tersedia</span>';
                    }
                })
                ->editColumn('category.name', function ($product) {
                    return $product->category->name;
                })
                ->rawColumns(['units', 'status'])
                ->make(true);
        }

        $products = Product::with(['category', 'productUnits.unit'])
            ->when($storeId, function($query) use ($storeId) {
                $query->where('store_id', $storeId);
            })
            ->where('is_active', true)
            ->get();

        $summary = [
            'total_products' => $products->count(),
            'low_stock' => $products->filter(function($product) {
                return $product->productUnits->contains(function($unit) {
                    return $unit->stock <= $unit->min_stock;
                });
            })->count(),
            'available' => $products->filter(function($product) {
                return $product->productUnits->sum('stock') > 0;
            })->count(),
            'out_of_stock' => $products->filter(function($product) {
                return $product->productUnits->sum('stock') <= 0;
            })->count(),
        ];

        return view('reports.inventory', compact('summary'));
    }

    public function storeStockMovement(Request $request)
    {
        $storeId = $this->getStoreId();

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'product_id' => 'nullable|exists:products,id'
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        if ($request->ajax()) {
            $query = StockHistory::with(['productUnit.product', 'productUnit.unit'])
                ->when($storeId, function($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                })
                ->whereBetween('created_at', [$startDate, $endDate]);

            if ($request->product_id) {
                $query->whereHas('productUnit', function($q) use ($request, $storeId) {
                    $q->where('product_id', $request->product_id)
                        ->when($storeId, function($q) use ($storeId) {
                            $q->where('store_id', $storeId);
                        });
                });
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function($movement) {
                    return $movement->created_at->format('d/m/Y H:i');
                })
                ->editColumn('productUnit.product.name', function($movement) {
                    return $movement->productUnit->product->name;
                })
                ->editColumn('productUnit.unit.name', function($movement) {
                    return $movement->productUnit->unit->name;
                })
                ->editColumn('type', function($movement) {
                    $badges = [
                        'in' => 'success',
                        'out' => 'danger',
                        'adjustment' => 'warning'
                    ];
                    $labels = [
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'adjustment' => 'Penyesuaian'
                    ];
                    return '<span class="badge bg-' . $badges[$movement->type] . '">' .
                        $labels[$movement->type] . '</span>';
                })
                ->editColumn('reference_type', function($movement) {
                    $refTypes = [
                        'stock_adjustments' => 'Penyesuaian Stok',
                        'transactions' => 'Transaksi',
                        'stock_takes' => 'Stok Opname'
                    ];
                    return $refTypes[$movement->reference_type] ?? $movement->reference_type;
                })
                ->rawColumns(['type'])
                ->make(true);
        }

        $products = Product::when($storeId, function($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })
            ->where('is_active', true)
            ->get();

        return view('reports.stock-movement', compact('products', 'startDate', 'endDate'));
    }

    public function storeFinancial(Request $request)  // Menggantikan financial()
    {
        $storeId = Auth::user()->store_id;

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        if ($request->ajax()) {
            $query = BalanceMutation::with('createdBy')
                ->when($storeId, function($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                })
                ->whereBetween('created_at', [$startDate, $endDate]);

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('created_at', function($mutation) {
                    return $mutation->created_at->format('d/m/Y H:i');
                })
                ->editColumn('type', function($mutation) {
                    return '<span class="badge bg-' . ($mutation->type === 'in' ? 'success' : 'danger') . '">' .
                        ($mutation->type === 'in' ? 'MASUK' : 'KELUAR') . '</span>';
                })
                ->editColumn('amount', function($mutation) {
                    return 'Rp ' . number_format($mutation->amount, 0, ',', '.');
                })
                ->editColumn('source_type', function($mutation) {
                    $sourceLabels = [
                        'App\Models\Transaction' => 'Transaksi Penjualan',
                        'App\Models\Purchase' => 'Transaksi Pembelian',
                        'App\Models\Expense' => 'Pengeluaran',
                        'App\Models\CashAdjustment' => 'Penyesuaian Kas',
                        // Tambahkan mapping lainnya sesuai kebutuhan
                    ];

                    $label = $sourceLabels[$mutation->source_type] ?? 'Lainnya';

                    if ($mutation->source_id) {
                        $label .= " #" . $mutation->source_id;
                    }

                    return $label;
                })
                ->editColumn('previous_balance', function($mutation) {
                    return 'Rp ' . number_format($mutation->previous_balance, 0, ',', '.');
                })
                ->editColumn('current_balance', function($mutation) {
                    return 'Rp ' . number_format($mutation->current_balance, 0, ',', '.');
                })
                ->editColumn('createdBy.name', function($mutation) {
                    return $mutation->createdBy->name;
                })
                ->editColumn('payment_method', function($mutation) {
                    $methodLabels = [
                        'cash' => 'Tunai',
                        'transfer' => 'Transfer',
                        'adjustment' => 'Penyesuaian'
                    ];
                    return $methodLabels[$mutation->payment_method] ?? ucfirst($mutation->payment_method);
                })
                ->rawColumns(['type'])
                ->make(true);
        }

        // Get summary data
        $summaryQuery = BalanceMutation::when($storeId, function($query) use ($storeId) {
            $query->where('store_id', $storeId);
        })
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalIn = $summaryQuery->clone()->where('type', 'in')->sum('amount');
        $totalOut = $summaryQuery->clone()->where('type', 'out')->sum('amount');

        $summary = [
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'net_amount' => $totalIn - $totalOut,
            'current_balance' => DB::table('store_balances')->first()->amount ?? 0,
        ];

        return view('reports.financial', compact('summary', 'startDate', 'endDate'));
    }
}
