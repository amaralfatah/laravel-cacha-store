<!-- resources/views/product-price/index.blade.php -->
@extends('layouts.app')

@section('content')

    <div class="row mb-4">
        <div class="col">
            <h2>Manajemen Harga Produk</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga Dasar</th>
                    <th>Pajak</th>
                    <th>Diskon</th>
                    <th>Unit Default</th>
                    <th>Jumlah Harga Bertingkat</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>Rp {{ number_format($product->base_price, 0, ',', '.') }}</td>
                        <td>
                            @if ($product->tax)
                                {{ $product->tax->name }} ({{ $product->tax->rate }}%)
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if ($product->discount)
                                {{ $product->discount->name }}
                                ({{ $product->discount->type === 'percentage' ? $product->discount->value . '%' : 'Rp ' . number_format($product->discount->value, 0, ',', '.') }}
                                )
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $product->defaultUnit->name ?? '-' }}</td>
                        <td>
                            @if($product->priceTiers->count() > 0)
                                <span class="badge bg-primary">
                                                {{ $product->priceTiers->count() }} tingkat
                                            </span>
                            @else
                                <span class="badge bg-secondary">Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('product-price.edit', $product) }}"
                               class="btn btn-sm btn-warning">Edit</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
