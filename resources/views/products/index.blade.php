@extends('layouts.app')

@section('content')
    <x-section-header
        title="Data Produk"
        :route="route('products.create')"
        buttonText="Tambah Produk"
        icon="bx-plus"
    />

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Grup</label>
                    <select id="group-filter" class="form-select">
                        <option value="">Semua Grup</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kategori</label>
                    <select id="category-filter" class="form-select">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Supplier</label>
                    <select id="supplier-filter" class="form-select">
                        <option value="">Semua Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select id="status-filter" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status Stok</label>
                    <select id="stock-filter" class="form-select">
                        <option value="">Semua</option>
                        <option value="low">Stok Menipis</option>
                        <option value="out">Stok Habis</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button id="reset-filter" class="btn btn-secondary w-100">
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="products-table">
                    <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Grup</th>
                        <th>Kategori</th>
                        <th>Supplier</th>
                        <th>Satuan</th>
                        <th>Stok</th>
                        <th>Harga Beli</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
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
        document.addEventListener('DOMContentLoaded', function() {
            const table = $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ route('products.index') }}",
                    data: function(d) {
                        d.group_id = $('#group-filter').val();
                        d.category_id = $('#category-filter').val();
                        d.supplier_id = $('#supplier-filter').val();
                        d.is_active = $('#status-filter').val();
                        d.stock_status = $('#stock-filter').val();
                    }
                },
                columns: [
                    { data: 'code', name: 'code' },
                    { data: 'name', name: 'name' },
                    { data: 'group_info', name: 'category.group.name' },
                    { data: 'category_name', name: 'category.name' },
                    { data: 'supplier_name', name: 'supplier.name' },
                    { data: 'unit_info', name: 'unit_info' },
                    { data: 'stock_info', name: 'stock_info' },
                    {
                        data: 'purchase_price',
                        name: 'purchase_price',
                        width: '120px',
                        className: 'text-end'
                    },
                    {
                        data: 'selling_price',
                        name: 'selling_price',
                        width: '120px',
                        className: 'text-end'
                    },
                    { data: 'status', name: 'is_active' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [[0, 'desc']]
            });

            // Event listener untuk filter
            $('#group-filter, #category-filter, #supplier-filter, #status-filter, #stock-filter')
                .change(function() {
                    table.draw();
                });

            // Update kategori berdasarkan grup yang dipilih
            $('#group-filter').change(function() {
                const groupId = $(this).val();
                const categorySelect = $('#category-filter');

                categorySelect.html('<option value="">Semua Kategori</option>');

                if (groupId) {
                    $.get(`/api/groups/${groupId}/categories`, function(categories) {
                        categories.forEach(function(category) {
                            categorySelect.append(
                                `<option value="${category.id}">${category.name}</option>`
                            );
                        });
                    });
                } else {
                    // Jika tidak ada grup dipilih, tampilkan semua kategori
                    @foreach($categories as $category)
                    categorySelect.append(
                        `<option value="{{ $category->id }}">{{ $category->name }}</option>`
                    );
                    @endforeach
                }
            });

            // Reset filter
            $('#reset-filter').click(function() {
                // Reset semua filter ke nilai default
                $('#group-filter').val('').trigger('change');
                $('#category-filter').val('');
                $('#supplier-filter').val('');
                $('#status-filter').val('');
                $('#stock-filter').val('');

                // Reset pencarian DataTable
                table.search('').columns().search('');

                // Redraw table
                table.draw();
            });

            // Tambahkan tooltip untuk cell yang terpotong
            $('#products-table').on('mouseenter', 'td', function() {
                if(this.offsetWidth < this.scrollWidth) {
                    $(this).attr('title', $(this).text());
                }
            });
        });
    </script>
@endpush
