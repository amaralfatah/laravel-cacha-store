@extends('layouts.app')

@section('content')
    <h2 class="mb-4">New Stock Take</h2>

    <form action="{{ route('stock-takes.store') }}" method="POST" id="stockTakeForm">
        @csrf
        <!-- Header card remains the same -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date"
                                value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    @if (auth()->user()->role === 'admin')
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="store_id" class="form-label">Store</label>
                                <select class="form-select" id="store_id" name="store_id" required>
                                    <option value="">Select Store</option>
                                    @foreach ($stores as $store)
                                        <option value="{{ $store->id }}"
                                            {{ old('store_id') == $store->id ? 'selected' : '' }}>
                                            {{ $store->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products card -->
        <div class="card">
            <div class="card-body">
                <!-- Filter Section -->
                <div class="row mb-4 align-items-end">
                    <div class="col-md-4">
                        <label for="categoryFilter" class="form-label">Category</label>
                        <select id="categoryFilter" class="form-select">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="showZeroStock">
                            <label class="form-check-label" for="showZeroStock">
                                Show Zero Stock Only
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-outline-primary" id="scanBarcodeBtn">
                            <i class="bi bi-upc-scan"></i> Scan Barcode
                        </button>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="table-responsive">
                    <table class="table table-striped" id="products-table" width="100%">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Barcode</th>
                                <th>Kategori</th>
                                <th>Unit</th>
                                <th>Stok Saat Ini</th>
                                <th>Stok Aktual</th>
                                <th>Selisih</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary" id="submitBtn">Save Stock Take</button>
            <a href="{{ route('stock-takes.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <!-- Barcode Scanner Modal -->
    <div class="modal fade" id="scannerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Scan Barcode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="barcodeInput" class="form-control" placeholder="Scan or type barcode"
                        autofocus>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .actual-stock-input {
            width: 100px;
        }

        .difference-cell {
            width: 100px;
        }

        .difference-positive {
            color: green;
        }

        .difference-negative {
            color: red;
        }

        .dataTables_filter {
            margin-bottom: 1rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            let table = $('#products-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('stock-takes.products') }}',
                    data: function(d) {
                        d.category_id = $('#categoryFilter').val();
                        d.zero_stock = $('#showZeroStock').is(':checked');
                        @if (auth()->user()->role === 'admin')
                            d.store_id = $('#store_id').val();
                        @endif
                    }
                },
                language: {
                    search: "Cari:",
                    searchPlaceholder: "Ketik nama produk atau barcode..."
                },
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'barcode',
                        name: 'barcode',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'category.name',
                        name: 'category.name'
                    },
                    {
                        data: 'units',
                        orderable: false,
                        render: function(data, type, row) {
                            if (!Array.isArray(data)) return '';

                            let html = '';
                            data.forEach(function(unit) {
                                html += `
                        <div class="mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="me-2">${unit.unit_name}</span>
                                <input type="number"
                                       name="items[${unit.product_id}_${unit.unit_id}][actual_qty]"
                                       class="form-control form-control-sm actual-stock-input"
                                       data-current-stock="${unit.stock}"
                                       data-conversion="${unit.conversion_factor}"
                                       step="0.01"
                                       min="0">
                                <input type="hidden"
                                       name="items[${unit.product_id}_${unit.unit_id}][product_id]"
                                       value="${unit.product_id}">
                                <input type="hidden"
                                       name="items[${unit.product_id}_${unit.unit_id}][unit_id]"
                                       value="${unit.unit_id}">
                                <span class="difference-cell ms-2"></span>
                            </div>
                        </div>`;
                            });
                            return html;
                        }
                    },
                    {
                        data: 'units',
                        orderable: false,
                        render: function(data) {
                            if (!Array.isArray(data)) return '';

                            let html = '';
                            data.forEach(function(unit) {
                                html += `
                        <div class="mb-2">
                            <strong>${unit.stock}</strong> ${unit.unit_name}
                            ${unit.conversion_factor > 1 ?
                                    `<small class="text-muted d-block">1 ${unit.unit_name} = ${unit.conversion_factor} unit</small>`
                                    : ''}
                        </div>`;
                            });
                            return html;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        defaultContent: '',
                        className: 'text-center'
                    },
                    {
                        data: null,
                        orderable: false,
                        defaultContent: '',
                        className: 'text-center'
                    }
                ],
                pageLength: 25,
                order: [
                    [0, 'asc']
                ],
                createdRow: function(row, data, dataIndex) {
                    $(row).find('.actual-stock-input').on('input', function() {
                        let currentStock = parseFloat($(this).data('current-stock')) || 0;
                        let actualStock = parseFloat($(this).val()) || 0;
                        let difference = actualStock - currentStock;

                        let differenceCell = $(this).closest('.d-flex').find(
                            '.difference-cell');
                        differenceCell.text(difference.toFixed(2))
                            .removeClass('difference-positive difference-negative')
                            .addClass(difference >= 0 ? 'difference-positive' :
                                'difference-negative');
                    });
                }
            });

            @if (auth()->user()->role === 'admin')
                $('#store_id').on('change', function() {
                    table.ajax.reload();
                });
            @endif

            // Filter handlers
            $('#categoryFilter, #showZeroStock').on('change', function() {
                table.ajax.reload();
            });

            // Barcode scanning
            $('#scanBarcodeBtn').on('click', function() {
                $('#scannerModal').modal('show');
                setTimeout(() => $('#barcodeInput').focus(), 500);
            });

            $('#barcodeInput').on('keyup', function(e) {
                if (e.key === 'Enter') {
                    let barcode = $(this).val();
                    table.search(barcode).draw();
                    $(this).val('');
                    $('#scannerModal').modal('hide');
                }
            });

            // Input handlers
            $('#products-table').on('input', '.actual-stock-input', function() {
                let currentStock = parseFloat($(this).data('current-stock'));
                let actualStock = parseFloat($(this).val()) || 0;
                let difference = actualStock - currentStock;

                let differenceCell = $(this).closest('.d-flex').find('.difference-cell');
                differenceCell.text(difference.toFixed(2))
                    .removeClass('difference-positive difference-negative')
                    .addClass(difference >= 0 ? 'difference-positive' : 'difference-negative');
            });

            // Form validation
            $('#stockTakeForm').on('submit', function(e) {
                let hasQuantity = false;
                $('.actual-stock-input').each(function() {
                    if ($(this).val() !== '') {
                        hasQuantity = true;
                        return false;
                    }
                });

                if (!hasQuantity) {
                    e.preventDefault();
                    alert('Please fill actual quantity for at least one item');
                }
            });
        });
    </script>
@endpush
