<!-- resources/views/products/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3>Create New Product</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="barcode" class="form-label">Barcode</label>
                                <input type="text" class="form-control @error('barcode') is-invalid @enderror"
                                    id="barcode" name="barcode" value="{{ old('barcode') }}">
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id"
                                    name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="default_unit_id" class="form-label">Default Unit</label>
                                <select name="default_unit_id"
                                        class="form-select @error('default_unit_id') is-invalid @enderror">
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="purchase_price" class="form-label">Purchase Price</label>
                                <input type="number" step="0.01"
                                       class="form-control @error('purchase_price') is-invalid @enderror"
                                       id="purchase_price" name="purchase_price"
                                       value="{{ old('purchase_price') }}">
                                @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="selling_price" class="form-label">Selling Price</label>
                                <input type="number" step="0.01"
                                       class="form-control @error('selling_price') is-invalid @enderror"
                                       id="selling_price" name="selling_price"
                                       value="{{ old('selling_price') }}">
                                @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="stock" class="form-label">Initial Stock</label>
                                <input type="number" step="1"
                                       class="form-control @error('stock') is-invalid @enderror"
                                       id="stock" name="stock"
                                       value="{{ old('stock', 0) }}">
                                @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                        value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Create Product</button>
                                <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
