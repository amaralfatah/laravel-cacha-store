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
                    @if (auth()->user()->role === 'admin')
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="store_id" class="form-label">Store</label>
                                <select name="store_id" id="store_id" class="form-control" required>
                                    <option value="">Select Store</option>
                                    @foreach ($stores as $store)
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
                            <input type="number" name="quantity" id="quantity" class="form-control" step="0.01"
                                min="0.01" required>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Save Adjustment</button>
                    <a href="{{ route('stock.adjustments.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#product_unit_id').select2({
                placeholder: 'Cari produk berdasarkan nama atau barcode...',
                allowClear: true,
                ajax: {
                    url: '{{ route('stock.adjustments.getProducts') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            page: params.page
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                },
                minimumInputLength: 2
            }).on('select2:select', function(e) {
                const data = e.params.data;
                $(this).find(`option[value="${data.id}"]`).attr('data-stock', data.stock);
                validateQuantity();
            });

            // Update the existing validateQuantity function
            function validateQuantity() {
                let type = $('#type').val();
                let quantity = parseFloat($('#quantity').val()) || 0;
                let selectedOption = $('#product_unit_id').select2('data')[0];
                let currentStock = selectedOption ? parseFloat(selectedOption.stock) || 0 : 0;

                if (type === 'out' && quantity > currentStock) {
                    $('#quantity').addClass('is-invalid');
                    $('.quantity-feedback').remove();
                    $('#quantity').after(`<div class="invalid-feedback quantity-feedback">
                        Quantity tidak boleh melebihi stok saat ini (${currentStock})
                    </div>`);
                    return false;
                }

                $('#quantity').removeClass('is-invalid');
                $('.quantity-feedback').remove();
                return true;
            }

            // Add event listeners
            $('#type, #quantity').on('change input', validateQuantity);
        });
    </script>
@endpush
