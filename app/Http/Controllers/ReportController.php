<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use App\Models\StockHistory;
use App\Models\BalanceMutation;
use App\Models\PurchaseOrder;
use App\Models\Store;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    protected function getStoreId()
    {
        $user = Auth::user();
        return $user->role === 'admin' ? null : $user->store_id;
    }

    /**
     * Sales report
     */
    public function sales(Request $request)
    {
        $storeId = $this->getStoreId();

        if ($request->ajax()) {
            $query = Transaction::with(['customer', 'cashier'])
                ->when($storeId, function ($q) use ($storeId) {
                    return $q->where('store_id', $storeId);
                })
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('invoice_date', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('invoice_date', '<=', $request->end_date);
                })
                ->when($request->status, function ($q) use ($request) {
                    return $q->where('status', $request->status);
                })
                ->orderBy('invoice_date', 'desc');

            return DataTables::of($query)
                ->addColumn('customer_name', function ($row) {
                    return $row->customer->name ?? 'N/A';
                })
                ->addColumn('cashier_name', function ($row) {
                    return $row->cashier->name ?? 'N/A';
                })
                ->addColumn('formatted_date', function ($row) {
                    return Carbon::parse($row->invoice_date)->format('d M Y H:i');
                })
                ->addColumn('formatted_total', function ($row) {
                    return 'Rp ' . number_format($row->final_amount, 0, ',', '.');
                })
                ->make(true);
        }

        // Summary data
        $totalSales = Transaction::when($storeId, function ($q) use ($storeId) {
            return $q->where('store_id', $storeId);
        })
            ->where('status', 'success')
            ->sum('final_amount');

        $salesCount = Transaction::when($storeId, function ($q) use ($storeId) {
            return $q->where('store_id', $storeId);
        })
            ->where('status', 'success')
            ->count();

        $topProducts = DB::table('transaction_items')
            ->join('transactions', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->select(
                'products.name',
                DB::raw('SUM(transaction_items.quantity) as total_qty'),
                DB::raw('SUM(transaction_items.subtotal) as total_amount')
            )
            ->when($storeId, function ($q) use ($storeId) {
                return $q->where('transactions.store_id', $storeId);
            })
            ->where('transactions.status', 'success')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();

        return view('reports.sales', compact('totalSales', 'salesCount', 'topProducts'));
    }

    public function financial(Request $request)
    {
        $storeId = $this->getStoreId();

        if ($request->ajax()) {
            $query = BalanceMutation::with('creator')
                ->when($storeId, function ($q) use ($storeId) {
                    return $q->where('store_id', $storeId);
                })
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('created_at', '<=', $request->end_date);
                })
                ->when($request->type, function ($q) use ($request) {
                    return $q->where('type', $request->type);
                })
                ->when($request->payment_method, function ($q) use ($request) {
                    return $q->where('payment_method', $request->payment_method);
                })
                ->orderBy('created_at', 'desc');

            return DataTables::of($query)
                ->addColumn('created_by', function ($row) {
                    return $row->creator->name ?? 'System';
                })
                ->addColumn('formatted_date', function ($row) {
                    return Carbon::parse($row->created_at)->format('d M Y H:i');
                })
                ->addColumn('formatted_amount', function ($row) {
                    $prefix = $row->type === 'in' ? '+ ' : '- ';
                    return $prefix . 'Rp ' . number_format($row->amount, 0, ',', '.');
                })
                ->addColumn('formatted_balance', function ($row) {
                    return 'Rp ' . number_format($row->current_balance, 0, ',', '.');
                })
                ->make(true);
        }

        // Summary data
        $totalIncome = BalanceMutation::when($storeId, function ($q) use ($storeId) {
            return $q->where('store_id', $storeId);
        })
            ->where('type', 'in')
            ->sum('amount');

        $totalExpense = BalanceMutation::when($storeId, function ($q) use ($storeId) {
            return $q->where('store_id', $storeId);
        })
            ->where('type', 'out')
            ->sum('amount');

        $netCashflow = $totalIncome - $totalExpense;

        // Get store balance data (cash + non-cash)
        $storeBalance = DB::table('store_balances')
            ->where('store_id', $storeId)
            ->first();

        // Calculate total balance
        $totalBalance = 0;
        if ($storeBalance) {
            $totalBalance = $storeBalance->cash_amount + $storeBalance->non_cash_amount;
        }

        return view('reports.financial', compact('totalIncome', 'totalExpense', 'netCashflow', 'totalBalance'));
    }

    public function inventory(Request $request)
    {
        $storeId = $this->getStoreId();

        if ($request->ajax()) {
            // Base product query
            $query = Product::with(['category', 'supplier', 'productUnits.unit'])
                ->when($storeId, function ($q) use ($storeId) {
                    return $q->where('store_id', $storeId);
                })
                ->when($request->category_id, function ($q) use ($request) {
                    return $q->where('category_id', $request->category_id);
                })
                ->when($request->supplier_id, function ($q) use ($request) {
                    return $q->where('supplier_id', $request->supplier_id);
                })
                ->when($request->status, function ($q) use ($request) {
                    if ($request->status === 'out_of_stock') {
                        return $q->whereHas('productUnits', function ($query) {
                            $query->where('stock', '<=', 0);
                        });
                    } elseif ($request->status === 'low_stock') {
                        return $q->whereHas('productUnits', function ($query) {
                            $query->whereRaw('stock <= min_stock')->where('stock', '>', 0);
                        });
                    } elseif ($request->status === 'in_stock') {
                        return $q->whereHas('productUnits', function ($query) {
                            $query->whereRaw('stock > min_stock');
                        });
                    }
                });


            // Get counts for summary cards (based on the same filters)
            $totalProducts = $query->count();

            // Clone the query for each stock status
            $inStockCount = clone $query;
            $lowStockCount = clone $query;
            $outOfStockCount = clone $query;

            // Modify each query to get the specific counts
            $inStockCount = $inStockCount->whereHas('productUnits', function ($q) {
                $q->whereRaw('stock > min_stock');
            })->count();

            $lowStockCount = $lowStockCount->whereHas('productUnits', function ($q) {
                $q->whereRaw('stock <= min_stock')->where('stock', '>', 0);
            })->count();

            $outOfStockCount = $outOfStockCount->whereHas('productUnits', function ($q) {
                $q->where('stock', '<=', 0);
            })->count();

            $response = DataTables::of($query)
                ->addColumn('category_name', function ($row) {
                    return $row->category->name ?? 'N/A';
                })
                ->addColumn('supplier_name', function ($row) {
                    return $row->supplier->name ?? 'N/A';
                })
                ->addColumn('stock_status', function ($row) {
                    $defaultUnit = $row->productUnits->where('is_default', true)->first();
                    if (!$defaultUnit)
                        return 'No Units';

                    $stock = $defaultUnit->stock;
                    $minStock = $defaultUnit->min_stock;

                    if ($stock <= 0) {
                        return '<span class="badge bg-danger">Out of Stock</span>';
                    } elseif ($stock <= $minStock) {
                        return '<span class="badge bg-warning">Low Stock</span>';
                    } else {
                        return '<span class="badge bg-success">In Stock</span>';
                    }
                })
                ->addColumn('current_stock', function ($row) {
                    $defaultUnit = $row->productUnits->where('is_default', true)->first();
                    if (!$defaultUnit)
                        return 'N/A';

                    return $defaultUnit->stock . ' ' . ($defaultUnit->unit->name ?? '');
                })
                ->rawColumns(['stock_status']);

            // Attach summary counts to the response
            $response = $response->with([
                'summary' => [
                    'totalProducts' => $totalProducts,
                    'inStockCount' => $inStockCount,
                    'lowStockCount' => $lowStockCount,
                    'outOfStockCount' => $outOfStockCount
                ]
            ]);

            return $response->make(true);
        }

        // Get filter data
        $categories = Category::when($storeId, function ($q) use ($storeId) {
            return $q->where('store_id', $storeId);
        })->where('is_active', true)->get();

        $suppliers = Supplier::when($storeId, function ($q) use ($storeId) {
            return $q->where('store_id', $storeId);
        })->get();

        return view('reports.inventory', compact('categories', 'suppliers'));
    }

    public function purchasing(Request $request)
    {
        $storeId = $this->getStoreId();

        if ($request->ajax()) {
            $query = PurchaseOrder::with(['supplier'])
                ->when($storeId, function ($q) use ($storeId) {
                    return $q->where('store_id', $storeId);
                })
                ->when($request->start_date, function ($q) use ($request) {
                    return $q->whereDate('purchase_date', '>=', $request->start_date);
                })
                ->when($request->end_date, function ($q) use ($request) {
                    return $q->whereDate('purchase_date', '<=', $request->end_date);
                })
                ->when($request->supplier_id, function ($q) use ($request) {
                    return $q->where('supplier_id', $request->supplier_id);
                })
                ->when($request->status, function ($q) use ($request) {
                    return $q->where('status', $request->status);
                })
                ->orderBy('purchase_date', 'desc');

            return DataTables::of($query)
                ->addColumn('supplier_name', function ($row) {
                    return $row->supplier->name ?? 'N/A';
                })
                ->addColumn('formatted_date', function ($row) {
                    return Carbon::parse($row->purchase_date)->format('d M Y');
                })
                ->addColumn('formatted_total', function ($row) {
                    return 'Rp ' . number_format($row->final_amount, 0, ',', '.');
                })
                ->addColumn('status_label', function ($row) {
                    if ($row->status === 'completed') {
                        return '<span class="badge bg-success">Completed</span>';
                    } elseif ($row->status === 'pending') {
                        return '<span class="badge bg-warning">Pending</span>';
                    } else {
                        return '<span class="badge bg-danger">Cancelled</span>';
                    }
                })
                ->rawColumns(['status_label'])
                ->make(true);
        }

        // Get suppliers for filter
        $suppliers = Supplier::when($storeId, function ($q) use ($storeId) {
            return $q->where('store_id', $storeId);
        })->get();

        // Get summary data
        $totalPurchases = PurchaseOrder::when($storeId, function ($q) use ($storeId) {
            return $q->where('store_id', $storeId);
        })
            ->where('status', 'completed')
            ->sum('final_amount');

        $pendingPurchasesCount = PurchaseOrder::when($storeId, function ($q) use ($storeId) {
            return $q->where('store_id', $storeId);
        })
            ->where('status', 'pending')
            ->count();

        return view('reports.purchasing', compact('suppliers', 'totalPurchases', 'pendingPurchasesCount'));
    }

    public function storePerformance()
    {
        // Only admin should access this
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        // Get stores with performance metrics
        $stores = Store::withCount([
            'transactions as sales_count' => function ($query) {
                $query->where('status', 'success');
            }
        ])
            ->withSum([
                'transactions as revenue' => function ($query) {
                    $query->where('status', 'success');
                }
            ], 'final_amount')
            ->withCount('customers')
            ->where('is_active', true)
            ->get();

        // Get top-performing stores
        $topStores = Store::withSum([
            'transactions as revenue' => function ($query) {
                $query->where('status', 'success')
                    ->whereMonth('invoice_date', Carbon::now()->month)
                    ->whereYear('invoice_date', Carbon::now()->year);
            }
        ], 'final_amount')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return view('reports.store_performance', compact('stores', 'topStores'));
    }

    public function salesByPaymentType(Request $request)
    {
        $storeId = $this->getStoreId();

        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        $paymentData = Transaction::select('payment_type', DB::raw('COUNT(*) as count'), DB::raw('SUM(final_amount) as amount'))
            ->when($storeId, function ($q) use ($storeId) {
                return $q->where('store_id', $storeId);
            })
            ->where('status', 'success')
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->groupBy('payment_type')
            ->get();

        // Calculate percentages
        $totalAmount = $paymentData->sum('amount');
        $chartData = $paymentData->map(function ($item) use ($totalAmount) {
            return [
                'payment_type' => $item->payment_type,
                'amount' => $totalAmount > 0 ? round(($item->amount / $totalAmount) * 100, 1) : 0,
                'raw_amount' => $item->amount,
                'count' => $item->count
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $chartData
        ]);
    }

    public function financialChart(Request $request)
    {
        $storeId = $this->getStoreId();

        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        // Calculate cashflow data grouped by date in a single query
        $cashflowData = BalanceMutation::selectRaw('
        DATE(created_at) as date,
        SUM(CASE WHEN type = "in" THEN amount ELSE 0 END) as income,
        SUM(CASE WHEN type = "out" THEN amount ELSE 0 END) as expense,
        COUNT(*) as transactions_count
    ')
            ->when($storeId, function ($q) use ($storeId) {
                return $q->where('store_id', $storeId);
            })
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('d M Y');
                return $item;
            });

        // Calculate payment method distribution with better handling for null values
        $paymentMethodsData = BalanceMutation::selectRaw('
        COALESCE(payment_method, "unknown") as method,
        COUNT(*) as count,
        SUM(amount) as total_amount
    ')
            ->when($storeId, function ($q) use ($storeId) {
                return $q->where('store_id', $storeId);
            })
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        // Calculate percentages
        $totalTransactions = $paymentMethodsData->sum('count');

        $paymentMethodsData = $paymentMethodsData
            ->filter(function ($item) {
                return $item->method != 'unknown' && $item->method != 'adjustment';
            })
            ->map(function ($item) use ($totalTransactions) {
                return [
                    'method' => $item->method,
                    'count' => $item->count,
                    'amount' => $item->total_amount,
                    'percentage' => $totalTransactions > 0 ? round(($item->count / $totalTransactions) * 100, 1) : 0
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'cashflow' => $cashflowData,
            'payment_methods' => $paymentMethodsData
        ]);
    }

    public function purchasingChart(Request $request)
    {
        $storeId = $this->getStoreId();

        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->format('Y-m-d');

        // Calculate purchase data grouped by date
        $purchaseData = PurchaseOrder::selectRaw('
        DATE(purchase_date) as date,
        COUNT(*) as count,
        SUM(final_amount) as amount
    ')
            ->when($storeId, function ($q) use ($storeId) {
                return $q->where('store_id', $storeId);
            })
            ->where('status', 'completed')
            ->whereBetween(DB::raw('DATE(purchase_date)'), [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(purchase_date)'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                $item->date = Carbon::parse($item->date)->format('d M Y');
                return $item;
            });

        // Calculate payment method distribution
        $paymentMethodsData = PurchaseOrder::selectRaw('
        payment_type as method,
        COUNT(*) as count,
        SUM(final_amount) as total_amount
    ')
            ->when($storeId, function ($q) use ($storeId) {
                return $q->where('store_id', $storeId);
            })
            ->where('status', 'completed')
            ->whereBetween(DB::raw('DATE(purchase_date)'), [$startDate, $endDate])
            ->groupBy('payment_type')
            ->get();

        // Calculate percentages
        $totalTransactions = $paymentMethodsData->sum('count');

        $paymentMethodsData = $paymentMethodsData
            ->map(function ($item) use ($totalTransactions) {
                return [
                    'method' => $item->method,
                    'count' => $item->count,
                    'amount' => $item->total_amount,
                    'percentage' => $totalTransactions > 0 ? round(($item->count / $totalTransactions) * 100, 1) : 0
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'purchases' => $purchaseData,
            'payment_methods' => $paymentMethodsData
        ]);
    }
}
