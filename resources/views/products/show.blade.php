<!-- resources/views/products/show.blade.php -->
@extends('layouts.app')

@section('content')

        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Product Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl>
                                    <dt>Name</dt>
                                    <dd>{{ $product->name }}</dd>

                                    <dt>Category</dt>
                                    <dd>{{ $product->category->name }}</dd>

                                    <dt>Base Price</dt>
                                    <dd>Rp {{ number_format($product->base_price, 2) }}</dd>

                                    <dt>Status</dt>
                                    <dd>
                                        <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                            <div class="col-md-6 text-center">
                                <dt>Barcode</dt>
                                <dd>
                                    @if ($product->barcode_image && Storage::disk('public')->exists($product->barcode_image))
                                        <img src="{{ Storage::url($product->barcode_image) }}" alt="Product Barcode"
                                            class="img-fluid">
                                        <div>{{ $product->barcode }}</div>
                                        <a href="{{ Storage::url($product->barcode_image) }}"
                                            download="{{ $product->barcode }}.png" class="btn btn-sm btn-secondary mt-2">
                                            Download Barcode
                                        </a>
                                    @else
                                        <div class="text-muted">No barcode image available</div>
                                    @endif
                                </dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Update Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4>Update Base Price</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.update-price', $product) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="base_price" class="form-label">New Base Price</label>
                                <input type="number" step="0.01"
                                    class="form-control @error('base_price') is-invalid @enderror" id="base_price"
                                    name="base_price" value="{{ old('base_price', $product->base_price) }}">
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="adjust_unit_prices"
                                        name="adjust_unit_prices" value="1">
                                    <label class="form-check-label" for="adjust_unit_prices">
                                        Adjust all unit prices proportionally
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Price</button>
                        </form>
                    </div>
                </div>

                <!-- Unit Prices -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Unit Prices</h4>
                        <a href="{{ route('products.units.create', $product) }}" class="btn btn-primary btn-sm">Add
                            Unit</a>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Unit</th>
                                    <th>Conversion</th>
                                    <th>Price</th>
                                    <th>Discount</th>
                                    <th>Default</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($product->productUnits as $unit)
                                    <tr>
                                        <td>{{ $unit->unit->name }}</td>
                                        <td>1 = {{ $unit->conversion_factor }}</td>
                                        <td>Rp {{ number_format($unit->price, 2) }}</td>
                                        <td>
                                            @if ($unit->conversion_factor > 1)
                                                Diskon: {{ $unit->discount_percentage }}%
                                                (Hemat Rp
                                                {{ number_format($unit->product->base_price * $unit->conversion_factor - $unit->price) }})
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $unit->is_default ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $unit->is_default ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('products.units.edit', [$product, $unit]) }}"
                                                class="btn btn-sm btn-primary">Edit</a>
                                            @if (!$unit->is_default)
                                                <form action="{{ route('products.units.destroy', [$product, $unit]) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure?')">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

@endsection
