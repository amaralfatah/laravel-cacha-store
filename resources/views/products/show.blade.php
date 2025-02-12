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

                                <dt>Purchase Price</dt>
                                <dd>
                                    @php
                                        $defaultUnit = $product->productUnits->where('is_default', true)->first();
                                        $purchasePrice = $defaultUnit ? $defaultUnit->purchase_price : 0;
                                    @endphp
                                    Rp {{ number_format($purchasePrice, 2) }}
                                </dd>

                                <dt>Selling Price</dt>
                                <dd>
                                    @php
                                        $sellingPrice = $defaultUnit ? $defaultUnit->selling_price : 0;
                                    @endphp
                                    Rp {{ number_format($sellingPrice, 2) }}
                                </dd>

                                <dt>Stock</dt>
                                <dd>
                                    @php
                                        $stock = $defaultUnit ? $defaultUnit->stock : 0;
                                    @endphp
                                    {{ number_format($stock) }}
                                </dd>

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

            <!-- Unit Unit -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Unit Prices</h4>
                    <a href="{{ route('products.units.create', $product) }}" class="btn btn-primary btn-sm">Add Unit</a>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Unit</th>
                            <th>Conversion</th>
                            <th>Purchase Price</th>
                            <th>Selling Price</th>
                            <th>Stock</th>
                            <th>Default</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($product->productUnits as $unit)
                            <tr>
                                <td>{{ $unit->unit->name }}</td>
                                <td>1 = {{ $unit->conversion_factor }}</td>
                                <td>Rp {{ number_format($unit->purchase_price, 2) }}</td>
                                <td>Rp {{ number_format($unit->selling_price, 2) }}</td>
                                <td>

                                    {{ number_format($unit->stock) }}
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
