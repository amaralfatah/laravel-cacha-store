@extends('layouts.app')

@section('content')
    <x-section-header title="Edit Product: {{ $product->name }}"/>

    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                                    <option value="{{ $store->id }}"
                                        {{ old('store_id', $product->store_id) == $store->id ? 'selected' : '' }}>
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
                               id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="code" class="form-label">Product Code</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                               id="code" name="code" value="{{ old('code', $product->code) }}" required>
                        @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="barcode" class="form-label">Barcode</label>
                        <input type="text" class="form-control @error('barcode') is-invalid @enderror"
                               id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}" required>
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
                               id="short_description" name="short_description"
                               value="{{ old('short_description', $product->short_description) }}">
                        @error('short_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">Full Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
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
                <!-- Current Images -->
                @if($product->images->count() > 0)
                    <div class="row g-3 mb-4">
                        @foreach($product->images as $image)
                            <div class="col-6 col-md-3">
                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                         class="img-thumbnail w-100"
                                         alt="{{ $image->alt_text }}">
                                    @if($image->is_primary)
                                        <span class="badge bg-primary position-absolute top-0 end-0 m-2">Primary</span>
                                    @endif
                                    <button type="button" class="btn btn-danger btn-sm position-absolute bottom-0 end-0 m-2"
                                            onclick="deleteImage({{ $image->id }})">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Upload New Images -->
                <div class="mb-3">
                    <label for="images" class="form-label">Upload New Images</label>
                    <input type="file" class="form-control @error('images.*') is-invalid @enderror"
                           id="images" name="images[]" multiple accept="image/*">
                    <div class="form-text">You can select multiple images. First new image will be set as primary if no primary image exists.</div>
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
                        <label for="purchase_price" class="form-label">Purchase Price</label>
                        <input type="number" step="0.01" class="form-control @error('purchase_price') is-invalid @enderror"
                               id="purchase_price" name="purchase_price"
                               value="{{ old('purchase_price', $defaultUnit ? $defaultUnit->purchase_price : 0) }}" required>
                        @error('purchase_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="selling_price" class="form-label">Selling Price</label>
                        <input type="number" step="0.01" class="form-control @error('selling_price') is-invalid @enderror"
                               id="selling_price" name="selling_price"
                               value="{{ old('selling_price', $defaultUnit ? $defaultUnit->selling_price : 0) }}" required>
                        @error('selling_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" step="1" class="form-control @error('stock') is-invalid @enderror"
                               id="stock" name="stock"
                               value="{{ old('stock', $defaultUnit ? $defaultUnit->stock : 0) }}" required>
                        @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="tax_id" class="form-label">Tax</label>
                        <select class="form-select @error('tax_id') is-invalid @enderror"
                                id="tax_id" name="tax_id">
                            <option value="">Select Tax</option>
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

                    <div class="col-12 col-md-4">
                        <label for="discount_id" class="form-label">Discount</label>
                        <select class="form-select @error('discount_id') is-invalid @enderror"
                                id="discount_id" name="discount_id">
                            <option value="">Select Discount</option>
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
                </div>
            </div>
        </div>

        <!-- Link Card -->
        <div class="card border mb-4">
            <div class="card-header bg-transparent">
                <h6 class="card-title mb-0">
                    <i class='bx bx-link me-2 text-primary'></i>
                    Product Link
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="url" class="form-label">Link Shopee, Tokopedia, dll</label>
                        <input type="text" class="form-control @error('url') is-invalid @enderror"
                               id="url" name="url" value="{{ old('url', $product->url) }}">
                        @error('url')
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
                                   value="1" {{ old('featured', $product->featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">Show on Landing Page</label>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                   value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
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
                Update Product
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            function deleteImage(imageId) {
                if (confirm('Are you sure you want to delete this image?')) {
                    // Implement image deletion logic
                    axios.delete(`/products/images/${imageId}`)
                        .then(response => {
                            window.location.reload();
                        })
                        .catch(error => {
                            alert('Error deleting image');
                        });
                }
            }
        </script>
    @endpush
@endsection
