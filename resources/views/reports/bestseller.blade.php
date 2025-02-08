<!-- resources/views/reports/bestseller.blade.php -->
@extends('reports.layout')

@section('report-title', 'Laporan Produk Terlaris')

@section('report-filters')
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control"
                    value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" onchange="this.form.submit()">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control"
                    value="{{ request('end_date', now()->format('Y-m-d')) }}" onchange="this.form.submit()">
            </div>
        </div>
    </form>
@endsection

@section('report-content')
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Total Qty Terjual</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item['product']->name }}</td>
                        <td>{{ $item['product']->category->name }}</td>
                        <td class="text-end">{{ number_format($item['total_quantity']) }}</td>
                        <td class="text-end">{{ number_format($item['total_amount']) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total</strong></td>
                    <td class="text-end">
                        <strong>{{ number_format($products->sum('total_quantity')) }}</strong>
                    </td>
                    <td class="text-end">
                        <strong>{{ number_format($products->sum('total_amount')) }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
