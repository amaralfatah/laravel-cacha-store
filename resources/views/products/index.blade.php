@extends('layouts.app')

@section('content')
    <x-section-header title="Data Produk">
        <x-slot:actions>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Tambah Produk
            </a>
        </x-slot:actions>
    </x-section-header>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Grup & Kategori</label>
                    <select id="group-filter" class="form-select">
                        <option value="">Semua Grup</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Stok</label>
                    <select id="stock-filter" class="form-select">
                        <option value="">Semua Stok</option>
                        <option value="low">Stok Menipis</option>
                        <option value="out">Stok Habis</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Produk</label>
                    <select id="status-filter" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button id="reset-filter" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise me-1"></i>Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="products-table">
                    <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Toko</th>
                        @endif
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Bantuan penggunaan -->
    <div class="alert alert-info mt-3">
        <div class="d-flex align-items-center">
            <i class="bx bx-info-circle fs-4 me-2"></i>
            <div>
                <strong>Tip:</strong> Klik pada nama produk untuk melihat detail lengkap dan mengatur unit, harga, dan gambar produk.
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .product-link {
            cursor: pointer;
            text-decoration: none !important;
        }
        .product-link:hover {
            text-decoration: underline !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Setup DataTable
            const table = $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('products.index') }}",
                    data: function(d) {
                        d.group_id = $('#group-filter').val();
                        d.is_active = $('#status-filter').val();
                        d.stock_status = $('#stock-filter').val();
                    }
                },
                columns: [
                    { data: 'code', name: 'code', width: '10%' },
                    { data: 'name_link', name: 'name', width: '20%' }, // Menggunakan name_link yang berisi HTML dengan link
                        @if(auth()->user()->role === 'admin')
                    { data: 'store_name', name: 'store_name', width: '10%' },
                        @endif
                    { data: 'category_name', name: 'category.name', width: '10%' },
                    { data: 'stock_info', name: 'stock_info', width: '15%' },
                    {
                        data: 'selling_price',
                        name: 'selling_price',
                        width: '10%',
                        className: 'text-end fw-bold'
                    },
                    {
                        data: 'status',
                        name: 'is_active',
                        width: '5%',
                        className: 'text-center'
                    }
                ],
                order: [[0, 'desc']],
                drawCallback: function() {
                    // Reinitialize tooltips for newly drawn rows
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl)
                    });
                }
            });

            // Event listener untuk filter
            $('#group-filter, #status-filter, #stock-filter').change(function() {
                table.draw();
            });

            // Reset filter
            $('#reset-filter').click(function() {
                $('#group-filter, #status-filter, #stock-filter').val('');
                table.search('').columns().search('');
                table.draw();
            });

            // Tooltip untuk cell yang terpotong
            $('#products-table').on('mouseenter', 'td', function() {
                if(this.offsetWidth < this.scrollWidth && !$(this).find('a.product-link').length) {
                    $(this).attr('title', $(this).text());
                }
            });
        });
    </script>
@endpush
