@extends('layouts.app')

@section('content')

    <x-section-header
        title="Laporan Stok"
    />

    <!-- Kartu Ringkasan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Produk</h6>
                    <h4 class="mb-0">{{ $products->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">Stok Menipis</h6>
                    <h4 class="mb-0">{{ $products->where('low_stock', true)->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Tersedia</h6>
                    <h4 class="mb-0">{{ $products->where('total_stock', '>', 0)->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Stok Habis</h6>
                    <h4 class="mb-0">{{ $products->where('total_stock', 0)->count() }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- Tabel Inventaris -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Total Stok</th>
                        <th>Satuan</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product['code'] }}</td>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ $product['category'] }}</td>
                            <td>{{ $product['total_stock'] }}</td>
                            <td>
                                @foreach($product['units'] as $unit)
                                    <div class="mb-1">
                                        {{ $unit->unit->name }}: {{ $unit->stock }}
                                        @if($unit->stock <= $unit->min_stock)
                                            <span class="badge bg-danger">Stok Menipis</span>
                                        @endif
                                    </div>
                                @endforeach
                            </td>
                            <td>
                                @if($product['total_stock'] <= 0)
                                    <span class="badge bg-danger">Stok Habis</span>
                                @elseif($product['low_stock'])
                                    <span class="badge bg-warning">Stok Menipis</span>
                                @else
                                    <span class="badge bg-success">Tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada produk ditemukan</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
