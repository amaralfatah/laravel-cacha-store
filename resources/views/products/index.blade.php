@extends('layouts.app')

@section('content')
    <x-section-header title="Data Produk">
        <x-slot:actions>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Tambah Produk
            </a>
        </x-slot:actions>
    </x-section-header>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header d-flex flex-row justify-content-between">
            <h4 class="card-title mb-0 ">
                Filter Produk
            </h4>
{{--            <button id="reset-filter" class="btn btn-secondary">--}}
{{--                <i class="bi bi-arrow-clockwise me-1"></i>Reset Filter--}}
{{--            </button>--}}
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- First row of filters - 5 columns -->
                <div class="col-md-3">
                    <select id="group-filter" class="form-select">
                        <option value="">Semua Grup</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="category-filter" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" data-group="{{ $category->group_id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="stock-filter" class="form-select">
                        <option value="">Semua Stok</option>
                        <option value="low">Stok Menipis</option>
                        <option value="out">Stok Habis</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="status-filter" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
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
                        <th>Grup</th>
                        <th>Kategori</th>
                        <th>Barcode</th>
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
                <br>
                <strong>Pencarian:</strong> Anda dapat mencari produk berdasarkan nama, kode, atau barcode di kolom pencarian.
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
        .dataTables_filter input {
            min-width: 250px;
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
                        d.category_id = $('#category-filter').val(); // Tambahkan ini
                        d.is_active = $('#status-filter').val();
                        d.stock_status = $('#stock-filter').val();
                    }
                },
                columns: [
                    {
                        data: 'code',
                        name: 'code',
                        width: '10%',
                        render: function(data) {
                            return '<span class="fw-semibold">' + data + '</span>';
                        }
                    },
                    {
                        data: 'name_link',
                        name: 'name',
                        width: '20%'
                    },
                        @if(auth()->user()->role === 'admin')
                    {
                        data: 'store_name',
                        name: 'store_name',
                        width: '10%',
                        render: function(data) {
                            return '<span class="badge bg-light text-dark">' + data + '</span>';
                        }
                    },
                        @endif
                    {
                        data: 'group_name',
                        name: 'category.group.name',
                        width: '10%'
                    },
                    {
                        data: 'category_name',
                        name: 'category.name',
                        width: '10%'
                    },
                    {
                        data: 'barcode',
                        name: 'barcode',
                        width: '10%',
                        render: function(data) {
                            if (!data) return '<span class="text-muted">-</span>';
                            return '<code>' + data + '</code>';
                        }
                    },
                    {
                        data: 'stock_info',
                        name: 'stock_info',
                        width: '15%'
                    },
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

            // Update event listener untuk filter
            $('#group-filter, #category-filter, #status-filter, #stock-filter').change(function() {
                table.draw();
            });

            // Filter kategori berdasarkan grup yang dipilih
            $('#group-filter').change(function() {
                const groupId = $(this).val();
                const categorySelect = $('#category-filter');

                categorySelect.find('option').show();
                if (groupId) {
                    categorySelect.find('option:not([data-group="' + groupId + '"])').hide();
                    categorySelect.find('option[value=""]').show();
                }
                categorySelect.val('');
            });

            // Reset filter
            $('#reset-filter').click(function() {
                $('#group-filter, #category-filter, #status-filter, #stock-filter').val('');
                table.search('').columns().search('');
                $('#category-filter option').show();
                table.draw();
            });

            // Tooltip untuk cell yang terpotong
            $('#products-table').on('mouseenter', 'td', function() {
                if(this.offsetWidth < this.scrollWidth && !$(this).find('a.product-link').length) {
                    $(this).attr('title', $(this).text());
                }
            });

            // Auto focus pada search input setelah page load
            setTimeout(function() {
                $('.dataTables_filter input[type="search"]').focus();
            }, 500);
        });
    </script>
@endpush
