{{-- resources/views/purchases/partials/form.blade.php --}}

@if ($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Purchase Information -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Informasi Pembelian</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @if (auth()->user()->role === 'admin')
                <div class="col-md-4">
                    <label class="form-label">Store</label>
                    <select name="store_id" class="form-select" required>
                        <option value="">Select Store</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}"
                                {{ isset($purchase) && $purchase->store_id == $store->id ? 'selected' : '' }}>
                                {{ $store->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="col-md-{{ auth()->user()->role === 'admin' ? '4' : '6' }}">
                <label class="form-label">Supplier</label>
                <select name="supplier_id" class="form-select" required>
                    <option value="">Select Supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            {{ isset($purchase) && $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                            @if (auth()->user()->role === 'admin')
                                ({{ $supplier->store->name }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-{{ auth()->user()->role === 'admin' ? '4' : '6' }}">
                <label class="form-label">Tanggal Pembelian</label>
                <input type="date" name="purchase_date" class="form-control" required
                    value="{{ isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : date('Y-m-d') }}">
            </div>
        </div>
    </div>
</div>

<!-- Items Section -->
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Barang</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#productModal">
            <i class='bx bx-plus'></i> Tambah Produk
        </button>
    </div>
    <div class="card-body">
        <div id="items-container">
            @if (isset($purchase) && $purchase->items->count() > 0)
                @foreach ($purchase->items as $index => $item)
                    <div class="row g-2 mb-3 p-3 border rounded item-row">
                        <div class="col-lg-4">
                            <label class="form-label">Produk</label>
                            <div class="input-group">
                                <input type="text" class="form-control product-name" readonly
                                    placeholder="Pilih Produk" value="{{ $item->product->name }}">
                                <input type="hidden" name="items[{{ $index }}][product_id]" class="product-id"
                                    value="{{ $item->product_id }}">
                                <button type="button" class="btn btn-outline-primary select-product-btn"
                                    data-bs-toggle="modal" data-bs-target="#productModal">
                                    <i class='bx bx-search'></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Unit</label>
                            <select name="items[{{ $index }}][unit_id]" class="form-select unit-select"
                                required>
                                @foreach ($item->product->units as $unit)
                                    <option value="{{ $unit->id }}" data-price="{{ $unit->purchase_price }}"
                                        {{ $item->unit_id == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} ({{ $unit->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-1">
                            <label class="form-label">QTY</label>
                            <input type="number" name="items[{{ $index }}][quantity]"
                                class="form-control quantity" required min="1" step="0.01"
                                value="{{ $item->quantity }}">
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Harga</label>
                            <input type="number" name="items[{{ $index }}][unit_price]"
                                class="form-control unit-price" required min="0" step="0.01"
                                value="{{ $item->unit_price }}">
                        </div>
                        <div class="col-lg-2">
                            <label class="form-label">Subtotal</label>
                            <input type="number" name="items[{{ $index }}][subtotal]"
                                class="form-control subtotal" readonly value="{{ $item->subtotal }}">
                        </div>
                        <div class="col-lg-1 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-danger remove-item">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="text-center py-4 text-muted"
            style="{{ isset($purchase) && $purchase->items->count() > 0 ? 'display: none;' : '' }}">
            <i class='bx bx-package fs-1'></i>
            <p>Belum ada produk. Klik "Tambah Produk" untuk memulai.</p>
        </div>
    </div>
</div>

<!-- Additional Information -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">Informasi Tambahan</h5>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Tipe Pembayaran</label>
                <select name="payment_type" class="form-select" required>
                    <option value="cash"
                        {{ isset($purchase) && $purchase->payment_type == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="transfer"
                        {{ isset($purchase) && $purchase->payment_type == 'transfer' ? 'selected' : '' }}>Transfer
                    </option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">No. Referensi</label>
                <input type="text" name="reference_number" class="form-control" placeholder="Optional"
                    value="{{ isset($purchase) ? $purchase->reference_number : '' }}">
            </div>
        </div>
        <div>
            <label class="form-label">Catatan</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan (opsional)">{{ isset($purchase) ? $purchase->notes : '' }}</textarea>
        </div>
    </div>
</div>

<!-- Summary -->
<div class="card border-primary">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div class="fs-5">
                <span class="fw-bold">Total: </span>
                <span id="total-amount" class="text-primary fs-4 fw-bold">
                    Rp {{ isset($purchase) ? number_format($purchase->total_amount, 0, ',', '.') : '0' }}
                </span>
                <input type="hidden" name="total_amount"
                    value="{{ isset($purchase) ? $purchase->total_amount : '0' }}">
                <input type="hidden" name="final_amount"
                    value="{{ isset($purchase) ? $purchase->final_amount : '0' }}">
            </div>
            <div class="d-flex gap-2">
                <a href="{{ isset($purchase) ? route('purchases.show', $purchase) : route('purchases.index') }}"
                    class="btn btn-outline-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    {{ isset($purchase) ? 'Update' : 'Create' }} Purchase
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Product Selection Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pilih Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="productTable">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Produk</th>
                                <th>Barcode</th>
                                <th>Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Setup CSRF token untuk semua AJAX request
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $(document).ready(function() {
            // Inisialisasi variabel
            var itemIndex = {{ isset($purchase) ? $purchase->items->count() : 0 }};
            var currentRow = null;
            var $itemsContainer = $('#items-container');
            var $emptyState = $('#empty-state');
            var $totalAmount = $('#total-amount');

            // Template untuk item row
            function getItemRowTemplate(index) {
                return '<div class="row g-2 mb-3 p-3 border rounded item-row">' +
                    '<div class="col-lg-4">' +
                    '<label class="form-label">Produk</label>' +
                    '<div class="input-group">' +
                    '<input type="text" class="form-control product-name" readonly placeholder="Pilih Produk">' +
                    '<input type="hidden" name="items[' + index + '][product_id]" class="product-id">' +
                    '<button type="button" class="btn btn-outline-primary select-product-btn" data-bs-toggle="modal" data-bs-target="#productModal">' +
                    '<i class="bx bx-search"></i>' +
                    '</button>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-lg-2">' +
                    '<label class="form-label">Unit</label>' +
                    '<select name="items[' + index + '][unit_id]" class="form-select unit-select" required>' +
                    '<option value="">Pilih Unit</option>' +
                    '</select>' +
                    '</div>' +
                    '<div class="col-lg-1">' +
                    '<label class="form-label">QTY</label>' +
                    '<input type="number" name="items[' + index +
                    '][quantity]" class="form-control quantity" required min="1" step="0.01">' +
                    '</div>' +
                    '<div class="col-lg-2">' +
                    '<label class="form-label">Harga</label>' +
                    '<input type="number" name="items[' + index +
                    '][unit_price]" class="form-control unit-price" required min="0" step="0.01">' +
                    '</div>' +
                    '<div class="col-lg-2">' +
                    '<label class="form-label">Subtotal</label>' +
                    '<input type="number" name="items[' + index +
                    '][subtotal]" class="form-control subtotal" readonly>' +
                    '</div>' +
                    '<div class="col-lg-1 d-flex align-items-end">' +
                    '<button type="button" class="btn btn-outline-danger remove-item">' +
                    '<i class="bx bx-trash"></i>' +
                    '</button>' +
                    '</div>' +
                    '</div>';
            }

            // Fungsi untuk menambah item baru
            function addNewItem(productData = null) {
                var $newRow = $(getItemRowTemplate(itemIndex));
                $itemsContainer.append($newRow);

                if (productData) {
                    var $row = $newRow;
                    $row.find('.product-id').val(productData.id);
                    $row.find('.product-name').val(productData.name);

                    // Populate units
                    var $unitSelect = $row.find('.unit-select');
                    $unitSelect.empty().append('<option value="">Pilih Unit</option>');

                    if (Array.isArray(productData.units) && productData.units.length > 0) {
                        productData.units.forEach(function(unit) {
                            $unitSelect.append(
                                '<option value="' + unit.id + '" data-price="' + unit.purchase_price +
                                '">' +
                                unit.name + ' (' + unit.stock + ')</option>'
                            );
                        });
                    }
                }

                itemIndex++;
                toggleEmptyState();
            }

            // Fungsi untuk toggle empty state
            function toggleEmptyState() {
                $emptyState.toggle($('.item-row').length === 0);
            }

            // Fungsi untuk menghitung subtotal
            function calculateSubtotal($row) {
                var qty = parseFloat($row.find('.quantity').val()) || 0;
                var price = parseFloat($row.find('.unit-price').val()) || 0;
                var subtotal = qty * price;
                $row.find('.subtotal').val(subtotal);
                calculateTotal();
            }

            // Fungsi untuk menghitung total
            function calculateTotal() {
                var total = 0;
                $('.subtotal').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $totalAmount.text('Rp ' + total.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }));
                $('input[name="total_amount"]').val(total);
                $('input[name="final_amount"]').val(total);
            }

            // Event Handlers
            $(document).on('change', '.unit-select', function() {
                var $this = $(this);
                var $row = $this.closest('.item-row');
                var price = $this.find(':selected').data('price');

                if (price) {
                    $row.find('.unit-price').val(price);
                    calculateSubtotal($row);
                }
            });

            $(document).on('input', '.quantity, .unit-price', function() {
                calculateSubtotal($(this).closest('.item-row'));
            });

            $(document).on('click', '.remove-item', function() {
                $(this).closest('.item-row').remove();
                calculateTotal();
                toggleEmptyState();
            });

            $(document).on('click', '.select-product', function(e) {
                e.preventDefault();
                var productData = {
                    id: $(this).data('id'),
                    name: $(this).data('name'),
                    barcode: $(this).data('barcode'),
                    units: $(this).data('units')
                };

                if (currentRow && currentRow.length > 0) {
                    currentRow.find('.product-id').val(productData.id);
                    currentRow.find('.product-name').val(productData.name);

                    // Populate units
                    var $unitSelect = currentRow.find('.unit-select');
                    $unitSelect.empty().append('<option value="">Pilih Unit</option>');

                    if (Array.isArray(productData.units) && productData.units.length > 0) {
                        productData.units.forEach(function(unit) {
                            $unitSelect.append(
                                '<option value="' + unit.id + '" data-price="' + unit
                                .purchase_price + '">' +
                                unit.name + ' (' + unit.stock + ')</option>'
                            );
                        });
                    }

                    currentRow = null;
                } else {
                    addNewItem(productData);
                }

                $('#productModal').modal('hide');
            });

            $(document).on('click', '.select-product-btn', function(e) {
                e.preventDefault();
                currentRow = $(this).closest('.item-row');
            });

            // Initialize DataTable
            $('#productTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('purchases.search-products') }}',
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
                        data: 'stock',
                        name: 'stock',
                        render: function(data) {
                            return data || 0;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return '<button type="button" class="btn btn-sm btn-primary select-product" ' +
                                'data-id="' + data.id + '" ' +
                                'data-name="' + data.name + '" ' +
                                'data-barcode="' + (data.barcode || '') + '" ' +
                                'data-units=\'' + JSON.stringify(data.units) + '\'>' +
                                'Pilih</button>';
                        }
                    }
                ],
                language: {
                    search: "Cari:",
                    searchPlaceholder: "Ketik nama produk atau barcode...",
                    processing: "Memuat data...",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                pageLength: 10,
                order: [
                    [0, 'asc']
                ]
            });

            // Initialize
            calculateTotal();
            toggleEmptyState();
        });
    </script>
@endpush
