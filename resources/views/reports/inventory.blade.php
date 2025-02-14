@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Inventory Report</h5>
                    <a href="{{ route('reports.export-inventory') }}" class="btn btn-secondary">Export</a>
                </div>
                <div class="card-body">
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Products</h6>
                                    <h4 class="mb-0">{{ $products->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Low Stock Items</h6>
                                    <h4 class="mb-0">{{ $products->where('low_stock', true)->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">In Stock Items</h6>
                                    <h4 class="mb-0">{{ $products->where('total_stock', '>', 0)->count() }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Out of Stock</h6>
                                    <h4 class="mb-0">{{ $products->where('total_stock', 0)->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inventory Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Code</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Total Stock</th>
                                <th>Units</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>{{ $product['code'] }}</td>
                                    <td>{{ $product['name'] }}</td>
                                    <td>{{ $product['category'] }}</td>
                                    <td>{{ $product['total_stock'] }}</td>
                                    <td>
                                        @foreach($product['units'] as $unit)
                                            <div class="mb-1">
                                                {{ $unit->unit->name }}: {{ $unit->stock }}
                                                @if($unit->stock <= $unit->min_stock)
                                                    <span class="badge bg-danger">Low Stock</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($product['total_stock'] <= 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($product['low_stock'])
                                            <span class="badge bg-warning">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">In Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No products found</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
