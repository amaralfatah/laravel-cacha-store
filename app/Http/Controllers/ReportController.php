<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\Inventory;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $type = $request->type ?? 'daily';
        $date = $request->date ?? now();
        $startDate = $type === 'daily'
            ? Carbon::parse($date)->startOfDay()
            : Carbon::parse($date)->startOfMonth();
        $endDate = $type === 'daily'
            ? Carbon::parse($date)->endOfDay()
            : Carbon::parse($date)->endOfMonth();

        $sales = Transaction::with(['customer', 'items.product', 'items.unit'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'success')
            ->get();

        return view('reports.sales', compact('sales', 'type', 'date'));
    }

    public function stockReport()
    {
        $stocks = Inventory::with(['product', 'unit'])
            ->get();

        return view('reports.stock', compact('stocks'));
    }

    public function bestSellerReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now();

        $products = Transaction::with(['items.product'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'success')
            ->get()
            ->flatMap(fn($transaction) => $transaction->items)
            ->groupBy('product_id')
            ->map(function ($items) {
                return [
                    'product' => $items->first()->product,
                    'total_quantity' => $items->sum('quantity'),
                    'total_amount' => $items->sum('subtotal'),
                ];
            })
            ->sortByDesc('total_quantity')
            ->values();

        return view('reports.bestseller', compact('products', 'startDate', 'endDate'));
    }

    public function profitReport(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now();

        $transactions = Transaction::with(['items.product'])
            ->whereBetween('invoice_date', [$startDate, $endDate])
            ->where('status', 'success')
            ->get();

        $profits = $transactions->map(function ($transaction) {
            $cost = $transaction->items->sum(function ($item) {
                return $item->product->base_price * $item->quantity;
            });

            return [
                'invoice_number' => $transaction->invoice_number,
                'date' => $transaction->invoice_date,
                'revenue' => $transaction->final_amount,
                'cost' => $cost,
                'profit' => $transaction->final_amount - $cost,
            ];
        });

        return view('reports.profit', compact('profits', 'startDate', 'endDate'));
    }

    private function getReportData(Request $request, $type)
    {
        switch ($type) {
            case 'sales':
                $reportType = $request->type ?? 'daily';
                $date = $request->date ?? now();
                $startDate = $reportType === 'daily'
                    ? Carbon::parse($date)->startOfDay()
                    : Carbon::parse($date)->startOfMonth();
                $endDate = $reportType === 'daily'
                    ? Carbon::parse($date)->endOfDay()
                    : Carbon::parse($date)->endOfMonth();

                return [
                    'sales' => Transaction::with(['customer', 'items.product', 'items.unit'])
                        ->whereBetween('invoice_date', [$startDate, $endDate])
                        ->where('status', 'success')
                        ->get(),
                    'type' => $reportType,
                    'date' => $date
                ];

            case 'stock':
                return [
                    'stocks' => Inventory::with(['product', 'unit'])->get()
                ];

            case 'bestseller':
                $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
                $endDate = $request->end_date ?? Carbon::now();

                return [
                    'products' => Transaction::with(['items.product'])
                        ->whereBetween('invoice_date', [$startDate, $endDate])
                        ->where('status', 'success')
                        ->get()
                        ->flatMap(fn($transaction) => $transaction->items)
                        ->groupBy('product_id')
                        ->map(function ($items) {
                            return [
                                'product' => $items->first()->product,
                                'total_quantity' => $items->sum('quantity'),
                                'total_amount' => $items->sum('subtotal'),
                            ];
                        })
                        ->sortByDesc('total_quantity')
                        ->values(),
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ];

            case 'profit':
                $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
                $endDate = $request->end_date ?? Carbon::now();

                $transactions = Transaction::with(['items.product'])
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->where('status', 'success')
                    ->get();

                return [
                    'profits' => $transactions->map(function ($transaction) {
                        $cost = $transaction->items->sum(function ($item) {
                            return $item->product->base_price * $item->quantity;
                        });

                        return [
                            'invoice_number' => $transaction->invoice_number,
                            'date' => $transaction->invoice_date,
                            'revenue' => $transaction->final_amount,
                            'cost' => $cost,
                            'profit' => $transaction->final_amount - $cost,
                        ];
                    }),
                    'startDate' => $startDate,
                    'endDate' => $endDate
                ];

            default:
                return [];
        }
    }

    public function exportPDF(Request $request, $type)
    {
        switch ($type) {
            case 'sales':
                $type = $request->type ?? 'daily';
                $date = $request->date ?? now();
                $startDate = $type === 'daily'
                    ? Carbon::parse($date)->startOfDay()
                    : Carbon::parse($date)->startOfMonth();
                $endDate = $type === 'daily'
                    ? Carbon::parse($date)->endOfDay()
                    : Carbon::parse($date)->endOfMonth();

                $sales = Transaction::with(['customer', 'items.product', 'items.unit'])
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->where('status', 'success')
                    ->get();

                $pdf = Pdf::loadView('reports.pdf.sales', compact('sales'));
                return $pdf->download('laporan-penjualan.pdf');

            case 'stock':
                $stocks = Inventory::with(['product.category', 'unit'])->get();
                $pdf = Pdf::loadView('reports.pdf.stock', compact('stocks'));
                return $pdf->download('laporan-stok.pdf');

            case 'bestseller':
                $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
                $endDate = $request->end_date ?? Carbon::now();

                $products = Transaction::with(['items.product'])
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->where('status', 'success')
                    ->get()
                    ->flatMap(fn($transaction) => $transaction->items)
                    ->groupBy('product_id')
                    ->map(function ($items) {
                        return [
                            'product' => $items->first()->product,
                            'total_quantity' => $items->sum('quantity'),
                            'total_amount' => $items->sum('subtotal'),
                        ];
                    })
                    ->sortByDesc('total_quantity')
                    ->values();

                $pdf = Pdf::loadView('reports.pdf.bestseller', compact('products', 'startDate', 'endDate'));
                return $pdf->download('laporan-produk-terlaris.pdf');

            case 'profit':
                $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
                $endDate = $request->end_date ?? Carbon::now();

                $transactions = Transaction::with(['items.product'])
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->where('status', 'success')
                    ->get();

                $profits = $transactions->map(function ($transaction) {
                    $cost = $transaction->items->sum(function ($item) {
                        return $item->product->base_price * $item->quantity;
                    });

                    return [
                        'invoice_number' => $transaction->invoice_number,
                        'date' => $transaction->invoice_date,
                        'revenue' => $transaction->final_amount,
                        'cost' => $cost,
                        'profit' => $transaction->final_amount - $cost,
                    ];
                });

                $pdf = Pdf::loadView('reports.pdf.profit', compact('profits', 'startDate', 'endDate'));
                return $pdf->download('laporan-keuntungan.pdf');
        }
    }

    public function exportExcel(Request $request, $type)
    {
        return Excel::download(new ReportExport($request, $type), "laporan-{$type}.xlsx");
    }
}
