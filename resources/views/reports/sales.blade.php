@extends('layouts.app')

@section('content')

    <x-section-header
        title="Laporan Penjualan"
    />

    <!-- Form Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales') }}" >
                <div class="row">
                    <div class="col-md-4">
                        <div>
                            <label for="start_date" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                   value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <label for="end_date" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="end_date" name="end_date"
                                   value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div>
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Kartu Ringkasan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Penjualan</h6>
                    <h4 class="mb-0">Rp {{ number_format($summary['total_sales'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Jumlah Transaksi</h6>
                    <h4 class="mb-0">{{ $summary['total_transactions'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Rata-rata Transaksi</h6>
                    <h4 class="mb-0">Rp {{ number_format($summary['average_transaction'], 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Item Terjual</h6>
                    <h4 class="mb-0">{{ $summary['total_items'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Tabel Penjualan -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No. Faktur</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th>Jumlah Item</th>
                        <th>Total</th>
                        <th>Diskon</th>
                        <th>Pajak</th>
                        <th>Total Akhir</th>
                        <th>Pembayaran</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_number }}</td>
                            <td>{{ $sale->invoice_date->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->customer->name }}</td>
                            <td>{{ $sale->cashier->name }}</td>
                            <td>{{ $sale->items->sum('quantity') }}</td>
                            <td>Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($sale->discount_amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($sale->final_amount, 0, ',', '.') }}</td>
                            <td>{{ $sale->payment_type === 'cash' ? 'Tunai' : 'Transfer' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada data penjualan</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
