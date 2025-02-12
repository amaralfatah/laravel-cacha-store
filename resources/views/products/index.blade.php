<!-- resources/views/products/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="row mb-3">
        <div class="col">
            <h2>Products</h2>
        </div>
        <div class="col text-end">
            <a href="{{ route('products.create') }}" class="btn btn-primary">Add New Product</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Barcode</th>
                        <th>Category</th>
                        <th>Purchase Price</th>
                        <th>Selling Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->barcode }}</td>
                            <td>{{ $product->category->name }}</td>
                            <td>
                                @php
                                    $defaultUnit = $product->productUnits->first();
                                    $purchasePrice = $defaultUnit ? $defaultUnit->purchase_price : 0;
                                @endphp
                                {{ number_format($purchasePrice, 2) }}
                            </td>
                            <td>
                                @php
                                    $sellingPrice = $defaultUnit ? $defaultUnit->selling_price : 0;
                                @endphp
                                {{ number_format($sellingPrice, 2) }}
                            </td>
                            <td>
                                <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-info">View</a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-warning">Edit</a>
                                <a href="{{ route('products.units.index', $product) }}"
                                    class="btn btn-sm btn-secondary">Units</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure?')">Delete</button>
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
