@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3>Edit Produk: {{ $product->name }}</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.update', $product) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @if(auth()->user()->role === 'admin')
                                <div class="mb-3">
                                    <label for="store_id" class="form-label">Toko</label>
                                    <select class="form-select @error('store_id') is-invalid @enderror"
                                            id="store_id" name="store_id" required>
                                        <option value="">Pilih Toko</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}"
                                                {{ old('store_id', $product->store_id ?? '') == $store->id ? 'selected' : '' }}>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('store_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $product->name) }}">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="code" class="form-label">Kode Produk</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                       id="code" name="code" value="{{ old('code', $product->code) }}">
                                @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="barcode" class="form-label">Kode Barcode</label>
                                <input type="text" class="form-control @error('barcode') is-invalid @enderror"
                                       id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                                @error('barcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Kategori</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                        name="category_id">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="purchase_price" class="form-label">Harga Beli</label>
                                <input type="number" step="0.01"
                                       class="form-control @error('purchase_price') is-invalid @enderror"
                                       id="purchase_price" name="purchase_price"
                                       value="{{ old('purchase_price', $defaultUnit ? $defaultUnit->purchase_price : 0) }}">
                                @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="selling_price" class="form-label">Harga Jual</label>
                                <input type="number" step="0.01"
                                       class="form-control @error('selling_price') is-invalid @enderror"
                                       id="selling_price" name="selling_price"
                                       value="{{ old('selling_price', $defaultUnit ? $defaultUnit->selling_price : 0) }}">
                                @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="stock" class="form-label">Stok</label>
                                <input type="number" step="1"
                                       class="form-control @error('stock') is-invalid @enderror"
                                       id="stock" name="stock"
                                       value="{{ old('stock', $defaultUnit ? $defaultUnit->stock : 0) }}">
                                @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="tax_id" class="form-label">Pajak</label>
                                <select class="form-select @error('tax_id') is-invalid @enderror" id="tax_id"
                                        name="tax_id">
                                    <option value="">Pilih Pajak</option>
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

                            <div class="mb-3">
                                <label for="discount_id" class="form-label">Diskon</label>
                                <select class="form-select @error('discount_id') is-invalid @enderror" id="discount_id"
                                        name="discount_id">
                                    <option value="">Pilih Diskon</option>
                                    @foreach ($discounts as $discount)
                                        <option value="{{ $discount->id }}"
                                            {{ old('discount_id', $product->discount_id) == $discount->id ? 'selected' : '' }}>
                                            {{ $discount->name }}
                                            ({{ $discount->type == 'percentage' ? $discount->value . '%' : 'Rp ' . number_format($discount->value, 0) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('discount_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                           value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Aktif</label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Perbarui Produk</button>
                                <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
