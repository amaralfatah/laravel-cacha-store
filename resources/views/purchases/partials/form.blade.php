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

<div class="row gap-3">
    <!-- Purchase Information Card -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Informasi Pembelian
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Store Selection (Admin Only) -->
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

                    <!-- Supplier Selection -->
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

                    <!-- Purchase Date -->
                    <div class="col-md-{{ auth()->user()->role === 'admin' ? '4' : '6' }}">
                        <label class="form-label">Tanggal Pembelian</label>
                        <input type="date" name="purchase_date" class="form-control" required
                            value="{{ isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : date('Y-m-d') }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Card -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    Daftar Barang
                </h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal"
                        data-bs-target="#productModal">
                        <i class='bx bx-search'></i> Cari Produk
                    </button>
                    <button type="button" id="add-item" class="btn btn-outline-success btn-sm">
                        <i class='bx bx-plus'></i> Tambah Manual
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Barcode Scanner Info -->
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class='bx bx-info-circle'></i>
                    <strong>Tips:</strong> Anda bisa scan barcode langsung, cari produk lewat modal, atau pilih manual
                    dari dropdown.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <div id="items-container" class="mb-3">
                    @if (isset($purchase) && $purchase->items->count() > 0)
                        @foreach ($purchase->items as $index => $item)
                            <div class="card mb-3 items-row border-1 shadow-none" data-index="{{ $index }}">
                                <div class="card-body">
                                    <div class="row g-2">
                                        <!-- Enhanced Product Selection -->
                                        <div class="col-md-3">
                                            <label class="form-label">Produk</label>
                                            <div class="input-group">
                                                <select name="items[{{ $index }}][product_id]"
                                                    class="form-select product-select" required>
                                                    <option value="">Pilih Produk</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            data-barcode="{{ $product->barcode ?? '' }}"
                                                            data-units="{{ json_encode(
                                                                $product->units->map(function ($unit) {
                                                                    return [
                                                                        'id' => $unit->id,
                                                                        'name' => $unit->name,
                                                                        'purchase_price' => $unit->purchase_price,
                                                                        'stock' => $unit->stock,
                                                                    ];
                                                                }),
                                                            ) }}"
                                                            {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }}
                                                            @if ($product->barcode)
                                                                ({{ $product->barcode }})
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="button"
                                                    class="btn btn-outline-primary btn-select-product"
                                                    data-bs-toggle="modal" data-bs-target="#productModal">
                                                    <i class='bx bx-search'></i>
                                                </button>
                                            </div>
                                            <small class="text-muted product-info">
                                                @if ($item->product->barcode)
                                                    Barcode: {{ $item->product->barcode }}
                                                @endif
                                            </small>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Unit</label>
                                            <select name="items[{{ $index }}][unit_id]"
                                                class="form-select unit-select" required>
                                                <option value="">Select Unit</option>
                                                @foreach ($products->find($item->product_id)->units as $unit)
                                                    <option value="{{ $unit->id }}"
                                                        data-price="{{ $unit->purchase_price }}"
                                                        data-stock="{{ $unit->stock }}"
                                                        {{ $item->unit_id == $unit->id ? 'selected' : '' }}>
                                                        {{ $unit->name }} (Stock: {{ $unit->stock }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">QTY</label>
                                            <input type="number" name="items[{{ $index }}][quantity]"
                                                class="form-control quantity" required min="1" step="0.01"
                                                value="{{ $item->quantity }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Harga Unit</label>
                                            <input type="number" name="items[{{ $index }}][unit_price]"
                                                class="form-control unit-price" required min="0" step="0.01"
                                                value="{{ $item->unit_price }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Subtotal</label>
                                            <input type="number" name="items[{{ $index }}][subtotal]"
                                                class="form-control subtotal" readonly value="{{ $item->subtotal }}">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-danger remove-item"
                                                title="Hapus item">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <!-- Items will be added here dynamically -->
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="text-center py-4"
                    style="{{ isset($purchase) && $purchase->items->count() > 0 ? 'display: none;' : '' }}">
                    <i class='bx bx-package fs-1 text-muted'></i>
                    <h5 class="text-muted">Belum ada produk</h5>
                    <p class="text-muted">Scan barcode, cari produk, atau tambah manual untuk memulai</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information Card -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Informasi Tambahan
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tipe Pembayaran</label>
                        <select name="payment_type" class="form-select" required>
                            <option value="cash"
                                {{ isset($purchase) && $purchase->payment_type == 'cash' ? 'selected' : '' }}>
                                Cash
                            </option>
                            <option value="transfer"
                                {{ isset($purchase) && $purchase->payment_type == 'transfer' ? 'selected' : '' }}>
                                Transfer
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
                    <textarea name="notes" class="form-control" rows="3" placeholder="Catatan pembelian (opsional)...">{{ isset($purchase) ? $purchase->notes : '' }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="fs-5">
                        <span class="fw-bold">Total Pembelian: </span>
                        <span id="total-amount" class="text-primary fs-4 fw-bold">
                            Rp {{ isset($purchase) ? number_format($purchase->total_amount, 0, ',', '.') : '0' }}
                        </span>
                        <input type="hidden" name="total_amount"
                            value="{{ isset($purchase) ? $purchase->total_amount : '0' }}">
                        <input type="hidden" name="tax_amount"
                            value="{{ isset($purchase) ? $purchase->tax_amount : '0' }}">
                        <input type="hidden" name="discount_amount"
                            value="{{ isset($purchase) ? $purchase->discount_amount : '0' }}">
                        <input type="hidden" name="final_amount"
                            value="{{ isset($purchase) ? $purchase->final_amount : '0' }}">
                    </div>
                    <div class="d-flex gap-2">
                        @if (isset($purchase))
                            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-secondary">
                                <i class='bx bx-x'></i> Cancel
                            </a>
                        @else
                            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                                <i class='bx bx-x'></i> Cancel
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <i class='bx {{ isset($purchase) ? 'bx-edit' : 'bx-plus' }}'></i>
                            {{ isset($purchase) ? 'Update' : 'Create' }} Purchase Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Product Selection Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">
                    <i class='bx bx-search'></i> Cari & Pilih Produk
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Enhanced Search -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class='bx bx-search'></i></span>
                            <input type="text" class="form-control" id="productSearch"
                                placeholder="Cari berdasarkan nama produk atau scan barcode...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="categoryFilter">
                            <option value="">Semua Kategori</option>
                            @foreach ($products->groupBy('category.name')->keys() as $categoryName)
                                @if ($categoryName)
                                    <option value="{{ $categoryName }}">{{ $categoryName }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Loading indicator -->
                <div id="loading" class="text-center py-3" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Barcode</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Stok Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            <!-- Products will be loaded here -->
                        </tbody>
                    </table>
                </div>

                <!-- No Results -->
                <div id="noResults" class="text-center py-4" style="display: none;">
                    <i class='bx bx-search-alt-2 fs-1 text-muted'></i>
                    <h5 class="text-muted">Produk tidak ditemukan</h5>
                    <p class="text-muted">Coba gunakan kata kunci lain atau scan barcode</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class='bx bx-x'></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <style>
        /* Enhanced styling for better form layout */
        .items-row .row {
            align-items: end;
        }

        .items-row .input-group {
            flex-wrap: nowrap;
        }

        .items-row .input-group .btn {
            border-left: 0;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            white-space: nowrap;
        }

        .items-row .form-select,
        .items-row .form-control {
            min-height: 38px;
        }

        .items-row .product-info {
            display: block;
            margin-top: 2px;
            font-size: 0.75rem;
        }

        @media (max-width: 768px) {
            .items-row .col-lg-1.col-md-12 {
                margin-top: 1rem;
                padding-top: 0.5rem;
                border-top: 1px solid #dee2e6;
            }

            .items-row .remove-item {
                width: 100%;
                justify-content: center;
            }
        }

        @media (min-width: 992px) {
            .items-row .col-lg-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }

            .items-row .col-lg-2 {
                flex: 0 0 16.666667%;
                max-width: 16.666667%;
            }

            .items-row .col-lg-1 {
                flex: 0 0 8.333333%;
                max-width: 8.333333%;
            }
        }

        /* Toast styling */
        .toast-container {
            z-index: 1080;
        }

        /* Modal enhancements */
        .modal-xl {
            max-width: 1200px;
        }

        #productsGrid {
            max-height: 60vh;
            overflow-y: auto;
        }

        /* Empty state styling */
        #empty-state {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            margin: 1rem 0;
        }

        /* Product selection button styling */
        .btn-select-product {
            border-left: 1px solid #ced4da !important;
        }

        .product-select+.btn-select-product {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
    </style>
    <script>
        $(document).ready(function() {
            let currentProductRow = null;
            let searchTimeout = null;

            // Initialize Select2 for existing rows
            $('.product-select').each(function() {
                initializeProductSelect($(this));
            });

            // Initialize Select2 for product selection
            function initializeProductSelect(selectElement) {
                if (selectElement.hasClass('select2-hidden-accessible')) {
                    return; // Already initialized
                }

                selectElement.select2({
                    placeholder: 'Cari produk...',
                    allowClear: true,
                    width: '100%'
                }).on('select2:select', function(e) {
                    const option = $(e.target).find('option:selected');
                    const units = JSON.parse(option.data('units') || '[]');
                    const row = $(this).closest('.items-row');
                    updateUnitSelect(row.find('.unit-select'), units);
                    updateProductInfo(row, option);
                });
            }

            // Add new item manually
            $('#add-item').on('click', function() {
                addNewItemRow();
            });

            // Add new item row
            function addNewItemRow(productData = null) {
                const container = $('#items-container');
                const itemIndex = Date.now(); // Use timestamp for unique index

                const itemHtml = `
                    <div class="card mb-3 items-row border-1 shadow-none" data-index="${itemIndex}">
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-lg-4 col-md-6">
                                    <label class="form-label">Produk</label>
                                    <div class="input-group">
                                        <select name="items[${itemIndex}][product_id]" class="form-select product-select" required>
                                            <option value="">Pilih Produk</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-barcode="{{ $product->barcode ?? '' }}"
                                                    data-units="{{ json_encode(
                                                        $product->units->map(function ($unit) {
                                                            return [
                                                                'id' => $unit->id,
                                                                'name' => $unit->name,
                                                                'purchase_price' => $unit->purchase_price,
                                                                'stock' => $unit->stock,
                                                            ];
                                                        }),
                                                    ) }}">
                                                    {{ $product->name }}
                                                    @if ($product->barcode)
                                                        ({{ $product->barcode }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="button" class="btn btn-outline-primary btn-select-product"
                                                data-bs-toggle="modal" data-bs-target="#productModal" title="Cari Produk">
                                            <i class='bx bx-search'></i>
                                        </button>
                                    </div>
                                    <small class="text-muted product-info"></small>
                                </div>
                                <div class="col-lg-2 col-md-6">
                                    <label class="form-label">Unit</label>
                                    <select name="items[${itemIndex}][unit_id]" class="form-select unit-select" required>
                                        <option value="">Pilih Unit</option>
                                    </select>
                                </div>
                                <div class="col-lg-1 col-md-4">
                                    <label class="form-label">QTY</label>
                                    <input type="number" name="items[${itemIndex}][quantity]"
                                           class="form-control quantity" required min="1" step="0.01">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">Harga Unit</label>
                                    <input type="number" name="items[${itemIndex}][unit_price]"
                                           class="form-control unit-price" required min="0" step="0.01">
                                </div>
                                <div class="col-lg-2 col-md-4">
                                    <label class="form-label">Subtotal</label>
                                    <input type="number" name="items[${itemIndex}][subtotal]"
                                           class="form-control subtotal" readonly>
                                </div>
                                <div class="col-lg-1 col-md-12 d-flex align-items-end justify-content-center">
                                    <button type="button" class="btn btn-outline-danger remove-item" title="Hapus item">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                container.append(itemHtml);
                const newRow = container.find('.items-row').last();

                // Initialize Select2 for new row
                initializeProductSelect(newRow.find('.product-select'));

                // If product data provided, set it
                if (productData) {
                    setProductInRow(newRow, productData);
                }

                toggleEmptyState();

                // Scroll to new item
                newRow[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }

            // Set product in row
            function setProductInRow(row, productData) {
                const productSelect = row.find('.product-select');
                productSelect.val(productData.id).trigger('change');

                // Trigger select2 update if needed
                if (productSelect.hasClass('select2-hidden-accessible')) {
                    productSelect.trigger('change.select2');
                }
            }

            // Update unit select
            function updateUnitSelect(unitSelect, units) {
                unitSelect.empty().append('<option value="">Pilih Unit</option>');

                units.forEach(unit => {
                    const option = $('<option></option>')
                        .attr('value', unit.id)
                        .attr('data-price', unit.purchase_price)
                        .attr('data-stock', unit.stock)
                        .text(`${unit.name} (Stock: ${unit.stock})`);
                    unitSelect.append(option);
                });
            }

            // Update product info
            function updateProductInfo(row, option) {
                const barcode = option.data('barcode');
                const infoElement = row.find('.product-info');
                infoElement.text(barcode ? `Barcode: ${barcode}` : '');
            }

            // Unit selection - auto fill price
            $(document).on('change', '.unit-select', function() {
                const selected = $(this).find('option:selected');
                const price = selected.data('price');
                const row = $(this).closest('.items-row');

                if (price) {
                    row.find('.unit-price').val(price);
                    calculateSubtotal(row);
                }
            });

            // Calculate subtotal
            $(document).on('input', '.quantity, .unit-price', function() {
                const row = $(this).closest('.items-row');
                calculateSubtotal(row);
            });

            function calculateSubtotal(row) {
                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
                const subtotal = quantity * unitPrice;
                row.find('.subtotal').val(subtotal);
                calculateTotal();
            }

            // Remove item
            $(document).on('click', '.remove-item', function() {
                $(this).closest('.items-row').remove();
                calculateTotal();
                toggleEmptyState();
            });

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

            // Toggle empty state
            function toggleEmptyState() {
                const hasItems = $('.items-row').length > 0;
                $('#empty-state').toggle(!hasItems);
            }

            // Enhanced Barcode Scanner
            let barcodeBuffer = '';
            let lastKeypressTime = 0;
            const barcodeThreshold = 100; // ms

            $(document).on('keypress', function(e) {
                // Only process if not typing in an input field
                if ($(e.target).is('input, textarea, select')) return;

                const currentTime = Date.now();

                if (currentTime - lastKeypressTime > barcodeThreshold) {
                    barcodeBuffer = '';
                }

                lastKeypressTime = currentTime;

                if (e.which === 13) { // Enter key
                    if (barcodeBuffer.length > 5) { // Minimum barcode length
                        e.preventDefault();
                        searchProductByBarcode(barcodeBuffer.trim());
                        barcodeBuffer = '';
                    }
                } else {
                    barcodeBuffer += String.fromCharCode(e.which);
                }
            });

            // Search product by barcode
            function searchProductByBarcode(barcode) {
                // Find product by barcode
                const productOption = $('.product-select option').filter(function() {
                    return $(this).data('barcode') === barcode;
                });

                if (productOption.length > 0) {
                    const productData = {
                        id: productOption.val(),
                        name: productOption.text(),
                        barcode: barcode,
                        units: JSON.parse(productOption.data('units') || '[]')
                    };

                    addNewItemRow(productData);

                    // Show success message
                    showToast('success', `Produk "${productData.name}" berhasil ditambahkan`);
                } else {
                    showToast('warning', `Produk dengan barcode "${barcode}" tidak ditemukan`);
                }
            }

            // Modal product selection
            $(document).on('click', '.btn-select-product', function() {
                currentProductRow = $(this).closest('.items-row');
                loadProductsInModal();
            });

            // Load products in modal
            function loadProductsInModal(searchTerm = '', category = '') {
                $('#loading').show();
                $('#productTableBody').empty();

                // Simulate API call (replace with actual AJAX)
                setTimeout(() => {
                    const tbody = $('#productTableBody');
                    let filteredProducts = @json($products->toArray());

                    // Filter products
                    if (searchTerm) {
                        filteredProducts = filteredProducts.filter(product =>
                            product.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                            (product.barcode && product.barcode.toLowerCase().includes(searchTerm
                                .toLowerCase()))
                        );
                    }

                    if (category) {
                        filteredProducts = filteredProducts.filter(product =>
                            product.category && product.category.name === category
                        );
                    }

                    tbody.empty();

                    if (filteredProducts.length === 0) {
                        $('#noResults').show();
                    } else {
                        $('#noResults').hide();

                        filteredProducts.forEach(product => {
                            const totalStock = product.units ? product.units.reduce((sum, unit) =>
                                sum + unit.stock, 0) : 0;
                            tbody.append(`
                                <tr>
                                    <td>${product.barcode || '-'}</td>
                                    <td>
                                        <strong>${product.name}</strong>
                                        ${product.barcode ? `<br><small class="text-muted">Barcode: ${product.barcode}</small>` : ''}
                                    </td>
                                    <td>${product.category ? product.category.name : '-'}</td>
                                    <td>
                                        <span class="badge ${totalStock > 0 ? 'bg-success' : 'bg-warning'}">${totalStock}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary select-product"
                                                data-product-id="${product.id}"
                                                data-product-name="${product.name}"
                                                data-product-barcode="${product.barcode || ''}"
                                                data-product-units='${JSON.stringify(product.units || [])}'>
                                            <i class='bx bx-plus'></i> Pilih
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    }

                    $('#loading').hide();
                }, 300);
            }

            // Modal search functionality
            $('#productSearch').on('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = $(this).val();
                const category = $('#categoryFilter').val();

                searchTimeout = setTimeout(() => {
                    loadProductsInModal(searchTerm, category);
                }, 300);
            });

            $('#categoryFilter').on('change', function() {
                const searchTerm = $('#productSearch').val();
                const category = $(this).val();
                loadProductsInModal(searchTerm, category);
            });

            // Select product from modal
            $(document).on('click', '.select-product', function() {
                const productData = {
                    id: $(this).data('product-id'),
                    name: $(this).data('product-name'),
                    barcode: $(this).data('product-barcode'),
                    units: $(this).data('product-units')
                };

                if (currentProductRow && currentProductRow.length > 0) {
                    // Update existing row
                    setProductInRow(currentProductRow, productData);
                } else {
                    // Add new row
                    addNewItemRow(productData);
                }

                $('#productModal').modal('hide');
                showToast('success', `Produk "${productData.name}" berhasil dipilih`);
            });

            // Show toast notification
            function showToast(type, message) {
                // Simple toast implementation
                const toast = $(`
                    <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'warning'} border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class='bx ${type === 'success' ? 'bx-check' : 'bx-info-circle'}'></i> ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `);

                // Add to body or toast container
                $('body').append(
                    `<div class="toast-container position-fixed top-0 end-0 p-3">${toast[0].outerHTML}</div>`);

                // Initialize and show
                const toastElement = new bootstrap.Toast($('.toast').last()[0]);
                toastElement.show();

                // Remove after hide
                $('.toast').last().on('hidden.bs.toast', function() {
                    $(this).closest('.toast-container').remove();
                });
            }

            // Initialize
            toggleEmptyState();
            calculateTotal();

            // Load products in modal when opened
            $('#productModal').on('shown.bs.modal', function() {
                $('#productSearch').focus();
                if ($('#productTableBody').is(':empty')) {
                    loadProductsInModal();
                }
            });
        });
    </script>
@endpush
