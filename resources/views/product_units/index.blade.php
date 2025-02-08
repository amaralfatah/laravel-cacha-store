<!-- resources/views/product_units/index.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="row mb-3">
        <div class="col">
            <h2>Unit Conversions for {{ $product->name }}</h2>
        </div>
        <div class="col text-end">
            <a href="{{ route('products.units.create', $product) }}" class="btn btn-primary">Add Unit Conversion</a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Unit</th>
                        <th>Conversion Factor</th>
                        <th>Price</th>
                        <th>Default</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productUnits as $productUnit)
                        <tr>
                            <td>{{ $productUnit->unit->name }} ({{ $productUnit->unit->code }})</td>
                            <td>{{ $productUnit->conversion_factor }}</td>
                            <td>{{ number_format($productUnit->price, 2) }}</td>
                            <td>
                                <span class="badge {{ $productUnit->is_default ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $productUnit->is_default ? 'Yes' : 'No' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('products.units.edit', [$product, $productUnit]) }}"
                                    class="btn btn-sm btn-primary">Edit</a>
                                @if (!$productUnit->is_default)
                                    <form action="{{ route('products.units.destroy', [$product, $productUnit]) }}"
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
@endsection
