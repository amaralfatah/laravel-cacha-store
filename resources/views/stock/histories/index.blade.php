@extends('layouts.app')

@section('content')
    {{-- Header section remains the same --}}
    <x-section-header title="Riwayat Stok">
        <x-slot:actions>
            <a href="{{ route('stock.adjustments.create') }}" class="btn btn-primary me-2">New Adjustment</a>
            <a href="{{ route('stock-takes.create') }}" class="btn btn-info">New Stock Opname</a>
        </x-slot:actions>
    </x-section-header>

    <!-- Overview Cards - remains the same -->
    <div class="row mb-4">
        <!-- ... overview cards code ... -->
    </div>

    <!-- Alert for Low Stock - remains the same -->
    @if($lowStockProducts->isNotEmpty())
        <!-- ... low stock alert code ... -->
    @endif

    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Product</label>
                    <select id="product-filter" class="form-select">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select id="category-filter" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select id="type-filter" class="form-select">
                        <option value="">All Types</option>
                        <option value="in">Stock In</option>
                        <option value="out">Stock Out</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Start Date</label>
                    <input type="date" id="start-date" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label">End Date</label>
                    <input type="date" id="end-date" class="form-control">
                </div>

                <div class="col-md-1">
                    <label class="form-label d-none d-md-block">&nbsp;</label>
                    <button id="reset-filter" class="btn btn-secondary">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock History Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="stock-history-table">
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
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = $('#stock-history-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('stock.histories.index') }}",
                    data: function(d) {
                        d.product_id = $('#product-filter').val();
                        d.category_id = $('#category-filter').val();
                        d.type = $('#type-filter').val();
                        d.start_date = $('#start-date').val();
                        d.end_date = $('#end-date').val();
                    }
                },
                columns: [
                    {
                        data: 'date',
                        name: 'created_at',
                        width: '120px'
                    },
                    {
                        data: 'product_name',
                        name: 'productUnit.product.name',
                        width: '200px'
                    },
                    {
                        data: 'category_name',
                        name: 'productUnit.product.category.name',
                        width: '150px'
                    },
                    {
                        data: 'unit_name',
                        name: 'productUnit.unit.name',
                        width: '100px'
                    },
                    {
                        data: 'type_badge',
                        name: 'type',
                        width: '100px'
                    },
                    {
                        data: 'quantity_formatted',
                        name: 'quantity',
                        width: '100px',
                        className: 'text-end'
                    },
                    {
                        data: 'remaining_formatted',
                        name: 'remaining_stock',
                        width: '100px',
                        className: 'text-end'
                    },
                    {
                        data: 'source',
                        name: 'reference_type',
                        width: '120px'
                    },
                    {
                        data: 'notes',
                        name: 'notes',
                        width: '200px'
                    }
                ],
                order: [[0, 'desc']],
            });

            // Event listener untuk filter
            $('#product-filter, #category-filter, #type-filter').change(function() {
                table.draw();
            });

            // Event listener untuk date filters
            $('#start-date, #end-date').change(function() {
                let startDate = $('#start-date').val();
                let endDate = $('#end-date').val();

                if (startDate && endDate && startDate > endDate) {
                    alert('Tanggal akhir harus setelah tanggal awal');
                    $(this).val('');
                    return;
                }

                table.draw();
            });

            // Reset filter
            $('#reset-filter').click(function() {
                $('#product-filter, #category-filter, #type-filter').val('');
                $('#start-date, #end-date').val('');
                table.draw();
            });

            // Tooltip untuk text yang terpotong
            $('#stock-history-table').on('mouseenter', 'td', function() {
                if(this.offsetWidth < this.scrollWidth) {
                    $(this).attr('title', $(this).text());
                }
            });
        });
    </script>
@endpush
