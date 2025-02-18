{{-- resources/views/purchases/edit.blade.php --}}
@extends('layouts.app')

@section('content')

    <x-section-header title="Edit Purchase Order"/>

    <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PUT')
        <div class="card">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <select name="supplier_id" class="form-select" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option
                                        value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Purchase Date</label>
                            <input type="date" name="purchase_date" class="form-control" required
                                   value="{{ $purchase->purchase_date->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h5 class="card-title">Items</h5>
                    <div id="items-container">
                        @foreach($purchase->items as $index => $item)
                            <div class="card mb-3 items-row">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">Product</label>
                                                <select name="items[{{ $index }}][product_id]"
                                                        class="form-select product-select" required>
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option
                                                            value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Unit</label>
                                                <select name="items[{{ $index }}][unit_id]"
                                                        class="form-select unit-select" required>
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
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Quantity</label>
                                                <input type="number" name="items[{{ $index }}][quantity]"
                                                       class="form-control quantity" required
                                                       value="{{ $item->quantity }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Unit Price</label>
                                                <input type="number" name="items[{{ $index }}][unit_price]"
                                                       class="form-control unit-price" required
                                                       value="{{ $item->unit_price }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label class="form-label">Subtotal</label>
                                                <input type="number" name="items[{{ $index }}][subtotal]"
                                                       class="form-control subtotal" readonly
                                                       value="{{ $item->subtotal }}">
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="mb-3">
                                                <label class="form-label">&nbsp;</label>
                                                <button type="button" class="btn btn-danger remove-item">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="add-item" class="btn btn-success">
                        <i class="bi bi-plus"></i> Add Item
                    </button>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Payment Type</label>
                            <select name="payment_type" class="form-select" required>
                                <option value="cash" {{ $purchase->payment_type == 'cash' ? 'selected' : '' }}>Cash
                                </option>
                                <option value="transfer" {{ $purchase->payment_type == 'transfer' ? 'selected' : '' }}>
                                    Transfer
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" class="form-control"
                                   value="{{ $purchase->reference_number }}">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3">{{ $purchase->notes }}</textarea>
                </div>

                <div class="text-end">
                    <div class="mb-3">
                        <span class="fw-bold">Total: </span>
                        <span id="total-amount">{{ number_format($purchase->total_amount, 2) }}</span>
                        <input type="hidden" name="total_amount" value="{{ $purchase->total_amount }}">
                        <input type="hidden" name="final_amount" value="{{ $purchase->final_amount }}">
                    </div>
                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Purchase Order
                    </button>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
    <script>
        // Tunggu sampai dokumen siap
        $(document).ready(function () {
            let products = @json($products);
            let productUnitsCache = new Map();

            function initializeSelect2(row) {
                const productSelect = $(row).find('.product-select');
                const unitSelect = $(row).find('.unit-select');

                // Initialize product select with AJAX search
                productSelect.select2({
                    placeholder: 'Search for a product...',
                    allowClear: true,
                    ajax: {
                        url: '{{ route("purchases.search") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                term: params.term
                            };
                        },
                        processResults: function (data) {
                            // Cache units data
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

                // Initialize unit select
                unitSelect.select2({
                    placeholder: 'Select Unit'
                });
            }

            // Sisanya tetap sama seperti sebelumnya, tapi gunakan jQuery selector
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

            // Initialize existing rows
            $('.items-row').each(function () {
                initializeSelect2(this);
                initializeItemListeners(this);
            });

            // Add item button handler
            $('#add-item').on('click', function () {
                const container = $('#items-container');
                const itemIndex = container.children().length;

                // HTML template sama seperti sebelumnya
                const itemHtml = `
            <div class="card mb-3 items-row">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Product</label>
                                <select name="items[${itemIndex}][product_id]" class="form-select product-select" required></select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Unit</label>
                                <select name="items[${itemIndex}][unit_id]" class="form-select unit-select" required>
                                    <option value="">Select Unit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="items[${itemIndex}][quantity]" class="form-control quantity" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Unit Price</label>
                                <input type="number" name="items[${itemIndex}][unit_price]" class="form-control unit-price" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Subtotal</label>
                                <input type="number" name="items[${itemIndex}][subtotal]" class="form-control subtotal" readonly>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-danger remove-item">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
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

            function initializeItemListeners(row) {
                const $row = $(row);
                const unitSelect = $row.find('.unit-select');
                const quantity = $row.find('.quantity');
                const unitPrice = $row.find('.unit-price');
                const removeButton = $row.find('.remove-item');

                unitSelect.on('change', function () {
                    const selectedOption = $(this).find(':selected');
                    if (selectedOption.length) {
                        unitPrice.val(selectedOption.data('price') || '');
                        calculateSubtotal($row);
                    }
                });

                quantity.add(unitPrice).on('input', function () {
                    calculateSubtotal($row);
                });

                removeButton.on('click', function () {
                    $row.remove();
                    calculateTotal();
                });
            }

            function calculateSubtotal(row) {
                const quantity = row.find('.quantity').val();
                const unitPrice = row.find('.unit-price').val();
                const subtotal = row.find('.subtotal');

                if (quantity && unitPrice) {
                    subtotal.val((quantity * unitPrice).toFixed(2));
                    calculateTotal();
                }
            }

            function calculateTotal() {
                const total = $('.subtotal').get().reduce((sum, input) => sum + (parseFloat($(input).val()) || 0), 0);

                $('#total-amount').text(total.toFixed(2));
                $('input[name="total_amount"]').val(total.toFixed(2));
                $('input[name="final_amount"]').val(total.toFixed(2));
            }
        });
    </script>
@endpush
