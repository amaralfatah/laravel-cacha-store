<?php
// app/Exports/ReportExport.php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\Inventory;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class ReportExport implements FromCollection, WithHeadings
{
    protected $request;
    protected $type;

    public function __construct(Request $request, $type)
    {
        $this->request = $request;
        $this->type = $type;
    }

    public function collection()
    {
        switch ($this->type) {
            case 'sales':
                $type = $this->request->type ?? 'daily';
                $date = $this->request->date ?? now();
                $startDate = $type === 'daily'
                    ? Carbon::parse($date)->startOfDay()
                    : Carbon::parse($date)->startOfMonth();
                $endDate = $type === 'daily'
                    ? Carbon::parse($date)->endOfDay()
                    : Carbon::parse($date)->endOfMonth();

                return Transaction::with(['customer', 'items.product', 'items.unit'])
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->where('status', 'success')
                    ->get()
                    ->map(function ($sale) {
                        return [
                            'Invoice' => $sale->invoice_number,
                            'Tanggal' => $sale->invoice_date,
                            'Customer' => $sale->customer->name,
                            'Total' => $sale->final_amount,
                            'Metode Pembayaran' => $sale->payment_type
                        ];
                    });

            case 'stock':
                return Inventory::with(['product', 'unit'])
                    ->get()
                    ->map(function ($stock) {
                        return [
                            'Produk' => $stock->product->name,
                            'Unit' => $stock->unit->name,
                            'Stok' => $stock->quantity,
                            'Minimum Stok' => $stock->min_stock
                        ];
                    });

            case 'bestseller':
                $startDate = $this->request->start_date ?? Carbon::now()->startOfMonth();
                $endDate = $this->request->end_date ?? Carbon::now();

                return Transaction::with(['items.product'])
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->where('status', 'success')
                    ->get()
                    ->flatMap(fn($transaction) => $transaction->items)
                    ->groupBy('product_id')
                    ->map(function ($items) {
                        $product = $items->first()->product;
                        return [
                            'Produk' => $product->name,
                            'Total Quantity' => $items->sum('quantity'),
                            'Total Penjualan' => $items->sum('subtotal')
                        ];
                    })
                    ->sortByDesc('Total Quantity')
                    ->values();

            case 'profit':
                $startDate = $this->request->start_date ?? Carbon::now()->startOfMonth();
                $endDate = $this->request->end_date ?? Carbon::now();

                return Transaction::with(['items.product'])
                    ->whereBetween('invoice_date', [$startDate, $endDate])
                    ->where('status', 'success')
                    ->get()
                    ->map(function ($transaction) {
                        $cost = $transaction->items->sum(function ($item) {
                            return $item->product->base_price * $item->quantity;
                        });

                        return [
                            'Invoice' => $transaction->invoice_number,
                            'Tanggal' => $transaction->invoice_date,
                            'Pendapatan' => $transaction->final_amount,
                            'Modal' => $cost,
                            'Keuntungan' => $transaction->final_amount - $cost
                        ];
                    });

            default:
                return collect([]);
        }
    }

    public function headings(): array
    {
        switch ($this->type) {
            case 'sales':
                return ['Invoice', 'Tanggal', 'Customer', 'Total', 'Metode Pembayaran'];
            case 'stock':
                return ['Produk', 'Unit', 'Stok', 'Minimum Stok'];
            case 'bestseller':
                return ['Produk', 'Total Quantity', 'Total Penjualan'];
            case 'profit':
                return ['Invoice', 'Tanggal', 'Pendapatan', 'Modal', 'Keuntungan'];
            default:
                return [];
        }
    }
}
