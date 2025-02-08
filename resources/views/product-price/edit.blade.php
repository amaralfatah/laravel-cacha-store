<!-- resources/views/product-price/edit.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Edit Harga Produk: {{ $product->name }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('product-price.update', $product) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="base_price" class="form-label">Harga Dasar</label>
                                        <input type="number" step="0.01"
                                            class="form-control @error('base_price') is-invalid @enderror" id="base_price"
                                            name="base_price" value="{{ old('base_price', $product->base_price) }}">
                                        @error('base_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="tax_id" class="form-label">Pajak</label>
                                        <select name="tax_id" id="tax_id"
                                            class="form-select @error('tax_id') is-invalid @enderror">
                                            <option value="">Tanpa Pajak</option>
                                            @foreach ($taxes as $tax)
                                                <option value="{{ $tax->id }}"
                                                    {{ old('tax_id', $product->tax_id) == $tax->id ? 'selected' : '' }}>
                                                    {{ $tax->name }} ({{ $tax->rate }}%)
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tax_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="discount_id" class="form-label">Diskon</label>
                                        <select name="discount_id" id="discount_id"
                                            class="form-select @error('discount_id') is-invalid @enderror">
                                            <option value="">Tanpa Diskon</option>
                                            @foreach ($discounts as $discount)
                                                <option value="{{ $discount->id }}"
                                                    {{ old('discount_id', $product->discount_id) == $discount->id ? 'selected' : '' }}>
                                                    {{ $discount->name }}
                                                    ({{ $discount->type === 'percentage' ? $discount->value . '%' : 'Rp ' . number_format($discount->value, 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('discount_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Update Harga Dasar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Price Tiers Section -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Harga Bertingkat</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addPriceTierModal">
                            Tambah Harga Bertingkat
                        </button>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Unit</th>
                                    <th>Minimum Quantity</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($priceTiers as $tier)
                                    <tr>
                                        <td>{{ $tier->unit->name }}</td>
                                        <td>{{ $tier->min_quantity }}</td>
                                        <td>Rp {{ number_format($tier->price, 0, ',', '.') }}</td>
                                        <td>
                                            <form action="{{ route('product-price.price-tier.destroy', $tier) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Price Tier Modal -->
    <div class="modal fade" id="addPriceTierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Harga Bertingkat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('product-price.price-tier.store', $product) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="unit_id" class="form-label">Unit</label>
                            <select name="unit_id" id="unit_id" class="form-select" required>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="min_quantity" class="form-label">Minimum Quantity</label>
                            <input type="number" class="form-control" id="min_quantity" name="min_quantity" required
                                min="1">
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Harga</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required
                                min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
