@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-plus-circle"></i> Add New Unit
                                <small class="text-muted d-block mt-1">{{ $product->name }}</small>
                            </h4>
                            @if($hasDefaultUnit)
                                <span class="badge bg-info">Default unit exists</span>
                            @else
                                <span class="badge bg-warning">No default unit</span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('products.units.store', $product) }}" method="POST" id="unitForm">
                            @csrf

                            <div class="mb-4">
                                <label for="unit_id" class="form-label">Select Unit</label>
                                <select class="form-select form-select-lg @error('unit_id') is-invalid @enderror"
                                        id="unit_id" name="unit_id" required>
                                    <option value="">Choose a unit...</option>
                                    @foreach($availableUnits as $unit)
                                        <option value="{{ $unit->id }}"
                                                data-code="{{ $unit->code }}"
                                            {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ $unit->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="conversion_factor" class="form-label required">
                                        Conversion Factor
                                        <i class="bi bi-info-circle" data-bs-toggle="tooltip"
                                           title="How many base units equal one of this unit"></i>
                                    </label>
                                    <div class="input-group">
                                        <input type="number"
                                               step="0.0001"
                                               min="0.0001"
                                               max="999999.9999"
                                               class="form-control @error('conversion_factor') is-invalid @enderror"
                                               id="conversion_factor"
                                               name="conversion_factor"
                                               value="{{ old('conversion_factor', '1.0000') }}"
                                               required>
                                        <span class="input-group-text"><span class="unit-code">x</span></span>
                                    </div>
                                    @error('conversion_factor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted conversion-example"></small>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="purchase_price" class="form-label required">Purchase Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number"
                                               step="100"
                                               min="0"
                                               max="999999999.99"
                                               class="form-control @error('purchase_price') is-invalid @enderror"
                                               id="purchase_price"
                                               name="purchase_price"
                                               value="{{ old('purchase_price') }}"
                                               required>
                                    </div>
                                    @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="selling_price" class="form-label required">Selling Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number"
                                               step="100"
                                               min="0"
                                               max="999999999.99"
                                               class="form-control @error('selling_price') is-invalid @enderror"
                                               id="selling_price"
                                               name="selling_price"
                                               value="{{ old('selling_price') }}"
                                               required>
                                    </div>
                                    @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="stock" class="form-label required">Initial Stock</label>
                                <div class="input-group">
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           max="999999999.99"
                                           class="form-control @error('stock') is-invalid @enderror"
                                           id="stock"
                                           name="stock"
                                           value="{{ old('stock', 0) }}"
                                           required>
                                    <span class="input-group-text unit-code"></span>
                                </div>
                                @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           id="is_default"
                                           name="is_default"
                                           value="1"
                                        {{ !$hasDefaultUnit ? 'checked disabled' : '' }}
                                        {{ old('is_default') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Set as Default Unit
                                        <small class="text-muted d-block">
                                            @if(!$hasDefaultUnit)
                                                This will be the first unit and automatically set as default
                                            @else
                                                The default unit will be used as the base for all conversions
                                            @endif
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
                                    <i class="bi bi-check-circle"></i> Save Unit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .required:after {
                content: " *";
                color: red;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })

                // Handle unit selection
                const unitSelect = document.getElementById('unit_id')
                const unitCodeSpans = document.querySelectorAll('.unit-code')
                const conversionExample = document.querySelector('.conversion-example')
                const conversionInput = document.getElementById('conversion_factor')

                function updateUnitCode() {
                    const selectedOption = unitSelect.options[unitSelect.selectedIndex]
                    const unitCode = selectedOption.dataset.code || 'unit'

                    unitCodeSpans.forEach(span => {
                        span.textContent = unitCode
                    })

                    updateConversionExample()
                }

                function updateConversionExample() {
                    if (!unitSelect.value) return

                    const selectedOption = unitSelect.options[unitSelect.selectedIndex]
                    const unitCode = selectedOption.dataset.code
                    const factor = parseFloat(conversionInput.value) || 0

                    if (factor > 0) {
                        conversionExample.textContent = `1 ${unitCode} = ${factor} base unit(s)`
                    }
                }

                unitSelect.addEventListener('change', updateUnitCode)
                conversionInput.addEventListener('input', updateConversionExample)
                updateUnitCode() // Initial update

                // Handle default unit checkbox
                const defaultCheckbox = document.getElementById('is_default')
                defaultCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        conversionInput.value = '1.0000'
                        conversionInput.readOnly = true
                    } else {
                        conversionInput.readOnly = false
                    }
                    updateConversionExample()
                })

                // Form validation
                const form = document.getElementById('unitForm')
                form.addEventListener('submit', function(e) {
                    if (!form.checkValidity()) {
                        e.preventDefault()
                        e.stopPropagation()
                    }
                    form.classList.add('was-validated')
                })
            })
        </script>
    @endpush
@endsection
