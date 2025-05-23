@extends('layouts.app')

@section('title', 'Laporan Inventaris')

@section('content')
    <x-section-header title="Laporan Inventaris">
        <x-slot:actions>
            <form id="filter-form" class="d-flex gap-3">
                <div class="input-group">
                    <span class="input-group-text">Kategori</span>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <span class="input-group-text">Supplier</span>
                    <select class="form-select" id="supplier_id" name="supplier_id">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <span class="input-group-text">Status</span>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="in_stock">Stok Tersedia</option>
                        <option value="low_stock">Stok Menipis</option>
                        <option value="out_of_stock">Stok Habis</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class='bx bx-filter-alt'></i>
                    <span>Filter</span>
                </button>
            </form>
        </x-slot:actions>
    </x-section-header>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <x-card-status
            title="Total Produk"
            subtitle="Jumlah produk aktif"
            icon="bx-package"
            iconColor="success"
            format="normal"
            columnSize="col-md-3"
            :value="0" />

        <x-card-status
            title="Produk Stok Tersedia"
            subtitle="Produk stok tersedia"
            icon="bx-check-circle"
            iconColor="info"
            format="normal"
            columnSize="col-md-3"
            :value="0" />

        <x-card-status
            title="Produk Stok Menipis"
            subtitle="Produk perlu diisi ulang"
            icon="bx-error"
            iconColor="warning"
            format="normal"
            columnSize="col-md-3"
            :value="0" />

        <x-card-status
            title="Produk Stok Habis"
            subtitle="Produk tidak tersedia"
            icon="bx-x-circle"
            iconColor="danger"
            format="normal"
            columnSize="col-md-3"
            :value="0" />
    </div>

    <!-- Inventory Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center p-4">
            <h5 class="card-title mb-0">Daftar Stok Produk</h5>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2" id="btnExport">
                    <i class='bx bx-export'></i>
                    <span>Export Excel</span>
                </button>
                <a href="{{ route('stock-takes.create') }}" class="btn btn-success d-flex align-items-center gap-2">
                    <i class='bx bx-list-check'></i>
                    <span>Stock Opname Baru</span>
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="inventory-table" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th>Status Stok</th>
                        <th>Stok Tersedia</th>
                        <th>Stok Minimal</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Updated script for correctly targeting card titles
        $(function () {
            // Initialize DataTable
            const inventoryTable = $('#inventory-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('reports.inventory') }}",
                    data: function (d) {
                        d.category_id = $('#category_id').val();
                        d.supplier_id = $('#supplier_id').val();
                        d.status = $('#status').val();
                    },
                    dataSrc: function(json) {
                        // Update summary cards when data is loaded
                        updateSummaryFromResponse(json.summary);
                        return json.data;
                    }
                },
                columns: [
                    {data: 'code', name: 'code'},
                    {data: 'name', name: 'name'},
                    {data: 'category_name', name: 'category_name'},
                    {data: 'supplier_name', name: 'supplier_name'},
                    {data: 'stock_status', name: 'stock_status'},
                    {data: 'current_stock', name: 'current_stock'},
                    {
                        data: null,
                        render: function(data, type, row) {
                            const defaultUnit = row.product_units.find(unit => unit.is_default);
                            if (!defaultUnit) return 'N/A';
                            return defaultUnit.min_stock + ' ' + (defaultUnit.unit ? defaultUnit.unit.name : '');
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (data, type, row) {
                            return `
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('products.edit', '') }}/${row.id}">
                                    <i class="bx bx-edit-alt me-1"></i> Edit Produk
                                </a>
                                <a class="dropdown-item" href="{{ route('stock.adjustments.create') }}?product_id=${row.id}">
                                    <i class="bx bx-plus-circle me-1"></i> Tambah Stok
                                </a>
                                <a class="dropdown-item" href="{{ route('stock.histories.index') }}?product_id=${row.id}">
                                    <i class="bx bx-history me-1"></i> Riwayat Stok
                                </a>
                            </div>
                        </div>
                    `;
                        }
                    }
                ]
            });

            // Handle filter form submit
            $('#filter-form').on('submit', function (e) {
                e.preventDefault();
                inventoryTable.ajax.reload();
            });

            // Update summary from server response
            function updateSummaryFromResponse(summary) {
                if (summary) {
                    // Based on the card-status.blade.php component structure,
                    // we need to target the h4.card-title elements inside each card
                    const cards = document.querySelectorAll('.row.mb-4 .card');

                    if (cards.length >= 4) {
                        // Target each h4.card-title element within each card
                        const totalProductsTitle = cards[0].querySelector('h4.card-title');
                        const inStockTitle = cards[1].querySelector('h4.card-title');
                        const lowStockTitle = cards[2].querySelector('h4.card-title');
                        const outOfStockTitle = cards[3].querySelector('h4.card-title');

                        // Update values with appropriate formatting
                        if (totalProductsTitle) {
                            totalProductsTitle.textContent = formatNumber(summary.totalProducts);
                        }

                        if (inStockTitle) {
                            inStockTitle.textContent = formatNumber(summary.inStockCount);
                        }

                        if (lowStockTitle) {
                            lowStockTitle.textContent = formatNumber(summary.lowStockCount);
                        }

                        if (outOfStockTitle) {
                            outOfStockTitle.textContent = formatNumber(summary.outOfStockCount);
                        }
                    } else {
                        console.error('Could not find all required card elements');
                    }
                }
            }

            // Helper function to format numbers with thousands separators
            function formatNumber(value) {
                return new Intl.NumberFormat('id-ID').format(value);
            }

            // Handle export button
            $('#btnExport').on('click', function() {
                const params = new URLSearchParams({
                    category_id: $('#category_id').val(),
                    supplier_id: $('#supplier_id').val(),
                    status: $('#status').val(),
                    export: 'excel'
                }).toString();

                window.location.href = `{{ route('reports.inventory') }}?${params}`;
            });
        });
    </script>
@endpush
