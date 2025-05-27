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
        <div>
            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal"
                data-bs-target="#productModal">
                <i class='bx bx-search'></i> Cari Produk
            </button>
            <button type="button" id="add-item" class="btn btn-success btn-sm">
                <i class='bx bx-plus'></i> Tambah
            </button>
        </div>
    </div>
    <div class="card-body">
        <div id="items-container">
            @if (isset($purchase) && $purchase->items->count() > 0)
                @foreach ($purchase->items as $index => $item)
                    <div class="row g-2 mb-3 p-3 border rounded item-row">
                        <div class="col-lg-4">
                            <label class="form-label">Produk</label>
                            <div class="input-group">
                                <select name="items[{{ $index }}][product_id]"
                                    class="form-select product-select" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            data-units='{{ json_encode(
                                                $product->units->map(function ($unit) {
                                                    return [
                                                        'id' => $unit->id,
                                                        'name' => $unit->name,
                                                        'purchase_price' => $unit->purchase_price,
                                                        'stock' => $unit->stock,
                                                    ];
                                                }),
                                            ) }}'
                                            {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                            @if ($product->barcode)
                                                ({{ $product->barcode }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
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
            <p>Belum ada produk. Scan barcode atau klik "Tambah" untuk memulai.</p>
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
        $(document).ready(function() {
            let itemIndex = {{ isset($purchase) ? $purchase->items->count() : 0 }};
            let currentRow = null;

            // Initialize DataTable
            const productTable = $('#productTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('purchases.search-products') }}',
                    type: 'GET',
                    data: function(d) {
                        return {
                            draw: d.draw,
                            start: d.start,
                            length: d.length,
                            search: d.search.value,
                            order: d.order,
                            columns: d.columns
                        };
                    }
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
                        data: 'stock',
                        name: 'stock',
                        render: function(data) {
                            return data || 0;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `
                                <button class="btn btn-sm btn-primary select-product"
                                    data-id="${data.id}"
                                    data-name="${data.name}"
                                    data-barcode="${data.barcode || ''}"
                                    data-units='${JSON.stringify(data.units)}'>
                                    Pilih
                                </button>
                            `;
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

            // Add new item
            $('#add-item').on('click', function() {
                addNewItem();
            });

            // Add new item function
            function addNewItem(productData = null) {
                const html = `
            <div class="row g-2 mb-3 p-3 border rounded item-row">
                <div class="col-lg-4">
                    <label class="form-label">Produk</label>
                    <div class="input-group">
                        <select name="items[${itemIndex}][product_id]" class="form-select product-select" required>
                            <option value="">Pilih Produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" data-units="{{ json_encode($product->units) }}">
                                    {{ $product->name }}
                                    @if ($product->barcode) ({{ $product->barcode }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-primary select-product-btn" data-bs-toggle="modal" data-bs-target="#productModal">
                            <i class='bx bx-search'></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Unit</label>
                    <select name="items[${itemIndex}][unit_id]" class="form-select unit-select" required>
                        <option value="">Pilih Unit</option>
                    </select>
                </div>
                <div class="col-lg-1">
                    <label class="form-label">QTY</label>
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity" required min="1" step="0.01">
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Harga</label>
                    <input type="number" name="items[${itemIndex}][unit_price]" class="form-control unit-price" required min="0" step="0.01">
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Subtotal</label>
                    <input type="number" name="items[${itemIndex}][subtotal]" class="form-control subtotal" readonly>
                </div>
                <div class="col-lg-1 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger remove-item">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
            </div>
        `;

                $('#items-container').append(html);
                itemIndex++;

                if (productData) {
                    const newRow = $('.item-row').last();
                    newRow.find('.product-select').val(productData.id).trigger('change');
                }

                toggleEmptyState();
            }

            // Product selection change
            $(document).on('change', '.product-select', function(e) {
                const $this = $(this);
                const selectedOption = $this.find(':selected');
                const units = selectedOption.data('units') || [];
                const unitSelect = $this.closest('.item-row').find('.unit-select');

                // Clear and populate unit select
                unitSelect.empty().append('<option value="">Pilih Unit</option>');

                if (Array.isArray(units) && units.length > 0) {
                    units.forEach(unit => {
                        unitSelect.append(
                            `<option value="${unit.id}" data-price="${unit.purchase_price}">${unit.name} (${unit.stock})</option>`
                        );
                    });
                }

                console.log('Product changed, units loaded:', units.length);
            });

            // Unit selection change - auto fill price
            $(document).on('change', '.unit-select', function() {
                const price = $(this).find(':selected').data('price');
                if (price) {
                    $(this).closest('.item-row').find('.unit-price').val(price);
                    calculateSubtotal($(this).closest('.item-row'));
                }
            });

            // Calculate subtotal
            $(document).on('input', '.quantity, .unit-price', function() {
                calculateSubtotal($(this).closest('.item-row'));
            });

            function calculateSubtotal(row) {
                const qty = parseFloat(row.find('.quantity').val()) || 0;
                const price = parseFloat(row.find('.unit-price').val()) || 0;
                const subtotal = qty * price;
                row.find('.subtotal').val(subtotal);
                calculateTotal();
            }

            // Calculate total
            function calculateTotal() {
                let total = 0;
                $('.subtotal').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#total-amount').text(`Rp ${total.toLocaleString('id-ID')}`);
                $('input[name="total_amount"]').val(total);
                $('input[name="final_amount"]').val(total);
            }

            // Remove item
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.item-row').remove();
                calculateTotal();
                toggleEmptyState();
            });

            // Toggle empty state
            function toggleEmptyState() {
                $('#empty-state').toggle($('.item-row').length === 0);
            }

            // Select product from modal
            $(document).on('click', '.select-product', function(e) {
                e.preventDefault();

                const productData = {
                    id: $(this).data('id'),
                    name: $(this).data('name'),
                    barcode: $(this).data('barcode'),
                    units: $(this).data('units')
                };

                if (currentRow && currentRow.length > 0) {
                    // Update existing row
                    const productSelect = currentRow.find('.product-select');
                    productSelect.val(productData.id);

                    // Trigger change event to load units
                    productSelect.trigger('change');

                    // Reset currentRow
                    currentRow = null;
                } else {
                    // Add new row
                    addNewItem(productData);
                }

                $('#productModal').modal('hide');

                // Show feedback
                console.log('Produk dipilih:', productData.name);
            });

            // Set current row for modal
            $(document).on('click', '.select-product-btn', function(e) {
                e.preventDefault();
                currentRow = $(this).closest('.item-row');
                console.log('Current row set for modal selection');
            });

            // Barcode scanner (simplified)
            let barcodeBuffer = '';
            let lastKeyTime = 0;

            $(document).on('keypress', function(e) {
                if ($(e.target).is('input, textarea, select')) return;

                const now = Date.now();
                if (now - lastKeyTime > 100) barcodeBuffer = '';
                lastKeyTime = now;

                if (e.which === 13 && barcodeBuffer.length > 5) {
                    e.preventDefault();
                    const barcode = barcodeBuffer.trim().toLowerCase();
                    const product = $('.product-row').filter(function() {
                        const productBarcode = $(this).attr('data-barcode') || '';
                        return productBarcode === barcode;
                    });

                    if (product.length) {
                        product.find('.select-product').click();
                    } else {
                        alert('Produk dengan barcode "' + barcode + '" tidak ditemukan');
                    }
                    barcodeBuffer = '';
                } else {
                    barcodeBuffer += String.fromCharCode(e.which);
                }
            });

            // Initialize
            calculateTotal();
            toggleEmptyState();
        });
    </script>
@endpush
