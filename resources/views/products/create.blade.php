@extends('layouts.app')

@section('content')
    <x-section-header title="Create New Product"/>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Basic Information Card -->
        <div class="card border mb-4">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class='bx bx-info-circle me-2 text-primary'></i>
                    Basic Information
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @if(auth()->user()->role === 'admin')
                        <div class="col-12">
                            <label for="store_id" class="form-label">Store</label>
                            <select class="form-select @error('store_id') is-invalid @enderror"
                                    id="store_id" name="store_id" required>
                                <option value="">Select Store</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                        {{ $store->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('store_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="code" class="form-label">Product Code</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                               id="code" name="code" value="{{ old('code') }}" required>
                        @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="barcode" class="form-label">Barcode</label>
                        <input type="text" class="form-control @error('barcode') is-invalid @enderror"
                               id="barcode" name="barcode" value="{{ old('barcode') }}" required>
                        @error('barcode')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Card -->
        <div class="card border mb-4">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class='bx bx-text me-2 text-primary'></i>
                    Product Description
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="short_description" class="form-label">Short Description</label>
                        <input type="text" class="form-control @error('short_description') is-invalid @enderror"
                               id="short_description" name="short_description" value="{{ old('short_description') }}">
                        @error('short_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Full Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Images Card -->
        <div class="card border mb-4">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class='bx bx-image me-2 text-primary'></i>
                    Product Images
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="images" class="form-label">Upload Images</label>
                    <input type="file" class="form-control @error('images.*') is-invalid @enderror"
                           id="images" name="images[]" multiple accept="image/*">
                    <div class="form-text">You can select multiple images. First image will be set as primary.</div>
                    @error('images.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Pricing & Stock Card -->
        <div class="card border mb-4">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class='bx bx-purchase-tag me-2 text-primary'></i>
                    Pricing & Stock
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="default_unit_id" class="form-label">Default Unit</label>
                        <select name="default_unit_id" class="form-select @error('default_unit_id') is-invalid @enderror" required>
                            <option value="">Select Unit</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('default_unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('default_unit_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="stock" class="form-label">Initial Stock</label>
                        <input type="number" step="1" class="form-control @error('stock') is-invalid @enderror"
                               id="stock" name="stock" value="{{ old('stock', 0) }}" required>
                        @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="purchase_price" class="form-label">Purchase Price</label>
                        <input type="number" step="0.01" class="form-control @error('purchase_price') is-invalid @enderror"
                               id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" required>
                        @error('purchase_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="selling_price" class="form-label">Selling Price</label>
                        <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror"
                               id="selling_price" name="selling_price" value="{{ old('selling_price') }}" required>
                        @error('selling_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Card -->
        <div class="card border mb-4">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class='bx bx-toggle-left me-2 text-primary'></i>
                    Product Status
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="featured" name="featured"
                                   value="1" {{ old('featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">Show on Landing Page</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                   value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="text-end">
            <a href="{{ route('products.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class='bx bx-save me-1'></i>
                Create Product
            </button>
        </div>
    </form>

@endsection
