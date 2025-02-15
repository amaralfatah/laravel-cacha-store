<!-- resources/views/products/index.blade.php -->
@extends('layouts.app')

@section('content')

    <x-section-header
        title="Manajemen Produk"
        :route="route('products.create')"
        buttonText="Tambah Produk"
        icon="bx-plus"
    />

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Kode Produk</th>
                    <th>Nama</th>
                    <th>Barcode</th>
                    <th>Kategori</th>
                    <th>Harga Beli</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th class="w-100">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>{{ $product->code }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->barcode }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>
                            @php
                                $defaultUnit = $product->productUnits->first();
                                $purchasePrice = $defaultUnit ? $defaultUnit->purchase_price : 0;
                            @endphp
                            Rp {{ number_format($purchasePrice, 2) }}
                        </td>
                        <td>
                            @php
                                $sellingPrice = $defaultUnit ? $defaultUnit->selling_price : 0;
                            @endphp
                            Rp {{ number_format($sellingPrice, 2) }}
                        </td>
                        <td>
                            @php
                                $stock = $defaultUnit ? $defaultUnit->stock : 0;
                            @endphp
                            {{ number_format($stock) }}
                        </td>
                        <td>
                           <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                               {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                           </span>
                        </td>
                        <td >
                            <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">Lihat</a>
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Ubah</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Apakah anda yakin?')">Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $products->links() }}
        </div>
    </div>
@endsection
