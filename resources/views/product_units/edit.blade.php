@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">
                                <i class="bi bi-pencil"></i> Edit Unit
                                <small class="text-muted d-block mt-1">{{ $product->name }}</small>
                            </h4>
                            <span class="badge {{ $unit->is_default ? 'bg-primary' : 'bg-secondary' }}">
                                {{ $unit->is_default ? 'Default Unit' : 'Additional Unit' }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <div class="fw-bold">{{ $unit->unit->name }} ({{ $unit->unit->code }})</div>
                            <p class="mb-2 small">Unit type cannot be changed. Delete this conversion and create a new one if needed.</p>
                            <div class="small">
                                <strong>Total Stock in Default Unit:</strong>
                                {{ number_format($totalStockInDefaultUnit, 2) }}
                            </div>
                        </div>

                        <form action="{{ route('products.units.update', [$product, $unit]) }}"
                              method="POST"
                              id="unitForm">
                            @csrf
                            @method('PUT')

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
                                               value="{{ old('conversion_factor', $unit->conversion_factor) }}"
                                               {{ $unit->is_default ? 'readonly' : '' }}
                                               required>
                                        <span class="input-group-text">{{ $unit->unit->code }}</span>
                                    </div>
                                    @error('conversion_factor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted conversion-example">
                                        1 {{ $unit->unit->code }} = {{ $unit->conversion_factor }} base unit(s)
                                    </small>
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
                                               value="{{ old('purchase_price', $unit->purchase_price) }}"
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
                                               value="{{ old('selling_price', $unit->selling_price) }}"
                                               required>
                                    </div>
                                    @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="stock" class="form-label required">Stock</label>
                                <div class="input-group">
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           max="999999999.99"
                                           class="form-control @error('stock') is-invalid @enderror"
                                           id="stock"
                                           name="stock"
                                           value="{{ old('stock', $unit->stock) }}"
                                           required>
                                    <span class="input-group-text">{{ $unit->unit->code }}</span>
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
                                        {{ old('is_default', $unit->is_default) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Set as Default Unit
                                        <small class="text-muted d-block">
                                            Making this the default unit will update prices and stock for all other units
                                        </small>
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between gap-2">
                                <div>
                                    <!-- Form delete dikeluarkan dari form utama -->
                                    <button type="button"
                                            class="btn btn-outline-danger"
                                            onclick="confirmDelete()">
                                        <i class="bi bi-trash"></i> Delete Unit
                                    </button>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="{{ route('products.show', $product) }}"
                                       class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Form delete dipindah ke luar form utama -->
                        <form id="deleteForm"
                              action="{{ route('products.units.destroy', [$product, $unit]) }}"
                              method="POST"
                              class="d-none">
                            @csrf
                            @method('DELETE')
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

                // Handle conversion factor changes
                const conversionInput = document.getElementById('conversion_factor')
                const conversionExample = document.querySelector('.conversion-example')
                const unitCode = '{{ $unit->unit->code }}'

                function updateConversionExample() {
                    const factor = parseFloat(conversionInput.value) || 0
                    if (factor > 0) {
                        conversionExample.textContent = `1 ${unitCode} = ${factor} base unit(s)`
                    }
                }

                conversionInput.addEventListener('input', updateConversionExample)

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

                function confirmDelete() {
                    if (confirm('Are you sure you want to delete this unit?')) {
                        document.getElementById('deleteForm').submit();
                    }
                }
            })
        </script>
    @endpush
@endsection
