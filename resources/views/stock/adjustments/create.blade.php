@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>New Stock Adjustment</h2>
        <a href="{{ route('stock.adjustments.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('stock.adjustments.store') }}" method="POST">
                @csrf

                <div class="row">
                    @if(auth()->user()->role === 'admin')
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="store_id" class="form-label">Store</label>
                                <select name="store_id" id="store_id" class="form-control" required>
                                    <option value="">Select Store</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="product_unit_id" class="form-label">Product & Unit</label>
                            <select name="product_unit_id" id="product_unit_id" class="form-control" required>
                                <option value="">Select Product & Unit</option>
                                @foreach($products as $product)
                                    <optgroup label="{{ $product->name }}">
                                        @foreach($product->productUnits as $productUnit)
                                            <option value="{{ $productUnit->id }}"
                                                    data-stock="{{ $productUnit->stock }}">
                                                {{ $product->name }} - {{ $productUnit->unit->name }}
                                                (Current Stock: {{ $productUnit->stock }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="in">Stock In</option>
                                <option value="out">Stock Out</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number"
                                   name="quantity"
                                   id="quantity"
                                   class="form-control"
                                   step="0.01"
                                   min="0.01"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes"
                              id="notes"
                              class="form-control"
                              rows="3"></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Adjustment</button>
                    <a href="{{ route('stock.adjustments.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function () {
                $('#type, #product_unit_id').on('change', function () {
                    validateQuantity();
                });

                $('#quantity').on('input', function () {
                    validateQuantity();
                });

                function validateQuantity() {
                    let type = $('#type').val();
                    let quantity = parseFloat($('#quantity').val()) || 0;
                    let option = $('#product_unit_id option:selected');
                    let currentStock = parseFloat(option.data('stock')) || 0;

                    if (type === 'out' && quantity > currentStock) {
                        $('#quantity').addClass('is-invalid');
                        $('.quantity-feedback').remove();
                        $('#quantity').after(`<div class="invalid-feedback quantity-feedback">
                    Quantity cannot exceed current stock (${currentStock})
                </div>`);
                    } else {
                        $('#quantity').removeClass('is-invalid');
                        $('.quantity-feedback').remove();
                    }
                }
            });
        </script>
    @endpush
@endsection
