<!-- resources/views/stock/histories/index.blade.php -->
@extends('layouts.app')

@section('content')

    {{-- With custom actions --}}
    <x-section-header title="Riwayat Stok">
        <x-slot:actions>
            <a href="{{ route('stock.adjustments.create') }}" class="btn btn-primary me-2">New Adjustment</a>
            <a href="{{ route('stock-takes.create') }}" class="btn btn-info">New Stock Opname</a>
        </x-slot:actions>
    </x-section-header>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Products</h6>
                    <h2 class="card-title mb-0">{{ number_format($overview['total_products']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Low Stock Items</h6>
                    <h2 class="card-title mb-0 text-warning">{{ number_format($overview['low_stock_count']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Out of Stock</h6>
                    <h2 class="card-title mb-0 text-danger">{{ number_format($overview['out_of_stock_count']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Today's Movements</h6>
                    <div class="d-flex justify-content-between">
                        <small class="text-success">In: {{ $overview['today_movements']['in'] ?? 0 }}</small>
                        <small class="text-danger">Out: {{ $overview['today_movements']['out'] ?? 0 }}</small>
                        <small class="text-warning">Adj: {{ $overview['today_movements']['adjustment'] ?? 0 }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert for Low Stock -->
    @if($lowStockProducts->isNotEmpty())
        <div class="alert alert-warning mb-4">
            <h6 class="mb-2">Low Stock Alert</h6>
            <ul class="list-unstyled mb-0">
                @foreach($lowStockProducts as $product)
                    <li>
                        {{ $product->product->name }} ({{ $product->unit->name }}):
                        <strong>{{ number_format($product->stock, 2) }}</strong>
                        <a href="{{ route('stock.adjustments.create', ['product_unit_id' => $product->id]) }}"
                           class="btn btn-sm btn-outline-primary float-end">
                            Add Stock
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('stock.histories.index') }}" method="GET" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product</label>
                            <select name="product_id" id="product_id" class="form-control">
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
                            <label for="category_id" class="form-label">Category</label>
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="">All Types</option>
                                <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Stock In</option>
                                <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Stock Out</option>
                                <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>
                                    Adjustment
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="date"
                                   name="start_date"
                                   id="start_date"
                                   class="form-control"
                                   value="{{ request('start_date') }}">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="date"
                                   name="end_date"
                                   id="end_date"
                                   class="form-control"
                                   value="{{ request('end_date') }}">
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="mb-3">
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stock History Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Sumber</th>
                        <th>Catatan</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($histories as $history)
                        <tr>
                            <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $history->productUnit->product->name }}</td>
                            <td>{{ $history->productUnit->product->category->name }}</td>
                            <td>{{ $history->productUnit->unit->name }}</td>
                            <td>
                                    <span
                                        class="badge bg-{{ $history->type === 'in' ? 'success' : ($history->type === 'out' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($history->type) }}
                                    </span>
                            </td>
                            <td>{{ number_format($history->quantity, 2) }}</td>
                            <td>{{ number_format($history->remaining_stock, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $history->reference_type)) }}</td>
                            <td>{{ $history->notes }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $histories->links() }}
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function () {
                // Auto-submit form when filters change
                $('#product_id, #category_id, #type').on('change', function () {
                    $('#filterForm').submit();
                });

                // Validate date range
                $('#end_date').on('change', function () {
                    let startDate = $('#start_date').val();
                    let endDate = $(this).val();

                    if (startDate && endDate && startDate > endDate) {
                        alert('End date must be after start date');
                        $(this).val('');
                    }
                });
            });
        </script>
    @endpush
@endsection
