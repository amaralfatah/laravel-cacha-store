<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\BalanceMutation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // ReportController.php

    public function sales(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        if ($request->ajax()) {
            $query = Transaction::with(['customer', 'cashier', 'items.product', 'items.unit'])
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
                ->editColumn('cashier.name', function($sale) {
                    return $sale->cashier->name;
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
        $salesQuery = Transaction::whereBetween('invoice_date', [$startDate, $endDate])
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

    public function inventory(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::with(['category', 'productUnits.unit'])
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

        // Get summary data
        $products = Product::with(['category', 'productUnits.unit'])
            ->where('is_active', true)
            ->get()
            ->map(function ($product) {
                $totalStock = $product->productUnits->sum('stock');
                $lowStock = $product->productUnits->filter(function ($unit) {
                        return $unit->stock <= $unit->min_stock;
                    })->count() > 0;

                return [
                    'total_stock' => $totalStock,
                    'low_stock' => $lowStock,
                ];
            });

        $summary = [
            'total_products' => $products->count(),
            'low_stock' => $products->where('low_stock', true)->count(),
            'available' => $products->where('total_stock', '>', 0)->count(),
            'out_of_stock' => $products->where('total_stock', 0)->count(),
        ];

        return view('reports.inventory', compact('summary'));
    }

    public function stockMovement(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'product_id' => 'nullable|exists:products,id'
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        if ($request->ajax()) {
            $query = StockHistory::with(['productUnit.product', 'productUnit.unit'])
                ->whereBetween('created_at', [$startDate, $endDate]);

            if ($request->product_id) {
                $query->whereHas('productUnit', function($q) use ($request) {
                    $q->where('product_id', $request->product_id);
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

        $products = Product::where('is_active', true)->get();

        return view('reports.stock-movement', compact('products', 'startDate', 'endDate'));
    }

    public function financial(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        if ($request->ajax()) {
            $query = BalanceMutation::with('createdBy')
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
                    $text = ucwords(str_replace('_', ' ', $mutation->source_type));
                    if ($mutation->source_id) {
                        $text .= " #" . $mutation->source_id;
                    }
                    return $text;
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
                ->rawColumns(['type'])
                ->make(true);
        }

        // Get summary data
        $summaryQuery = BalanceMutation::whereBetween('created_at', [$startDate, $endDate]);

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

    public function exportSales(Request $request)
    {
        // Implementation for exporting sales report to Excel/PDF
    }

    public function exportInventory(Request $request)
    {
        // Implementation for exporting inventory report to Excel/PDF
    }

    public function exportFinancial(Request $request)
    {
        // Implementation for exporting financial report to Excel/PDF
    }
}
