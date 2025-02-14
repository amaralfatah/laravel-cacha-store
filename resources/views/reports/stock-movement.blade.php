@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Stock Movement Report</h5>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('reports.stock-movement') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date"
                                           value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date"
                                           value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="product_id" class="form-label">Product</label>
                                    <select class="form-select" id="product_id" name="product_id">
                                        <option value="">All Products</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Stock Movements Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Unit</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Remaining Stock</th>
                                <th>Reference</th>
                                <th>Notes</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($movements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $movement->productUnit->product->name }}</td>
                                    <td>{{ $movement->productUnit->unit->name }}</td>
                                    <td>
                                            <span
                                                class="badge bg-{{ $movement->type === 'in' ? 'success' : ($movement->type === 'out' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($movement->type) }}
                                            </span>
                                    </td>
                                    <td>{{ $movement->quantity }}</td>
                                    <td>{{ $movement->remaining_stock }}</td>
                                    <td>{{ $movement->reference_type }}</td>
                                    <td>{{ $movement->notes }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No stock movements found</td>
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
