@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="card-title mb-0">
                            <i class="bi bi-pencil"></i> Edit Unit
                            <small class="text-muted d-block mt-1">{{ $product->name }}</small>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.units.update', [$product, $unit]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="alert alert-info">
                                <strong>{{ $unit->unit->name }} ({{ $unit->unit->code }})</strong>
                                <p class="mb-0 small">Unit type cannot be changed. Delete this conversion and create a new one if needed.</p>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="conversion_factor" class="form-label">
                                        Conversion Factor
                                        <i class="bi bi-info-circle" data-bs-toggle="tooltip"
                                           title="How many base units equal one of this unit"></i>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.0001"
                                               class="form-control @error('conversion_factor') is-invalid @enderror"
                                               id="conversion_factor" name="conversion_factor"
                                               value="{{ old('conversion_factor', $unit->conversion_factor) }}"
                                            {{ $unit->is_default ? 'readonly' : '' }}>
                                        <span class="input-group-text">x base unit</span>
                                    </div>
                                    @error('conversion_factor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="purchase_price" class="form-label">
                                        Purchase Price
                                        <i class="bi bi-info-circle" data-bs-toggle="tooltip"
                                           title="Purchase price for this unit size"></i>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" step="100"
                                               class="form-control @error('purchase_price') is-invalid @enderror"
                                               id="purchase_price" name="purchase_price"
                                               value="{{ old('purchase_price', $unit->purchase_price) }}">
                                    </div>
                                    @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="selling_price" class="form-label">
                                        Selling Price
                                        <i class="bi bi-info-circle" data-bs-toggle="tooltip"
                                           title="Selling price for this unit size"></i>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" step="100"
                                               class="form-control @error('selling_price') is-invalid @enderror"
                                               id="selling_price" name="selling_price"
                                               value="{{ old('selling_price', $unit->selling_price) }}">
                                    </div>
                                    @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="stock" class="form-label">
                                    Stock
                                    <i class="bi bi-info-circle" data-bs-toggle="tooltip"
                                       title="Current stock for this unit"></i>
                                </label>
                                <input type="number" step="1"
                                       class="form-control @error('stock') is-invalid @enderror"
                                       id="stock" name="stock"
                                       value="{{ old('stock', $unit->stock) }}">
                                @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="is_default"
                                           name="is_default" value="1"
                                        {{ old('is_default', $unit->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Set as Default Unit
                                        <small class="text-muted d-block">
                                            Making this the default unit will update prices for all other units
                                        </small>
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('products.show', $product) }}"
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize tooltips
                let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })

                // Handle default unit checkbox
                const defaultCheckbox = document.getElementById('is_default')
                const conversionInput = document.getElementById('conversion_factor')

                defaultCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        conversionInput.value = '1.0000'
                        conversionInput.readOnly = true
                    } else {
                        conversionInput.readOnly = false
                    }
                })
            })
        </script>
    @endpush
@endsection
