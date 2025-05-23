{{-- resources/views/purchases/partials/form.blade.php --}}

@if($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
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
                    @if(auth()->user()->role === 'admin')
                        <div class="col-md-4">
                            <label class="form-label">Store</label>
                            <select name="store_id" class="form-select" required>
                                <option value="">Select Store</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ isset($purchase) && $purchase->store_id == $store->id ? 'selected' : '' }}>
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
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ isset($purchase) && $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                    @if(auth()->user()->role === 'admin')
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
                               value="{{ isset($purchase) ? $purchase->purchase_date->format('Y-m-d') : '' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Card -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Daftar Barang
                </h5>
            </div>
            <div class="card-body">
                <div id="items-container" class="mb-3">
                    @if(isset($purchase) && $purchase->items->count() > 0)
                        @foreach($purchase->items as $index => $item)
                            <div class="card mb-3 items-row border-1 shadow-none">
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <label class="form-label">Produk</label>
                                            <select name="items[{{ $index }}][product_id]" class="form-select product-select" required>
                                                <option value="">Select Product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                        {{ $product->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Unit</label>
                                            <select name="items[{{ $index }}][unit_id]" class="form-select unit-select" required>
                                                <option value="">Select Unit</option>
                                                @foreach($products->find($item->product_id)->units as $unit)
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
                                            <input type="number" name="items[{{ $index }}][quantity]" class="form-control quantity" required value="{{ $item->quantity }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Harga Unit</label>
                                            <input type="number" name="items[{{ $index }}][unit_price]" class="form-control unit-price" required value="{{ $item->unit_price }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Subtotal</label>
                                            <input type="number" name="items[{{ $index }}][subtotal]" class="form-control subtotal" readonly value="{{ $item->subtotal }}">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-danger remove-item">
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
                <button type="button" id="add-item" class="btn btn-outline-success">
                    <i class='bx bx-plus'></i> Tambah Barang
                </button>
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
                            <option value="cash" {{ isset($purchase) && $purchase->payment_type == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="transfer" {{ isset($purchase) && $purchase->payment_type == 'transfer' ? 'selected' : '' }}>Transfer</option>
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
                    <textarea name="notes" class="form-control" rows="3" placeholder="Optional notes...">{{ isset($purchase) ? $purchase->notes : '' }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Summary & Submit -->
                <div class="d-flex justify-content-between align-items-center">
                    <div class="fs-5">
                        <span class="fw-bold">Total Pembelian: </span>
                        <span id="total-amount" class="text-primary">{{ isset($purchase) ? number_format($purchase->total_amount, 2) : '0.00' }}</span>
                        <input type="hidden" name="total_amount" value="{{ isset($purchase) ? $purchase->total_amount : '0' }}">
                        <input type="hidden" name="tax_amount" value="{{ isset($purchase) ? $purchase->tax_amount : '0' }}">
                        <input type="hidden" name="discount_amount" value="{{ isset($purchase) ? $purchase->discount_amount : '0' }}">
                        <input type="hidden" name="final_amount" value="{{ isset($purchase) ? $purchase->final_amount : '0' }}">
                    </div>
                    <div class="d-flex gap-2">
                        @if(isset($purchase))
                            <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        @else
                            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            {{ isset($purchase) ? 'Update' : 'Create' }} Purchase Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            let products = @json($products);
            let productUnitsCache = new Map();

            // Initialize Select2 for product and unit dropdowns
            function initializeSelect2(row) {
                const productSelect = $(row).find('.product-select');
                const unitSelect = $(row).find('.unit-select');

                // Product select with search functionality
                productSelect.select2({
                    placeholder: 'Search for a product...',
                    allowClear: true,
                    ajax: {
                        url: '{{ route("purchases.search") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return { term: params.term };
                        },
                        processResults: function (data) {
                            // Cache units data for quick access
                            data.results.forEach(item => {
                                productUnitsCache.set(item.id, item.units);
                            });
                            return data;
                        },
                        cache: true
                    }
                }).on('select2:select', function (e) {
                    const productId = e.target.value;
                    const units = productUnitsCache.get(parseInt(productId));
                    updateUnitSelect(unitSelect, units);
                });

                // Unit select initialization
                unitSelect.select2({
                    placeholder: 'Select Unit'
                });
            }

            // Update unit options when product changes
            function updateUnitSelect(unitSelect, units) {
                unitSelect.empty().append('<option value="">Select Unit</option>');

                units.forEach(unit => {
                    const option = new Option(
                        `${unit.name} (Stock: ${unit.stock})`,
                        unit.id,
                        false,
                        false
                    );
                    $(option).data('price', unit.purchase_price);
                    $(option).data('stock', unit.stock);
                    unitSelect.append(option);
                });

                unitSelect.trigger('change');
            }

            // Add event listeners to item rows
            function initializeItemListeners(row) {
                const $row = $(row);
                const unitSelect = $row.find('.unit-select');
                const quantity = $row.find('.quantity');
                const unitPrice = $row.find('.unit-price');
                const removeButton = $row.find('.remove-item');

                // Set price when unit changes
                unitSelect.on('change', function () {
                    const selectedOption = $(this).find(':selected');
                    if (selectedOption.length) {
                        unitPrice.val(selectedOption.data('price') || '');
                        calculateSubtotal($row);
                    }
                });

                // Calculate on input change
                quantity.add(unitPrice).on('input', function () {
                    calculateSubtotal($row);
                });

                // Remove item
                removeButton.on('click', function () {
                    $row.remove();
                    calculateTotal();
                });
            }

            // Calculate subtotal for an item
            function calculateSubtotal(row) {
                const quantity = row.find('.quantity').val();
                const unitPrice = row.find('.unit-price').val();
                const subtotal = row.find('.subtotal');

                if (quantity && unitPrice) {
                    subtotal.val((quantity * unitPrice).toFixed(2));
                    calculateTotal();
                }
            }

            // Calculate order total
            function calculateTotal() {
                const total = $('.subtotal').get().reduce((sum, input) =>
                    sum + (parseFloat($(input).val()) || 0), 0);

                $('#total-amount').text(total.toFixed(2));
                $('input[name="total_amount"]').val(total.toFixed(2));
                $('input[name="final_amount"]').val(total.toFixed(2));
            }

            // Add a new item row
            $('#add-item').on('click', function () {
                const container = $('#items-container');
                const itemIndex = container.children().length;

                const itemHtml = `
                <div class="card mb-3 items-row border-1 shadow-none">
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">Product</label>
                                <select name="items[${itemIndex}][product_id]" class="form-select product-select" required></select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Unit</label>
                                <select name="items[${itemIndex}][unit_id]" class="form-select unit-select" required>
                                    <option value="">Select Unit</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Unit Price</label>
                                <input type="number" name="items[${itemIndex}][unit_price]" class="form-control unit-price" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Subtotal</label>
                                <input type="number" name="items[${itemIndex}][subtotal]" class="form-control subtotal" readonly>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger remove-item">
                                    <i class='bx bx-trash'></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                container.append(itemHtml);
                const newRow = container.children().last();

                initializeSelect2(newRow);
                initializeItemListeners(newRow);
            });

            // Initialize existing rows
            $('.items-row').each(function () {
                initializeSelect2(this);
                initializeItemListeners(this);
            });

            // Initialize the first item row if no items exist
            if ($('.items-row').length === 0) {
                $('#add-item').trigger('click');
            }
        });
    </script>
@endpush
