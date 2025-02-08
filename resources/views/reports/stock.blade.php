<!-- resources/views/reports/stock.blade.php -->
@extends('reports.layout')

@section('report-title', 'Laporan Stok')

@section('report-content')
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Unit</th>
                    <th>Stok Saat Ini</th>
                    <th>Minimum Stok</th>
                    <th>Status</th>
                    <th>Terakhir Update</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stocks as $stock)
                    <tr>
                        <td>{{ $stock->product->name }}</td>
                        <td>{{ $stock->product->category->name }}</td>
                        <td>{{ $stock->unit->name }}</td>
                        <td class="text-end">{{ number_format($stock->quantity) }}</td>
                        <td class="text-end">{{ number_format($stock->min_stock) }}</td>
                        <td>
                            @if ($stock->quantity <= $stock->min_stock)
                                <span class="badge bg-danger">Stok Rendah</span>
                            @else
                                <span class="badge bg-success">Normal</span>
                            @endif
                        </td>
                        <td>{{ $stock->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
